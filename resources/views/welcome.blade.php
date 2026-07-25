<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $activeTournament?->name ?? 'Outlaw Showdown 2026' }} | Nepal's Premier Esports Championship</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @php
        $themeColor = $activeTournament?->theme_color ?? '#10b981';

        // Convert Hex to RGB for full 100% dynamic opacity blending
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
        $prizePool = $activeTournament?->prize_pool_total ?? 500000;
        $heroHeadline = $activeTournament?->hero_headline ?: 'UNLEASH THE LEGEND, CLAIM YOUR GLORY';
        $heroSubheadline = $activeTournament?->hero_subheadline ?: "Nepal's premier national esports championship stage is live! Register your squad for multi-game title disciplines and follow live brackets on Challonge.com.";
        $logoUrl = $activeTournament?->logo_path ? Storage::url($activeTournament->logo_path) : null;
        $bannerUrl = $activeTournament?->banner_path ? Storage::url($activeTournament->banner_path) : null;
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
        .font-mono-cyber {
            font-family: 'Space Grotesk', monospace;
        }

        /* Dynamic Theme System */
        .dynamic-accent-text {
            color: {{ $themeColor }} !important;
        }
        .dynamic-accent-bg {
            background-color: {{ $themeColor }} !important;
        }
        .dynamic-accent-border {
            border-color: rgba({{ $rgb }}, 0.4) !important;
        }
        .dynamic-badge {
            background-color: rgba({{ $rgb }}, 0.15);
            border: 1px solid rgba({{ $rgb }}, 0.4);
            color: {{ $themeColor }};
        }
        .dynamic-btn-gradient {
            background: linear-gradient(135deg, {{ $themeColor }} 0%, rgba({{ $rgb }}, 0.8) 100%);
            box-shadow: 0 0 30px rgba({{ $rgb }}, 0.45);
        }
        .dynamic-btn-gradient:hover {
            box-shadow: 0 0 50px rgba({{ $rgb }}, 0.75);
        }

        /* High Intensity Glassmorphism */
        .esports-card-v2 {
            background: rgba(10, 18, 30, 0.85);
            -webkit-backdrop-filter: blur(24px);
            backdrop-filter: blur(24px);
            border: 1px solid rgba({{ $rgb }}, 0.3);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .esports-card-v2:hover {
            border-color: rgba({{ $rgb }}, 0.75);
            box-shadow: 0 25px 60px -10px rgba({{ $rgb }}, 0.4);
        }

        /* Persistent Light Mode */
        html.light body {
            background-color: #f8fafc;
            color: #0f172a;
        }
        html.light .esports-card-v2 {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba({{ $rgb }}, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        html.light .nav-bg {
            background-color: rgba(255, 255, 255, 0.92);
        }
    </style>
</head>
<body class="cyber-bg text-slate-100 min-h-screen selection:bg-emerald-500 selection:text-slate-950 overflow-x-hidden antialiased relative">

    <!-- DYNAMIC PARTICLES BACKGROUND CANVAS -->
    <canvas id="particle-canvas" class="fixed inset-0 pointer-events-none z-0 opacity-40"></canvas>

    <!-- SCANLINE OVERLAY -->
    <div class="fixed inset-0 scanlines pointer-events-none z-40 opacity-30"></div>

    <!-- TOP NAVIGATION BAR -->
    <nav class="sticky top-0 z-50 backdrop-blur-2xl nav-bg bg-slate-950/90 border-b dynamic-accent-border transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4">
                
                <!-- BRAND LOGO -->
                <a href="#" class="flex items-center gap-3 group shrink-0">
                    <div class="w-11 h-11 clip-corner-sm dynamic-btn-gradient flex items-center justify-center font-black text-slate-950 text-xl group-hover:scale-110 transition-transform duration-300">
                        OS
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-xl sm:text-2xl tracking-wider group-hover:dynamic-accent-text transition-colors whitespace-nowrap">
                                {{ strtoupper(strtok($tournamentName, ' ')) }}<span class="dynamic-accent-text">{{ strtoupper(substr($tournamentName, strpos($tournamentName, ' ') ?: 0)) }}</span>
                            </span>
                            <span class="hidden sm:inline-flex px-2 py-0.5 rounded text-[10px] font-mono-cyber font-bold dynamic-badge animate-pulse whitespace-nowrap">LIVE</span>
                        </div>
                        <div class="text-[10px] tracking-widest dynamic-accent-text uppercase font-mono-cyber font-bold whitespace-nowrap">{{ $seasonVersion }} • ARENA</div>
                    </div>
                </a>

                <!-- DESKTOP NAV LINKS -->
                <div class="hidden xl:flex items-center gap-8 text-sm font-extrabold tracking-wide shrink-0">
                    <a href="#games" class="hover:dynamic-accent-text transition-colors flex items-center gap-2 whitespace-nowrap hover:scale-105 transform">
                        <span class="text-base">🎮</span> Game Titles
                    </a>
                    <a href="#hub" class="hover:dynamic-accent-text transition-colors flex items-center gap-2 whitespace-nowrap hover:scale-105 transform">
                        <span class="text-base">🏆</span> Challonge Brackets
                    </a>
                    <a href="#sponsors" class="hover:dynamic-accent-text transition-colors flex items-center gap-2 whitespace-nowrap hover:scale-105 transform">
                        <span class="text-base">🤝</span> Sponsors & Partners
                    </a>
                </div>

                <!-- DESKTOP CTA & THEME TOGGLE BUTTONS -->
                <div class="hidden lg:flex items-center gap-4 shrink-0">
                    <!-- PERSISTENT LIGHT / DARK THEME TOGGLE BUTTON -->
                    <button id="theme-toggle-btn" class="p-2.5 rounded-xl bg-slate-900/80 border dynamic-accent-border text-slate-200 hover:dynamic-accent-text focus:outline-none transition-colors" title="Toggle Light / Dark Mode">
                        <span id="theme-sun-icon" class="hidden">☀️</span>
                        <span id="theme-moon-icon">🌙</span>
                    </button>

                    <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-900/90 border dynamic-accent-border text-xs font-mono-cyber whitespace-nowrap">
                        <span class="text-slate-400">ENTRY FEE:</span> <span class="font-black dynamic-accent-text">Rs. {{ number_format($entryFee) }}</span> / player
                    </div>

                    @auth('participant')
                        <a href="{{ url('/mukhyadwar') }}" class="px-5 py-2.5 rounded-xl font-black text-sm dynamic-btn-gradient text-slate-950 transition-all hover:scale-105 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Mukhyadwar Arena</span>
                        </a>
                    @else
                        <a href="{{ url('/mukhyadwar/login') }}" class="font-extrabold text-sm px-3 py-2 transition-colors whitespace-nowrap">
                            Log In
                        </a>
                        <a href="{{ url('/mukhyadwar/register') }}" class="px-5 py-2.5 clip-corner-sm dynamic-btn-gradient text-slate-950 font-black text-xs uppercase tracking-wider transition-all hover:scale-105 whitespace-nowrap">
                            Mukhyadwar Portal
                        </a>
                    @endauth
                </div>

                <!-- MOBILE MENU TOGGLE -->
                <div class="flex xl:hidden items-center gap-3 shrink-0">
                    <button id="theme-toggle-btn-mobile" class="p-2.5 rounded-xl bg-slate-900 border dynamic-accent-border text-slate-200">
                        <span>🌓</span>
                    </button>
                    <button id="mobile-menu-toggle" class="p-2.5 rounded-xl bg-slate-900 border dynamic-accent-border dynamic-accent-text focus:outline-none transition-colors">
                        <svg id="hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- MOBILE MENU DRAWER -->
        <div id="mobile-menu-drawer" class="hidden xl:hidden bg-slate-950/95 border-b dynamic-accent-border backdrop-blur-2xl px-4 pt-4 pb-6 space-y-4 transition-all">
            <div class="flex flex-col gap-3 font-bold text-sm tracking-wide">
                <a href="#games" onclick="toggleMobileMenu()" class="px-4 py-2.5 rounded-lg hover:bg-slate-900 hover:dynamic-accent-text flex items-center gap-2">
                    <span>🎮</span> Game Titles
                </a>
                <a href="#hub" onclick="toggleMobileMenu()" class="px-4 py-2.5 rounded-lg hover:bg-slate-900 hover:dynamic-accent-text flex items-center gap-2">
                    <span>🏆</span> Challonge Brackets
                </a>
                <a href="#sponsors" onclick="toggleMobileMenu()" class="px-4 py-2.5 rounded-lg hover:bg-slate-900 hover:dynamic-accent-text flex items-center gap-2">
                    <span>🤝</span> Sponsors & Partners
                </a>
            </div>

            <div class="pt-4 border-t border-slate-800 flex flex-col gap-3">
                <div class="flex items-center justify-between px-4 py-2 rounded-lg bg-slate-900 border dynamic-accent-border text-xs font-mono-cyber">
                    <span class="text-slate-400">ENTRY FEE:</span> <span class="font-bold dynamic-accent-text">Rs. {{ number_format($entryFee) }} / person</span>
                </div>

                @auth('participant')
                    <a href="{{ url('/mukhyadwar') }}" class="w-full py-3 rounded-xl text-center font-black text-sm dynamic-btn-gradient text-slate-950">
                        Mukhyadwar Arena
                    </a>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ url('/mukhyadwar/login') }}" class="py-2.5 rounded-lg text-center font-semibold text-sm bg-slate-900 border dynamic-accent-border">
                            Log In
                        </a>
                        <a href="{{ url('/mukhyadwar/register') }}" class="py-2.5 clip-corner-sm text-center font-extrabold text-sm uppercase dynamic-btn-gradient text-slate-950">
                            Portal
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- 1. HIGH-INTENSITY HERO ARENA SECTION (100% DYNAMIC TOURNAMENT SETUP) -->
    <section class="relative pt-12 pb-24 lg:pt-20 lg:pb-32 overflow-hidden z-10">
        <!-- Dynamic Glow Aura -->
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] sm:w-[900px] h-[600px] sm:h-[900px] rounded-full blur-[180px] pointer-events-none animate-pulse-glow" style="background-color: rgba({{ $rgb }}, 0.2);"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- HERO DYNAMIC TEXT CONTENT -->
                <div class="lg:col-span-7 text-center lg:text-left reveal-left">
                    <!-- Dynamic Featured Badge -->
                    <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full dynamic-badge text-xs sm:text-sm font-mono-cyber tracking-widest uppercase mb-6 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full dynamic-accent-bg animate-ping"></span>
                        <span>FEATURED EVENT: {{ strtoupper($tournamentName) }}</span>
                    </div>

                    <!-- Dynamic Hero Headline -->
                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight uppercase leading-[0.95] mb-6">
                        <span class="dynamic-accent-text">{{ $heroHeadline }}</span>
                    </h1>

                    <!-- Dynamic Hero Subheadline -->
                    <p class="text-slate-300 text-base sm:text-lg lg:text-xl font-normal leading-relaxed mb-8 max-w-2xl mx-auto lg:mx-0">
                        {{ $heroSubheadline }}
                    </p>

                    <!-- Dynamic Stats Card Grid -->
                    <div class="p-6 rounded-3xl esports-card-v2 mb-8 max-w-lg mx-auto lg:mx-0 grid grid-cols-2 gap-6 border dynamic-accent-border">
                        <div class="p-2 border-r border-slate-800">
                            <div class="text-xs dynamic-accent-text font-mono-cyber uppercase tracking-wider font-bold">ENTRY FEE PER PERSON</div>
                            <div class="text-2xl sm:text-4xl font-black mt-1">Rs. {{ number_format($entryFee) }}</div>
                        </div>
                        <div class="p-2">
                            <div class="text-xs dynamic-accent-text font-mono-cyber uppercase tracking-wider font-bold">TOTAL PRIZE POOL</div>
                            <div class="text-2xl sm:text-4xl font-black dynamic-accent-text mt-1">Rs. {{ number_format($prizePool) }}</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ url('/mukhyadwar/register') }}" onclick="playCyberSound()" class="w-full sm:w-auto px-10 py-5 clip-corner dynamic-btn-gradient text-slate-950 font-black text-lg uppercase tracking-wider transition-all duration-300 hover:scale-105 flex items-center justify-center gap-3">
                            <span>Register Your Squad</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="w-full sm:w-auto px-10 py-5 clip-corner bg-slate-900/90 hover:bg-slate-800 border dynamic-accent-border dynamic-accent-text font-extrabold text-lg uppercase tracking-wider transition-all duration-300 hover:scale-105 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5 dynamic-accent-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Sponsor Query</span>
                        </button>
                    </div>
                </div>

                <!-- HERO DYNAMIC EMBLEM / GRAPHIC CARD -->
                <div class="lg:col-span-5 relative reveal-right">
                    <div class="relative mx-auto max-w-sm sm:max-w-md lg:max-w-none">
                        @if($bannerUrl || $logoUrl)
                            <div class="rounded-3xl esports-card-v2 p-4 border dynamic-accent-border overflow-hidden group">
                                <img src="{{ $bannerUrl ?: $logoUrl }}" alt="{{ $tournamentName }}" class="w-full h-auto rounded-2xl object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @else
                            <!-- SLEEK HIGH-INTENSITY METALLIC TROPHY CARD -->
                            <div class="rounded-3xl esports-card-v2 p-8 sm:p-10 border-2 dynamic-accent-border text-center relative overflow-hidden group">
                                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-slate-900/90 border-2 dynamic-accent-border flex items-center justify-center text-5xl sm:text-6xl mx-auto mb-6 shadow-2xl group-hover:scale-110 transition-transform">
                                    🏆
                                </div>
                                <div class="px-4 py-1.5 rounded-full dynamic-badge text-xs font-mono-cyber uppercase font-bold tracking-widest inline-block mb-3">
                                    OFFICIAL ARENA TROPHY
                                </div>
                                <h3 class="text-2xl sm:text-3xl font-black uppercase text-white mb-2">{{ $tournamentName }}</h3>
                                <p class="text-slate-400 text-xs sm:text-sm max-w-xs mx-auto mb-6">
                                    Compete across {{ $gameTitles->count() }} major disciplines for glory & Rs. {{ number_format($prizePool) }} prize pool.
                                </p>
                                <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-800 text-left font-mono-cyber text-xs">
                                    <div>
                                        <div class="text-slate-400">DISCIPLINES:</div>
                                        <div class="font-bold text-white mt-0.5">{{ $gameTitles->count() }} Titles</div>
                                    </div>
                                    <div>
                                        <div class="text-slate-400">STAGE STATUS:</div>
                                        <div class="font-bold dynamic-accent-text mt-0.5">{{ strtoupper(str_replace('_', ' ', $activeTournament?->status ?? 'registration_open')) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. GAME TITLES SECTION -->
    <section class="py-20 relative bg-slate-950/80 border-t dynamic-accent-border" id="games">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal-on-scroll">
                <span class="px-4 py-1.5 rounded-full dynamic-badge text-xs font-mono-cyber uppercase font-bold tracking-widest">MULTIPLE GAME TITLE DISCIPLINES</span>
                <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight mt-4">
                    TOURNAMENT <span class="dynamic-accent-text">GAME TITLES</span>
                </h2>
                <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto mt-2">
                    Multiple game titles assigned to {{ $tournamentName }}. Entry fee: Rs. {{ number_format($entryFee) }} / player.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($gameTitles as $game)
                    <div class="reveal-on-scroll rounded-3xl esports-card-v2 p-7 border dynamic-accent-border transition-all duration-500 hover:-translate-y-3 group relative overflow-hidden">
                        <div class="flex justify-between items-start mb-6">
                            <span class="px-3 py-1 clip-corner-sm dynamic-badge font-mono-cyber text-[11px] uppercase font-bold">{{ strtoupper(str_replace('_', ' ', $game->game_type)) }}</span>
                            <span class="text-xs font-mono-cyber text-slate-300 font-bold">Rs. {{ number_format($entryFee) }} / Person</span>
                        </div>
                        <div class="w-16 h-16 rounded-2xl bg-slate-900/90 border dynamic-accent-border flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">🎮</div>
                        <h3 class="text-2xl font-extrabold group-hover:dynamic-accent-text transition-colors mb-3">{{ $game->name }}</h3>
                        <p class="text-slate-400 text-sm mb-6 leading-relaxed">Official esports discipline powered by {{ $game->developer ?? 'Outlaw Operations' }}.</p>
                        <div class="flex items-center justify-between pt-5 border-t border-slate-800">
                            <span class="text-xs font-mono-cyber dynamic-accent-text font-bold">Official Arena</span>
                            <a href="{{ url('/mukhyadwar/register') }}" onclick="playCyberSound()" class="px-4 py-2 clip-corner-sm dynamic-btn-gradient text-slate-950 font-extrabold text-xs uppercase tracking-wider flex items-center gap-1 transition-all">Register Squad</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 3. CHALLONGE BRACKETS ARENA WITH FALLBACK -->
    <section class="py-24 relative" id="hub">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6 reveal-on-scroll">
                <div>
                    <div class="text-xs font-mono-cyber dynamic-accent-text uppercase tracking-widest mb-2">POWERED BY CHALLONGE.COM API</div>
                    <h2 class="text-3xl sm:text-5xl font-black uppercase">LIVE MATCH <span class="dynamic-accent-text">BRACKETS</span></h2>
                </div>
                @if(!empty($activeTournament?->challonge_url))
                    <a href="{{ $activeTournament->challonge_url }}" target="_blank" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-900 border dynamic-accent-border dynamic-accent-text hover:text-white font-extrabold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-all">
                        <span>Open Full Challonge Bracket</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                @endif
            </div>

            @if(!empty($activeTournament?->challonge_embed_url) || !empty($activeTournament?->challonge_url))
                <div class="reveal-on-scroll rounded-3xl esports-card-v2 border dynamic-accent-border overflow-hidden shadow-[0_0_50px_rgba(var(--accent-rgb),0.2)]">
                    <div class="bg-slate-900/90 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full dynamic-accent-bg animate-ping"></span>
                            <span class="font-extrabold text-xs sm:text-sm tracking-wider uppercase">Official Challonge Bracket Module</span>
                        </div>
                        <span class="hidden sm:inline-block text-xs font-mono-cyber dynamic-accent-text font-bold">CHALLONGE.COM</span>
                    </div>
                    <div class="w-full bg-slate-950 overflow-hidden min-h-[550px] relative">
                        <iframe src="{{ $challongeEmbedUrl }}" width="100%" height="550" frameborder="0" scrolling="auto" allowtransparency="true" class="w-full h-[550px] border-0"></iframe>
                    </div>
                </div>
            @else
                <!-- CATCHY CHALLONGE COMING SOON FALLBACK CARD -->
                <div class="reveal-on-scroll rounded-3xl esports-card-v2 p-12 text-center border-2 border-dashed dynamic-accent-border">
                    <div class="w-16 h-16 rounded-2xl bg-slate-900 border dynamic-accent-border flex items-center justify-center text-3xl mx-auto mb-4 animate-bounce">
                        🏆
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black uppercase mb-3">TIE-SHEET BRACKETS <span class="dynamic-accent-text">COMING SOON!</span></h3>
                    <p class="text-slate-400 text-sm max-w-lg mx-auto mb-6">
                        Official match draw tie-sheets and group stage brackets for {{ $tournamentName }} are currently being generated on Challonge.com. Stay tuned!
                    </p>
                    <a href="{{ $activeTournament?->discord_server_url ?: 'https://discord.gg/outlawshowdown' }}" target="_blank" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl dynamic-btn-gradient text-slate-950 font-extrabold text-sm uppercase tracking-wider">
                        <span>Join Discord for Live Draw Alerts</span>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- 4. OFFICIAL SPONSORS & PARTNERS SECTION WITH FALLBACK -->
    <section class="py-20 relative bg-slate-950 border-b dynamic-accent-border" id="sponsors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal-on-scroll">
                <span class="px-4 py-1.5 rounded-full dynamic-badge text-xs font-mono-cyber uppercase font-bold tracking-widest">
                    {{ strtoupper($tournamentName) }} PARTNERSHIPS
                </span>
                <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight mt-4">
                    SPONSORS & <span class="dynamic-accent-text">MEDIA PARTNERS</span>
                </h2>
            </div>

            @php
                $titleSponsors = $sponsors->where('level', 'title');
            @endphp

            @if($sponsors->count() > 0 || $partners->count() > 0)
                @if($titleSponsors->count() > 0)
                    <div class="mb-14 reveal-on-scroll">
                        <div class="grid grid-cols-1 max-w-2xl mx-auto">
                            @foreach($titleSponsors as $sponsor)
                                <a href="{{ $sponsor->website_url ?? '#' }}" target="_blank" class="p-8 rounded-3xl esports-card-v2 border-2 border-amber-500/60 text-center flex flex-col items-center justify-center gap-4 group hover:scale-105 transition-transform">
                                    <img src="{{ $sponsor->logo_url ? Storage::url($sponsor->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $sponsor->name }}" class="max-h-28 max-w-[240px] object-contain rounded-xl">
                                    <div class="text-xl font-black text-amber-400 tracking-wider uppercase">{{ $sponsor->name }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                    @foreach($partners as $partner)
                        <a href="{{ $partner->website_url ?? '#' }}" target="_blank" class="p-6 rounded-2xl esports-card-v2 border dynamic-accent-border text-center flex flex-col items-center justify-center gap-3 hover:border-white transition-colors">
                            <img src="{{ $partner->logo_url ? Storage::url($partner->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $partner->name }}" class="max-h-14 max-w-[160px] object-contain rounded-lg">
                            <span class="text-base font-extrabold">{{ $partner->name }}</span>
                            <span class="px-3 py-1 rounded text-xs font-mono-cyber font-bold dynamic-badge">{{ $partner->title }}</span>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- CATCHY SPONSORS EMPTY FALLBACK CTA CARD -->
                <div class="reveal-on-scroll rounded-3xl esports-card-v2 p-12 text-center border-2 border-dashed dynamic-accent-border max-w-3xl mx-auto">
                    <div class="w-16 h-16 rounded-2xl bg-slate-900 border dynamic-accent-border flex items-center justify-center text-3xl mx-auto mb-4">🤝</div>
                    <h3 class="text-2xl sm:text-4xl font-black uppercase mb-3">BECOME AN OFFICIAL <span class="dynamic-accent-text">SPONSOR OR PARTNER!</span></h3>
                    <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto mb-8">
                        Connect your brand directly with thousands of competitive gamers, youth audiences, and esports fans across Nepal for {{ $tournamentName }}.
                    </p>
                    <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="px-10 py-5 clip-corner dynamic-btn-gradient text-slate-950 font-black text-lg uppercase tracking-wider transition-all duration-300 hover:scale-105">
                        Grab Sponsorship Opportunity
                    </button>
                </div>
            @endif
        </div>
    </section>

    <!-- 5. SPONSORSHIP INQUIRY BANNER & MODAL -->
    <section class="py-24 relative bg-gradient-to-b from-slate-950 to-slate-900 border-t dynamic-accent-border" id="sponsor-query-section">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-on-scroll">
            <span class="px-4 py-1.5 rounded-full dynamic-badge text-xs font-mono-cyber uppercase font-bold tracking-widest">BRAND PARTNERSHIPS</span>
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight mt-4 mb-6">
                PARTNER WITH <span class="dynamic-accent-text">{{ strtoupper($tournamentName) }}</span>
            </h2>
            <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto mb-10">
                Connect your brand directly with thousands of competitive gamers, youth audiences, and esports enthusiasts across Nepal!
            </p>
            <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="px-10 py-5 clip-corner dynamic-btn-gradient text-slate-950 font-black text-lg uppercase tracking-wider transition-all duration-300 hover:scale-105">
                Send Sponsorship Query
            </button>
        </div>
    </section>

    <!-- LIVEWIRE SPONSOR QUERY MODAL -->
    <div id="sponsor-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-xl hidden">
        <div class="relative w-full max-w-lg rounded-3xl esports-card-v2 p-8 border dynamic-accent-border shadow-[0_0_60px_rgba(var(--accent-rgb),0.5)]">
            <button onclick="document.getElementById('sponsor-modal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-white text-2xl font-bold">✕</button>
            
            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 border dynamic-accent-border flex items-center justify-center text-3xl mx-auto mb-3">🤝</div>
                <h3 class="text-2xl font-black uppercase">Sponsor {{ $tournamentName }}</h3>
                <p class="text-slate-400 text-xs mt-1">Submit your partnership query and join our esports sponsor lineup!</p>
            </div>

            <livewire:sponsor-query-form />
        </div>
    </div>

    <!-- 6. FOOTER -->
    <footer class="bg-slate-950 border-t dynamic-accent-border py-12 text-slate-400 text-sm relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 clip-corner-sm dynamic-btn-gradient flex items-center justify-center font-black text-slate-950 text-base">OS</div>
                <span class="font-extrabold tracking-wider text-base">{{ strtoupper($tournamentName) }}</span>
            </div>
            <div class="text-xs font-mono-cyber text-slate-400 text-center">
                © 2026 {{ $tournamentName }}. All Rights Reserved. Entry Fee: Rs. {{ number_format($entryFee) }} / person.
            </div>
            <div class="flex items-center gap-5 dynamic-accent-text font-bold text-xs">
                <a href="#games" class="hover:underline">Games</a>
                <a href="#hub" class="hover:underline">Results</a>
                <a href="#sponsors" class="hover:underline">Sponsors</a>
            </div>
        </div>
    </footer>

    @livewireScripts
    <script>
        // Persistent Light / Dark Mode Toggle Logic
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
