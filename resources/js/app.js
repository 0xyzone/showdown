// Esports Platform Interactive Client Engine
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
            osc.frequency.setValueAtTime(600, now);
            osc.frequency.exponentialRampToValueAtTime(300, now + 0.06);
            gain.gain.setValueAtTime(0.08, now);
            gain.gain.linearRampToValueAtTime(0.001, now + 0.06);
            osc.start(now);
            osc.stop(now + 0.06);
        } catch (e) {}
    }
}

window.esportsAudio = new EsportsAudio();

// Dynamic Countdown Timer Engine
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

// Scroll Spy & Navbar Shadow
function initNavbarAndScroll() {
    const navbar = document.getElementById('floating-navbar');
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-spy-link');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            navbar?.classList.add('scrolled');
        } else {
            navbar?.classList.remove('scrolled');
        }
    });

    // Intersection Observer for Scrollspy
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

    // Reveal on scroll
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal-on-scroll').forEach((el) => revealObserver.observe(el));
}

document.addEventListener('DOMContentLoaded', () => {
    initCountdown();
    initNavbarAndScroll();

    document.querySelectorAll('a, button').forEach((btn) => {
        btn.addEventListener('click', () => {
            window.esportsAudio.playClick();
        });
    });
});
