<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Reset Your Password</title>
<style>body{font-family:'Inter',Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}.wrapper{max-width:600px;margin:32px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)}.header{background:#0a0a0a;padding:24px 32px}.header h1{color:#D71920;font-size:1.25rem;margin:0}.body{padding:32px;color:#333;line-height:1.6}.btn{display:inline-block;background:#D71920;color:#fff;padding:14px 28px;border-radius:6px;text-decoration:none;font-weight:600;margin:16px 0}.footer{background:#f4f4f4;padding:16px 32px;font-size:12px;color:#888;text-align:center}</style>
</head>
<body>
<div class="wrapper">
  <div class="header"><h1>Password Reset Request</h1></div>
  <div class="body">
    <p>Hi <?= htmlspecialchars($name ?? 'there', ENT_QUOTES, 'UTF-8') ?>,</p>
    <p>We received a request to reset the password for your <strong>The Paragon .Design CMS</strong> account.</p>
    <p>Click the button below to reset your password. This link expires in <strong>60 minutes</strong>.</p>
    <a href="<?= htmlspecialchars($reset_url ?? '', ENT_QUOTES, 'UTF-8') ?>" class="btn">Reset My Password</a>
    <p style="font-size:13px;color:#888">Or copy this link: <code style="word-break:break-all"><?= htmlspecialchars($reset_url ?? '', ENT_QUOTES, 'UTF-8') ?></code></p>
    <p>If you didn't request a password reset, you can safely ignore this email. Your password won't change.</p>
    <p style="font-size:13px;color:#888;margin-top:24px">For security, this link expires at <?= htmlspecialchars($expires_at ?? '', ENT_QUOTES, 'UTF-8') ?>.</p>
  </div>
  <div class="footer">The Paragon .Design CMS — Admin Access Only</div>
</div>
</body>
</html>
