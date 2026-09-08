<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header"><div class="page-header-left"><h1>Audit Requests</h1></div></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Contact</th><th>Website</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
    <tbody>
    <?php if (empty($audits)): ?><tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon">◑</div><p>No audit requests yet</p></div></td></tr>
    <?php else: ?>
    <?php foreach ($audits as $a): ?>
    <tr>
        <td><span class="fw-600"><?= e($a['name']) ?></span><br><span class="text-sm text-muted"><?= e($a['email']) ?></span></td>
        <td class="text-sm"><a href="<?= e($a['website_url']) ?>" target="_blank" rel="noopener"><?= e($a['website_url']) ?></a></td>
        <td><span class="badge <?= $a['status'] === 'completed' ? 'badge-green' : ($a['status'] === 'in_progress' ? 'badge-blue' : 'badge-yellow') ?>"><?= ucfirst(str_replace('_',' ',$a['status'])) ?></span></td>
        <td class="text-sm text-muted"><?= format_date($a['created_at'], 'd M Y') ?></td>
        <td class="table-actions">
            <a href="/admin/audits/<?= $a['id'] ?>" class="btn btn-sm btn-secondary">View</a>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table></div></div>
<?php \App\Support\View::endSection() ?>
