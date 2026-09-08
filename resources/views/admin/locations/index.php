<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header"><div class="page-header-left"><h1>Locations</h1></div><a href="/admin/locations/create" class="btn btn-primary">+ Add Location</a></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Location</th><th>Type</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php if (empty($locations)): ?><tr><td colspan="4"><div class="empty-state"><div class="empty-state-icon">◌</div><p>No locations yet</p></div></td></tr>
    <?php else: ?>
    <?php foreach ($locations as $loc): ?>
    <tr>
        <td><a href="/admin/locations/<?= $loc['id'] ?>/edit" class="fw-600"><?= e($loc['name']) ?></a><br><span class="text-sm text-muted">/locations/<?= e($loc['slug']) ?></span></td>
        <td class="text-sm"><?= e(ucfirst(str_replace('_',' ',$loc['type']))) ?></td>
        <td><span class="badge <?= $loc['status'] === 'published' ? 'badge-green' : 'badge-grey' ?>"><?= ucfirst($loc['status']) ?></span></td>
        <td class="table-actions">
            <a href="/admin/locations/<?= $loc['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
            <form method="POST" action="/admin/locations/<?= $loc['id'] ?>/delete" style="display:inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete?">✕</button></form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table></div></div>
<?php \App\Support\View::endSection() ?>
