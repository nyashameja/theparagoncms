<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Lead Inbox</h1>
        <p><?= number_format($total ?? 0) ?> total leads</p>
    </div>
    <div>
        <a href="/admin/leads/export?<?= http_build_query(array_filter($_GET ?? [])) ?>" class="btn btn-secondary">Export CSV</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="width:100%">
            <input type="text" id="tableSearch" name="search" class="form-control" placeholder="Search name, email, company…" value="<?= e($_GET['search'] ?? '') ?>">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach (['new','contacted','qualified','proposal_sent','won','lost','spam','archived'] as $s): ?>
                <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="form_type" class="form-control" onchange="this.form.submit()">
                <option value="">All Forms</option>
                <?php foreach (['contact','quote','consultation','audit','newsletter'] as $f): ?>
                <option value="<?= $f ?>" <?= ($_GET['form_type'] ?? '') === $f ? 'selected' : '' ?>><?= ucfirst($f) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <?php if (!empty($_GET['search']) || !empty($_GET['status']) || !empty($_GET['form_type'])): ?>
            <a href="/admin/leads" class="btn btn-ghost">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Name / Contact</th>
                    <th>Form</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($leads)): ?>
                <tr><td colspan="7"><div class="empty-state"><div class="empty-state-icon">◐</div><p>No leads found</p></div></td></tr>
            <?php else: ?>
            <?php foreach ($leads as $lead): ?>
            <tr>
                <td>
                    <a href="/admin/leads/<?= $lead['id'] ?>" class="fw-600"><?= e($lead['name']) ?></a>
                    <?php if ($lead['is_duplicate']): ?><span class="badge badge-yellow" title="Possible duplicate">dup</span><?php endif; ?>
                    <br><span class="text-sm text-muted"><?= e($lead['email']) ?></span>
                    <?php if ($lead['company']): ?><br><span class="text-sm text-muted"><?= e($lead['company']) ?></span><?php endif; ?>
                </td>
                <td class="text-sm"><?= e(ucfirst($lead['form_type'])) ?></td>
                <td>
                    <?php $sc = ['new'=>'badge-red','contacted'=>'badge-blue','qualified'=>'badge-purple','proposal_sent'=>'badge-yellow','won'=>'badge-green','lost'=>'badge-grey','spam'=>'badge-grey','archived'=>'badge-grey'];
                    $sn = ucfirst(str_replace('_',' ',$lead['status'])); ?>
                    <span class="badge <?= $sc[$lead['status']] ?? 'badge-grey' ?>"><?= $sn ?></span>
                </td>
                <td>
                    <?php $pc = ['low'=>'badge-grey','normal'=>'badge-blue','high'=>'badge-yellow','urgent'=>'badge-red']; ?>
                    <span class="badge <?= $pc[$lead['priority']] ?? 'badge-grey' ?>"><?= ucfirst($lead['priority']) ?></span>
                </td>
                <td class="text-sm"><?= e($lead['service_type'] ?? '—') ?></td>
                <td class="text-sm text-muted"><?= format_date($lead['created_at'], 'd M Y') ?></td>
                <td class="table-actions">
                    <a href="/admin/leads/<?= $lead['id'] ?>" class="btn btn-sm btn-secondary">View</a>
                    <form method="POST" action="/admin/leads/<?= $lead['id'] ?>/delete" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Archive this lead?">✕</button>
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
            <?php if ($pagination['current_page'] > 1): ?>
            <div class="page-item"><a href="?<?= http_build_query(array_merge($_GET ?? [], ['page' => $pagination['current_page'] - 1])) ?>" class="page-link">‹</a></div>
            <?php endif; ?>
            <?php for ($p = max(1, $pagination['current_page'] - 3); $p <= min($pagination['last_page'], $pagination['current_page'] + 3); $p++): ?>
            <div class="page-item <?= $p === $pagination['current_page'] ? 'active' : '' ?>">
                <a href="?<?= http_build_query(array_merge($_GET ?? [], ['page' => $p])) ?>" class="page-link"><?= $p ?></a>
            </div>
            <?php endfor; ?>
            <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
            <div class="page-item"><a href="?<?= http_build_query(array_merge($_GET ?? [], ['page' => $pagination['current_page'] + 1])) ?>" class="page-link">›</a></div>
            <?php endif; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>
<?php \App\Support\View::endSection() ?>
