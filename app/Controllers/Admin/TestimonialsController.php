<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\View;
use App\Support\Validator;
use App\Models\Testimonial;

class TestimonialsController
{
    public function index(Request $request): void
    {
        View::render('admin/testimonials/index', [
            'title'        => 'Testimonials',
            'testimonials' => Testimonial::all(['deleted_at IS NULL'], 'sort_order ASC, id DESC'),
        ]);
    }

    public function create(Request $request): void
    {
        View::render('admin/testimonials/form', ['title' => 'New Testimonial', 'testimonial' => [], 'errors' => []]);
    }

    public function store(Request $request): void
    {
        $data = $request->all();
        $v = Validator::make($data, ['author_name' => 'required|max:200', 'content' => 'required']);
        if ($v->fails()) {
            View::render('admin/testimonials/form', ['title' => 'New Testimonial', 'testimonial' => $data, 'errors' => $v->errors()]);
            return;
        }
        $id = Testimonial::create($this->buildFields($data));
        Session::flash('success', 'Testimonial saved.');
        redirect('/admin/testimonials/' . $id . '/edit');
    }

    public function edit(Request $request, int $id): void
    {
        View::render('admin/testimonials/form', [
            'title'       => 'Edit Testimonial',
            'testimonial' => Testimonial::findOrFail($id),
            'errors'      => [],
        ]);
    }

    public function update(Request $request, int $id): void
    {
        Testimonial::findOrFail($id);
        $data = $request->all();
        $v = Validator::make($data, ['author_name' => 'required|max:200', 'content' => 'required']);
        if ($v->fails()) {
            View::render('admin/testimonials/form', ['title' => 'Edit Testimonial', 'testimonial' => array_merge(Testimonial::find($id), $data), 'errors' => $v->errors()]);
            return;
        }
        Testimonial::update($id, $this->buildFields($data));
        Session::flash('success', 'Testimonial updated.');
        redirect('/admin/testimonials/' . $id . '/edit');
    }

    public function delete(Request $request, int $id): void
    {
        Testimonial::findOrFail($id);
        Testimonial::delete($id);
        Session::flash('success', 'Testimonial deleted.');
        redirect('/admin/testimonials');
    }

    private function buildFields(array $d): array
    {
        return [
            'author_name'       => $d['author_name'] ?? '',
            'author_title'      => $d['author_title'] ?? null,
            'author_company'    => $d['author_company'] ?? null,
            'content'           => $d['content'] ?? '',
            'rating'            => !empty($d['rating']) ? (int)$d['rating'] : null,
            'source'            => $d['source'] ?? null,
            'source_url'        => $d['source_url'] ?? null,
            'status'            => in_array($d['status'] ?? '', ['pending','approved','rejected']) ? $d['status'] : 'pending',
            'is_featured'       => !empty($d['is_featured']) ? 1 : 0,
            'sort_order'        => (int)($d['sort_order'] ?? 0),
            'permission_granted'=> !empty($d['permission_granted']) ? 1 : 0,
        ];
    }
}
