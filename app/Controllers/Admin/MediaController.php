<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\Database;
use App\Support\Logger;
use App\Models\Media;

class MediaController
{
    public function index(Request $request): string
    {
        $result = Media::search(
            $request->get('q', ''),
            $request->get('type', ''),
            max(1, (int) $request->get('page', 1))
        );

        if ($request->wantsJson() || $request->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        return view('admin.media.index', [
            'title'  => 'Media Library',
            'media'  => $result['data'],
            'paginate' => $result,
        ]);
    }

    public function upload(Request $request): void
    {
        header('Content-Type: application/json');

        $file = $request->file('file');
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'Upload failed or no file provided.']);
            exit;
        }

        try {
            $result = Media::upload($file, Session::get('user_id'), $request->get('folder', ''));
            Logger::audit('media_uploaded', ['id' => $result['id'], 'path' => $result['path']]);
            echo json_encode(['success' => true, 'media' => $result]);
        } catch (\RuntimeException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        }

        exit;
    }

    public function update(Request $request, array $params): void
    {
        $id = (int) $params['id'];
        $media = Database::selectOne('SELECT * FROM media WHERE id = ?', [$id]);
        if (!$media) abort(404);

        Database::update(
            'UPDATE media SET alt_text = ?, title = ?, caption = ?, credit = ?, updated_at = NOW() WHERE id = ?',
            [$request->get('alt_text'), $request->get('title'), $request->get('caption'), $request->get('credit'), $id]
        );

        if ($request->wantsJson()) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
            exit;
        }

        Session::flash('success', 'Media updated.');
        redirect('/admin/media');
    }

    public function delete(Request $request, array $params): void
    {
        $id    = (int) $params['id'];
        $media = Database::selectOne('SELECT * FROM media WHERE id = ?', [$id]);
        if (!$media) abort(404);

        // Check for usage
        $used = Database::selectOne('SELECT COUNT(*) as c FROM media_usage WHERE media_id = ?', [$id])['c'] ?? 0;
        if ($used > 0 && !$request->get('force')) {
            Session::flash('error', 'This file is in use. Delete references first or confirm forced deletion.');
            redirect('/admin/media');
        }

        // Soft delete
        Database::update('UPDATE media SET deleted_at = NOW() WHERE id = ?', [$id]);
        Logger::audit('media_deleted', ['id' => $id]);

        if ($request->wantsJson()) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
            exit;
        }

        Session::flash('success', 'File deleted.');
        redirect('/admin/media');
    }
}
