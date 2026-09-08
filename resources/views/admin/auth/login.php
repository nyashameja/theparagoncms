<!DOCTYPE html>
<html lang="en-ZA">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — Paragon CMS</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="auth-body">
<div class="auth-card">
    <div class="auth-logo">
        <?php if (!empty($brand['logo_dark'])): ?>
            <img src="<?= e($brand['logo_dark']) ?>" alt="<?= e($brand['name'] ?? 'Paragon') ?>">
        <?php else: ?>
            <div class="auth-logo-text">Paragon<span>.</span></div>
        <?php endif; ?>
    </div>
    <h1 class="auth-title">Sign in to your account</h1>
    <p class="auth-subtitle">Admin dashboard access only</p>

    <?php if (\App\Support\Session::hasFlash('error')): ?>
        <div class="flash flash-error mb-3"><?= e(\App\Support\Session::getFlash('error')) ?></div>
    <?php endif; ?>

    <form method="POST" action="/admin/login">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input type="email" id="email" name="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                   value="<?= e(old('email', '')) ?>" autocomplete="email" autofocus required>
            <?php if (!empty($errors['email'])): ?>
                <div class="form-error"><?= e($errors['email']) ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                   autocomplete="current-password" required>
            <?php if (!empty($errors['password'])): ?>
                <div class="form-error"><?= e($errors['password']) ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" id="remember" name="remember" class="form-check-input" value="1">
                <label for="remember" class="form-check-label">Remember me</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">Sign In</button>
    </form>

    <div class="text-center mt-4">
        <a href="/admin/forgot-password" class="text-sm text-muted">Forgot your password?</a>
    </div>
</div>
</body>
</html>
