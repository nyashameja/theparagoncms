<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li>Portfolio</li></ol></nav>
    <h1 class="reveal">Our Portfolio</h1>
    <p class="hero-sub reveal" style="animation-delay:.1s">A selection of projects we're proud of — and results we're even prouder of.</p>
  </div>
</section>

<!-- Filter bar -->
<?php if (!empty($categories)): ?>
<div class="section" style="padding-block:2rem">
  <div class="container">
    <div class="filter-bar" role="group" aria-label="Filter by service">
      <button class="filter-btn active" data-filter="all">All</button>
      <?php foreach ($categories as $cat): ?>
      <button class="filter-btn" data-filter="<?= e($cat) ?>"><?= e($cat) ?></button>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<section class="section" style="padding-top:0">
  <div class="container">
    <?php if (!empty($projects)): ?>
    <div class="portfolio-grid">
      <?php foreach ($projects as $proj): ?>
      <a href="/portfolio/<?= e($proj['slug']) ?>" class="portfolio-item reveal" data-category="<?= e($proj['service_name'] ?? '') ?>">
        <?php if ($proj['featured_image']): ?>
        <img src="<?= e(url($proj['featured_image'])) ?>" alt="<?= e($proj['title']) ?>" loading="lazy">
        <?php else: ?>
        <div class="portfolio-placeholder"></div>
        <?php endif; ?>
        <div class="portfolio-overlay">
          <h3><?= e($proj['title']) ?></h3>
          <?php if ($proj['service_name'] ?? ''): ?><p><?= e($proj['service_name']) ?></p><?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center text-muted">No projects to show yet.</p>
    <?php endif; ?>

    <?php if (!empty($pagination)): ?>
    <nav class="pagination mt-4" aria-label="Portfolio pages">
      <?php if ($pagination['prev']): ?><a href="?page=<?= $pagination['prev'] ?>" class="pagination-btn">&larr; Previous</a><?php endif; ?>
      <span class="pagination-info">Page <?= $pagination['current'] ?> of <?= $pagination['total'] ?></span>
      <?php if ($pagination['next']): ?><a href="?page=<?= $pagination['next'] ?>" class="pagination-btn">Next &rarr;</a><?php endif; ?>
    </nav>
    <?php endif; ?>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
