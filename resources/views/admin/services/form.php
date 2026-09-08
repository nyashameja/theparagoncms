<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<?php $isEdit = !empty($service['id']); ?>
<div class="page-header">
    <div class="page-header-left">
        <h1><?= $isEdit ? 'Edit Service' : 'New Service' ?></h1>
        <p><a href="/admin/services">← Services</a></p>
    </div>
    <?php if ($isEdit): ?>
    <a href="/services/<?= e($service['slug']) ?>" target="_blank" class="btn btn-secondary">View Live ↗</a>
    <?php endif; ?>
</div>

<form method="POST" action="<?= $isEdit ? '/admin/services/' . $service['id'] : '/admin/services' ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="content-grid">
        <div>
            <!-- Tabs -->
            <div data-tabs>
                <div class="tabs">
                    <button type="button" class="tab-btn active" data-tab="main">Main</button>
                    <button type="button" class="tab-btn" data-tab="content">Content</button>
                    <button type="button" class="tab-btn" data-tab="faqs">FAQs</button>
                    <button type="button" class="tab-btn" data-tab="process">Process</button>
                    <button type="button" class="tab-btn" data-tab="seo">SEO</button>
                </div>

                <div class="tab-panel active" data-panel="main">
                    <div class="card">
                        <div class="card-body">
                            <?php if (!empty($errors)): ?>
                            <div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label class="form-label">Name <span class="required">*</span></label>
                                <input type="text" id="slugSource" name="name" class="form-control" value="<?= e($service['name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Slug <span class="required">*</span></label>
                                <input type="text" id="slug" name="slug" class="form-control" value="<?= e($service['slug'] ?? '') ?>" required>
                                <div class="form-hint">URL: /services/{slug}</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tagline</label>
                                <input type="text" name="tagline" class="form-control" value="<?= e($service['tagline'] ?? '') ?>" placeholder="Short compelling line">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Short Description</label>
                                <textarea name="short_description" class="form-control" rows="3"><?= e($service['short_description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Icon (emoji or CSS class)</label>
                                <input type="text" name="icon" class="form-control" value="<?= e($service['icon'] ?? '') ?>" placeholder="e.g. ◈ or fa-code">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Hero Image</label>
                                <div class="d-flex gap-2 align-center">
                                    <input type="text" name="hero_image" id="heroImage" class="form-control" value="<?= e($service['hero_image'] ?? '') ?>" placeholder="/uploads/…">
                                    <button type="button" class="btn btn-secondary btn-sm" data-media-picker data-target="heroImage" data-preview="heroPreview">Browse</button>
                                </div>
                                <?php if (!empty($service['hero_image'])): ?>
                                <img id="heroPreview" src="<?= e($service['hero_image']) ?>" class="image-preview mt-2">
                                <?php else: ?>
                                <img id="heroPreview" style="display:none" class="image-preview mt-2">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-panel" data-panel="content">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Full Description</label>
                                <div data-editor="description_html">
                                    <div class="editor-toolbar">
                                        <button type="button" class="editor-btn" data-cmd="bold"><b>B</b></button>
                                        <button type="button" class="editor-btn" data-cmd="italic"><i>I</i></button>
                                        <button type="button" class="editor-btn" data-cmd="insertUnorderedList">• List</button>
                                        <button type="button" class="editor-btn" data-cmd="insertOrderedList">1. List</button>
                                        <button type="button" class="editor-btn" data-cmd="formatBlock" data-arg="h2">H2</button>
                                        <button type="button" class="editor-btn" data-cmd="formatBlock" data-arg="h3">H3</button>
                                        <button type="button" class="editor-btn" data-cmd="formatBlock" data-arg="p">¶</button>
                                        <button type="button" class="editor-btn" data-cmd="removeFormat">✕ fmt</button>
                                    </div>
                                    <div class="editor-area" contenteditable="true"></div>
                                </div>
                                <textarea id="description_html" name="description" style="display:none"><?= e($service['description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Features (one per line)</label>
                                <textarea name="features_text" class="form-control" rows="5" placeholder="Feature one&#10;Feature two"><?php
                                    $feats = $service['features'] ?? [];
                                    echo e(implode("\n", array_column($feats, 'title')));
                                ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Benefits (one per line)</label>
                                <textarea name="benefits_text" class="form-control" rows="5" placeholder="Benefit one&#10;Benefit two"><?php
                                    $bens = $service['benefits'] ?? [];
                                    echo e(implode("\n", array_column($bens, 'title')));
                                ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Deliverables (one per line)</label>
                                <textarea name="deliverables_text" class="form-control" rows="4" placeholder="Deliverable one&#10;Deliverable two"><?php
                                    $dels = $service['deliverables'] ?? [];
                                    echo e(implode("\n", array_column($dels, 'title')));
                                ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">CTA Label</label>
                                <input type="text" name="cta_label" class="form-control" value="<?= e($service['cta_label'] ?? 'Get Started') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">CTA URL</label>
                                <input type="text" name="cta_url" class="form-control" value="<?= e($service['cta_url'] ?? '/contact') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-panel" data-panel="faqs">
                    <div class="card">
                        <div class="card-body">
                            <div id="faqRepeater" class="repeater">
                                <?php foreach (($service['faqs'] ?? []) as $i => $faq): ?>
                                <div class="repeater-item" data-repeater-item>
                                    <button type="button" class="repeater-remove" data-repeater-remove>✕</button>
                                    <div class="form-group"><label class="form-label">Question</label>
                                        <input type="text" name="faqs[<?= $i ?>][question]" class="form-control" value="<?= e($faq['question']) ?>">
                                    </div>
                                    <div class="form-group"><label class="form-label">Answer</label>
                                        <textarea name="faqs[<?= $i ?>][answer]" class="form-control" rows="3"><?= e($faq['answer']) ?></textarea>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <div data-repeater-template style="display:none" class="repeater-item">
                                    <button type="button" class="repeater-remove" data-repeater-remove>✕</button>
                                    <div class="form-group"><label class="form-label">Question</label>
                                        <input type="text" name="faqs[__IDX__][question]" class="form-control">
                                    </div>
                                    <div class="form-group"><label class="form-label">Answer</label>
                                        <textarea name="faqs[__IDX__][answer]" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="repeater-add" data-repeater-add="faqRepeater">+ Add FAQ</button>
                        </div>
                    </div>
                </div>

                <div class="tab-panel" data-panel="process">
                    <div class="card">
                        <div class="card-body">
                            <div id="processRepeater" class="repeater">
                                <?php foreach (($service['process_steps'] ?? []) as $i => $step): ?>
                                <div class="repeater-item" data-repeater-item>
                                    <button type="button" class="repeater-remove" data-repeater-remove>✕</button>
                                    <div style="display:grid;grid-template-columns:80px 1fr;gap:12px">
                                        <div class="form-group"><label class="form-label">Step #</label>
                                            <input type="number" name="steps[<?= $i ?>][step_number]" class="form-control" value="<?= e($step['step_number']) ?>">
                                        </div>
                                        <div class="form-group"><label class="form-label">Title</label>
                                            <input type="text" name="steps[<?= $i ?>][title]" class="form-control" value="<?= e($step['title']) ?>">
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="form-label">Description</label>
                                        <textarea name="steps[<?= $i ?>][description]" class="form-control" rows="2"><?= e($step['description'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <div data-repeater-template style="display:none" class="repeater-item">
                                    <button type="button" class="repeater-remove" data-repeater-remove>✕</button>
                                    <div style="display:grid;grid-template-columns:80px 1fr;gap:12px">
                                        <div class="form-group"><label class="form-label">Step #</label>
                                            <input type="number" name="steps[__IDX__][step_number]" class="form-control">
                                        </div>
                                        <div class="form-group"><label class="form-label">Title</label>
                                            <input type="text" name="steps[__IDX__][title]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="form-label">Description</label>
                                        <textarea name="steps[__IDX__][description]" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="repeater-add" data-repeater-add="processRepeater">+ Add Step</button>
                        </div>
                    </div>
                </div>

                <div class="tab-panel" data-panel="seo">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" value="<?= e($service['meta_title'] ?? '') ?>" data-maxlength="60">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="3" data-maxlength="160"><?= e($service['meta_description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Canonical URL</label>
                                <input type="url" name="canonical_url" class="form-control" value="<?= e($service['canonical_url'] ?? '') ?>" placeholder="Leave blank for auto">
                            </div>
                            <div class="form-group">
                                <label class="form-label">OG Image</label>
                                <input type="text" name="og_image" class="form-control" value="<?= e($service['og_image'] ?? '') ?>">
                            </div>
                        </div>
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
                            <option value="<?= $s ?>" <?= ($service['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" id="is_featured" name="is_featured" class="form-check-input" value="1" <?= !empty($service['is_featured']) ? 'checked' : '' ?>>
                        <label for="is_featured" class="form-check-label">Featured service</label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= e($service['sort_order'] ?? 0) ?>" min="0">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%">Save Service</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php \App\Support\View::endSection() ?>
