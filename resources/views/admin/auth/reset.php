<!DOCTYPE html>
<html lang="en-ZA">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password — Paragon CMS</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="auth-body">
<div class="auth-card">
    <div class="auth-logo"><div class="auth-logo-text">Paragon<span>.</span></div></div>
    <h1 class="auth-title">Set a new password</h1>
    <p class="auth-subtitle">Choose a strong password — minimum 12 characters</p>

    <?php if (\App\Support\Session::hasFlash('error')): ?>
        <div class="flash flash-error mb-3"><?= e(\App\Support\Session::getFlash('error')) ?></div>
    <?php endif; ?>

    <form method="POST" action="/admin/reset-password">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token ?? '') ?>">
        <div class="form-group">
            <label class="form-label" for="password">New Password</label>
            <input type="password" id="password" name="password" class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" autocomplete="new-password" required>
            <?php if (!empty($errors['password'])): ?><div class="form-error"><?= e($errors['password']) ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">Reset Password</button>
    </form>
</div>
</body>
</html>
