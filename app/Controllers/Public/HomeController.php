<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\View;
use App\Support\Database;

class HomeController
{
    public function index(Request $request): void
    {
        $services = Database::select(
            "SELECT * FROM services WHERE status='published' AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT 6"
        );

        $projects = Database::select(
            "SELECT p.*, s.name AS service_name FROM projects p
             LEFT JOIN services s ON p.service_id = s.id
             WHERE p.status='published' AND p.deleted_at IS NULL
             ORDER BY p.sort_order ASC, p.created_at DESC LIMIT 6"
        );

        $testimonials = Database::select(
            "SELECT * FROM testimonials WHERE is_active=1 AND permission_granted=1 AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT 8"
        );

        $articles = Database::select(
            "SELECT a.*, c.name AS category_name FROM articles a
             LEFT JOIN article_categories c ON a.category_id = c.id
             WHERE a.status='published' AND a.deleted_at IS NULL
             ORDER BY a.published_at DESC LIMIT 3"
        );

        echo View::render('home/index', [
            'title'        => null,
            'services'     => $services,
            'projects'     => $projects,
            'testimonials' => $testimonials,
            'articles'     => $articles,
        ]);
    }
}
