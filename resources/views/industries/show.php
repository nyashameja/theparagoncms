<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li><?= e($industry['name']) ?></li></ol></nav>
    <h1 class="reveal">Web Design &amp; Digital Marketing for <?= e($industry['name']) ?></h1>
    <?php if ($industry['tagline'] ?? ''): ?>
    <p class="hero-sub reveal" style="animation-delay:.1s"><?= e($industry['tagline']) ?></p>
    <?php endif; ?>
    <div class="mt-3 reveal" style="animation-delay:.2s">
      <a href="/get-quote" class="btn btn-primary btn-lg">Get a Free Quote</a>
    </div>
  </div>
</section>

<?php if ($industry['description'] ?? ''): ?>
<section class="section">
  <div class="container" style="max-width:840px">
    <div class="rich-content reveal"><?= $industry['description'] ?></div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($services)): ?>
<section class="section section-grey">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="eyebrow">Tailored Solutions</span>
      <h2>Services for the <?= e($industry['name']) ?> Sector</h2>
    </div>
    <div class="grid grid-3 mt-4">
      <?php foreach ($services as $svc): ?>
      <a href="/services/<?= e($svc['slug']) ?>" class="service-card reveal">
        <h3><?= e($svc['name']) ?></h3>
        <p><?= e($svc['short_description'] ?? '') ?></p>
        <span class="link-arrow">Learn more →</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($projects)): ?>
<section class="section">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="eyebrow">Our Work</span>
      <h2><?= e($industry['name']) ?> Projects</h2>
    </div>
    <div class="portfolio-grid mt-4">
      <?php foreach ($projects as $proj): ?>
      <a href="/portfolio/<?= e($proj['slug']) ?>" class="portfolio-item reveal">
        <?php if ($proj['featured_image']): ?>
        <img src="<?= e(url($proj['featured_image'])) ?>" alt="<?= e($proj['title']) ?>" loading="lazy">
        <?php else: ?>
        <div class="portfolio-placeholder"></div>
        <?php endif; ?>
        <div class="portfolio-overlay"><h3><?= e($proj['title']) ?></h3></div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="cta-section section section-dark">
  <div class="container text-center reveal">
    <h2 style="color:#fff">Ready to Grow Your <?= e($industry['name']) ?> Business Online?</h2>
    <p class="section-sub" style="color:rgba(255,255,255,.7)">Let's build something that generates real leads and results.</p>
    <a href="/get-quote" class="btn btn-primary btn-lg mt-3">Get a Free Quote</a>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
