<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<!-- Hero -->
<section class="hero-section">
  <div class="container hero-inner">
    <div class="hero-content reveal">
      <span class="eyebrow">South Africa's Premium Digital Agency</span>
      <h1><?= e(setting('hero_headline', 'We Build Digital Experiences That Convert')) ?></h1>
      <p class="hero-sub"><?= e(setting('hero_subheadline', 'Web design, branding, and digital marketing for ambitious South African businesses ready to grow.')) ?></p>
      <div class="hero-actions">
        <a href="/get-quote" class="btn btn-primary btn-lg">Get a Free Quote</a>
        <a href="/portfolio" class="btn btn-outline-white btn-lg">View Our Work</a>
      </div>
    </div>
    <?php $heroImg = setting('hero_image'); ?>
    <?php if ($heroImg): ?>
    <div class="hero-media reveal" style="animation-delay:.15s">
      <img src="<?= e(url($heroImg)) ?>" alt="Premium digital design work" loading="eager" width="640" height="480">
    </div>
    <?php endif; ?>
  </div>
  <div class="hero-badge-strip">
    <div class="container">
      <?php foreach (['POPIA Compliant','WCAG 2.2 AA','Mobile-First','South Africa Based'] as $b): ?>
      <span class="badge badge-outline-white"><?= e($b) ?></span>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Services overview -->
<?php if (!empty($services)): ?>
<section class="section">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="eyebrow">What We Do</span>
      <h2>Services Built Around Your Goals</h2>
      <p class="section-sub">From brand identity to high-performance websites and targeted digital marketing campaigns.</p>
    </div>
    <div class="grid grid-3 mt-4">
      <?php foreach ($services as $svc): ?>
      <a href="/services/<?= e($svc['slug']) ?>" class="service-card reveal">
        <?php if ($svc['icon']): ?><div class="service-icon" aria-hidden="true"><?= $svc['icon'] ?></div><?php endif; ?>
        <h3><?= e($svc['name']) ?></h3>
        <p><?= e($svc['short_description'] ?? '') ?></p>
        <span class="link-arrow">Learn more →</span>
      </a>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4"><a href="/services" class="btn btn-outline">View All Services</a></div>
  </div>
</section>
<?php endif; ?>

<!-- Stats -->
<section class="stats-section section-grey">
  <div class="container stats-row">
    <?php foreach ([
      ['value'=>'150+','label'=>'Projects Delivered','target'=>150,'suffix'=>'+'],
      ['value'=>'8+','label'=>'Years Experience','target'=>8,'suffix'=>'+'],
      ['value'=>'98%','label'=>'Client Satisfaction','target'=>98,'suffix'=>'%'],
      ['value'=>'40+','label'=>'Industries Served','target'=>40,'suffix'=>'+'],
    ] as $stat): ?>
    <div class="stat-item text-center reveal">
      <div class="stat-value" data-counter data-target="<?= $stat['target'] ?>" data-suffix="<?= $stat['suffix'] ?>"><?= $stat['value'] ?></div>
      <div class="stat-label"><?= e($stat['label']) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Portfolio preview -->
<?php if (!empty($projects)): ?>
<section class="section">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="eyebrow">Our Work</span>
      <h2>Recent Projects</h2>
    </div>
    <div class="portfolio-grid mt-4">
      <?php foreach ($projects as $proj): ?>
      <a href="/portfolio/<?= e($proj['slug']) ?>" class="portfolio-item reveal" data-category="<?= e($proj['category'] ?? '') ?>">
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
    <div class="text-center mt-4"><a href="/portfolio" class="btn btn-outline">View Full Portfolio</a></div>
  </div>
</section>
<?php endif; ?>

<!-- Testimonials -->
<?php if (!empty($testimonials)): ?>
<section class="section section-dark">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="eyebrow" style="color:var(--accent)">What Clients Say</span>
      <h2 style="color:#fff">Trusted by South African Businesses</h2>
    </div>
    <div class="testimonial-slider mt-4">
      <div class="testimonial-track">
        <?php foreach ($testimonials as $t): ?>
        <div class="testimonial-card">
          <blockquote>
            <p><?= e($t['quote']) ?></p>
          </blockquote>
          <footer class="testimonial-footer">
            <?php if ($t['avatar']): ?>
            <img src="<?= e(url($t['avatar'])) ?>" alt="<?= e($t['client_name']) ?>" class="testimonial-avatar" loading="lazy" width="48" height="48">
            <?php endif; ?>
            <div>
              <strong><?= e($t['client_name']) ?></strong>
              <?php if ($t['client_title'] || $t['company']): ?>
              <span class="text-muted"><?= e(implode(', ', array_filter([$t['client_title'], $t['company']]))) ?></span>
              <?php endif; ?>
            </div>
          </footer>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (count($testimonials) > 1): ?>
      <div class="slider-controls">
        <button class="slider-prev" aria-label="Previous">&#8592;</button>
        <div class="slider-dots" aria-label="Testimonial navigation"></div>
        <button class="slider-next" aria-label="Next">&#8594;</button>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Free audit CTA -->
<section class="cta-section section">
  <div class="container text-center reveal">
    <span class="eyebrow">Free Offer</span>
    <h2>Get Your Free Website Audit</h2>
    <p class="section-sub">Discover what's holding your website back. We'll analyse performance, SEO, mobile usability, and security — at no cost.</p>
    <a href="/free-audit" class="btn btn-primary btn-lg mt-3">Request Free Audit</a>
  </div>
</section>

<!-- Recent blog posts -->
<?php if (!empty($articles)): ?>
<section class="section section-grey">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="eyebrow">Insights</span>
      <h2>From Our Blog</h2>
    </div>
    <div class="grid grid-3 mt-4">
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
    <div class="text-center mt-4"><a href="/blog" class="btn btn-outline">Read More Articles</a></div>
  </div>
</section>
<?php endif; ?>

<?php \App\Support\View::endSection() ?>
