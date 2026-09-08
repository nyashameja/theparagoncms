<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\View;
use App\Support\Database;

class MenusController
{
    public function index(Request $request): void
    {
        $menus = Database::select("SELECT * FROM menus WHERE deleted_at IS NULL ORDER BY name ASC");
        View::render('admin/menus/index', ['title' => 'Menus', 'menus' => $menus]);
    }

    public function create(Request $request): void
    {
        View::render('admin/menus/form', ['title' => 'New Menu', 'menu' => [], 'items' => [], 'errors' => []]);
    }

    public function store(Request $request): void
    {
        $name     = trim($request->input('name', ''));
        $location = trim($request->input('location', ''));
        $errors   = [];

        if (!$name)     $errors['name']     = 'Name is required.';
        if (!$location) $errors['location'] = 'Location is required.';

        if ($errors) {
            View::render('admin/menus/form', ['title' => 'New Menu', 'menu' => $request->all(), 'items' => [], 'errors' => $errors]);
            return;
        }

        $id = Database::insert(
            "INSERT INTO menus (name, location, created_at, updated_at) VALUES (?, ?, NOW(), NOW())",
            [$name, $location]
        );
        Session::flash('success', 'Menu created.');
        redirect('/admin/menus/' . $id . '/edit');
    }

    public function edit(Request $request, int $id): void
    {
        $menu = Database::selectOne("SELECT * FROM menus WHERE id=? AND deleted_at IS NULL", [$id]);
        if (!$menu) abort(404);
        $items = Database::select(
            "SELECT * FROM menu_items WHERE menu_id=? AND deleted_at IS NULL ORDER BY sort_order ASC",
            [$id]
        );
        View::render('admin/menus/form', ['title' => 'Edit Menu', 'menu' => $menu, 'items' => $items, 'errors' => []]);
    }

    public function update(Request $request, int $id): void
    {
        $menu = Database::selectOne("SELECT * FROM menus WHERE id=? AND deleted_at IS NULL", [$id]);
        if (!$menu) abort(404);

        Database::update(
            "UPDATE menus SET name=?, location=?, updated_at=NOW() WHERE id=?",
            [trim($request->input('name', '')), trim($request->input('location', '')), $id]
        );

        /* Rebuild items: delete all then re-insert */
        Database::update("DELETE FROM menu_items WHERE menu_id=?", [$id]);
        $labels  = $request->input('item_label',  []);
        $urls    = $request->input('item_url',     []);
        $targets = $request->input('item_target',  []);
        $order   = 0;
        foreach ($labels as $i => $label) {
            $label = trim($label);
            $url   = trim($urls[$i] ?? '');
            if (!$label || !$url) continue;
            Database::insert(
                "INSERT INTO menu_items (menu_id, label, url, target, sort_order, created_at, updated_at) VALUES (?,?,?,?,?,NOW(),NOW())",
                [$id, $label, $url, $targets[$i] ?? '_self', $order++]
            );
        }

        Session::flash('success', 'Menu saved.');
        redirect('/admin/menus/' . $id . '/edit');
    }

    public function destroy(Request $request, int $id): void
    {
        Database::update("UPDATE menus SET deleted_at=NOW() WHERE id=?", [$id]);
        Session::flash('success', 'Menu deleted.');
        redirect('/admin/menus');
    }
}
