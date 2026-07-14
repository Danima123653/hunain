/* Custom typing effect for athlete landing page - Athlete Portfolio */
document.addEventListener("DOMContentLoaded", () => {
    const typingSpan = document.getElementById("typing-text");
    if (!typingSpan) return;

    // Retrieve roles from attribute or define defaults
    const rolesAttr = typingSpan.getAttribute("data-roles");
    const roles = rolesAttr ? JSON.parse(rolesAttr) : [
        "Pakistan Soft Tennis Player",
        "Regional Cricket Player",
        "Computer Science Student"
    ];

    let roleIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    let typingSpeed = 100;

    function type() {
        const currentRole = roles[roleIndex];
        
        if (isDeleting) {
            // Delete speed is faster
            typingSpeed = 50;
            typingSpan.textContent = currentRole.substring(0, charIndex - 1);
            charIndex--;
        } else {
            // Typing speed
            typingSpeed = 120;
            typingSpan.textContent = currentRole.substring(0, charIndex + 1);
            charIndex++;
        }

        // Handle typing state cycles
        if (!isDeleting && charIndex === currentRole.length) {
            // Pause at the end of word before deleting
            typingSpeed = 2000;
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            // Move to next role
            roleIndex = (roleIndex + 1) % roles.length;
            // Short pause before starting typing again
            typingSpeed = 500;
        }

        setTimeout(type, typingSpeed);
    }

    // Start typewriter logic
    setTimeout(type, 1000);
});
