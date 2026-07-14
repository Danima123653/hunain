/* Custom Stats Counter Up Animation - Athlete Portfolio */
document.addEventListener("DOMContentLoaded", () => {
    const statsSection = document.querySelector(".stats-grid");
    const counters = document.querySelectorAll(".stat-counter");

    if (!counters.length) return;

    // Trigger count animation helper
    const startCount = (counter) => {
        const target = +counter.getAttribute("data-target");
        const duration = 2000; // 2 seconds count duration
        const stepTime = Math.max(Math.floor(duration / target), 15);
        let start = 0;

        const timer = setInterval(() => {
            start += Math.ceil(target / (duration / stepTime));
            if (start >= target) {
                counter.textContent = target + (counter.getAttribute("data-suffix") || "");
                clearInterval(timer);
            } else {
                counter.textContent = start + (counter.getAttribute("data-suffix") || "");
            }
        }, stepTime);
    };

    // Viewport Intersection Observer
    const observerOptions = {
        root: null, // viewport
        threshold: 0.25, // trigger when 25% of grid is visible
    };

    const statsObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                counters.forEach((counter) => startCount(counter));
                // Disconnect observer after animation starts so it only runs once
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    if (statsSection) {
        statsObserver.observe(statsSection);
    } else {
        // Fallback: observe individual counters if grid parent doesn't exist
        counters.forEach((c) => {
            const indObserver = new IntersectionObserver((entries, obs) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        startCount(entry.target);
                        obs.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            indObserver.observe(c);
        });
    }
});
