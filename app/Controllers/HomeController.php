<?php

namespace App\Controllers;

use App\Support\Request;
use App\Support\Database;
use App\Models\Service;
use App\Models\Project;
use App\Models\CaseStudy;
use App\Models\Testimonial;
use App\Models\Article;
use App\Models\Industry;
use App\Models\Setting;

class HomeController
{
    public function index(Request $request): string
    {
        $brand      = Setting::getBrand();
        $services   = Service::featured();
        $projects   = Project::featured(6);
        $caseStudy  = CaseStudy::featured(1);
        $testimonials = Testimonial::featured(6);
        $articles   = Article::latest(3);
        $industries = Industry::featured(8);

        $clientLogos = Database::select("SELECT * FROM client_logos WHERE status = 'approved' AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT 12");
        $stats       = Database::select("SELECT * FROM trust_statistics WHERE is_active = 1 AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT 6");
        $packages    = Database::select("SELECT * FROM packages WHERE is_featured = 1 AND status = 'published' AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT 3");

        $homeSections = Database::select("SELECT * FROM page_sections WHERE page_id = 1 AND is_active = 1 ORDER BY sort_order ASC");

        return view('public.home.index', [
            'title'        => Setting::get('site_title', 'The Paragon .Design — Premium Digital Agency in Johannesburg'),
            'description'  => Setting::get('site_description', 'The Paragon .Design builds custom websites, e-commerce solutions, and digital strategies for South African businesses.'),
            'brand'        => $brand,
            'services'     => $services,
            'projects'     => $projects,
            'caseStudy'    => $caseStudy[0] ?? null,
            'testimonials' => $testimonials,
            'articles'     => $articles,
            'industries'   => $industries,
            'clientLogos'  => $clientLogos,
            'stats'        => $stats,
            'packages'     => $packages,
            'homeSections' => $homeSections,
        ]);
    }

    public function about(Request $request): string
    {
        $testimonials = Testimonial::featured(3);
        $teamMembers  = Database::select("SELECT * FROM team_members WHERE is_active = 1 AND deleted_at IS NULL ORDER BY sort_order ASC");
        $stats        = Database::select("SELECT * FROM trust_statistics WHERE is_active = 1 AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT 4");

        return view('public.about', [
            'title'       => 'About The Paragon .Design | Premium Digital Agency Johannesburg',
            'description' => 'Learn about The Paragon .Design — a premium South African digital agency specialising in web design, branding, SEO, and digital marketing.',
            'testimonials' => $testimonials,
            'teamMembers' => $teamMembers,
            'stats'       => $stats,
        ]);
    }

    public function contact(Request $request): string
    {
        return view('public.contact', [
            'title'       => 'Contact Us | The Paragon .Design',
            'description' => 'Get in touch with The Paragon .Design. We\'re based in Johannesburg and serve clients across South Africa.',
        ]);
    }

    public function privacy(Request $request): string
    {
        $content = Setting::get('privacy_policy_content', '');
        return view('public.policy', [
            'title'   => 'Privacy Policy | The Paragon .Design',
            'heading' => 'Privacy Policy',
            'content' => $content,
        ]);
    }

    public function terms(Request $request): string
    {
        $content = Setting::get('terms_content', '');
        return view('public.policy', [
            'title'   => 'Terms of Service | The Paragon .Design',
            'heading' => 'Terms of Service',
            'content' => $content,
        ]);
    }

    public function cookies(Request $request): string
    {
        $content = Setting::get('cookie_policy_content', '');
        return view('public.policy', [
            'title'   => 'Cookie Policy | The Paragon .Design',
            'heading' => 'Cookie Policy',
            'content' => $content,
        ]);
    }

    public function hostingTerms(Request $request): string
    {
        $content = Setting::get('hosting_terms_content', '');
        return view('public.policy', [
            'title'   => 'Hosting Terms | The Paragon .Design',
            'heading' => 'Hosting & Acceptable Use Policy',
            'content' => $content,
        ]);
    }

    public function packages(Request $request): string
    {
        $packages  = Database::select("SELECT * FROM packages WHERE status = 'published' AND deleted_at IS NULL ORDER BY category ASC, sort_order ASC");
        $categories = array_unique(array_column($packages, 'category'));

        return view('public.packages', [
            'title'       => 'Packages & Starting Prices | The Paragon .Design',
            'description' => 'Explore our service packages and starting prices. Web design, SEO, hosting, and more from The Paragon .Design.',
            'packages'    => $packages,
            'categories'  => $categories,
        ]);
    }

    public function hosting(Request $request): string
    {
        $plans = Database::select("SELECT * FROM packages WHERE category = 'hosting' AND status = 'published' AND deleted_at IS NULL ORDER BY sort_order ASC");
        $care  = Database::select("SELECT * FROM packages WHERE category = 'care' AND status = 'published' AND deleted_at IS NULL ORDER BY sort_order ASC");

        return view('public.hosting', [
            'title'       => 'Website Hosting & Care Plans | The Paragon .Design',
            'description' => 'Managed WordPress hosting and website care plans by The Paragon .Design. Fast, secure, and locally supported.',
            'plans'       => $plans,
            'care'        => $care,
        ]);
    }

    public function search(Request $request): string
    {
        $query  = trim($request->get('q', ''));
        $type   = $request->get('type', '');
        $page   = max(1, (int) $request->get('page', 1));
        $results = [];

        if ($query) {
            $results = $this->globalSearch($query, $type, $page);
            // Log popular searches
            try {
                Database::query(
                    'INSERT INTO analytics_events (event_type, data, ip_address, created_at) VALUES (?, ?, ?, NOW())',
                    ['search', json_encode(['query' => $query, 'type' => $type]), $request->ip]
                );
            } catch (\Throwable) {}
        }

        return view('public.search', [
            'title'   => $query ? "Search: $query | The Paragon .Design" : 'Search | The Paragon .Design',
            'query'   => $query,
            'type'    => $type,
            'results' => $results,
            'page'    => $page,
        ]);
    }

    private function globalSearch(string $query, string $type, int $page): array
    {
        $q      = '%' . $query . '%';
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        $results = [];

        if (!$type || $type === 'services') {
            $services = Database::select(
                "SELECT 'service' as type, id, name as title, slug, short_description as excerpt FROM services WHERE (name LIKE ? OR short_description LIKE ?) AND status = 'published' AND deleted_at IS NULL LIMIT 5",
                [$q, $q]
            );
            $results = array_merge($results, $services);
        }

        if (!$type || $type === 'projects') {
            $projects = Database::select(
                "SELECT 'project' as type, id, title, slug, summary as excerpt FROM projects WHERE (title LIKE ? OR summary LIKE ?) AND status = 'published' AND deleted_at IS NULL LIMIT 5",
                [$q, $q]
            );
            $results = array_merge($results, $projects);
        }

        if (!$type || $type === 'articles') {
            $articles = Database::select(
                "SELECT 'article' as type, id, title, slug, excerpt FROM articles WHERE (title LIKE ? OR excerpt LIKE ? OR content LIKE ?) AND status = 'published' AND deleted_at IS NULL LIMIT 5",
                [$q, $q, $q]
            );
            $results = array_merge($results, $articles);
        }

        if (!$type || $type === 'case_studies') {
            $cs = Database::select(
                "SELECT 'case_study' as type, id, title, slug, executive_summary as excerpt FROM case_studies WHERE (title LIKE ? OR executive_summary LIKE ?) AND status = 'published' AND deleted_at IS NULL LIMIT 5",
                [$q, $q]
            );
            $results = array_merge($results, $cs);
        }

        return $results;
    }
}
