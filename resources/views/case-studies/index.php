<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li>Case Studies</li></ol></nav>
    <h1 class="reveal">Case Studies</h1>
    <p class="hero-sub reveal" style="animation-delay:.1s">Real businesses, real results. See how we've helped South African companies grow with digital.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!empty($caseStudies)): ?>
    <div class="grid grid-3">
      <?php foreach ($caseStudies as $cs): ?>
      <article class="article-card reveal">
        <?php if ($cs['featured_image']): ?>
        <a href="/case-studies/<?= e($cs['slug']) ?>" class="article-card-image" tabindex="-1" aria-hidden="true">
          <img src="<?= e(url($cs['featured_image'])) ?>" alt="<?= e($cs['title']) ?>" loading="lazy">
        </a>
        <?php endif; ?>
        <div class="article-card-body">
          <?php if ($cs['industry_name'] ?? ''): ?><span class="article-category"><?= e($cs['industry_name']) ?></span><?php endif; ?>
          <h3><a href="/case-studies/<?= e($cs['slug']) ?>"><?= e($cs['title']) ?></a></h3>
          <?php if ($cs['excerpt'] ?? ''): ?><p><?= e($cs['excerpt']) ?></p><?php endif; ?>
          <?php if ($cs['result_highlight'] ?? ''): ?>
          <p class="text-sm fw-600" style="color:var(--accent)"><?= e($cs['result_highlight']) ?></p>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center text-muted">Case studies coming soon.</p>
    <?php endif; ?>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
