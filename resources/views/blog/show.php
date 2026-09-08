<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<article>
  <!-- Hero -->
  <section class="page-hero section-dark">
    <div class="container" style="max-width:800px">
      <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li><a href="/blog">Blog</a></li><?php if ($article['category_name'] ?? ''): ?><li><a href="/blog/category/<?= e($article['category_slug'] ?? '') ?>"><?= e($article['category_name']) ?></a></li><?php endif; ?><li><?= e($article['title']) ?></li></ol></nav>
      <?php if ($article['category_name'] ?? ''): ?><span class="eyebrow" style="color:var(--accent)"><?= e($article['category_name']) ?></span><?php endif; ?>
      <h1 class="reveal"><?= e($article['title']) ?></h1>
      <div class="article-meta reveal" style="animation-delay:.1s">
        <time datetime="<?= e($article['published_at']) ?>"><?= format_date($article['published_at'], 'd M Y') ?></time>
        <?php if ($article['read_time'] ?? ''): ?><span>&bull; <?= (int)$article['read_time'] ?> min read</span><?php endif; ?>
        <?php if ($article['author_name'] ?? ''): ?><span>&bull; By <?= e($article['author_name']) ?></span><?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Featured image -->
  <?php if ($article['featured_image']): ?>
  <div class="container" style="max-width:900px;margin-top:-2rem">
    <img src="<?= e(url($article['featured_image'])) ?>" alt="<?= e($article['title']) ?>" loading="eager" style="width:100%;border-radius:12px;display:block">
  </div>
  <?php endif; ?>

  <!-- Body -->
  <div class="section">
    <div class="container" style="max-width:800px">
      <div class="rich-content reveal">
        <?= $article['content'] ?>
      </div>

      <!-- Tags -->
      <?php if (!empty($tags)): ?>
      <div class="article-tags mt-4">
        <?php foreach ($tags as $tag): ?>
        <a href="/blog/tag/<?= e($tag['slug']) ?>" class="badge badge-outline"><?= e($tag['name']) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Author card -->
      <?php if ($article['author_name'] ?? ''): ?>
      <div class="author-card mt-4 reveal">
        <?php if ($article['author_avatar'] ?? ''): ?>
        <img src="<?= e(url($article['author_avatar'])) ?>" alt="<?= e($article['author_name']) ?>" class="author-avatar" loading="lazy" width="64" height="64">
        <?php endif; ?>
        <div>
          <strong><?= e($article['author_name']) ?></strong>
          <?php if ($article['author_bio'] ?? ''): ?><p class="text-sm text-muted mt-1"><?= e($article['author_bio']) ?></p><?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- CTA -->
      <div class="cta-inline mt-5 reveal">
        <h3>Want results like these for your business?</h3>
        <p>Get a free consultation and find out how we can help you grow online.</p>
        <a href="/book-consultation" class="btn btn-primary">Book Free Consultation</a>
      </div>
    </div>
  </div>
</article>

<!-- Related articles -->
<?php if (!empty($related)): ?>
<section class="section section-grey">
  <div class="container">
    <h2 class="mb-4">Related Articles</h2>
    <div class="grid grid-3">
      <?php foreach ($related as $art): ?>
      <article class="article-card reveal">
        <?php if ($art['featured_image']): ?>
        <a href="/blog/<?= e($art['slug']) ?>" class="article-card-image" tabindex="-1" aria-hidden="true">
          <img src="<?= e(url($art['featured_image'])) ?>" alt="" loading="lazy">
        </a>
        <?php endif; ?>
        <div class="article-card-body">
          <h3><a href="/blog/<?= e($art['slug']) ?>"><?= e($art['title']) ?></a></h3>
          <time class="text-sm text-muted" datetime="<?= e($art['published_at']) ?>"><?= format_date($art['published_at'], 'd M Y') ?></time>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php \App\Support\View::endSection() ?>
