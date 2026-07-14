/* Main Application Logic - Athlete Portfolio */

// ----------------------------------------------------
// THEME TOGGLE SWITCHER (LIGHT / DARK MODE)
// ----------------------------------------------------
(function() {
    const initTheme = () => {
        const themeToggleBtn = document.getElementById("theme-toggle-btn");
        if (!themeToggleBtn) {
            return false; // Retrying on DOMContentLoaded fallback
        }
        
        // Check for saved theme preference safely
        let currentTheme = "dark";
        try {
            currentTheme = localStorage.getItem("theme") || "dark";
        } catch (e) {
            console.warn("Storage access blocked: defaulting to dark theme.", e);
        }
        
        // Function to apply theme changes smoothly
        const applyTheme = (theme) => {
            if (theme === "light") {
                document.documentElement.setAttribute("data-theme", "light");
                themeToggleBtn.innerHTML = '<i class="fa-solid fa-sun"></i>';
            } else {
                document.documentElement.removeAttribute("data-theme");
                themeToggleBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
            }
        };
        
        // Apply current theme on load
        applyTheme(currentTheme);

        themeToggleBtn.addEventListener("click", () => {
            const currentThemeAttr = document.documentElement.getAttribute("data-theme");
            const isLight = currentThemeAttr === "light";
            const newTheme = isLight ? "dark" : "light";
            
            applyTheme(newTheme);
            
            try {
                localStorage.setItem("theme", newTheme);
            } catch (e) {
                console.warn("Could not save theme preference:", e);
            }
        });
        
        return true;
    };

    // Try executing immediately. If the DOM isn't fully loaded, fallback to DOMContentLoaded
    if (!initTheme()) {
        document.addEventListener("DOMContentLoaded", initTheme);
    }
})();

