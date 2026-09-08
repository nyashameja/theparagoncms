<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<?php $isEdit = !empty($caseStudy['id']); ?>
<div class="page-header">
    <div class="page-header-left"><h1><?= $isEdit ? 'Edit Case Study' : 'New Case Study' ?></h1><p><a href="/admin/case-studies">← Case Studies</a></p></div>
</div>
<form method="POST" action="<?= $isEdit ? '/admin/case-studies/' . $caseStudy['id'] : '/admin/case-studies' ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
    <div class="content-grid">
        <div>
            <div class="card mb-4"><div class="card-body">
                <?php if (!empty($errors)): ?><div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div><?php endif; ?>
                <div class="form-group"><label class="form-label">Title <span class="required">*</span></label><input type="text" id="slugSource" name="title" class="form-control" value="<?= e($caseStudy['title'] ?? '') ?>" required></div>
                <div class="form-group"><label class="form-label">Slug</label><input type="text" id="slug" name="slug" class="form-control" value="<?= e($caseStudy['slug'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">Client Name</label><input type="text" name="client_name" class="form-control" value="<?= e($caseStudy['client_name'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">Industry</label>
                    <select name="industry_id" class="form-control"><option value="">— Select —</option>
                    <?php foreach ($industries ?? [] as $ind): ?><option value="<?= $ind['id'] ?>" <?= ($caseStudy['industry_id'] ?? '') == $ind['id'] ? 'selected' : '' ?>><?= e($ind['name']) ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label class="form-label">Summary</label><textarea name="summary" class="form-control" rows="4"><?= e($caseStudy['summary'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">Challenge</label><textarea name="challenge" class="form-control" rows="4"><?= e($caseStudy['challenge'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">Solution</label><textarea name="solution" class="form-control" rows="4"><?= e($caseStudy['solution'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">Outcome</label><textarea name="outcome" class="form-control" rows="4"><?= e($caseStudy['outcome'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">Featured Image</label>
                    <div class="d-flex gap-2 align-center">
                        <input type="text" name="featured_image" id="featuredImage" class="form-control" value="<?= e($caseStudy['featured_image'] ?? '') ?>">
                        <button type="button" class="btn btn-secondary btn-sm" data-media-picker data-target="featuredImage" data-preview="featuredPreview">Browse</button>
                    </div>
                    <img id="featuredPreview" src="<?= e($caseStudy['featured_image'] ?? '') ?>" class="image-preview mt-2" <?= empty($caseStudy['featured_image']) ? 'style="display:none"' : '' ?>>
                </div>
                <div class="form-group"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?= e($caseStudy['meta_title'] ?? '') ?>" data-maxlength="60"></div>
                <div class="form-group"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="2" data-maxlength="160"><?= e($caseStudy['meta_description'] ?? '') ?></textarea></div>
            </div></div>
        </div>
        <div>
            <div class="card mb-4"><div class="card-header"><span class="card-title">Publish</span></div><div class="card-body">
                <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control"><?php foreach (['draft','published','archived'] as $s): ?><option value="<?= $s ?>" <?= ($caseStudy['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
                <div class="form-check mb-3"><input type="checkbox" name="is_featured" class="form-check-input" value="1" <?= !empty($caseStudy['is_featured']) ? 'checked' : '' ?>><label class="form-check-label">Featured</label></div>
                <div class="form-group"><label class="form-label">Published At</label><input type="date" name="published_at" class="form-control" value="<?= e(isset($caseStudy['published_at']) ? date('Y-m-d', strtotime($caseStudy['published_at'])) : '') ?>"></div>
                <button type="submit" class="btn btn-primary" style="width:100%">Save Case Study</button>
            </div></div>
        </div>
    </div>
</form>
<?php \App\Support\View::endSection() ?>
