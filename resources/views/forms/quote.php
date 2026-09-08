<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container" style="max-width:760px">
    <h1 class="reveal">Get a Free Quote</h1>
    <p class="hero-sub reveal" style="animation-delay:.1s">Tell us about your project and we'll send you a tailored quote within one business day.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:720px">
    <?php if (!empty($success)): ?>
    <div class="flash flash-success mb-4" role="alert">
      <strong>Quote request received!</strong> We'll review your requirements and get back to you within one business day.
    </div>
    <?php else: ?>

    <?php if (!empty($errors)): ?>
    <div class="flash flash-error mb-3" role="alert"><?= e(array_values($errors)[0]) ?></div>
    <?php endif; ?>

    <form method="POST" action="/get-quote" novalidate>
      <?= csrf_field() ?>
      <div style="display:none" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
      <input type="hidden" name="hp_time" id="hp_time">

      <div class="grid grid-2">
        <div class="form-group">
          <label class="form-label" for="name">Full Name <span class="required" aria-hidden="true">*</span></label>
          <input type="text" id="name" name="name" class="form-control" value="<?= e($_POST['name'] ?? '') ?>" required autocomplete="name">
        </div>
        <div class="form-group">
          <label class="form-label" for="email">Email <span class="required" aria-hidden="true">*</span></label>
          <input type="email" id="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>" required autocomplete="email">
        </div>
      </div>
      <div class="grid grid-2">
        <div class="form-group">
          <label class="form-label" for="phone">Phone</label>
          <input type="tel" id="phone" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>" autocomplete="tel">
        </div>
        <div class="form-group">
          <label class="form-label" for="company">Company</label>
          <input type="text" id="company" name="company" class="form-control" value="<?= e($_POST['company'] ?? '') ?>" autocomplete="organization">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="service">Service Required <span class="required" aria-hidden="true">*</span></label>
        <select id="service" name="service" class="form-control" required>
          <option value="">— Select a service —</option>
          <?php foreach ($services ?? [] as $svc): ?>
          <option value="<?= e($svc['name']) ?>" <?= (($_POST['service'] ?? '') === $svc['name']) ? 'selected' : '' ?>><?= e($svc['name']) ?></option>
          <?php endforeach; ?>
          <option value="Multiple Services">Multiple Services</option>
          <option value="Not sure yet">Not sure yet</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="budget">Approximate Budget</label>
        <select id="budget" name="budget" class="form-control">
          <option value="">— Select a range —</option>
          <option value="Under R15 000" <?= (($_POST['budget'] ?? '') === 'Under R15 000') ? 'selected' : '' ?>>Under R15 000</option>
          <option value="R15 000 – R30 000" <?= (($_POST['budget'] ?? '') === 'R15 000 – R30 000') ? 'selected' : '' ?>>R15 000 – R30 000</option>
          <option value="R30 000 – R60 000" <?= (($_POST['budget'] ?? '') === 'R30 000 – R60 000') ? 'selected' : '' ?>>R30 000 – R60 000</option>
          <option value="R60 000 – R100 000" <?= (($_POST['budget'] ?? '') === 'R60 000 – R100 000') ? 'selected' : '' ?>>R60 000 – R100 000</option>
          <option value="R100 000+" <?= (($_POST['budget'] ?? '') === 'R100 000+') ? 'selected' : '' ?>>R100 000+</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="timeline">Ideal Timeline</label>
        <select id="timeline" name="timeline" class="form-control">
          <option value="">— Select —</option>
          <option value="ASAP">ASAP</option>
          <option value="1–2 months">1–2 months</option>
          <option value="3–6 months">3–6 months</option>
          <option value="Flexible">Flexible</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="message">Project Description <span class="required" aria-hidden="true">*</span></label>
        <textarea id="message" name="message" class="form-control" rows="5" maxlength="3000" required><?= e($_POST['message'] ?? '') ?></textarea>
        <div class="form-hint char-count" aria-live="polite">0 / 3000</div>
      </div>
      <div class="form-check mb-3">
        <input type="checkbox" id="consent" name="consent" class="form-check-input" value="1" required <?= !empty($_POST['consent']) ? 'checked' : '' ?>>
        <label class="form-check-label" for="consent">I consent to The Paragon .Design collecting and using my information to respond to this quote request, per our <a href="/privacy-policy" target="_blank">Privacy Policy</a> and POPIA. <span class="required" aria-hidden="true">*</span></label>
      </div>
      <button type="submit" class="btn btn-primary btn-lg" style="width:100%">Submit Quote Request</button>
    </form>
    <?php endif; ?>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