document.addEventListener("DOMContentLoaded", () => {
    
    // ----------------------------------------------------
    // PRELOADER DISMISSAL
    // ----------------------------------------------------
    const preloader = document.getElementById("preloader");
    window.addEventListener("load", () => {
        if (preloader) {
            preloader.classList.add("fade-out");
            setTimeout(() => {
                preloader.style.display = "none";
            }, 800);
        }
    });
    // Fallback in case load event takes too long
    setTimeout(() => {
        if (preloader && !preloader.classList.contains("fade-out")) {
            preloader.classList.add("fade-out");
            setTimeout(() => {
                preloader.style.display = "none";
            }, 800);
        }
    }, 3000);

    // ----------------------------------------------------
    // STICKY HEADER & SCROLL PROGRESS
    // ----------------------------------------------------
    const header = document.querySelector(".header");
    const progressBar = document.querySelector(".scroll-progress-bar");
    const backToTopBtn = document.querySelector(".back-to-top-btn");

    window.addEventListener("scroll", () => {
        const scrollPos = window.scrollY;
        const documentHeight = document.documentElement.scrollHeight - window.innerHeight;
        
        // Sticky Header class toggles
        if (scrollPos > 50) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }

        // Scroll Progress width update
        if (progressBar && documentHeight > 0) {
            const scrollPercentage = (scrollPos / documentHeight) * 100;
            progressBar.style.width = `${scrollPercentage}%`;
        }

        // Back to top floating button display
        if (backToTopBtn) {
            if (scrollPos > 600) {
                backToTopBtn.classList.add("active");
            } else {
                backToTopBtn.classList.remove("active");
            }
        }
    });

    // Back to top trigger
    if (backToTopBtn) {
        backToTopBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    }

    // ----------------------------------------------------
    // MOBILE NAV DRAWER TOGGLE
    // ----------------------------------------------------
    const navToggle = document.getElementById("nav-toggle-btn");
    const navMenu = document.getElementById("nav-menu-list");
    const navLinks = document.querySelectorAll(".nav-link");

    if (navToggle && navMenu) {
        navToggle.addEventListener("click", () => {
            navToggle.classList.toggle("open");
            navMenu.classList.toggle("open");
            document.body.classList.toggle("no-scroll");
        });

        // Close menu when clicking nav links
        navLinks.forEach((link) => {
            link.addEventListener("click", () => {
                navToggle.classList.remove("open");
                navMenu.classList.remove("open");
                document.body.classList.remove("no-scroll");
            });
        });
    }

    // ----------------------------------------------------
    // SCROLL SPY ACTIVE LINKS
    // ----------------------------------------------------
    const sections = document.querySelectorAll("section[id]");
    
    const scrollSpy = () => {
        const scrollY = window.pageYOffset;
        
        sections.forEach((current) => {
            const sectionHeight = current.offsetHeight;
            const sectionTop = current.offsetTop - 100;
            const sectionId = current.getAttribute("id");
            const navLink = document.querySelector(`.nav-menu a[href*=${sectionId}]`);

            if (navLink) {
                if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                    navLink.classList.add("active");
                } else {
                    navLink.classList.remove("active");
                }
            }
        });
    };
    window.addEventListener("scroll", scrollSpy);

    // ----------------------------------------------------
    // INTERSECTION OBSERVER FOR SCROLL REVEALS
    // ----------------------------------------------------
    const revealItems = document.querySelectorAll(".reveal");
    const revealObserverOptions = {
        root: null,
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    };

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("active");
                observer.unobserve(entry.target); // Animates once
            }
        });
    }, revealObserverOptions);

    revealItems.forEach((item) => {
        revealObserver.observe(item);
    });

    // ----------------------------------------------------
    // CARD MOUSE GLOW & TILT EFFECT (PREMIUM UX)
    // ----------------------------------------------------
    const cards = document.querySelectorAll(".luxury-card-glow, .stat-card, .achievement-card, .skill-card");
    
    cards.forEach((card) => {
        card.addEventListener("mousemove", (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left; // x coordinate inside card
            const y = e.clientY - rect.top;  // y coordinate inside card

            // Set custom properties for hover glow
            card.style.setProperty("--mouse-x", `${x}px`);
            card.style.setProperty("--mouse-y", `${y}px`);

            // Light Tilt animation
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const tiltX = ((y - centerY) / centerY) * 6; // Max tilt 6deg
            const tiltY = ((centerX - x) / centerX) * 6;
            
            card.style.transform = `perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale3d(1.02, 1.02, 1.02)`;
        });

        card.addEventListener("mouseleave", () => {
            card.style.transform = "perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)";
        });
    });

    // ----------------------------------------------------
    // ABOUT TAB SELECTION
    // ----------------------------------------------------
    const tabBtns = document.querySelectorAll(".about-tab-btn");
    const tabPanels = document.querySelectorAll(".about-tab-panel");

    if (tabBtns.length && tabPanels.length) {
        tabBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                const target = btn.getAttribute("data-tab");

                tabBtns.forEach((b) => b.classList.remove("active"));
                tabPanels.forEach((p) => p.classList.remove("active"));

                btn.classList.add("active");
                const matchingPanel = document.getElementById(`tab-${target}`);
                if (matchingPanel) matchingPanel.classList.add("active");
            });
        });
    }

    // ----------------------------------------------------
    // TESTIMONIALS SLIDER
    // ----------------------------------------------------
    const slides = document.querySelectorAll(".testimonial-slide");
    const dots = document.querySelectorAll(".slider-dot");
    const prevBtn = document.querySelector(".slider-prev-btn");
    const nextBtn = document.querySelector(".slider-next-btn");

    if (slides.length) {
        let currentSlide = 0;

        const updateSlider = (index) => {
            currentSlide = index;
            if (currentSlide < 0) currentSlide = slides.length - 1;
            if (currentSlide >= slides.length) currentSlide = 0;

            slides.forEach((slide) => slide.classList.remove("active"));
            dots.forEach((dot) => dot.classList.remove("active"));

            slides[currentSlide].classList.add("active");
            if (dots[currentSlide]) dots[currentSlide].classList.add("active");
        };

        if (prevBtn && nextBtn) {
            prevBtn.addEventListener("click", () => updateSlider(currentSlide - 1));
            nextBtn.addEventListener("click", () => updateSlider(currentSlide + 1));
        }

        dots.forEach((dot, index) => {
            dot.addEventListener("click", () => updateSlider(index));
        });

        // Autoplay testimonials
        setInterval(() => {
            updateSlider(currentSlide + 1);
        }, 8000);
    }

    // ----------------------------------------------------
    // ACCORDION Collapsibles (FAQ)
    // ----------------------------------------------------
    const accordionHeaders = document.querySelectorAll(".faq-accordion-header");

    accordionHeaders.forEach((header) => {
        header.addEventListener("click", () => {
            const activeHeader = document.querySelector(".faq-accordion-header.active");
            if (activeHeader && activeHeader !== header) {
                activeHeader.classList.remove("active");
                activeHeader.nextElementSibling.style.maxHeight = null;
            }

            header.classList.toggle("active");
            const panel = header.nextElementSibling;
            if (header.classList.contains("active")) {
                panel.style.maxHeight = panel.scrollHeight + "px";
            } else {
                panel.style.maxHeight = null;
            }
        });
    });

    // ----------------------------------------------------
    // TRAINING ROUTINE SCHEDULER TABS
    // ----------------------------------------------------
    const routineTabs = document.querySelectorAll(".routine-tab-btn");
    const routinePanels = document.querySelectorAll(".routine-table-panel");

    if (routineTabs.length && routinePanels.length) {
        routineTabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                const targetDay = tab.getAttribute("data-day");
                routineTabs.forEach((t) => t.classList.remove("active"));
                routinePanels.forEach((p) => p.classList.remove("active"));

                tab.classList.add("active");
                const matchingPanel = document.getElementById(`routine-${targetDay}`);
                if (matchingPanel) matchingPanel.classList.add("active");
            });
        });
    }

    // ----------------------------------------------------
    // AJAX CONTACT FORM HANDLER
    // ----------------------------------------------------
    const contactForm = document.getElementById("contact-form");
    const formResponse = document.getElementById("form-response-msg");

    if (contactForm && formResponse) {
        contactForm.addEventListener("submit", (e) => {
            e.preventDefault();

            // Client-side visual verification loading state
            const submitBtn = contactForm.querySelector("button[type='submit']");
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = `<span>Sending...</span>`;
            submitBtn.disabled = true;

            // Form data preparation
            const formData = new FormData(contactForm);

            // Fetch request
            fetch("php/contact.php", {
                method: "POST",
                body: formData
            })
            .then((response) => response.json())
            .then((data) => {
                formResponse.style.display = "block";
                
                if (data.status === "success") {
                    formResponse.className = "form-msg success";
                    formResponse.innerHTML = `<p>${data.message}</p>`;
                    contactForm.reset();
                } else {
                    formResponse.className = "form-msg error";
                    formResponse.innerHTML = `<p>${data.message}</p>`;
                }
            })
            .catch((error) => {
                formResponse.style.display = "block";
                formResponse.className = "form-msg error";
                formResponse.innerHTML = `<p>An unexpected error occurred. Please try again or reach out directly.</p>`;
                console.error("Form error:", error);
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                
                // Hide message after 5 seconds
                setTimeout(() => {
                    formResponse.style.opacity = "0";
                    setTimeout(() => {
                        formResponse.style.display = "none";
                        formResponse.style.opacity = "1";
                    }, 500);
                }, 6000);
            });
        });
    }
});
