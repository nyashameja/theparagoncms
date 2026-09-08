<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container" style="max-width:760px">
    <h1 class="reveal">Book a Free Consultation</h1>
    <p class="hero-sub reveal" style="animation-delay:.1s">A free 30-minute strategy call to discuss your goals, challenges, and how we can help. No obligation.</p>
  </div>
</section>

<section class="section">
  <div class="container grid grid-2" style="gap:4rem;align-items:start">
    <div class="reveal">
      <?php if (!empty($success)): ?>
      <div class="flash flash-success mb-4" role="alert">
        <strong>Consultation requested!</strong> We'll be in touch within one business day to confirm a time.
      </div>
      <?php else: ?>

      <?php if (!empty($errors)): ?>
      <div class="flash flash-error mb-3" role="alert"><?= e(array_values($errors)[0]) ?></div>
      <?php endif; ?>

      <form method="POST" action="/book-consultation" novalidate>
        <?= csrf_field() ?>
        <div style="display:none" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
        <input type="hidden" name="hp_time" id="hp_time">

        <div class="form-group">
          <label class="form-label" for="name">Full Name <span class="required" aria-hidden="true">*</span></label>
          <input type="text" id="name" name="name" class="form-control" value="<?= e($_POST['name'] ?? '') ?>" required autocomplete="name">
        </div>
        <div class="form-group">
          <label class="form-label" for="email">Email <span class="required" aria-hidden="true">*</span></label>
          <input type="email" id="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>" required autocomplete="email">
        </div>
        <div class="form-group">
          <label class="form-label" for="phone">Phone</label>
          <input type="tel" id="phone" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>" autocomplete="tel">
        </div>
        <div class="form-group">
          <label class="form-label" for="preferred_time">Preferred Time</label>
          <select id="preferred_time" name="preferred_time" class="form-control">
            <option value="">— Select —</option>
            <option value="Morning (08:00–12:00)">Morning (08:00–12:00)</option>
            <option value="Afternoon (12:00–17:00)">Afternoon (12:00–17:00)</option>
            <option value="Flexible">Flexible</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="topic">What would you like to discuss? <span class="required" aria-hidden="true">*</span></label>
          <textarea id="topic" name="topic" class="form-control" rows="4" maxlength="1000" required><?= e($_POST['topic'] ?? '') ?></textarea>
          <div class="form-hint char-count" aria-live="polite">0 / 1000</div>
        </div>
        <div class="form-check mb-3">
          <input type="checkbox" id="consent" name="consent" class="form-check-input" value="1" required <?= !empty($_POST['consent']) ? 'checked' : '' ?>>
          <label class="form-check-label" for="consent">I consent to The Paragon .Design collecting and using my information in accordance with our <a href="/privacy-policy" target="_blank">Privacy Policy</a> and POPIA. <span class="required" aria-hidden="true">*</span></label>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">Request Consultation</button>
      </form>
      <?php endif; ?>
    </div>

    <div class="reveal" style="animation-delay:.15s">
      <div class="card">
        <div class="card-body">
          <h3>What to Expect</h3>
          <ul class="feature-list mt-2">
            <li>30-minute video or phone call</li>
            <li>Review of your current digital presence</li>
            <li>Discussion of your business goals</li>
            <li>High-level strategy recommendations</li>
            <li>No sales pressure — ever</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
