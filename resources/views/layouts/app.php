<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($metaTitle ?? $title ?? setting('site_name', 'The Paragon .Design')) ?></title>
<?php if (!empty($metaDescription)): ?>
<meta name="description" content="<?= e($metaDescription) ?>">
<?php endif; ?>
<?php if (!empty($canonical)): ?>
<link rel="canonical" href="<?= e($canonical) ?>">
<?php endif; ?>
<?php if (!empty($noindex)): ?>
<meta name="robots" content="noindex,nofollow">
<?php endif; ?>
<!-- Open Graph -->
<meta property="og:type" content="<?= e($ogType ?? 'website') ?>">
<meta property="og:title" content="<?= e($metaTitle ?? $title ?? setting('site_name')) ?>">
<?php if (!empty($metaDescription)): ?>
<meta property="og:description" content="<?= e($metaDescription) ?>">
<?php endif; ?>
<?php $ogImage = $ogImage ?? setting('og_image'); if ($ogImage): ?>
<meta property="og:image" content="<?= e(url($ogImage)) ?>">
<?php endif; ?>
<meta property="og:url" content="<?= e(url($_SERVER['REQUEST_URI'])) ?>">
<meta property="og:site_name" content="<?= e(setting('site_name')) ?>">
<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<?php if ($tc = setting('twitter_handle')): ?>
<meta name="twitter:site" content="@<?= e($tc) ?>">
<?php endif; ?>
<!-- Schema JSON-LD -->
<?php if (!empty($schema)): ?>
<script type="application/ld+json"><?= $schema ?></script>
<?php endif; ?>
<!-- Favicons -->
<?php $fav = setting('favicon'); ?>
<?php if ($fav): ?><link rel="icon" href="<?= e(url($fav)) ?>"><?php else: ?><link rel="icon" href="/assets/img/favicon.ico"><?php endif; ?>
<!-- Styles -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap">
<link rel="stylesheet" href="/assets/css/app.css?v=<?= filemtime(BASE_PATH . '/public/assets/css/app.css') ?>">
<!-- Header scripts (analytics, etc.) -->
<?php $hs = setting('header_scripts'); if ($hs) echo $hs; ?>
<?php $ga = setting('ga4_id'); if ($ga): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= e($ga) ?>');</script>
<?php endif; ?>
<?php $gtm = setting('gtm_id'); if ($gtm): ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= e($gtm) ?>');</script>
<?php endif; ?>
</head>
<body>
<?php if ($gtm): ?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= e($gtm) ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php endif; ?>

<!-- Site Header -->
<header class="site-header" role="banner">
  <div class="container header-inner">
    <a href="/" class="site-logo" aria-label="<?= e(setting('site_name')) ?> — Home">
      <?php $logo = setting('logo'); ?>
      <?php if ($logo): ?>
        <img src="<?= e(url($logo)) ?>" alt="<?= e(setting('site_name')) ?>" height="40" loading="eager">
      <?php else: ?>
        <span class="logo-text"><?= e(setting('site_name', 'Paragon')) ?></span>
      <?php endif; ?>
    </a>
    <nav class="main-nav" aria-label="Main navigation">
      <ul role="list">
        <li><a href="/services"<?= str_starts_with($_SERVER['REQUEST_URI'], '/services') ? ' aria-current="page"' : '' ?>>Services</a></li>
        <li><a href="/portfolio"<?= str_starts_with($_SERVER['REQUEST_URI'], '/portfolio') ? ' aria-current="page"' : '' ?>>Portfolio</a></li>
        <li><a href="/about"<?= $_SERVER['REQUEST_URI'] === '/about' ? ' aria-current="page"' : '' ?>>About</a></li>
        <li><a href="/blog"<?= str_starts_with($_SERVER['REQUEST_URI'], '/blog') ? ' aria-current="page"' : '' ?>>Blog</a></li>
        <li><a href="/contact"<?= $_SERVER['REQUEST_URI'] === '/contact' ? ' aria-current="page"' : '' ?>>Contact</a></li>
      </ul>
    </nav>
    <a href="/get-quote" class="btn btn-primary btn-sm header-cta">Get a Quote</a>
    <button id="nav-toggle" class="nav-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-nav">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- Mobile Nav -->
<div id="mobile-nav" class="mobile-nav" role="dialog" aria-modal="true" aria-label="Mobile navigation">
  <button id="nav-close" class="nav-close" aria-label="Close menu">&times;</button>
  <nav aria-label="Mobile navigation">
    <ul role="list">
      <li><a href="/services">Services</a></li>
      <li><a href="/portfolio">Portfolio</a></li>
      <li><a href="/about">About</a></li>
      <li><a href="/blog">Blog</a></li>
      <li><a href="/contact">Contact</a></li>
      <li class="mt-2"><a href="/get-quote" class="btn btn-primary" style="display:block;text-align:center">Get a Quote</a></li>
    </ul>
  </nav>
</div>

<!-- Flash messages -->
<?php $flash = \App\Support\Session::getFlash(); ?>
<?php if ($flash): ?>
<div class="container mt-3" role="alert">
  <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
</div>
<?php endif; ?>

<!-- Main content -->
<main id="main-content">
  <?= \App\Support\View::yieldSection('content') ?>
</main>

