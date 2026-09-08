<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li>Services</li></ol></nav>
    <h1 class="reveal">Our Services</h1>
    <p class="hero-sub reveal" style="animation-delay:.1s">End-to-end digital solutions — from brand identity to high-performance websites and growth marketing.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!empty($services)): ?>
    <div class="grid grid-3">
      <?php foreach ($services as $svc): ?>
      <a href="/services/<?= e($svc['slug']) ?>" class="service-card reveal">
        <?php if ($svc['icon']): ?><div class="service-icon" aria-hidden="true"><?= $svc['icon'] ?></div><?php endif; ?>
        <h3><?= e($svc['name']) ?></h3>
        <p><?= e($svc['short_description'] ?? '') ?></p>
        <span class="link-arrow">Explore service →</span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-muted text-center">Services coming soon.</p>
    <?php endif; ?>
  </div>
</section>

<section class="cta-section section section-grey">
  <div class="container text-center reveal">
    <h2>Not sure which service you need?</h2>
    <p class="section-sub">Book a free 30-minute consultation and we'll map out exactly what your business needs to grow online.</p>
    <a href="/book-consultation" class="btn btn-primary btn-lg mt-3">Book Free Consultation</a>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
