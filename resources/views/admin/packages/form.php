<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<?php $isEdit = !empty($package['id']); ?>
<div class="page-header">
    <div class="page-header-left"><h1><?= $isEdit ? 'Edit Package' : 'New Package' ?></h1><p><a href="/admin/packages">← Packages</a></p></div>
</div>
<form method="POST" action="<?= $isEdit ? '/admin/packages/' . $package['id'] : '/admin/packages' ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
    <div class="content-grid">
        <div class="card"><div class="card-body">
            <?php if (!empty($errors)): ?><div class="flash flash-error mb-3"><?= e(array_values($errors)[0]) ?></div><?php endif; ?>
            <div class="form-group"><label class="form-label">Name <span class="required">*</span></label><input type="text" name="name" class="form-control" value="<?= e($package['name'] ?? '') ?>" required></div>
            <div class="form-group"><label class="form-label">Category</label><input type="text" name="category" class="form-control" value="<?= e($package['category'] ?? 'general') ?>" placeholder="e.g. web-design, seo, hosting"></div>
            <div class="form-group"><label class="form-label">Short Description</label><textarea name="short_description" class="form-control" rows="3"><?= e($package['short_description'] ?? '') ?></textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group"><label class="form-label">Starting Price</label><input type="text" name="starting_price" class="form-control" value="<?= e($package['starting_price'] ?? '') ?>" placeholder="e.g. R 5,000"></div>
                <div class="form-group"><label class="form-label">Billing Type</label><select name="billing_type" class="form-control"><?php foreach (['once_off'=>'Once Off','monthly'=>'Monthly','annual'=>'Annual','custom'=>'Custom'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($package['billing_type'] ?? 'once_off') === $v ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="form-group"><label class="form-label">Price Note</label><input type="text" name="price_note" class="form-control" value="<?= e($package['price_note'] ?? '') ?>" placeholder="e.g. Prices excluding VAT"></div>
            <div class="form-group"><label class="form-label">Included Items (one per line)</label><textarea name="included_text" class="form-control" rows="8" placeholder="5 pages&#10;Contact form&#10;SEO setup"><?php
                $items = $package['included_items'] ?? [];
                if (is_string($items)) $items = json_decode($items, true) ?? [];
                echo e(implode("\n", $items));
            ?></textarea></div>
            <div class="form-group"><label class="form-label">Exclusions / Caveats</label><textarea name="exclusions" class="form-control" rows="3"><?= e($package['exclusions'] ?? '') ?></textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group"><label class="form-label">CTA Label</label><input type="text" name="cta_label" class="form-control" value="<?= e($package['cta_label'] ?? 'Get Started') ?>"></div>
                <div class="form-group"><label class="form-label">CTA URL</label><input type="text" name="cta_url" class="form-control" value="<?= e($package['cta_url'] ?? '/contact') ?>"></div>
            </div>
        </div></div>
        <div>
            <div class="card mb-4"><div class="card-header"><span class="card-title">Publish</span></div><div class="card-body">
                <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control"><?php foreach (['draft','published','archived'] as $s): ?><option value="<?= $s ?>" <?= ($package['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
                <div class="form-check mb-2"><input type="checkbox" name="is_featured" class="form-check-input" value="1" <?= !empty($package['is_featured']) ? 'checked' : '' ?>><label class="form-check-label">Featured</label></div>
                <div class="form-check mb-3"><input type="checkbox" name="custom_quote" class="form-check-input" value="1" <?= !empty($package['custom_quote']) ? 'checked' : '' ?>><label class="form-check-label">Custom quote (hides price)</label></div>
                <div class="form-group"><label class="form-label">Linked Service</label><select name="service_id" class="form-control"><option value="">— None —</option><?php foreach ($services ?? [] as $s): ?><option value="<?= $s['id'] ?>" <?= ($package['service_id'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($package['sort_order'] ?? 0) ?>"></div>
                <button type="submit" class="btn btn-primary" style="width:100%">Save Package</button>
            </div></div>
        </div>
    </div>
</form>
<?php \App\Support\View::endSection() ?>
