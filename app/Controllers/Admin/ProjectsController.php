<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\Validator;
use App\Support\Database;
use App\Support\Logger;
use App\Models\Project;
use App\Models\Service;

class ProjectsController
{
    public function index(Request $request): string
    {
        $page   = max(1, (int) $request->get('page', 1));
        $result = Project::paginate($page, 20, '', [], 'created_at DESC');
        return view('admin.projects.index', [
            'title'    => 'Projects',
            'projects' => $result['data'],
            'paginate' => $result,
        ]);
    }

    public function create(Request $request): string
    {
        return view('admin.projects.form', [
            'title'    => 'Add Project',
            'project'  => null,
            'services' => Service::published(),
            'industries' => Database::select("SELECT * FROM industries WHERE deleted_at IS NULL ORDER BY name ASC"),
        ]);
    }

    public function store(Request $request): void
    {
        $v = Validator::make($request->body, [
            'title'  => 'required|max:200',
            'slug'   => 'required|max:200|unique:projects,slug',
            'status' => 'required|in:draft,review,published,archived',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', $request->body);
            redirect('/admin/projects/create');
        }

        $id = $this->saveProject(0, $request);
        Logger::audit('project_created', ['id' => $id]);
        Session::flash('success', 'Project created.');
        redirect('/admin/projects/' . $id . '/edit');
    }

    public function edit(Request $request, array $params): string
    {
        $project = Project::getWithRelated((int) $params['id']);
        if (!$project) abort(404);
        return view('admin.projects.form', [
            'title'      => 'Edit Project',
            'project'    => $project,
            'services'   => Service::published(),
            'industries' => Database::select("SELECT * FROM industries WHERE deleted_at IS NULL ORDER BY name ASC"),
        ]);
    }

    public function update(Request $request, array $params): void
    {
        $id = (int) $params['id'];
        $v  = Validator::make($request->body, [
            'title'  => 'required|max:200',
            'slug'   => 'required|max:200|unique:projects,slug,' . $id,
            'status' => 'required|in:draft,review,published,archived',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', $request->body);
            redirect('/admin/projects/' . $id . '/edit');
        }

        $this->saveProject($id, $request);
        Logger::audit('project_updated', ['id' => $id]);
        Session::flash('success', 'Project updated.');
        redirect('/admin/projects/' . $id . '/edit');
    }

    public function delete(Request $request, array $params): void
    {
        $id = (int) $params['id'];
        Project::delete($id);
        Logger::audit('project_deleted', ['id' => $id]);
        Session::flash('success', 'Project deleted.');
        redirect('/admin/projects');
    }

    private function saveProject(int $id, Request $request): int|string
    {
        $data = $request->body;
        $fields = [
            'title', 'slug', 'client_name', 'summary', 'status',
            'cover_image', 'card_image', 'project_year', 'project_url',
            'challenge', 'approach', 'deliverables', 'outcome',
            'industry_id', 'is_featured', 'display_priority',
            'is_anonymised', 'meta_title', 'meta_description',
        ];

        $save = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $save[$f] = $data[$f] ?: null;
            }
        }
        $save['is_featured']   = !empty($data['is_featured']) ? 1 : 0;
        $save['is_anonymised'] = !empty($data['is_anonymised']) ? 1 : 0;

        if ($id) {
            Project::update($id, $save);
            $newId = $id;
        } else {
            $newId = (int) Project::create($save);
        }

        // Services
        Database::query('DELETE FROM project_services WHERE project_id = ?', [$newId]);
        foreach ((array) ($data['service_ids'] ?? []) as $sid) {
            if ($sid) {
                Database::insert('INSERT IGNORE INTO project_services (project_id, service_id) VALUES (?, ?)', [$newId, $sid]);
            }
        }

        // Results
        if (isset($data['results'])) {
            Database::query('DELETE FROM project_results WHERE project_id = ?', [$newId]);
            foreach ((array) $data['results'] as $i => $r) {
                if (empty($r['metric'])) continue;
                Database::insert(
                    'INSERT INTO project_results (project_id, metric, value, description, sort_order) VALUES (?, ?, ?, ?, ?)',
                    [$newId, $r['metric'], $r['value'] ?? '', $r['description'] ?? '', $i]
                );
            }
        }

        return $newId;
    }
}
