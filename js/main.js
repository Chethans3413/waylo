/* =========================================================
   WAYLO — Main JavaScript
   Handles: scroll animations, navbar, mobile menu, contact form
   ========================================================= */

// ── Scroll reveal (replaces Framer Motion) ─────────────────────────────────
(function () {
  const reveals = document.querySelectorAll('.reveal');

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const delay = parseFloat(el.dataset.delay || 0);
          setTimeout(() => {
            el.classList.add('visible');
          }, delay * 1000);
          observer.unobserve(el);
        }
      });
    },
    { threshold: 0.12, rootMargin: '-60px 0px' }
  );

  reveals.forEach((el) => observer.observe(el));
})();

// ── Navbar: scroll effect ───────────────────────────────────────────────────
(function () {
  const navbar = document.querySelector('.navbar');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 60) {
      navbar.style.background = 'rgba(10,25,15,0.92)';
      navbar.style.backdropFilter = 'blur(12px)';
      navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.4)';
    } else {
      navbar.style.background = '';
      navbar.style.backdropFilter = '';
      navbar.style.boxShadow = '';
    }
  }, { passive: true });
})();

// ── Mobile menu ─────────────────────────────────────────────────────────────
(function () {
  const hamburger = document.getElementById('nav-hamburger');
  const mobileMenu = document.getElementById('mobile-menu');
  const closeBtn = document.getElementById('mobile-menu-close');
  const mobileLinks = document.querySelectorAll('.mobile-menu__link');

  function openMenu() {
    mobileMenu.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    mobileMenu.classList.remove('open');
    document.body.style.overflow = '';
  }

  hamburger.addEventListener('click', openMenu);
  closeBtn.addEventListener('click', closeMenu);
  mobileLinks.forEach((link) => link.addEventListener('click', closeMenu));
})();

// ── Contact form submission ─────────────────────────────────────────────────
(function () {
  const form = document.getElementById('contact-form');
  const success = document.getElementById('contact-success');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const data = {
      name:      form.querySelector('[name="name"]').value,
      email:     form.querySelector('[name="email"]').value,
      pickup:    form.querySelector('[name="pickup"]').value,
      delivery:  form.querySelector('[name="delivery"]').value,
      cargoType: form.querySelector('[name="cargoType"]').value,
      weight:    form.querySelector('[name="weight"]').value,
      date:      form.querySelector('[name="date"]').value,
      message:   form.querySelector('[name="message"]').value,
    };

    try {
      await fetch('/api/quote.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
      });
    } catch (_) {
      // Show success even if offline
    }

    form.style.display = 'none';
    success.style.display = 'block';
  });
})();

// ── Smooth scroll for anchor links ─────────────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach((link) => {
  link.addEventListener('click', (e) => {
    const target = document.querySelector(link.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// ── Polaroid subtle hover parallax ─────────────────────────────────────────
(function () {
  const polaroids = document.querySelectorAll('.grp__polaroid');
  polaroids.forEach((p) => {
    p.addEventListener('mousemove', (e) => {
      const rect = p.getBoundingClientRect();
      const x = (e.clientX - rect.left - rect.width / 2) / rect.width;
      const y = (e.clientY - rect.top - rect.height / 2) / rect.height;
      p.style.transform = `rotate(0deg) scale(1.05) rotateY(${x * 10}deg) rotateX(${-y * 10}deg)`;
    });
    p.addEventListener('mouseleave', () => {
      p.style.transform = `rotate(var(--rot)) scale(1)`;
    });
  });
})();
