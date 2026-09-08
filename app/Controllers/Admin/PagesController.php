<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\Validator;
use App\Support\Database;
use App\Support\Logger;
use App\Models\Page;

class PagesController
{
    public function index(Request $request): string
    {
        $pages = Database::select(
            "SELECT * FROM pages WHERE deleted_at IS NULL ORDER BY title ASC"
        );
        return view('admin.pages.index', ['title' => 'Pages', 'pages' => $pages]);
    }

    public function create(Request $request): string
    {
        return view('admin.pages.form', ['title' => 'New Page', 'page' => null]);
    }

    public function store(Request $request): void
    {
        $v = Validator::make($request->body, [
            'title'  => 'required|max:300',
            'slug'   => 'required|max:300|unique:pages,slug',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', $request->body);
            redirect('/admin/pages/create');
        }

        $id = $this->savePage(0, $request->body);
        Logger::audit('page_created', ['id' => $id]);
        Session::flash('success', 'Page created.');
        redirect('/admin/pages/' . $id . '/edit');
    }

    public function edit(Request $request, int $id): string
    {
        $page = Page::find($id);
        if (!$page) abort(404);
        return view('admin.pages.form', ['title' => 'Edit Page', 'page' => $page]);
    }

    public function update(Request $request, int $id): void
    {
        $v = Validator::make($request->body, [
            'title'  => 'required|max:300',
            'slug'   => 'required|max:300|unique:pages,slug,' . $id,
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', $request->body);
            redirect('/admin/pages/' . $id . '/edit');
        }

        $this->savePage($id, $request->body);
        Logger::audit('page_updated', ['id' => $id]);
        Session::flash('success', 'Page saved.');
        redirect('/admin/pages/' . $id . '/edit');
    }

    public function destroy(Request $request, int $id): void
    {
        $page = Page::find($id);
        if (!$page) abort(404);
        Page::delete($id);
        Logger::audit('page_deleted', ['id' => $id]);
        Session::flash('success', 'Page deleted.');
        redirect('/admin/pages');
    }

    private function savePage(int $id, array $data): int|string
    {
        $fields = [
            'title', 'slug', 'status', 'layout',
            'hero_title', 'hero_subtitle', 'hero_image',
            'content', 'sidebar_content',
            'meta_title', 'meta_description', 'og_image',
            'is_in_nav', 'nav_label', 'nav_order',
            'show_hero', 'cta_label', 'cta_url',
        ];

        $save = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $save[$f] = $data[$f] ?: null;
            }
        }
        $save['is_in_nav'] = !empty($data['is_in_nav']) ? 1 : 0;
        $save['show_hero'] = !empty($data['show_hero']) ? 1 : 0;

        if ($id) {
            Page::update($id, $save);
            return $id;
        }

        return Page::create($save);
    }
}
