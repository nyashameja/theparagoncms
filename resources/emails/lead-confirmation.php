<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>We received your enquiry</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}.wrapper{max-width:600px;margin:32px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)}.header{background:#0a0a0a;padding:32px;text-align:center}.header h1{color:#fff;font-size:1.5rem;margin:0 0 4px}.header p{color:rgba(255,255,255,.6);margin:0;font-size:14px}.body{padding:32px;color:#333;line-height:1.6}.footer{background:#f4f4f4;padding:16px 32px;font-size:12px;color:#888;text-align:center}</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>Thanks, <?= htmlspecialchars($first_name ?? $name ?? 'there', ENT_QUOTES, 'UTF-8') ?>!</h1>
    <p>We've received your enquiry.</p>
  </div>
  <div class="body">
    <p>Thank you for reaching out to <strong>The Paragon .Design</strong>. We've received your message and will get back to you within <strong>one business day</strong>.</p>
    <?php if (!empty($service)): ?>
    <p>You enquired about: <strong><?= htmlspecialchars($service, ENT_QUOTES, 'UTF-8') ?></strong></p>
    <?php endif; ?>
    <p>In the meantime, feel free to:</p>
    <ul style="padding-left:20px;margin:12px 0">
      <li><a href="<?= htmlspecialchars($site_url ?? '/', ENT_QUOTES, 'UTF-8') ?>/portfolio">Browse our portfolio</a></li>
      <li><a href="<?= htmlspecialchars($site_url ?? '/', ENT_QUOTES, 'UTF-8') ?>/case-studies">Read our case studies</a></li>
      <li><a href="<?= htmlspecialchars($site_url ?? '/', ENT_QUOTES, 'UTF-8') ?>/blog">Explore our blog</a></li>
    </ul>
    <?php if (!empty($whatsapp_number)): ?>
    <p>Or, for a faster response, <a href="https://wa.me/<?= htmlspecialchars(preg_replace('/\D/', '', $whatsapp_number), ENT_QUOTES, 'UTF-8') ?>">message us on WhatsApp</a>.</p>
    <?php endif; ?>
    <hr style="border:none;border-top:1px solid #eee;margin:24px 0">
    <p style="font-size:13px;color:#888">Your personal information is handled in accordance with our <a href="<?= htmlspecialchars($site_url ?? '/', ENT_QUOTES, 'UTF-8') ?>/privacy-policy">Privacy Policy</a> and POPIA. We will not share your details with third parties without your consent.</p>
  </div>
  <div class="footer">The Paragon .Design &bull; <a href="<?= htmlspecialchars($site_url ?? '/', ENT_QUOTES, 'UTF-8') ?>">theparagon.design</a></div>
</div>
</body>
</html>
