<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\View;
use App\Support\Validator;
use App\Models\Industry;

class IndustriesController
{
    public function index(Request $request): void
    {
        View::render('admin/industries/index', [
            'title'      => 'Industries',
            'industries' => Industry::all(['deleted_at IS NULL'], 'sort_order ASC, name ASC'),
        ]);
    }

    public function create(Request $request): void
    {
        View::render('admin/industries/form', ['title' => 'New Industry', 'industry' => [], 'errors' => []]);
    }

    public function store(Request $request): void
    {
        $data = $request->all();
        $v = Validator::make($data, ['name' => 'required|max:200', 'slug' => 'required|max:200|unique:industries,slug']);
        if ($v->fails()) {
            View::render('admin/industries/form', ['title' => 'New Industry', 'industry' => $data, 'errors' => $v->errors()]);
            return;
        }
        $id = Industry::create($this->buildFields($data));
        Session::flash('success', 'Industry created.');
        redirect('/admin/industries/' . $id . '/edit');
    }

    public function edit(Request $request, int $id): void
    {
        View::render('admin/industries/form', ['title' => 'Edit Industry', 'industry' => Industry::findOrFail($id), 'errors' => []]);
    }

    public function update(Request $request, int $id): void
    {
        Industry::findOrFail($id);
        $data = $request->all();
        $v = Validator::make($data, ['name' => 'required|max:200', 'slug' => 'required|max:200|unique:industries,slug,' . $id]);
        if ($v->fails()) {
            View::render('admin/industries/form', ['title' => 'Edit Industry', 'industry' => array_merge(Industry::find($id), $data), 'errors' => $v->errors()]);
            return;
        }
        Industry::update($id, $this->buildFields($data));
        Session::flash('success', 'Industry updated.');
        redirect('/admin/industries/' . $id . '/edit');
    }

    public function delete(Request $request, int $id): void
    {
        Industry::findOrFail($id);
        Industry::delete($id);
        Session::flash('success', 'Industry deleted.');
        redirect('/admin/industries');
    }

    private function buildFields(array $d): array
    {
        return [
            'name'             => $d['name'] ?? '',
            'slug'             => $d['slug'] ?? '',
            'description'      => $d['description'] ?? null,
            'hero_image'       => $d['hero_image'] ?? null,
            'status'           => in_array($d['status'] ?? '', ['draft','published','archived']) ? $d['status'] : 'draft',
            'is_featured'      => !empty($d['is_featured']) ? 1 : 0,
            'sort_order'       => (int)($d['sort_order'] ?? 0),
            'meta_title'       => $d['meta_title'] ?? null,
            'meta_description' => $d['meta_description'] ?? null,
        ];
    }
}
