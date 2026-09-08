<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<?php $isEdit = !empty($industry['id']); ?>
<div class="page-header"><div class="page-header-left"><h1><?= $isEdit ? 'Edit Industry' : 'New Industry' ?></h1><p><a href="/admin/industries">← Industries</a></p></div></div>
<form method="POST" action="<?= $isEdit ? '/admin/industries/' . $industry['id'] : '/admin/industries' ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
    <div class="content-grid">
        <div class="card"><div class="card-body">
            <?php if (!empty($errors)): ?><div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div><?php endif; ?>
            <div class="form-group"><label class="form-label">Name <span class="required">*</span></label><input type="text" id="slugSource" name="name" class="form-control" value="<?= e($industry['name'] ?? '') ?>" required></div>
            <div class="form-group"><label class="form-label">Slug</label><input type="text" id="slug" name="slug" class="form-control" value="<?= e($industry['slug'] ?? '') ?>"></div>
            <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4"><?= e($industry['description'] ?? '') ?></textarea></div>
            <div class="form-group"><label class="form-label">Hero Image</label>
                <div class="d-flex gap-2 align-center"><input type="text" name="hero_image" id="heroImage" class="form-control" value="<?= e($industry['hero_image'] ?? '') ?>"><button type="button" class="btn btn-secondary btn-sm" data-media-picker data-target="heroImage" data-preview="heroPreview">Browse</button></div>
                <img id="heroPreview" src="<?= e($industry['hero_image'] ?? '') ?>" class="image-preview mt-2" <?= empty($industry['hero_image']) ? 'style="display:none"' : '' ?>></div>
            <div class="form-group"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?= e($industry['meta_title'] ?? '') ?>" data-maxlength="60"></div>
            <div class="form-group"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="2" data-maxlength="160"><?= e($industry['meta_description'] ?? '') ?></textarea></div>
        </div></div>
        <div><div class="card mb-4"><div class="card-header"><span class="card-title">Publish</span></div><div class="card-body">
            <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control"><?php foreach (['draft','published','archived'] as $s): ?><option value="<?= $s ?>" <?= ($industry['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
            <div class="form-check mb-3"><input type="checkbox" name="is_featured" class="form-check-input" value="1" <?= !empty($industry['is_featured']) ? 'checked' : '' ?>><label class="form-check-label">Featured</label></div>
            <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($industry['sort_order'] ?? 0) ?>"></div>
            <button type="submit" class="btn btn-primary" style="width:100%">Save Industry</button>
        </div></div></div>
    </div>
</form>
<?php \App\Support\View::endSection() ?>
