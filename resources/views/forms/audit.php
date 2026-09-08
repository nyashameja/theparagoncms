<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container" style="max-width:760px">
    <h1 class="reveal">Free Website Audit</h1>
    <p class="hero-sub reveal" style="animation-delay:.1s">We'll analyse your website's performance, SEO, mobile usability, accessibility, and security — completely free.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:680px">

    <?php if (!empty($success)): ?>
    <div class="flash flash-success mb-4" role="alert">
      <strong>Request received!</strong> We'll run your audit and get back to you within 2 business days.
    </div>
    <?php else: ?>

    <?php if (!empty($errors)): ?>
    <div class="flash flash-error mb-3" role="alert"><?= e(array_values($errors)[0]) ?></div>
    <?php endif; ?>

    <!-- What you'll get -->
    <div class="card mb-4">
      <div class="card-body">
        <h3 class="mb-2">What Your Audit Covers</h3>
        <ul class="feature-list">
          <li><strong>Performance</strong> — Page speed, Core Web Vitals, image optimisation</li>
          <li><strong>SEO</strong> — On-page SEO, meta tags, schema markup, crawlability</li>
          <li><strong>Mobile</strong> — Mobile responsiveness and usability</li>
          <li><strong>Accessibility</strong> — WCAG 2.2 AA compliance basics</li>
          <li><strong>Security</strong> — HTTPS, headers, common vulnerabilities</li>
        </ul>
        <p class="text-sm text-muted mt-2">Results delivered via email within 2 business days. No obligation — ever.</p>
      </div>
    </div>

    <form method="POST" action="/free-audit" novalidate>
      <?= csrf_field() ?>
      <div style="display:none" aria-hidden="true">
        <input type="text" name="website_hp" tabindex="-1" autocomplete="off">
      </div>
      <input type="hidden" name="hp_time" id="hp_time">

      <div class="form-group">
        <label class="form-label" for="name">Your Name <span class="required" aria-hidden="true">*</span></label>
        <input type="text" id="name" name="name" class="form-control" value="<?= e($_POST['name'] ?? '') ?>" required autocomplete="name">
      </div>
      <div class="form-group">
        <label class="form-label" for="email">Email Address <span class="required" aria-hidden="true">*</span></label>
        <input type="email" id="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>" required autocomplete="email">
      </div>
      <div class="form-group">
        <label class="form-label" for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>" autocomplete="tel">
      </div>
      <div class="form-group">
        <label class="form-label" for="company">Company / Business Name</label>
        <input type="text" id="company" name="company" class="form-control" value="<?= e($_POST['company'] ?? '') ?>" autocomplete="organization">
      </div>
      <div class="form-group">
        <label class="form-label" for="website_url">Website URL <span class="required" aria-hidden="true">*</span></label>
        <input type="url" id="website_url" name="website_url" class="form-control" value="<?= e($_POST['website_url'] ?? '') ?>" placeholder="https://yourbusiness.co.za" required>
        <div class="form-hint">Full URL including https://</div>
      </div>
      <div class="form-group">
        <label class="form-label" for="notes">Anything specific you'd like us to look at?</label>
        <textarea id="notes" name="notes" class="form-control" rows="3" maxlength="1000"><?= e($_POST['notes'] ?? '') ?></textarea>
        <div class="form-hint char-count" aria-live="polite">0 / 1000</div>
      </div>
      <div class="form-check mb-3">
        <input type="checkbox" id="consent" name="consent" class="form-check-input" value="1" required <?= !empty($_POST['consent']) ? 'checked' : '' ?>>
        <label class="form-check-label" for="consent">I consent to The Paragon .Design collecting and using my information for this audit request, in accordance with our <a href="/privacy-policy" target="_blank">Privacy Policy</a> and POPIA. <span class="required" aria-hidden="true">*</span></label>
      </div>
      <button type="submit" class="btn btn-primary btn-lg" style="width:100%">Request Free Audit</button>
    </form>
    <?php endif; ?>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
