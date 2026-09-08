<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header">
  <div class="page-header-left"><h1>Menus</h1></div>
  <div class="page-header-right"><a href="/admin/menus/create" class="btn btn-primary">+ New Menu</a></div>
</div>
<div class="card">
  <table class="table">
    <thead><tr><th>Name</th><th>Location</th><th>Items</th><th></th></tr></thead>
    <tbody>
      <?php if (empty($menus)): ?>
      <tr><td colspan="4" class="text-center text-muted py-3">No menus yet.</td></tr>
      <?php else: ?>
      <?php foreach ($menus as $menu): ?>
      <tr>
        <td><?= e($menu['name']) ?></td>
        <td><code><?= e($menu['location']) ?></code></td>
        <td><?= (int)($menu['item_count'] ?? 0) ?></td>
        <td class="table-actions">
          <a href="/admin/menus/<?= $menu['id'] ?>/edit" class="btn btn-sm btn-outline">Edit</a>
          <form method="POST" action="/admin/menus/<?= $menu['id'] ?>" style="display:inline">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete this menu?">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php \App\Support\View::endSection() ?>
