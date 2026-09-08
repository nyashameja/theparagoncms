<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header"><div class="page-header-left"><h1>Analytics</h1><p>Site traffic overview (last 30 days)</p></div></div>

<div class="stats-grid mb-4">
    <div class="stat-card"><div class="stat-value"><?= number_format($pageviews ?? 0) ?></div><div class="stat-label">Total Pageviews</div></div>
    <div class="stat-card"><div class="stat-value"><?= number_format($uniquePages ?? 0) ?></div><div class="stat-label">Unique Pages</div></div>
    <div class="stat-card"><div class="stat-value"><?= number_format($ctaClicks ?? 0) ?></div><div class="stat-label">CTA Clicks</div></div>
    <div class="stat-card"><div class="stat-value"><?= number_format($formSubmits ?? 0) ?></div><div class="stat-label">Form Submits</div></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <div class="card">
        <div class="card-header"><span class="card-title">Top Pages</span></div>
        <div class="table-wrapper"><table class="table">
            <thead><tr><th>Page</th><th>Views</th></tr></thead>
            <tbody>
            <?php if (empty($topPages)): ?><tr><td colspan="2" class="text-center text-muted" style="padding:24px">No data yet</td></tr>
            <?php else: ?>
            <?php foreach ($topPages as $page): ?>
            <tr><td class="text-sm" style="font-family:monospace"><?= e($page['path']) ?></td><td class="text-sm"><?= number_format($page['views']) ?></td></tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table></div>
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title">404 Not Found Log</span></div>
        <div class="table-wrapper"><table class="table">
            <thead><tr><th>Path</th><th>Hits</th><th>Last Seen</th></tr></thead>
            <tbody>
            <?php if (empty($notFoundLogs)): ?><tr><td colspan="3" class="text-center text-muted" style="padding:24px">No 404s logged</td></tr>
            <?php else: ?>
            <?php foreach ($notFoundLogs as $nfl): ?>
            <tr><td class="text-sm" style="font-family:monospace"><?= e($nfl['path']) ?></td><td><?= $nfl['hits'] ?></td><td class="text-sm text-muted"><?= format_date($nfl['last_seen_at'], 'd M') ?></td></tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table></div>
    </div>
</div>
<?php \App\Support\View::endSection() ?>
