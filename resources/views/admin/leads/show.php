<?php \App\Support\View::extend('admin/layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <h1><?= e($lead['name']) ?></h1>
        <p><a href="/admin/leads">← Lead Inbox</a></p>
    </div>
    <div class="d-flex gap-2">
        <a href="mailto:<?= e($lead['email']) ?>" class="btn btn-secondary">Send Email</a>
        <form method="POST" action="/admin/leads/<?= $lead['id'] ?>/delete">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger" data-confirm="Archive this lead?">Archive</button>
        </form>
    </div>
</div>

<div class="content-grid">
    <div>
        <!-- Lead details -->
        <div class="card mb-4">
            <div class="card-header"><span class="card-title">Lead Details</span></div>
            <div class="card-body">
                <dl style="display:grid;grid-template-columns:140px 1fr;gap:8px 16px">
                    <dt class="text-sm fw-600">Name</dt><dd class="text-sm"><?= e($lead['name']) ?></dd>
                    <dt class="text-sm fw-600">Email</dt><dd class="text-sm"><a href="mailto:<?= e($lead['email']) ?>"><?= e($lead['email']) ?></a></dd>
                    <?php if ($lead['phone']): ?><dt class="text-sm fw-600">Phone</dt><dd class="text-sm"><?= e($lead['phone']) ?></dd><?php endif; ?>
                    <?php if ($lead['company']): ?><dt class="text-sm fw-600">Company</dt><dd class="text-sm"><?= e($lead['company']) ?></dd><?php endif; ?>
                    <?php if ($lead['website']): ?><dt class="text-sm fw-600">Website</dt><dd class="text-sm"><a href="<?= e($lead['website']) ?>" target="_blank" rel="noopener"><?= e($lead['website']) ?></a></dd><?php endif; ?>
                    <dt class="text-sm fw-600">Form Type</dt><dd class="text-sm"><?= e(ucfirst($lead['form_type'])) ?></dd>
                    <dt class="text-sm fw-600">Service</dt><dd class="text-sm"><?= e($lead['service_type'] ?? '—') ?></dd>
                    <?php if ($lead['landing_page']): ?><dt class="text-sm fw-600">Landing Page</dt><dd class="text-sm"><?= e($lead['landing_page']) ?></dd><?php endif; ?>
                    <?php if ($lead['utm_source']): ?><dt class="text-sm fw-600">UTM Source</dt><dd class="text-sm"><?= e($lead['utm_source']) ?> / <?= e($lead['utm_medium'] ?? '') ?> / <?= e($lead['utm_campaign'] ?? '') ?></dd><?php endif; ?>
                    <dt class="text-sm fw-600">Submitted</dt><dd class="text-sm"><?= format_date($lead['created_at'], 'd M Y H:i') ?></dd>
                    <dt class="text-sm fw-600">Consent</dt><dd class="text-sm"><?= $lead['consent'] ? '✓ Given ' . format_date($lead['consent_at'], 'd M Y') : '—' ?></dd>
                </dl>
                <?php if (!empty($lead['message'])): ?>
                <hr class="separator">
                <p class="text-sm fw-600 mb-2">Message</p>
                <p class="text-sm" style="white-space:pre-wrap"><?= e($lead['message']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notes -->
        <div class="card mb-4">
            <div class="card-header"><span class="card-title">Notes</span></div>
            <div class="card-body">
                <?php if (empty($lead['notes'])): ?>
                    <p class="text-sm text-muted">No notes yet</p>
                <?php else: ?>
                <div class="notes-list mb-4">
                    <?php foreach ($lead['notes'] as $note): ?>
                    <div class="note-item">
                        <div class="note-meta"><?= e($note['user_name'] ?? 'System') ?> · <?= format_date($note['created_at'], 'd M Y H:i') ?></div>
                        <div class="note-content"><?= e($note['content']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <form method="POST" action="/admin/leads/<?= $lead['id'] ?>/note">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="3" placeholder="Add a note…" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm">Add Note</button>
                </form>
            </div>
        </div>

        <!-- Activity log -->
        <?php if (!empty($lead['activities'])): ?>
        <div class="card">
            <div class="card-header"><span class="card-title">Activity Log</span></div>
            <div class="card-body">
                <?php foreach (array_reverse($lead['activities']) as $act): ?>
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div>
                        <span class="fw-600"><?= e(ucfirst(str_replace('_', ' ', $act['action']))) ?></span>
                        <span class="text-muted"> · <?= format_date($act['created_at'], 'd M Y H:i') ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div>
        <!-- Status management -->
        <div class="card mb-4">
            <div class="card-header"><span class="card-title">Manage Lead</span></div>
            <div class="card-body">
                <form method="POST" action="/admin/leads/<?= $lead['id'] ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <?php foreach (['new','contacted','qualified','proposal_sent','won','lost','spam','archived'] as $s): ?>
                            <option value="<?= $s ?>" <?= $lead['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-control">
                            <?php foreach (['low','normal','high','urgent'] as $p): ?>
                            <option value="<?= $p ?>" <?= $lead['priority'] === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Follow-up Date</label>
                        <input type="date" name="follow_up_date" class="form-control" value="<?= e($lead['follow_up_date'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%">Save Changes</button>
                </form>
            </div>
        </div>

        <?php if (!empty($lead['attachments'])): ?>
        <div class="card mb-4">
            <div class="card-header"><span class="card-title">Attachments</span></div>
            <div class="card-body">
                <?php foreach ($lead['attachments'] as $att): ?>
                <div class="d-flex align-center justify-between mb-2">
                    <span class="text-sm truncate"><?= e($att['original_name']) ?></span>
                    <span class="text-sm text-muted"><?= format_bytes($att['file_size']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header"><span class="card-title">Technical</span></div>
            <div class="card-body">
                <dl style="display:grid;grid-template-columns:100px 1fr;gap:6px">
                    <dt class="text-sm fw-600">IP</dt><dd class="text-sm text-muted"><?= e($lead['ip_address'] ?? '—') ?></dd>
                    <dt class="text-sm fw-600">Lead ID</dt><dd class="text-sm text-muted">#<?= $lead['id'] ?></dd>
                    <?php if ($lead['source']): ?><dt class="text-sm fw-600">Source</dt><dd class="text-sm text-muted"><?= e($lead['source']) ?></dd><?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
</div>
<?php \App\Support\View::endSection() ?>
