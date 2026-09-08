/* app.js — public website */
(function () {
  'use strict';

  /* ── Mobile nav ─────────────────────────────────────────── */
  const navToggle = document.getElementById('nav-toggle');
  const navClose  = document.getElementById('nav-close');
  const mobileNav = document.getElementById('mobile-nav');

  function openNav() {
    mobileNav.classList.add('open');
    document.body.style.overflow = 'hidden';
    navToggle.setAttribute('aria-expanded', 'true');
  }
  function closeNav() {
    mobileNav.classList.remove('open');
    document.body.style.overflow = '';
    navToggle.setAttribute('aria-expanded', 'false');
  }

  if (navToggle) navToggle.addEventListener('click', openNav);
  if (navClose)  navClose.addEventListener('click', closeNav);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && mobileNav && mobileNav.classList.contains('open')) closeNav();
  });

  /* close nav when a link is clicked */
  if (mobileNav) {
    mobileNav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', closeNav);
    });
  }

  /* ── Sticky header shadow ───────────────────────────────── */
  const header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', function () {
      header.classList.toggle('scrolled', window.scrollY > 10);
    }, { passive: true });
  }

  /* ── FAQ accordion ──────────────────────────────────────── */
  document.querySelectorAll('.faq-item').forEach(function (item) {
    const btn = item.querySelector('.faq-question');
    const body = item.querySelector('.faq-answer');
    if (!btn || !body) return;

    btn.addEventListener('click', function () {
      const open = item.classList.contains('open');
      /* collapse siblings in same list */
      const parent = item.closest('.faq-list');
      if (parent) {
        parent.querySelectorAll('.faq-item.open').forEach(function (other) {
          if (other !== item) {
            other.classList.remove('open');
            other.querySelector('.faq-answer').style.maxHeight = null;
            other.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
          }
        });
      }
      if (open) {
        item.classList.remove('open');
        body.style.maxHeight = null;
        btn.setAttribute('aria-expanded', 'false');
      } else {
        item.classList.add('open');
        body.style.maxHeight = body.scrollHeight + 'px';
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  /* ── Cookie consent banner ──────────────────────────────── */
  const cookieBanner = document.getElementById('cookie-banner');
  const cookieAccept = document.getElementById('cookie-accept');
  const cookieDecline = document.getElementById('cookie-decline');

  function setCookie(name, value, days) {
    var expires = '';
    if (days) {
      var d = new Date();
      d.setTime(d.getTime() + days * 864e5);
      expires = '; expires=' + d.toUTCString();
    }
    document.cookie = name + '=' + value + expires + '; path=/; SameSite=Lax';
  }
  function getCookie(name) {
    var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
    return v ? v[2] : null;
  }

  if (cookieBanner && !getCookie('cookie_consent')) {
    cookieBanner.hidden = false;
  }
  if (cookieAccept) {
    cookieAccept.addEventListener('click', function () {
      setCookie('cookie_consent', 'accepted', 365);
      cookieBanner.hidden = true;
    });
  }
  if (cookieDecline) {
    cookieDecline.addEventListener('click', function () {
      setCookie('cookie_consent', 'declined', 365);
      cookieBanner.hidden = true;
    });
  }

  /* ── Smooth scroll for anchor links ────────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var target = document.querySelector(a.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      var offset = header ? header.offsetHeight + 16 : 80;
      var top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top: top, behavior: 'smooth' });
    });
  });

  /* ── Testimonial slider (simple CSS-driven) ─────────────── */
  var sliderTrack = document.querySelector('.testimonial-track');
  if (sliderTrack) {
    var slides = sliderTrack.querySelectorAll('.testimonial-card');
    var total = slides.length;
    var current = 0;
    var prevBtn = document.querySelector('.slider-prev');
    var nextBtn = document.querySelector('.slider-next');
    var dotsContainer = document.querySelector('.slider-dots');

    /* build dots */
    if (dotsContainer && total > 1) {
      for (var i = 0; i < total; i++) {
        var dot = document.createElement('button');
        dot.setAttribute('aria-label', 'Slide ' + (i + 1));
        dot.className = 'slider-dot' + (i === 0 ? ' active' : '');
        (function (idx) {
          dot.addEventListener('click', function () { goTo(idx); });
        })(i);
        dotsContainer.appendChild(dot);
      }
    }

    function goTo(idx) {
      current = (idx + total) % total;
      sliderTrack.style.transform = 'translateX(-' + (current * 100) + '%)';
      if (dotsContainer) {
        dotsContainer.querySelectorAll('.slider-dot').forEach(function (d, i) {
          d.classList.toggle('active', i === current);
        });
      }
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });

    /* auto-advance */
    if (total > 1) {
      setInterval(function () { goTo(current + 1); }, 6000);
    }

    /* touch swipe */
    var touchStartX = 0;
    sliderTrack.addEventListener('touchstart', function (e) {
      touchStartX = e.touches[0].clientX;
    }, { passive: true });
    sliderTrack.addEventListener('touchend', function (e) {
      var diff = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) goTo(diff > 0 ? current + 1 : current - 1);
    });
  }

  /* ── Counter animation ───────────────────────────────────── */
  function animateCounter(el) {
    var target = parseFloat(el.dataset.target || el.textContent.replace(/[^\d.]/g, ''));
    var suffix = el.dataset.suffix || el.textContent.replace(/[\d.]/g, '');
    var start = 0;
    var duration = 1800;
    var startTime = null;

    function step(ts) {
      if (!startTime) startTime = ts;
      var progress = Math.min((ts - startTime) / duration, 1);
      var val = start + (target - start) * easeOut(progress);
      el.textContent = (Number.isInteger(target) ? Math.round(val) : val.toFixed(1)) + suffix;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  function easeOut(t) { return 1 - Math.pow(1 - t, 3); }

  /* observe stats row */
  if ('IntersectionObserver' in window) {
    var statsObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.querySelectorAll('[data-counter]').forEach(animateCounter);
          statsObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });

    document.querySelectorAll('.stats-row').forEach(function (el) {
      statsObserver.observe(el);
    });
  }

  /* ── Reveal on scroll ────────────────────────────────────── */
  if ('IntersectionObserver' in window) {
    var revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal').forEach(function (el) {
      revealObserver.observe(el);
    });
  }

  /* ── Portfolio filter ───────────────────────────────────── */
  var filterBtns = document.querySelectorAll('.filter-btn');
  if (filterBtns.length) {
    filterBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var filter = btn.dataset.filter;
        filterBtns.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        document.querySelectorAll('.portfolio-item').forEach(function (item) {
          if (filter === 'all' || item.dataset.category === filter) {
            item.style.display = '';
          } else {
            item.style.display = 'none';
          }
        });
      });
    });
  }

  /* ── Contact / quote / audit form enhancements ──────────── */
  /* Honeypot: set timestamp on page load for minimum time check */
  var hpTime = document.querySelector('input[name="hp_time"]');
  if (hpTime) hpTime.value = Date.now();

  /* Show/hide conditional fields */
  document.querySelectorAll('[data-shows]').forEach(function (trigger) {
    trigger.addEventListener('change', function () {
      var targetId = trigger.dataset.shows;
      var target = document.getElementById(targetId);
      if (!target) return;
      var show = trigger.type === 'checkbox' ? trigger.checked : trigger.value === trigger.dataset.showsValue;
      target.hidden = !show;
      target.querySelectorAll('input,select,textarea').forEach(function (f) {
        if (show) f.setAttribute('required', '');
        else f.removeAttribute('required');
      });
    });
  });

  /* Character counter for textareas */
  document.querySelectorAll('textarea[maxlength]').forEach(function (ta) {
    var counter = ta.nextElementSibling;
    if (!counter || !counter.classList.contains('char-count')) return;
    ta.addEventListener('input', function () {
      counter.textContent = ta.value.length + ' / ' + ta.getAttribute('maxlength');
    });
  });

  /* ── Flash message auto-dismiss ─────────────────────────── */
  document.querySelectorAll('.flash').forEach(function (el) {
    setTimeout(function () {
      el.style.opacity = '0';
      el.style.transition = 'opacity .4s';
      setTimeout(function () { el.hidden = true; }, 400);
    }, 5000);
  });

  /* ── Service tabs (on services page) ────────────────────── */
  document.querySelectorAll('.tab-list[role="tablist"]').forEach(function (list) {
    var tabs = list.querySelectorAll('[role="tab"]');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var panelId = tab.getAttribute('aria-controls');
        tabs.forEach(function (t) {
          t.setAttribute('aria-selected', 'false');
          t.classList.remove('active');
        });
        tab.setAttribute('aria-selected', 'true');
        tab.classList.add('active');
        var container = list.closest('.tabs-wrapper') || document;
        container.querySelectorAll('[role="tabpanel"]').forEach(function (p) {
          p.hidden = p.id !== panelId;
        });
      });
    });
  });

  /* ── Back to top button ─────────────────────────────────── */
  var btt = document.getElementById('back-to-top');
  if (btt) {
    window.addEventListener('scroll', function () {
      btt.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });
    btt.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ── WhatsApp float ─────────────────────────────────────── */
  var waFloat = document.querySelector('.wa-float');
  if (waFloat) {
    /* Show after 3 s */
    setTimeout(function () { waFloat.classList.add('visible'); }, 3000);
  }

  /* ── Video play (lazy) ──────────────────────────────────── */
  document.querySelectorAll('.video-embed[data-src]').forEach(function (el) {
    el.addEventListener('click', function () {
      var iframe = document.createElement('iframe');
      iframe.src = el.dataset.src + (el.dataset.src.includes('?') ? '&' : '?') + 'autoplay=1';
      iframe.allow = 'autoplay; fullscreen';
      iframe.allowFullscreen = true;
      iframe.className = el.className;
      el.parentNode.replaceChild(iframe, el);
    });
  });

})();
