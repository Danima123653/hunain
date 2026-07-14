/* Custom Masonry Filter Grid & Premium Lightbox - Athlete Portfolio */
document.addEventListener("DOMContentLoaded", () => {
    // ----------------------------------------------------
    // GALLERY FILTER SECTION
    // ----------------------------------------------------
    const filters = document.querySelectorAll(".gallery-filter");
    const items = document.querySelectorAll(".gallery-item");

    if (filters.length && items.length) {
        filters.forEach((filter) => {
            filter.addEventListener("click", () => {
                // Toggle active filter button style
                filters.forEach((f) => f.classList.remove("active"));
                filter.classList.add("active");

                const targetCategory = filter.getAttribute("data-filter");

                items.forEach((item) => {
                    const itemCategory = item.getAttribute("data-category");
                    
                    // Smooth filter fade
                    if (targetCategory === "all" || itemCategory === targetCategory) {
                        item.style.display = "block";
                        setTimeout(() => {
                            item.style.opacity = "1";
                            item.style.transform = "scale(1)";
                        }, 50);
                    } else {
                        item.style.opacity = "0";
                        item.style.transform = "scale(0.85)";
                        setTimeout(() => {
                            item.style.display = "none";
                        }, 300);
                    }
                });
            });
        });
    }

    // ----------------------------------------------------
    // LIGHTBOX SYSTEM (IMAGES & VIDEOS)
    // ----------------------------------------------------
    const lightbox = document.getElementById("lightbox-modal");
    const lightboxImg = document.getElementById("lightbox-img");
    const lightboxVideo = document.getElementById("lightbox-video");
    const lightboxCaption = document.getElementById("lightbox-caption");
    const lightboxClose = document.getElementById("lightbox-close");
    const lightboxPrev = document.getElementById("lightbox-prev");
    const lightboxNext = document.getElementById("lightbox-next");

    if (!lightbox) return;

    let currentIndex = 0;
    let mediaList = []; // Contains current active elements matching category filter

    const updateMediaList = () => {
        // Collect only visible elements in the current filter state
        mediaList = Array.from(items).filter(item => item.style.display !== "none");
    };

    const showMedia = (index) => {
        if (index < 0 || index >= mediaList.length) return;
        currentIndex = index;

        const activeItem = mediaList[currentIndex];
        const mediaTrigger = activeItem.querySelector(".gallery-overlay-btn") || activeItem.querySelector(".gallery-thumbnail");
        const mediaType = activeItem.getAttribute("data-type") || "image";
        const mediaSrc = activeItem.getAttribute("data-src");
        const captionText = activeItem.querySelector(".gallery-title")?.textContent || "";

        // Reset elements
        lightboxImg.style.display = "none";
        lightboxImg.src = "";
        lightboxVideo.style.display = "none";
        lightboxVideo.src = "";

        if (mediaType === "video") {
            lightboxVideo.style.display = "block";
            lightboxVideo.src = mediaSrc;
            lightboxVideo.play().catch(() => {}); // handle browser autoplay blocks
        } else {
            lightboxImg.style.display = "block";
            lightboxImg.src = mediaSrc;
        }

        lightboxCaption.textContent = captionText;
        lightbox.classList.add("active");
        document.body.classList.add("no-scroll");
    };

    const closeLightbox = () => {
        lightbox.classList.remove("active");
        document.body.classList.remove("no-scroll");
        
        // Stop playing video if active
        lightboxVideo.pause();
        lightboxVideo.src = "";
        lightboxImg.src = "";
    };

    // Attach click events on elements
    items.forEach((item) => {
        const trigger = item.querySelector(".gallery-overlay-btn") || item;
        trigger.addEventListener("click", (e) => {
            e.preventDefault();
            updateMediaList();
            
            const index = mediaList.indexOf(item);
            if (index !== -1) {
                showMedia(index);
            }
        });
    });

    // Close button trigger
    lightboxClose.addEventListener("click", closeLightbox);

    // Modal background click overlay trigger
    lightbox.addEventListener("click", (e) => {
        if (e.target === lightbox || e.target.classList.contains("lightbox-content-wrapper")) {
            closeLightbox();
        }
    });

    // Prev / Next button triggers
    lightboxPrev.addEventListener("click", (e) => {
        e.stopPropagation();
        let prevIndex = currentIndex - 1;
        if (prevIndex < 0) prevIndex = mediaList.length - 1;
        showMedia(prevIndex);
    });

    lightboxNext.addEventListener("click", (e) => {
        e.stopPropagation();
        let nextIndex = currentIndex + 1;
        if (nextIndex >= mediaList.length) nextIndex = 0;
        showMedia(nextIndex);
    });

    // Keyboard controls support
    document.addEventListener("keydown", (e) => {
        if (!lightbox.classList.contains("active")) return;
        
        if (e.key === "Escape") {
            closeLightbox();
        } else if (e.key === "ArrowLeft") {
            lightboxPrev.click();
        } else if (e.key === "ArrowRight") {
            lightboxNext.click();
        }
    });
});
