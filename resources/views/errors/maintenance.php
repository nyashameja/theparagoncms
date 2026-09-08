<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Maintenance — Back Soon</title>
<meta name="robots" content="noindex">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',system-ui,sans-serif;background:#0a0a0a;color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:2rem}
.logo{font-size:1.75rem;font-weight:800;color:#D71920;margin-bottom:2rem}
h1{font-size:clamp(1.5rem,4vw,2.5rem);margin-bottom:1rem}
p{color:rgba(255,255,255,.6);max-width:480px;line-height:1.6;margin:0 auto .5rem}
</style>
</head>
<body>
<main>
  <div class="logo">Paragon .Design</div>
  <h1>We'll Be Back Shortly</h1>
  <p>We're performing scheduled maintenance to improve your experience. We'll be back online shortly.</p>
  <?php if (!empty($message)): ?><p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
</main>
</body>
</html>
