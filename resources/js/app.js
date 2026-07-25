// Anti-Gravity Modern Esports Motion Engine & Interactive Controller

class AudioEngine {
    constructor() {
        this.ctx = null;
        this.enabled = true;

        const unlockAudio = () => {
            this.initCtx();
            window.removeEventListener('click', unlockAudio);
            window.removeEventListener('keydown', unlockAudio);
            window.removeEventListener('touchstart', unlockAudio);
        };

        if (typeof window !== 'undefined') {
            window.addEventListener('click', unlockAudio);
            window.addEventListener('keydown', unlockAudio);
            window.addEventListener('touchstart', unlockAudio);
        }
    }

    initCtx() {
        if (!this.ctx) {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) {
                this.ctx = new AudioCtx();
            }
        }
        if (this.ctx && this.ctx.state === 'suspended') {
            this.ctx.resume().catch(() => {});
        }
    }

    playSound(type) {
        if (!this.enabled || !this.ctx || this.ctx.state !== 'running') return;

        try {
            const now = this.ctx.currentTime;
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            if (type === 'click') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(800, now);
                osc.frequency.exponentialRampToValueAtTime(400, now + 0.08);
                gain.gain.setValueAtTime(0.15, now);
                gain.gain.linearRampToValueAtTime(0.01, now + 0.08);
                osc.start(now);
                osc.stop(now + 0.08);
            }
        } catch (e) {}
    }
}

window.esportsAudio = new AudioEngine();

// Reactive Cursor Trail & Mouse-Follow Parallax Motion Engine
function initInteractiveMotion() {
    const dot = document.getElementById('cursor-dot');
    const ring = document.getElementById('cursor-ring');
    const parallaxElements = document.querySelectorAll('[data-parallax-speed]');
    const tiltCards = document.querySelectorAll('.tilt-card');

    let mouseX = 0, mouseY = 0;
    let ringX = 0, ringY = 0;

    window.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;

        if (dot) {
            dot.style.left = `${mouseX}px`;
            dot.style.top = `${mouseY}px`;
        }

        const centerX = window.innerWidth / 2;
        const centerY = window.innerHeight / 2;
        const deltaX = (mouseX - centerX) / centerX;
        const deltaY = (mouseY - centerY) / centerY;

        parallaxElements.forEach((el) => {
            const speed = parseFloat(el.getAttribute('data-parallax-speed') || '10');
            const moveX = deltaX * speed;
            const moveY = deltaY * speed;
            el.style.transform = `translate3d(${moveX}px, ${moveY}px, 0)`;
        });
    });

    // 3D Card Tilt on Hover
    tiltCards.forEach((card) => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = ((y - centerY) / centerY) * -10;
            const rotateY = ((x - centerX) / centerX) * 10;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
        });
    });

    function renderCursor() {
        ringX += (mouseX - ringX) * 0.18;
        ringY += (mouseY - ringY) * 0.18;

        if (ring) {
            ring.style.left = `${ringX}px`;
            ring.style.top = `${ringY}px`;
        }

        requestAnimationFrame(renderCursor);
    }

    if (window.innerWidth > 1024) {
        renderCursor();
    } else {
        if (dot) dot.style.display = 'none';
        if (ring) ring.style.display = 'none';
    }

    // Scroll Reveal Observer
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        },
        { threshold: 0.15 }
    );

    document.querySelectorAll('.reveal-on-scroll').forEach((el) => observer.observe(el));
}

// Dynamic Countdown Timer
function initCountdownTimer() {
    const targetDateEl = document.getElementById('countdown-target');
    if (!targetDateEl) return;

    const targetDate = new Date(targetDateEl.getAttribute('data-date') || Date.now() + 864000000).getTime();

    function updateTimer() {
        const now = new Date().getTime();
        const diff = targetDate - now;

        if (diff <= 0) {
            document.getElementById('cd-days').innerText = '00';
            document.getElementById('cd-hours').innerText = '00';
            document.getElementById('cd-mins').innerText = '00';
            document.getElementById('cd-secs').innerText = '00';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const secs = Math.floor((diff % (1000 * 60)) / 1000);

        const dEl = document.getElementById('cd-days');
        const hEl = document.getElementById('cd-hours');
        const mEl = document.getElementById('cd-mins');
        const sEl = document.getElementById('cd-secs');

        if (dEl) dEl.innerText = String(days).padStart(2, '0');
        if (hEl) hEl.innerText = String(hours).padStart(2, '0');
        if (mEl) mEl.innerText = String(mins).padStart(2, '0');
        if (sEl) sEl.innerText = String(secs).padStart(2, '0');
    }

    updateTimer();
    setInterval(updateTimer, 1000);
}

// Particle Canvas Background
function initCyberGrid() {
    const canvas = document.getElementById('particle-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    window.addEventListener('resize', () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    });

    const particles = [];
    const count = Math.min(Math.floor(width / 25), 60);

    for (let i = 0; i < count; i++) {
        particles.push({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.8,
            vy: (Math.random() - 0.5) * 0.8,
            size: Math.random() * 2.5 + 1,
            pulse: Math.random() * Math.PI * 2,
        });
    }

    function draw() {
        ctx.clearRect(0, 0, width, height);

        for (let i = 0; i < particles.length; i++) {
            const p1 = particles[i];
            p1.x += p1.vx;
            p1.y += p1.vy;
            p1.pulse += 0.03;

            if (p1.x < 0 || p1.x > width) p1.vx *= -1;
            if (p1.y < 0 || p1.y > height) p1.vy *= -1;

            ctx.beginPath();
            ctx.arc(p1.x, p1.y, p1.size, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(0, 240, 255, ${0.4 + Math.sin(p1.pulse) * 0.2})`;
            ctx.fill();

            for (let j = i + 1; j < particles.length; j++) {
                const p2 = particles[j];
                const dx = p1.x - p2.x;
                const dy = p1.y - p2.y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < 130) {
                    ctx.beginPath();
                    ctx.moveTo(p1.x, p1.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.strokeStyle = `rgba(0, 240, 255, ${0.2 * (1 - dist / 130)})`;
                    ctx.lineWidth = 0.8;
                    ctx.stroke();
                }
            }
        }

        requestAnimationFrame(draw);
    }

    draw();
}

document.addEventListener('DOMContentLoaded', () => {
    initCyberGrid();
    initInteractiveMotion();
    initCountdownTimer();

    document.querySelectorAll('a, button').forEach((btn) => {
        btn.addEventListener('click', () => {
            window.esportsAudio.playSound('click');
        });
    });
});
