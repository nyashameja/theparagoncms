<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li><a href="/portfolio">Portfolio</a></li><li><?= e($project['title']) ?></li></ol></nav>
    <h1 class="reveal"><?= e($project['title']) ?></h1>
    <?php if ($project['service_name'] ?? ''): ?>
    <p class="hero-sub reveal" style="animation-delay:.1s"><?= e($project['service_name']) ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-2" style="gap:4rem;align-items:start">
      <!-- Main content -->
      <div>
        <?php if ($project['featured_image']): ?>
        <img src="<?= e(url($project['featured_image'])) ?>" alt="<?= e($project['title']) ?>" loading="eager" style="width:100%;border-radius:12px;margin-bottom:2rem">
        <?php endif; ?>

        <?php if ($project['description']): ?>
        <div class="rich-content reveal"><?= $project['description'] ?></div>
        <?php endif; ?>

        <?php if (!empty($results)): ?>
        <h3 class="mt-4">Results</h3>
        <div class="stats-row mt-2" style="justify-content:flex-start;gap:2rem">
          <?php foreach ($results as $r): ?>
          <div class="stat-item">
            <div class="stat-value"><?= e($r['value'] ?? '') ?></div>
            <div class="stat-label"><?= e($r['label'] ?? '') ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Sidebar -->
      <div>
        <div class="card reveal">
          <div class="card-body">
            <dl style="display:grid;grid-template-columns:120px 1fr;gap:12px 16px">
              <?php if ($project['client_name'] && !($project['is_anonymised'] ?? false)): ?>
              <dt class="fw-600 text-sm">Client</dt><dd class="text-sm"><?= e($project['client_name']) ?></dd>
              <?php endif; ?>
              <?php if ($project['industry_name'] ?? ''): ?>
              <dt class="fw-600 text-sm">Industry</dt><dd class="text-sm"><?= e($project['industry_name']) ?></dd>
              <?php endif; ?>
              <?php if ($project['completion_date']): ?>
              <dt class="fw-600 text-sm">Completed</dt><dd class="text-sm"><?= format_date($project['completion_date'], 'M Y') ?></dd>
              <?php endif; ?>
              <?php if ($project['project_url']): ?>
              <dt class="fw-600 text-sm">Website</dt><dd class="text-sm"><a href="<?= e($project['project_url']) ?>" target="_blank" rel="noopener noreferrer">Visit Site →</a></dd>
              <?php endif; ?>
            </dl>
          </div>
        </div>
        <div class="card mt-3 reveal" style="animation-delay:.1s">
          <div class="card-body text-center">
            <h3 class="mb-2">Like What You See?</h3>
            <p class="text-sm text-muted">Let's build something this good for your business.</p>
            <a href="/get-quote" class="btn btn-primary mt-2" style="width:100%">Get a Quote</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Related projects -->
    <?php if (!empty($related)): ?>
    <div class="mt-5">
      <h2 class="mb-3">More Projects</h2>
      <div class="portfolio-grid">
        <?php foreach ($related as $r): ?>
        <a href="/portfolio/<?= e($r['slug']) ?>" class="portfolio-item reveal">
          <?php if ($r['featured_image']): ?>
          <img src="<?= e(url($r['featured_image'])) ?>" alt="<?= e($r['title']) ?>" loading="lazy">
          <?php else: ?>
          <div class="portfolio-placeholder"></div>
          <?php endif; ?>
          <div class="portfolio-overlay"><h3><?= e($r['title']) ?></h3></div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
