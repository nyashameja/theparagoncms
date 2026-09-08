<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 — Page Not Found</title>
<meta name="robots" content="noindex">
<link rel="stylesheet" href="/assets/css/app.css">
<style>
.error-page{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem}
.error-code{font-size:clamp(6rem,15vw,10rem);font-weight:800;line-height:1;color:var(--accent);margin-bottom:1rem}
</style>
</head>
<body>
<main class="error-page" id="main-content">
  <div class="error-code" aria-hidden="true">404</div>
  <h1>Page Not Found</h1>
  <p class="text-muted mt-2" style="max-width:480px">The page you're looking for doesn't exist or has been moved. Double-check the URL or head back home.</p>
  <div style="display:flex;gap:1rem;flex-wrap:wrap;justify-content:center;margin-top:2rem">
    <a href="/" class="btn btn-primary">Go to Homepage</a>
    <a href="/contact" class="btn btn-outline">Contact Us</a>
  </div>
</main>
</body>
</html>
