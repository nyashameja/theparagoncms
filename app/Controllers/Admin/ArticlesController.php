<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\Validator;
use App\Support\Database;
use App\Support\Logger;
use App\Models\Article;

class ArticlesController
{
    public function index(Request $request): string
    {
        $page   = max(1, (int) $request->get('page', 1));
        $status = $request->get('status', '');
        $where  = $status ? "status = '$status'" : '';
        $result = Article::paginate($page, 20, $where, [], 'created_at DESC');
        return view('admin.articles.index', [
            'title'    => 'Articles',
            'articles' => $result['data'],
            'paginate' => $result,
            'status'   => $status,
        ]);
    }

    public function create(Request $request): string
    {
        return view('admin.articles.form', [
            'title'      => 'New Article',
            'article'    => null,
            'categories' => Article::getCategories(),
        ]);
    }

    public function store(Request $request): void
    {
        $v = Validator::make($request->body, [
            'title'  => 'required|max:300',
            'slug'   => 'required|max:300|unique:articles,slug',
            'status' => 'required|in:draft,review,scheduled,published,archived',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', $request->body);
            redirect('/admin/articles/create');
        }

        $id = $this->saveArticle(0, $request);
        Logger::audit('article_created', ['id' => $id]);
        Session::flash('success', 'Article created.');
        redirect('/admin/articles/' . $id . '/edit');
    }

    public function edit(Request $request, int $id): string
    {
        $article = Article::find($id);
        if (!$article) abort(404);
        $article['tags'] = Article::getTags($article['id']);
        return view('admin.articles.form', [
            'title'      => 'Edit Article',
            'article'    => $article,
            'categories' => Article::getCategories(),
        ]);
    }

    public function update(Request $request, int $id): void
    {
        $v = Validator::make($request->body, [
            'title'  => 'required|max:300',
            'slug'   => 'required|max:300|unique:articles,slug,' . $id,
            'status' => 'required|in:draft,review,scheduled,published,archived',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            redirect('/admin/articles/' . $id . '/edit');
        }

        $this->saveArticle($id, $request);

        // Revision history
        $current = Article::find($id);
        Database::insert(
            'INSERT INTO article_revisions (article_id, user_id, content_snapshot, created_at) VALUES (?, ?, ?, NOW())',
            [$id, Session::get('user_id'), json_encode($current)]
        );

        Logger::audit('article_updated', ['id' => $id]);
        Session::flash('success', 'Article saved.');
        redirect('/admin/articles/' . $id . '/edit');
    }

    public function delete(Request $request, int $id): void
    {
        Article::delete($id);
        Logger::audit('article_deleted', ['id' => $id]);
        Session::flash('success', 'Article deleted.');
        redirect('/admin/articles');
    }

    private function saveArticle(int $id, Request $request): int|string
    {
        $data = $request->body;
        $fields = [
            'title', 'slug', 'excerpt', 'content', 'status',
            'author_id', 'featured_image', 'og_image',
            'meta_title', 'meta_description', 'canonical_url',
            'is_featured', 'published_at', 'allow_comments',
            'schema_type',
        ];

        $save = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) $save[$f] = $data[$f] ?: null;
        }
        $save['is_featured'] = !empty($data['is_featured']) ? 1 : 0;

        if (!$save['author_id']) {
            $save['author_id'] = Session::get('user_id');
        }

        if ($id) {
            Article::update($id, $save);
            $newId = $id;
        } else {
            $newId = (int) Article::create($save);
        }

        // Categories
        Database::query('DELETE FROM article_category_map WHERE article_id = ?', [$newId]);
        foreach ((array) ($data['category_ids'] ?? []) as $cid) {
            if ($cid) Database::insert('INSERT IGNORE INTO article_category_map (article_id, category_id) VALUES (?, ?)', [$newId, $cid]);
        }

        // Tags
        Database::query('DELETE FROM article_tag_map WHERE article_id = ?', [$newId]);
        $rawTags = $data['tags'] ?? '';
        if ($rawTags) {
            foreach (explode(',', $rawTags) as $tagName) {
                $tagName = trim($tagName);
                if (!$tagName) continue;
                $tag = Database::selectOne('SELECT id FROM article_tags WHERE name = ? LIMIT 1', [$tagName]);
                if (!$tag) {
                    $tagId = Database::insert('INSERT INTO article_tags (name, slug, created_at) VALUES (?, ?, NOW())', [$tagName, str_slug($tagName)]);
                } else {
                    $tagId = $tag['id'];
                }
                Database::insert('INSERT IGNORE INTO article_tag_map (article_id, tag_id) VALUES (?, ?)', [$newId, $tagId]);
            }
        }

        return $newId;
    }
}
