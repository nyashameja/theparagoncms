<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Database;
use App\Models\Lead;
use App\Models\Setting;

class DashboardController
{
    public function index(Request $request): string
    {
        $leadCounts = Lead::countsPerStatus();
        $followUps  = Lead::followUpsDue();

        $recentLeads = Database::select(
            "SELECT * FROM leads WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 10"
        );

        $recentArticles = Database::select(
            "SELECT * FROM articles WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5"
        );

        $recentProjects = Database::select(
            "SELECT * FROM projects WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5"
        );

        $auditRequests = Database::select(
            "SELECT * FROM audit_requests WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5"
        );

        $mediaStats = Database::selectOne(
            "SELECT COUNT(*) as count, SUM(file_size) as total_size FROM media WHERE deleted_at IS NULL"
        );

        $failedEmails = Database::select(
            "SELECT * FROM email_logs WHERE status = 'failed' ORDER BY created_at DESC LIMIT 5"
        );

        $leadsThisMonth = Database::selectOne(
            "SELECT COUNT(*) as count FROM leads WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) AND deleted_at IS NULL"
        );

        $topPages = Database::select(
            "SELECT path, COUNT(*) as views FROM analytics_events WHERE event_type = 'pageview' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY path ORDER BY views DESC LIMIT 10"
        );

        return view('admin.dashboard', [
            'title'          => 'Dashboard',
            'leadCounts'     => $leadCounts,
            'followUps'      => $followUps,
            'recentLeads'    => $recentLeads,
            'recentArticles' => $recentArticles,
            'recentProjects' => $recentProjects,
            'auditRequests'  => $auditRequests,
            'mediaStats'     => $mediaStats,
            'failedEmails'   => $failedEmails,
            'leadsThisMonth' => $leadsThisMonth['count'] ?? 0,
            'topPages'       => $topPages,
        ]);
    }
}
