// Modern Esports Cyber Interactive JavaScript Controller

class AudioEngine {
    constructor() {
        this.ctx = null;
        this.enabled = true;
    }

    initCtx() {
        if (!this.ctx) {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) {
                this.ctx = new AudioCtx();
            }
        }
        if (this.ctx && this.ctx.state === 'suspended') {
            this.ctx.resume();
        }
    }

    playSound(type) {
        if (!this.enabled) return;
        try {
            this.initCtx();
            if (!this.ctx) return;

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
            } else if (type === 'hit') {
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(520, now);
                osc.frequency.exponentialRampToValueAtTime(1040, now + 0.12);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.linearRampToValueAtTime(0.01, now + 0.12);
                osc.start(now);
                osc.stop(now + 0.12);
            } else if (type === 'miss') {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(180, now);
                osc.frequency.linearRampToValueAtTime(90, now + 0.15);
                gain.gain.setValueAtTime(0.2, now);
                gain.gain.linearRampToValueAtTime(0.01, now + 0.15);
                osc.start(now);
                osc.stop(now + 0.15);
            } else if (type === 'respawn') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(300, now);
                osc.frequency.exponentialRampToValueAtTime(1200, now + 0.35);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.linearRampToValueAtTime(0.01, now + 0.35);
                osc.start(now);
                osc.stop(now + 0.35);
            }
        } catch (e) {
            // Audio context fallback safeguard
        }
    }
}

window.esportsAudio = new AudioEngine();

// Particle Grid Background System
function initCyberGrid() {
    const canvas = document.getElementById('cyber-canvas');
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

        // Draw connecting lines
        for (let i = 0; i < particles.length; i++) {
            const p1 = particles[i];
            p1.x += p1.vx;
            p1.y += p1.vy;
            p1.pulse += 0.03;

            if (p1.x < 0 || p1.x > width) p1.vx *= -1;
            if (p1.y < 0 || p1.y > height) p1.vy *= -1;

            ctx.beginPath();
            ctx.arc(p1.x, p1.y, p1.size, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(52, 211, 153, ${0.4 + Math.sin(p1.pulse) * 0.2})`;
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
                    ctx.strokeStyle = `rgba(16, 185, 129, ${0.25 * (1 - dist / 130)})`;
                    ctx.lineWidth = 0.8;
                    ctx.stroke();
                }
            }
        }

        requestAnimationFrame(draw);
    }

    draw();
}

// Esports Aim Trainer Mini Game
function initEsportsAimLab() {
    const arena = document.getElementById('esports-aim-arena');
    if (!arena) return;

    const scoreEl = document.getElementById('aim-score');
    const streakEl = document.getElementById('aim-streak');
    const apmEl = document.getElementById('aim-apm');
    const highScoreEl = document.getElementById('aim-high-score');
    const resetBtn = document.getElementById('aim-reset-btn');

    let score = 0;
    let streak = 0;
    let totalClicks = 0;
    let startTime = null;
    let highScore = parseInt(localStorage.getItem('esports_aim_highscore') || '0', 10);

    if (highScoreEl) highScoreEl.innerText = highScore;

    function updateStats() {
        if (scoreEl) scoreEl.innerText = score;
        if (streakEl) streakEl.innerText = streak;

        if (startTime && apmEl) {
            const elapsedSeconds = Math.max((Date.now() - startTime) / 1000, 1);
            const apm = Math.round((totalClicks / elapsedSeconds) * 60);
            apmEl.innerText = apm;
        }

        if (score > highScore) {
            highScore = score;
            localStorage.setItem('esports_aim_highscore', highScore);
            if (highScoreEl) highScoreEl.innerText = highScore;
        }
    }

    function spawnTarget() {
        arena.querySelectorAll('.cyber-target').forEach((t) => t.remove());

        const target = document.createElement('button');
        target.className =
            'cyber-target absolute rounded-full border-2 border-emerald-400 bg-emerald-500/30 flex items-center justify-center cursor-pointer transition-transform hover:scale-110 animate-pulse active:scale-95 shadow-[0_0_20px_#10b981]';

        const size = Math.floor(Math.random() * 20) + 40;
        target.style.width = `${size}px`;
        target.style.height = `${size}px`;

        const maxX = arena.clientWidth - size - 20;
        const maxY = arena.clientHeight - size - 20;

        const left = Math.max(10, Math.floor(Math.random() * maxX));
        const top = Math.max(10, Math.floor(Math.random() * maxY));

        target.style.left = `${left}px`;
        target.style.top = `${top}px`;

        target.innerHTML = `
            <div class="w-3 h-3 rounded-full bg-emerald-300 shadow-[0_0_10px_#34d399]"></div>
            <div class="absolute inset-0 rounded-full border border-emerald-300/40 animate-ping"></div>
        `;

        target.addEventListener('click', (e) => {
            e.stopPropagation();
            if (!startTime) startTime = Date.now();
            totalClicks++;
            score += 100 + streak * 10;
            streak++;
            window.esportsAudio.playSound('hit');
            updateStats();
            spawnTarget();
        });

        arena.appendChild(target);
    }

    arena.addEventListener('click', (e) => {
        if (e.target === arena || e.target.classList.contains('arena-bg')) {
            if (!startTime) startTime = Date.now();
            totalClicks++;
            streak = 0;
            window.esportsAudio.playSound('miss');
            updateStats();
        }
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            score = 0;
            streak = 0;
            totalClicks = 0;
            startTime = null;
            if (apmEl) apmEl.innerText = '0';
            updateStats();
            window.esportsAudio.playSound('respawn');
            spawnTarget();
        });
    }

    spawnTarget();
}

// Global UI Sound & Interactivity Initializer
document.addEventListener('DOMContentLoaded', () => {
    initCyberGrid();
    initEsportsAimLab();

    document.querySelectorAll('a, button').forEach((btn) => {
        btn.addEventListener('mouseenter', () => {
            window.esportsAudio.playSound('click');
        });
    });

    const soundToggle = document.getElementById('sound-toggle-btn');
    if (soundToggle) {
        soundToggle.addEventListener('click', () => {
            window.esportsAudio.enabled = !window.esportsAudio.enabled;
            soundToggle.classList.toggle('text-emerald-400', window.esportsAudio.enabled);
            soundToggle.classList.toggle('text-gray-500', !window.esportsAudio.enabled);
            window.esportsAudio.playSound('click');
        });
    }
});
