<?php

namespace App\Models;

use App\Support\Database;

class Lead extends BaseModel
{
    protected static string $table = 'leads';

    const STATUS_NEW       = 'new';
    const STATUS_CONTACTED = 'contacted';
    const STATUS_QUALIFIED = 'qualified';
    const STATUS_PROPOSAL  = 'proposal_sent';
    const STATUS_WON       = 'won';
    const STATUS_LOST      = 'lost';
    const STATUS_SPAM      = 'spam';
    const STATUS_ARCHIVED  = 'archived';

    public static function allStatuses(): array
    {
        return [
            self::STATUS_NEW       => 'New',
            self::STATUS_CONTACTED => 'Contacted',
            self::STATUS_QUALIFIED => 'Qualified',
            self::STATUS_PROPOSAL  => 'Proposal Sent',
            self::STATUS_WON       => 'Won',
            self::STATUS_LOST      => 'Lost',
            self::STATUS_SPAM      => 'Spam',
            self::STATUS_ARCHIVED  => 'Archived',
        ];
    }

    public static function inbox(int $page = 1, int $perPage = 25, array $filters = []): array
    {
        $where  = ['l.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'l.status = ?';
            $params[] = $filters['status'];
        } else {
            $where[] = "l.status NOT IN ('spam', 'archived')";
        }

        if (!empty($filters['search'])) {
            $where[]  = '(l.name LIKE ? OR l.email LIKE ? OR l.company LIKE ?)';
            $q = '%' . $filters['search'] . '%';
            array_push($params, $q, $q, $q);
        }

        if (!empty($filters['service'])) {
            $where[]  = 'l.service_type = ?';
            $params[] = $filters['service'];
        }

        if (!empty($filters['assigned_to'])) {
            $where[]  = 'l.assigned_to = ?';
            $params[] = $filters['assigned_to'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);
        $total = (int) (Database::selectOne("SELECT COUNT(*) as c FROM leads l $whereClause", $params)['c'] ?? 0);
        $offset = ($page - 1) * $perPage;

        $data = Database::select(
            "SELECT l.*, u.name as assigned_name FROM leads l
             LEFT JOIN users u ON u.id = l.assigned_to
             $whereClause ORDER BY l.created_at DESC LIMIT $perPage OFFSET $offset",
            $params
        );

        return ['data' => $data, 'total' => $total, 'current_page' => $page, 'per_page' => $perPage, 'last_page' => (int) ceil($total / $perPage)];
    }

    public static function getWithRelated(int $id): ?array
    {
        $lead = static::find($id);
        if (!$lead) return null;

        $lead['notes']      = Database::select('SELECT n.*, u.name as author_name FROM lead_notes n LEFT JOIN users u ON u.id = n.user_id WHERE n.lead_id = ? ORDER BY n.created_at DESC', [$id]);
        $lead['activities'] = Database::select('SELECT a.*, u.name as author_name FROM lead_activities a LEFT JOIN users u ON u.id = a.user_id WHERE a.lead_id = ? ORDER BY a.created_at DESC', [$id]);
        $lead['attachments']= Database::select('SELECT * FROM lead_attachments WHERE lead_id = ?', [$id]);
        $lead['tags']       = Database::select('SELECT t.* FROM tags t JOIN lead_tags lt ON lt.tag_id = t.id WHERE lt.lead_id = ?', [$id]);

        return $lead;
    }

    public static function detectDuplicate(string $email, string $phone = ''): ?array
    {
        if ($email) {
            $row = Database::selectOne('SELECT id FROM leads WHERE email = ? AND deleted_at IS NULL LIMIT 1', [$email]);
            if ($row) return $row;
        }
        if ($phone) {
            $row = Database::selectOne('SELECT id FROM leads WHERE phone = ? AND deleted_at IS NULL LIMIT 1', [$phone]);
            if ($row) return $row;
        }
        return null;
    }

    public static function addNote(int $leadId, int $userId, string $content): int|string
    {
        return Database::insert(
            'INSERT INTO lead_notes (lead_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())',
            [$leadId, $userId, $content]
        );
    }

    public static function logActivity(int $leadId, int $userId, string $action, ?array $data = null): void
    {
        Database::query(
            'INSERT INTO lead_activities (lead_id, user_id, action, data, created_at) VALUES (?, ?, ?, ?, NOW())',
            [$leadId, $userId, $action, $data ? json_encode($data) : null]
        );
    }

    public static function countsPerStatus(): array
    {
        $rows = Database::select(
            "SELECT status, COUNT(*) as count FROM leads WHERE deleted_at IS NULL GROUP BY status"
        );
        $result = [];
        foreach ($rows as $row) $result[$row['status']] = (int) $row['count'];
        return $result;
    }

    public static function followUpsDue(): array
    {
        return Database::select(
            "SELECT l.*, u.name as assigned_name FROM leads l
             LEFT JOIN users u ON u.id = l.assigned_to
             WHERE l.follow_up_date <= CURDATE() AND l.status NOT IN ('won','lost','spam','archived') AND l.deleted_at IS NULL
             ORDER BY l.follow_up_date ASC LIMIT 10"
        );
    }
}
