<?php
/**
 * Installer — runs once, then locks itself.
 * Access is blocked after successful setup via .htaccess rule or lock file.
 */

define('INSTALLER_VERSION', '1.0.0');
define('LOCK_FILE', __DIR__ . '/.installed');
define('ENV_FILE', dirname(__DIR__) . '/.env');

/* ── Already installed? ─────────────────────────────────── */
if (file_exists(LOCK_FILE)) {
    http_response_code(403);
    die('<h1>403 Forbidden</h1><p>The installer has already been used and is now locked. Delete <code>installer/.installed</code> only if you need to re-run setup.</p>');
}

session_start();

$step   = (int)($_GET['step'] ?? 1);
$errors = [];

/* ── POST handlers ──────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* CSRF */
    if (empty($_POST['_token']) || $_POST['_token'] !== ($_SESSION['install_token'] ?? '')) {
        die('Invalid token. Please go back and try again.');
    }

    if ($step === 1) {
        /* Requirements checked — advance */
        header('Location: ?step=2');
        exit;
    }

    if ($step === 2) {
        /* Write .env */
        $fields = [
            'APP_NAME'      => trim($_POST['APP_NAME']      ?? 'The Paragon Design'),
            'APP_URL'       => rtrim(trim($_POST['APP_URL'] ?? 'http://localhost'), '/'),
            'APP_ENV'       => 'production',
            'APP_DEBUG'     => 'false',
            'APP_KEY'       => 'base64:' . base64_encode(random_bytes(32)),
            'DB_HOST'       => trim($_POST['DB_HOST']       ?? '127.0.0.1'),
            'DB_PORT'       => trim($_POST['DB_PORT']        ?? '3306'),
            'DB_DATABASE'   => trim($_POST['DB_DATABASE']   ?? ''),
            'DB_USERNAME'   => trim($_POST['DB_USERNAME']   ?? ''),
            'DB_PASSWORD'   => $_POST['DB_PASSWORD']        ?? '',
            'MAIL_HOST'     => trim($_POST['MAIL_HOST']     ?? ''),
            'MAIL_PORT'     => trim($_POST['MAIL_PORT']     ?? '587'),
            'MAIL_USERNAME' => trim($_POST['MAIL_USERNAME'] ?? ''),
            'MAIL_PASSWORD' => $_POST['MAIL_PASSWORD']      ?? '',
            'MAIL_FROM'     => trim($_POST['MAIL_FROM']     ?? ''),
            'MAIL_FROM_NAME'=> trim($_POST['MAIL_FROM_NAME'] ?? $fields['APP_NAME'] ?? 'The Paragon Design'),
        ];

        foreach (['DB_DATABASE', 'DB_USERNAME', 'APP_URL'] as $required) {
            if (!$fields[$required]) $errors[$required] = $required . ' is required.';
        }

        if (!$errors) {
            $envContent = '';
            foreach ($fields as $k => $v) {
                $needsQuote = str_contains($v, ' ') || str_contains($v, '#');
                $envContent .= $k . '=' . ($needsQuote ? '"' . addslashes($v) . '"' : $v) . "\n";
            }
            if (file_put_contents(ENV_FILE, $envContent) === false) {
                $errors['env'] = 'Could not write .env file. Check directory permissions.';
            }
        }

        if (!$errors) {
            header('Location: ?step=3');
            exit;
        }
    }

    if ($step === 3) {
        /* Run migrations */
        require_once dirname(__DIR__) . '/database/migrations/run.php';
        header('Location: ?step=4');
        exit;
    }

    if ($step === 4) {
        /* Create admin user */
        $name  = trim($_POST['admin_name']  ?? '');
        $email = trim($_POST['admin_email'] ?? '');
        $pass  = $_POST['admin_password']   ?? '';
        $conf  = $_POST['admin_password_confirmation'] ?? '';

        if (!$name)  $errors['admin_name']  = 'Name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['admin_email'] = 'Valid email required.';
        if (strlen($pass) < 12) $errors['admin_password'] = 'Password must be at least 12 characters.';
        if ($pass !== $conf)    $errors['admin_password'] = 'Passwords do not match.';

        if (!$errors) {
            /* Load env then PDO */
            $env = parse_ini_file(ENV_FILE);
            try {
                $pdo = new PDO(
                    'mysql:host=' . $env['DB_HOST'] . ';port=' . ($env['DB_PORT'] ?? 3306) . ';dbname=' . $env['DB_DATABASE'] . ';charset=utf8mb4',
                    $env['DB_USERNAME'], $env['DB_PASSWORD'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, status, created_at, updated_at) VALUES (?,?,?,'active',NOW(),NOW())");
                $stmt->execute([$name, $email, $hash]);
                $userId = (int)$pdo->lastInsertId();
                /* Assign admin role */
                $role = $pdo->query("SELECT id FROM roles WHERE name='admin' LIMIT 1")->fetch();
                if ($role) {
                    $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?,?) ON DUPLICATE KEY UPDATE role_id=role_id")->execute([$userId, $role['id']]);
                }
            } catch (Exception $e) {
                $errors['db'] = 'Database error: ' . $e->getMessage();
            }
        }

        if (!$errors) {
            /* Lock installer */
            file_put_contents(LOCK_FILE, date('c'));
            header('Location: ?step=5');
            exit;
        }
    }
}

