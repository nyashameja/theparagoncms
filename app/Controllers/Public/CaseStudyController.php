<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\View;
use App\Support\Database;

class CaseStudyController
{
    public function index(Request $request): void
    {
        $caseStudies = Database::select(
            "SELECT cs.*, i.name AS industry_name, s.name AS service_name
             FROM case_studies cs
             LEFT JOIN industries i ON cs.industry_id = i.id
             LEFT JOIN services s ON cs.service_id = s.id
             WHERE cs.status='published' AND cs.deleted_at IS NULL
             ORDER BY cs.sort_order ASC, cs.created_at DESC"
        );

        View::render('case-studies/index', [
            'title'        => 'Case Studies — The Paragon .Design',
            'metaDescription' => 'Real results for South African businesses. See how The Paragon .Design has helped clients grow online.',
            'caseStudies'  => $caseStudies,
        ]);
    }

    public function show(Request $request, string $slug): void
    {
        $cs = Database::selectOne(
            "SELECT cs.*, i.name AS industry_name, s.name AS service_name
             FROM case_studies cs
             LEFT JOIN industries i ON cs.industry_id = i.id
             LEFT JOIN services s ON cs.service_id = s.id
             WHERE cs.slug=? AND cs.status='published' AND cs.deleted_at IS NULL",
            [$slug]
        );
        if (!$cs) abort(404);

        View::render('case-studies/show', [
            'title'           => $cs['title'] . ' — Case Study',
            'metaDescription' => $cs['excerpt'] ?? '',
            'caseStudy'       => $cs,
        ]);
    }
}
