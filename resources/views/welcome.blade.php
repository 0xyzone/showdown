<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Outlaw Showdown 2026 Vol-I | Nepal's Ultimate Esports Championship</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .font-mono-cyber {
            font-family: 'Space Grotesk', monospace;
        }
    </style>
</head>
<body class="cyber-bg text-slate-100 min-h-screen selection:bg-emerald-500 selection:text-slate-950 overflow-x-hidden antialiased relative">

    <!-- DYNAMIC BACKGROUND CANVAS PARTICLES -->
    <canvas id="particle-canvas" class="fixed inset-0 pointer-events-none z-0 opacity-40"></canvas>

    <!-- SCANLINE EFFECT OVERLAY -->
    <div class="fixed inset-0 scanlines pointer-events-none z-40 opacity-30"></div>

    <!-- TOP NAVIGATION -->
    <nav class="sticky top-0 z-50 backdrop-blur-xl bg-slate-950/85 border-b border-emerald-500/25 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- LOGO -->
                <a href="#" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 clip-corner-sm bg-gradient-to-br from-emerald-400 via-emerald-500 to-teal-600 flex items-center justify-center font-black text-slate-950 text-xl group-hover:scale-110 transition-transform duration-300 shadow-[0_0_25px_rgba(16,185,129,0.6)]">
                        OS
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-xl sm:text-2xl tracking-wider text-white group-hover:text-emerald-400 transition-colors">
                                OUTLAW<span class="text-emerald-400">SHOWDOWN</span>
                            </span>
                            <span class="hidden sm:inline-flex px-2 py-0.5 rounded text-[10px] font-mono-cyber font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 animate-pulse">LIVE</span>
                        </div>
                        <div class="text-[10px] tracking-widest text-emerald-400/90 uppercase font-mono-cyber font-bold">2026 • VOL-I • ANNUAL ARENA</div>
                    </div>
                </a>

                <!-- NAV LINKS -->
                <div class="hidden lg:flex items-center gap-8 text-sm font-semibold tracking-wide">
                    <a href="#games" class="text-slate-300 hover:text-emerald-400 transition-colors flex items-center gap-1.5 hover:scale-105 transform">
                        <span>🎮</span> Game Titles
                    </a>
                    <a href="#hub" class="text-slate-300 hover:text-emerald-400 transition-colors flex items-center gap-1.5 hover:scale-105 transform">
                        <span>🏆</span> Challonge Brackets
                    </a>
                    <a href="#sponsors" class="text-slate-300 hover:text-emerald-400 transition-colors flex items-center gap-1.5 hover:scale-105 transform">
                        <span>⚡</span> Sponsors
                    </a>
                    <a href="#partners" class="text-slate-300 hover:text-emerald-400 transition-colors flex items-center gap-1.5 hover:scale-105 transform">
                        <span>🤝</span> Partners
                    </a>
                </div>

                <!-- CTA & AUTH BUTTONS -->
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-950/60 border border-emerald-500/40 text-xs font-mono-cyber text-emerald-300">
                        <span class="text-slate-400">FEE:</span> <span class="font-bold text-white">Rs. 100</span> / person
                    </div>

                    @auth('participant')
                        <a href="{{ url('/mukhyadwar') }}" class="px-5 py-2.5 rounded-lg font-bold text-sm bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-[0_0_25px_rgba(16,185,129,0.5)] transition-all duration-300 hover:scale-105 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Mukhyadwar Arena
                        </a>
                    @else
                        <a href="{{ url('/mukhyadwar/login') }}" class="hidden sm:inline-flex text-slate-300 hover:text-white font-semibold text-sm px-4 py-2 transition-colors">
                            Player Log In
                        </a>
                        <a href="{{ url('/mukhyadwar/register') }}" class="px-6 py-2.5 clip-corner-sm bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-extrabold text-sm uppercase tracking-wider shadow-[0_0_30px_rgba(16,185,129,0.6)] transition-all duration-300 hover:scale-105">
                            Mukhyadwar Portal
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- 1. HERO SECTION -->
    <section class="relative pt-12 pb-24 lg:pt-20 lg:pb-36 overflow-hidden z-10">
        <div class="absolute top-1/3 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-emerald-500/15 rounded-full blur-[160px] pointer-events-none animate-pulse-glow"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <div class="lg:col-span-7 text-center lg:text-left reveal-left">
                    <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-emerald-950/70 border border-emerald-500/40 text-emerald-300 text-xs sm:text-sm font-mono-cyber tracking-widest uppercase mb-6 shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>FEATURED EVENT: {{ strtoupper($activeTournament?->name ?? 'OUTLAW SHOWDOWN 2026') }}</span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-white uppercase leading-[0.95] mb-6">
                        UNLEASH THE <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 via-emerald-400 to-teal-200 cyber-glow-text">LEGEND</span>
                        <br><span class="text-emerald-400">CLAIM YOUR GLORY</span>
                    </h1>

                    <p class="text-slate-300 text-base sm:text-lg lg:text-xl font-normal leading-relaxed mb-8 max-w-xl mx-auto lg:mx-0">
                        Nepal’s premier esports championship stage is live! Register your squad for multi-game title disciplines and follow live brackets on Challonge.com.
                    </p>

                    <div class="p-5 rounded-2xl esports-card border border-emerald-500/40 mb-8 max-w-lg mx-auto lg:mx-0 grid grid-cols-2 gap-4">
                        <div class="p-2 border-r border-emerald-500/20">
                            <div class="text-xs text-emerald-400 font-mono-cyber uppercase tracking-wider font-bold">ENTRY FEE</div>
                            <div class="text-2xl sm:text-3xl font-black text-white mt-1">Rs. 100 <span class="text-xs text-slate-400 font-normal">/ player</span></div>
                        </div>
                        <div class="p-2">
                            <div class="text-xs text-emerald-400 font-mono-cyber uppercase tracking-wider font-bold">PRIZE POOL</div>
                            <div class="text-2xl sm:text-3xl font-black text-emerald-400 mt-1">Rs. {{ number_format($activeTournament?->prize_pool_total ?? 500000) }}</div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-10">
                        <a href="{{ url('/mukhyadwar/register') }}" onclick="playCyberSound()" class="w-full sm:w-auto px-8 py-4 clip-corner bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-lg uppercase tracking-wider shadow-[0_0_35px_rgba(16,185,129,0.6)] transition-all duration-300 hover:scale-105 flex items-center justify-center gap-3">
                            <span>Register Your Team</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="w-full sm:w-auto px-8 py-4 clip-corner bg-slate-900/90 hover:bg-slate-800 border border-emerald-500/40 text-emerald-400 font-extrabold text-lg uppercase tracking-wider transition-all duration-300 hover:scale-105 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Sponsor Query</span>
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-5 relative reveal-right" id="hero-mascot-container">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <div class="relative z-10 animate-float-hero">
                            <div class="absolute inset-0 bg-emerald-500/20 rounded-full blur-[80px] -z-10"></div>
                            <img src="/images/outlaw_hero_mascot.png" alt="Outlaw Showdown Mascot" class="w-full h-auto rounded-3xl border border-emerald-500/40 shadow-[0_0_60px_rgba(16,185,129,0.5)] transform hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. GAME TITLES SECTION -->
    <section class="py-24 relative bg-slate-950/80 border-t border-emerald-500/20" id="games">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal-on-scroll">
                <span class="px-4 py-1.5 rounded-full bg-emerald-950 border border-emerald-500/40 text-emerald-400 text-xs font-mono-cyber uppercase font-bold tracking-widest">MULTIPLE GAME TITLE DISCIPLINES</span>
                <h2 class="text-3xl sm:text-5xl font-black uppercase text-white tracking-tight mt-4">
                    TOURNAMENT <span class="text-emerald-400">GAME TITLES</span>
                </h2>
                <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto mt-2">
                    Multiple game titles assigned to {{ $activeTournament?->name ?? 'Outlaw Showdown' }}. Select your battlefield and enter!
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($gameTitles as $game)
                    <div class="reveal-on-scroll rounded-3xl esports-card p-7 border border-emerald-500/30 hover:border-emerald-400 transition-all duration-500 hover:-translate-y-3 group relative overflow-hidden">
                        <div class="flex justify-between items-start mb-6">
                            <span class="px-3.5 py-1 clip-corner-sm bg-emerald-500/20 text-emerald-300 font-mono-cyber text-xs uppercase font-bold border border-emerald-500/40">{{ strtoupper(str_replace('_', ' ', $game->game_type)) }}</span>
                            <span class="text-xs font-mono-cyber text-slate-300 font-bold">Rs. 100 / Person</span>
                        </div>
                        <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">🎮</div>
                        <h3 class="text-2xl font-extrabold text-white group-hover:text-emerald-400 transition-colors mb-3">{{ $game->name }}</h3>
                        <p class="text-slate-400 text-sm mb-6 leading-relaxed">Official esports discipline powered by {{ $game->developer ?? 'Outlaw Operations' }}.</p>
                        <div class="flex items-center justify-between pt-5 border-t border-emerald-500/20">
                            <span class="text-xs font-mono-cyber text-emerald-400">Official Arena</span>
                            <a href="{{ url('/mukhyadwar/register') }}" onclick="playCyberSound()" class="px-4 py-2 clip-corner-sm bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider flex items-center gap-1 transition-all">Register Team</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 3. CHALLONGE INTERACTIVE BRACKETS ARENA -->
    <section class="py-24 relative" id="hub">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6 reveal-on-scroll">
                <div>
                    <div class="text-xs font-mono-cyber text-emerald-400 uppercase tracking-widest mb-2">POWERED BY CHALLONGE.COM API</div>
                    <h2 class="text-3xl sm:text-5xl font-black uppercase text-white">LIVE MATCH <span class="text-emerald-400">BRACKETS</span></h2>
                </div>
                @if($activeTournament?->challonge_url)
                    <a href="{{ $activeTournament->challonge_url }}" target="_blank" class="px-6 py-3 rounded-xl bg-slate-900 border border-emerald-500/40 text-emerald-400 hover:text-white font-extrabold text-xs uppercase tracking-wider flex items-center gap-2 transition-all">
                        <span>Open Full Challonge Bracket</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                @endif
            </div>

            <div class="reveal-on-scroll rounded-3xl esports-card border border-emerald-500/40 overflow-hidden shadow-[0_0_50px_rgba(16,185,129,0.2)]">
                <div class="bg-emerald-950/80 px-6 py-4 border-b border-emerald-500/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="font-extrabold text-white text-sm tracking-wider uppercase">Official Challonge Tie-Sheet & Bracket Module</span>
                    </div>
                    <span class="text-xs font-mono-cyber text-emerald-400">CHALLONGE.COM INTEGRATION</span>
                </div>
                <div class="w-full bg-slate-950 overflow-hidden min-h-[550px] relative">
                    <iframe src="{{ $challongeEmbedUrl }}" width="100%" height="600" frameborder="0" scrolling="auto" allowtransparency="true" class="w-full h-[600px] border-0"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. OFFICIAL SPONSORS SECTION -->
    <section class="py-24 relative bg-slate-950/90 border-y border-emerald-500/30 overflow-hidden" id="sponsors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal-on-scroll">
                <span class="px-4 py-1.5 rounded-full bg-emerald-950 border border-emerald-500/40 text-emerald-400 text-xs font-mono-cyber uppercase font-bold tracking-widest">
                    {{ strtoupper($activeTournament?->name ?? 'OUTLAW SHOWDOWN') }} SPONSOR LINEUP
                </span>
                <h2 class="text-3xl sm:text-5xl font-black uppercase text-white tracking-tight mt-4">
                    TOURNAMENT <span class="text-emerald-400">SPONSORS</span>
                </h2>
            </div>

            @php
                $titleSponsors = $sponsors->where('level', 'title');
            @endphp

            @if($titleSponsors->count() > 0)
                <div class="mb-16 reveal-on-scroll">
                    <div class="grid grid-cols-1 max-w-2xl mx-auto">
                        @foreach($titleSponsors as $sponsor)
                            <a href="{{ $sponsor->website_url ?? '#' }}" target="_blank" class="p-8 rounded-3xl esports-card border-2 border-amber-500/60 text-center flex flex-col items-center justify-center gap-4 group hover:scale-105 transition-transform">
                                <img src="{{ $sponsor->logo_url ? Storage::url($sponsor->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $sponsor->name }}" class="max-h-28 max-w-[240px] object-contain rounded-xl">
                                <div class="text-xl font-black text-amber-400 tracking-wider uppercase">{{ $sponsor->name }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- 5. MEDIA & EVENT PARTNERS SECTION -->
    <section class="py-20 relative bg-slate-950 border-b border-emerald-500/20" id="partners">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 reveal-on-scroll">
                <span class="px-4 py-1 rounded-full bg-emerald-950 text-emerald-400 text-xs font-mono-cyber uppercase font-bold">EVENT PARTNERSHIPS</span>
                <h2 class="text-3xl font-black uppercase text-white tracking-tight mt-2">MEDIA & EVENT <span class="text-emerald-400">PARTNERS</span></h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                @foreach($partners as $partner)
                    <a href="{{ $partner->website_url ?? '#' }}" target="_blank" class="p-6 rounded-2xl esports-card border border-emerald-500/30 text-center flex flex-col items-center justify-center gap-3 hover:border-emerald-400 transition-colors">
                        <img src="{{ $partner->logo_url ? Storage::url($partner->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $partner->name }}" class="max-h-14 max-w-[160px] object-contain rounded-lg">
                        <span class="text-base font-extrabold text-white">{{ $partner->name }}</span>
                        <span class="px-3 py-1 rounded text-xs font-mono-cyber font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">{{ $partner->title }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. SPONSORSHIP INQUIRY SECTION & LIVEWIRE MODAL -->
    <section class="py-24 relative bg-gradient-to-b from-slate-950 to-slate-900 border-t border-emerald-500/20" id="sponsor-query-section">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-on-scroll">
            <span class="px-4 py-1.5 rounded-full bg-emerald-950 border border-emerald-500/40 text-emerald-400 text-xs font-mono-cyber uppercase font-bold tracking-widest">BRAND PARTNERSHIPS</span>
            <h2 class="text-3xl sm:text-5xl font-black uppercase text-white tracking-tight mt-4 mb-6">
                PARTNER WITH <span class="text-emerald-400">{{ strtoupper($activeTournament?->name ?? 'OUTLAW SHOWDOWN') }}</span>
            </h2>
            <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto mb-10">
                Connect your brand directly with thousands of competitive gamers, youth audiences, and esports enthusiasts across Nepal!
            </p>
            <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="px-10 py-5 clip-corner bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-lg uppercase tracking-wider shadow-[0_0_40px_rgba(16,185,129,0.6)] transition-all duration-300 hover:scale-105">
                Send Sponsorship Query
            </button>
        </div>
    </section>

    <!-- LIVEWIRE SPONSOR QUERY MODAL -->
    <div id="sponsor-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-xl hidden">
        <div class="relative w-full max-w-lg rounded-3xl esports-card p-8 border border-emerald-500/50 shadow-[0_0_60px_rgba(16,185,129,0.4)]">
            <button onclick="document.getElementById('sponsor-modal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-white text-2xl font-bold">✕</button>
            
            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-3xl mx-auto mb-3">🤝</div>
                <h3 class="text-2xl font-black text-white uppercase">Sponsor Outlaw Showdown</h3>
                <p class="text-slate-400 text-xs mt-1">Submit your partnership query and join our esports sponsor lineup!</p>
            </div>

            <livewire:sponsor-query-form />
        </div>
    </div>

    <!-- 7. FOOTER -->
    <footer class="bg-slate-950 border-t border-emerald-500/20 py-12 text-slate-400 text-sm relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 clip-corner-sm bg-emerald-500 flex items-center justify-center font-black text-slate-950 text-base">OS</div>
                <span class="font-extrabold text-white tracking-wider text-base">OUTLAW SHOWDOWN 2026</span>
            </div>
            <div class="text-xs font-mono-cyber text-slate-400">
                © 2026 Outlaw Showdown Vol-I. All Rights Reserved. Tournament Fee: Rs. 100 / person.
            </div>
            <div class="flex items-center gap-5 text-emerald-400 font-bold text-xs">
                <a href="#games" class="hover:underline">Games</a>
                <a href="#hub" class="hover:underline">Results</a>
                <a href="#sponsors" class="hover:underline">Sponsors</a>
            </div>
        </div>
    </footer>

    @livewireScripts
    <script>
        function playCyberSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(800, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(300, ctx.currentTime + 0.08);
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.08);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.08);
            } catch(e) {}
        }
    </script>
</body>
</html>
