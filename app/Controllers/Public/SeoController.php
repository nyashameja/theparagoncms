<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\Database;

class SeoController
{
    public function sitemap(Request $request): void
    {
        header('Content-Type: application/xml; charset=UTF-8');
        $base = rtrim(setting('app_url', 'https://theparagon.design'), '/');

        $urls = [];

        /* Static pages */
        foreach (['/', '/about', '/services', '/portfolio', '/case-studies', '/blog', '/packages', '/contact', '/free-audit', '/get-quote', '/book-consultation'] as $path) {
            $urls[] = ['loc' => $base . $path, 'changefreq' => 'monthly', 'priority' => $path === '/' ? '1.0' : '0.8'];
        }

        /* Services */
        $services = Database::select("SELECT slug, updated_at FROM services WHERE status='published' AND deleted_at IS NULL");
        foreach ($services as $r) {
            $urls[] = ['loc' => $base . '/services/' . $r['slug'], 'lastmod' => substr($r['updated_at'], 0, 10), 'changefreq' => 'monthly', 'priority' => '0.7'];
        }

        /* Projects */
        $projects = Database::select("SELECT slug, updated_at FROM projects WHERE status='published' AND deleted_at IS NULL");
        foreach ($projects as $r) {
            $urls[] = ['loc' => $base . '/portfolio/' . $r['slug'], 'lastmod' => substr($r['updated_at'], 0, 10), 'changefreq' => 'monthly', 'priority' => '0.6'];
        }

        /* Articles */
        $articles = Database::select("SELECT slug, published_at FROM articles WHERE status='published' AND deleted_at IS NULL ORDER BY published_at DESC");
        foreach ($articles as $r) {
            $urls[] = ['loc' => $base . '/blog/' . $r['slug'], 'lastmod' => substr($r['published_at'], 0, 10), 'changefreq' => 'weekly', 'priority' => '0.7'];
        }

        /* Case studies */
        $cs = Database::select("SELECT slug, updated_at FROM case_studies WHERE status='published' AND deleted_at IS NULL");
        foreach ($cs as $r) {
            $urls[] = ['loc' => $base . '/case-studies/' . $r['slug'], 'lastmod' => substr($r['updated_at'], 0, 10), 'changefreq' => 'monthly', 'priority' => '0.6'];
        }

        /* Locations */
        $locs = Database::select("SELECT slug FROM locations WHERE is_active=1 AND deleted_at IS NULL");
        foreach ($locs as $r) {
            $urls[] = ['loc' => $base . '/locations/' . $r['slug'], 'changefreq' => 'monthly', 'priority' => '0.5'];
        }

        /* Industries */
        $inds = Database::select("SELECT slug FROM industries WHERE is_active=1 AND deleted_at IS NULL");
        foreach ($inds as $r) {
            $urls[] = ['loc' => $base . '/industries/' . $r['slug'], 'changefreq' => 'monthly', 'priority' => '0.5'];
        }

        /* Pages */
        $pages = Database::select("SELECT slug, updated_at FROM pages WHERE status='published' AND deleted_at IS NULL");
        foreach ($pages as $r) {
            $urls[] = ['loc' => $base . '/page/' . $r['slug'], 'lastmod' => substr($r['updated_at'], 0, 10), 'changefreq' => 'monthly', 'priority' => '0.5'];
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
            if (!empty($u['lastmod']))   echo '    <lastmod>' . htmlspecialchars($u['lastmod'], ENT_XML1) . "</lastmod>\n";
            if (!empty($u['changefreq'])) echo '    <changefreq>' . htmlspecialchars($u['changefreq'], ENT_XML1) . "</changefreq>\n";
            if (!empty($u['priority']))  echo '    <priority>' . htmlspecialchars($u['priority'], ENT_XML1) . "</priority>\n";
            echo "  </url>\n";
        }
        echo '</urlset>';
    }

    public function robots(Request $request): void
    {
        header('Content-Type: text/plain; charset=UTF-8');

        $override = setting('robots_txt');
        if ($override) {
            echo $override;
            return;
        }

        $base = rtrim(setting('app_url', 'https://theparagon.design'), '/');
        echo "User-agent: *\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /installer/\n";
        echo "Disallow: /storage/\n";
        echo "\n";
        echo "Sitemap: {$base}/sitemap.xml\n";
    }
}
