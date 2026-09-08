<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<div class="page-header">
    <div class="page-header-left"><h1>Pages</h1><p>Static & CMS pages</p></div>
    <a href="/admin/pages/create" class="btn btn-primary">+ New Page</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>Title</th><th>Slug</th><th>Status</th><th>In Nav</th><th>Updated</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($pages)): ?>
                <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon">□</div><p>No pages yet</p></div></td></tr>
            <?php else: ?>
            <?php foreach ($pages as $pg): ?>
            <tr>
                <td>
                    <a href="/admin/pages/<?= $pg['id'] ?>/edit" class="fw-600"><?= e($pg['title']) ?></a>
                </td>
                <td class="text-sm text-muted">/page/<?= e($pg['slug']) ?></td>
                <td><span class="badge <?= $pg['status'] === 'published' ? 'badge-green' : ($pg['status'] === 'draft' ? 'badge-grey' : 'badge-yellow') ?>"><?= ucfirst($pg['status']) ?></span></td>
                <td class="text-sm"><?= $pg['is_in_nav'] ? 'Yes' : '—' ?></td>
                <td class="text-sm text-muted"><?= $pg['updated_at'] ? format_date($pg['updated_at'], 'd M Y') : '—' ?></td>
                <td class="table-actions">
                    <?php if ($pg['status'] === 'published'): ?>
                    <a href="/page/<?= e($pg['slug']) ?>" target="_blank" class="btn btn-sm btn-ghost">↗</a>
                    <?php endif; ?>
                    <a href="/admin/pages/<?= $pg['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                    <form method="POST" action="/admin/pages/<?= $pg['id'] ?>/delete" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete this page?">✕</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php \App\Support\View::endSection() ?>
