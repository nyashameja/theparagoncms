<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<?php $isEdit = !empty($redirect['id']); ?>
<div class="page-header"><div class="page-header-left"><h1><?= $isEdit ? 'Edit Redirect' : 'New Redirect' ?></h1><p><a href="/admin/redirects">← Redirects</a></p></div></div>
<form method="POST" action="<?= $isEdit ? '/admin/redirects/' . $redirect['id'] : '/admin/redirects' ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
    <div class="content-grid">
        <div class="card"><div class="card-body">
            <?php if (!empty($errors)): ?><div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div><?php endif; ?>
            <div class="form-group"><label class="form-label">Source Path <span class="required">*</span></label>
                <input type="text" name="source_path" class="form-control" value="<?= e($redirect['source_path'] ?? '') ?>" placeholder="/old-page-url" required>
                <div class="form-hint">Must start with /</div></div>
            <div class="form-group"><label class="form-label">Target URL <span class="required">*</span></label>
                <input type="text" name="target_url" class="form-control" value="<?= e($redirect['target_url'] ?? '') ?>" placeholder="/new-page or https://…" required></div>
            <div class="form-group"><label class="form-label">Redirect Type</label>
                <select name="redirect_type" class="form-control" style="width:auto"><option value="301" <?= ($redirect['redirect_type'] ?? '301') === '301' ? 'selected' : '' ?>>301 — Permanent</option><option value="302" <?= ($redirect['redirect_type'] ?? '') === '302' ? 'selected' : '' ?>>302 — Temporary</option></select></div>
            <div class="form-check"><input type="checkbox" name="is_active" class="form-check-input" value="1" <?= ($redirect['is_active'] ?? 1) ? 'checked' : '' ?>><label class="form-check-label">Active</label></div>
        </div></div>
        <div><div class="card"><div class="card-body"><button type="submit" class="btn btn-primary" style="width:100%">Save Redirect</button></div></div></div>
    </div>
</form>
<?php \App\Support\View::endSection() ?>
