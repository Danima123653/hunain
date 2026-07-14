/* Custom Luxury Ambient Particles Background - Athlete Portfolio */
document.addEventListener("DOMContentLoaded", () => {
    const canvas = document.getElementById("particles-canvas");
    if (!canvas) return;

    const ctx = canvas.getContext("2d");
    let particlesArray = [];
    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    // Dynamic sizing helper
    window.addEventListener("resize", () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
        initParticles();
    });

    // Particle Object Definition
    class Particle {
        constructor(x, y, directionX, directionY, size, color) {
            this.x = x;
            this.y = y;
            this.directionX = directionX;
            this.directionY = directionY;
            this.size = size;
            this.color = color;
        }

        // Draw individual particle
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2, false);
            ctx.fillStyle = this.color;
            ctx.fill();
        }

        // Move particle & keep within canvas boundary
        update() {
            if (this.x > width || this.x < 0) {
                this.directionX = -this.directionX;
            }
            if (this.y > height || this.y < 0) {
                this.directionY = -this.directionY;
            }

            this.x += this.directionX;
            this.y += this.directionY;
            this.draw();
        }
    }

    // Populate particles base array
    function initParticles() {
        particlesArray = [];
        // Scale number of particles by resolution
        const numberOfParticles = Math.min((width * height) / 14000, 100);
        
        for (let i = 0; i < numberOfParticles; i++) {
            const size = Math.random() * 2 + 1; // small elegant dots
            const x = Math.random() * (width - size * 2) + size;
            const y = Math.random() * (height - size * 2) + size;
            
            // Gentle speed multipliers
            const directionX = Math.random() * 0.4 - 0.2;
            const directionY = Math.random() * 0.4 - 0.2;
            
            // Blend gold and emerald green colored particles
            const color = Math.random() > 0.4 
                ? "rgba(0, 200, 83, 0.25)"  /* Translucent Emerald Green */
                : "rgba(255, 215, 0, 0.25)"; /* Translucent Royal Gold */

            particlesArray.push(new Particle(x, y, directionX, directionY, size, color));
        }
    }

    // Connect particles when proximity is near
    function connect() {
        const maxDistance = 140;
        for (let a = 0; a < particlesArray.length; a++) {
            for (let b = a + 1; b < particlesArray.length; b++) {
                const dx = particlesArray[a].x - particlesArray[b].x;
                const dy = particlesArray[a].y - particlesArray[b].y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < maxDistance) {
                    const opacity = 1 - distance / maxDistance;
                    
                    // Connect using ultra-light grey or primary gradient
                    ctx.strokeStyle = `rgba(0, 200, 83, ${opacity * 0.08})`;
                    ctx.lineWidth = 0.8;
                    ctx.beginPath();
                    ctx.moveTo(particlesArray[a].x, particlesArray[a].y);
                    ctx.lineTo(particlesArray[b].x, particlesArray[b].y);
                    ctx.stroke();
                }
            }
        }
    }

    // Animation Loop
    function animate() {
        ctx.clearRect(0, 0, width, height);
        for (let i = 0; i < particlesArray.length; i++) {
            particlesArray[i].update();
        }
        connect();
        requestAnimationFrame(animate);
    }

    initParticles();
    animate();
});
