/* ============================================================
   js/script.js — Nature-Drop
   Bubbles · Ripple effects · Language toggle · Navbar scroll
   ============================================================ */

'use strict';

/* ── 1. AMBIENT BUBBLES ── */
function initBubbles() {
  const container = document.getElementById('bubbles-container');
  if (!container) return;

  const COUNT = 18;

  for (let i = 0; i < COUNT; i++) {
    spawnBubble(container, true);
  }

  // Continuously spawn new ones
  setInterval(() => spawnBubble(container, false), 1200);
}

function spawnBubble(container, randomStart) {
  const el = document.createElement('div');
  el.className = 'bubble';

  const size     = Math.random() * 24 + 6;        // 6–30 px
  const leftPct  = Math.random() * 100;
  const duration = Math.random() * 14 + 8;         // 8–22s
  const delay    = randomStart ? -(Math.random() * duration) : 0;
  const drift    = (Math.random() - 0.5) * 120 + 'px';

  el.style.cssText = `
    width: ${size}px;
    height: ${size}px;
    left: ${leftPct}%;
    animation-duration: ${duration}s;
    animation-delay: ${delay}s;
    --drift: ${drift};
    opacity: 0;
  `;

  container.appendChild(el);

  // Remove after animation ends (keep DOM clean)
  const lifespan = (duration + Math.abs(delay)) * 1000 + 1000;
  setTimeout(() => {
    if (el.parentNode) el.parentNode.removeChild(el);
    // Spawn a replacement
    spawnBubble(container, false);
  }, lifespan);
}

/* ── 2. NAVBAR SCROLL ── */
function initNavbar() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;

  const onScroll = () => {
    navbar.classList.toggle('scrolled', window.scrollY > 40);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

/* ── 3. HAMBURGER MENU ── */
function initHamburger() {
  const btn   = document.getElementById('hamburger');
  const links = document.getElementById('navLinks');
  if (!btn || !links) return;

  btn.addEventListener('click', () => {
    const open = links.classList.toggle('open');
    btn.classList.toggle('open', open);
    btn.setAttribute('aria-expanded', open);
  });

  // Close on outside click
  document.addEventListener('click', (e) => {
    if (!btn.contains(e.target) && !links.contains(e.target)) {
      links.classList.remove('open');
      btn.classList.remove('open');
    }
  });
}

/* ── 4. RIPPLE EFFECT on buttons ── */
function initRipples() {
  document.querySelectorAll('.btn').forEach(btn => {
    btn.classList.add('ripple-container');
    btn.addEventListener('click', function(e) {
      const rect   = btn.getBoundingClientRect();
      const size   = Math.max(rect.width, rect.height) * 1.5;
      const x      = e.clientX - rect.left - size / 2;
      const y      = e.clientY - rect.top  - size / 2;

      const ripple = document.createElement('span');
      ripple.className = 'ripple';
      ripple.style.cssText = `
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
      `;
      btn.appendChild(ripple);
      setTimeout(() => ripple.remove(), 700);
    });
  });
}

/* ── 5. LANGUAGE TOGGLE (EN / Gujarati) ── */
function initLanguageToggle() {
  const toggle = document.getElementById('langToggle');
  if (!toggle) return;

  const html = document.documentElement;
  let lang   = localStorage.getItem('nd_lang') || 'en';

  function applyLang(l) {
    lang = l;
    html.setAttribute('data-lang', l);
    localStorage.setItem('nd_lang', l);

    // Swap text for all [data-en] / [data-gu] elements
    document.querySelectorAll('[data-en]').forEach(el => {
      const text = el.getAttribute('data-' + l);
      if (text) el.textContent = text;
    });

    // Update toggle label
    const label = document.getElementById('langLabel');
    if (label) label.textContent = l === 'en' ? 'EN / ગુ' : 'ગુ / EN';
  }

  toggle.addEventListener('click', () => {
    applyLang(lang === 'en' ? 'gu' : 'en');
  });

  // Apply saved preference on load
  applyLang(lang);
}

/* ── 6. SCROLL REVEAL (lightweight, no lib) ── */
function initScrollReveal() {
  const targets = document.querySelectorAll('.card, .feature-card, .supply-card, .product-card, .stat-card, .team-card, .testimonial-card');

  if (!('IntersectionObserver' in window)) {
    // Fallback: just show everything
    targets.forEach(t => t.style.opacity = '1');
    return;
  }

  targets.forEach(t => {
    t.style.opacity    = '0';
    t.style.transform  = 'translateY(30px)';
    t.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
  });

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => {
          entry.target.style.opacity   = '1';
          entry.target.style.transform = 'translateY(0)';
        }, i * 60);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  targets.forEach(t => observer.observe(t));
}

/* ── 7. WATER RIPPLE on hero click ── */
function initHeroRipple() {
  const hero = document.querySelector('.hero');
  if (!hero) return;

  hero.addEventListener('click', (e) => {
    const circle = document.createElement('div');
    circle.style.cssText = `
      position: absolute;
      border-radius: 50%;
      border: 2px solid rgba(92,225,200,0.5);
      width: 10px;
      height: 10px;
      left: ${e.clientX - hero.getBoundingClientRect().left - 5}px;
      top:  ${e.clientY - hero.getBoundingClientRect().top  - 5}px;
      pointer-events: none;
      animation: heroRipple 1.2s ease-out forwards;
    `;
    hero.appendChild(circle);
    setTimeout(() => circle.remove(), 1300);
  });

  // Inject keyframe once
  if (!document.getElementById('hero-ripple-kf')) {
    const style = document.createElement('style');
    style.id = 'hero-ripple-kf';
    style.textContent = `
      @keyframes heroRipple {
        to { transform: scale(30); opacity: 0; }
      }
    `;
    document.head.appendChild(style);
  }
}

/* ── 8. FORM VALIDATION FEEDBACK ── */
function initFormValidation() {
  document.querySelectorAll('form.validate').forEach(form => {
    form.addEventListener('submit', (e) => {
      let valid = true;
      form.querySelectorAll('[required]').forEach(input => {
        if (!input.value.trim()) {
          input.classList.add('error');
          valid = false;
        } else {
          input.classList.remove('error');
        }
      });
      if (!valid) {
        e.preventDefault();
        const errMsg = form.querySelector('.alert-error');
        if (errMsg) {
          errMsg.textContent = 'Please fill in all required fields.';
          errMsg.style.display = 'block';
        }
      }
    });

    // Remove error class on input
    form.querySelectorAll('input, textarea').forEach(input => {
      input.addEventListener('input', () => input.classList.remove('error'));
    });
  });
}

/* ── 9. COUNTER ANIMATION for stats ── */
function initCounters() {
  const counters = document.querySelectorAll('[data-count]');
  if (!counters.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el     = entry.target;
      const target = parseInt(el.getAttribute('data-count'), 10);
      const suffix = el.getAttribute('data-suffix') || '';
      const dur    = 1800;
      const step   = 16;
      const inc    = target / (dur / step);
      let   val    = 0;

      const tick = () => {
        val = Math.min(val + inc, target);
        el.textContent = Math.round(val).toLocaleString() + suffix;
        if (val < target) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
      observer.unobserve(el);
    });
  }, { threshold: 0.5 });

  counters.forEach(c => observer.observe(c));
}

/* ── INIT ── */
document.addEventListener('DOMContentLoaded', () => {
  initBubbles();
  initNavbar();
  initHamburger();
  initRipples();
  initLanguageToggle();
  initScrollReveal();
  initHeroRipple();
  initFormValidation();
  initCounters();
});