<?php

namespace App\Models;

use App\Support\Database;

class Article extends BaseModel
{
    protected static string $table = 'articles';

    public static function findBySlug(string $slug): ?array
    {
        return Database::selectOne(
            'SELECT a.*, u.name as author_name, u.avatar as author_avatar, u.bio as author_bio
             FROM articles a
             LEFT JOIN users u ON u.id = a.author_id
             WHERE a.slug = ? AND a.status = ? AND a.deleted_at IS NULL
             AND (a.published_at IS NULL OR a.published_at <= NOW()) LIMIT 1',
            [$slug, 'published']
        );
    }

    public static function published(int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        $total = (int) (Database::selectOne(
            "SELECT COUNT(*) as c FROM articles WHERE status = 'published' AND deleted_at IS NULL AND (published_at IS NULL OR published_at <= NOW())"
        )['c'] ?? 0);

        $data = Database::select(
            "SELECT a.*, u.name as author_name FROM articles a
             LEFT JOIN users u ON u.id = a.author_id
             WHERE a.status = 'published' AND a.deleted_at IS NULL AND (a.published_at IS NULL OR a.published_at <= NOW())
             ORDER BY a.published_at DESC LIMIT $perPage OFFSET $offset"
        );

        return ['data' => $data, 'total' => $total, 'current_page' => $page, 'per_page' => $perPage, 'last_page' => (int) ceil($total / $perPage)];
    }

    public static function featured(int $limit = 3): array
    {
        return Database::select(
            "SELECT a.*, u.name as author_name FROM articles a
             LEFT JOIN users u ON u.id = a.author_id
             WHERE a.status = 'published' AND a.is_featured = 1 AND a.deleted_at IS NULL AND (a.published_at IS NULL OR a.published_at <= NOW())
             ORDER BY a.published_at DESC LIMIT ?",
            [$limit]
        );
    }

    public static function latest(int $limit = 4): array
    {
        return Database::select(
            "SELECT a.*, u.name as author_name FROM articles a
             LEFT JOIN users u ON u.id = a.author_id
             WHERE a.status = 'published' AND a.deleted_at IS NULL AND (a.published_at IS NULL OR a.published_at <= NOW())
             ORDER BY a.published_at DESC LIMIT ?",
            [$limit]
        );
    }

    public static function byCategory(string $slug, int $page = 1, int $perPage = 12): array
    {
        $cat = Database::selectOne('SELECT * FROM article_categories WHERE slug = ? LIMIT 1', [$slug]);
        if (!$cat) return ['data' => [], 'total' => 0, 'current_page' => 1, 'per_page' => $perPage, 'last_page' => 1, 'category' => null];

        $offset = ($page - 1) * $perPage;
        $total = (int) (Database::selectOne(
            "SELECT COUNT(*) as c FROM articles a
             JOIN article_category_map acm ON acm.article_id = a.id
             WHERE acm.category_id = ? AND a.status = 'published' AND a.deleted_at IS NULL",
            [$cat['id']]
        )['c'] ?? 0);

        $data = Database::select(
            "SELECT a.*, u.name as author_name FROM articles a
             JOIN article_category_map acm ON acm.article_id = a.id
             LEFT JOIN users u ON u.id = a.author_id
             WHERE acm.category_id = ? AND a.status = 'published' AND a.deleted_at IS NULL
             ORDER BY a.published_at DESC LIMIT $perPage OFFSET $offset",
            [$cat['id']]
        );

        return ['data' => $data, 'total' => $total, 'current_page' => $page, 'per_page' => $perPage, 'last_page' => (int) ceil($total / $perPage), 'category' => $cat];
    }

    public static function related(int $id, int $limit = 3): array
    {
        return Database::select(
            "SELECT a.*, u.name as author_name FROM articles a
             LEFT JOIN users u ON u.id = a.author_id
             WHERE a.id != ? AND a.status = 'published' AND a.deleted_at IS NULL
             ORDER BY RAND() LIMIT ?",
            [$id, $limit]
        );
    }

    public static function search(string $query, int $page = 1, int $perPage = 12): array
    {
        $q = '%' . $query . '%';
        $offset = ($page - 1) * $perPage;
        $total = (int) (Database::selectOne(
            "SELECT COUNT(*) as c FROM articles WHERE (title LIKE ? OR excerpt LIKE ? OR content LIKE ?) AND status = 'published' AND deleted_at IS NULL",
            [$q, $q, $q]
        )['c'] ?? 0);

        $data = Database::select(
            "SELECT * FROM articles WHERE (title LIKE ? OR excerpt LIKE ?) AND status = 'published' AND deleted_at IS NULL
             ORDER BY published_at DESC LIMIT $perPage OFFSET $offset",
            [$q, $q]
        );

        return ['data' => $data, 'total' => $total, 'current_page' => $page, 'per_page' => $perPage, 'last_page' => (int) ceil($total / $perPage)];
    }

    public static function getCategories(): array
    {
        return Database::select('SELECT * FROM article_categories WHERE deleted_at IS NULL ORDER BY name ASC');
    }

    public static function getTags(int $articleId): array
    {
        return Database::select(
            'SELECT t.* FROM article_tags t JOIN article_tag_map atm ON atm.tag_id = t.id WHERE atm.article_id = ?',
            [$articleId]
        );
    }
}
