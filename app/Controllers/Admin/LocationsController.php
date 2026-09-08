<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\View;
use App\Support\Validator;
use App\Models\Location;

class LocationsController
{
    public function index(Request $request): void
    {
        View::render('admin/locations/index', [
            'title'     => 'Locations',
            'locations' => Location::all(['deleted_at IS NULL'], 'sort_order ASC, name ASC'),
        ]);
    }

    public function create(Request $request): void
    {
        View::render('admin/locations/form', ['title' => 'New Location', 'location' => [], 'errors' => []]);
    }

    public function store(Request $request): void
    {
        $data = $request->all();
        $v = Validator::make($data, ['name' => 'required|max:200', 'slug' => 'required|max:200|unique:locations,slug']);
        if ($v->fails()) {
            View::render('admin/locations/form', ['title' => 'New Location', 'location' => $data, 'errors' => $v->errors()]);
            return;
        }
        $id = Location::create($this->buildFields($data));
        Session::flash('success', 'Location created.');
        redirect('/admin/locations/' . $id . '/edit');
    }

    public function edit(Request $request, int $id): void
    {
        View::render('admin/locations/form', ['title' => 'Edit Location', 'location' => Location::findOrFail($id), 'errors' => []]);
    }

    public function update(Request $request, int $id): void
    {
        Location::findOrFail($id);
        $data = $request->all();
        $v = Validator::make($data, ['name' => 'required|max:200', 'slug' => 'required|max:200|unique:locations,slug,' . $id]);
        if ($v->fails()) {
            View::render('admin/locations/form', ['title' => 'Edit Location', 'location' => array_merge(Location::find($id), $data), 'errors' => $v->errors()]);
            return;
        }
        Location::update($id, $this->buildFields($data));
        Session::flash('success', 'Location updated.');
        redirect('/admin/locations/' . $id . '/edit');
    }

    public function delete(Request $request, int $id): void
    {
        Location::findOrFail($id);
        Location::delete($id);
        Session::flash('success', 'Location deleted.');
        redirect('/admin/locations');
    }

    private function buildFields(array $d): array
    {
        return [
            'name'             => $d['name'] ?? '',
            'slug'             => $d['slug'] ?? '',
            'type'             => in_array($d['type'] ?? '', ['primary','service_area']) ? $d['type'] : 'service_area',
            'province'         => $d['province'] ?? null,
            'description'      => $d['description'] ?? null,
            'status'           => in_array($d['status'] ?? '', ['draft','published','archived']) ? $d['status'] : 'draft',
            'sort_order'       => (int)($d['sort_order'] ?? 0),
            'meta_title'       => $d['meta_title'] ?? null,
            'meta_description' => $d['meta_description'] ?? null,
        ];
    }
}
