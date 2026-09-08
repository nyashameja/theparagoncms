<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\View;
use App\Support\Database;

class BlogController
{
    private int $perPage = 12;

    public function index(Request $request): void
    {
        $page = max(1, (int)$request->input('page', 1));
        $offset = ($page - 1) * $this->perPage;

        $total = (int)(Database::selectOne(
            "SELECT COUNT(*) AS n FROM articles WHERE status='published' AND deleted_at IS NULL"
        )['n'] ?? 0);

        $articles = Database::select(
            "SELECT a.*, c.name AS category_name, c.slug AS category_slug
             FROM articles a
             LEFT JOIN article_categories c ON a.category_id = c.id
             WHERE a.status='published' AND a.deleted_at IS NULL
             ORDER BY a.published_at DESC LIMIT ? OFFSET ?",
            [$this->perPage, $offset]
        );

        $categories = Database::select(
            "SELECT c.* FROM article_categories c
             INNER JOIN articles a ON a.category_id = c.id AND a.status='published' AND a.deleted_at IS NULL
             GROUP BY c.id ORDER BY c.name ASC"
        );

        $totalPages = max(1, (int)ceil($total / $this->perPage));

        View::render('blog/index', [
            'title'           => 'Blog — The Paragon .Design',
            'metaDescription' => 'Digital marketing insights, web design tips, and business growth strategies for South African companies.',
            'articles'        => $articles,
            'categories'      => $categories,
            'currentCategory' => null,
            'pagination'      => $this->paginationData($page, $totalPages),
        ]);
    }

    public function category(Request $request, string $slug): void
    {
        $category = Database::selectOne(
            "SELECT * FROM article_categories WHERE slug=?",
            [$slug]
        );
        if (!$category) abort(404);

        $page = max(1, (int)$request->input('page', 1));
        $offset = ($page - 1) * $this->perPage;

        $total = (int)(Database::selectOne(
            "SELECT COUNT(*) AS n FROM articles WHERE status='published' AND category_id=? AND deleted_at IS NULL",
            [$category['id']]
        )['n'] ?? 0);

        $articles = Database::select(
            "SELECT a.*, c.name AS category_name, c.slug AS category_slug
             FROM articles a
             LEFT JOIN article_categories c ON a.category_id = c.id
             WHERE a.status='published' AND a.category_id=? AND a.deleted_at IS NULL
             ORDER BY a.published_at DESC LIMIT ? OFFSET ?",
            [$category['id'], $this->perPage, $offset]
        );

        $categories = Database::select(
            "SELECT c.* FROM article_categories c
             INNER JOIN articles a ON a.category_id = c.id AND a.status='published' AND a.deleted_at IS NULL
             GROUP BY c.id ORDER BY c.name ASC"
        );

        $totalPages = max(1, (int)ceil($total / $this->perPage));

        View::render('blog/category', [
            'title'           => $category['name'] . ' — Blog',
            'metaDescription' => $category['description'] ?? '',
            'category'        => $category,
            'articles'        => $articles,
            'categories'      => $categories,
            'currentCategory' => $category,
            'pagination'      => $this->paginationData($page, $totalPages),
        ]);
    }

    public function tag(Request $request, string $slug): void
    {
        /* Reuse category view with tag data */
        $tag = Database::selectOne("SELECT * FROM article_tags WHERE slug=?", [$slug]);
        if (!$tag) abort(404);

        $page = max(1, (int)$request->input('page', 1));
        $offset = ($page - 1) * $this->perPage;

        $articles = Database::select(
            "SELECT a.*, c.name AS category_name, c.slug AS category_slug
             FROM articles a
             LEFT JOIN article_categories c ON a.category_id = c.id
             INNER JOIN article_tag_pivots atp ON atp.article_id = a.id
             WHERE a.status='published' AND atp.tag_id=? AND a.deleted_at IS NULL
             ORDER BY a.published_at DESC LIMIT ? OFFSET ?",
            [$tag['id'], $this->perPage, $offset]
        );

        View::render('blog/category', [
            'title'           => '#' . $tag['name'] . ' — Blog',
            'category'        => ['name' => $tag['name'], 'description' => '', 'slug' => $tag['slug']],
            'articles'        => $articles,
            'categories'      => [],
            'currentCategory' => null,
            'pagination'      => null,
        ]);
    }

    public function show(Request $request, string $slug): void
    {
        $article = Database::selectOne(
            "SELECT a.*, c.name AS category_name, c.slug AS category_slug,
                    u.name AS author_name
             FROM articles a
             LEFT JOIN article_categories c ON a.category_id = c.id
             LEFT JOIN users u ON a.author_id = u.id
             WHERE a.slug=? AND a.status='published' AND a.deleted_at IS NULL",
            [$slug]
        );
        if (!$article) abort(404);

        /* Increment view count */
        Database::update("UPDATE articles SET view_count = view_count + 1 WHERE id=?", [$article['id']]);

        /* Tags */
        $tags = Database::select(
            "SELECT t.* FROM article_tags t
             INNER JOIN article_tag_pivots atp ON atp.tag_id = t.id
             WHERE atp.article_id=?",
            [$article['id']]
        );

        /* Related by category */
        $related = Database::select(
            "SELECT * FROM articles WHERE status='published' AND category_id=? AND id!=? AND deleted_at IS NULL
             ORDER BY published_at DESC LIMIT 3",
            [$article['category_id'], $article['id']]
        );

        View::render('blog/show', [
            'title'           => $article['meta_title'] ?: $article['title'],
            'metaDescription' => $article['meta_description'] ?? $article['excerpt'] ?? '',
            'ogType'          => 'article',
            'article'         => $article,
            'tags'            => $tags,
            'related'         => $related,
        ]);
    }

    private function paginationData(int $current, int $total): ?array
    {
        if ($total <= 1) return null;
        return [
            'current' => $current,
            'total'   => $total,
            'prev'    => $current > 1 ? $current - 1 : null,
            'next'    => $current < $total ? $current + 1 : null,
        ];
    }
}
