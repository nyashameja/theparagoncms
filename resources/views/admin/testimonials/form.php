<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<?php $isEdit = !empty($testimonial['id']); ?>
<div class="page-header">
    <div class="page-header-left"><h1><?= $isEdit ? 'Edit Testimonial' : 'New Testimonial' ?></h1><p><a href="/admin/testimonials">← Testimonials</a></p></div>
</div>

<form method="POST" action="<?= $isEdit ? '/admin/testimonials/' . $testimonial['id'] : '/admin/testimonials' ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
    <div class="content-grid">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($errors)): ?><div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div><?php endif; ?>
                <div class="form-group"><label class="form-label">Author Name <span class="required">*</span></label>
                    <input type="text" name="author_name" class="form-control" value="<?= e($testimonial['author_name'] ?? '') ?>" required></div>
                <div class="form-group"><label class="form-label">Author Title</label>
                    <input type="text" name="author_title" class="form-control" value="<?= e($testimonial['author_title'] ?? '') ?>" placeholder="e.g. CEO"></div>
                <div class="form-group"><label class="form-label">Company</label>
                    <input type="text" name="author_company" class="form-control" value="<?= e($testimonial['author_company'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">Content <span class="required">*</span></label>
                    <textarea name="content" class="form-control" rows="5" required><?= e($testimonial['content'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">Rating (1–5)</label>
                    <select name="rating" class="form-control" style="width:auto"><option value="">—</option><?php for ($i=1;$i<=5;$i++): ?><option value="<?= $i ?>" <?= ($testimonial['rating'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?> ★</option><?php endfor; ?></select></div>
                <div class="form-group"><label class="form-label">Source</label>
                    <input type="text" name="source" class="form-control" value="<?= e($testimonial['source'] ?? '') ?>" placeholder="Google, LinkedIn, direct…"></div>
                <div class="form-group"><label class="form-label">Source URL</label>
                    <input type="url" name="source_url" class="form-control" value="<?= e($testimonial['source_url'] ?? '') ?>"></div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="permission_granted" class="form-check-input" value="1" <?= !empty($testimonial['permission_granted']) ? 'checked' : '' ?>>
                    <label class="form-check-label">Permission granted to publish</label>
                </div>
            </div>
        </div>
        <div>
            <div class="card mb-4"><div class="card-header"><span class="card-title">Publish</span></div><div class="card-body">
                <div class="form-group"><label class="form-label">Status</label>
                    <select name="status" class="form-control"><?php foreach (['pending','approved','rejected'] as $s): ?><option value="<?= $s ?>" <?= ($testimonial['status'] ?? 'pending') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
                <div class="form-check mb-3"><input type="checkbox" name="is_featured" class="form-check-input" value="1" <?= !empty($testimonial['is_featured']) ? 'checked' : '' ?>><label class="form-check-label">Featured</label></div>
                <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($testimonial['sort_order'] ?? 0) ?>"></div>
                <button type="submit" class="btn btn-primary" style="width:100%">Save</button>
            </div></div>
        </div>
    </div>
</form>
<?php \App\Support\View::endSection() ?>
