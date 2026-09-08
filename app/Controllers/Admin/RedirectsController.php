<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\View;
use App\Support\Validator;
use App\Support\Database;

class RedirectsController
{
    public function index(Request $request): void
    {
        $redirects = Database::select("SELECT * FROM redirects ORDER BY id DESC");
        View::render('admin/redirects/index', ['title' => 'Redirects', 'redirects' => $redirects]);
    }

    public function create(Request $request): void
    {
        View::render('admin/redirects/form', ['title' => 'New Redirect', 'redirect' => [], 'errors' => []]);
    }

    public function store(Request $request): void
    {
        $data = $request->all();
        $v = Validator::make($data, ['source_path' => 'required|max:500', 'target_url' => 'required|max:500']);
        if ($v->fails()) {
            View::render('admin/redirects/form', ['title' => 'New Redirect', 'redirect' => $data, 'errors' => $v->errors()]);
            return;
        }
        Database::insert(
            "INSERT INTO redirects (source_path, target_url, redirect_type, is_active, created_at, updated_at) VALUES (?,?,?,?,NOW(),NOW())",
            [$data['source_path'], $data['target_url'], in_array($data['redirect_type'] ?? '', ['301','302']) ? $data['redirect_type'] : '301', !empty($data['is_active']) ? 1 : 0]
        );
        Session::flash('success', 'Redirect created.');
        redirect('/admin/redirects');
    }

    public function edit(Request $request, int $id): void
    {
        $r = Database::selectOne("SELECT * FROM redirects WHERE id=?", [$id]);
        if (!$r) abort(404);
        View::render('admin/redirects/form', ['title' => 'Edit Redirect', 'redirect' => $r, 'errors' => []]);
    }

    public function update(Request $request, int $id): void
    {
        $data = $request->all();
        Database::update(
            "UPDATE redirects SET source_path=?, target_url=?, redirect_type=?, is_active=?, updated_at=NOW() WHERE id=?",
            [$data['source_path'], $data['target_url'], in_array($data['redirect_type'] ?? '', ['301','302']) ? $data['redirect_type'] : '301', !empty($data['is_active']) ? 1 : 0, $id]
        );
        Session::flash('success', 'Redirect updated.');
        redirect('/admin/redirects');
    }

    public function delete(Request $request, int $id): void
    {
        Database::delete("DELETE FROM redirects WHERE id=?", [$id]);
        Session::flash('success', 'Redirect deleted.');
        redirect('/admin/redirects');
    }
}
