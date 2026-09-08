<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\View;
use App\Support\Database;

class ServicesController
{
    public function index(Request $request): void
    {
        $services = Database::select(
            "SELECT * FROM services WHERE status='published' AND deleted_at IS NULL ORDER BY sort_order ASC"
        );

        echo View::render('services/index', [
            'title'           => 'Services — The Paragon .Design',
            'metaDescription' => 'Web design, branding, SEO, and digital marketing services for South African businesses.',
            'services'        => $services,
        ]);
    }

    public function show(Request $request, string $slug): void
    {
        $service = Database::selectOne(
            "SELECT * FROM services WHERE slug=? AND status='published' AND deleted_at IS NULL",
            [$slug]
        );
        if (!$service) abort(404);

        /* Parse JSON fields */
        $faqs         = !empty($service['faqs_json'])    ? json_decode($service['faqs_json'], true) ?? []    : [];
        $processSteps = !empty($service['process_json']) ? json_decode($service['process_json'], true) ?? [] : [];

        /* Related projects for this service */
        $relatedProjects = Database::select(
            "SELECT * FROM projects WHERE service_id=? AND status='published' AND deleted_at IS NULL
             ORDER BY sort_order ASC LIMIT 4",
            [$service['id']]
        );

        echo View::render('services/show', [
            'title'           => ($service['meta_title'] ?: $service['name'] . ' — The Paragon .Design'),
            'metaDescription' => $service['meta_description'] ?? $service['short_description'] ?? '',
            'service'         => $service,
            'faqs'            => $faqs,
            'processSteps'    => $processSteps,
            'relatedProjects' => $relatedProjects,
        ]);
    }
}
