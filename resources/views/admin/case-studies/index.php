<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header"><div class="page-header-left"><h1>Case Studies</h1></div><a href="/admin/case-studies/create" class="btn btn-primary">+ New Case Study</a></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Title</th><th>Client</th><th>Status</th><th>Featured</th><th></th></tr></thead>
    <tbody>
    <?php if (empty($caseStudies)): ?>
        <tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon">◎</div><p>No case studies yet</p></div></td></tr>
    <?php else: ?>
    <?php foreach ($caseStudies as $cs): ?>
    <tr>
        <td><a href="/admin/case-studies/<?= $cs['id'] ?>/edit" class="fw-600"><?= e($cs['title']) ?></a><br><span class="text-sm text-muted">/case-studies/<?= e($cs['slug']) ?></span></td>
        <td class="text-sm"><?= e($cs['client_name'] ?? '—') ?></td>
        <td><span class="badge <?= $cs['status'] === 'published' ? 'badge-green' : 'badge-grey' ?>"><?= ucfirst($cs['status']) ?></span></td>
        <td><?= $cs['is_featured'] ? '★' : '' ?></td>
        <td class="table-actions">
            <a href="/admin/case-studies/<?= $cs['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
            <form method="POST" action="/admin/case-studies/<?= $cs['id'] ?>/delete" style="display:inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete?">✕</button></form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table></div></div>
<?php \App\Support\View::endSection() ?>
