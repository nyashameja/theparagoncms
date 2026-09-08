<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<!-- Hero -->
<section class="page-hero section-dark">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li><a href="/services">Services</a></li><li><?= e($service['name']) ?></li></ol></nav>
    <h1 class="reveal"><?= e($service['name']) ?></h1>
    <?php if ($service['short_description']): ?>
    <p class="hero-sub reveal" style="animation-delay:.1s"><?= e($service['short_description']) ?></p>
    <?php endif; ?>
    <div class="mt-3 reveal" style="animation-delay:.2s">
      <a href="/get-quote?service=<?= urlencode($service['name']) ?>" class="btn btn-primary btn-lg">Get a Quote</a>
    </div>
  </div>
</section>

<!-- Description -->
<section class="section">
  <div class="container grid grid-2" style="gap:4rem;align-items:start">
    <div class="reveal">
      <?php if ($service['description']): ?>
      <div class="rich-content"><?= $service['description'] ?></div>
      <?php endif; ?>

      <?php if ($service['features']): ?>
      <h3 class="mt-4">What's Included</h3>
      <ul class="feature-list">
        <?php foreach (array_filter(explode("\n", $service['features'])) as $feat): ?>
        <li><?= e(trim($feat)) ?></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
    <div class="reveal" style="animation-delay:.15s">
      <?php if ($service['hero_image']): ?>
      <img src="<?= e(url($service['hero_image'])) ?>" alt="<?= e($service['name']) ?>" loading="lazy" style="width:100%;border-radius:12px">
      <?php endif; ?>

      <!-- Quote sidebar card -->
      <div class="card mt-3">
        <div class="card-body text-center">
          <h3 class="mb-2">Interested in <?= e($service['name']) ?>?</h3>
          <p class="text-sm text-muted">Let's discuss how we can tailor this service to your business.</p>
          <a href="/get-quote?service=<?= urlencode($service['name']) ?>" class="btn btn-primary mt-2" style="width:100%">Get a Free Quote</a>
          <a href="/book-consultation" class="btn btn-outline mt-2" style="width:100%">Book Consultation</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Process steps -->
<?php if (!empty($processSteps)): ?>
<section class="section section-grey">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="eyebrow">How We Work</span>
      <h2>Our Process</h2>
    </div>
    <ol class="process-list mt-4">
      <?php foreach ($processSteps as $i => $step): ?>
      <li class="process-step reveal">
        <div class="process-number" aria-hidden="true"><?= $i + 1 ?></div>
        <div>
          <h3><?= e($step['title'] ?? '') ?></h3>
          <p><?= e($step['description'] ?? '') ?></p>
        </div>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
<?php endif; ?>

<!-- FAQs -->
<?php if (!empty($faqs)): ?>
<section class="section">
  <div class="container" style="max-width:720px">
    <div class="section-header text-center reveal">
      <h2>Frequently Asked Questions</h2>
    </div>
    <div class="faq-list mt-4">
      <?php foreach ($faqs as $faq): ?>
      <div class="faq-item reveal">
        <button class="faq-question" aria-expanded="false"><?= e($faq['question'] ?? '') ?><span class="faq-icon" aria-hidden="true"></span></button>
        <div class="faq-answer"><p><?= e($faq['answer'] ?? '') ?></p></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Related projects -->
<?php if (!empty($relatedProjects)): ?>
<section class="section section-grey">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="eyebrow">Our Work</span>
      <h2><?= e($service['name']) ?> Projects</h2>
    </div>
    <div class="portfolio-grid mt-4">
      <?php foreach ($relatedProjects as $proj): ?>
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

<?php \App\Support\View::endSection() ?>
