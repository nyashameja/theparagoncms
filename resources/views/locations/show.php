<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<?php
/* Derive display copy from type to avoid implying a physical office for service areas */
$isPrimary = ($location['type'] ?? '') === 'primary';
$areaLabel = $isPrimary ? 'Based in ' . e($location['name']) : 'Serving ' . e($location['name']);
?>

<section class="page-hero section-dark">
  <div class="container">
    <h1 class="reveal">Web Design &amp; Digital Marketing <?= $isPrimary ? 'in' : 'for' ?> <?= e($location['name']) ?></h1>
    <?php if ($location['tagline'] ?? ''): ?>
    <p class="hero-sub reveal" style="animation-delay:.1s"><?= e($location['tagline']) ?></p>
    <?php else: ?>
    <p class="hero-sub reveal" style="animation-delay:.1s">Premium digital services <?= $isPrimary ? 'from our ' . e($location['name']) . ' studio' : 'delivered remotely to ' . e($location['name']) . ' businesses' ?>.</p>
    <?php endif; ?>
    <div class="mt-3 reveal" style="animation-delay:.2s">
      <a href="/get-quote" class="btn btn-primary btn-lg">Get a Free Quote</a>
    </div>
  </div>
</section>

<?php if ($location['description'] ?? ''): ?>
<section class="section">
  <div class="container" style="max-width:840px">
    <div class="rich-content reveal"><?= $location['description'] ?></div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($services)): ?>
<section class="section section-grey">
  <div class="container">
    <div class="section-header text-center reveal">
      <h2>Our Services <?= $isPrimary ? 'in ' : 'for ' ?><?= e($location['name']) ?></h2>
      <?php if (!$isPrimary): ?>
      <p class="section-sub">We work remotely with clients across <?= e($location['name']) ?> — our delivery is fully digital and just as hands-on as if we were down the road.</p>
      <?php endif; ?>
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

<section class="cta-section section section-dark">
  <div class="container text-center reveal">
    <h2 style="color:#fff">Ready to Grow Your <?= e($location['name']) ?> Business?</h2>
    <p class="section-sub" style="color:rgba(255,255,255,.7)">Get a free quote and let's discuss how we can help.</p>
    <a href="/get-quote" class="btn btn-primary btn-lg mt-3">Get a Free Quote</a>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
