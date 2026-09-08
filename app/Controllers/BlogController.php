<?php

namespace App\Controllers;

use App\Support\Request;
use App\Support\Database;
use App\Models\Article;

class BlogController
{
    public function index(Request $request): string
    {
        $page    = max(1, (int) $request->get('page', 1));
        $result  = Article::published($page, 12);
        $cats    = Article::getCategories();

        return view('public.blog.index', [
            'title'       => 'Insights & Resources | The Paragon .Design',
            'description' => 'Digital marketing insights, web design tips, SEO guides and business advice from the team at The Paragon .Design.',
            'articles'    => $result['data'],
            'paginate'    => $result,
            'categories'  => $cats,
        ]);
    }

    public function category(Request $request, array $params): string
    {
        $page   = max(1, (int) $request->get('page', 1));
        $result = Article::byCategory($params['slug'], $page, 12);
        $cat    = $result['category'];

        if (!$cat) abort(404);

        return view('public.blog.index', [
            'title'       => ($cat['name'] ?? '') . ' | The Paragon .Design Insights',
            'description' => 'Articles in the ' . ($cat['name'] ?? '') . ' category.',
            'articles'    => $result['data'],
            'paginate'    => $result,
            'categories'  => Article::getCategories(),
            'activeCategory' => $cat,
        ]);
    }

    public function show(Request $request, array $params): string
    {
        $article = Article::findBySlug($params['slug']);
        if (!$article) abort(404);

        $article['tags'] = Article::getTags($article['id']);
        $related = Article::related($article['id'], 3);

        $readTime = reading_time($article['content'] ?? '');

        // Track view
        try {
            Database::query(
                'INSERT INTO analytics_events (event_type, resource_type, resource_id, path, ip_address, created_at) VALUES (?, ?, ?, ?, ?, NOW())',
                ['pageview', 'article', $article['id'], $request->path, $request->ip]
            );
            Database::update('UPDATE articles SET view_count = view_count + 1 WHERE id = ?', [$article['id']]);
        } catch (\Throwable) {}

        return view('public.blog.show', [
            'title'       => ($article['meta_title'] ?: $article['title'] . ' | The Paragon .Design'),
            'description' => $article['meta_description'] ?: str_excerpt($article['excerpt'] ?? $article['content'] ?? '', 160),
            'article'     => $article,
            'related'     => $related,
            'readTime'    => $readTime,
            'breadcrumb'  => [['label' => 'Insights', 'url' => '/insights'], ['label' => $article['title']]],
        ]);
    }
}
