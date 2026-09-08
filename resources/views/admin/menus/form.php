<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<?php $isEdit = !empty($menu['id']); ?>
<div class="page-header">
  <div class="page-header-left"><h1><?= $isEdit ? 'Edit Menu' : 'New Menu' ?></h1><p><a href="/admin/menus">← Menus</a></p></div>
</div>
<form method="POST" action="<?= $isEdit ? '/admin/menus/' . $menu['id'] : '/admin/menus' ?>">
  <?= csrf_field() ?>
  <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
  <div class="content-grid">
    <div>
      <div class="card mb-3"><div class="card-body">
        <?php if (!empty($errors)): ?><div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div><?php endif; ?>
        <div class="form-group">
          <label class="form-label">Menu Name <span class="required">*</span></label>
          <input type="text" name="name" class="form-control" value="<?= e($menu['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Location <span class="required">*</span></label>
          <select name="location" class="form-control">
            <?php foreach (['primary_nav' => 'Primary Navigation', 'footer_nav' => 'Footer Navigation', 'footer_legal' => 'Footer Legal'] as $val => $label): ?>
            <option value="<?= $val ?>" <?= ($menu['location'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div></div>

      <?php if ($isEdit): ?>
      <!-- Menu items -->
      <div class="card"><div class="card-header" style="display:flex;align-items:center;justify-content:space-between"><span class="card-title">Menu Items</span><button type="button" class="btn btn-sm btn-outline" id="add-menu-item">+ Add Item</button></div><div class="card-body">
        <div id="menu-items-list">
          <?php foreach ($items ?? [] as $item): ?>
          <div class="menu-item-row" style="display:grid;grid-template-columns:1fr 1fr auto auto;gap:8px;align-items:center;margin-bottom:8px">
            <input type="text" name="item_label[]" class="form-control" placeholder="Label" value="<?= e($item['label']) ?>">
            <input type="text" name="item_url[]" class="form-control" placeholder="/url or https://…" value="<?= e($item['url']) ?>">
            <select name="item_target[]" class="form-control" style="width:auto">
              <option value="_self" <?= ($item['target'] ?? '_self') === '_self' ? 'selected' : '' ?>>Same tab</option>
              <option value="_blank" <?= ($item['target'] ?? '') === '_blank' ? 'selected' : '' ?>>New tab</option>
            </select>
            <button type="button" class="btn btn-sm btn-danger remove-menu-item" aria-label="Remove">&times;</button>
          </div>
          <?php endforeach; ?>
        </div>
        <template id="menu-item-tpl">
          <div class="menu-item-row" style="display:grid;grid-template-columns:1fr 1fr auto auto;gap:8px;align-items:center;margin-bottom:8px">
            <input type="text" name="item_label[]" class="form-control" placeholder="Label">
            <input type="text" name="item_url[]" class="form-control" placeholder="/url or https://…">
            <select name="item_target[]" class="form-control" style="width:auto">
              <option value="_self">Same tab</option>
              <option value="_blank">New tab</option>
            </select>
            <button type="button" class="btn btn-sm btn-danger remove-menu-item" aria-label="Remove">&times;</button>
          </div>
        </template>
      </div></div>
      <?php endif; ?>
    </div>

    <div><div class="card"><div class="card-body">
      <button type="submit" class="btn btn-primary" style="width:100%">Save Menu</button>
    </div></div></div>
  </div>
</form>
<script>
document.getElementById('add-menu-item')?.addEventListener('click', function() {
  var tpl = document.getElementById('menu-item-tpl');
  var clone = tpl.content.cloneNode(true);
  document.getElementById('menu-items-list').appendChild(clone);
});
document.getElementById('menu-items-list')?.addEventListener('click', function(e) {
  if (e.target.classList.contains('remove-menu-item')) e.target.closest('.menu-item-row').remove();
});
</script>
<?php \App\Support\View::endSection() ?>
