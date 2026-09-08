<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li><a href="/blog">Blog</a></li><li><?= e($category['name']) ?></li></ol></nav>
    <h1 class="reveal"><?= e($category['name']) ?></h1>
    <?php if ($category['description'] ?? ''): ?>
    <p class="hero-sub reveal" style="animation-delay:.1s"><?= e($category['description']) ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
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
          <h3><a href="/blog/<?= e($art['slug']) ?>"><?= e($art['title']) ?></a></h3>
          <p><?= e($art['excerpt'] ?? '') ?></p>
          <time class="text-sm text-muted" datetime="<?= e($art['published_at']) ?>"><?= format_date($art['published_at'], 'd M Y') ?></time>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center text-muted">No articles in this category yet.</p>
    <?php endif; ?>

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
