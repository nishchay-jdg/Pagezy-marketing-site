// ── Nav scroll effect
const nav = document.getElementById('nav');
if (nav) {
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 20);
    }, { passive: true });
}

// ── Mobile nav
function mobileNavOpen() {
    document.getElementById('mobile-overlay').style.display = 'block';
    document.getElementById('mobile-drawer').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function mobileNavClose() {
    document.getElementById('mobile-overlay').style.display = 'none';
    document.getElementById('mobile-drawer').classList.remove('open');
    document.body.style.overflow = '';
}

// ── Fade-up on scroll
const fadeEls = document.querySelectorAll('.fade-up');
if (fadeEls.length) {
    const obs = new IntersectionObserver((entries) => {
        entries.forEach((e, i) => {
            if (e.isIntersecting) {
                setTimeout(() => e.target.classList.add('visible'), i * 80);
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });
    fadeEls.forEach(el => obs.observe(el));
}

// ── Pricing toggle (annual/monthly)
const pricingToggle = document.getElementById('pricing-toggle');
if (pricingToggle) {
    pricingToggle.addEventListener('change', () => {
        const isAnnual = pricingToggle.checked;
        document.querySelectorAll('[data-monthly]').forEach(el => {
            const m = el.dataset.monthly;
            const a = el.dataset.annual;
            el.textContent = isAnnual ? a : m;
        });
        document.querySelectorAll('.period-label').forEach(el => {
            el.textContent = isAnnual ? '/yr' : '/mo';
        });
    });
}

// ── Counter animation
function animateCounter(el, target, suffix = '') {
    let current = 0;
    const step = target / 60;
    const timer = setInterval(() => {
        current += step;
        if (current >= target) { current = target; clearInterval(timer); }
        el.textContent = Math.floor(current).toLocaleString() + suffix;
    }, 16);
}

const counters = document.querySelectorAll('[data-counter]');
if (counters.length) {
    const obs2 = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const el = e.target;
                animateCounter(el, +el.dataset.counter, el.dataset.suffix || '');
                obs2.unobserve(el);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(el => obs2.observe(el));
}
