<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header"><div class="page-header-left"><h1>Industries</h1></div><a href="/admin/industries/create" class="btn btn-primary">+ Add Industry</a></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Industry</th><th>Status</th><th>Featured</th><th>Sort</th><th></th></tr></thead>
    <tbody>
    <?php if (empty($industries)): ?><tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon">◻</div><p>No industries yet</p></div></td></tr>
    <?php else: ?>
    <?php foreach ($industries as $ind): ?>
    <tr>
        <td><a href="/admin/industries/<?= $ind['id'] ?>/edit" class="fw-600"><?= e($ind['name']) ?></a><br><span class="text-sm text-muted">/industries/<?= e($ind['slug']) ?></span></td>
        <td><span class="badge <?= $ind['status'] === 'published' ? 'badge-green' : 'badge-grey' ?>"><?= ucfirst($ind['status']) ?></span></td>
        <td><?= $ind['is_featured'] ? '★' : '' ?></td>
        <td class="text-sm"><?= $ind['sort_order'] ?></td>
        <td class="table-actions">
            <a href="/admin/industries/<?= $ind['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
            <form method="POST" action="/admin/industries/<?= $ind['id'] ?>/delete" style="display:inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete?">✕</button></form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table></div></div>
<?php \App\Support\View::endSection() ?>
