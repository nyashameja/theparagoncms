<?php

namespace App\Models;

use App\Support\Database;

class Location extends BaseModel
{
    protected static string $table = 'locations';

    public static function findBySlug(string $slug): ?array
    {
        return Database::selectOne("SELECT * FROM locations WHERE slug = ? AND status = 'published' AND deleted_at IS NULL LIMIT 1", [$slug]);
    }

    public static function published(): array
    {
        return Database::select("SELECT * FROM locations WHERE status = 'published' AND deleted_at IS NULL ORDER BY sort_order ASC, name ASC");
    }
}
