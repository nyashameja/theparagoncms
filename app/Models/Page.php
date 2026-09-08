<?php

namespace App\Models;

use App\Support\Database;

class Page extends BaseModel
{
    protected static string $table = 'pages';

    public static function findBySlug(string $slug): ?array
    {
        return Database::selectOne(
            "SELECT * FROM pages WHERE slug = ? AND status = 'published' AND deleted_at IS NULL LIMIT 1",
            [$slug]
        );
    }

    public static function getWithSections(int $id): ?array
    {
        $page = static::find($id);
        if (!$page) return null;
        $page['sections'] = Database::select(
            'SELECT * FROM page_sections WHERE page_id = ? AND is_active = 1 ORDER BY sort_order ASC',
            [$id]
        );
        return $page;
    }

    public static function getBySlugWithSections(string $slug): ?array
    {
        $page = static::findBySlug($slug);
        if (!$page) return null;
        return static::getWithSections($page['id']);
    }

    public static function published(): array
    {
        return Database::select("SELECT * FROM pages WHERE status = 'published' AND deleted_at IS NULL ORDER BY title ASC");
    }
}
