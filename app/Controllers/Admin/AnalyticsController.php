<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\View;
use App\Support\Database;

class AnalyticsController
{
    public function index(Request $request): void
    {
        $pageviews = Database::selectOne(
            "SELECT COUNT(*) as cnt FROM analytics_events WHERE event_type='pageview' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        )['cnt'] ?? 0;

        $uniquePages = Database::selectOne(
            "SELECT COUNT(DISTINCT path) as cnt FROM analytics_events WHERE event_type='pageview' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        )['cnt'] ?? 0;

        $ctaClicks = Database::selectOne(
            "SELECT COUNT(*) as cnt FROM analytics_events WHERE event_type='cta_click' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        )['cnt'] ?? 0;

        $formSubmits = Database::selectOne(
            "SELECT COUNT(*) as cnt FROM analytics_events WHERE event_type='form_submit' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        )['cnt'] ?? 0;

        $topPages = Database::select(
            "SELECT path, COUNT(*) as views FROM analytics_events WHERE event_type='pageview' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY path ORDER BY views DESC LIMIT 20"
        );

        $notFoundLogs = Database::select(
            "SELECT path, hits, last_seen_at FROM not_found_logs ORDER BY hits DESC LIMIT 20"
        );

        View::render('admin/analytics/index', [
            'title'         => 'Analytics',
            'pageviews'     => $pageviews,
            'uniquePages'   => $uniquePages,
            'ctaClicks'     => $ctaClicks,
            'formSubmits'   => $formSubmits,
            'topPages'      => $topPages,
            'notFoundLogs'  => $notFoundLogs,
        ]);
    }
}
