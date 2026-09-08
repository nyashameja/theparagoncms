<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header"><div class="page-header-left"><h1>Settings</h1></div></div>
<div style="display:flex;gap:20px;align-items:flex-start">
    <nav style="width:180px;flex-shrink:0"><div style="background:#fff;border:1px solid #e5e5e5;border-radius:6px;overflow:hidden">
        <?php foreach (['/admin/settings/general'=>'General','/admin/settings/brand'=>'Brand','/admin/settings/seo'=>'SEO','/admin/settings/email'=>'Email','/admin/settings/integrations'=>'Integrations'] as $url=>$label): ?>
        <a href="<?= $url ?>" style="display:block;padding:10px 14px;font-size:13px;font-weight:500;color:<?= str_contains($_SERVER['REQUEST_URI'],$url)?'#D71920':'#333' ?>;background:<?= str_contains($_SERVER['REQUEST_URI'],$url)?'#fff5f5':'#fff' ?>;text-decoration:none;border-bottom:1px solid #f0f0f0"><?= $label ?></a>
        <?php endforeach; ?>
    </div></nav>
    <div style="flex:1">
        <form method="POST" action="/admin/settings/seo/save">
            <?= csrf_field() ?>
            <div class="card">
                <div class="card-header"><span class="card-title">SEO Settings</span></div>
                <div class="card-body">
                    <div class="setting-row"><div><div class="setting-label">Site Title</div><div class="setting-hint">Appended to all page titles</div></div><div><input type="text" name="site_title" class="form-control" value="<?= e($settings['site_title'] ?? '') ?>"></div></div>
                    <div class="setting-row"><div><div class="setting-label">Default Meta Description</div></div><div><textarea name="meta_description" class="form-control" rows="3" data-maxlength="160"><?= e($settings['meta_description'] ?? '') ?></textarea></div></div>
                    <div class="setting-row"><div><div class="setting-label">Default OG Image</div></div><div><input type="text" name="og_image" class="form-control" value="<?= e($settings['og_image'] ?? '') ?>"></div></div>
                    <div class="setting-row"><div><div class="setting-label">Google Analytics ID</div><div class="setting-hint">e.g. G-XXXXXXXXXX</div></div><div><input type="text" name="ga4_id" class="form-control" value="<?= e($settings['ga4_id'] ?? '') ?>"></div></div>
                    <div class="setting-row"><div><div class="setting-label">Google Tag Manager ID</div><div class="setting-hint">e.g. GTM-XXXXXXX</div></div><div><input type="text" name="gtm_id" class="form-control" value="<?= e($settings['gtm_id'] ?? '') ?>"></div></div>
                    <div class="setting-row"><div><div class="setting-label">Robots.txt Override</div><div class="setting-hint">Leave blank for default (allow all)</div></div><div><textarea name="robots_txt" class="form-control" rows="6" style="font-family:monospace;font-size:12px"><?= e($settings['robots_txt'] ?? '') ?></textarea></div></div>
                    <div class="setting-row"><div><div class="setting-label">Header Scripts</div><div class="setting-hint">Injected before &lt;/head&gt;</div></div><div><textarea name="header_scripts" class="form-control" rows="4" style="font-family:monospace;font-size:12px"><?= e($settings['header_scripts'] ?? '') ?></textarea></div></div>
                    <div class="setting-row"><div><div class="setting-label">Footer Scripts</div><div class="setting-hint">Injected before &lt;/body&gt;</div></div><div><textarea name="footer_scripts" class="form-control" rows="4" style="font-family:monospace;font-size:12px"><?= e($settings['footer_scripts'] ?? '') ?></textarea></div></div>
                </div>
                <div class="card-footer"><button type="submit" class="btn btn-primary">Save SEO Settings</button></div>
            </div>
        </form>
    </div>
</div>
<?php \App\Support\View::endSection() ?>
