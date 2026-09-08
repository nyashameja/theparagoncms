<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<div class="page-header">
    <div class="page-header-left"><h1>Settings</h1></div>
</div>

<div style="display:flex;gap:20px;align-items:flex-start">
    <nav style="width:180px;flex-shrink:0">
        <div style="background:#fff;border:1px solid #e5e5e5;border-radius:6px;overflow:hidden">
            <?php foreach ([
                '/admin/settings/general' => 'General',
                '/admin/settings/brand' => 'Brand',
                '/admin/settings/seo' => 'SEO',
                '/admin/settings/email' => 'Email',
                '/admin/settings/integrations' => 'Integrations',
            ] as $url => $label): ?>
            <a href="<?= $url ?>" style="display:block;padding:10px 14px;font-size:13px;font-weight:500;color:<?= str_contains($_SERVER['REQUEST_URI'], $url) ? '#D71920' : '#333' ?>;background:<?= str_contains($_SERVER['REQUEST_URI'], $url) ? '#fff5f5' : '#fff' ?>;text-decoration:none;border-bottom:1px solid #f0f0f0"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    </nav>

    <div style="flex:1">
        <form method="POST" action="/admin/settings/general/save">
            <?= csrf_field() ?>
            <div class="card">
                <div class="card-header"><span class="card-title">General Settings</span></div>
                <div class="card-body">
                    <div class="settings-section">
                        <div class="settings-section-title">Business Information</div>
                        <?php foreach ([
                            ['business_name', 'Business Name', 'text'],
                            ['business_tagline', 'Tagline', 'text'],
                            ['contact_email', 'Contact Email', 'email'],
                            ['contact_phone', 'Phone', 'text'],
                            ['contact_address', 'Address', 'textarea'],
                            ['contact_city', 'City', 'text'],
                            ['contact_province', 'Province', 'text'],
                            ['contact_country', 'Country', 'text'],
                            ['contact_postal_code', 'Postal Code', 'text'],
                        ] as [$key, $label, $type]): ?>
                        <div class="setting-row">
                            <div><div class="setting-label"><?= $label ?></div></div>
                            <div>
                                <?php if ($type === 'textarea'): ?>
                                <textarea name="<?= $key ?>" class="form-control" rows="3"><?= e($settings[$key] ?? '') ?></textarea>
                                <?php else: ?>
                                <input type="<?= $type ?>" name="<?= $key ?>" class="form-control" value="<?= e($settings[$key] ?? '') ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="settings-section">
                        <div class="settings-section-title">Social Media</div>
                        <?php foreach ([
                            ['social_linkedin', 'LinkedIn URL'],
                            ['social_twitter', 'X / Twitter URL'],
                            ['social_facebook', 'Facebook URL'],
                            ['social_instagram', 'Instagram URL'],
                            ['social_youtube', 'YouTube URL'],
                            ['whatsapp_number', 'WhatsApp Number (international)'],
                        ] as [$key, $label]): ?>
                        <div class="setting-row">
                            <div><div class="setting-label"><?= $label ?></div></div>
                            <div><input type="text" name="<?= $key ?>" class="form-control" value="<?= e($settings[$key] ?? '') ?>"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="settings-section">
                        <div class="settings-section-title">Maintenance</div>
                        <div class="setting-row">
                            <div><div class="setting-label">Maintenance Mode</div><div class="setting-hint">Shows a maintenance page to public visitors</div></div>
                            <div>
                                <label class="form-check">
                                    <input type="checkbox" name="maintenance_mode" class="form-check-input" value="1" <?= !empty($settings['maintenance_mode']) ? 'checked' : '' ?>>
                                    <span class="form-check-label">Enable maintenance mode</span>
                                </label>
                            </div>
                        </div>
                        <div class="setting-row">
                            <div><div class="setting-label">Maintenance Message</div></div>
                            <div><textarea name="maintenance_message" class="form-control" rows="3"><?= e($settings['maintenance_message'] ?? '') ?></textarea></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php \App\Support\View::endSection() ?>
