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
        <form method="POST" action="/admin/settings/email/save">
            <?= csrf_field() ?>
            <div class="card mb-4">
                <div class="card-header"><span class="card-title">SMTP Configuration</span></div>
                <div class="card-body">
                    <div class="setting-row"><div><div class="setting-label">SMTP Host</div></div><div><input type="text" name="mail_host" class="form-control" value="<?= e($settings['mail_host'] ?? '') ?>" placeholder="smtp.example.com"></div></div>
                    <div class="setting-row"><div><div class="setting-label">SMTP Port</div></div><div><input type="number" name="mail_port" class="form-control" value="<?= e($settings['mail_port'] ?? '587') ?>" style="width:100px"></div></div>
                    <div class="setting-row"><div><div class="setting-label">Encryption</div></div><div><select name="mail_encryption" class="form-control" style="width:auto"><option value="tls" <?= ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS / STARTTLS</option><option value="ssl" <?= ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option><option value="" <?= ($settings['mail_encryption'] ?? '') === '' ? 'selected' : '' ?>>None</option></select></div></div>
                    <div class="setting-row"><div><div class="setting-label">Username</div></div><div><input type="email" name="mail_username" class="form-control" value="<?= e($settings['mail_username'] ?? '') ?>"></div></div>
                    <div class="setting-row"><div><div class="setting-label">Password</div><div class="setting-hint">Leave blank to keep existing</div></div><div><input type="password" name="mail_password" class="form-control" autocomplete="new-password"></div></div>
                    <div class="setting-row"><div><div class="setting-label">From Address</div></div><div><input type="email" name="mail_from_address" class="form-control" value="<?= e($settings['mail_from_address'] ?? '') ?>"></div></div>
                    <div class="setting-row"><div><div class="setting-label">From Name</div></div><div><input type="text" name="mail_from_name" class="form-control" value="<?= e($settings['mail_from_name'] ?? '') ?>"></div></div>
                    <div class="setting-row"><div><div class="setting-label">Lead Notification Email(s)</div><div class="setting-hint">Comma-separated. Receives new lead notifications.</div></div><div><input type="text" name="lead_notification_email" class="form-control" value="<?= e($settings['lead_notification_email'] ?? '') ?>"></div></div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Email Settings</button>
                    <a href="/admin/settings/email/test" class="btn btn-secondary">Send Test Email</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php \App\Support\View::endSection() ?>
