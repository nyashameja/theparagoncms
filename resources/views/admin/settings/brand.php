<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<div class="page-header"><div class="page-header-left"><h1>Settings</h1></div></div>
<div style="display:flex;gap:20px;align-items:flex-start">
    <nav style="width:180px;flex-shrink:0">
        <div style="background:#fff;border:1px solid #e5e5e5;border-radius:6px;overflow:hidden">
            <?php foreach (['/admin/settings/general'=>'General','/admin/settings/brand'=>'Brand','/admin/settings/seo'=>'SEO','/admin/settings/email'=>'Email','/admin/settings/integrations'=>'Integrations'] as $url => $label): ?>
            <a href="<?= $url ?>" style="display:block;padding:10px 14px;font-size:13px;font-weight:500;color:<?= str_contains($_SERVER['REQUEST_URI'],$url)?'#D71920':'#333' ?>;background:<?= str_contains($_SERVER['REQUEST_URI'],$url)?'#fff5f5':'#fff' ?>;text-decoration:none;border-bottom:1px solid #f0f0f0"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    </nav>
    <div style="flex:1">
        <form method="POST" action="/admin/settings/brand/save" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="card">
                <div class="card-header"><span class="card-title">Brand Settings</span></div>
                <div class="card-body">
                    <div class="settings-section">
                        <div class="settings-section-title">Logos</div>
                        <?php foreach ([
                            ['logo_light','Logo (Light bg)'],['logo_dark','Logo (Dark bg)'],['favicon','Favicon (32×32)'],
                        ] as [$key,$label]): ?>
                        <div class="setting-row">
                            <div><div class="setting-label"><?= $label ?></div></div>
                            <div>
                                <div class="d-flex gap-2 align-center">
                                    <input type="text" name="<?= $key ?>" id="<?= $key ?>" class="form-control" value="<?= e($settings[$key] ?? '') ?>">
                                    <button type="button" class="btn btn-secondary btn-sm" data-media-picker data-target="<?= $key ?>" data-preview="<?= $key ?>_prev">Browse</button>
                                </div>
                                <?php if (!empty($settings[$key])): ?><img id="<?= $key ?>_prev" src="<?= e($settings[$key]) ?>" style="max-height:40px;margin-top:8px"><?php else: ?><img id="<?= $key ?>_prev" style="display:none;max-height:40px;margin-top:8px"><?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="settings-section">
                        <div class="settings-section-title">Colours</div>
                        <?php foreach ([
                            ['color_primary','Primary Colour','#D71920'],
                            ['color_secondary','Secondary Colour','#0a0a0a'],
                            ['color_accent','Accent Colour','#D71920'],
                        ] as [$key,$label,$default]): ?>
                        <div class="setting-row">
                            <div><div class="setting-label"><?= $label ?></div></div>
                            <div class="color-input-group">
                                <div class="color-swatch"><input type="color" name="<?= $key ?>" value="<?= e($settings[$key] ?? $default) ?>" data-color-preview="<?= $key ?>_swatch"></div>
                                <input type="text" class="form-control" style="width:120px" value="<?= e($settings[$key] ?? $default) ?>" id="<?= $key ?>_swatch" oninput="document.querySelector('[name=<?= $key ?>]').value=this.value">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="settings-section">
                        <div class="settings-section-title">Typography</div>
                        <div class="setting-row">
                            <div><div class="setting-label">Heading Font</div></div>
                            <div><input type="text" name="font_heading" class="form-control" value="<?= e($settings['font_heading'] ?? 'Inter') ?>"></div>
                        </div>
                        <div class="setting-row">
                            <div><div class="setting-label">Body Font</div></div>
                            <div><input type="text" name="font_body" class="form-control" value="<?= e($settings['font_body'] ?? 'Inter') ?>"></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer"><button type="submit" class="btn btn-primary">Save Brand</button></div>
            </div>
        </form>
    </div>
</div>
<?php \App\Support\View::endSection() ?>
