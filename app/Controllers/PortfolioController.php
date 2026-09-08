<?php

namespace App\Controllers;

use App\Support\Request;
use App\Support\Database;
use App\Models\Project;
use App\Models\Service;
use App\Models\Industry;

class PortfolioController
{
    public function index(Request $request): string
    {
        $page    = max(1, (int) $request->get('page', 1));
        $filters = [
            'service'  => $request->get('service', ''),
            'industry' => $request->get('industry', ''),
            'year'     => $request->get('year', ''),
        ];

        $q = $request->get('q', '');
        $result = Project::search($q, $filters, $page, 12);

        $services  = Service::published();
        $industries = Industry::published();
        $years = Database::select("SELECT DISTINCT project_year FROM projects WHERE status = 'published' AND project_year IS NOT NULL ORDER BY project_year DESC");

        return view('public.portfolio.index', [
            'title'       => 'Our Work | The Paragon .Design Portfolio',
            'description' => 'Browse The Paragon .Design portfolio — websites, e-commerce, branding, and digital projects across multiple industries.',
            'projects'    => $result['data'],
            'paginate'    => $result,
            'services'    => $services,
            'industries'  => $industries,
            'years'       => $years,
            'filters'     => $filters,
            'query'       => $q,
        ]);
    }

    public function show(Request $request, array $params): string
    {
        $project = Project::getBySlugWithRelated($params['slug']);
        if (!$project || $project['status'] !== 'published') abort(404);

        $related  = Project::related($project['id'], 3);
        $prevNext = Project::prevNext($project['id']);

        // Track view
        try {
            Database::query(
                'INSERT INTO analytics_events (event_type, resource_type, resource_id, path, ip_address, created_at) VALUES (?, ?, ?, ?, ?, NOW())',
                ['pageview', 'project', $project['id'], $request->path, $request->ip]
            );
            Database::update('UPDATE projects SET view_count = view_count + 1 WHERE id = ?', [$project['id']]);
        } catch (\Throwable) {}

        $clientName = $project['is_anonymised'] ? 'A Client' : ($project['client_name'] ?? 'A Client');

        return view('public.portfolio.show', [
            'title'       => ($project['meta_title'] ?: $project['title'] . ' | The Paragon .Design'),
            'description' => $project['meta_description'] ?: str_excerpt($project['summary'] ?? '', 160),
            'project'     => $project,
            'clientName'  => $clientName,
            'related'     => $related,
            'prev'        => $prevNext['prev'],
            'next'        => $prevNext['next'],
            'breadcrumb'  => [['label' => 'Work', 'url' => '/work'], ['label' => $project['title']]],
        ]);
    }
}
