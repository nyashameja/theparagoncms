<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>New Contact Enquiry</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}.wrapper{max-width:600px;margin:32px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)}.header{background:#0a0a0a;padding:24px 32px}.header h1{color:#D71920;font-size:1.25rem;margin:0}.body{padding:32px}.field{margin-bottom:16px}.label{font-size:12px;font-weight:600;text-transform:uppercase;color:#888;margin-bottom:4px}.value{font-size:15px;color:#111}.footer{background:#f4f4f4;padding:16px 32px;font-size:12px;color:#888;text-align:center}</style>
</head>
<body>
<div class="wrapper">
  <div class="header"><h1>New Contact Enquiry</h1></div>
  <div class="body">
    <div class="field"><div class="label">Name</div><div class="value"><?= htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8') ?></div></div>
    <div class="field"><div class="label">Email</div><div class="value"><a href="mailto:<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?></a></div></div>
    <?php if (!empty($phone)): ?><div class="field"><div class="label">Phone</div><div class="value"><?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?></div></div><?php endif; ?>
    <?php if (!empty($company)): ?><div class="field"><div class="label">Company</div><div class="value"><?= htmlspecialchars($company, ENT_QUOTES, 'UTF-8') ?></div></div><?php endif; ?>
    <?php if (!empty($service)): ?><div class="field"><div class="label">Service Interested In</div><div class="value"><?= htmlspecialchars($service, ENT_QUOTES, 'UTF-8') ?></div></div><?php endif; ?>
    <div class="field"><div class="label">Message</div><div class="value" style="white-space:pre-wrap"><?= htmlspecialchars($message ?? '', ENT_QUOTES, 'UTF-8') ?></div></div>
    <div class="field"><div class="label">Submitted</div><div class="value"><?= htmlspecialchars($submitted_at ?? '', ENT_QUOTES, 'UTF-8') ?></div></div>
    <p style="margin-top:24px"><a href="<?= htmlspecialchars($admin_url ?? '', ENT_QUOTES, 'UTF-8') ?>" style="background:#D71920;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:600">View in CMS</a></p>
  </div>
  <div class="footer">The Paragon .Design — Internal Notification</div>
</div>
</body>
</html>
