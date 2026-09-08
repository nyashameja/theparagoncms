<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<div class="page-header">
    <div class="page-header-left"><h1>Projects</h1><p>Portfolio projects</p></div>
    <a href="/admin/projects/create" class="btn btn-primary">+ Add Project</a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="width:100%">
            <input type="text" name="search" class="form-control" placeholder="Search projects…" value="<?= e($_GET['search'] ?? '') ?>">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach (['draft','published','archived'] as $s): ?>
                <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>Project</th><th>Client</th><th>Status</th><th>Featured</th><th>Date</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($projects)): ?>
                <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon">◉</div><p>No projects yet</p></div></td></tr>
            <?php else: ?>
            <?php foreach ($projects as $p): ?>
            <tr>
                <td>
                    <a href="/admin/projects/<?= $p['id'] ?>/edit" class="fw-600"><?= e($p['title']) ?></a>
                    <br><span class="text-sm text-muted">/portfolio/<?= e($p['slug']) ?></span>
                </td>
                <td class="text-sm"><?= e($p['client_name'] ?? '—') ?></td>
                <td><span class="badge <?= $p['status'] === 'published' ? 'badge-green' : 'badge-grey' ?>"><?= ucfirst($p['status']) ?></span></td>
                <td><?= $p['is_featured'] ? '★' : '' ?></td>
                <td class="text-sm text-muted"><?= $p['completion_date'] ? format_date($p['completion_date'], 'M Y') : '—' ?></td>
                <td class="table-actions">
                    <a href="/portfolio/<?= e($p['slug']) ?>" target="_blank" class="btn btn-sm btn-ghost">↗</a>
                    <a href="/admin/projects/<?= $p['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                    <form method="POST" action="/admin/projects/<?= $p['id'] ?>/delete" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete this project?">✕</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($pagination)): ?>
    <div class="card-footer">
        <nav class="pagination">
            <?php for ($p = max(1, $pagination['current_page'] - 3); $p <= min($pagination['last_page'], $pagination['current_page'] + 3); $p++): ?>
            <div class="page-item <?= $p === $pagination['current_page'] ? 'active' : '' ?>">
                <a href="?<?= http_build_query(array_merge($_GET ?? [], ['page' => $p])) ?>" class="page-link"><?= $p ?></a>
            </div>
            <?php endfor; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>
<?php \App\Support\View::endSection() ?>
