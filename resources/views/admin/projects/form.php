<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<?php $isEdit = !empty($project['id']); ?>
<div class="page-header">
    <div class="page-header-left">
        <h1><?= $isEdit ? 'Edit Project' : 'New Project' ?></h1>
        <p><a href="/admin/projects">← Projects</a></p>
    </div>
</div>

<form method="POST" action="<?= $isEdit ? '/admin/projects/' . $project['id'] : '/admin/projects' ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="content-grid">
        <div>
            <?php if (!empty($errors)): ?>
            <div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Title <span class="required">*</span></label>
                        <input type="text" id="slugSource" name="title" class="form-control" value="<?= e($project['title'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug <span class="required">*</span></label>
                        <input type="text" id="slug" name="slug" class="form-control" value="<?= e($project['slug'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Client Name</label>
                        <input type="text" name="client_name" class="form-control" value="<?= e($project['client_name'] ?? '') ?>">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                        <div class="form-group">
                            <label class="form-label">Industry</label>
                            <select name="industry_id" class="form-control">
                                <option value="">— Select —</option>
                                <?php foreach ($industries ?? [] as $ind): ?>
                                <option value="<?= $ind['id'] ?>" <?= ($project['industry_id'] ?? '') == $ind['id'] ? 'selected' : '' ?>><?= e($ind['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Completion Date</label>
                            <input type="date" name="completion_date" class="form-control" value="<?= e($project['completion_date'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="3"><?= e($project['short_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control" rows="8"><?= e($project['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Featured Image URL</label>
                        <div class="d-flex gap-2 align-center">
                            <input type="text" name="featured_image" id="featuredImage" class="form-control" value="<?= e($project['featured_image'] ?? '') ?>">
                            <button type="button" class="btn btn-secondary btn-sm" data-media-picker data-target="featuredImage" data-preview="featuredPreview">Browse</button>
                        </div>
                        <img id="featuredPreview" src="<?= e($project['featured_image'] ?? '') ?>" class="image-preview mt-2" <?= empty($project['featured_image']) ? 'style="display:none"' : '' ?>>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website URL</label>
                        <input type="url" name="website_url" class="form-control" value="<?= e($project['website_url'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Services</label>
                        <div style="display:flex;flex-wrap:wrap;gap:8px">
                            <?php foreach ($allServices ?? [] as $svc): ?>
                            <label class="form-check" style="white-space:nowrap">
                                <input type="checkbox" name="services[]" value="<?= $svc['id'] ?>" class="form-check-input"
                                    <?= in_array($svc['id'], $projectServices ?? []) ? 'checked' : '' ?>>
                                <span class="form-check-label"><?= e($svc['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Results -->
                    <div class="form-group">
                        <label class="form-label">Results / Outcomes</label>
                        <div id="resultsRepeater" class="repeater mb-2">
                            <?php foreach (($project['results'] ?? []) as $i => $res): ?>
                            <div class="repeater-item" data-repeater-item>
                                <button type="button" class="repeater-remove" data-repeater-remove>✕</button>
                                <div style="display:grid;grid-template-columns:1fr 1fr 2fr;gap:10px">
                                    <div class="form-group"><label class="form-label">Metric</label>
                                        <input type="text" name="results[<?= $i ?>][metric]" class="form-control" value="<?= e($res['metric']) ?>" placeholder="e.g. +120%"></div>
                                    <div class="form-group"><label class="form-label">Label</label>
                                        <input type="text" name="results[<?= $i ?>][label]" class="form-control" value="<?= e($res['label']) ?>" placeholder="e.g. Organic Traffic"></div>
                                    <div class="form-group"><label class="form-label">Description</label>
                                        <input type="text" name="results[<?= $i ?>][description]" class="form-control" value="<?= e($res['description'] ?? '') ?>"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <div data-repeater-template style="display:none" class="repeater-item">
                                <button type="button" class="repeater-remove" data-repeater-remove>✕</button>
                                <div style="display:grid;grid-template-columns:1fr 1fr 2fr;gap:10px">
                                    <div class="form-group"><label class="form-label">Metric</label><input type="text" name="results[__IDX__][metric]" class="form-control" placeholder="e.g. +120%"></div>
                                    <div class="form-group"><label class="form-label">Label</label><input type="text" name="results[__IDX__][label]" class="form-control" placeholder="e.g. Organic Traffic"></div>
                                    <div class="form-group"><label class="form-label">Description</label><input type="text" name="results[__IDX__][description]" class="form-control"></div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="repeater-add" data-repeater-add="resultsRepeater">+ Add Result</button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="<?= e($project['meta_title'] ?? '') ?>" data-maxlength="60">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2" data-maxlength="160"><?= e($project['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card mb-4">
                <div class="card-header"><span class="card-title">Publish</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <?php foreach (['draft','published','archived'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($project['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" id="is_featured" name="is_featured" class="form-check-input" value="1" <?= !empty($project['is_featured']) ? 'checked' : '' ?>>
                        <label for="is_featured" class="form-check-label">Featured project</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" id="is_anonymised" name="is_anonymised" class="form-check-input" value="1" <?= !empty($project['is_anonymised']) ? 'checked' : '' ?>>
                        <label for="is_anonymised" class="form-check-label">Anonymise client name</label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= e($project['sort_order'] ?? 0) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%">Save Project</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php \App\Support\View::endSection() ?>
