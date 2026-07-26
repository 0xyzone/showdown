<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $activeTournament?->name ?? 'Outlaw Showdown 2026' }} | Nepal's Premier Esports Championship</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @php
        $themeColor = $activeTournament?->theme_color ?? '#00f0ff';

        // Convert Hex to RGB for full opacity blending
        $hex = ltrim($themeColor, '#');
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = "$r, $g, $b";

        $tournamentName = $activeTournament?->name ?? 'OUTLAW SHOWDOWN 2026';
        $seasonVersion = $activeTournament?->season_version ?? '2026 VOL-I';
        $entryFee = $activeTournament?->entry_fee ?? 100;
        $entryFeeSuffix = $activeTournament?->entry_fee_suffix ?: 'person';
        $prizePool = $activeTournament?->prize_pool_total ?? 500000;
        $heroHeadline = $activeTournament?->hero_headline ?: 'OUTLAW SHOWDOWN';
        $heroSubheadline = $activeTournament?->hero_subheadline ?: "Outlaw Showdown 2026 Vol-I inaugurate premier esports stage with Laravel & Filament v5 backend.";
        $registrationEnd = $activeTournament?->registration_end ? $activeTournament->registration_end->toIso8601String() : now()->addDays(10)->toIso8601String();
    @endphp

    <style>
        :root {
            --accent-hex: {{ $themeColor }};
            --accent-rgb: {{ $rgb }};
            --color-emerald-500: {{ $themeColor }};
            --color-emerald-400: {{ $themeColor }};
            --color-emerald-300: {{ $themeColor }};
            --color-emerald-600: {{ $themeColor }};
        }
        body {
            font-family: 'Outfit', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Persistent Light Mode */
        html.light body {
            background-color: #f8fafc;
            color: #0f172a;
        }
        html.light .esports-card-v2 {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(0, 240, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        html.light .nav-bg {
            background-color: rgba(255, 255, 255, 0.92);
        }
    </style>
</head>
<body class="cyber-bg text-slate-100 min-h-screen selection:bg-cyan-400 selection:text-slate-950 overflow-x-hidden antialiased relative">

    <!-- REACTIVE CURSOR TRAIL -->
    <div id="cursor-dot"></div>
    <div id="cursor-ring"></div>

    <!-- DYNAMIC PARTICLES BACKGROUND CANVAS -->
    <canvas id="particle-canvas" class="fixed inset-0 pointer-events-none z-0 opacity-40"></canvas>

    <!-- SCANLINE OVERLAY -->
    <div class="fixed inset-0 scanlines pointer-events-none z-40 opacity-20"></div>

    <!-- 1. TOP NAVIGATION BAR (MATCHING REFERENCE IMAGE) -->
    <nav class="sticky top-0 z-50 backdrop-blur-2xl nav-bg bg-slate-950/90 border-b border-cyan-500/30 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4 overflow-hidden">
                
                <!-- BRAND LOGO -->
                <a href="#" class="flex items-center gap-2.5 group shrink-0">
                    <div class="w-10 h-10 clip-corner-sm btn-primary-cyan flex items-center justify-center font-black text-slate-950 text-lg group-hover:scale-105 transition-transform duration-300">
                        OS
                    </div>
                    <div class="flex flex-col min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="font-orbitron font-black text-lg sm:text-xl tracking-wider text-white whitespace-nowrap">
                                OUTLAW<span class="text-neon-cyan">SHOWDOWN</span>
                            </span>
                            <span class="hidden sm:inline-flex px-1.5 py-0.5 rounded text-[9px] font-mono-cyber font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 animate-pulse whitespace-nowrap">LIVE</span>
                        </div>
                    </div>
                </a>

                <!-- DESKTOP NAV LINKS -->
                <div class="hidden lg:flex items-center gap-6 xl:gap-8 text-sm font-extrabold tracking-wide shrink-0">
                    <a href="#" class="text-cyan-400 font-bold border-b-2 border-cyan-400 pb-1 flex items-center gap-1.5 whitespace-nowrap">
                        Overview
                    </a>
                    <a href="#games" class="text-slate-300 hover:text-cyan-400 transition-colors flex items-center gap-1.5 whitespace-nowrap hover:scale-105 transform">
                        Esports
                    </a>
                    <a href="{{ url('/mukhyadwar') }}" class="text-slate-300 hover:text-cyan-400 transition-colors flex items-center gap-1.5 whitespace-nowrap hover:scale-105 transform">
                        Players
                    </a>
                    <a href="{{ url('/mukhyadwar') }}" class="text-slate-300 hover:text-cyan-400 transition-colors flex items-center gap-1.5 whitespace-nowrap hover:scale-105 transform">
                        Managers
                    </a>
                    <a href="#community" class="text-slate-300 hover:text-cyan-400 transition-colors flex items-center gap-1.5 whitespace-nowrap hover:scale-105 transform">
                        Contact
                    </a>
                </div>

                <!-- DESKTOP CTA & THEME TOGGLE BUTTONS -->
                <div class="hidden lg:flex items-center gap-3 shrink-0">
                    <button id="theme-toggle-btn" class="p-2.5 rounded-xl bg-slate-900/80 border border-cyan-500/30 text-slate-200 hover:text-cyan-400 focus:outline-none transition-colors" title="Toggle Light / Dark Mode">
                        <span id="theme-sun-icon" class="hidden">☀️</span>
                        <span id="theme-moon-icon">🌙</span>
                    </button>

                    @auth('participant')
                        <a href="{{ url('/mukhyadwar') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm btn-primary-cyan transition-all hover:scale-105 flex items-center gap-2 whitespace-nowrap">
                            Mukhyadwar Arena
                        </a>
                    @else
                        <a href="{{ url('/mukhyadwar/login') }}" class="font-bold text-sm text-slate-300 hover:text-white px-3 py-2 transition-colors whitespace-nowrap">
                            Log in
                        </a>
                        <a href="{{ url('/mukhyadwar/register') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-800 border border-cyan-500/40 text-white hover:bg-slate-700 transition-all hover:scale-105 whitespace-nowrap">
                            Sign Up
                        </a>
                    @endauth
                </div>

                <!-- MOBILE MENU TOGGLE -->
                <div class="flex lg:hidden items-center gap-2.5 shrink-0">
                    <button id="theme-toggle-btn-mobile" class="p-2.5 rounded-xl bg-slate-900 border border-cyan-500/30 text-slate-200">
                        <span>🌓</span>
                    </button>
                    <button id="mobile-menu-toggle" class="p-2.5 rounded-xl bg-slate-900 border border-cyan-500/30 text-cyan-400 focus:outline-none transition-colors">
                        <svg id="hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- MOBILE MENU DRAWER -->
        <div id="mobile-menu-drawer" class="hidden lg:hidden bg-slate-950/95 border-b border-cyan-500/30 backdrop-blur-2xl px-4 pt-4 pb-6 space-y-4 transition-all">
            <div class="flex flex-col gap-3 font-bold text-sm tracking-wide">
                <a href="#" onclick="toggleMobileMenu()" class="px-4 py-2.5 rounded-lg hover:bg-slate-900 text-cyan-400">Overview</a>
                <a href="#games" onclick="toggleMobileMenu()" class="px-4 py-2.5 rounded-lg hover:bg-slate-900 text-slate-200">Esports</a>
                <a href="{{ url('/mukhyadwar') }}" class="px-4 py-2.5 rounded-lg hover:bg-slate-900 text-slate-200">Players & Managers</a>
                <a href="#community" onclick="toggleMobileMenu()" class="px-4 py-2.5 rounded-lg hover:bg-slate-900 text-slate-200">Contact</a>
            </div>

            <div class="pt-4 border-t border-slate-800 flex flex-col gap-3">
                @auth('participant')
                    <a href="{{ url('/mukhyadwar') }}" class="w-full py-3 rounded-xl text-center font-bold text-sm btn-primary-cyan">
                        Mukhyadwar Arena
                    </a>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ url('/mukhyadwar/login') }}" class="py-2.5 rounded-lg text-center font-semibold text-sm bg-slate-900 border border-cyan-500/30">
                            Log in
                        </a>
                        <a href="{{ url('/mukhyadwar/register') }}" class="py-2.5 rounded-lg text-center font-bold text-sm bg-slate-800 border border-cyan-500/40 text-white">
                            Sign Up
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- 2. HERO SECTION: "THE SHOWDOWN ARENA" (REGENERATED TRANSPARENT CHIBI MASCOT OVERLAYS) -->
    <section class="relative pt-10 pb-20 lg:pt-16 lg:pb-28 overflow-hidden z-10">
        <!-- Dynamic Glow Aura -->
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] rounded-full blur-[180px] pointer-events-none animate-pulse-glow" style="background-color: rgba(0, 240, 255, 0.15);"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="relative min-h-[520px] rounded-3xl esports-card-v2 p-8 sm:p-12 border-2 border-cyan-500/40 overflow-visible">
                
                <!-- NEW REGENERATED FLOATING TRANSPARENT MASCOT 1: CYBER SNIPER GIRL (TOP-RIGHT MATCHING REFERENCE) -->
                <div class="absolute -top-14 -right-6 sm:-right-10 z-20 pointer-events-auto reveal-right animate-float-left" data-parallax-speed="18">
                    <img src="/images/cyber_chibi_sniper_girl.png" alt="Cyber Chibi Sniper Girl" class="w-56 sm:w-80 lg:w-[420px] h-auto object-contain filter drop-shadow-[0_10px_35px_rgba(255,0,85,0.55)] transform hover:scale-105 transition-transform duration-300">
                </div>

                <!-- NEW REGENERATED FLOATING TRANSPARENT MASCOT 2: MECHA ROBOT DRAGON (BOTTOM-LEFT MATCHING REFERENCE) -->
                <div class="absolute -bottom-10 -left-6 sm:-left-10 z-20 pointer-events-auto reveal-left animate-float-right" data-parallax-speed="-14">
                    <img src="/images/mecha_chibi_robot_dragon.png" alt="Mecha Robot Dragon Mascot" class="w-48 sm:w-64 lg:w-[320px] h-auto object-contain filter drop-shadow-[0_10px_35px_rgba(0,240,255,0.55)] transform hover:scale-105 transition-transform duration-300">
                </div>

                <!-- HERO INNER CENTER CONTENT -->
                <div class="max-w-2xl mx-auto text-center relative z-10 py-6">
                    <div class="text-xs font-mono-cyber font-bold text-slate-400 uppercase tracking-widest mb-3">
                        The Showdown Arena
                    </div>

                    <!-- Main Dual Neon Title matching reference image -->
                    <h1 class="font-orbitron text-4xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight leading-none mb-6">
                        <span class="text-stroke-cyan block mb-2">OUTLAW</span>
                        <span class="text-stroke-pink block">SHOWDOWN</span>
                    </h1>

                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed mb-8 max-w-lg mx-auto">
                        {{ $heroSubheadline }}
                    </p>

                    <!-- Countdown Timer matching reference layout -->
                    <div id="countdown-target" data-date="{{ $registrationEnd }}" class="mb-8 p-4 rounded-2xl bg-slate-950/80 border border-cyan-500/30 max-w-sm mx-auto shadow-xl">
                        <div class="text-[10px] font-mono-cyber font-bold text-slate-400 uppercase tracking-widest mb-2">Countdown Timer</div>
                        <div class="grid grid-cols-4 gap-2 font-orbitron text-center">
                            <div class="p-2 rounded-xl bg-slate-900 border border-slate-800">
                                <div id="cd-days" class="text-lg sm:text-2xl font-black text-white">00</div>
                                <div class="text-[9px] font-mono-cyber text-slate-400 mt-0.5">Days</div>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-900 border border-slate-800">
                                <div id="cd-hours" class="text-lg sm:text-2xl font-black text-white">06</div>
                                <div class="text-[9px] font-mono-cyber text-slate-400 mt-0.5">Hour</div>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-900 border border-slate-800">
                                <div id="cd-mins" class="text-lg sm:text-2xl font-black text-white">40</div>
                                <div class="text-[9px] font-mono-cyber text-slate-400 mt-0.5">Hours</div>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-900 border border-slate-800">
                                <div id="cd-secs" class="text-lg sm:text-2xl font-black text-cyan-400">36</div>
                                <div class="text-[9px] font-mono-cyber text-slate-400 mt-0.5">Seconds</div>
                            </div>
                        </div>
                    </div>

                    <!-- Dual CTAs matching reference: /mukhyadwar Primary + Discord Secondary -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ url('/mukhyadwar') }}" onclick="playCyberSound()" class="w-full sm:w-auto px-8 py-3.5 rounded-xl btn-primary-cyan font-bold text-sm tracking-wide transition-all hover:scale-105 flex items-center justify-center gap-2">
                            <span>/mukhyadwar</span>
                        </a>
                        <a href="{{ $activeTournament?->discord_server_url ?: 'https://discord.gg/outlawshowdown' }}" target="_blank" onclick="playCyberSound()" class="w-full sm:w-auto px-8 py-3.5 rounded-xl btn-purple-discord font-bold text-sm tracking-wide transition-all hover:scale-105 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994.021-.041.001-.09-.041-.106a13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128c.126-.093.252-.19.373-.287a.074.074 0 0 1 .078-.01c3.927 1.793 8.18 1.793 12.061 0a.074.074 0 0 1 .079.009c.12.098.245.195.372.288a.077.077 0 0 1-.006.127c-.598.35-1.22.656-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.028z"/></svg>
                            <span>Discord</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 3. LIVE MATCH & SCHEDULE TICKER PANELS (WITH REGENERATED TURTLE & DRAGON MASCOTS) -->
    <section class="py-16 relative" id="ticker">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-left mb-8 reveal-on-scroll">
                <h2 class="font-orbitron text-2xl sm:text-4xl font-black uppercase text-white">
                    Live Match & Schedule Ticker
                </h2>
                <p class="text-slate-400 text-xs sm:text-sm mt-1">
                    Live chibi mascots line-up charm game mascots.
                </p>
            </div>

            <!-- PANELS GRID MATCHING REFERENCE IMAGE -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                
                <!-- NEW REGENERATED TRANSPARENT TURTLE WARRIOR MASCOT OVERLAY ON TICKER CARD EDGE -->
                <div class="absolute -top-16 right-4 z-20 animate-float-left pointer-events-none hidden sm:block">
                    <img src="/images/chibi_turtle_warrior_mascot.png" alt="Turtle Warrior Mascot" class="w-24 h-24 object-contain filter drop-shadow-[0_8px_25px_rgba(16,185,129,0.5)]">
                </div>

                <!-- PANEL 1: MOBA SCORES -->
                <div class="reveal-on-scroll rounded-3xl esports-card-v2 p-6 border border-cyan-500/30 flex items-center justify-between gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-amber-500/40 flex items-center justify-center text-3xl">
                        🔥
                    </div>
                    <div class="text-center">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono-cyber font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 uppercase">MOBA</span>
                        <div class="font-orbitron text-3xl font-black text-white mt-2">1 - 0</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-xl">🛡️</div>
                </div>

                <!-- PANEL 2: MATCH CENTER -->
                <div class="reveal-on-scroll rounded-3xl esports-card-v2 p-6 border border-cyan-500/30 text-center">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                        <span class="text-[11px] font-mono-cyber text-slate-300">Live score</span>
                    </div>
                    <div class="font-orbitron text-4xl font-black text-white mb-2">0 - 0</div>
                    <div class="text-xs text-slate-400 font-mono-cyber">Upcoming fixtures: <span class="text-white font-bold">3 VS 0</span></div>
                </div>

                <!-- PANEL 3: UPCOMING FIXTURES -->
                <div class="reveal-on-scroll rounded-3xl esports-card-v2 p-6 border border-cyan-500/30">
                    <div class="text-xs font-mono-cyber font-bold text-slate-300 mb-3">Upcoming Fixtures</div>
                    <div class="space-y-2 text-xs font-mono-cyber">
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/80">
                            <span class="flex items-center gap-2"><span>🎮</span> MOBA Mascot</span>
                            <span class="font-bold text-cyan-400">1 <span class="text-slate-500">vs</span> 0</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/80">
                            <span class="flex items-center gap-2"><span>🎯</span> FPS Mascot</span>
                            <span class="font-bold text-pink-400">1 <span class="text-slate-500">vs</span> 0</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 4. ACTIVE TOURNAMENT SPOTLIGHT (GAME CARDS MATCHING REFERENCE IMAGE) -->
    <section class="py-16 relative bg-slate-950/80" id="games">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-left mb-10 reveal-on-scroll">
                <h2 class="font-orbitron text-2xl sm:text-4xl font-black uppercase text-white">
                    Active Tournament Spotlight
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($gameTitles as $game)
                    <div class="reveal-on-scroll rounded-3xl esports-card-v2 tilt-card p-6 border border-cyan-500/30 group">
                        <div class="w-full h-44 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-5xl mb-4 group-hover:scale-105 transition-transform overflow-hidden relative">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent z-10"></div>
                            <span>🎮</span>
                        </div>
                        <h3 class="font-orbitron text-lg font-bold text-white mb-1 group-hover:text-cyan-400 transition-colors">{{ $game->name }}</h3>
                        <div class="text-xs text-slate-400 font-mono-cyber capitalize">{{ str_replace('_', ' ', $game->game_type) }}</div>
                    </div>
                @endforeach
            </div>

            <!-- CHALLONGE BRACKETS PREVIEW -->
            <div class="mt-16 reveal-on-scroll rounded-3xl esports-card-v2 border border-cyan-500/30 overflow-hidden" id="hub">
                <div class="bg-slate-900/90 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <span class="font-orbitron text-sm font-bold text-white uppercase">Official Challonge Bracket Module</span>
                    <span class="text-xs font-mono-cyber text-cyan-400">CHALLONGE.COM</span>
                </div>
                <div class="w-full bg-slate-950 overflow-hidden min-h-[500px] relative">
                    <iframe src="{{ $challongeEmbedUrl }}" width="100%" height="500" frameborder="0" scrolling="auto" allowtransparency="true" class="w-full h-[500px] border-0"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. COMMUNITY HUB: "JOIN COMMUNITY" (REGENERATED HOODIE CAT MASCOT OVERLAY MATCHING REFERENCE) -->
    <section class="py-20 relative bg-slate-950 border-t border-cyan-500/30 overflow-hidden" id="community">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            
            <div class="rounded-3xl esports-card-v2 p-10 sm:p-14 border-2 border-cyan-500/40 text-center relative overflow-visible">
                
                <!-- NEW REGENERATED TRANSPARENT HOODIE CAT MASCOT OVERLAY ON RIGHT EDGE (MATCHING REFERENCE IMAGE) -->
                <div class="absolute -bottom-8 -right-8 sm:-right-12 z-20 animate-float-right pointer-events-auto" data-parallax-speed="15">
                    <img src="/images/chibi_hoodie_cat_mascot.png" alt="Chibi Hoodie Cat Mascot" class="w-52 sm:w-72 lg:w-[340px] h-auto object-contain filter drop-shadow-[0_10px_35px_rgba(0,240,255,0.6)] transform hover:scale-105 transition-transform duration-300">
                </div>

                <!-- NEW REGENERATED TRANSPARENT CYBER SNIPER MASCOT OVERLAY ON LEFT EDGE -->
                <div class="absolute -bottom-8 -left-8 sm:-left-12 z-20 animate-float-left pointer-events-auto hidden sm:block" data-parallax-speed="-12">
                    <img src="/images/cyber_chibi_sniper_girl.png" alt="Cyber Chibi Mascot" class="w-48 sm:w-64 lg:w-[280px] h-auto object-contain filter drop-shadow-[0_10px_35px_rgba(255,0,85,0.5)] transform hover:scale-105 transition-transform duration-300">
                </div>

                <!-- CENTER DISCORD CTA CONTENT -->
                <div class="max-w-md mx-auto relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-purple-600/20 border border-purple-500/40 flex items-center justify-center text-3xl mx-auto mb-4 text-purple-400">
                        💬
                    </div>
                    <h2 class="font-orbitron text-3xl sm:text-4xl font-black uppercase text-white mb-2">
                        Join Community
                    </h2>
                    <p class="text-slate-300 text-sm mb-6 font-mono-cyber">
                        Join the official Discord community.
                    </p>
                    <a href="{{ $activeTournament?->discord_server_url ?: 'https://discord.gg/outlawshowdown' }}" target="_blank" onclick="playCyberSound()" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-white text-slate-950 font-bold text-sm hover:bg-slate-200 transition-all hover:scale-105 shadow-xl">
                        <span>Join community</span>
                    </a>
                </div>

            </div>

        </div>
    </section>

    <!-- SPONSORS & PARTNERS LINEUP -->
    <section class="py-16 relative bg-slate-950 border-t border-cyan-500/20" id="sponsors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 reveal-on-scroll">
                <span class="px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-mono-cyber uppercase font-bold tracking-widest">OFFICIAL PARTNERSHIPS</span>
                <h2 class="font-orbitron text-2xl sm:text-4xl font-black uppercase text-white mt-3">Sponsors & Partners</h2>
            </div>

            @if($sponsors->count() > 0 || $partners->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                    @foreach($sponsors as $sponsor)
                        <div class="p-4 rounded-2xl esports-card-v2 border border-cyan-500/30 text-center flex flex-col items-center justify-center gap-2">
                            <img src="{{ $sponsor->logo_url ? Storage::url($sponsor->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $sponsor->name }}" class="max-h-12 max-w-[140px] object-contain rounded-lg">
                            <span class="text-xs font-bold text-white">{{ $sponsor->name }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono-cyber font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 uppercase">Sponsor</span>
                        </div>
                    @endforeach
                    @foreach($partners as $partner)
                        <div class="p-4 rounded-2xl esports-card-v2 border border-cyan-500/30 text-center flex flex-col items-center justify-center gap-2">
                            <img src="{{ $partner->logo_url ? Storage::url($partner->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $partner->name }}" class="max-h-12 max-w-[140px] object-contain rounded-lg">
                            <span class="text-xs font-bold text-white">{{ $partner->name }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono-cyber font-bold dynamic-badge">{{ $partner->title }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="reveal-on-scroll rounded-3xl esports-card-v2 p-8 text-center border border-dashed border-cyan-500/30 max-w-xl mx-auto">
                    <h3 class="font-orbitron text-xl font-bold uppercase mb-2">Become an Official Partner</h3>
                    <p class="text-slate-400 text-xs mb-4">Connect your brand directly with esports fans across Nepal.</p>
                    <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="px-6 py-2.5 rounded-xl btn-primary-cyan text-xs uppercase font-bold">
                        Sponsor Query
                    </button>
                </div>
            @endif
        </div>
    </section>

    <!-- LIVEWIRE SPONSOR QUERY MODAL -->
    <div id="sponsor-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-xl hidden">
        <div class="relative w-full max-w-lg rounded-3xl esports-card-v2 p-8 border border-cyan-500/40 shadow-[0_0_60px_rgba(0,240,255,0.5)]">
            <button onclick="document.getElementById('sponsor-modal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-white text-2xl font-bold">✕</button>
            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-cyan-500/30 flex items-center justify-center text-3xl mx-auto mb-3">🤝</div>
                <h3 class="font-orbitron text-2xl font-black uppercase text-white">Sponsor Outlaw Showdown</h3>
            </div>
            <livewire:sponsor-query-form />
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-950 border-t border-cyan-500/30 py-10 text-slate-400 text-xs font-mono-cyber relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 clip-corner-sm btn-primary-cyan flex items-center justify-center font-black text-slate-950 text-sm">OS</div>
                <span class="font-orbitron font-extrabold text-white text-sm">OUTLAW SHOWDOWN 2026</span>
            </div>
            <div>© 2026 Outlaw Showdown. All Rights Reserved. Entry Fee: Rs. {{ number_format($entryFee) }}/{{ $entryFeeSuffix }}.</div>
        </div>
    </footer>

    @livewireScripts
    <script>
        function initTheme() {
            const savedTheme = localStorage.getItem('app-theme') || 'dark';
            if (savedTheme === 'light') {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
                document.getElementById('theme-sun-icon')?.classList.remove('hidden');
                document.getElementById('theme-moon-icon')?.classList.add('hidden');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
                document.getElementById('theme-sun-icon')?.classList.add('hidden');
                document.getElementById('theme-moon-icon')?.classList.remove('hidden');
            }
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('light')) {
                document.documentElement.classList.remove('light');
                document.documentElement.classList.add('dark');
                localStorage.setItem('app-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                localStorage.setItem('app-theme', 'light');
            }
            initTheme();
        }

        document.getElementById('theme-toggle-btn')?.addEventListener('click', toggleTheme);
        document.getElementById('theme-toggle-btn-mobile')?.addEventListener('click', toggleTheme);
        initTheme();

        function toggleMobileMenu() {
            const drawer = document.getElementById('mobile-menu-drawer');
            const hamburger = document.getElementById('hamburger-icon');
            const close = document.getElementById('close-icon');

            if (drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                hamburger.classList.add('hidden');
                close.classList.remove('hidden');
            } else {
                drawer.classList.add('hidden');
                hamburger.classList.remove('hidden');
                close.classList.add('hidden');
            }
        }

        document.getElementById('mobile-menu-toggle')?.addEventListener('click', toggleMobileMenu);

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
