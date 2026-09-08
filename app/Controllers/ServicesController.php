<?php

namespace App\Controllers;

use App\Support\Request;
use App\Support\Database;
use App\Models\Service;
use App\Models\Project;
use App\Models\CaseStudy;
use App\Models\Setting;

class ServicesController
{
    public function index(Request $request): string
    {
        $services = Service::published();

        return view('public.services.index', [
            'title'       => 'Our Services | The Paragon .Design',
            'description' => 'Explore the full range of digital services offered by The Paragon .Design — from web design to SEO, branding and digital marketing.',
            'services'    => $services,
        ]);
    }

    public function show(Request $request, array $params): string
    {
        $service = Service::getBySlugWithRelated($params['slug']);
        if (!$service || $service['status'] !== 'published') abort(404);

        $relatedProjects = [];
        if ($service['id']) {
            $relatedProjects = Database::select(
                "SELECT p.* FROM projects p
                 JOIN project_services ps ON ps.project_id = p.id
                 WHERE ps.service_id = ? AND p.status = 'published' AND p.deleted_at IS NULL
                 ORDER BY p.is_featured DESC LIMIT 3",
                [$service['id']]
            );
        }

        $relatedArticles = Database::select(
            "SELECT a.* FROM articles a
             JOIN article_service_map asm ON asm.article_id = a.id
             WHERE asm.service_id = ? AND a.status = 'published' AND a.deleted_at IS NULL
             ORDER BY a.published_at DESC LIMIT 3",
            [$service['id']]
        );

        $testimonials = Database::select(
            "SELECT t.* FROM testimonials t WHERE t.service_id = ? AND t.status = 'approved' AND t.deleted_at IS NULL ORDER BY t.sort_order ASC LIMIT 3",
            [$service['id']]
        );

        // Track pageview
        $this->trackView($request, 'service', $service['id']);

        return view('public.services.show', [
            'title'           => ($service['meta_title'] ?: $service['name'] . ' | The Paragon .Design'),
            'description'     => $service['meta_description'] ?: str_excerpt($service['short_description'] ?? '', 160),
            'service'         => $service,
            'relatedProjects' => $relatedProjects,
            'relatedArticles' => $relatedArticles,
            'testimonials'    => $testimonials,
            'breadcrumb'      => [['label' => 'Services', 'url' => '/services'], ['label' => $service['name']]],
        ]);
    }

    private function trackView(Request $request, string $type, int $id): void
    {
        try {
            Database::query(
                'INSERT INTO analytics_events (event_type, resource_type, resource_id, path, ip_address, created_at) VALUES (?, ?, ?, ?, ?, NOW())',
                ['pageview', $type, $id, $request->path, $request->ip]
            );
        } catch (\Throwable) {}
    }
}
