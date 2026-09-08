<?php

namespace App\Controllers;

use App\Support\Request;
use App\Support\Database;
use App\Models\Setting;
use App\Models\Service;
use App\Models\Project;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Industry;
use App\Models\Location;

class SeoController
{
    public function sitemap(Request $request): void
    {
        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';

        $baseUrl = rtrim(env('APP_URL', 'https://theparagondesign.com'), '/');

        $urls = [];

        // Static pages
        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/services', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/work', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/case-studies', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/insights', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['loc' => '/contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/quote', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => '/consultation', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/website-audit', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/hosting', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/packages', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $urls[] = $page;
        }

        // Services
        $services = Service::published();
        foreach ($services as $s) {
            $urls[] = ['loc' => '/services/' . $s['slug'], 'priority' => '0.8', 'changefreq' => 'monthly', 'lastmod' => $s['updated_at']];
        }

        // Projects
        $projects = Database::select("SELECT slug, updated_at FROM projects WHERE status = 'published' AND deleted_at IS NULL");
        foreach ($projects as $p) {
            $urls[] = ['loc' => '/work/' . $p['slug'], 'priority' => '0.7', 'changefreq' => 'monthly', 'lastmod' => $p['updated_at']];
        }

        // Case Studies
        $caseStudies = Database::select("SELECT slug, updated_at FROM case_studies WHERE status = 'published' AND deleted_at IS NULL");
        foreach ($caseStudies as $cs) {
            $urls[] = ['loc' => '/case-studies/' . $cs['slug'], 'priority' => '0.7', 'changefreq' => 'monthly', 'lastmod' => $cs['updated_at']];
        }

        // Articles
        $articles = Database::select("SELECT slug, updated_at, published_at FROM articles WHERE status = 'published' AND deleted_at IS NULL");
        foreach ($articles as $a) {
            $urls[] = ['loc' => '/insights/' . $a['slug'], 'priority' => '0.6', 'changefreq' => 'weekly', 'lastmod' => $a['updated_at']];
        }

        // Industries
        $industries = Industry::published();
        foreach ($industries as $i) {
            $urls[] = ['loc' => '/industries/' . $i['slug'], 'priority' => '0.6', 'changefreq' => 'monthly'];
        }

        // Locations
        $locations = Location::published();
        foreach ($locations as $l) {
            $urls[] = ['loc' => '/locations/' . $l['slug'], 'priority' => '0.6', 'changefreq' => 'monthly'];
        }

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $url) {
            $loc      = htmlspecialchars($baseUrl . $url['loc'], ENT_QUOTES, 'UTF-8');
            $lastmod  = isset($url['lastmod']) ? '<lastmod>' . date('Y-m-d', strtotime($url['lastmod'])) . '</lastmod>' : '';
            $changefreq = '<changefreq>' . ($url['changefreq'] ?? 'monthly') . '</changefreq>';
            $priority   = '<priority>' . ($url['priority'] ?? '0.5') . '</priority>';

            echo "<url><loc>$loc</loc>$lastmod$changefreq$priority</url>";
        }
        echo '</urlset>';
        exit;
    }

    public function robots(Request $request): void
    {
        header('Content-Type: text/plain; charset=UTF-8');

        $custom = Setting::get('robots_txt_content', '');
        if ($custom) {
            echo $custom;
            exit;
        }

        $baseUrl = rtrim(env('APP_URL', 'https://theparagondesign.com'), '/');
        echo "User-agent: *\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /installer/\n";
        echo "Disallow: /storage/\n";
        echo "Disallow: /search?*\n\n";
        echo "Sitemap: $baseUrl/sitemap.xml\n";
        exit;
    }
}
