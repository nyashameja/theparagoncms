<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<div class="page-header">
    <div class="page-header-left"><h1>Testimonials</h1></div>
    <a href="/admin/testimonials/create" class="btn btn-primary">+ Add Testimonial</a>
</div>
<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>Author</th><th>Company</th><th>Rating</th><th>Status</th><th>Featured</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($testimonials)): ?>
                <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon">★</div><p>No testimonials yet</p></div></td></tr>
            <?php else: ?>
            <?php foreach ($testimonials as $t): ?>
            <tr>
                <td class="fw-600"><?= e($t['author_name']) ?><br><span class="text-sm text-muted"><?= e($t['author_title'] ?? '') ?></span></td>
                <td class="text-sm"><?= e($t['author_company'] ?? '—') ?></td>
                <td class="text-sm"><?= $t['rating'] ? str_repeat('★', $t['rating']) : '—' ?></td>
                <td><span class="badge <?= $t['status'] === 'approved' ? 'badge-green' : ($t['status'] === 'pending' ? 'badge-yellow' : 'badge-red') ?>"><?= ucfirst($t['status']) ?></span></td>
                <td><?= $t['is_featured'] ? '★' : '' ?></td>
                <td class="table-actions">
                    <a href="/admin/testimonials/<?= $t['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                    <form method="POST" action="/admin/testimonials/<?= $t['id'] ?>/delete" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete?">✕</button>
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
