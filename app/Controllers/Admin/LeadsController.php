<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\Validator;
use App\Support\Database;
use App\Support\Logger;
use App\Models\Lead;

class LeadsController
{
    public function index(Request $request): string
    {
        $filters = [
            'status'      => $request->get('status', ''),
            'search'      => $request->get('q', ''),
            'service'     => $request->get('service', ''),
            'assigned_to' => $request->get('assigned_to', ''),
        ];

        $page   = max(1, (int) $request->get('page', 1));
        $result = Lead::inbox($page, 25, $filters);

        return view('admin.leads.index', [
            'title'    => 'Lead Inbox',
            'leads'    => $result['data'],
            'paginate' => $result,
            'filters'  => $filters,
            'statuses' => Lead::allStatuses(),
            'counts'   => Lead::countsPerStatus(),
        ]);
    }

    public function show(Request $request, array $params): string
    {
        $lead = Lead::getWithRelated((int) $params['id']);
        if (!$lead) abort(404);

        // Log access for POPIA
        Logger::audit('lead_viewed', ['lead_id' => $lead['id'], 'user_id' => Session::get('user_id')]);

        $users = Database::select("SELECT id, name FROM users WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC");

        return view('admin.leads.show', [
            'title'  => 'Lead: ' . e($lead['name']),
            'lead'   => $lead,
            'users'  => $users,
            'statuses' => Lead::allStatuses(),
        ]);
    }

    public function update(Request $request, array $params): void
    {
        $lead = Lead::find((int) $params['id']);
        if (!$lead) abort(404);

        $data = [];
        $allowed = ['status', 'priority', 'assigned_to', 'follow_up_date'];
        foreach ($allowed as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->get($field) ?: null;
            }
        }

        if (!empty($data)) {
            Lead::update($lead['id'], $data);
            Lead::logActivity($lead['id'], Session::get('user_id'), 'updated', $data);
        }

        $note = trim($request->get('note', ''));
        if ($note) {
            Lead::addNote($lead['id'], Session::get('user_id'), $note);
            Lead::logActivity($lead['id'], Session::get('user_id'), 'note_added');
        }

        Logger::audit('lead_updated', ['lead_id' => $lead['id'], 'changes' => $data]);
        Session::flash('success', 'Lead updated.');
        redirect('/admin/leads/' . $lead['id']);
    }

    public function addNote(Request $request, array $params): void
    {
        $lead = Lead::find((int) $params['id']);
        if (!$lead) abort(404);

        $content = trim($request->get('content', ''));
        if (!$content) {
            Session::flash('error', 'Note content is required.');
            redirect('/admin/leads/' . $lead['id']);
        }

        Lead::addNote($lead['id'], Session::get('user_id'), $content);
        Lead::logActivity($lead['id'], Session::get('user_id'), 'note_added');

        Session::flash('success', 'Note added.');
        redirect('/admin/leads/' . $lead['id']);
    }

    public function delete(Request $request, array $params): void
    {
        $lead = Lead::find((int) $params['id']);
        if (!$lead) abort(404);

        Lead::delete($lead['id']);
        Logger::audit('lead_deleted', ['lead_id' => $lead['id']]);
        Session::flash('success', 'Lead archived.');
        redirect('/admin/leads');
    }

    public function export(Request $request): void
    {
        Logger::audit('leads_export', ['user_id' => Session::get('user_id')]);

        $filters = [
            'status'  => $request->get('status', ''),
            'search'  => $request->get('q', ''),
        ];

        $result = Lead::inbox(1, 1000, $filters);
        $leads  = $result['data'];

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="leads-' . date('Y-m-d') . '.csv"');
        header('Pragma: no-cache');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Company', 'Email', 'Phone', 'Service', 'Status', 'Priority', 'Source', 'Created', 'Follow Up']);

        foreach ($leads as $lead) {
            fputcsv($output, [
                $lead['id'], $lead['name'], $lead['company'] ?? '',
                $lead['email'], $lead['phone'] ?? '', $lead['service_type'] ?? '',
                $lead['status'], $lead['priority'] ?? '', $lead['source'] ?? '',
                $lead['created_at'], $lead['follow_up_date'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }
}
