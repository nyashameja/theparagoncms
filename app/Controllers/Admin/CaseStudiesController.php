<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\View;
use App\Support\Validator;
use App\Models\CaseStudy;
use App\Models\Industry;

class CaseStudiesController
{
    public function index(Request $request): void
    {
        $page = (int)($request->get('page', 1));
        $result = CaseStudy::paginate(20, $page, ['deleted_at IS NULL'], 'id DESC');
        View::render('admin/case-studies/index', [
            'title' => 'Case Studies',
            'caseStudies' => $result['data'],
            'pagination' => $result['pagination'],
        ]);
    }

    public function create(Request $request): void
    {
        View::render('admin/case-studies/form', [
            'title' => 'New Case Study',
            'caseStudy' => [],
            'industries' => Industry::all(['deleted_at IS NULL', "status = 'published'"], 'name ASC'),
            'errors' => [],
        ]);
    }

    public function store(Request $request): void
    {
        $data = $request->all();
        $v = Validator::make($data, [
            'title' => 'required|max:300',
            'slug'  => 'required|max:300|unique:case_studies,slug',
        ]);
        if ($v->fails()) {
            View::render('admin/case-studies/form', [
                'title' => 'New Case Study',
                'caseStudy' => $data,
                'industries' => Industry::all(['deleted_at IS NULL', "status = 'published'"], 'name ASC'),
                'errors' => $v->errors(),
            ]);
            return;
        }
        $id = CaseStudy::create($this->buildFields($data));
        Session::flash('success', 'Case study created.');
        redirect('/admin/case-studies/' . $id . '/edit');
    }

    public function edit(Request $request, int $id): void
    {
        $cs = CaseStudy::findOrFail($id);
        View::render('admin/case-studies/form', [
            'title' => 'Edit Case Study',
            'caseStudy' => $cs,
            'industries' => Industry::all(['deleted_at IS NULL', "status = 'published'"], 'name ASC'),
            'errors' => [],
        ]);
    }

    public function update(Request $request, int $id): void
    {
        $cs = CaseStudy::findOrFail($id);
        $data = $request->all();
        $v = Validator::make($data, [
            'title' => 'required|max:300',
            'slug'  => 'required|max:300|unique:case_studies,slug,' . $id,
        ]);
        if ($v->fails()) {
            View::render('admin/case-studies/form', [
                'title' => 'Edit Case Study',
                'caseStudy' => array_merge($cs, $data),
                'industries' => Industry::all(['deleted_at IS NULL', "status = 'published'"], 'name ASC'),
                'errors' => $v->errors(),
            ]);
            return;
        }
        CaseStudy::update($id, $this->buildFields($data));
        Session::flash('success', 'Case study updated.');
        redirect('/admin/case-studies/' . $id . '/edit');
    }

    public function delete(Request $request, int $id): void
    {
        CaseStudy::findOrFail($id);
        CaseStudy::delete($id);
        Session::flash('success', 'Case study deleted.');
        redirect('/admin/case-studies');
    }

    private function buildFields(array $d): array
    {
        return [
            'title'            => $d['title'] ?? '',
            'slug'             => $d['slug'] ?? '',
            'client_name'      => $d['client_name'] ?? null,
            'industry_id'      => !empty($d['industry_id']) ? (int)$d['industry_id'] : null,
            'summary'          => $d['summary'] ?? null,
            'challenge'        => $d['challenge'] ?? null,
            'solution'         => $d['solution'] ?? null,
            'outcome'          => $d['outcome'] ?? null,
            'featured_image'   => $d['featured_image'] ?? null,
            'status'           => in_array($d['status'] ?? '', ['draft','published','archived']) ? $d['status'] : 'draft',
            'is_featured'      => !empty($d['is_featured']) ? 1 : 0,
            'published_at'     => !empty($d['published_at']) ? $d['published_at'] : null,
            'meta_title'       => $d['meta_title'] ?? null,
            'meta_description' => $d['meta_description'] ?? null,
        ];
    }
}
