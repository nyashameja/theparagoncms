<?php

namespace App\Models;

use App\Support\Database;

class Project extends BaseModel
{
    protected static string $table = 'projects';

    public static function findBySlug(string $slug): ?array
    {
        return Database::selectOne('SELECT * FROM projects WHERE slug = ? AND deleted_at IS NULL LIMIT 1', [$slug]);
    }

    public static function published(int $limit = 0): array
    {
        $sql = 'SELECT * FROM projects WHERE status = ? AND deleted_at IS NULL ORDER BY is_featured DESC, display_priority ASC, project_year DESC';
        if ($limit > 0) $sql .= " LIMIT $limit";
        return Database::select($sql, ['published']);
    }

    public static function featured(int $limit = 6): array
    {
        return Database::select(
            'SELECT * FROM projects WHERE status = ? AND is_featured = 1 AND deleted_at IS NULL ORDER BY display_priority ASC LIMIT ?',
            ['published', $limit]
        );
    }

    public static function search(string $query, array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $where  = ['p.status = ?', 'p.deleted_at IS NULL'];
        $params = ['published'];

        if ($query) {
            $where[]  = '(p.title LIKE ? OR p.summary LIKE ? OR p.client_name LIKE ?)';
            $q = '%' . $query . '%';
            array_push($params, $q, $q, $q);
        }

        if (!empty($filters['service'])) {
            $where[]  = 'EXISTS (SELECT 1 FROM project_services ps JOIN services s ON s.id = ps.service_id WHERE ps.project_id = p.id AND s.slug = ?)';
            $params[] = $filters['service'];
        }

        if (!empty($filters['industry'])) {
            $where[]  = 'EXISTS (SELECT 1 FROM industries i WHERE i.id = p.industry_id AND i.slug = ?)';
            $params[] = $filters['industry'];
        }

        if (!empty($filters['year'])) {
            $where[]  = 'p.project_year = ?';
            $params[] = (int) $filters['year'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);
        $countSql    = "SELECT COUNT(*) as c FROM projects p $whereClause";
        $total       = (int) (Database::selectOne($countSql, $params)['c'] ?? 0);

        $offset = ($page - 1) * $perPage;
        $dataSql = "SELECT p.* FROM projects p $whereClause ORDER BY p.is_featured DESC, p.display_priority ASC, p.project_year DESC LIMIT $perPage OFFSET $offset";
        $data = Database::select($dataSql, $params);

        return [
            'data'         => $data,
            'total'        => $total,
            'current_page' => $page,
            'per_page'     => $perPage,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    public static function getWithRelated(int $id): ?array
    {
        $project = static::find($id);
        if (!$project) return null;

        $project['gallery'] = Database::select('SELECT * FROM project_media WHERE project_id = ? ORDER BY sort_order ASC', [$id]);
        $project['services'] = Database::select(
            'SELECT s.* FROM services s JOIN project_services ps ON ps.service_id = s.id WHERE ps.project_id = ?',
            [$id]
        );
        $project['results'] = Database::select('SELECT * FROM project_results WHERE project_id = ? ORDER BY sort_order ASC', [$id]);
        $project['testimonial'] = Database::selectOne(
            'SELECT t.* FROM testimonials t WHERE t.project_id = ? AND t.status = ? LIMIT 1',
            [$id, 'approved']
        );

        return $project;
    }

    public static function getBySlugWithRelated(string $slug): ?array
    {
        $project = static::findBySlug($slug);
        if (!$project) return null;
        return static::getWithRelated($project['id']);
    }

    public static function related(int $id, int $limit = 3): array
    {
        return Database::select(
            'SELECT * FROM projects WHERE id != ? AND status = ? AND deleted_at IS NULL ORDER BY RAND() LIMIT ?',
            [$id, 'published', $limit]
        );
    }

    public static function prevNext(int $id): array
    {
        $prev = Database::selectOne(
            'SELECT id, title, slug FROM projects WHERE id < ? AND status = ? AND deleted_at IS NULL ORDER BY id DESC LIMIT 1',
            [$id, 'published']
        );
        $next = Database::selectOne(
            'SELECT id, title, slug FROM projects WHERE id > ? AND status = ? AND deleted_at IS NULL ORDER BY id ASC LIMIT 1',
            [$id, 'published']
        );
        return ['prev' => $prev, 'next' => $next];
    }
}
