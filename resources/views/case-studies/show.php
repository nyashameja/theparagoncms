<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container" style="max-width:840px">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li><a href="/case-studies">Case Studies</a></li><li><?= e($caseStudy['title']) ?></li></ol></nav>
    <?php if ($caseStudy['industry_name'] ?? ''): ?><span class="eyebrow" style="color:var(--accent)"><?= e($caseStudy['industry_name']) ?></span><?php endif; ?>
    <h1 class="reveal"><?= e($caseStudy['title']) ?></h1>
    <?php if ($caseStudy['result_highlight'] ?? ''): ?>
    <p class="hero-sub reveal" style="animation-delay:.1s;color:var(--accent)"><?= e($caseStudy['result_highlight']) ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container grid grid-2" style="gap:4rem;align-items:start">
    <div>
      <?php if ($caseStudy['featured_image']): ?>
      <img src="<?= e(url($caseStudy['featured_image'])) ?>" alt="<?= e($caseStudy['title']) ?>" loading="eager" style="width:100%;border-radius:12px;margin-bottom:2rem">
      <?php endif; ?>

      <?php if ($caseStudy['challenge']): ?>
      <h2>The Challenge</h2>
      <div class="rich-content mt-2 reveal"><?= $caseStudy['challenge'] ?></div>
      <?php endif; ?>

      <?php if ($caseStudy['solution']): ?>
      <h2 class="mt-4">Our Solution</h2>
      <div class="rich-content mt-2 reveal"><?= $caseStudy['solution'] ?></div>
      <?php endif; ?>

      <?php if ($caseStudy['outcome']): ?>
      <h2 class="mt-4">The Outcome</h2>
      <div class="rich-content mt-2 reveal"><?= $caseStudy['outcome'] ?></div>
      <?php endif; ?>
    </div>

    <div>
      <div class="card reveal">
        <div class="card-body">
          <h3>Project Details</h3>
          <dl style="display:grid;grid-template-columns:110px 1fr;gap:10px 16px;margin-top:12px">
            <?php if ($caseStudy['client_name'] ?? ''): ?>
            <dt class="text-sm fw-600">Client</dt><dd class="text-sm"><?= e($caseStudy['client_name']) ?></dd>
            <?php endif; ?>
            <?php if ($caseStudy['industry_name'] ?? ''): ?>
            <dt class="text-sm fw-600">Industry</dt><dd class="text-sm"><?= e($caseStudy['industry_name']) ?></dd>
            <?php endif; ?>
            <?php if ($caseStudy['service_name'] ?? ''): ?>
            <dt class="text-sm fw-600">Service</dt><dd class="text-sm"><?= e($caseStudy['service_name']) ?></dd>
            <?php endif; ?>
          </dl>
        </div>
      </div>
      <div class="card mt-3 reveal" style="animation-delay:.1s">
        <div class="card-body text-center">
          <h3>Want Results Like This?</h3>
          <p class="text-sm text-muted">Let's talk about what we can do for your business.</p>
          <a href="/get-quote" class="btn btn-primary mt-2" style="width:100%">Get a Free Quote</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
