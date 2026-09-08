<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\View;
use App\Support\Database;

class IndustryController
{
    public function show(Request $request, string $slug): void
    {
        $industry = Database::selectOne(
            "SELECT * FROM industries WHERE slug=? AND is_active=1 AND deleted_at IS NULL",
            [$slug]
        );
        if (!$industry) abort(404);

        $services = Database::select(
            "SELECT * FROM services WHERE status='published' AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT 6"
        );

        $projects = Database::select(
            "SELECT p.* FROM projects p
             WHERE p.industry_id=? AND p.status='published' AND p.deleted_at IS NULL
             ORDER BY p.sort_order ASC LIMIT 6",
            [$industry['id']]
        );

        echo View::render('industries/show', [
            'title'           => 'Web Design for ' . $industry['name'] . ' — The Paragon .Design',
            'metaDescription' => $industry['meta_description'] ?? ('Premium web design and digital marketing services for ' . $industry['name'] . ' businesses in South Africa.'),
            'industry'        => $industry,
            'services'        => $services,
            'projects'        => $projects,
        ]);
    }
}
