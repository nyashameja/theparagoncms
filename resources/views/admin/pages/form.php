<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<?php $isEdit = !empty($page['id']); ?>
<div class="page-header">
    <div class="page-header-left">
        <h1><?= $isEdit ? 'Edit Page' : 'New Page' ?></h1>
        <p><a href="/admin/pages">← Pages</a></p>
    </div>
    <?php if ($isEdit && ($page['status'] ?? '') === 'published'): ?>
    <a href="/page/<?= e($page['slug']) ?>" target="_blank" class="btn btn-secondary">View Live ↗</a>
    <?php endif; ?>
</div>

<form method="POST" action="<?= $isEdit ? '/admin/pages/' . $page['id'] : '/admin/pages' ?>">
    <?= csrf_field() ?>

    <?php if (!empty($errors)): ?>
    <div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div>
    <?php endif; ?>

    <div class="content-grid">
        <div>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Title <span class="required">*</span></label>
                        <input type="text" id="slugSource" name="title" class="form-control"
                               value="<?= e($page['title'] ?? old('title')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-prefix">/page/</span>
                            <input type="text" id="slug" name="slug" class="form-control"
                                   value="<?= e($page['slug'] ?? old('slug')) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><span class="card-title">Hero Section</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="show_hero" value="1" <?= !empty($page['show_hero']) ? 'checked' : '' ?>>
                            Show hero section
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hero Title</label>
                        <input type="text" name="hero_title" class="form-control"
                               value="<?= e($page['hero_title'] ?? old('hero_title')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hero Subtitle</label>
                        <textarea name="hero_subtitle" class="form-control" rows="2"><?= e($page['hero_subtitle'] ?? old('hero_subtitle')) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hero Image URL</label>
                        <input type="text" name="hero_image" class="form-control"
                               value="<?= e($page['hero_image'] ?? old('hero_image')) ?>">
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><span class="card-title">Content</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <div data-editor="page_content">
                            <div class="editor-toolbar">
                                <button type="button" class="editor-btn" data-cmd="bold"><b>B</b></button>
                                <button type="button" class="editor-btn" data-cmd="italic"><i>I</i></button>
                                <button type="button" class="editor-btn" data-cmd="underline"><u>U</u></button>
                                <button type="button" class="editor-btn" data-cmd="insertUnorderedList">• List</button>
                                <button type="button" class="editor-btn" data-cmd="insertOrderedList">1. List</button>
                                <button type="button" class="editor-btn" data-cmd="formatBlock" data-arg="h2">H2</button>
                                <button type="button" class="editor-btn" data-cmd="formatBlock" data-arg="h3">H3</button>
                                <button type="button" class="editor-btn" data-cmd="formatBlock" data-arg="blockquote">❝</button>
                                <button type="button" class="editor-btn" data-cmd="formatBlock" data-arg="p">¶</button>
                                <button type="button" class="editor-btn" data-cmd="removeFormat">✕ fmt</button>
                            </div>
                            <div class="editor-area" contenteditable="true" style="min-height:300px"><?= $page['content'] ?? '' ?></div>
                        </div>
                        <textarea name="content" id="page_content" hidden><?= $page['content'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><span class="card-title">CTA</span></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">CTA Label</label>
                            <input type="text" name="cta_label" class="form-control"
                                   value="<?= e($page['cta_label'] ?? old('cta_label')) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CTA URL</label>
                            <input type="text" name="cta_url" class="form-control"
                                   value="<?= e($page['cta_url'] ?? old('cta_url')) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card mb-4">
                <div class="card-header"><span class="card-title">Status</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" class="form-control" required>
                            <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($page['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Layout</label>
                        <select name="layout" class="form-control">
                            <?php foreach (['default', 'wide', 'narrow', 'landing'] as $l): ?>
                            <option value="<?= $l ?>" <?= ($page['layout'] ?? 'default') === $l ? 'selected' : '' ?>><?= ucfirst($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><span class="card-title">Navigation</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="is_in_nav" value="1" <?= !empty($page['is_in_nav']) ? 'checked' : '' ?>>
                            Include in navigation
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nav Label</label>
                        <input type="text" name="nav_label" class="form-control"
                               value="<?= e($page['nav_label'] ?? old('nav_label')) ?>"
                               placeholder="Defaults to page title">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nav Order</label>
                        <input type="number" name="nav_order" class="form-control"
                               value="<?= e($page['nav_order'] ?? 0) ?>" min="0">
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><span class="card-title">SEO</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control"
                               value="<?= e($page['meta_title'] ?? old('meta_title')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3"
                                  data-maxlength="160"><?= e($page['meta_description'] ?? old('meta_description')) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">OG Image URL</label>
                        <input type="text" name="og_image" class="form-control"
                               value="<?= e($page['og_image'] ?? old('og_image')) ?>">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">Save Page</button>
                <?php if ($isEdit): ?>
                <form method="POST" action="/admin/pages/<?= $page['id'] ?>/delete" style="display:inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-ghost text-danger" data-confirm="Delete this page?">Delete</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<?php \App\Support\View::endSection() ?>
