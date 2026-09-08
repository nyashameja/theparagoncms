<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header"><div class="page-header-left"><h1>Users</h1></div><a href="/admin/users/create" class="btn btn-primary">+ Add User</a></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>User</th><th>Roles</th><th>Status</th><th>Last Login</th><th></th></tr></thead>
    <tbody>
    <?php if (empty($users)): ?><tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon">◯</div><p>No users yet</p></div></td></tr>
    <?php else: ?>
    <?php foreach ($users as $u): ?>
    <tr>
        <td><span class="fw-600"><?= e($u['name']) ?></span><br><span class="text-sm text-muted"><?= e($u['email']) ?></span></td>
        <td class="text-sm"><?= e($u['roles'] ?? '') ?></td>
        <td><span class="badge <?= $u['status'] === 'active' ? 'badge-green' : 'badge-grey' ?>"><?= ucfirst($u['status']) ?></span></td>
        <td class="text-sm text-muted"><?= $u['last_login_at'] ? format_date($u['last_login_at'], 'd M Y H:i') : 'Never' ?></td>
        <td class="table-actions">
            <a href="/admin/users/<?= $u['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
            <?php if ($u['id'] !== (\App\Support\Session::get('user_id'))): ?>
            <form method="POST" action="/admin/users/<?= $u['id'] ?>/delete" style="display:inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-ghost text-danger" data-confirm="Delete this user?">✕</button></form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table></div></div>
<?php \App\Support\View::endSection() ?>
