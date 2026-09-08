<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<div class="page-header">
    <div class="page-header-left"><h1>Articles</h1><p>Blog & content</p></div>
    <a href="/admin/articles/create" class="btn btn-primary">+ New Article</a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="width:100%">
            <input type="text" name="search" class="form-control" placeholder="Search articles…" value="<?= e($_GET['search'] ?? '') ?>">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach (['draft','review','scheduled','published','archived'] as $s): ?>
                <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>Title</th><th>Author</th><th>Status</th><th>Views</th><th>Published</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($articles)): ?>
                <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon">▤</div><p>No articles yet</p></div></td></tr>
            <?php else: ?>
            <?php foreach ($articles as $art): ?>
            <tr>
                <td>
                    <a href="/admin/articles/<?= $art['id'] ?>/edit" class="fw-600"><?= e($art['title']) ?></a>
                    <br><span class="text-sm text-muted">/blog/<?= e($art['slug']) ?></span>
                </td>
                <td class="text-sm"><?= e($art['author_name'] ?? '—') ?></td>
                <td><span class="badge <?= $art['status'] === 'published' ? 'badge-green' : ($art['status'] === 'draft' ? 'badge-grey' : ($art['status'] === 'review' ? 'badge-yellow' : 'badge-blue')) ?>"><?= ucfirst($art['status']) ?></span></td>
                <td class="text-sm"><?= number_format($art['view_count']) ?></td>
                <td class="text-sm text-muted"><?= $art['published_at'] ? format_date($art['published_at'], 'd M Y') : '—' ?></td>
                <td class="table-actions">
                    <?php if ($art['status'] === 'published'): ?>
                    <a href="/blog/<?= e($art['slug']) ?>" target="_blank" class="btn btn-sm btn-ghost">↗</a>
                    <?php endif; ?>
                    <a href="/admin/articles/<?= $art['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                    <form method="POST" action="/admin/articles/<?= $art['id'] ?>/delete" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete this article?">✕</button>
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
