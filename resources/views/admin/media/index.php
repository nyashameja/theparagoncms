<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<?php $isPicker = !empty($_GET['picker']); ?>
<div class="page-header">
    <div class="page-header-left"><h1>Media Library</h1><p><?= number_format($total ?? 0) ?> files</p></div>
    <div class="d-flex gap-2">
        <label class="btn btn-primary" for="fileUpload">+ Upload Files</label>
        <input type="file" id="fileUpload" multiple accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx" style="display:none">
    </div>
</div>

<div class="card mb-4" id="uploadProgress" style="display:none">
    <div class="card-body">
        <p class="text-sm fw-600" id="uploadStatus">Uploading…</p>
        <progress id="uploadBar" value="0" max="100" style="width:100%;height:8px"></progress>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="filter-bar" style="width:100%">
            <?php if ($isPicker): ?><input type="hidden" name="picker" value="1"><?php endif; ?>
            <input type="text" name="search" class="form-control" placeholder="Search media…" value="<?= e($_GET['search'] ?? '') ?>">
            <select name="type" class="form-control" onchange="this.form.submit()">
                <option value="">All Types</option>
                <?php foreach (['image','document','video'] as $t): ?>
                <option value="<?= $t ?>" <?= ($_GET['type'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>
    <div class="card-body">
        <div class="media-grid" id="mediaGrid">
            <?php if (empty($media)): ?>
                <div class="empty-state" style="grid-column:1/-1"><div class="empty-state-icon">▣</div><p>No media files yet</p></div>
            <?php else: ?>
            <?php foreach ($media as $file): ?>
            <div class="media-item" data-id="<?= $file['id'] ?>" data-url="<?= e($file['url'] ?? '/uploads/' . $file['filename']) ?>">
                <?php if (str_starts_with($file['mime_type'] ?? '', 'image/')): ?>
                    <img src="<?= e($file['webp_path'] ?? ($file['url'] ?? '/uploads/' . $file['filename'])) ?>" alt="<?= e($file['alt_text'] ?? $file['original_name']) ?>" loading="lazy">
                <?php else: ?>
                    <div class="media-doc"><div class="media-doc-icon">📄</div><span><?= e(strtoupper(pathinfo($file['original_name'], PATHINFO_EXTENSION))) ?></span></div>
                <?php endif; ?>
                <div class="media-item-overlay">
                    <?php if ($isPicker): ?>
                    <button type="button" class="btn btn-sm btn-primary" onclick="window._mediaPick('<?= e($file['url'] ?? '/uploads/' . $file['filename']) ?>', <?= $file['id'] ?>)">Select</button>
                    <?php else: ?>
                    <a href="/admin/media/<?= $file['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                    <form method="POST" action="/admin/media/<?= $file['id'] ?>/delete" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete this file?">✕</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty($pagination)): ?>
    <div class="card-footer">
        <nav class="pagination">
            <?php for ($p = max(1, $pagination['current_page'] - 3); $p <= min($pagination['last_page'], $pagination['current_page'] + 3); $p++): ?>
            <div class="page-item <?= $p === $pagination['current_page'] ? 'active' : '' ?>">
                <a href="?<?= http_build_query(array_merge($_GET ?? [], ['page' => $p])) ?>" class="page-link"><?= $p ?></a>
            </div>
            <?php endfor; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('fileUpload').addEventListener('change', function () {
    const files = Array.from(this.files);
    if (!files.length) return;
    const prog = document.getElementById('uploadProgress');
    const bar = document.getElementById('uploadBar');
    const status = document.getElementById('uploadStatus');
    prog.style.display = '';
    let done = 0;
    files.forEach(function (file) {
        const fd = new FormData();
        fd.append('file', file);
        fd.append('_token', document.querySelector('meta[name=csrf-token]')?.content || '');
        fetch('/admin/media/upload', { method: 'POST', body: fd, headers: { 'X-CSRF-Token': document.querySelector('[name=_token]')?.value || '' } })
            .then(r => r.json())
            .then(json => {
                done++;
                bar.value = (done / files.length) * 100;
                status.textContent = done + ' / ' + files.length + ' uploaded';
                if (done === files.length) {
                    setTimeout(() => location.reload(), 600);
                }
                if (!json.success) console.error(json.message);
            })
            .catch(() => { done++; status.textContent = 'Error uploading ' + file.name; });
    });
});
</script>
<?php \App\Support\View::endSection() ?>
