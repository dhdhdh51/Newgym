/**
 * Premium Gym Website - Main JavaScript
 * Handles all client-side interactivity for the public site.
 * State-class contract matches assets/css/style.css.
 */

document.addEventListener('DOMContentLoaded', function () {

    'use strict';

    var body = document.body;

    // ========================================
    // Sticky Header (#siteHeader -> .scrolled)
    // ========================================
    var siteHeader = document.getElementById('siteHeader');
    var scrollTopBtn = document.getElementById('scrollTopBtn');

    function handleScroll() {
        var y = window.scrollY || window.pageYOffset;

        if (siteHeader) {
            siteHeader.classList.toggle('scrolled', y > 40);
        }
        if (scrollTopBtn) {
            scrollTopBtn.classList.toggle('visible', y > 400);
        }
    }

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    // ========================================
    // Mobile Navigation Drawer
    // #navToggle -> .nav-menu(#navMenu).is-open + overlay + body.nav-open
    // ========================================
    var navToggle = document.getElementById('navToggle');
    var navMenu = document.getElementById('navMenu');
    var navOverlay = null;

    function ensureOverlay() {
        if (!navOverlay) {
            navOverlay = document.createElement('div');
            navOverlay.className = 'nav-overlay';
            navOverlay.setAttribute('aria-hidden', 'true');
            body.appendChild(navOverlay);
            navOverlay.addEventListener('click', closeNav);
        }
        return navOverlay;
    }

    function openNav() {
        if (!navMenu) return;
        ensureOverlay();
        navMenu.classList.add('is-open');
        navOverlay.classList.add('is-active');
        if (navToggle) {
            navToggle.classList.add('is-active');
            navToggle.setAttribute('aria-expanded', 'true');
        }
        body.classList.add('nav-open');
    }

    function closeNav() {
        if (!navMenu) return;
        navMenu.classList.remove('is-open');
        if (navOverlay) navOverlay.classList.remove('is-active');
        if (navToggle) {
            navToggle.classList.remove('is-active');
            navToggle.setAttribute('aria-expanded', 'false');
        }
        body.classList.remove('nav-open');
    }

    function toggleNav() {
        if (navMenu && navMenu.classList.contains('is-open')) {
            closeNav();
        } else {
            openNav();
        }
    }

    if (navToggle && navMenu) {
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.addEventListener('click', toggleNav);

        // Close when a drawer link is tapped
        navMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeNav);
        });
    }

    // Reset drawer state if resized up to desktop
    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if (window.innerWidth >= 992) closeNav();
        }, 150);
    });

    // ========================================
    // Smooth Scroll for in-page anchor links
    // ========================================
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (!targetId || targetId === '#') return;

            var target = document.querySelector(targetId);
            if (!target) return;

            e.preventDefault();
            var headerHeight = siteHeader ? siteHeader.offsetHeight : 0;
            var top = target.getBoundingClientRect().top + (window.scrollY || window.pageYOffset) - headerHeight - 12;
            window.scrollTo({ top: top, behavior: 'smooth' });
        });
    });

    // ========================================
    // Scroll to Top
    // ========================================
    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ========================================
    // FAQ Accordion (.faq-item.active + aria-expanded)
    // ========================================
    var faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(function (item) {
        var question = item.querySelector('.faq-question');
        if (!question) return;

        question.setAttribute('aria-expanded', item.classList.contains('active') ? 'true' : 'false');

        question.addEventListener('click', function () {
            var isActive = item.classList.contains('active');

            // Close all (single-open accordion)
            faqItems.forEach(function (other) {
                other.classList.remove('active');
                var q = other.querySelector('.faq-question');
                if (q) q.setAttribute('aria-expanded', 'false');
            });

            if (!isActive) {
                item.classList.add('active');
                question.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // ========================================
    // Gallery Category Filter (.filter-btn -> data-filter)
    // ========================================
    var filterButtons = document.querySelectorAll('.gallery-filters .filter-btn');
    var galleryItems = document.querySelectorAll('.gallery-grid .gallery-item, .gallery-grid-full .gallery-item');

    filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterButtons.forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');

            var filter = this.getAttribute('data-filter') || this.getAttribute('data-category') || 'all';

            galleryItems.forEach(function (item) {
                var cat = item.getAttribute('data-category');
                var show = (filter === 'all' || cat === filter);
                item.style.display = show ? '' : 'none';
            });
        });
    });

    // ========================================
    // Gallery Lightbox (#lightboxModal)
    // Triggers: .gallery-lightbox (preferred) else .gallery-item img
    // ========================================
    var lightbox = document.getElementById('lightboxModal');
    var lightboxImage = document.getElementById('lightboxImage');
    var lightboxCaption = document.getElementById('lightboxCaption');
    var lightboxClose = document.getElementById('lightboxClose');
    var lightboxPrev = document.getElementById('lightboxPrev');
    var lightboxNext = document.getElementById('lightboxNext');

    var slides = [];
    var currentIndex = 0;

    var lightboxTriggers = document.querySelectorAll('.gallery-lightbox');

    if (lightbox && lightboxImage && lightboxTriggers.length) {
        lightboxTriggers.forEach(function (trigger, index) {
            var src = trigger.getAttribute('href') || (trigger.querySelector('img') ? trigger.querySelector('img').src : '');
            var caption = trigger.getAttribute('data-title') || '';
            slides.push({ src: src, caption: caption });

            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                currentIndex = index;
                openLightbox();
            });
        });

        function openLightbox() {
            renderSlide();
            lightbox.classList.add('active');
            lightbox.classList.add('is-open');
            body.classList.add('nav-open'); // reuse scroll-lock
        }

        function closeLightbox() {
            lightbox.classList.remove('active');
            lightbox.classList.remove('is-open');
            body.classList.remove('nav-open');
        }

        function renderSlide() {
            var slide = slides[currentIndex];
            if (!slide) return;
            lightboxImage.src = slide.src;
            lightboxImage.alt = slide.caption;
            if (lightboxCaption) lightboxCaption.textContent = slide.caption;
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            renderSlide();
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            renderSlide();
        }

        if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
        if (lightboxPrev) lightboxPrev.addEventListener('click', prevSlide);
        if (lightboxNext) lightboxNext.addEventListener('click', nextSlide);

        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) closeLightbox();
        });

        document.addEventListener('keydown', function (e) {
            if (!lightbox.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            else if (e.key === 'ArrowLeft') prevSlide();
            else if (e.key === 'ArrowRight') nextSlide();
        });
    }

    // ========================================
    // Global ESC closes the mobile nav drawer too
    // ========================================
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && navMenu && navMenu.classList.contains('is-open')) {
            closeNav();
        }
    });

    // ========================================
    // Client-side Form Validation (progressive enhancement)
    // Adds .is-invalid + inline .field-error; never blocks valid submits.
    // ========================================
    var forms = document.querySelectorAll('.form, .free-trial-form, .contact-form');

    function setError(field, message) {
        field.classList.add('is-invalid');
        field.setAttribute('aria-invalid', 'true');
        var group = field.closest('.form-group') || field.parentElement;
        var msg = group ? group.querySelector('.field-error') : null;
        if (!msg && group) {
            msg = document.createElement('span');
            msg.className = 'field-error';
            group.appendChild(msg);
        }
        if (msg) msg.textContent = message;
    }

    function clearError(field) {
        field.classList.remove('is-invalid');
        field.removeAttribute('aria-invalid');
        var group = field.closest('.form-group') || field.parentElement;
        var msg = group ? group.querySelector('.field-error') : null;
        if (msg) msg.textContent = '';
    }

    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var valid = true;
            var firstInvalid = null;

            form.querySelectorAll('[required]').forEach(function (field) {
                clearError(field);
                if (!field.value.trim()) {
                    valid = false;
                    setError(field, 'This field is required.');
                    if (!firstInvalid) firstInvalid = field;
                }
            });

            form.querySelectorAll('input[type="email"]').forEach(function (field) {
                if (field.value.trim()) {
                    var ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim());
                    if (!ok) {
                        valid = false;
                        setError(field, 'Please enter a valid email address.');
                        if (!firstInvalid) firstInvalid = field;
                    }
                }
            });

            form.querySelectorAll('input[type="tel"]').forEach(function (field) {
                if (field.value.trim()) {
                    var digits = field.value.replace(/[^0-9]/g, '');
                    if (digits.length < 7) {
                        valid = false;
                        setError(field, 'Please enter a valid phone number.');
                        if (!firstInvalid) firstInvalid = field;
                    }
                }
            });

            if (!valid) {
                e.preventDefault();
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus({ preventScroll: true });
                }
            }
        });

        // Clear an error as the user fixes the field
        form.querySelectorAll('input, select, textarea').forEach(function (field) {
            field.addEventListener('input', function () {
                if (field.classList.contains('is-invalid') && field.value.trim()) {
                    clearError(field);
                }
            });
        });
    });

    // ========================================
    // Scroll-reveal animations ([data-animate] -> .in-view)
    // Content is visible by default; this only enhances.
    // ========================================
    var animated = document.querySelectorAll('[data-animate]');

    if ('IntersectionObserver' in window && animated.length) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        animated.forEach(function (el) { observer.observe(el); });
    } else {
        animated.forEach(function (el) { el.classList.add('in-view'); });
    }

    // ========================================
    // Toast notifications -> window.showToast(message, type)
    // type: 'success' | 'error' | 'info'
    // ========================================
    window.showToast = function (message, type) {
        type = type || 'info';
        var container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            body.appendChild(container);
        }

        var toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.setAttribute('role', 'status');
        toast.textContent = message;
        container.appendChild(toast);

        setTimeout(function () {
            toast.classList.add('hide');
            setTimeout(function () { toast.remove(); }, 320);
        }, 4000);
    };

    // ========================================
    // Auto-dismiss server-rendered alerts (non-destructive)
    // ========================================
    document.querySelectorAll('.alert[data-auto-hide]').forEach(function (alert) {
        var delay = parseInt(alert.getAttribute('data-auto-hide'), 10) || 5000;
        setTimeout(function () {
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 320);
        }, delay);
    });

});
