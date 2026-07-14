/* Custom Luxury Cursor Trail - Athlete Portfolio */
document.addEventListener("DOMContentLoaded", () => {
    const cursorDot = document.querySelector(".custom-cursor-dot");
    const cursorOutline = document.querySelector(".custom-cursor-outline");

    if (!cursorDot || !cursorOutline) return;

    // Hide custom cursor on touch/mobile devices
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    if (isTouchDevice) {
        cursorDot.style.display = "none";
        cursorOutline.style.display = "none";
        return;
    }

    let mouseX = 0;
    let mouseY = 0;
    let outlineX = 0;
    let outlineY = 0;

    // Follow speed multiplier (lower = smoother/slower)
    const trailSpeed = 0.15;

    window.addEventListener("mousemove", (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        
        // Show cursor elements once active
        cursorDot.style.opacity = "1";
        cursorOutline.style.opacity = "1";
    });

    // Custom requestAnimationFrame loop for ultra-smooth lag tracking
    function animateCursor() {
        // Linear interpolation formula: current = current + (target - current) * factor
        outlineX += (mouseX - outlineX) * trailSpeed;
        outlineY += (mouseY - outlineY) * trailSpeed;

        // Position the inner dot
        cursorDot.style.transform = `translate3d(${mouseX}px, ${mouseY}px, 0)`;
        // Position the outer lagging ring
        cursorOutline.style.transform = `translate3d(${outlineX}px, ${outlineY}px, 0)`;

        requestAnimationFrame(animateCursor);
    }
    requestAnimationFrame(animateCursor);

    // Interactive Hover States
    const interactiveElements = document.querySelectorAll(
        "a, button, input, textarea, select, .btn, .nav-link, .gallery-filter, .card, .accordion-header, .interactive"
    );

    interactiveElements.forEach((el) => {
        el.addEventListener("mouseenter", () => {
            cursorDot.classList.add("cursor-hover");
            cursorOutline.classList.add("cursor-hover");
            
            // If the element has specialized gold glow request
            if (el.classList.contains("gold-hover") || el.closest(".gold-hover-parent")) {
                cursorOutline.style.borderColor = "var(--clr-secondary)";
                cursorDot.style.backgroundColor = "var(--clr-secondary)";
            }
        });

        el.addEventListener("mouseleave", () => {
            cursorDot.classList.remove("cursor-hover");
            cursorOutline.classList.remove("cursor-hover");
            cursorOutline.style.borderColor = "var(--clr-primary)";
            cursorDot.style.backgroundColor = "var(--clr-secondary)";
        });
    });

    // Hide cursor when mouse leaves the browser window
    document.addEventListener("mouseleave", () => {
        cursorDot.style.opacity = "0";
        cursorOutline.style.opacity = "0";
    });
});
