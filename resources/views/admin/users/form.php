<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<?php $isEdit = !empty($user['id']); ?>
<div class="page-header"><div class="page-header-left"><h1><?= $isEdit ? 'Edit User' : 'New User' ?></h1><p><a href="/admin/users">← Users</a></p></div></div>
<form method="POST" action="<?= $isEdit ? '/admin/users/' . $user['id'] : '/admin/users' ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
    <div class="content-grid">
        <div class="card"><div class="card-body">
            <?php if (!empty($errors)): ?><div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div><?php endif; ?>
            <div class="form-group"><label class="form-label">Name <span class="required">*</span></label><input type="text" name="name" class="form-control" value="<?= e($user['name'] ?? '') ?>" required></div>
            <div class="form-group"><label class="form-label">Email <span class="required">*</span></label><input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" required></div>
            <div class="form-group"><label class="form-label"><?= $isEdit ? 'New Password' : 'Password' ?> <?= !$isEdit ? '<span class="required">*</span>' : '' ?></label>
                <input type="password" name="password" class="form-control" autocomplete="new-password" <?= !$isEdit ? 'required' : '' ?>>
                <div class="form-hint">Minimum 12 characters<?= $isEdit ? '. Leave blank to keep current.' : '' ?></div></div>
            <div class="form-group"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" autocomplete="new-password"></div>
            <div class="form-group"><label class="form-label">Roles</label>
                <?php foreach ($allRoles ?? [] as $role): ?>
                <div class="form-check"><input type="checkbox" name="roles[]" value="<?= $role['id'] ?>" class="form-check-input" <?= in_array($role['id'], $userRoles ?? []) ? 'checked' : '' ?>><label class="form-check-label"><?= e(ucfirst($role['name'])) ?></label></div>
                <?php endforeach; ?>
            </div>
        </div></div>
        <div><div class="card mb-4"><div class="card-header"><span class="card-title">Status</span></div><div class="card-body">
            <div class="form-group"><select name="status" class="form-control"><option value="active" <?= ($user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
            <button type="submit" class="btn btn-primary" style="width:100%">Save User</button>
        </div></div></div>
    </div>
</form>
<?php \App\Support\View::endSection() ?>
