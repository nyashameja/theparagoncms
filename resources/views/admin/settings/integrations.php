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
        <form method="POST" action="/admin/settings/integrations/save">
            <?= csrf_field() ?>
            <div class="card mb-4">
                <div class="card-header"><span class="card-title">reCAPTCHA</span></div>
                <div class="card-body">
                    <div class="setting-row"><div><div class="setting-label">Site Key</div></div><div><input type="text" name="recaptcha_site_key" class="form-control" value="<?= e($settings['recaptcha_site_key'] ?? '') ?>"></div></div>
                    <div class="setting-row"><div><div class="setting-label">Secret Key</div></div><div><input type="password" name="recaptcha_secret_key" class="form-control" autocomplete="off" placeholder="Leave blank to keep existing"></div></div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header"><span class="card-title">LeadForge Integration</span></div>
                <div class="card-body">
                    <div class="flash flash-warning mb-3" style="display:block">LeadForge push is disabled by default. Enable only when your webhook is live and tested.</div>
                    <div class="setting-row"><div><div class="setting-label">Enable LeadForge Push</div></div><div><label class="form-check"><input type="checkbox" name="leadforge_enabled" class="form-check-input" value="1" <?= !empty($settings['leadforge_enabled']) ? 'checked' : '' ?>><span class="form-check-label">Push new leads to LeadForge</span></label></div></div>
                    <div class="setting-row"><div><div class="setting-label">Webhook URL</div></div><div><input type="url" name="leadforge_webhook_url" class="form-control" value="<?= e($settings['leadforge_webhook_url'] ?? '') ?>" placeholder="https://…"></div></div>
                    <div class="setting-row"><div><div class="setting-label">API Key</div></div><div><input type="password" name="leadforge_api_key" class="form-control" autocomplete="off" placeholder="Leave blank to keep existing"></div></div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header"><span class="card-title">AuditForge Integration</span></div>
                <div class="card-body">
                    <div class="flash flash-warning mb-3" style="display:block">AuditForge is disabled by default. Enable only when your AuditForge account is active.</div>
                    <div class="setting-row"><div><div class="setting-label">Enable AuditForge</div></div><div><label class="form-check"><input type="checkbox" name="auditforge_enabled" class="form-check-input" value="1" <?= !empty($settings['auditforge_enabled']) ? 'checked' : '' ?>><span class="form-check-label">Enable AuditForge automated audits</span></label></div></div>
                    <div class="setting-row"><div><div class="setting-label">API Endpoint</div></div><div><input type="url" name="auditforge_api_url" class="form-control" value="<?= e($settings['auditforge_api_url'] ?? '') ?>"></div></div>
                    <div class="setting-row"><div><div class="setting-label">API Key</div></div><div><input type="password" name="auditforge_api_key" class="form-control" autocomplete="off" placeholder="Leave blank to keep existing"></div></div>
                </div>
            </div>
            <div class="card-footer"><button type="submit" class="btn btn-primary">Save Integration Settings</button></div>
        </form>
    </div>
</div>
<?php \App\Support\View::endSection() ?>
