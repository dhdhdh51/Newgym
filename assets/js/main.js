/**
 * Premium Gym Website - Main JavaScript
 * Handles all client-side interactivity
 */

document.addEventListener('DOMContentLoaded', function () {

    // ========================================
    // Mobile Menu Toggle
    // ========================================
    const hamburger = document.querySelector('.hamburger');
    const mobileNav = document.querySelector('.mobile-nav');
    const navOverlay = document.querySelector('.nav-overlay');

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            this.classList.toggle('active');
            if (mobileNav) mobileNav.classList.toggle('active');
            if (navOverlay) navOverlay.classList.toggle('active');
            document.body.style.overflow = mobileNav && mobileNav.classList.contains('active') ? 'hidden' : '';
        });
    }

    if (navOverlay) {
        navOverlay.addEventListener('click', function () {
            if (hamburger) hamburger.classList.remove('active');
            if (mobileNav) mobileNav.classList.remove('active');
            this.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // Close mobile menu on link click
    const mobileNavLinks = document.querySelectorAll('.mobile-nav a');
    mobileNavLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (hamburger) hamburger.classList.remove('active');
            if (mobileNav) mobileNav.classList.remove('active');
            if (navOverlay) navOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // ========================================
    // Smooth Scroll for Anchor Links
    // ========================================
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                const headerHeight = document.querySelector('.header') ? document.querySelector('.header').offsetHeight : 0;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ========================================
    // Sticky Header
    // ========================================
    const header = document.querySelector('.header');

    function handleScroll() {
        if (header) {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }

        // Scroll to top button visibility
        const scrollTopBtn = document.querySelector('.scroll-top');
        if (scrollTopBtn) {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        }
    }

    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Run on load

    // ========================================
    // Scroll to Top Button
    // ========================================
    const scrollTopBtn = document.querySelector('.scroll-top');
    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ========================================
    // FAQ Accordion
    // ========================================
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(function (item) {
        const question = item.querySelector('.faq-question');
        if (question) {
            question.addEventListener('click', function () {
                const isActive = item.classList.contains('active');

                // Close all other FAQ items
                faqItems.forEach(function (otherItem) {
                    otherItem.classList.remove('active');
                });

                // Toggle current item
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        }
    });

    // ========================================
    // Gallery Lightbox
    // ========================================
    const galleryItems = document.querySelectorAll('.gallery-item');
    const lightbox = document.querySelector('.lightbox');
    const lightboxImg = lightbox ? lightbox.querySelector('img') : null;
    const lightboxClose = document.querySelector('.lightbox-close');
    const lightboxPrev = document.querySelector('.lightbox-prev');
    const lightboxNext = document.querySelector('.lightbox-next');
    let currentGalleryIndex = 0;
    let galleryImages = [];

    // Collect all gallery image sources
    galleryItems.forEach(function (item, index) {
        const img = item.querySelector('img');
        if (img) {
            galleryImages.push(img.src);
            item.addEventListener('click', function () {
                currentGalleryIndex = index;
                openLightbox(img.src);
            });
        }
    });

    function openLightbox(src) {
        if (lightbox && lightboxImg) {
            lightboxImg.src = src;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLightbox() {
        if (lightbox) {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function showPrevImage() {
        if (galleryImages.length === 0) return;
        currentGalleryIndex = (currentGalleryIndex - 1 + galleryImages.length) % galleryImages.length;
        if (lightboxImg) lightboxImg.src = galleryImages[currentGalleryIndex];
    }

    function showNextImage() {
        if (galleryImages.length === 0) return;
        currentGalleryIndex = (currentGalleryIndex + 1) % galleryImages.length;
        if (lightboxImg) lightboxImg.src = galleryImages[currentGalleryIndex];
    }

    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }

    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', showPrevImage);
    }

    if (lightboxNext) {
        lightboxNext.addEventListener('click', showNextImage);
    }

    // Close lightbox on clicking outside image
    if (lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
    }

    // Keyboard navigation for lightbox
    document.addEventListener('keydown', function (e) {
        if (lightbox && lightbox.classList.contains('active')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') showPrevImage();
            if (e.key === 'ArrowRight') showNextImage();
        }
    });

    // ========================================
    // Gallery Category Filter
    // ========================================
    const filterButtons = document.querySelectorAll('.gallery-filters .filter-btn');
    const galleryFilterItems = document.querySelectorAll('.gallery-grid .gallery-item');

    filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            // Update active button
            filterButtons.forEach(function (b) {
                b.classList.remove('active');
            });
            this.classList.add('active');

            const category = this.getAttribute('data-category');

            galleryFilterItems.forEach(function (item) {
                if (category === 'all' || item.getAttribute('data-category') === category) {
                    item.style.display = '';
                    item.style.opacity = '1';
                    item.style.transform = 'scale(1)';
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(function () {
                        item.style.display = 'none';
                    }, 300);
                }
            });
        });
    });

    // ========================================
    // Form Validation
    // ========================================
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            let isValid = true;

            // Clear previous errors
            form.querySelectorAll('.form-control.error').forEach(function (field) {
                field.classList.remove('error');
            });
            form.querySelectorAll('.form-error').forEach(function (msg) {
                msg.style.display = 'none';
            });

            // Validate required fields
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(function (field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    const errorMsg = field.parentElement.querySelector('.form-error');
                    if (errorMsg) {
                        errorMsg.textContent = 'This field is required';
                        errorMsg.style.display = 'block';
                    }
                }
            });

            // Validate email fields
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(function (field) {
                if (field.value.trim()) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(field.value)) {
                        isValid = false;
                        field.classList.add('error');
                        const errorMsg = field.parentElement.querySelector('.form-error');
                        if (errorMsg) {
                            errorMsg.textContent = 'Please enter a valid email address';
                            errorMsg.style.display = 'block';
                        }
                    }
                }
            });

            // Validate phone fields
            const phoneFields = form.querySelectorAll('input[type="tel"]');
            phoneFields.forEach(function (field) {
                if (field.value.trim()) {
                    const phonePattern = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
                    if (!phonePattern.test(field.value.replace(/\s/g, ''))) {
                        isValid = false;
                        field.classList.add('error');
                        const errorMsg = field.parentElement.querySelector('.form-error');
                        if (errorMsg) {
                            errorMsg.textContent = 'Please enter a valid phone number';
                            errorMsg.style.display = 'block';
                        }
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector('.form-control.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    });

    // ========================================
    // Intersection Observer for Scroll Animations
    // ========================================
    const animatedElements = document.querySelectorAll('.fade-in-up');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        animatedElements.forEach(function (el) {
            observer.observe(el);
        });
    } else {
        // Fallback for older browsers
        animatedElements.forEach(function (el) {
            el.classList.add('visible');
        });
    }

    // ========================================
    // Counter Animation for Stats
    // ========================================
    const counters = document.querySelectorAll('[data-count]');

    function animateCounter(el) {
        const target = parseInt(el.getAttribute('data-count'), 10);
        const duration = 2000;
        const start = 0;
        const startTime = performance.now();

        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Ease out quad
            const eased = 1 - (1 - progress) * (1 - progress);
            const current = Math.floor(eased * (target - start) + start);

            el.textContent = current.toLocaleString();

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                el.textContent = target.toLocaleString();
            }
        }

        requestAnimationFrame(updateCounter);
    }

    if ('IntersectionObserver' in window && counters.length > 0) {
        const counterObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(function (counter) {
            counterObserver.observe(counter);
        });
    }

    // ========================================
    // Image Preview for File Inputs (Admin)
    // ========================================
    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');

    fileInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            const previewId = this.getAttribute('data-preview');
            const preview = document.getElementById(previewId);

            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    let img = preview.querySelector('img');
                    if (!img) {
                        preview.innerHTML = '';
                        img = document.createElement('img');
                        preview.appendChild(img);
                    }
                    img.src = e.target.result;
                };

                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // ========================================
    // Admin Sidebar Toggle (Mobile)
    // ========================================
    const adminToggle = document.querySelector('.admin-toggle-sidebar');
    const adminSidebar = document.querySelector('.admin-sidebar');

    if (adminToggle && adminSidebar) {
        adminToggle.addEventListener('click', function () {
            adminSidebar.classList.toggle('active');
        });
    }

    // ========================================
    // Confirm Delete Actions
    // ========================================
    const deleteButtons = document.querySelectorAll('[data-confirm]');

    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ========================================
    // Auto-hide Alert Messages
    // ========================================
    const alerts = document.querySelectorAll('.alert[data-auto-hide]');

    alerts.forEach(function (alert) {
        const delay = parseInt(alert.getAttribute('data-auto-hide'), 10) || 5000;
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function () {
                alert.remove();
            }, 300);
        }, delay);
    });

});
