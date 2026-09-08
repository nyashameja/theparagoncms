<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Free Website Audit Request Received</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}.wrapper{max-width:600px;margin:32px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)}.header{background:#0a0a0a;padding:32px;text-align:center}.header h1{color:#fff;font-size:1.5rem;margin:0 0 4px}.header p{color:rgba(255,255,255,.6);margin:0;font-size:14px}.body{padding:32px;color:#333;line-height:1.6}.checklist{list-style:none;padding:0;margin:16px 0}.checklist li{padding:8px 0 8px 28px;border-bottom:1px solid #f0f0f0;position:relative}.checklist li::before{content:"✓";position:absolute;left:0;color:#D71920;font-weight:700}.footer{background:#f4f4f4;padding:16px 32px;font-size:12px;color:#888;text-align:center}</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>Audit Request Received!</h1>
    <p>We'll have your report ready within 2 business days.</p>
  </div>
  <div class="body">
    <p>Hi <?= htmlspecialchars($first_name ?? $name ?? 'there', ENT_QUOTES, 'UTF-8') ?>,</p>
    <p>Great news — we've received your free website audit request for <strong><?= htmlspecialchars($website_url ?? '', ENT_QUOTES, 'UTF-8') ?></strong>.</p>
    <p>Here's what we'll be auditing:</p>
    <ul class="checklist">
      <li>Performance &amp; Core Web Vitals</li>
      <li>On-page SEO &amp; crawlability</li>
      <li>Mobile responsiveness</li>
      <li>Accessibility basics (WCAG 2.2 AA)</li>
      <li>Security headers &amp; HTTPS</li>
    </ul>
    <p>We'll send your full report to <strong><?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?></strong> within 2 business days.</p>
    <p>If you have any questions in the meantime, reply to this email or <a href="<?= htmlspecialchars($site_url ?? '/', ENT_QUOTES, 'UTF-8') ?>/contact">contact us</a>.</p>
    <hr style="border:none;border-top:1px solid #eee;margin:24px 0">
    <p style="font-size:13px;color:#888">Handled in accordance with our <a href="<?= htmlspecialchars($site_url ?? '/', ENT_QUOTES, 'UTF-8') ?>/privacy-policy">Privacy Policy</a> and POPIA.</p>
  </div>
  <div class="footer">The Paragon .Design &bull; <a href="<?= htmlspecialchars($site_url ?? '/', ENT_QUOTES, 'UTF-8') ?>">theparagon.design</a></div>
</div>
</body>
</html>