<!-- Footer -->
<footer class="site-footer" role="contentinfo">
  <div class="container footer-grid">
    <div class="footer-brand">
      <?php if ($logo): ?>
        <img src="<?= e(url($logo)) ?>" alt="<?= e(setting('site_name')) ?>" height="36" loading="lazy">
      <?php else: ?>
        <span class="logo-text" style="color:#fff"><?= e(setting('site_name', 'Paragon')) ?></span>
      <?php endif; ?>
      <p class="footer-tagline"><?= e(setting('tagline', 'Premium digital experiences for South African businesses.')) ?></p>
      <div class="social-links">
        <?php $socials = [
          'facebook_url' => ['f','Facebook'],
          'instagram_url' => ['in','Instagram'],
          'linkedin_url' => ['li','LinkedIn'],
          'x_url' => ['x','X / Twitter'],
        ]; ?>
        <?php foreach ($socials as $key => [$icon, $label]): ?>
          <?php $u = setting($key); if ($u): ?>
          <a href="<?= e($u) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($label) ?>" class="social-link"><?= strtoupper($icon) ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="footer-col">
      <h3 class="footer-heading">Services</h3>
      <ul role="list">
        <li><a href="/services/web-design">Web Design</a></li>
        <li><a href="/services/branding">Branding</a></li>
        <li><a href="/services/seo">SEO</a></li>
        <li><a href="/services/digital-marketing">Digital Marketing</a></li>
        <li><a href="/services">All Services →</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h3 class="footer-heading">Company</h3>
      <ul role="list">
        <li><a href="/about">About Us</a></li>
        <li><a href="/portfolio">Portfolio</a></li>
        <li><a href="/case-studies">Case Studies</a></li>
        <li><a href="/blog">Blog</a></li>
        <li><a href="/contact">Contact</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h3 class="footer-heading">Get Started</h3>
      <ul role="list">
        <li><a href="/get-quote">Get a Quote</a></li>
        <li><a href="/free-audit">Free Website Audit</a></li>
        <li><a href="/book-consultation">Book Consultation</a></li>
        <li><a href="/packages">Packages &amp; Pricing</a></li>
      </ul>
      <?php $phone = setting('phone'); $email_addr = setting('contact_email'); ?>
      <?php if ($phone): ?><p class="mt-2 text-sm" style="color:var(--footer-muted)"><a href="tel:<?= e(preg_replace('/\s+/','',$phone)) ?>" style="color:inherit"><?= e($phone) ?></a></p><?php endif; ?>
      <?php if ($email_addr): ?><p class="text-sm" style="color:var(--footer-muted)"><a href="mailto:<?= e($email_addr) ?>" style="color:inherit"><?= e($email_addr) ?></a></p><?php endif; ?>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container footer-bottom-inner">
      <p>&copy; <?= date('Y') ?> <?= e(setting('site_name', 'The Paragon .Design')) ?>. All rights reserved.</p>
      <ul role="list" class="footer-legal">
        <li><a href="/privacy-policy">Privacy Policy</a></li>
        <li><a href="/terms-of-service">Terms of Service</a></li>
        <li><a href="/popia">POPIA</a></li>
      </ul>
    </div>
  </div>
</footer>

<!-- Cookie consent banner -->
<div id="cookie-banner" class="cookie-banner" hidden role="dialog" aria-live="polite" aria-label="Cookie consent">
  <div class="cookie-banner-inner">
    <p class="text-sm">We use cookies to improve your experience. By continuing, you agree to our <a href="/privacy-policy">Privacy Policy</a> and consent to cookies in terms of POPIA.</p>
    <div class="cookie-actions">
      <button id="cookie-accept" class="btn btn-primary btn-sm">Accept</button>
      <button id="cookie-decline" class="btn btn-outline btn-sm">Decline</button>
    </div>
  </div>
</div>

<!-- WhatsApp float -->
<?php $wa = setting('whatsapp_number'); if ($wa): ?>
<a href="https://wa.me/<?= e(preg_replace('/\D/','',$wa)) ?>?text=<?= rawurlencode('Hi, I\'d like to enquire about your services.') ?>"
   class="wa-float" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
  <svg viewBox="0 0 32 32" fill="currentColor" aria-hidden="true" width="28" height="28"><path d="M16 2C8.268 2 2 8.268 2 16c0 2.485.657 4.818 1.802 6.833L2 30l7.355-1.775A13.927 13.927 0 0016 30c7.732 0 14-6.268 14-14S23.732 2 16 2zm0 25.4a11.36 11.36 0 01-5.797-1.585l-.415-.247-4.364 1.054 1.083-4.254-.27-.437A11.36 11.36 0 014.6 16C4.6 9.704 9.704 4.6 16 4.6S27.4 9.704 27.4 16 22.296 27.4 16 27.4zm6.22-8.5c-.34-.17-2.014-1-2.328-1.113-.314-.113-.542-.17-.77.17s-.882 1.113-1.082 1.34c-.198.228-.397.257-.737.086-.34-.17-1.434-.528-2.73-1.685-1.01-.9-1.69-2.012-1.888-2.352-.198-.34-.021-.524.149-.693.153-.152.34-.397.51-.596.17-.198.227-.34.34-.567.114-.228.057-.426-.028-.596-.086-.17-.77-1.854-1.056-2.54-.278-.667-.56-.577-.77-.587l-.655-.011c-.228 0-.597.086-.91.426-.314.34-1.196 1.17-1.196 2.852s1.225 3.308 1.395 3.537c.17.228 2.41 3.68 5.84 5.16.816.352 1.453.562 1.95.72.82.26 1.566.224 2.156.136.658-.098 2.014-.824 2.298-1.618.284-.794.284-1.475.198-1.618-.085-.142-.313-.228-.654-.397z"/></svg>
</a>
<?php endif; ?>

<!-- Scripts -->
<script src="/assets/js/app.js?v=<?= filemtime(BASE_PATH . '/public/assets/js/app.js') ?>"></script>
<?php $fs = setting('footer_scripts'); if ($fs) echo $fs; ?>
</body>
</html>
