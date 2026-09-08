<?php

namespace App\Models;

use App\Support\Database;

class Industry extends BaseModel
{
    protected static string $table = 'industries';

    public static function findBySlug(string $slug): ?array
    {
        return Database::selectOne("SELECT * FROM industries WHERE slug = ? AND status = 'published' AND deleted_at IS NULL LIMIT 1", [$slug]);
    }

    public static function published(): array
    {
        return Database::select("SELECT * FROM industries WHERE status = 'published' AND deleted_at IS NULL ORDER BY sort_order ASC, name ASC");
    }

    public static function featured(int $limit = 8): array
    {
        return Database::select("SELECT * FROM industries WHERE status = 'published' AND is_featured = 1 AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT ?", [$limit]);
    }
}
