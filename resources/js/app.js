// Outlaw Showdown: Master Kinetic Esports Interactive Engine

class EsportsAudio {
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

    playClick() {
        if (!this.enabled || !this.ctx || this.ctx.state !== 'running') return;
        try {
            const now = this.ctx.currentTime;
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.type = 'sine';
            osc.frequency.setValueAtTime(700, now);
            osc.frequency.exponentialRampToValueAtTime(350, now + 0.04);
            gain.gain.setValueAtTime(0.05, now);
            gain.gain.linearRampToValueAtTime(0.001, now + 0.04);
            osc.start(now);
            osc.stop(now + 0.04);
        } catch (e) {}
    }

    playPower() {
        if (!this.enabled || !this.ctx || this.ctx.state !== 'running') return;
        try {
            const now = this.ctx.currentTime;
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(220, now);
            osc.frequency.exponentialRampToValueAtTime(880, now + 0.14);
            gain.gain.setValueAtTime(0.07, now);
            gain.gain.linearRampToValueAtTime(0.001, now + 0.14);
            osc.start(now);
            osc.stop(now + 0.14);
        } catch (e) {}
    }
}

window.esportsAudio = new EsportsAudio();

// 1. Kinetic Hero Particle Canvas
function initKineticParticles() {
    const canvas = document.getElementById('hero-particle-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let width = (canvas.width = canvas.parentElement.offsetWidth);
    let height = (canvas.height = canvas.parentElement.offsetHeight);

    const particles = [];
    const particleCount = Math.min(Math.floor(width / 22), 60);

    let mouseX = width / 2;
    let mouseY = height / 2;

    window.addEventListener('resize', () => {
        if (!canvas.parentElement) return;
        width = canvas.width = canvas.parentElement.offsetWidth;
        height = canvas.height = canvas.parentElement.offsetHeight;
    });

    window.addEventListener('mousemove', (e) => {
        const rect = canvas.getBoundingClientRect();
        mouseX = e.clientX - rect.left;
        mouseY = e.clientY - rect.top;
    }, { passive: true });

    class Particle {
        constructor() {
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            this.vx = (Math.random() - 0.5) * 0.7;
            this.vy = (Math.random() - 0.5) * 0.7;
            this.radius = Math.random() * 1.5 + 0.8;
            this.alpha = Math.random() * 0.5 + 0.2;
        }

        update() {
            this.x += this.vx;
            this.y += this.vy;

            if (this.x < 0) this.x = width;
            if (this.x > width) this.x = 0;
            if (this.y < 0) this.y = height;
            if (this.y > height) this.y = 0;

            // Subtle mouse repulsion / attraction
            const dx = mouseX - this.x;
            const dy = mouseY - this.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < 120) {
                this.x -= (dx / dist) * 0.8;
                this.y -= (dy / dist) * 0.8;
            }
        }

        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(16, 185, 129, ${this.alpha})`;
            ctx.fill();
        }
    }

    for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }

    function animate() {
        ctx.clearRect(0, 0, width, height);

        for (let i = 0; i < particles.length; i++) {
            particles[i].update();
            particles[i].draw();

            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < 90) {
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.strokeStyle = `rgba(16, 185, 129, ${0.15 * (1 - dist / 90)})`;
                    ctx.lineWidth = 0.6;
                    ctx.stroke();
                }
            }
        }

        requestAnimationFrame(animate);
    }

    animate();
}

// 2. Mouse Spotlight Tracking on Cards
function initCardSpotlights() {
    const cards = document.querySelectorAll('.editorial-card, .editorial-card-featured');
    cards.forEach((card) => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        }, { passive: true });
    });
}

// 3. Dynamic Countdown Timer Engine
function initCountdown() {
    const targetEl = document.getElementById('countdown-target');
    if (!targetEl) return;

    const dateStr = targetEl.getAttribute('data-date');
    if (!dateStr) return;

    const targetDate = new Date(dateStr).getTime();

    function update() {
        const now = new Date().getTime();
        const diff = targetDate - now;

        const dEl = document.getElementById('cd-days');
        const hEl = document.getElementById('cd-hours');
        const mEl = document.getElementById('cd-mins');
        const sEl = document.getElementById('cd-secs');

        if (diff <= 0) {
            if (dEl) dEl.innerText = '00';
            if (hEl) hEl.innerText = '00';
            if (mEl) mEl.innerText = '00';
            if (sEl) sEl.innerText = '00';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const secs = Math.floor((diff % (1000 * 60)) / 1000);

        if (dEl) dEl.innerText = String(days).padStart(2, '0');
        if (hEl) hEl.innerText = String(hours).padStart(2, '0');
        if (mEl) mEl.innerText = String(mins).padStart(2, '0');
        if (sEl) sEl.innerText = String(secs).padStart(2, '0');
    }

    update();
    setInterval(update, 1000);
}

// 4. Number Counter Animation on Scroll
function initAnimatedCounters() {
    const counterElements = document.querySelectorAll('.animate-counter');
    if (!counterElements.length) return;

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const targetVal = parseInt(el.getAttribute('data-target') || '0', 10);
                const prefix = el.getAttribute('data-prefix') || '';
                const suffix = el.getAttribute('data-suffix') || '';
                const duration = 1600;
                const startTime = performance.now();

                function step(now) {
                    const progress = Math.min((now - startTime) / duration, 1);
                    const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                    const current = Math.floor(ease * targetVal);
                    el.innerText = `${prefix}${current.toLocaleString()}${suffix}`;

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    } else {
                        el.innerText = `${prefix}${targetVal.toLocaleString()}${suffix}`;
                    }
                }

                requestAnimationFrame(step);
                obs.unobserve(el);
            }
        });
    }, { threshold: 0.15 });

    counterElements.forEach((el) => observer.observe(el));
}

// 5. Scroll Spy & Navbar Behavior
function initNavbarAndScroll() {
    const navbar = document.getElementById('floating-navbar');
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-spy-link');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 25) {
            navbar?.classList.add('scrolled');
        } else {
            navbar?.classList.remove('scrolled');
        }
    }, { passive: true });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinks.forEach((link) => {
                    const href = link.getAttribute('href')?.replace('#', '');
                    if (href === id) {
                        link.classList.add('active-nav-link');
                    } else {
                        link.classList.remove('active-nav-link');
                    }
                });
            }
        });
    }, {
        rootMargin: '-20% 0px -60% 0px',
        threshold: 0
    });

    sections.forEach((s) => observer.observe(s));

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.05 });

    document.querySelectorAll('.reveal-on-scroll').forEach((el) => revealObserver.observe(el));
}

// 6. Interactive Fan Zone "Choose Your Side" & Predictions
window.castFanVote = function(teamName, buttonEl) {
    window.esportsAudio?.playPower();
    const parentCard = buttonEl.closest('.vote-card');
    if (!parentCard) return;

    const countEl = parentCard.querySelector('.vote-count');
    if (countEl) {
        let current = parseInt(countEl.getAttribute('data-count') || '0', 10);
        current += 1;
        countEl.innerText = current.toLocaleString() + ' HYPES';
        countEl.setAttribute('data-count', current);
    }

    buttonEl.innerHTML = '<span>✓ HYPED</span>';
    buttonEl.classList.remove('btn-secondary-action');
    buttonEl.classList.add('btn-primary-action');
    buttonEl.disabled = true;
};

// 7. Subtle 3D Card Tilt
function initCardTilt() {
    const cards = document.querySelectorAll('.tilt-card');
    cards.forEach((card) => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -4;
            const rotateY = ((x - centerX) / centerX) * 4;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-3px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0px)';
        });
    });
}

// 8. Cinematic Trailer Video Modal Controller
window.openTrailerModal = function() {
    window.esportsAudio?.playPower();
    const modal = document.getElementById('trailer-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
};

window.closeTrailerModal = function() {
    const modal = document.getElementById('trailer-modal');
    if (modal) {
        modal.classList.add('hidden');
        // Stop video if iframe
        const iframe = modal.querySelector('iframe');
        if (iframe) {
            const src = iframe.src;
            iframe.src = src;
        }
    }
};

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        window.closeTrailerModal();
        const sponsorModal = document.getElementById('sponsor-modal');
        if (sponsorModal) sponsorModal.classList.add('hidden');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    initKineticParticles();
    initCardSpotlights();
    initCountdown();
    initAnimatedCounters();
    initNavbarAndScroll();
    initCardTilt();

    document.querySelectorAll('a, button').forEach((btn) => {
        btn.addEventListener('click', () => {
            window.esportsAudio?.playClick();
        });
    });
});

