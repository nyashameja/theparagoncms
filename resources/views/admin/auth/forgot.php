<!DOCTYPE html>
<html lang="en-ZA">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password — Paragon CMS</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="auth-body">
<div class="auth-card">
    <div class="auth-logo"><div class="auth-logo-text">Paragon<span>.</span></div></div>
    <h1 class="auth-title">Forgot your password?</h1>
    <p class="auth-subtitle">We'll send you a reset link if your email is registered</p>

    <?php if (\App\Support\Session::hasFlash('success')): ?>
        <div class="flash flash-success mb-3"><?= e(\App\Support\Session::getFlash('success')) ?></div>
    <?php endif; ?>
    <?php if (\App\Support\Session::hasFlash('error')): ?>
        <div class="flash flash-error mb-3"><?= e(\App\Support\Session::getFlash('error')) ?></div>
    <?php endif; ?>

    <form method="POST" action="/admin/forgot-password">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= e(old('email', '')) ?>" autocomplete="email" autofocus required>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">Send Reset Link</button>
    </form>
    <p class="text-center mt-4 text-sm"><a href="/admin/login">← Back to sign in</a></p>
</div>
</body>
</html>
