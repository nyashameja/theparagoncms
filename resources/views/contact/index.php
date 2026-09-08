<?php \App\Support\View::extend('layouts/app') ?>
<?php \App\Support\View::section('content') ?>

<section class="page-hero section-dark">
  <div class="container">
    <h1 class="reveal">Get in Touch</h1>
    <p class="hero-sub reveal" style="animation-delay:.1s">We'd love to hear about your project. Fill in the form and we'll get back to you within one business day.</p>
  </div>
</section>

<section class="section">
  <div class="container grid grid-2" style="gap:4rem;align-items:start">

    <!-- Contact form -->
    <div class="reveal">
      <?php if (!empty($success)): ?>
      <div class="flash flash-success mb-3" role="alert">Thank you — we'll be in touch shortly!</div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
      <div class="flash flash-error mb-3" role="alert"><?= e(array_values($errors)[0]) ?></div>
      <?php endif; ?>

      <form method="POST" action="/contact" novalidate>
        <?= csrf_field() ?>
        <!-- Honeypot -->
        <div style="display:none" aria-hidden="true">
          <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>
        <input type="hidden" name="hp_time" id="hp_time">

        <div class="form-group">
          <label class="form-label" for="name">Full Name <span class="required" aria-hidden="true">*</span></label>
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
          <label class="form-label" for="company">Company / Business</label>
          <input type="text" id="company" name="company" class="form-control" value="<?= e($_POST['company'] ?? '') ?>" autocomplete="organization">
        </div>
        <div class="form-group">
          <label class="form-label" for="service">I'm interested in</label>
          <select id="service" name="service" class="form-control">
            <option value="">— Select a service —</option>
            <?php foreach ($services ?? [] as $svc): ?>
            <option value="<?= e($svc['name']) ?>" <?= (($_POST['service'] ?? '') === $svc['name']) ? 'selected' : '' ?>><?= e($svc['name']) ?></option>
            <?php endforeach; ?>
            <option value="General Enquiry">General Enquiry</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="message">Message <span class="required" aria-hidden="true">*</span></label>
          <textarea id="message" name="message" class="form-control" rows="5" maxlength="2000" required><?= e($_POST['message'] ?? '') ?></textarea>
          <div class="form-hint char-count" aria-live="polite">0 / 2000</div>
        </div>
        <!-- POPIA consent -->
        <div class="form-check mb-3">
          <input type="checkbox" id="consent" name="consent" class="form-check-input" value="1" required <?= !empty($_POST['consent']) ? 'checked' : '' ?>>
          <label class="form-check-label" for="consent">I consent to The Paragon .Design collecting and using my information to respond to this enquiry, in accordance with our <a href="/privacy-policy" target="_blank">Privacy Policy</a> and POPIA. <span class="required" aria-hidden="true">*</span></label>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">Send Message</button>
      </form>
    </div>

    <!-- Contact info sidebar -->
    <div class="reveal" style="animation-delay:.15s">
      <div class="card mb-3">
        <div class="card-body">
          <h3>Contact Details</h3>
          <?php $phone = setting('phone'); $email_addr = setting('contact_email'); $address = setting('address'); ?>
          <?php if ($phone): ?><p class="text-sm mt-2"><strong>Phone:</strong> <a href="tel:<?= e(preg_replace('/\s+/','',$phone)) ?>"><?= e($phone) ?></a></p><?php endif; ?>
          <?php if ($email_addr): ?><p class="text-sm"><strong>Email:</strong> <a href="mailto:<?= e($email_addr) ?>"><?= e($email_addr) ?></a></p><?php endif; ?>
          <?php if ($address): ?><p class="text-sm"><strong>Address:</strong> <?= nl2br(e($address)) ?></p><?php endif; ?>
          <p class="text-sm mt-2"><strong>Business Hours:</strong><br><?= nl2br(e(setting('business_hours', 'Mon–Fri, 08:00–17:00 SAST'))) ?></p>
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-body text-center">
          <h3>Prefer a Quick Chat?</h3>
          <p class="text-sm text-muted">Book a free 30-minute strategy call.</p>
          <a href="/book-consultation" class="btn btn-outline mt-2" style="width:100%">Book Consultation</a>
        </div>
      </div>
      <?php $wa = setting('whatsapp_number'); if ($wa): ?>
      <div class="card">
        <div class="card-body text-center">
          <h3>WhatsApp Us</h3>
          <p class="text-sm text-muted">For quick questions, message us on WhatsApp.</p>
          <a href="https://wa.me/<?= e(preg_replace('/\D/','',$wa)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-2" style="width:100%;background:#25D366;border-color:#25D366">Chat on WhatsApp</a>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php \App\Support\View::endSection() ?>
