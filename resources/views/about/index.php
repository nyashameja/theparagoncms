<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li>About</li></ol></nav>
    <h1 class="reveal">About The Paragon .Design</h1>
    <p class="hero-sub reveal" style="animation-delay:.1s">We're a South African digital agency on a mission to build websites and brands that generate real business results.</p>
  </div>
</section>

<!-- Mission / story -->
<section class="section">
  <div class="container grid grid-2" style="align-items:center;gap:4rem">
    <div class="reveal">
      <span class="eyebrow">Our Story</span>
      <h2>Built on the Belief That Design Should Pay for Itself</h2>
      <p>The Paragon .Design was founded with a simple conviction: a beautiful website that doesn't convert is a liability, not an asset. We combine strategic thinking, premium aesthetics, and performance obsession to deliver digital experiences that look world-class and work harder than any salesperson.</p>
      <p class="mt-2">Based in South Africa, we understand the unique challenges and opportunities facing local businesses — from Cape Town startups to Johannesburg enterprises.</p>
    </div>
    <div class="reveal" style="animation-delay:.15s">
      <?php $aboutImg = setting('about_image'); ?>
      <?php if ($aboutImg): ?>
      <img src="<?= e(url($aboutImg)) ?>" alt="The Paragon Design team" class="img-rounded" loading="lazy" style="width:100%;border-radius:12px">
      <?php else: ?>
      <div style="background:var(--grey-100);border-radius:12px;height:360px;display:flex;align-items:center;justify-content:center;color:var(--grey-400)">Team Photo</div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Values -->
<section class="section section-grey">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="eyebrow">What We Stand For</span>
      <h2>Our Values</h2>
    </div>
    <div class="grid grid-4 mt-4">
      <?php foreach ([
        ['icon'=>'◆','title'=>'Excellence First','desc'=>'We don\'t ship work we wouldn\'t be proud to show off. Every pixel, every line of code.'],
        ['icon'=>'◎','title'=>'Transparent Communication','desc'=>'No jargon, no surprises. You\'ll always know where your project stands.'],
        ['icon'=>'▲','title'=>'Results-Driven','desc'=>'We measure success in leads, conversions, and revenue — not just aesthetics.'],
        ['icon'=>'✦','title'=>'Long-Term Partnership','desc'=>'We build relationships, not just websites. Most of our clients have been with us for years.'],
      ] as $v): ?>
      <div class="service-card reveal text-center">
        <div class="service-icon" style="font-size:2rem" aria-hidden="true"><?= $v['icon'] ?></div>
        <h3><?= e($v['title']) ?></h3>
        <p><?= e($v['desc']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Stats -->
<section class="section">
  <div class="container stats-row">
    <?php foreach ([
      ['value'=>'150+','label'=>'Projects Delivered','target'=>150,'suffix'=>'+'],
      ['value'=>'8+','label'=>'Years in Business','target'=>8,'suffix'=>'+'],
      ['value'=>'98%','label'=>'Client Satisfaction','target'=>98,'suffix'=>'%'],
      ['value'=>'40+','label'=>'Industries','target'=>40,'suffix'=>'+'],
    ] as $s): ?>
    <div class="stat-item text-center reveal">
      <div class="stat-value" data-counter data-target="<?= $s['target'] ?>" data-suffix="<?= $s['suffix'] ?>"><?= $s['value'] ?></div>
      <div class="stat-label"><?= e($s['label']) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Team -->
<?php if (!empty($team)): ?>
<section class="section section-grey">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="eyebrow">The People</span>
      <h2>Meet the Team</h2>
    </div>
    <div class="grid grid-4 mt-4">
      <?php foreach ($team as $member): ?>
      <div class="team-card text-center reveal">
        <?php if ($member['avatar']): ?>
        <img src="<?= e(url($member['avatar'])) ?>" alt="<?= e($member['name']) ?>" class="team-avatar" loading="lazy" width="120" height="120">
        <?php endif; ?>
        <h3><?= e($member['name']) ?></h3>
        <p class="text-sm text-muted"><?= e($member['role']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-section section section-dark">
  <div class="container text-center reveal">
    <h2 style="color:#fff">Ready to Work Together?</h2>
    <p class="section-sub" style="color:rgba(255,255,255,.7)">Let's talk about your project and see if we're the right fit.</p>
    <div class="mt-3" style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="/contact" class="btn btn-primary btn-lg">Get in Touch</a>
      <a href="/portfolio" class="btn btn-outline-white btn-lg">See Our Work</a>
    </div>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
