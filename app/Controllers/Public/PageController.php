<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\View;
use App\Support\Database;

class PageController
{
    public function show(Request $request, string $slug): void
    {
        $page = Database::selectOne(
            "SELECT * FROM pages WHERE slug=? AND status='published' AND deleted_at IS NULL",
            [$slug]
        );
        if (!$page) abort(404);

        echo View::render('pages/show', [
            'title'           => ($page['meta_title'] ?: $page['title']),
            'metaDescription' => $page['meta_description'] ?? '',
            'noindex'         => $page['noindex'] ?? false,
            'page'            => $page,
        ]);
    }
}
