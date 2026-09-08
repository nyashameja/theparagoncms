<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container">
    <h1 class="reveal">Packages &amp; Pricing</h1>
    <p class="hero-sub reveal" style="animation-delay:.1s">Transparent pricing for premium digital services. Every package is customisable to your exact needs.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!empty($packages)): ?>
    <div class="packages-grid">
      <?php foreach ($packages as $pkg): ?>
      <div class="package-card <?= ($pkg['is_featured'] ?? false) ? 'package-card-featured' : '' ?> reveal">
        <?php if ($pkg['is_featured'] ?? false): ?><div class="package-badge">Most Popular</div><?php endif; ?>
        <div class="package-header">
          <h3><?= e($pkg['name']) ?></h3>
          <?php if ($pkg['description'] ?? ''): ?><p class="text-sm text-muted"><?= e($pkg['description']) ?></p><?php endif; ?>
        </div>
        <div class="package-price">
          <?php if ($pkg['custom_quote'] ?? false): ?>
          <span class="price-value">Custom Quote</span>
          <?php else: ?>
          <span class="price-currency">R</span>
          <span class="price-value"><?= number_format((float)($pkg['price'] ?? 0)) ?></span>
          <?php if ($pkg['billing_type'] ?? ''): ?><span class="price-period">/<?= e($pkg['billing_type']) ?></span><?php endif; ?>
          <?php endif; ?>
        </div>
        <?php if ($pkg['included_items'] ?? ''): ?>
        <?php $items = is_array($pkg['included_items']) ? $pkg['included_items'] : (json_decode($pkg['included_items'], true) ?? []); ?>
        <?php if (!empty($items)): ?>
        <ul class="package-features">
          <?php foreach ($items as $item): ?>
          <li><?= e(trim($item)) ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php endif; ?>
        <div class="package-cta">
          <?php if ($pkg['custom_quote'] ?? false): ?>
          <a href="/get-quote?package=<?= urlencode($pkg['name']) ?>" class="btn <?= ($pkg['is_featured'] ?? false) ? 'btn-primary' : 'btn-outline' ?>" style="width:100%">Get a Quote</a>
          <?php else: ?>
          <a href="/get-quote?package=<?= urlencode($pkg['name']) ?>" class="btn <?= ($pkg['is_featured'] ?? false) ? 'btn-primary' : 'btn-outline' ?>" style="width:100%">Get Started</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center text-muted">Package information coming soon. <a href="/contact">Contact us</a> for pricing.</p>
    <?php endif; ?>

    <p class="text-center text-sm text-muted mt-4">All prices exclude VAT. Custom solutions available for enterprise clients. <a href="/contact">Contact us</a> to discuss your specific requirements.</p>
  </div>
</section>

<section class="cta-section section section-grey">
  <div class="container text-center reveal">
    <h2>Not sure which package is right for you?</h2>
    <p class="section-sub">Book a free consultation and we'll recommend the best fit for your goals and budget.</p>
    <a href="/book-consultation" class="btn btn-primary btn-lg mt-3">Book Free Consultation</a>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
