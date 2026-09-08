<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<div class="page-header">
    <div class="page-header-left"><h1>Services</h1><p>Manage your service offerings</p></div>
    <a href="/admin/services/create" class="btn btn-primary">+ Add Service</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th style="width:32px"></th><th>Service</th><th>Status</th><th>Featured</th><th>Sort</th><th></th></tr></thead>
            <tbody id="sortableServices" class="drag-list">
            <?php if (empty($services)): ?>
                <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon">◈</div><p>No services yet</p></div></td></tr>
            <?php else: ?>
            <?php foreach ($services as $svc): ?>
            <tr class="drag-item" data-id="<?= $svc['id'] ?>">
                <td class="drag-handle" style="cursor:grab;color:#bbb">⠿</td>
                <td>
                    <a href="/admin/services/<?= $svc['id'] ?>/edit" class="fw-600"><?= e($svc['name']) ?></a>
                    <br><span class="text-sm text-muted">/services/<?= e($svc['slug']) ?></span>
                </td>
                <td><span class="badge <?= $svc['status'] === 'published' ? 'badge-green' : ($svc['status'] === 'draft' ? 'badge-grey' : 'badge-yellow') ?>"><?= ucfirst($svc['status']) ?></span></td>
                <td><?= $svc['is_featured'] ? '★' : '' ?></td>
                <td class="text-sm text-muted"><?= $svc['sort_order'] ?></td>
                <td class="table-actions">
                    <a href="/services/<?= e($svc['slug']) ?>" target="_blank" class="btn btn-sm btn-ghost">↗</a>
                    <a href="/admin/services/<?= $svc['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                    <form method="POST" action="/admin/services/<?= $svc['id'] ?>/delete" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete this service?">✕</button>
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
