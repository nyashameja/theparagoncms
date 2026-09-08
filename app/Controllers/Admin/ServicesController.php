<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\Validator;
use App\Support\Database;
use App\Support\Logger;
use App\Models\Service;

class ServicesController
{
    public function index(Request $request): string
    {
        $services = Database::select('SELECT * FROM services WHERE deleted_at IS NULL ORDER BY sort_order ASC, name ASC');
        return view('admin.services.index', ['title' => 'Services', 'services' => $services]);
    }

    public function create(Request $request): string
    {
        return view('admin.services.form', ['title' => 'Add Service', 'service' => null]);
    }

    public function store(Request $request): void
    {
        $v = Validator::make($request->body, [
            'name'   => 'required|max:200',
            'slug'   => 'required|max:200|unique:services,slug',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', $request->body);
            redirect('/admin/services/create');
        }

        $id = $this->saveService(0, $request->body);
        Logger::audit('service_created', ['id' => $id, 'name' => $request->get('name')]);
        Session::flash('success', 'Service created.');
        redirect('/admin/services/' . $id . '/edit');
    }

    public function edit(Request $request, int $id): string
    {
        $service = Service::getWithRelated($id);
        if (!$service) abort(404);
        return view('admin.services.form', ['title' => 'Edit Service', 'service' => $service]);
    }

    public function update(Request $request, int $id): void
    {
        $v  = Validator::make($request->body, [
            'name'   => 'required|max:200',
            'slug'   => 'required|max:200|unique:services,slug,' . $id,
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', $request->body);
            redirect('/admin/services/' . $id . '/edit');
        }

        $this->saveService($id, $request->body);
        Logger::audit('service_updated', ['id' => $id]);
        Session::flash('success', 'Service updated.');
        redirect('/admin/services/' . $id . '/edit');
    }

    public function delete(Request $request, int $id): void
    {
        Service::delete($id);
        Logger::audit('service_deleted', ['id' => $id]);
        Session::flash('success', 'Service deleted.');
        redirect('/admin/services');
    }

    public function reorder(Request $request): void
    {
        $ids = $request->get('ids', []);
        if (is_array($ids)) {
            Service::reorder($ids);
        }
        if ($request->wantsJson()) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
            exit;
        }
        redirect('/admin/services');
    }

    private function saveService(int $id, array $data): int|string
    {
        $fields = [
            'name', 'slug', 'status', 'short_description', 'long_description',
            'hero_headline', 'hero_copy', 'icon', 'starting_price', 'price_label',
            'meta_title', 'meta_description', 'og_image',
            'primary_cta_label', 'primary_cta_url', 'secondary_cta_label', 'secondary_cta_url',
            'is_featured', 'sort_order', 'schema_type',
        ];

        $save = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $save[$f] = $data[$f] ?: null;
            }
        }
        $save['is_featured'] = !empty($data['is_featured']) ? 1 : 0;

        if ($id) {
            Service::update($id, $save);
            $this->saveRelated($id, $data);
            return $id;
        }

        $newId = Service::create($save);
        $this->saveRelated((int) $newId, $data);
        return $newId;
    }

    private function saveRelated(int $serviceId, array $data): void
    {
        // FAQs
        if (isset($data['faqs'])) {
            Database::query('DELETE FROM service_faqs WHERE service_id = ?', [$serviceId]);
            foreach ((array) $data['faqs'] as $i => $faq) {
                if (empty($faq['question'])) continue;
                Database::insert(
                    'INSERT INTO service_faqs (service_id, question, answer, sort_order) VALUES (?, ?, ?, ?)',
                    [$serviceId, $faq['question'], $faq['answer'] ?? '', $i]
                );
            }
        }

        // Process steps
        if (isset($data['process_steps'])) {
            Database::query('DELETE FROM service_process_steps WHERE service_id = ?', [$serviceId]);
            foreach ((array) $data['process_steps'] as $i => $step) {
                if (empty($step['title'])) continue;
                Database::insert(
                    'INSERT INTO service_process_steps (service_id, step_number, title, description) VALUES (?, ?, ?, ?)',
                    [$serviceId, $i + 1, $step['title'], $step['description'] ?? '']
                );
            }
        }

        // Features
        if (isset($data['features'])) {
            Database::query('DELETE FROM service_features WHERE service_id = ?', [$serviceId]);
            foreach ((array) $data['features'] as $i => $feature) {
                if (empty($feature['title'])) continue;
                Database::insert(
                    'INSERT INTO service_features (service_id, title, description, sort_order) VALUES (?, ?, ?, ?)',
                    [$serviceId, $feature['title'], $feature['description'] ?? '', $i]
                );
            }
        }

        // Benefits
        if (isset($data['benefits'])) {
            Database::query('DELETE FROM service_benefits WHERE service_id = ?', [$serviceId]);
            foreach ((array) $data['benefits'] as $i => $benefit) {
                if (empty($benefit['title'])) continue;
                Database::insert(
                    'INSERT INTO service_benefits (service_id, title, description, sort_order) VALUES (?, ?, ?, ?)',
                    [$serviceId, $benefit['title'], $benefit['description'] ?? '', $i]
                );
            }
        }
    }
}
