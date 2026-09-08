<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header"><div class="page-header-left"><h1>Packages</h1></div><a href="/admin/packages/create" class="btn btn-primary">+ Add Package</a></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Package</th><th>Category</th><th>Price</th><th>Status</th><th>Featured</th><th></th></tr></thead>
    <tbody>
    <?php if (empty($packages)): ?>
        <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon">⊟</div><p>No packages yet</p></div></td></tr>
    <?php else: ?>
    <?php foreach ($packages as $pkg): ?>
    <tr>
        <td class="fw-600"><?= e($pkg['name']) ?></td>
        <td class="text-sm"><?= e(ucfirst($pkg['category'])) ?></td>
        <td class="text-sm"><?= e($pkg['starting_price'] ?? '—') ?> <span class="text-muted"><?= e($pkg['billing_type']) ?></span></td>
        <td><span class="badge <?= $pkg['status'] === 'published' ? 'badge-green' : 'badge-grey' ?>"><?= ucfirst($pkg['status']) ?></span></td>
        <td><?= $pkg['is_featured'] ? '★' : '' ?></td>
        <td class="table-actions">
            <a href="/admin/packages/<?= $pkg['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
            <form method="POST" action="/admin/packages/<?= $pkg['id'] ?>/delete" style="display:inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete?">✕</button></form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table></div></div>
<?php \App\Support\View::endSection() ?>