/* ── CSRF token ─────────────────────────────────────────── */
if (empty($_SESSION['install_token'])) {
    $_SESSION['install_token'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['install_token'];

/* ── Requirements check (step 1) ───────────────────────── */
$requirements = [
    'PHP 8.2+' => version_compare(PHP_VERSION, '8.2.0', '>='),
    'PDO MySQL' => extension_loaded('pdo_mysql'),
    'OpenSSL'   => extension_loaded('openssl'),
    'Mbstring'  => extension_loaded('mbstring'),
    'FileInfo'  => extension_loaded('fileinfo'),
    '.env writable (parent dir)' => is_writable(dirname(__DIR__)),
];
$allPassed = !in_array(false, $requirements, true);

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Paragon CMS Installer — Step <?= $step ?></title>
<style>
*{box-sizing:border-box;margin:0;padding:0}body{font-family:system-ui,sans-serif;background:#f0f0f0;color:#222;padding:2rem}
.wrap{max-width:640px;margin:0 auto}
.card{background:#fff;border-radius:10px;padding:2rem;box-shadow:0 2px 12px rgba(0,0,0,.1);margin-top:1.5rem}
h1{font-size:1.6rem;margin-bottom:.5rem}h2{font-size:1.1rem;margin-bottom:1rem;color:#555}
.logo{font-size:1.4rem;font-weight:800;color:#D71920;margin-bottom:1rem}
.req-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #eee;font-size:.9rem}
.pass{color:#22c55e;font-weight:700}.fail{color:#ef4444;font-weight:700}
.form-group{margin-bottom:1rem}label{display:block;font-size:.85rem;font-weight:600;margin-bottom:4px}
input[type=text],input[type=email],input[type=password],input[type=url]{width:100%;padding:10px 12px;border:1.5px solid #d0d0d0;border-radius:6px;font-size:.95rem}
.btn{display:inline-block;background:#D71920;color:#fff;padding:12px 28px;border-radius:6px;border:none;font-size:1rem;font-weight:600;cursor:pointer;width:100%;margin-top:.5rem}
.error{color:#ef4444;font-size:.85rem;margin-top:4px}
.steps{display:flex;gap:.5rem;margin-bottom:1.5rem}
.step{flex:1;height:4px;border-radius:2px;background:#e0e0e0}.step.done{background:#D71920}
.success{color:#22c55e;font-size:1.1rem;margin-bottom:1rem}
</style>
</head>
<body>
<div class="wrap">
  <div class="logo">Paragon .Design CMS Installer</div>
  <div class="steps">
    <?php for($i=1;$i<=5;$i++): ?><div class="step <?= $i<=$step?'done':'' ?>"></div><?php endfor; ?>
  </div>

  <?php if ($step === 1): ?>
  <div class="card">
    <h1>Step 1 — Requirements</h1>
    <h2>Checking server requirements</h2>
    <?php foreach ($requirements as $label => $pass): ?>
    <div class="req-row"><span><?= htmlspecialchars($label) ?></span><span class="<?= $pass?'pass':'fail' ?>"><?= $pass?'✓ Pass':'✗ Fail' ?></span></div>
    <?php endforeach; ?>
    <form method="POST" action="?step=1" style="margin-top:1.5rem">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
      <?php if (!$allPassed): ?>
      <p style="color:#ef4444;margin-bottom:1rem">Fix the failing requirements before continuing.</p>
      <?php endif; ?>
      <button type="submit" class="btn" <?= $allPassed?'':'disabled' ?>>Continue →</button>
    </form>
  </div>

  <?php elseif ($step === 2): ?>
  <div class="card">
    <h1>Step 2 — Configuration</h1>
    <h2>Application &amp; database settings</h2>
    <?php if ($errors): ?><p class="error"><?= htmlspecialchars(array_values($errors)[0]) ?></p><?php endif; ?>
    <form method="POST" action="?step=2">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
      <div class="form-group"><label>Site Name</label><input type="text" name="APP_NAME" value="<?= htmlspecialchars($_POST['APP_NAME'] ?? 'The Paragon .Design') ?>"></div>
      <div class="form-group"><label>Site URL</label><input type="url" name="APP_URL" value="<?= htmlspecialchars($_POST['APP_URL'] ?? 'https://') ?>" required></div>
      <hr style="margin:1rem 0;border:none;border-top:1px solid #eee">
      <div class="form-group"><label>DB Host</label><input type="text" name="DB_HOST" value="<?= htmlspecialchars($_POST['DB_HOST'] ?? '127.0.0.1') ?>"></div>
      <div class="form-group"><label>DB Port</label><input type="text" name="DB_PORT" value="<?= htmlspecialchars($_POST['DB_PORT'] ?? '3306') ?>"></div>
      <div class="form-group"><label>DB Name</label><input type="text" name="DB_DATABASE" value="<?= htmlspecialchars($_POST['DB_DATABASE'] ?? '') ?>" required></div>
      <div class="form-group"><label>DB Username</label><input type="text" name="DB_USERNAME" value="<?= htmlspecialchars($_POST['DB_USERNAME'] ?? '') ?>" required></div>
      <div class="form-group"><label>DB Password</label><input type="password" name="DB_PASSWORD" autocomplete="new-password"></div>
      <hr style="margin:1rem 0;border:none;border-top:1px solid #eee">
      <div class="form-group"><label>SMTP Host (optional)</label><input type="text" name="MAIL_HOST" value="<?= htmlspecialchars($_POST['MAIL_HOST'] ?? '') ?>"></div>
      <div class="form-group"><label>SMTP Port</label><input type="text" name="MAIL_PORT" value="<?= htmlspecialchars($_POST['MAIL_PORT'] ?? '587') ?>"></div>
      <div class="form-group"><label>SMTP Username</label><input type="text" name="MAIL_USERNAME" value="<?= htmlspecialchars($_POST['MAIL_USERNAME'] ?? '') ?>"></div>
      <div class="form-group"><label>SMTP Password</label><input type="password" name="MAIL_PASSWORD" autocomplete="new-password"></div>
      <div class="form-group"><label>From Email</label><input type="email" name="MAIL_FROM" value="<?= htmlspecialchars($_POST['MAIL_FROM'] ?? '') ?>"></div>
      <div class="form-group"><label>From Name</label><input type="text" name="MAIL_FROM_NAME" value="<?= htmlspecialchars($_POST['MAIL_FROM_NAME'] ?? '') ?>"></div>
      <button type="submit" class="btn">Continue →</button>
    </form>
  </div>

  <?php elseif ($step === 3): ?>
  <div class="card">
    <h1>Step 3 — Database</h1>
    <h2>Run migrations to create all tables</h2>
    <form method="POST" action="?step=3">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
      <p style="margin-bottom:1rem">This will create all required tables. Existing tables are skipped.</p>
      <button type="submit" class="btn">Run Migrations →</button>
    </form>
  </div>

  <?php elseif ($step === 4): ?>
  <div class="card">
    <h1>Step 4 — Admin Account</h1>
    <h2>Create your administrator login</h2>
    <?php if ($errors): ?><p class="error"><?= htmlspecialchars(array_values($errors)[0]) ?></p><?php endif; ?>
    <form method="POST" action="?step=4">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
      <div class="form-group"><label>Full Name</label><input type="text" name="admin_name" value="<?= htmlspecialchars($_POST['admin_name'] ?? '') ?>" required></div>
      <div class="form-group"><label>Email Address</label><input type="email" name="admin_email" value="<?= htmlspecialchars($_POST['admin_email'] ?? '') ?>" required></div>
      <div class="form-group"><label>Password (min 12 chars)</label><input type="password" name="admin_password" autocomplete="new-password" required minlength="12"></div>
      <div class="form-group"><label>Confirm Password</label><input type="password" name="admin_password_confirmation" autocomplete="new-password" required></div>
      <button type="submit" class="btn">Create Account &amp; Finish →</button>
    </form>
  </div>

  <?php elseif ($step === 5): ?>
  <div class="card" style="text-align:center">
    <div class="success" style="font-size:3rem">✓</div>
    <h1 style="margin-bottom:1rem">Installation Complete!</h1>
    <p>Your Paragon CMS is ready. The installer is now locked.</p>
    <p style="margin-top:1rem;font-size:.9rem;color:#888">Delete the <code>installer/</code> directory from your server for maximum security.</p>
    <a href="/admin" style="display:block;background:#D71920;color:#fff;padding:14px;border-radius:6px;text-decoration:none;font-weight:700;margin-top:1.5rem">Go to Admin Dashboard →</a>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
