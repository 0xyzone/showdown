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
                        <span>🏆</span> Match Hub
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

                    @auth
                        <a href="{{ url('/maidan') }}" class="px-5 py-2.5 rounded-lg font-bold text-sm bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-[0_0_25px_rgba(16,185,129,0.5)] transition-all duration-300 hover:scale-105 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Player Portal
                        </a>
                    @else
                        <a href="{{ Route::has('login') ? route('login') : url('/maidan/login') }}" class="hidden sm:inline-flex text-slate-300 hover:text-white font-semibold text-sm px-4 py-2 transition-colors">
                            Log In
                        </a>
                        <a href="{{ url('/maidan/login') }}" class="px-6 py-2.5 clip-corner-sm bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-extrabold text-sm uppercase tracking-wider shadow-[0_0_30px_rgba(16,185,129,0.6)] transition-all duration-300 hover:scale-105">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="relative pt-12 pb-24 lg:pt-20 lg:pb-36 overflow-hidden z-10">
        <div class="absolute top-1/3 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-emerald-500/15 rounded-full blur-[160px] pointer-events-none animate-pulse-glow"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <div class="lg:col-span-7 text-center lg:text-left reveal-left">
                    <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-emerald-950/70 border border-emerald-500/40 text-emerald-300 text-xs sm:text-sm font-mono-cyber tracking-widest uppercase mb-6 shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>OUTLAW SHOWDOWN 2026 • VOL-I ARENA</span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-white uppercase leading-[0.95] mb-6">
                        UNLEASH THE <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 via-emerald-400 to-teal-200 cyber-glow-text">LEGEND</span>
                        <br><span class="text-emerald-400">CLAIM YOUR GLORY</span>
                    </h1>

                    <p class="text-slate-300 text-base sm:text-lg lg:text-xl font-normal leading-relaxed mb-8 max-w-xl mx-auto lg:mx-0">
                        Nepal’s premier esports showdown is here! Compete across PUBG Mobile, MLBB Open & Women's, eFootball Mobile, Valorant, and Cosplay Stage Competition for epic prize pools.
                    </p>

                    <div class="p-5 rounded-2xl esports-card border border-emerald-500/40 mb-8 max-w-lg mx-auto lg:mx-0 grid grid-cols-2 gap-4">
                        <div class="p-2 border-r border-emerald-500/20">
                            <div class="text-xs text-emerald-400 font-mono-cyber uppercase tracking-wider font-bold">ENTRY FEE</div>
                            <div class="text-2xl sm:text-3xl font-black text-white mt-1">Rs. 100 <span class="text-xs text-slate-400 font-normal">/ player</span></div>
                        </div>
                        <div class="p-2">
                            <div class="text-xs text-emerald-400 font-mono-cyber uppercase tracking-wider font-bold">OPEN ARENA</div>
                            <div class="text-2xl sm:text-3xl font-black text-emerald-400 mt-1">Solos & Squads</div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-10">
                        <a href="{{ url('/maidan/login') }}" onclick="playCyberSound()" class="w-full sm:w-auto px-8 py-4 clip-corner bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-lg uppercase tracking-wider shadow-[0_0_35px_rgba(16,185,129,0.6)] transition-all duration-300 hover:scale-105 flex items-center justify-center gap-3">
                            <span>Register Your Team</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <button onclick="playCyberSound(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="w-full sm:w-auto px-8 py-4 clip-corner bg-slate-900/90 hover:bg-slate-800 border border-emerald-500/40 text-emerald-400 font-extrabold text-lg uppercase tracking-wider transition-all duration-300 hover:scale-105 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Sponsor Query</span>
                        </button>
                    </div>

                    <div class="max-w-md mx-auto lg:mx-0">
                        <div class="text-xs text-emerald-400 font-mono-cyber tracking-widest uppercase mb-3">TOURNAMENT COUNTDOWN</div>
                        <div class="grid grid-cols-4 gap-2 text-center" id="countdown">
                            <div class="p-3 rounded-xl esports-card border border-emerald-500/30">
                                <div class="text-2xl font-black text-white font-mono-cyber" id="days">14</div>
                                <div class="text-[10px] text-emerald-400/80 uppercase">Days</div>
                            </div>
                            <div class="p-3 rounded-xl esports-card border border-emerald-500/30">
                                <div class="text-2xl font-black text-white font-mono-cyber" id="hours">08</div>
                                <div class="text-[10px] text-emerald-400/80 uppercase">Hours</div>
                            </div>
                            <div class="p-3 rounded-xl esports-card border border-emerald-500/30">
                                <div class="text-2xl font-black text-white font-mono-cyber" id="minutes">45</div>
                                <div class="text-[10px] text-emerald-400/80 uppercase">Mins</div>
                            </div>
                            <div class="p-3 rounded-xl esports-card border border-emerald-500/30">
                                <div class="text-2xl font-black text-emerald-400 font-mono-cyber" id="seconds">22</div>
                                <div class="text-[10px] text-emerald-400/80 uppercase">Secs</div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-5 relative reveal-right" id="hero-mascot-container">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <div class="relative z-10 animate-float-hero">
                            <div class="absolute inset-0 bg-emerald-500/20 rounded-full blur-[80px] -z-10"></div>
                            <img src="/images/outlaw_hero_mascot.png" alt="Outlaw Showdown Mascot" class="w-full h-auto rounded-3xl border border-emerald-500/40 shadow-[0_0_60px_rgba(16,185,129,0.5)] transform hover:scale-105 transition-transform duration-500">
                        </div>

                        <div class="absolute -top-6 -left-6 z-20 p-4 rounded-2xl esports-card border border-emerald-500/50 shadow-[0_0_30px_rgba(16,185,129,0.4)] backdrop-blur-xl animate-bounce" style="animation-duration: 6s;">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-xl">🔥</div>
                                <div>
                                    <div class="text-xs font-mono-cyber text-emerald-400 uppercase font-bold">120+ Squads</div>
                                    <div class="text-[11px] text-slate-300">Ready For Battle</div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute -bottom-6 -right-6 z-20 p-4 rounded-2xl esports-card border border-emerald-500/50 shadow-[0_0_30px_rgba(16,185,129,0.4)] backdrop-blur-xl animate-bounce" style="animation-duration: 5s;">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-xl">🏆</div>
                                <div>
                                    <div class="text-xs font-mono-cyber text-emerald-400 uppercase font-bold">Guaranteed Arena</div>
                                    <div class="text-[11px] text-slate-300">Official National Event</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- DYNAMIC OFFICIAL SPONSORS SECTION WITH TIER LEVEL HIERARCHY & PLACEHOLDER LOGOS -->
    <section class="py-24 relative bg-slate-950/90 border-y border-emerald-500/30 overflow-hidden" id="sponsors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal-on-scroll">
                <span class="px-4 py-1.5 rounded-full bg-emerald-950 border border-emerald-500/40 text-emerald-400 text-xs font-mono-cyber uppercase font-bold tracking-widest">OFFICIAL SPONSORSHIP LINEUP</span>
                <h2 class="text-3xl sm:text-5xl font-black uppercase text-white tracking-tight mt-4">
                    TOURNAMENT <span class="text-emerald-400">SPONSORS</span>
                </h2>
                <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto mt-2">
                    Graded by sponsorship hierarchy levels supporting Nepal's esports championship.
                </p>
            </div>

            @php
                $titleSponsors = $sponsors->where('level', 'title');
                $platinumSponsors = $sponsors->where('level', 'platinum');
                $goldSponsors = $sponsors->where('level', 'gold');
                $silverSponsors = $sponsors->where('level', 'silver');
            @endphp

            <!-- LEVEL 1: MEGA TITLE SPONSOR SPOTLIGHT -->
            @if($titleSponsors->count() > 0)
                <div class="mb-16 reveal-on-scroll">
                    <div class="text-center mb-6">
                        <span class="px-4 py-1 clip-corner-sm bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 text-xs font-black font-mono-cyber uppercase tracking-widest shadow-[0_0_20px_rgba(245,158,11,0.6)]">
                            👑 MEGA TITLE SPONSOR
                        </span>
                    </div>
                    <div class="grid grid-cols-1 max-w-2xl mx-auto">
                        @foreach($titleSponsors as $sponsor)
                            <a href="{{ $sponsor->website_url ?? '#' }}" target="_blank" class="p-8 sm:p-10 rounded-3xl esports-card border-2 border-amber-500/60 shadow-[0_0_60px_rgba(245,158,11,0.3)] hover:scale-105 transition-all text-center flex flex-col items-center justify-center gap-4 group">
                                <img src="{{ $sponsor->logo_url ? Storage::url($sponsor->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $sponsor->name }}" class="max-h-28 max-w-[240px] object-contain rounded-xl">
                                <div class="text-xl font-black text-amber-400 tracking-wider uppercase">{{ $sponsor->name }}</div>
                                <div class="text-xs font-mono-cyber text-amber-300 font-bold uppercase tracking-widest">OFFICIAL TITLE SPONSOR</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- LEVEL 2: PLATINUM SPONSORS -->
            @if($platinumSponsors->count() > 0)
                <div class="mb-14 reveal-on-scroll">
                    <div class="text-center mb-6">
                        <span class="px-3.5 py-1 rounded-full bg-cyan-950 border border-cyan-500/50 text-cyan-300 text-xs font-mono-cyber uppercase font-bold tracking-widest">
                            💎 PLATINUM SPONSORS
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-4xl mx-auto">
                        @foreach($platinumSponsors as $sponsor)
                            <a href="{{ $sponsor->website_url ?? '#' }}" target="_blank" class="p-6 rounded-2xl esports-card border border-cyan-500/40 shadow-[0_0_30px_rgba(6,182,212,0.25)] hover:border-cyan-400 transition-all text-center flex flex-col items-center justify-center gap-3 group">
                                <img src="{{ $sponsor->logo_url ? Storage::url($sponsor->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $sponsor->name }}" class="max-h-16 max-w-[180px] object-contain rounded-lg">
                                <span class="text-sm font-black text-cyan-300 tracking-wider uppercase">{{ $sponsor->name }}</span>
                                <span class="text-[11px] font-mono-cyber text-cyan-400 uppercase font-bold">PLATINUM PARTNER</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- LEVEL 3 & 4: GOLD & SILVER SPONSORS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 reveal-on-scroll">
                @if($goldSponsors->count() > 0)
                    <div>
                        <div class="text-center mb-4">
                            <span class="px-3 py-1 rounded bg-yellow-500/20 text-yellow-300 font-mono-cyber text-xs font-bold uppercase">🥇 GOLD SPONSORS</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($goldSponsors as $sponsor)
                                <a href="{{ $sponsor->website_url ?? '#' }}" target="_blank" class="p-5 rounded-xl esports-card border border-yellow-500/30 hover:border-yellow-400 text-center flex flex-col items-center justify-center gap-2 transition-all group">
                                    <img src="{{ $sponsor->logo_url ? Storage::url($sponsor->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $sponsor->name }}" class="max-h-12 max-w-[130px] object-contain rounded">
                                    <span class="font-extrabold text-sm text-yellow-400">{{ $sponsor->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($silverSponsors->count() > 0)
                    <div>
                        <div class="text-center mb-4">
                            <span class="px-3 py-1 rounded bg-slate-800 text-slate-300 font-mono-cyber text-xs font-bold uppercase">🛡️ SILVER SPONSORS</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($silverSponsors as $sponsor)
                                <a href="{{ $sponsor->website_url ?? '#' }}" target="_blank" class="p-5 rounded-xl esports-card border border-slate-700 hover:border-slate-500 text-center flex flex-col items-center justify-center gap-2 transition-all group">
                                    <img src="{{ $sponsor->logo_url ? Storage::url($sponsor->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $sponsor->name }}" class="max-h-12 max-w-[130px] object-contain rounded">
                                    <span class="font-extrabold text-sm text-slate-300">{{ $sponsor->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </section>

    <!-- DYNAMIC OFFICIAL PARTNERS SECTION (WITH LOGOS & PLACEHOLDERS) -->
    <section class="py-24 relative bg-slate-950/70 border-b border-emerald-500/20" id="partners">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-16 reveal-on-scroll">
                <span class="px-4 py-1.5 rounded-full bg-emerald-950 border border-emerald-500/40 text-emerald-400 text-xs font-mono-cyber uppercase font-bold tracking-widest">OFFICIAL EVENT PARTNERS</span>
                <h2 class="text-3xl sm:text-5xl font-black uppercase text-white tracking-tight mt-4">
                    EVENT <span class="text-emerald-400">PARTNERS</span>
                </h2>
                <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto mt-2">
                    Media, hospitality, broadcasting, and technology partners bringing Outlaw Showdown 2026 to life.
                </p>
            </div>

            <!-- PARTNERS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 reveal-on-scroll">
                @foreach($partners as $partner)
                    <div class="p-7 rounded-3xl esports-card border border-emerald-500/30 hover:border-emerald-400 transition-all duration-300 hover:-translate-y-2 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 clip-corner-sm bg-emerald-500/20 text-emerald-300 font-mono-cyber text-xs font-bold uppercase border border-emerald-500/40">
                                    {{ $partner->title }}
                                </span>
                                @if($partner->level === 'major')
                                    <span class="text-[10px] font-mono-cyber font-bold text-amber-400 uppercase">MAJOR PARTNER</span>
                                @endif
                            </div>
                            <h3 class="text-xl font-extrabold text-white group-hover:text-emerald-400 transition-colors mb-4">
                                {{ $partner->name }}
                            </h3>
                        </div>

                        <div class="pt-4 border-t border-emerald-500/20 flex items-center justify-between mt-4">
                            <img src="{{ $partner->logo_url ? Storage::url($partner->logo_url) : asset('images/sponsor_placeholder.png') }}" alt="{{ $partner->name }}" class="max-h-12 max-w-[140px] object-contain rounded">

                            @if($partner->website_url)
                                <a href="{{ $partner->website_url }}" target="_blank" class="text-xs font-bold uppercase text-slate-300 hover:text-emerald-400 flex items-center gap-1 transition-colors">
                                    Visit <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- GAME TITLES ROSTER SECTION -->
    <section class="py-24 relative" id="games">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal-on-scroll">
                <span class="px-4 py-1.5 rounded-full bg-emerald-950 border border-emerald-500/40 text-emerald-400 text-xs font-mono-cyber uppercase font-bold tracking-widest">SELECT YOUR CHAMPIONSHIP</span>
                <h2 class="text-3xl sm:text-5xl font-black uppercase text-white tracking-tight mt-4">
                    TOURNAMENT <span class="text-emerald-400">GAME TITLES</span>
                </h2>
                <p class="text-slate-400 text-base sm:text-lg max-w-2xl mx-auto mt-3">
                    Register solo or build your full squad. Entry fee is just <span class="text-emerald-400 font-bold">Rs. 100 per player</span> for any title!
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- CARD 1: PUBG MOBILE -->
                <div class="reveal-on-scroll rounded-3xl esports-card p-7 border border-emerald-500/30 hover:border-emerald-400 transition-all duration-500 hover:-translate-y-3 group relative overflow-hidden">
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-3.5 py-1 clip-corner-sm bg-emerald-500/20 text-emerald-300 font-mono-cyber text-xs uppercase font-bold border border-emerald-500/40">Squad • Mobile</span>
                        <span class="text-xs font-mono-cyber text-slate-300 font-bold">Rs. 100 / Person</span>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">🔫</div>
                    <h3 class="text-2xl font-extrabold text-white group-hover:text-emerald-400 transition-colors mb-3">PUBG Mobile</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Erangel & Miramar tactical battle royale. Drop in, loot up, out-rotate rivals, and claim the Chicken Dinner!</p>
                    <div class="flex items-center justify-between pt-5 border-t border-emerald-500/20">
                        <span class="text-xs font-mono-cyber text-emerald-400">Format: TPP Squads</span>
                        <a href="{{ url('/maidan/login') }}" onclick="playCyberSound()" class="px-4 py-2 clip-corner-sm bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider flex items-center gap-1 transition-all">Register</a>
                    </div>
                </div>

                <!-- CARD 2: MLBB OPEN -->
                <div class="reveal-on-scroll rounded-3xl esports-card p-7 border border-emerald-500/30 hover:border-emerald-400 transition-all duration-500 hover:-translate-y-3 group relative overflow-hidden">
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-3.5 py-1 clip-corner-sm bg-emerald-500/20 text-emerald-300 font-mono-cyber text-xs uppercase font-bold border border-emerald-500/40">5v5 • Open Category</span>
                        <span class="text-xs font-mono-cyber text-slate-300 font-bold">Rs. 100 / Person</span>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">⚔️</div>
                    <h3 class="text-2xl font-extrabold text-white group-hover:text-emerald-400 transition-colors mb-3">MLBB (Open)</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Mobile Legends open category. Draft your meta picks, execute Lord fights, and push to victory.</p>
                    <div class="flex items-center justify-between pt-5 border-t border-emerald-500/20">
                        <span class="text-xs font-mono-cyber text-emerald-400">Format: Custom Draft 5v5</span>
                        <a href="{{ url('/maidan/login') }}" onclick="playCyberSound()" class="px-4 py-2 clip-corner-sm bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider flex items-center gap-1 transition-all">Register</a>
                    </div>
                </div>

                <!-- CARD 3: MLBB WOMEN'S -->
                <div class="reveal-on-scroll rounded-3xl esports-card p-7 border border-purple-500/40 hover:border-purple-400 transition-all duration-500 hover:-translate-y-3 group relative overflow-hidden">
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-3.5 py-1 clip-corner-sm bg-purple-500/25 text-purple-300 font-mono-cyber text-xs uppercase font-bold border border-purple-500/40">5v5 • Women's Category</span>
                        <span class="text-xs font-mono-cyber text-slate-300 font-bold">Rs. 100 / Person</span>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-purple-500/15 border border-purple-500/30 flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">👑</div>
                    <h3 class="text-2xl font-extrabold text-white group-hover:text-purple-400 transition-colors mb-3">MLBB (Women's)</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Dedicated female championship. Prove your squad's tactical mastery and claim the championship crown!</p>
                    <div class="flex items-center justify-between pt-5 border-t border-purple-500/20">
                        <span class="text-xs font-mono-cyber text-purple-300">Format: Female Squads</span>
                        <a href="{{ url('/maidan/login') }}" onclick="playCyberSound()" class="px-4 py-2 clip-corner-sm bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider flex items-center gap-1 transition-all">Register</a>
                    </div>
                </div>

                <!-- CARD 4: EFOOTBALL -->
                <div class="reveal-on-scroll rounded-3xl esports-card p-7 border border-emerald-500/30 hover:border-emerald-400 transition-all duration-500 hover:-translate-y-3 group relative overflow-hidden">
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-3.5 py-1 clip-corner-sm bg-emerald-500/20 text-emerald-300 font-mono-cyber text-xs uppercase font-bold border border-emerald-500/40">1v1 • Mobile Soccer</span>
                        <span class="text-xs font-mono-cyber text-slate-300 font-bold">Rs. 100 / Person</span>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">⚽</div>
                    <h3 class="text-2xl font-extrabold text-white group-hover:text-emerald-400 transition-colors mb-3">eFootball Mobile</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">1v1 solo football showdown. Build your dream squad, outplay your opponent, and score the tournament winner!</p>
                    <div class="flex items-center justify-between pt-5 border-t border-emerald-500/20">
                        <span class="text-xs font-mono-cyber text-emerald-400">Format: 1v1 Knockout</span>
                        <a href="{{ url('/maidan/login') }}" onclick="playCyberSound()" class="px-4 py-2 clip-corner-sm bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider flex items-center gap-1 transition-all">Register</a>
                    </div>
                </div>

                <!-- CARD 5: VALORANT -->
                <div class="reveal-on-scroll rounded-3xl esports-card p-7 border border-red-500/40 hover:border-red-400 transition-all duration-500 hover:-translate-y-3 group relative overflow-hidden">
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-3.5 py-1 clip-corner-sm bg-red-500/25 text-red-300 font-mono-cyber text-xs uppercase font-bold border border-red-500/40">5v5 • Tactical FPS</span>
                        <span class="text-xs font-mono-cyber text-slate-300 font-bold">Rs. 100 / Person</span>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-red-500/15 border border-red-500/30 flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">🎯</div>
                    <h3 class="text-2xl font-extrabold text-white group-hover:text-red-400 transition-colors mb-3">Valorant</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">5v5 agent-based tactical shooter. Execute site takes, plant the Spike, and click heads under pressure.</p>
                    <div class="flex items-center justify-between pt-5 border-t border-red-500/20">
                        <span class="text-xs font-mono-cyber text-red-400">Format: Competitive 5v5</span>
                        <a href="{{ url('/maidan/login') }}" onclick="playCyberSound()" class="px-4 py-2 clip-corner-sm bg-red-500 hover:bg-red-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider flex items-center gap-1 transition-all">Register</a>
                    </div>
                </div>

                <!-- CARD 6: COSPLAY -->
                <div class="reveal-on-scroll rounded-3xl esports-card p-7 border border-amber-500/40 hover:border-amber-400 transition-all duration-500 hover:-translate-y-3 group relative overflow-hidden">
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-3.5 py-1 clip-corner-sm bg-amber-500/25 text-amber-300 font-mono-cyber text-xs uppercase font-bold border border-amber-500/40">Stage Performance</span>
                        <span class="text-xs font-mono-cyber text-slate-300 font-bold">Rs. 100 / Person</span>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform">🎭</div>
                    <h3 class="text-2xl font-extrabold text-white group-hover:text-amber-400 transition-colors mb-3">Cosplay Showdown</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Bring your anime & gaming characters to life! Main stage runway walk, character acts, and prop judging.</p>
                    <div class="flex items-center justify-between pt-5 border-t border-amber-500/20">
                        <span class="text-xs font-mono-cyber text-amber-400">Category: Solo / Duo</span>
                        <a href="{{ url('/maidan/login') }}" onclick="playCyberSound()" class="px-4 py-2 clip-corner-sm bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider flex items-center gap-1 transition-all">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LIVE MATCH CENTER & BRACKETS -->
    <section class="py-24 relative" id="hub">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6 reveal-on-scroll">
                <div>
                    <div class="text-xs font-mono-cyber text-emerald-400 uppercase tracking-widest mb-2">LIVE TOURNAMENT OPERATIONS</div>
                    <h2 class="text-3xl sm:text-5xl font-black uppercase text-white">MATCH HUB & <span class="text-emerald-400">BRACKETS</span></h2>
                </div>
                <div class="flex items-center gap-2 bg-slate-900/90 p-2 rounded-2xl border border-emerald-500/30">
                    <button onclick="playCyberSound(); switchTab('leaderboard')" id="btn-leaderboard" class="px-5 py-2.5 text-xs font-extrabold rounded-xl bg-emerald-500 text-slate-950 transition-all shadow-[0_0_15px_rgba(16,185,129,0.4)]">Leaderboard</button>
                    <button onclick="playCyberSound(); switchTab('bracket')" id="btn-bracket" class="px-5 py-2.5 text-xs font-extrabold rounded-xl text-slate-300 hover:text-white transition-all">Brackets Tree</button>
                    <button onclick="playCyberSound(); switchTab('news')" id="btn-news" class="px-5 py-2.5 text-xs font-extrabold rounded-xl text-slate-300 hover:text-white transition-all">Announcements</button>
                </div>
            </div>

            <!-- TAB 1: LEADERBOARD -->
            <div id="tab-leaderboard" class="reveal-on-scroll rounded-3xl esports-card border border-emerald-500/30 overflow-hidden shadow-[0_0_40px_rgba(16,185,129,0.15)]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-emerald-950/60 text-emerald-400 font-mono-cyber text-xs uppercase border-b border-emerald-500/20">
                            <tr>
                                <th class="p-5">Rank</th>
                                <th class="p-5">Team / Competitor</th>
                                <th class="p-5">Game Title</th>
                                <th class="p-5">Matches Played</th>
                                <th class="p-5">Kills / Score</th>
                                <th class="p-5">Total Points</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-500/10 text-slate-200">
                            <tr class="hover:bg-emerald-500/10 transition-colors">
                                <td class="p-5 font-black text-emerald-400 text-lg">#1</td>
                                <td class="p-5 font-extrabold text-white flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/30">🥇</span>
                                    Team Velocity
                                </td>
                                <td class="p-5 font-mono-cyber text-slate-300">PUBG Mobile</td>
                                <td class="p-5">6</td>
                                <td class="p-5 text-emerald-400 font-bold">48 Kills</td>
                                <td class="p-5 font-black text-white font-mono-cyber text-base">102 PTS</td>
                            </tr>
                            <tr class="hover:bg-emerald-500/10 transition-colors">
                                <td class="p-5 font-black text-slate-300 text-lg">#2</td>
                                <td class="p-5 font-extrabold text-white flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-slate-800 text-slate-300 flex items-center justify-center text-sm border border-slate-700">🥈</span>
                                    Neon Knights
                                </td>
                                <td class="p-5 font-mono-cyber text-slate-300">MLBB (Open)</td>
                                <td class="p-5">5</td>
                                <td class="p-5 text-emerald-400 font-bold">5-0 Win Streak</td>
                                <td class="p-5 font-black text-white font-mono-cyber text-base">90 PTS</td>
                            </tr>
                            <tr class="hover:bg-emerald-500/10 transition-colors">
                                <td class="p-5 font-black text-amber-500 text-lg">#3</td>
                                <td class="p-5 font-extrabold text-white flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm border border-amber-500/30">🥉</span>
                                    Valkyrie Queens
                                </td>
                                <td class="p-5 font-mono-cyber text-slate-300">MLBB (Women's)</td>
                                <td class="p-5">4</td>
                                <td class="p-5 text-emerald-400 font-bold">4-0 Win Streak</td>
                                <td class="p-5 font-black text-white font-mono-cyber text-base">84 PTS</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: BRACKET TREE -->
            <div id="tab-bracket" class="hidden rounded-3xl esports-card p-8 sm:p-10 border border-emerald-500/30">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-emerald-500/20">
                        <div class="text-xs font-mono-cyber text-emerald-400 uppercase font-bold mb-6">QUARTER FINALS</div>
                        <div class="space-y-4">
                            <div class="p-4 rounded-xl bg-slate-950 border border-emerald-500/30 flex justify-between font-bold"><span>Team Alpha</span><span class="text-emerald-400">2</span></div>
                            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex justify-between text-slate-500"><span>Omega Gaming</span><span>0</span></div>
                        </div>
                    </div>
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-emerald-500/20">
                        <div class="text-xs font-mono-cyber text-emerald-400 uppercase font-bold mb-6">SEMI FINALS</div>
                        <div class="space-y-4">
                            <div class="p-4 rounded-xl bg-slate-950 border border-emerald-500/30 flex justify-between font-bold"><span>Team Alpha</span><span class="text-emerald-400">2</span></div>
                            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex justify-between text-slate-500"><span>Valkyrie Squad</span><span>1</span></div>
                        </div>
                    </div>
                    <div class="p-5 rounded-2xl bg-emerald-950/50 border border-emerald-500/50 shadow-[0_0_30px_rgba(16,185,129,0.3)]">
                        <div class="text-xs font-mono-cyber text-emerald-400 uppercase font-bold mb-6">GRAND FINALS ARENA</div>
                        <div class="p-6 rounded-2xl bg-slate-950 border border-emerald-500/50 text-center">
                            <div class="text-xs text-slate-400 mb-2 font-mono-cyber">CHAMPIONSHIP MATCH</div>
                            <div class="text-xl font-black text-white">Team Alpha vs Apex Spec-Ops</div>
                            <div class="mt-4 px-4 py-2 rounded-lg bg-emerald-500/20 text-emerald-400 font-mono-cyber font-bold text-xs inline-block">LIVE SUNDAY 18:00 IST</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: NEWS -->
            <div id="tab-news" class="hidden grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="p-8 rounded-3xl esports-card border border-emerald-500/30">
                    <span class="px-3 py-1 rounded bg-emerald-500/20 text-emerald-400 font-mono-cyber text-xs font-bold">ANNOUNCEMENT</span>
                    <h3 class="text-2xl font-bold text-white mt-4 mb-3">Registration Open for MLBB Women's Category</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">Assemble your female squad for Outlaw Showdown 2026. Entry fee Rs. 100 per player with dedicated prize pool rewards!</p>
                </div>
                <div class="p-8 rounded-3xl esports-card border border-emerald-500/30">
                    <span class="px-3 py-1 rounded bg-emerald-500/20 text-emerald-400 font-mono-cyber text-xs font-bold">RULES & MAPS</span>
                    <h3 class="text-2xl font-bold text-white mt-4 mb-3">PUBG Mobile Map Rotation Announced</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">Official map sequence: Erangel -> Miramar -> Erangel -> Sanhok. Check full tournament rulebook in player dashboard.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- SPONSOR INQUIRY SECTION & LIVEWIRE MODAL -->
    <section class="py-24 relative bg-gradient-to-b from-slate-950 to-slate-900 border-t border-emerald-500/20" id="sponsor-query-section">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-on-scroll">
            <span class="px-4 py-1.5 rounded-full bg-emerald-950 border border-emerald-500/40 text-emerald-400 text-xs font-mono-cyber uppercase font-bold tracking-widest">BRAND PARTNERSHIPS</span>
            <h2 class="text-3xl sm:text-5xl font-black uppercase text-white tracking-tight mt-4 mb-6">
                PARTNER WITH <span class="text-emerald-400">OUTLAW SHOWDOWN 2026</span>
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

    <!-- FOOTER -->
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
                <a href="#partners" class="hover:underline">Partners</a>
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

        function switchTab(tabName) {
            ['leaderboard', 'bracket', 'news'].forEach(t => {
                document.getElementById('tab-' + t).classList.add('hidden');
                document.getElementById('btn-' + t).className = "px-5 py-2.5 text-xs font-extrabold rounded-xl text-slate-300 hover:text-white transition-all";
            });
            document.getElementById('tab-' + tabName).classList.remove('hidden');
            document.getElementById('btn-' + tabName).className = "px-5 py-2.5 text-xs font-extrabold rounded-xl bg-emerald-500 text-slate-950 transition-all shadow-[0_0_15px_rgba(16,185,129,0.4)]";
        }

        document.addEventListener("DOMContentLoaded", function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.reveal-on-scroll, .reveal-left, .reveal-right').forEach(el => observer.observe(el));
        });

        document.addEventListener('mousemove', (e) => {
            const container = document.getElementById('hero-mascot-container');
            if (!container) return;
            const x = (e.clientX / window.innerWidth - 0.5) * 20;
            const y = (e.clientY / window.innerHeight - 0.5) * 20;
            container.style.transform = `translate3d(${x}px, ${y}px, 0)`;
        });

        const canvas = document.getElementById('particle-canvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;

            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            });

            const particles = Array.from({ length: 45 }, () => ({
                x: Math.random() * width,
                y: Math.random() * height,
                radius: Math.random() * 2 + 1,
                vx: (Math.random() - 0.5) * 0.6,
                vy: (Math.random() - 0.5) * 0.6
            }));

            function drawParticles() {
                ctx.clearRect(0, 0, width, height);
                ctx.fillStyle = 'rgba(52, 211, 153, 0.4)';
                ctx.strokeStyle = 'rgba(16, 185, 129, 0.08)';

                particles.forEach((p, i) => {
                    p.x += p.vx;
                    p.y += p.vy;

                    if (p.x < 0 || p.x > width) p.vx *= -1;
                    if (p.y < 0 || p.y > height) p.vy *= -1;

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fill();

                    for (let j = i + 1; j < particles.length; j++) {
                        const p2 = particles[j];
                        const dist = Math.hypot(p.x - p2.x, p.y - p2.y);
                        if (dist < 130) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.stroke();
                        }
                    }
                });

                requestAnimationFrame(drawParticles);
            }

            drawParticles();
        }

        let totalSeconds = 14 * 86400 + 8 * 3600 + 45 * 60 + 22;
        setInterval(() => {
            if (totalSeconds <= 0) return;
            totalSeconds--;
            const d = Math.floor(totalSeconds / 86400);
            const h = Math.floor((totalSeconds % 86400) / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;
            document.getElementById('days').innerText = String(d).padStart(2, '0');
            document.getElementById('hours').innerText = String(h).padStart(2, '0');
            document.getElementById('minutes').innerText = String(m).padStart(2, '0');
            document.getElementById('seconds').innerText = String(s).padStart(2, '0');
        }, 1000);
    </script>
</body>
</html>
