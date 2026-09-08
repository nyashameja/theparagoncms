<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>
<div class="page-header">
    <div class="page-header-left"><h1><?= e($audit['name']) ?> — Audit</h1><p><a href="/admin/audits">← Audit Requests</a></p></div>
</div>
<div class="content-grid">
    <div>
        <div class="card mb-4"><div class="card-header"><span class="card-title">Request Details</span></div><div class="card-body">
            <dl style="display:grid;grid-template-columns:140px 1fr;gap:8px 16px">
                <dt class="text-sm fw-600">Name</dt><dd class="text-sm"><?= e($audit['name']) ?></dd>
                <dt class="text-sm fw-600">Email</dt><dd class="text-sm"><a href="mailto:<?= e($audit['email']) ?>"><?= e($audit['email']) ?></a></dd>
                <?php if ($audit['phone']): ?><dt class="text-sm fw-600">Phone</dt><dd class="text-sm"><?= e($audit['phone']) ?></dd><?php endif; ?>
                <?php if ($audit['company']): ?><dt class="text-sm fw-600">Company</dt><dd class="text-sm"><?= e($audit['company']) ?></dd><?php endif; ?>
                <dt class="text-sm fw-600">Website</dt><dd class="text-sm"><a href="<?= e($audit['website_url']) ?>" target="_blank"><?= e($audit['website_url']) ?></a></dd>
                <dt class="text-sm fw-600">Submitted</dt><dd class="text-sm"><?= format_date($audit['created_at'], 'd M Y H:i') ?></dd>
            </dl>
            <?php if ($audit['notes']): ?>
            <hr class="separator"><p class="text-sm fw-600 mb-2">Notes</p><p class="text-sm"><?= e($audit['notes']) ?></p>
            <?php endif; ?>
        </div></div>

        <?php if ($report): ?>
        <div class="card"><div class="card-header"><span class="card-title">Audit Report</span></div><div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px">
                <?php foreach (['overall'=>'Overall','performance'=>'Performance','seo'=>'SEO','mobile'=>'Mobile','accessibility'=>'Accessibility','security'=>'Security'] as $key=>$label): ?>
                <?php $score = $report[$key . '_score'] ?? null; if ($score === null) continue; ?>
                <div class="text-center">
                    <div class="score-circle <?= $score >= 70 ? 'score-high' : ($score >= 40 ? 'score-mid' : 'score-low') ?>" style="margin:0 auto 4px"><?= $score ?></div>
                    <div class="text-sm text-muted"><?= $label ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div></div>
        <?php endif; ?>
    </div>
    <div>
        <div class="card"><div class="card-header"><span class="card-title">Status</span></div><div class="card-body">
            <form method="POST" action="/admin/audits/<?= $audit['id'] ?>/status">
                <?= csrf_field() ?>
                <div class="form-group"><label class="form-label">Status</label>
                    <select name="status" class="form-control"><?php foreach (['pending','in_progress','completed','sent'] as $s): ?><option value="<?= $s ?>" <?= $audit['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option><?php endforeach; ?></select></div>
                <button type="submit" class="btn btn-primary" style="width:100%">Update Status</button>
            </form>
        </div></div>
    </div>
</div>
<?php \App\Support\View::endSection() ?>
