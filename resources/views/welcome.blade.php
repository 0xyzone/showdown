<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $activeTournament?->name ?? 'Outlaw Showdown' }} | Nepal's Premier Esports Championship</title>
    
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

        // Calculate Luminance to determine if theme background/buttons require dark or light text
        // Relative Luminance formula (WCAG Standard): 0.2126*R + 0.7152*G + 0.0722*B
        $luminance = ($r * 0.2126 + $g * 0.7152 + $b * 0.0722) / 255;
        $btnTextColor = $luminance > 0.55 ? '#0b0e14' : '#ffffff';
        $accentBadgeBg = $luminance > 0.55 ? "rgba($rgb, 0.25)" : "rgba($rgb, 0.18)";
        $accentBadgeText = $luminance > 0.55 ? "rgb(" . max(0, $r-50) . "," . max(0, $g-50) . "," . max(0, $b-50) . ")" : $themeColor;

        $tournamentName = $activeTournament?->name ?? 'OUTLAW SHOWDOWN';
        $seasonVersion = $activeTournament?->season_version ?? 'SEASON UPCOMING';
        $entryFee = $activeTournament?->entry_fee ?? 100;
        $entryFeeSuffix = $activeTournament?->entry_fee_suffix ?: 'person';

        // Calculate dynamic total prize pool from per-game allocations if present
        $calculatedPrizePool = 0;
        if ($activeTournament && $activeTournament->gameTitles->count() > 0) {
            foreach ($activeTournament->gameTitles as $g) {
                $calculatedPrizePool += (float) ($g->pivot->prize_pool ?? 0);
            }
        }
        $prizePool = $calculatedPrizePool > 0 ? $calculatedPrizePool : ($activeTournament?->prize_pool_total ?? 500000);

        $heroHeadline = $activeTournament?->hero_headline ?: 'OUTLAW SHOWDOWN';
        $heroSubheadline = $activeTournament?->hero_subheadline ?: "Experience Nepal's ultimate competitive esports arena.";
        $registrationEnd = $activeTournament?->registration_end ? $activeTournament->registration_end->toIso8601String() : null;
    @endphp

    <style>
        :root {
            --accent-hex: {{ $themeColor }};
            --accent-rgb: {{ $rgb }};
            --accent-btn-text: {{ $btnTextColor }};
            --color-emerald-500: {{ $themeColor }};
            --color-emerald-400: {{ $themeColor }};
            --color-emerald-300: {{ $themeColor }};
            --color-emerald-600: {{ $themeColor }};
        }
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 5rem;
        }
        body {
            font-family: 'Outfit', sans-serif;
        }
        /* Dynamic Accent Styles with Contrast Contrast Safeguards */
        .dynamic-accent-bg {
            background-color: {{ $themeColor }};
            color: {{ $btnTextColor }};
        }
        .dynamic-accent-text {
            color: {{ $themeColor }};
        }
        .dynamic-accent-border {
            border-color: {{ $themeColor }};
        }
        .dynamic-accent-btn {
            background-color: {{ $themeColor }};
            color: {{ $btnTextColor }};
            box-shadow: 0 0 20px rgba({{ $rgb }}, 0.4);
        }
        .dynamic-accent-btn:hover {
            box-shadow: 0 0 30px rgba({{ $rgb }}, 0.7);
            transform: translateY(-1px);
        }
        .dynamic-stroke-text {
            -webkit-text-stroke: 1.8px {{ $themeColor }};
            color: transparent;
            text-shadow: 0 0 20px rgba({{ $rgb }}, 0.6);
        }

        /* Sleek Floating Capsule Navbar Animation */
        #floating-navbar.scrolled {
            background-color: rgba(9, 14, 26, 0.96);
            border-color: rgba({{ $rgb }}, 0.5);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.9), 0 0 25px rgba({{ $rgb }}, 0.35);
        }

        /* Active Navigation Link Styling */
        .mobile-nav-link {
            transition: all 0.25s ease;
        }
        .mobile-nav-link.active {
            color: {{ $themeColor }} !important;
            background-color: rgba(30, 41, 59, 0.9) !important;
            font-weight: 800 !important;
        }

        /* Mobile Drawer Transition */
        #mobile-menu-drawer {
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            max-height: 0;
            opacity: 0;
            transform: translateY(-10px) scale(0.97);
            overflow: hidden;
            pointer-events: none;
        }
        #mobile-menu-drawer.open {
            max-height: 600px;
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
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

    <!-- 1. MINIMALIST FLOATING CAPSULE HEADER (CLEAN LOGO EMBLEM + HAMBURGER ICON BUTTON) -->
    <header class="fixed top-3 sm:top-5 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-2xl pointer-events-none">
        
        <!-- MAIN CAPSULE BAR -->
        <nav id="floating-navbar" class="pointer-events-auto rounded-full bg-slate-950/92 backdrop-blur-2xl border border-cyan-500/35 px-4 sm:px-6 py-2.5 shadow-[0_8px_32px_rgba(0,0,0,0.85)] flex items-center justify-between gap-4 relative z-20">
            
            <!-- BRAND LOGO EMBLEM -->
            <a href="#overview" class="flex items-center gap-2 group shrink-0 min-w-0">
                @if($activeTournament?->logo_path)
                    <img src="{{ Storage::url($activeTournament->logo_path) }}" alt="{{ $tournamentName }}" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-contain border border-cyan-500/40 p-0.5 group-hover:scale-105 transition-transform bg-slate-900 shrink-0">
                @else
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full dynamic-accent-btn flex items-center justify-center font-black text-xs group-hover:scale-105 transition-transform duration-300 shrink-0">
                        OS
                    </div>
                @endif

                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="font-orbitron font-black text-xs sm:text-sm tracking-wider text-white whitespace-nowrap truncate max-w-[150px] sm:max-w-[260px]">
                        {{ $tournamentName }}
                    </span>
                    @if($activeTournament)
                        <span class="hidden sm:inline-flex px-2 py-0.5 rounded-full text-[8px] font-mono-cyber font-extrabold uppercase animate-pulse shrink-0" style="background-color: rgba({{ $rgb }}, 0.2); color: {{ $themeColor }}; border: 1px solid rgba({{ $rgb }}, 0.4);">LIVE</span>
                    @else
                        <span class="hidden sm:inline-flex px-2 py-0.5 rounded-full text-[8px] font-mono-cyber font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40 shrink-0">SOON</span>
                    @endif
                </div>
            </a>

            <!-- HAMBURGER MENU BUTTON -->
            <button id="mobile-menu-toggle" aria-label="Toggle navigation menu" class="p-2 rounded-xl bg-slate-900 border border-cyan-500/30 text-cyan-400 focus:outline-none transition-colors hover:bg-slate-800 cursor-pointer shrink-0">
                <svg id="hamburger-icon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="close-icon" class="w-5 h-5 hidden transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </nav>

        <!-- ABSOLUTELY POSITIONED HAMBURGER MENU DRAWER (CONTAINING ALL NAV LINKS, PARTNER BUTTON & LOGIN/SIGNUP) -->
        <div id="mobile-menu-drawer" class="pointer-events-auto absolute top-full left-0 right-0 mt-3 rounded-2xl bg-slate-950/98 border border-cyan-500/40 backdrop-blur-2xl px-4 pt-3.5 pb-4 shadow-[0_20px_50px_rgba(0,0,0,0.95)] space-y-3.5 z-10">
            <!-- NAV LINKS -->
            <div class="flex flex-col gap-1 font-bold text-xs tracking-wide">
                <a href="#overview" onclick="toggleMobileMenu()" class="mobile-nav-link active px-3.5 py-2.5 rounded-xl bg-slate-900/90 text-slate-200 hover:bg-slate-800 flex items-center justify-between transition-colors">
                    <span>Overview</span>
                    <span class="text-[10px] text-slate-400 font-mono-cyber">#overview</span>
                </a>
                @if($activeTournament)
                    <a href="#games" onclick="toggleMobileMenu()" class="mobile-nav-link px-3.5 py-2.5 rounded-xl text-slate-200 hover:bg-slate-900 flex items-center justify-between transition-colors">
                        <span>Esports & Prize Pool</span>
                        <span class="text-[10px] text-slate-400 font-mono-cyber">#games</span>
                    </a>
                    <a href="#timeline" onclick="toggleMobileMenu()" class="mobile-nav-link px-3.5 py-2.5 rounded-xl text-slate-200 hover:bg-slate-900 flex items-center justify-between transition-colors">
                        <span>Event Timeline</span>
                        <span class="text-[10px] text-slate-400 font-mono-cyber">#timeline</span>
                    </a>
                    <a href="#brackets" onclick="toggleMobileMenu()" class="mobile-nav-link px-3.5 py-2.5 rounded-xl text-slate-200 hover:bg-slate-900 flex items-center justify-between transition-colors">
                        <span>Match Brackets</span>
                        <span class="text-[10px] text-slate-400 font-mono-cyber">#brackets</span>
                    </a>
                @endif
                <a href="#sponsors" onclick="toggleMobileMenu()" class="mobile-nav-link px-3.5 py-2.5 rounded-xl text-slate-200 hover:bg-slate-900 flex items-center justify-between transition-colors">
                    <span>Sponsors</span>
                    <span class="text-[10px] text-slate-400 font-mono-cyber">#sponsors</span>
                </a>
                <a href="#partners" onclick="toggleMobileMenu()" class="mobile-nav-link px-3.5 py-2.5 rounded-xl text-slate-200 hover:bg-slate-900 flex items-center justify-between transition-colors">
                    <span>Partners</span>
                    <span class="text-[10px] text-slate-400 font-mono-cyber">#partners</span>
                </a>
            </div>

            <!-- ACTION BUTTONS SECTION INSIDE DRAWER (PARTNER, LOGIN, SIGNUP / ARENA PORTAL) -->
            <div class="pt-3 border-t border-slate-800/80 flex flex-col gap-2">
                <button onclick="toggleMobileMenu(); playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="w-full py-2.5 rounded-xl text-xs font-bold bg-amber-500/15 text-amber-300 border border-amber-500/35 hover:bg-amber-500/25 transition-all text-center flex items-center justify-center gap-2">
                    <span>🤝 Submit Partner / Sponsor Inquiry</span>
                </button>

                @auth('participant')
                    <a href="{{ url('/mukhyadwar') }}" class="w-full py-2.5 rounded-xl text-center font-extrabold text-xs dynamic-accent-btn">
                        Arena Portal
                    </a>
                @else
                    <div class="grid grid-cols-2 gap-2 pt-0.5">
                        <a href="{{ url('/mukhyadwar/login') }}" class="py-2.5 rounded-xl text-center font-bold text-xs bg-slate-900 border border-cyan-500/30 text-white hover:bg-slate-800 transition-colors">
                            Log in
                        </a>
                        <a href="{{ url('/mukhyadwar/register') }}" class="py-2.5 rounded-xl text-center font-bold text-xs bg-slate-800 border border-cyan-500/40 text-white hover:bg-slate-700 transition-colors">
                            Sign Up
                        </a>
                    </div>
                @endauth
            </div>
        </div>

    </header>

    <div class="pt-16 sm:pt-20"></div>

    @if($activeTournament)
        <!-- ACTIVE TOURNAMENT HERO SECTION -->
        <section class="relative pt-2 pb-14 sm:pt-4 sm:pb-16 lg:pt-8 lg:pb-24 overflow-visble
         z-10 scroll-mt-20" id="overview">
            <!-- Dynamic Glow Aura reflecting active tournament accent -->
            <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] rounded-full blur-[180px] pointer-events-none animate-pulse-glow" style="background-color: rgba({{ $rgb }}, 0.2);"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="relative min-h-[480px] sm:min-h-[520px] rounded-3xl esports-card-v2 p-6 sm:p-12 lg:p-16 border-2 border-cyan-500/40 overflow-hidden sm:overflow-visible">
                    
                    <!-- MASCOT 1: CYBER SNIPER GIRL (HIDDEN ON MOBILE, CONTROLLED SIZE ON DESKTOP) -->
                    <div class="hidden lg:block absolute -top-14 -right-6 sm:-right-10 z-20 pointer-events-auto reveal-right animate-float-left overflow-visible" data-parallax-speed="18">
                        <img src="/images/cyber_chibi_sniper_girl.png" alt="Cyber Chibi Mascot" class="w-64 lg:w-[340px] xl:w-[380px] h-auto object-contain filter drop-shadow-[0_10px_35px_rgba(255,0,85,0.55)] transform hover:scale-105 transition-transform duration-300 overflow-visible">
                    </div>

                    <!-- MASCOT 2: MECHA ROBOT DRAGON (HIDDEN ON MOBILE, CONTROLLED SIZE ON DESKTOP) -->
                    <div class="hidden lg:block absolute -bottom-10 -left-6 sm:-left-10 z-20 pointer-events-auto reveal-left animate-float-right" data-parallax-speed="-14">
                        <img src="/images/mecha_chibi_robot_dragon.png" alt="Mecha Robot Dragon Mascot" class="w-56 lg:w-[260px] xl:w-[300px] h-auto object-contain filter drop-shadow-[0_10px_35px_rgba({{ $rgb }},0.55)] transform hover:scale-105 transition-transform duration-300">
                    </div>

                    <!-- HERO INNER CENTER CONTENT (SPACIOUS MAX WIDTH FOR DESKTOP VIEW) -->
                    <div class="max-w-3xl lg:max-w-4xl mx-auto text-center relative z-10 py-4 sm:py-6">
                        
                        <!-- INDEPENDENT PROMINENT TOURNAMENT LOGO DISPLAY (NO ENCLOSING BOX) -->
                        @if($activeTournament->logo_path)
                            <div class="mb-6 flex justify-center">
                                <img src="{{ Storage::url($activeTournament->logo_path) }}" alt="{{ $activeTournament->name }} Official Logo" class="w-28 h-28 sm:w-36 sm:h-36 lg:w-44 lg:h-44 object-contain filter drop-shadow-[0_0_25px_rgba({{ $rgb }},0.6)] transform hover:scale-105 transition-transform duration-300">
                            </div>
                        @endif

                        <div class="text-xs sm:text-sm font-mono-cyber font-bold text-slate-400 uppercase tracking-widest mb-3">
                            The Showdown Arena • {{ $seasonVersion }}
                        </div>

                        <!-- Main Title -->
                        <h1 class="font-orbitron text-2xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-tight sm:leading-none mb-6">
                            <span class="dynamic-stroke-text block mb-2 break-words">{{ $heroHeadline }}</span>
                        </h1>

                        <!-- PRIZE POOL DYNAMIC BADGE -->
                        <div class="mb-6 flex justify-center">
                            <div class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500/20 via-yellow-400/20 to-amber-500/20 border border-amber-400/60 shadow-[0_0_25px_rgba(251,191,36,0.35)] flex items-center gap-2">
                                <span class="text-xl sm:text-2xl">🏆</span>
                                <span class="font-orbitron text-sm sm:text-lg font-black text-amber-300 uppercase tracking-wider">
                                    Total Prize Pool: NPR Rs. {{ number_format($prizePool) }}
                                </span>
                            </div>
                        </div>

                        <p class="text-slate-300 text-xs sm:text-base lg:text-lg leading-relaxed mb-8 max-w-2xl mx-auto font-sans">
                            {{ $heroSubheadline }}
                        </p>

                        @if($registrationEnd)
                            <!-- Countdown Timer -->
                            <div id="countdown-target" data-date="{{ $registrationEnd }}" class="mb-8 p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-cyan-500/30 max-w-md mx-auto shadow-xl">
                                <div class="text-[10px] sm:text-xs font-mono-cyber font-bold text-slate-400 uppercase tracking-widest mb-3">Registration Closes In</div>
                                <div class="grid grid-cols-4 gap-2 sm:gap-3 font-orbitron text-center">
                                    <div class="p-2 sm:p-3 rounded-xl bg-slate-900 border border-slate-800">
                                        <div id="cd-days" class="text-base sm:text-3xl font-black text-white">00</div>
                                        <div class="text-[9px] sm:text-xs font-mono-cyber text-slate-400 mt-0.5">Days</div>
                                    </div>
                                    <div class="p-2 sm:p-3 rounded-xl bg-slate-900 border border-slate-800">
                                        <div id="cd-hours" class="text-base sm:text-3xl font-black text-white">00</div>
                                        <div class="text-[9px] sm:text-xs font-mono-cyber text-slate-400 mt-0.5">Hours</div>
                                    </div>
                                    <div class="p-2 sm:p-3 rounded-xl bg-slate-900 border border-slate-800">
                                        <div id="cd-mins" class="text-base sm:text-3xl font-black text-white">00</div>
                                        <div class="text-[9px] sm:text-xs font-mono-cyber text-slate-400 mt-0.5">Mins</div>
                                    </div>
                                    <div class="p-2 sm:p-3 rounded-xl bg-slate-900 border border-slate-800">
                                        <div id="cd-secs" class="text-base sm:text-3xl font-black dynamic-accent-text">00</div>
                                        <div class="text-[9px] sm:text-xs font-mono-cyber text-slate-400 mt-0.5">Secs</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- CTAs -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ url('/mukhyadwar') }}" onclick="playCyberSound()" class="w-full sm:w-auto px-8 py-3.5 sm:px-10 sm:py-4 rounded-xl dynamic-accent-btn font-extrabold text-sm tracking-wide transition-all flex items-center justify-center gap-2">
                                <span>/mukhyadwar Registration</span>
                            </a>
                            <a href="{{ $activeTournament->discord_server_url ?: 'https://discord.gg/outlawshowdown' }}" target="_blank" onclick="playCyberSound()" class="w-full sm:w-auto px-8 py-3.5 sm:px-10 sm:py-4 rounded-xl btn-purple-discord font-extrabold text-sm tracking-wide transition-all hover:scale-105 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994.021-.041.001-.09-.041-.106a13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128c.126-.093.252-.19.373-.287a.074.074 0 0 1 .078-.01c3.927 1.793 8.18 1.793 12.061 0a.074.074 0 0 1 .079.009c.12.098.245.195.372.288a.077.077 0 0 1-.006.127c-.598.35-1.22.656-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.028z"/></svg>
                                <span>Discord Community</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ACTIVE TOURNAMENT SPOTLIGHT & PRIZE POOL BREAKDOWN -->
        <section class="py-14 relative bg-slate-950/80 scroll-mt-20" id="games">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-left mb-10 reveal-on-scroll">
                    <span class="px-3 py-1 rounded text-xs font-mono-cyber font-bold uppercase" style="background-color: rgba({{ $rgb }}, 0.2); color: {{ $themeColor }}; border: 1px solid rgba({{ $rgb }}, 0.4);">FEATURED TITLES & PRIZE ALLOCATION</span>
                    <h2 class="font-orbitron text-2xl sm:text-4xl font-black uppercase text-white mt-2">
                        Game Titles & Dedicated Prize Pool
                    </h2>
                </div>

                @if($gameTitles->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($gameTitles as $game)
                            @php
                                $allocatedPrize = $game->pivot?->prize_pool ? (float)$game->pivot->prize_pool : 0;
                                $distributionRaw = $game->pivot?->prize_distribution;
                                $distributionItems = [];
                                if (is_array($distributionRaw)) {
                                    $distributionItems = $distributionRaw;
                                } elseif (is_string($distributionRaw) && !empty($distributionRaw)) {
                                    $decoded = json_decode($distributionRaw, true);
                                    if (is_array($decoded)) {
                                        $distributionItems = $decoded;
                                    }
                                }
                            @endphp
                            <div class="reveal-on-scroll rounded-3xl esports-card-v2 tilt-card p-6 border border-cyan-500/30 group flex flex-col justify-between">
                                <div>
                                    <div class="w-full h-44 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-5xl mb-4 group-hover:scale-105 transition-transform overflow-hidden relative">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent z-10"></div>
                                        @if($game->logo_path)
                                            <img src="{{ Storage::url($game->logo_path) }}" alt="{{ $game->name }}" class="w-28 h-28 object-contain relative z-20">
                                        @else
                                            <span>🎮</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <h3 class="font-orbitron text-lg font-bold text-white group-hover:dynamic-accent-text transition-colors break-words">{{ $game->name }}</h3>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 uppercase font-mono-cyber font-bold shrink-0">
                                            {{ str_replace('_', ' ', $game->game_type) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-slate-800/80">
                                    <div class="flex items-center justify-between text-xs font-mono-cyber mb-2">
                                        <span class="text-slate-400">Allocated Prize Pool:</span>
                                        <span class="font-black text-amber-300 text-sm">
                                            {{ $allocatedPrize > 0 ? 'NPR Rs. ' . number_format($allocatedPrize) : 'TBD' }}
                                        </span>
                                    </div>

                                    @if(!empty($distributionItems))
                                        <div class="p-3 rounded-xl bg-slate-950/80 border border-amber-500/30 text-[11px] font-mono-cyber text-slate-300 leading-relaxed">
                                            <div class="text-[9px] font-bold uppercase text-amber-400 mb-1.5 flex items-center gap-1">
                                                <span>🏆</span>
                                                <span>Distribution Breakdown</span>
                                            </div>
                                            <div class="space-y-1">
                                                @foreach($distributionItems as $rank => $amount)
                                                    <div class="flex items-center justify-between text-[11px]">
                                                        <span class="text-slate-300 font-bold">{{ $rank }}</span>
                                                        <span class="text-amber-300 font-extrabold">Rs. {{ is_numeric($amount) ? number_format((float)$amount) : $amount }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif(is_string($distributionRaw) && !empty($distributionRaw))
                                        <div class="p-3 rounded-xl bg-slate-950/80 border border-amber-500/30 text-[11px] font-mono-cyber text-slate-300 whitespace-pre-line leading-relaxed">
                                            <div class="text-[9px] font-bold uppercase text-amber-400 mb-1">🏆 Distribution Breakdown</div>
                                            {{ $distributionRaw }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- COMING SOON / NO TITLES SELECTED DISCLAIMER -->
                    <div class="max-w-2xl mx-auto reveal-on-scroll">
                        <div class="rounded-3xl esports-card-v2 p-10 text-center border border-dashed border-cyan-500/40 shadow-[0_0_40px_rgba({{ $rgb }},0.15)]">
                            <div class="w-16 h-16 rounded-2xl bg-slate-900 border border-cyan-500/30 flex items-center justify-center text-3xl mx-auto mb-4 animate-bounce">
                                🎮
                            </div>
                            <span class="px-4 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-xs font-mono-cyber font-extrabold uppercase tracking-widest">
                                TITLES ANNOUNCING SOON
                            </span>
                            <h3 class="font-orbitron text-xl sm:text-2xl font-black uppercase text-white mt-4 mb-2">
                                Game Titles & Prize Pool Coming Soon
                            </h3>
                            <p class="text-slate-400 text-xs sm:text-sm font-mono-cyber max-w-md mx-auto">
                                Tournament organizers are preparing the game title disciplines and dedicated prize pool distributions for this championship. Stay tuned!
                            </p>
                        </div>
                    </div>
                @endif

                <!-- EVENT TIMELINE SECTION -->
                <div class="mt-16 reveal-on-scroll scroll-mt-20" id="timeline">
                    <div class="text-left mb-8">
                        <span class="px-3 py-1 rounded text-xs font-mono-cyber font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40">TOURNAMENT SCHEDULE</span>
                        <h2 class="font-orbitron text-2xl sm:text-4xl font-black uppercase text-white mt-2">
                            Event Timeline & Key Dates
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- REGISTRATION OPENS -->
                        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-cyan-500/40 transition-all flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/20 border border-cyan-500/40 flex items-center justify-center text-xl text-cyan-400 shrink-0">🚀</div>
                            <div>
                                <span class="text-[10px] font-mono-cyber font-bold text-slate-400 uppercase tracking-widest block">Phase 1</span>
                                <h4 class="font-orbitron text-sm font-bold text-white mt-0.5">Registration Opens</h4>
                                <p class="text-xs font-mono-cyber text-cyan-300 mt-1">
                                    {{ $activeTournament->registration_start ? $activeTournament->registration_start->format('M d, Y • h:i A') : 'Announced Soon' }}
                                </p>
                            </div>
                        </div>

                        <!-- REGISTRATION CLOSES -->
                        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-cyan-500/40 transition-all flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-red-500/20 border border-red-500/40 flex items-center justify-center text-xl text-red-400 shrink-0">⏳</div>
                            <div>
                                <span class="text-[10px] font-mono-cyber font-bold text-slate-400 uppercase tracking-widest block">Phase 2</span>
                                <h4 class="font-orbitron text-sm font-bold text-white mt-0.5">Registration Closes</h4>
                                <p class="text-xs font-mono-cyber text-red-300 mt-1">
                                    {{ $activeTournament->registration_end ? $activeTournament->registration_end->format('M d, Y • h:i A') : 'Announced Soon' }}
                                </p>
                            </div>
                        </div>

                        <!-- TOURNAMENT START -->
                        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-cyan-500/40 transition-all flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-xl text-amber-400 shrink-0">⚔️</div>
                            <div>
                                <span class="text-[10px] font-mono-cyber font-bold text-slate-400 uppercase tracking-widest block">Phase 3</span>
                                <h4 class="font-orbitron text-sm font-bold text-white mt-0.5">Tournament Begins</h4>
                                <p class="text-xs font-mono-cyber text-amber-300 mt-1">
                                    {{ $activeTournament->start_date ? $activeTournament->start_date->format('M d, Y • h:i A') : 'Announced Soon' }}
                                </p>
                            </div>
                        </div>

                        <!-- TOURNAMENT FINALE -->
                        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-cyan-500/40 transition-all flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-xl text-emerald-400 shrink-0">👑</div>
                            <div>
                                <span class="text-[10px] font-mono-cyber font-bold text-slate-400 uppercase tracking-widest block">Phase 4</span>
                                <h4 class="font-orbitron text-sm font-bold text-white mt-0.5">Grand Finals & Ceremony</h4>
                                <p class="text-xs font-mono-cyber text-emerald-300 mt-1">
                                    {{ $activeTournament->end_date ? $activeTournament->end_date->format('M d, Y') : 'Announced Soon' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CHALLONGE BRACKETS SECTION -->
                <div class="mt-14 reveal-on-scroll rounded-3xl esports-card-v2 border border-cyan-500/30 overflow-hidden scroll-mt-20" id="brackets">
                    <div class="bg-slate-900/90 px-6 py-4 border-b border-slate-800 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <span class="font-orbitron text-sm font-bold text-white uppercase block">Tournament Match Brackets & Live Results</span>
                            <span class="text-xs font-mono-cyber text-slate-400">Directly synchronized via Challonge Engine</span>
                        </div>
                        @if($activeTournament->challonge_url)
                            <a href="{{ $activeTournament->challonge_url }}" target="_blank" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5" style="background-color: rgba({{ $rgb }}, 0.2); color: {{ $themeColor }}; border: 1px solid rgba({{ $rgb }}, 0.4);">
                                🌐 Open Full Bracket on Challonge
                            </a>
                        @endif
                    </div>
                    
                    <div class="w-full bg-slate-950 overflow-hidden min-h-[500px] relative">
                        @if($challongeEmbedUrl)
                            <iframe src="{{ $challongeEmbedUrl }}" width="100%" height="550" frameborder="0" scrolling="auto" allowtransparency="true" class="w-full h-[550px] border-0"></iframe>
                        @else
                            <div class="flex flex-col items-center justify-center p-16 text-center space-y-4">
                                <div class="w-16 h-16 rounded-2xl bg-slate-900 border border-cyan-500/30 flex items-center justify-center text-4xl">🏆</div>
                                <h4 class="font-orbitron text-xl font-bold text-white">Brackets Preparation in Progress</h4>
                                <p class="text-slate-400 text-sm max-w-md">Match brackets and tournament standings will be published live on Challonge as soon as registrations close.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @else
        <!-- NO ACTIVE TOURNAMENT / COMING SOON / MAINTENANCE DISCLAIMER HERO -->
        <section class="relative pt-12 pb-20 overflow-hidden z-10 scroll-mt-20" id="overview">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
                <div class="rounded-3xl esports-card-v2 p-8 sm:p-16 border-2 border-amber-500/40 relative overflow-hidden shadow-[0_0_80px_rgba(245,158,11,0.15)]">
                    
                    <div class="w-20 h-20 rounded-3xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-4xl mx-auto mb-6 text-amber-400 animate-bounce">
                        ⚙️
                    </div>

                    <span class="px-4 py-1.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-mono-cyber font-extrabold uppercase tracking-widest">
                        TOURNAMENT SYSTEM UPDATE
                    </span>

                    <h1 class="font-orbitron text-2xl sm:text-5xl font-black uppercase text-white mt-6 mb-4 break-words">
                        No Active Tournament Currently Running
                    </h1>

                    <p class="text-slate-300 text-xs sm:text-base leading-relaxed max-w-xl mx-auto mb-8 font-mono-cyber">
                        We are currently preparing for our next high-stakes championship series! Registrations and bracket brackets will reopen shortly. Stay tuned or join our official Discord to be notified first.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="w-full sm:w-auto px-8 py-3.5 rounded-xl dynamic-accent-btn font-bold text-sm tracking-wide transition-all">
                            🤝 Submit Partner/Sponsor Inquiry
                        </button>
                        <a href="https://discord.gg/outlawshowdown" target="_blank" class="w-full sm:w-auto px-8 py-3.5 rounded-xl btn-purple-discord font-bold text-sm tracking-wide transition-all hover:scale-105 flex items-center justify-center gap-2">
                            <span>Join Discord Community</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>
    @endif

    <!-- 5. SHINING PRESTIGE SPONSORS TREE SECTION WITH PROPORTIONAL HIERARCHY -->
    <section class="py-24 relative bg-slate-950 border-t border-cyan-500/20 scroll-mt-20 overflow-hidden" id="sponsors">
        <!-- AMBIENT SHINE GLOW BG -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-500/10 rounded-full blur-[160px] pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-16 reveal-on-scroll relative z-10">
            <span class="px-4 py-1.5 rounded-full text-xs font-mono-cyber uppercase font-bold tracking-widest inline-block shadow-[0_0_25px_rgba({{ $rgb }},0.35)]" style="background-color: {{ $accentBadgeBg }}; color: {{ $accentBadgeText }}; border: 1px solid rgba({{ $rgb }}, 0.5);">
                ✨ PRESTIGE SPONSORSHIP HIERARCHY ✨
            </span>
            <h2 class="font-orbitron text-3xl sm:text-5xl font-black uppercase text-white mt-4 tracking-tight drop-shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                Championship Sponsors
            </h2>
            <p class="text-slate-400 text-xs sm:text-sm font-mono-cyber max-w-lg mx-auto mt-3">
                Powering Nepal's largest competitive esports stage and prize ecosystem.
            </p>
            
            <div class="mt-6 flex justify-center">
                <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="px-6 py-2.5 rounded-full dynamic-accent-btn text-xs font-black uppercase tracking-wider transition-all hover:scale-105 flex items-center gap-2">
                    ⚡ Apply for Sponsorship
                </button>
            </div>
        </div>

        @if($sponsors->count() > 0)
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <!-- CENTER VERTICAL SHINING STEM LINE -->
                <div class="absolute left-1/2 top-4 bottom-12 -translate-x-1/2 w-1 bg-gradient-to-b from-amber-400 via-cyan-400 to-transparent hidden sm:block shadow-[0_0_15px_#00f0ff]"></div>

                <div class="space-y-16 relative z-10">
                    @foreach($sponsors as $level => $groupSponsors)
                        @php
                            $tierKey = strtolower($level);
                            
                            // Hierarchy Card & Logo Sizing Logic
                            if (str_contains($tierKey, 'title')) {
                                $cardClasses = "w-56 sm:w-64 min-h-[160px] p-6 border-amber-400/60 shadow-[0_0_35px_rgba(251,191,36,0.3)] bg-gradient-to-b from-slate-900/95 via-amber-950/20 to-slate-900/95";
                                $badgeClasses = "bg-amber-500/20 border-amber-400 text-amber-300 shadow-[0_0_25px_rgba(251,191,36,0.5)]";
                                $logoMaxH = "max-h-16";
                                $logoContainerH = "h-16";
                            } elseif (str_contains($tierKey, 'platinum')) {
                                $cardClasses = "w-48 sm:w-56 min-h-[145px] p-5 border-cyan-400/50 shadow-[0_0_25px_rgba(6,182,212,0.25)] bg-slate-900/90";
                                $badgeClasses = "bg-cyan-500/20 border-cyan-400 text-cyan-300 shadow-[0_0_20px_rgba(6,182,212,0.4)]";
                                $logoMaxH = "max-h-14";
                                $logoContainerH = "h-14";
                            } elseif (str_contains($tierKey, 'gold')) {
                                $cardClasses = "w-44 sm:w-52 min-h-[135px] p-4 sm:p-5 border-amber-500/40 shadow-[0_0_20px_rgba(245,158,11,0.2)] bg-slate-900/85";
                                $badgeClasses = "bg-amber-500/15 border-amber-500/50 text-amber-300";
                                $logoMaxH = "max-h-12";
                                $logoContainerH = "h-12";
                            } else {
                                // Silver / Bronze / General
                                $cardClasses = "w-40 sm:w-44 min-h-[125px] p-4 border-slate-700/80 shadow-[0_0_15px_rgba(255,255,255,0.08)] bg-slate-900/75";
                                $badgeClasses = "bg-slate-800 border-slate-600 text-slate-300";
                                $logoMaxH = "max-h-10";
                                $logoContainerH = "h-10";
                            }
                        @endphp

                        <div class="flex flex-col items-center text-center reveal-on-scroll relative">
                            <!-- TIER BADGE EMBLEM WITH SHINE GLOW -->
                            <div class="relative mb-6 z-10">
                                <div class="px-6 py-2 rounded-full border backdrop-blur-xl flex items-center gap-2.5 {{ $badgeClasses }}">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
                                    <span class="font-orbitron text-xs font-black uppercase tracking-widest">
                                        {{ ucfirst($level) }} Tier Backers
                                    </span>
                                </div>
                            </div>

                            <!-- SYMMETRIC HIERARCHICAL CARDS -->
                            <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 w-full">
                                @foreach($groupSponsors as $sponsor)
                                    <a href="{{ $sponsor->website_url ?: '#' }}" target="{{ $sponsor->website_url ? '_blank' : '_self' }}" class="group relative flex flex-col items-center justify-between rounded-3xl backdrop-blur-2xl transition-all duration-300 hover:scale-105 text-center overflow-hidden {{ $cardClasses }}">
                                        <!-- SWEEP SHINE OVERLAY EFFECT -->
                                        <div class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-white/10 to-transparent pointer-events-none animate-gold-shine"></div>

                                        <!-- LOGO EMBLEM -->
                                        <div class="w-full flex items-center justify-center mb-2 overflow-hidden relative z-10 {{ $logoContainerH }}">
                                            @if($sponsor->logo_url)
                                                <img src="{{ Storage::url($sponsor->logo_url) }}" alt="{{ $sponsor->name }}" class="w-auto object-contain filter grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-300 {{ $logoMaxH }}">
                                            @else
                                                <div class="w-12 h-12 rounded-2xl bg-slate-950 border border-amber-500/40 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                                    ⚡
                                                </div>
                                            @endif
                                        </div>

                                        <!-- BRAND LABEL -->
                                        <span class="font-orbitron text-xs font-extrabold text-slate-200 group-hover:text-amber-300 transition-colors block truncate w-full relative z-10">
                                            {{ $sponsor->name }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="max-w-2xl mx-auto px-4 reveal-on-scroll">
                <div class="rounded-3xl esports-card-v2 p-10 text-center border border-dashed border-amber-500/40 gold-glow-card">
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/20 border border-amber-400 flex items-center justify-center text-3xl mx-auto mb-4 text-amber-300">🏆</div>
                    <h3 class="font-orbitron text-xl font-bold uppercase mb-2 text-white">Sponsor Outlaw Showdown</h3>
                    <p class="text-slate-400 text-xs mb-6 max-w-md mx-auto">Connect your brand directly with esports fans and thousands of active tournament participants across Nepal.</p>
                    <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 font-black text-xs uppercase tracking-wider shadow-[0_0_25px_rgba(251,191,36,0.5)] hover:scale-105 transition-all">
                        Apply for Sponsorship
                    </button>
                </div>
            </div>
        @endif
    </section>

    <!-- 6. ULTRA-PRESTIGE GOLD-THEMED SHINING PARTNERS NETWORK SECTION -->
    <section class="py-24 relative bg-slate-950 border-t border-amber-500/30 scroll-mt-20 overflow-hidden" id="partners">
        <!-- GOLD AMBIENT GLOW BG -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-amber-500/10 rounded-full blur-[180px] pointer-events-none animate-pulse"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 reveal-on-scroll">
                <span class="px-4 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/60 text-amber-300 text-xs font-mono-cyber uppercase font-bold tracking-widest shadow-[0_0_20px_rgba(245,158,11,0.3)]">
                    👑 OFFICIAL ALLIANCE NETWORK
                </span>
                <h2 class="font-orbitron text-3xl sm:text-5xl font-black uppercase text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-400 to-yellow-500 mt-4 drop-shadow-[0_0_25px_rgba(245,158,11,0.4)]">
                    Official Partners
                </h2>
                <p class="text-slate-400 text-xs sm:text-sm font-mono-cyber max-w-md mx-auto mt-3">
                    Broadcasting, production, venue, and gaming media partners.
                </p>
            </div>

            @if($partners->count() > 0)
                <div class="space-y-14">
                    @foreach($partners as $level => $groupPartners)
                        <div class="reveal-on-scroll">
                            <div class="text-center mb-8">
                                <span class="px-5 py-1.5 rounded-full bg-slate-900 border border-amber-400/50 text-xs font-mono-cyber font-extrabold uppercase text-amber-300 tracking-wider shadow-[0_0_15px_rgba(245,158,11,0.2)]">
                                    🌟 {{ ucfirst($level) }} Alliance
                                </span>
                            </div>

                            <!-- GOLD PRESTIGE PARTNER CARDS -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($groupPartners as $partner)
                                    <a href="{{ $partner->website_url ?: '#' }}" target="{{ $partner->website_url ? '_blank' : '_self' }}" class="relative p-6 rounded-3xl bg-slate-900/80 border border-amber-500/35 hover:border-amber-400 backdrop-blur-2xl transition-all duration-300 hover:scale-[1.03] group flex items-center gap-5 gold-glow-card overflow-hidden">
                                        <!-- SWEEP SHINE OVERLAY EFFECT -->
                                        <div class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-amber-200/10 to-transparent pointer-events-none animate-gold-shine"></div>

                                        <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-amber-400/40 flex items-center justify-center shrink-0 overflow-hidden group-hover:scale-110 transition-transform p-2 relative z-10 shadow-[0_0_15px_rgba(245,158,11,0.25)]">
                                            @if($partner->logo_url)
                                                <img src="{{ Storage::url($partner->logo_url) }}" alt="{{ $partner->name }}" class="w-full h-full object-contain">
                                            @else
                                                <span class="text-2xl">🤝</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1 relative z-10">
                                            <h4 class="font-orbitron font-extrabold text-sm text-white group-hover:text-amber-300 transition-colors truncate">
                                                {{ $partner->name }}
                                            </h4>
                                            @if($partner->title)
                                                <span class="inline-block mt-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-mono-cyber font-bold bg-amber-500/20 text-amber-300 border border-amber-400/50 uppercase">
                                                    {{ $partner->title }}
                                                </span>
                                            @else
                                                <span class="text-[11px] text-slate-400 font-mono-cyber block mt-1">Official Ecosystem Partner</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="max-w-2xl mx-auto px-4 reveal-on-scroll">
                    <div class="rounded-3xl esports-card-v2 p-10 text-center border border-dashed border-amber-400/40 gold-glow-card">
                        <div class="w-16 h-16 rounded-2xl bg-amber-500/20 border border-amber-400 flex items-center justify-center text-3xl mx-auto mb-4 text-amber-300">🤝</div>
                        <h3 class="font-orbitron text-xl font-bold uppercase mb-2 text-white">Partner With Outlaw Showdown</h3>
                        <p class="text-slate-400 text-xs mb-6 max-w-md mx-auto">Broadcasting, venue, production, and gaming media opportunities available across Nepal.</p>
                        <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 font-black text-xs uppercase tracking-wider shadow-[0_0_25px_rgba(251,191,36,0.5)] hover:scale-105 transition-all">
                            Apply for Partnership
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- COMMUNITY HUB & DISCORD CTA -->
    <section class="py-16 relative bg-slate-950 border-t border-cyan-500/30 overflow-hidden scroll-mt-20" id="community">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="rounded-3xl esports-card-v2 p-8 sm:p-14 border-2 border-cyan-500/40 text-center relative overflow-hidden sm:overflow-visible">
                
                <!-- HOODIE CAT MASCOT (HIDDEN ON DESKTOP TO PREVENT OVERLAPPING CONTENT) -->
                <div class="hidden lg:block absolute -bottom-8 -right-8 sm:-right-12 z-20 animate-float-right pointer-events-auto" data-parallax-speed="15">
                    <img src="/images/chibi_hoodie_cat_mascot.png" alt="Chibi Hoodie Cat Mascot" class="w-48 md:w-64 lg:w-[300px] h-auto object-contain filter drop-shadow-[0_10px_35px_rgba({{ $rgb }},0.6)] transform hover:scale-105 transition-transform duration-300">
                </div>

                <!-- CENTER DISCORD CTA CONTENT -->
                <div class="max-w-md mx-auto relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-purple-600/20 border border-purple-500/40 flex items-center justify-center text-3xl mx-auto mb-4 text-purple-400">
                        💬
                    </div>
                    <h2 class="font-orbitron text-2xl sm:text-4xl font-black uppercase text-white mb-2 break-words">
                        Join Community
                    </h2>
                    <p class="text-slate-300 text-xs sm:text-sm mb-6 font-mono-cyber">
                        Join the official Discord community for scrims, tournament news, and live updates.
                    </p>
                    <a href="{{ $activeTournament?->discord_server_url ?: 'https://discord.gg/outlawshowdown' }}" target="_blank" onclick="playCyberSound()" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-white text-slate-950 font-bold text-sm hover:bg-slate-200 transition-all hover:scale-105 shadow-xl">
                        <span>Join Discord Community</span>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- LIVEWIRE SPONSOR QUERY MODAL -->
    <div id="sponsor-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-xl hidden">
        <div class="relative w-full max-w-lg rounded-3xl esports-card-v2 p-6 sm:p-8 border border-cyan-500/40 shadow-[0_0_60px_rgba({{ $rgb }},0.5)] max-h-[90vh] overflow-y-auto">
            <button onclick="document.getElementById('sponsor-modal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-white text-2xl font-bold">✕</button>
            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-cyan-500/30 flex items-center justify-center text-3xl mx-auto mb-3">🤝</div>
                <h3 class="font-orbitron text-xl sm:text-2xl font-black uppercase text-white">Sponsor / Partner Application</h3>
            </div>
            <livewire:sponsor-query-form />
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-950 border-t border-cyan-500/30 py-10 text-slate-400 text-xs font-mono-cyber relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 clip-corner-sm dynamic-accent-btn flex items-center justify-center font-black text-sm">OS</div>
                <span class="font-orbitron font-extrabold text-white text-sm">OUTLAW SHOWDOWN</span>
            </div>
            <div>© 2026 Outlaw Showdown. All Rights Reserved. @if($activeTournament) Entry Fee: Rs. {{ number_format($entryFee) }}/{{ $entryFeeSuffix }} @endif</div>
        </div>
    </footer>

    @livewireScripts
    <script>
        function toggleMobileMenu() {
            const drawer = document.getElementById('mobile-menu-drawer');
            const hamburger = document.getElementById('hamburger-icon');
            const close = document.getElementById('close-icon');

            if (!drawer) return;

            if (drawer.classList.contains('open')) {
                drawer.classList.remove('open');
                hamburger?.classList.remove('hidden');
                close?.classList.add('hidden');
            } else {
                drawer.classList.add('open');
                hamburger?.classList.add('hidden');
                close?.classList.close-icon?.classList.remove('hidden');
                close?.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('mobile-menu-toggle')?.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleMobileMenu();
            });
        });

        // High-precision IntersectionObserver for Nav ScrollSpy
        const sectionTargets = document.querySelectorAll('section[id], div[id="brackets"]');
        const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

        function setActiveNavId(id) {
            mobileNavLinks.forEach(link => {
                const href = link.getAttribute('href').replace('#', '');
                if (href === id) {
                    link.classList.add('active', 'dynamic-accent-text');
                } else {
                    link.classList.remove('active', 'dynamic-accent-text');
                }
            });
        }

        const observerOptions = {
            root: null,
            rootMargin: '-20% 0px -60% 0px',
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setActiveNavId(entry.target.getAttribute('id'));
                }
            });
        }, observerOptions);

        sectionTargets.forEach(section => observer.observe(section));

        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('floating-navbar');
            if (window.scrollY > 20) {
                navbar?.classList.add('scrolled');
            } else {
                navbar?.classList.remove('scrolled');
            }
            if (window.scrollY < 80) {
                setActiveNavId('overview');
            }
        });

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
