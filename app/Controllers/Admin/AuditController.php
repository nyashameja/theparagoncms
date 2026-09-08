<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\View;
use App\Support\Database;

class AuditController
{
    public function index(Request $request): void
    {
        $audits = Database::select(
            "SELECT * FROM audit_requests WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 100"
        );
        View::render('admin/audits/index', ['title' => 'Audit Requests', 'audits' => $audits]);
    }

    public function show(Request $request, int $id): void
    {
        $audit = Database::selectOne("SELECT * FROM audit_requests WHERE id=? AND deleted_at IS NULL", [$id]);
        if (!$audit) abort(404);
        $report = Database::selectOne("SELECT * FROM audit_reports WHERE audit_request_id=?", [$id]);
        View::render('admin/audits/show', [
            'title'  => 'Audit: ' . $audit['name'],
            'audit'  => $audit,
            'report' => $report,
        ]);
    }

    public function updateStatus(Request $request, int $id): void
    {
        $status = $request->input('status');
        $allowed = ['pending','in_progress','completed','sent'];
        if (in_array($status, $allowed)) {
            Database::update("UPDATE audit_requests SET status=?, updated_at=NOW() WHERE id=?", [$status, $id]);
            Session::flash('success', 'Status updated.');
        }
        redirect('/admin/audits/' . $id);
    }
}
