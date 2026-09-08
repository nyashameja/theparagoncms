<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<div class="stats-grid">
    <div class="stat-card stat-accent">
        <div class="stat-value"><?= $leadCounts['new'] ?? 0 ?></div>
        <div class="stat-label">New Leads</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= ($leadCounts['qualified'] ?? 0) + ($leadCounts['proposal_sent'] ?? 0) ?></div>
        <div class="stat-label">Active Pipeline</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $auditCount ?? 0 ?></div>
        <div class="stat-label">Pending Audits</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $monthlyLeads ?? 0 ?></div>
        <div class="stat-label">Leads This Month</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $articleCount ?? 0 ?></div>
        <div class="stat-label">Published Articles</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $projectCount ?? 0 ?></div>
        <div class="stat-label">Live Projects</div>
    </div>
</div>

<?php if (!empty($followUpsDue)): ?>
<div class="card mb-4">
    <div class="card-header">
        <span class="card-title">⚠ Follow-ups Due</span>
        <a href="/admin/leads?filter=follow_up" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>Name</th><th>Email</th><th>Follow-up Date</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($followUpsDue as $lead): ?>
            <tr>
                <td class="fw-600"><?= e($lead['name']) ?></td>
                <td><?= e($lead['email']) ?></td>
                <td class="text-danger"><?= format_date($lead['follow_up_date'], 'd M Y') ?></td>
                <td><span class="badge badge-yellow"><?= e(ucfirst($lead['status'])) ?></span></td>
                <td><a href="/admin/leads/<?= $lead['id'] ?>" class="btn btn-sm btn-secondary">View</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px" class="dashboard-cols">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Recent Leads</span>
            <a href="/admin/leads" class="btn btn-sm btn-ghost">View All →</a>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Name</th><th>Source</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                <?php if (empty($recentLeads)): ?>
                    <tr><td colspan="4" class="text-center text-muted" style="padding:24px">No leads yet</td></tr>
                <?php else: ?>
                <?php foreach ($recentLeads as $lead): ?>
                <tr>
                    <td><a href="/admin/leads/<?= $lead['id'] ?>" class="fw-600"><?= e($lead['name']) ?></a><br><span class="text-sm text-muted"><?= e($lead['email']) ?></span></td>
                    <td class="text-sm"><?= e($lead['form_type'] ?? 'contact') ?></td>
                    <td><span class="badge <?= $lead['status'] === 'new' ? 'badge-red' : ($lead['status'] === 'won' ? 'badge-green' : 'badge-yellow') ?>"><?= e(ucfirst(str_replace('_', ' ', $lead['status']))) ?></span></td>
                    <td class="text-sm text-muted"><?= format_date($lead['created_at'], 'd M') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Recent Articles</span>
            <a href="/admin/articles" class="btn btn-sm btn-ghost">View All →</a>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Title</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                <?php if (empty($recentArticles)): ?>
                    <tr><td colspan="3" class="text-center text-muted" style="padding:24px">No articles yet</td></tr>
                <?php else: ?>
                <?php foreach ($recentArticles as $art): ?>
                <tr>
                    <td><a href="/admin/articles/<?= $art['id'] ?>/edit" class="fw-600 truncate" style="max-width:200px;display:block"><?= e($art['title']) ?></a></td>
                    <td><span class="badge <?= $art['status'] === 'published' ? 'badge-green' : 'badge-grey' ?>"><?= e(ucfirst($art['status'])) ?></span></td>
                    <td class="text-sm text-muted"><?= format_date($art['created_at'], 'd M') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($topPages)): ?>
<div class="card">
    <div class="card-header"><span class="card-title">Top Pages (Last 30 Days)</span></div>
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>Page</th><th>Views</th></tr></thead>
            <tbody>
            <?php foreach ($topPages as $page): ?>
            <tr><td><?= e($page['path']) ?></td><td><?= number_format($page['views']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<style>@media(max-width:700px){.dashboard-cols{grid-template-columns:1fr!important}}</style>
<?php \App\Support\View::endSection() ?>
