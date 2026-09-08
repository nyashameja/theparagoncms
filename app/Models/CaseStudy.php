<?php

namespace App\Models;

use App\Support\Database;

class CaseStudy extends BaseModel
{
    protected static string $table = 'case_studies';

    public static function findBySlug(string $slug): ?array
    {
        return Database::selectOne(
            "SELECT * FROM case_studies WHERE slug = ? AND status = 'published' AND deleted_at IS NULL LIMIT 1",
            [$slug]
        );
    }

    public static function published(int $page = 1, int $perPage = 9): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = (int) (Database::selectOne("SELECT COUNT(*) as c FROM case_studies WHERE status = 'published' AND deleted_at IS NULL")['c'] ?? 0);
        $data   = Database::select(
            "SELECT * FROM case_studies WHERE status = 'published' AND deleted_at IS NULL ORDER BY is_featured DESC, published_at DESC LIMIT $perPage OFFSET $offset"
        );
        return ['data' => $data, 'total' => $total, 'current_page' => $page, 'per_page' => $perPage, 'last_page' => (int) ceil($total / $perPage)];
    }

    public static function featured(int $limit = 1): array
    {
        return Database::select(
            "SELECT * FROM case_studies WHERE status = 'published' AND is_featured = 1 AND deleted_at IS NULL ORDER BY published_at DESC LIMIT ?",
            [$limit]
        );
    }

    public static function getWithBlocks(int $id): ?array
    {
        $cs = static::find($id);
        if (!$cs) return null;
        $cs['blocks'] = Database::select('SELECT * FROM case_study_blocks WHERE case_study_id = ? ORDER BY sort_order ASC', [$id]);
        $cs['results'] = Database::select('SELECT * FROM case_study_results WHERE case_study_id = ? ORDER BY sort_order ASC', [$id]);
        $cs['testimonial'] = Database::selectOne("SELECT * FROM testimonials WHERE case_study_id = ? AND status = 'approved' LIMIT 1", [$id]);
        return $cs;
    }
}
