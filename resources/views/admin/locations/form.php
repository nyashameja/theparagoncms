<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<?php $isEdit = !empty($location['id']); ?>
<div class="page-header"><div class="page-header-left"><h1><?= $isEdit ? 'Edit Location' : 'New Location' ?></h1><p><a href="/admin/locations">← Locations</a></p></div></div>
<form method="POST" action="<?= $isEdit ? '/admin/locations/' . $location['id'] : '/admin/locations' ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
    <div class="content-grid">
        <div class="card"><div class="card-body">
            <?php if (!empty($errors)): ?><div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div><?php endif; ?>
            <div class="form-group"><label class="form-label">Name <span class="required">*</span></label><input type="text" id="slugSource" name="name" class="form-control" value="<?= e($location['name'] ?? '') ?>" required></div>
            <div class="form-group"><label class="form-label">Slug</label><input type="text" id="slug" name="slug" class="form-control" value="<?= e($location['slug'] ?? '') ?>"></div>
            <div class="form-group"><label class="form-label">Type</label>
                <select name="type" class="form-control"><option value="primary" <?= ($location['type'] ?? '') === 'primary' ? 'selected' : '' ?>>Primary Office</option><option value="service_area" <?= ($location['type'] ?? '') === 'service_area' ? 'selected' : '' ?>>Service Area</option></select>
                <div class="form-hint">Service area pages do not claim a physical office — they describe areas where services are provided remotely.</div></div>
            <div class="form-group"><label class="form-label">Province / Region</label><input type="text" name="province" class="form-control" value="<?= e($location['province'] ?? '') ?>"></div>
            <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5"><?= e($location['description'] ?? '') ?></textarea></div>
            <div class="form-group"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?= e($location['meta_title'] ?? '') ?>" data-maxlength="60"></div>
            <div class="form-group"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="2" data-maxlength="160"><?= e($location['meta_description'] ?? '') ?></textarea></div>
        </div></div>
        <div><div class="card mb-4"><div class="card-header"><span class="card-title">Publish</span></div><div class="card-body">
            <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control"><?php foreach (['draft','published','archived'] as $s): ?><option value="<?= $s ?>" <?= ($location['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($location['sort_order'] ?? 0) ?>"></div>
            <button type="submit" class="btn btn-primary" style="width:100%">Save Location</button>
        </div></div></div>
    </div>
</form>
<?php \App\Support\View::endSection() ?>
