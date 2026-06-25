// ============================================================
//  assets/js/main.js — School Website JavaScript
//  Intellectitech Ntinda Hub Training Project
// ============================================================

// ── MOBILE NAVIGATION TOGGLE ────────────────────────────────
const burger = document.getElementById('burger');
const nav    = document.getElementById('main-nav');

if (burger && nav) {
    burger.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('open');
        burger.setAttribute('aria-expanded', isOpen);
    });
    // Close nav when any link is clicked (mobile)
    nav.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            nav.classList.remove('open');
            burger.setAttribute('aria-expanded', 'false');
        });
    });
}

// ── CLOSE NAV WHEN CLICKING OUTSIDE ─────────────────────────
document.addEventListener('click', (e) => {
    if (nav && !nav.contains(e.target) && !burger.contains(e.target)) {
        nav.classList.remove('open');
    }
});

// ── FADE-IN ON SCROLL ────────────────────────────────────────
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.style.opacity = '1';
            e.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.12 });

document.querySelectorAll('.card, .staff-card, .event-item, .testimonial-card').forEach(el => {
    el.style.opacity    = '0';
    el.style.transform  = 'translateY(18px)';
    el.style.transition = 'opacity .4s ease, transform .4s ease';
    observer.observe(el);
});

// ── GALLERY FULLSCREEN ON CLICK ──────────────────────────────
document.querySelectorAll('.gallery-item img').forEach(img => {
    img.addEventListener('click', () => {
        if (img.requestFullscreen) img.requestFullscreen();
    });
});

// ── TEXTAREA CHARACTER COUNTER ───────────────────────────────
document.querySelectorAll('textarea[maxlength]').forEach(ta => {
    const info = document.createElement('small');
    info.style.cssText = 'display:block;text-align:right;color:#5A6A80;margin-top:.25rem';
    ta.after(info);
    const update = () => info.textContent = `${ta.value.length} / ${ta.maxLength}`;
    ta.addEventListener('input', update);
    update();
});
