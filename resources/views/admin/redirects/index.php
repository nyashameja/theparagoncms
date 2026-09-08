<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header"><div class="page-header-left"><h1>Redirects</h1></div><a href="/admin/redirects/create" class="btn btn-primary">+ Add Redirect</a></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Source Path</th><th>Target URL</th><th>Type</th><th>Hits</th><th>Active</th><th></th></tr></thead>
    <tbody>
    <?php if (empty($redirects)): ?><tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon">↗</div><p>No redirects yet</p></div></td></tr>
    <?php else: ?>
    <?php foreach ($redirects as $r): ?>
    <tr>
        <td class="text-sm" style="font-family:monospace"><?= e($r['source_path']) ?></td>
        <td class="text-sm" style="font-family:monospace;max-width:250px" class="truncate"><?= e($r['target_url']) ?></td>
        <td><span class="badge badge-blue"><?= $r['redirect_type'] ?></span></td>
        <td class="text-sm"><?= number_format($r['hits'] ?? 0) ?></td>
        <td><?= $r['is_active'] ? '<span class="badge badge-green">Yes</span>' : '<span class="badge badge-grey">No</span>' ?></td>
        <td class="table-actions">
            <a href="/admin/redirects/<?= $r['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
            <form method="POST" action="/admin/redirects/<?= $r['id'] ?>/delete" style="display:inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete?">✕</button></form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table></div></div>
<?php \App\Support\View::endSection() ?>
