<!DOCTYPE html>
<html lang="en-ZA">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Two-Factor Authentication — Paragon CMS</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="auth-body">
<div class="auth-card">
    <div class="auth-logo">
        <div class="auth-logo-text">Paragon<span>.</span></div>
    </div>
    <h1 class="auth-title">Two-Factor Authentication</h1>
    <p class="auth-subtitle">Enter the 6-digit code from your authenticator app</p>

    <?php if (\App\Support\Session::hasFlash('error')): ?>
        <div class="flash flash-error mb-3"><?= e(\App\Support\Session::getFlash('error')) ?></div>
    <?php endif; ?>

    <form method="POST" action="/admin/2fa">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="code">Authentication Code</label>
            <input type="text" id="code" name="code" class="form-control <?= !empty($errors['code']) ? 'is-invalid' : '' ?>"
                   inputmode="numeric" pattern="[0-9\s-]{6,}" autocomplete="one-time-code" autofocus
                   placeholder="000000" maxlength="10" style="font-size:20px;letter-spacing:4px;text-align:center">
            <?php if (!empty($errors['code'])): ?>
                <div class="form-error"><?= e($errors['code']) ?></div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">Verify</button>
    </form>

    <p class="text-center mt-4 text-sm text-muted">
        Lost your device? <a href="/admin/2fa?recovery=1">Use a recovery code</a>
    </p>
    <p class="text-center mt-2 text-sm">
        <a href="/admin/logout">← Sign out</a>
    </p>
</div>
</body>
</html>
