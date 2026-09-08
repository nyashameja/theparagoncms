<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li>Blog</li></ol></nav>
    <h1 class="reveal">Insights &amp; Resources</h1>
    <p class="hero-sub reveal" style="animation-delay:.1s">Digital marketing, web design tips, and business growth strategies for South African companies.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <!-- Category filter -->
    <?php if (!empty($categories)): ?>
    <div class="filter-bar mb-4" role="group" aria-label="Filter by category">
      <a href="/blog" class="filter-btn <?= empty($currentCategory) ? 'active' : '' ?>">All</a>
      <?php foreach ($categories as $cat): ?>
      <a href="/blog/category/<?= e($cat['slug']) ?>" class="filter-btn <?= ($currentCategory['slug'] ?? '') === $cat['slug'] ? 'active' : '' ?>"><?= e($cat['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($articles)): ?>
    <!-- Featured post (first) -->
    <?php $first = array_shift($articles); ?>
    <article class="article-card article-card-featured reveal mb-4">
      <?php if ($first['featured_image']): ?>
      <a href="/blog/<?= e($first['slug']) ?>" class="article-card-image" tabindex="-1" aria-hidden="true">
        <img src="<?= e(url($first['featured_image'])) ?>" alt="" loading="eager">
      </a>
      <?php endif; ?>
      <div class="article-card-body">
        <?php if ($first['category_name'] ?? ''): ?><span class="article-category"><?= e($first['category_name']) ?></span><?php endif; ?>
        <h2><a href="/blog/<?= e($first['slug']) ?>"><?= e($first['title']) ?></a></h2>
        <p><?= e($first['excerpt'] ?? '') ?></p>
        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
          <time class="text-sm text-muted" datetime="<?= e($first['published_at']) ?>"><?= format_date($first['published_at'], 'd M Y') ?></time>
          <?php if ($first['read_time'] ?? ''): ?><span class="text-sm text-muted"><?= (int)$first['read_time'] ?> min read</span><?php endif; ?>
        </div>
      </div>
    </article>

    <!-- Grid of remaining -->
    <?php if (!empty($articles)): ?>
    <div class="grid grid-3">
      <?php foreach ($articles as $art): ?>
      <article class="article-card reveal">
        <?php if ($art['featured_image']): ?>
        <a href="/blog/<?= e($art['slug']) ?>" class="article-card-image" tabindex="-1" aria-hidden="true">
          <img src="<?= e(url($art['featured_image'])) ?>" alt="" loading="lazy">
        </a>
        <?php endif; ?>
        <div class="article-card-body">
          <?php if ($art['category_name'] ?? ''): ?><span class="article-category"><?= e($art['category_name']) ?></span><?php endif; ?>
          <h3><a href="/blog/<?= e($art['slug']) ?>"><?= e($art['title']) ?></a></h3>
          <p><?= e($art['excerpt'] ?? '') ?></p>
          <time class="text-sm text-muted" datetime="<?= e($art['published_at']) ?>"><?= format_date($art['published_at'], 'd M Y') ?></time>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <p class="text-center text-muted">No articles published yet.</p>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if (!empty($pagination)): ?>
    <nav class="pagination mt-4" aria-label="Blog pages">
      <?php if ($pagination['prev']): ?><a href="?page=<?= $pagination['prev'] ?>" class="pagination-btn">&larr; Previous</a><?php endif; ?>
      <span class="pagination-info">Page <?= $pagination['current'] ?> of <?= $pagination['total'] ?></span>
      <?php if ($pagination['next']): ?><a href="?page=<?= $pagination['next'] ?>" class="pagination-btn">Next &rarr;</a><?php endif; ?>
    </nav>
    <?php endif; ?>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
