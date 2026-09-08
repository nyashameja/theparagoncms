<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<?php $isEdit = !empty($article['id']); ?>
<div class="page-header">
    <div class="page-header-left">
        <h1><?= $isEdit ? 'Edit Article' : 'New Article' ?></h1>
        <p><a href="/admin/articles">← Articles</a></p>
    </div>
    <?php if ($isEdit && $article['status'] === 'published'): ?>
    <a href="/blog/<?= e($article['slug']) ?>" target="_blank" class="btn btn-secondary">View Live ↗</a>
    <?php endif; ?>
</div>

<form method="POST" action="<?= $isEdit ? '/admin/articles/' . $article['id'] : '/admin/articles' ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <?php if (!empty($errors)): ?>
    <div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div>
    <?php endif; ?>

    <div class="content-grid">
        <div>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Title <span class="required">*</span></label>
                        <input type="text" id="slugSource" name="title" class="form-control" value="<?= e($article['title'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug <span class="required">*</span></label>
                        <input type="text" id="slug" name="slug" class="form-control" value="<?= e($article['slug'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" class="form-control" rows="3" data-maxlength="300"><?= e($article['excerpt'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><span class="card-title">Content</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <div data-editor="article_content">
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
                            <div class="editor-area" contenteditable="true" style="min-height:400px"></div>
                        </div>
                        <textarea id="article_content" name="content" style="display:none"><?= e($article['content'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><span class="card-title">SEO</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="<?= e($article['meta_title'] ?? '') ?>" data-maxlength="60">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2" data-maxlength="160"><?= e($article['meta_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Canonical URL</label>
                        <input type="url" name="canonical_url" class="form-control" value="<?= e($article['canonical_url'] ?? '') ?>" placeholder="Leave blank for auto">
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
                            <?php foreach (['draft','review','scheduled','published','archived'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($article['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Published At</label>
                        <input type="datetime-local" name="published_at" class="form-control" value="<?= e(isset($article['published_at']) ? date('Y-m-d\TH:i', strtotime($article['published_at'])) : '') ?>">
                        <div class="form-hint">Leave blank to auto-set on publish</div>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" id="is_featured" name="is_featured" class="form-check-input" value="1" <?= !empty($article['is_featured']) ? 'checked' : '' ?>>
                        <label for="is_featured" class="form-check-label">Featured article</label>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%">Save Article</button>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><span class="card-title">Featured Image</span></div>
                <div class="card-body">
                    <div class="d-flex gap-2 align-center mb-2">
                        <input type="text" name="featured_image" id="featuredImage" class="form-control" value="<?= e($article['featured_image'] ?? '') ?>" placeholder="/uploads/…">
                        <button type="button" class="btn btn-secondary btn-sm" data-media-picker data-target="featuredImage" data-preview="featuredPreview">Browse</button>
                    </div>
                    <img id="featuredPreview" src="<?= e($article['featured_image'] ?? '') ?>" class="image-preview" style="width:100%;height:120px;object-fit:cover <?= empty($article['featured_image']) ? ';display:none' : '' ?>">
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><span class="card-title">Categories & Tags</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Categories</label>
                        <?php foreach ($categories ?? [] as $cat): ?>
                        <div class="form-check">
                            <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" class="form-check-input"
                                <?= in_array($cat['id'], $articleCategories ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label"><?= e($cat['name']) ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tags</label>
                        <input type="text" name="tags" class="form-control" placeholder="tag1, tag2, new-tag"
                            value="<?= e(implode(', ', array_column($articleTags ?? [], 'name'))) ?>">
                        <div class="form-hint">Comma-separated. New tags are created automatically.</div>
                    </div>
                </div>
            </div>

            <?php if ($isEdit && !empty($revisions)): ?>
            <div class="card">
                <div class="card-header"><span class="card-title">Revision History</span></div>
                <div class="card-body">
                    <?php foreach (array_slice($revisions, 0, 5) as $rev): ?>
                    <div class="d-flex justify-between align-center mb-2">
                        <span class="text-sm"><?= format_date($rev['created_at'], 'd M Y H:i') ?></span>
                        <span class="text-sm text-muted"><?= e($rev['user_name'] ?? 'System') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>
<?php \App\Support\View::endSection() ?>
