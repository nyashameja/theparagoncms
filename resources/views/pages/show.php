<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<?php if ($page['show_hero'] ?? true): ?>
<section class="page-hero section-dark">
  <div class="container">
    <?php if ($page['parent_title'] ?? ''): ?>
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li><?= e($page['title']) ?></li></ol></nav>
    <?php endif; ?>
    <h1 class="reveal"><?= e($page['hero_headline'] ?: $page['title']) ?></h1>
    <?php if ($page['hero_subheadline'] ?? ''): ?>
    <p class="hero-sub reveal" style="animation-delay:.1s"><?= e($page['hero_subheadline']) ?></p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<section class="section">
  <div class="container<?= ($page['layout'] ?? 'wide') === 'narrow' ? '' : '' ?>" style="<?= ($page['layout'] ?? '') === 'narrow' ? 'max-width:800px' : '' ?>">
    <div class="rich-content reveal">
      <?= $page['content'] ?>
    </div>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
