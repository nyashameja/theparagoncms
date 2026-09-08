<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\View;
use App\Support\Validator;
use App\Models\Service;
use App\Support\Database;

class PackagesController
{
    public function index(Request $request): void
    {
        $packages = Database::select("SELECT * FROM packages WHERE deleted_at IS NULL ORDER BY sort_order ASC, id DESC");
        View::render('admin/packages/index', ['title' => 'Packages', 'packages' => $packages]);
    }

    public function create(Request $request): void
    {
        View::render('admin/packages/form', [
            'title' => 'New Package', 'package' => [], 'errors' => [],
            'services' => Service::published(),
        ]);
    }

    public function store(Request $request): void
    {
        $data = $request->all();
        $v = Validator::make($data, ['name' => 'required|max:200']);
        if ($v->fails()) {
            View::render('admin/packages/form', ['title' => 'New Package', 'package' => $data, 'errors' => $v->errors(), 'services' => Service::published()]);
            return;
        }
        $id = Database::insert(
            "INSERT INTO packages (name, category, short_description, starting_price, price_note, billing_type, included_items, exclusions, cta_label, cta_url, service_id, is_featured, custom_quote, status, sort_order, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())",
            [$data['name'], $data['category'] ?? 'general', $data['short_description'] ?? null, $data['starting_price'] ?? null, $data['price_note'] ?? null, $data['billing_type'] ?? 'once_off',
             json_encode(array_filter(array_map('trim', explode("\n", $data['included_text'] ?? '')))), $data['exclusions'] ?? null,
             $data['cta_label'] ?? 'Get Started', $data['cta_url'] ?? '/contact',
             !empty($data['service_id']) ? (int)$data['service_id'] : null,
             !empty($data['is_featured']) ? 1 : 0, !empty($data['custom_quote']) ? 1 : 0,
             in_array($data['status'] ?? '', ['draft','published','archived']) ? $data['status'] : 'draft',
             (int)($data['sort_order'] ?? 0)]
        );
        Session::flash('success', 'Package created.');
        redirect('/admin/packages/' . $id . '/edit');
    }

    public function edit(Request $request, int $id): void
    {
        $pkg = Database::selectOne("SELECT * FROM packages WHERE id = ? AND deleted_at IS NULL", [$id]);
        if (!$pkg) abort(404);
        View::render('admin/packages/form', ['title' => 'Edit Package', 'package' => $pkg, 'errors' => [], 'services' => Service::published()]);
    }

    public function update(Request $request, int $id): void
    {
        $pkg = Database::selectOne("SELECT * FROM packages WHERE id = ? AND deleted_at IS NULL", [$id]);
        if (!$pkg) abort(404);
        $data = $request->all();
        $v = Validator::make($data, ['name' => 'required|max:200']);
        if ($v->fails()) {
            View::render('admin/packages/form', ['title' => 'Edit Package', 'package' => array_merge($pkg, $data), 'errors' => $v->errors(), 'services' => Service::published()]);
            return;
        }
        Database::update(
            "UPDATE packages SET name=?, category=?, short_description=?, starting_price=?, price_note=?, billing_type=?, included_items=?, exclusions=?, cta_label=?, cta_url=?, service_id=?, is_featured=?, custom_quote=?, status=?, sort_order=?, updated_at=NOW() WHERE id=?",
            [$data['name'], $data['category'] ?? 'general', $data['short_description'] ?? null, $data['starting_price'] ?? null, $data['price_note'] ?? null, $data['billing_type'] ?? 'once_off',
             json_encode(array_filter(array_map('trim', explode("\n", $data['included_text'] ?? '')))), $data['exclusions'] ?? null,
             $data['cta_label'] ?? 'Get Started', $data['cta_url'] ?? '/contact',
             !empty($data['service_id']) ? (int)$data['service_id'] : null,
             !empty($data['is_featured']) ? 1 : 0, !empty($data['custom_quote']) ? 1 : 0,
             in_array($data['status'] ?? '', ['draft','published','archived']) ? $data['status'] : 'draft',
             (int)($data['sort_order'] ?? 0), $id]
        );
        Session::flash('success', 'Package updated.');
        redirect('/admin/packages/' . $id . '/edit');
    }

    public function delete(Request $request, int $id): void
    {
        Database::update("UPDATE packages SET deleted_at=NOW() WHERE id=?", [$id]);
        Session::flash('success', 'Package deleted.');
        redirect('/admin/packages');
    }
}
