<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\View;
use App\Support\Database;

class PortfolioController
{
    public function index(Request $request): void
    {
        $page    = max(1, (int)$request->input('page', 1));
        $perPage = 12;
        $offset  = ($page - 1) * $perPage;

        $total = (int)(Database::selectOne(
            "SELECT COUNT(*) AS n FROM projects WHERE status='published' AND deleted_at IS NULL"
        )['n'] ?? 0);

        $projects = Database::select(
            "SELECT p.*, s.name AS service_name FROM projects p
             LEFT JOIN services s ON p.service_id = s.id
             WHERE p.status='published' AND p.deleted_at IS NULL
             ORDER BY p.sort_order ASC, p.created_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        /* Distinct service categories for filter */
        $categories = array_unique(array_filter(array_column($projects, 'service_name')));

        $totalPages = max(1, (int)ceil($total / $perPage));

        echo View::render('portfolio/index', [
            'title'           => 'Portfolio — The Paragon .Design',
            'metaDescription' => 'Browse our portfolio of web design, branding, and digital marketing projects for South African businesses.',
            'projects'        => $projects,
            'categories'      => array_values($categories),
            'pagination'      => $totalPages > 1 ? [
                'current' => $page,
                'total'   => $totalPages,
                'prev'    => $page > 1 ? $page - 1 : null,
                'next'    => $page < $totalPages ? $page + 1 : null,
            ] : null,
        ]);
    }

    public function show(Request $request, string $slug): void
    {
        $project = Database::selectOne(
            "SELECT p.*, s.name AS service_name, i.name AS industry_name
             FROM projects p
             LEFT JOIN services s ON p.service_id = s.id
             LEFT JOIN industries i ON p.industry_id = i.id
             WHERE p.slug=? AND p.status='published' AND p.deleted_at IS NULL",
            [$slug]
        );
        if (!$project) abort(404);

        /* Results stored as JSON */
        $results = [];
        if (!empty($project['results_json'])) {
            $results = json_decode($project['results_json'], true) ?: [];
        }

        /* Related projects */
        $related = Database::select(
            "SELECT * FROM projects WHERE status='published' AND service_id=? AND id!=? AND deleted_at IS NULL
             ORDER BY sort_order ASC LIMIT 3",
            [$project['service_id'], $project['id']]
        );

        echo View::render('portfolio/show', [
            'title'           => $project['meta_title'] ?: $project['title'],
            'metaDescription' => $project['meta_description'] ?? $project['excerpt'] ?? '',
            'project'         => $project,
            'results'         => $results,
            'related'         => $related,
        ]);
    }
}
