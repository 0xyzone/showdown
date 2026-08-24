<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>{{ $activeTournament?->name ? $activeTournament->name . ' — Nepal\'s Biggest Esports Battle' : 'OUTLAW SHOWDOWN — Official Esports Championship' }}</title>
    <meta name="description" content="{{ $activeTournament?->hero_subheadline ?? 'Nepal\'s premier national esports championship circuit. Where champions rise, rivalries begin, and history is made.' }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @php
        $themeColor = $activeTournament?->theme_color ?? '#10b981';

        $hex = ltrim($themeColor, '#');
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } elseif (strlen($hex) >= 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        } else {
            $r = 16; $g = 185; $b = 129;
        }
        $rgb = "$r, $g, $b";

        $luminance = ($r * 0.2126 + $g * 0.7152 + $b * 0.0722) / 255;
        $btnTextColor = $luminance > 0.55 ? '#020406' : '#ffffff';
        $badgeTextColor = $luminance > 0.55 ? "rgb(" . max(0, $r - 40) . "," . max(0, $g - 40) . "," . max(0, $b - 40) . ")" : $themeColor;

        $tournamentName = $activeTournament?->name ?? 'OUTLAW SHOWDOWN';
        $seasonVersion = $activeTournament?->season_version ?? '2026';
        $entryFee = $activeTournament?->entry_fee ?? 0;
        $entryFeeSuffix = $activeTournament?->entry_fee_suffix ?: 'person';

        $calculatedPrizePool = 0;
        if ($activeTournament && $activeTournament->gameTitles->count() > 0) {
            foreach ($activeTournament->gameTitles as $g) {
                $calculatedPrizePool += (float) ($g->pivot->prize_pool ?? 0);
            }
        }
        $prizePool = $calculatedPrizePool > 0 ? $calculatedPrizePool : ($activeTournament?->prize_pool_total ?? 0);

        $heroHeadline = $activeTournament?->hero_headline ?: "NEPAL'S PREMIER ESPORTS CHAMPIONSHIP";
        $heroSubheadline = $activeTournament?->hero_subheadline ?: "Where champions rise, rivalries begin, and history is made.";
        $registrationEnd = $activeTournament?->registration_end ? $activeTournament->registration_end->toIso8601String() : null;
        $eventDays = $activeTournament?->eventDays ? $activeTournament->eventDays->sortBy('order') : collect();
    @endphp

    <style>
        :root {
            --primary: {{ $themeColor }};
            --primary-rgb: {{ $rgb }};
            --text-on-primary: {{ $btnTextColor }};
            --primary-badge-text: {{ $badgeTextColor }};
        }
    </style>
</head>
<body class="editorial-bg min-h-screen antialiased flex flex-col justify-between selection:bg-emerald-500/30 selection:text-white text-slate-200">

    <!-- COMPACT, CLEAN FLOATING NAVBAR -->
    <header id="floating-navbar" class="sticky top-0 z-50 nav-floating transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-4">
            
            <!-- BRAND LOGO -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group shrink-0 min-w-0">
                @if($activeTournament?->logo_path && file_exists(public_path('storage/' . $activeTournament->logo_path)))
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#090d14] border border-white/15 p-1 group-hover:border-emerald-400/50 transition-all flex items-center justify-center shrink-0 shadow-lg">
                        <img src="{{ asset('storage/' . $activeTournament->logo_path) }}" alt="{{ $tournamentName }}" class="w-full h-full object-contain">
                    </div>
                @else
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-[#020406] flex items-center justify-center font-display font-black text-xs tracking-tighter shrink-0 shadow-md group-hover:scale-105 transition-transform">
                        OS
                    </div>
                @endif

                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-display font-black text-sm sm:text-base tracking-wide text-white uppercase truncate">
                            {{ $tournamentName }}
                        </span>
                        @if($seasonVersion)
                            <span class="hidden sm:inline-block px-1.5 py-0.5 text-[9px] font-mono-tech uppercase font-bold rounded bg-emerald-500/15 border border-emerald-500/30 text-emerald-400">
                                {{ $seasonVersion }}
                            </span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- STREAMLINED DYNAMIC NAVIGATION (ONLY SHOWN IF DATA EXISTS) -->
            <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-xs font-bold tracking-wider uppercase text-slate-300 font-mono-tech">
                @if($gameTitles->isNotEmpty())
                    <a href="#games" class="nav-spy-link hover:text-white transition-colors">Disciplines</a>
                @endif
                @if($eventDays->isNotEmpty())
                    <a href="#schedule" class="nav-spy-link hover:text-white transition-colors">Schedule</a>
                @endif
                @if($approvedRegistrations->isNotEmpty())
                    <a href="#teams" class="nav-spy-link hover:text-white transition-colors">Contenders</a>
                @endif
                @if($sponsors->isNotEmpty() || $partners->isNotEmpty())
                    <a href="#sponsors" class="nav-spy-link hover:text-white transition-colors">Partners</a>
                @endif
                <a href="{{ url('/guide') }}" class="nav-spy-link text-emerald-400 hover:text-emerald-300 transition-colors font-bold">Guide</a>
            </nav>

            <!-- ACTION BUTTONS -->
            <div class="flex items-center gap-3 shrink-0">
                @auth('participant')
                    <a href="{{ url('/mukhyadwar') }}" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg text-xs font-black btn-primary-action flex items-center gap-1.5">
                        <span>Player Portal</span>
                        <span>→</span>
                    </a>
                @else
                    <a href="{{ url('/mukhyadwar/login') }}" class="hidden sm:inline-block px-3 py-2 text-xs font-bold text-slate-300 hover:text-white transition-colors">
                        Sign In
                    </a>
                    <a href="{{ url('/mukhyadwar/register') }}" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg text-xs font-black btn-primary-action">
                        Register Squad
                    </a>
                @endauth

                <!-- MOBILE TOGGLE (ONLY IF LINKS EXIST) -->
                @if($gameTitles->isNotEmpty() || $eventDays->isNotEmpty() || $approvedRegistrations->isNotEmpty() || $sponsors->isNotEmpty() || $partners->isNotEmpty())
                    <button id="mobile-nav-toggle" onclick="toggleMobileNav()" aria-label="Toggle navigation menu" class="md:hidden p-2 rounded-lg bg-[#0e131d] border border-white/10 text-slate-300 hover:text-white focus:outline-none cursor-pointer">
                        <svg id="menu-bars" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg id="menu-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>

        </div>

        <!-- MOBILE DRAWER -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-white/10 bg-[#020406]/98 px-6 py-5 space-y-3 backdrop-blur-2xl">
            <div class="flex flex-col space-y-2.5 text-xs font-bold tracking-wider uppercase text-slate-300 font-mono-tech">
                @if($gameTitles->isNotEmpty())
                    <a href="#games" onclick="toggleMobileNav()" class="py-1.5 hover:text-white">Disciplines</a>
                @endif
                @if($eventDays->isNotEmpty())
                    <a href="#schedule" onclick="toggleMobileNav()" class="py-1.5 hover:text-white">Schedule</a>
                @endif
                @if($approvedRegistrations->isNotEmpty())
                    <a href="#teams" onclick="toggleMobileNav()" class="py-1.5 hover:text-white">Contenders</a>
                @endif
                @if($sponsors->isNotEmpty() || $partners->isNotEmpty())
                    <a href="#sponsors" onclick="toggleMobileNav()" class="py-1.5 hover:text-white">Partners</a>
                @endif
                <a href="{{ url('/guide') }}" class="py-1.5 text-emerald-400 font-bold hover:text-emerald-300">Manager Guide</a>
            </div>

            @guest('participant')
                <div class="pt-3 border-t border-white/10 flex flex-col gap-2">
                    <a href="{{ url('/mukhyadwar/login') }}" class="w-full py-2.5 rounded-lg text-xs font-bold uppercase text-center bg-white/5 border border-white/10 text-white">
                        Sign In to Portal
                    </a>
                </div>
            @endguest
        </div>
    </header>

    <main class="space-y-16 sm:space-y-24">
        
        <!-- 1. DYNAMIC TOURNAMENT HERO -->
        <section id="hero" class="relative min-h-[75vh] lg:min-h-[85vh] flex items-center py-12 sm:py-20 border-b border-white/5 overflow-hidden mb-0">
            <canvas id="hero-particle-canvas" class="absolute inset-0 w-full h-full pointer-events-none z-0"></canvas>
            <div class="absolute inset-0 tech-grid opacity-40 pointer-events-none"></div>
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-emerald-500/10 blur-[140px] rounded-full pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
                @if($activeTournament)
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                        
                        <!-- LEFT COMMAND COLUMN -->
                        <div class="lg:col-span-7 space-y-6">
                            
                            <!-- Badges -->
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md text-xs font-mono-tech uppercase font-bold status-pill">
                                    <span class="w-2 h-2 rounded-full animate-ping" style="background-color: var(--primary);"></span>
                                    <span>{{ strtoupper(str_replace('_', ' ', $activeTournament->status)) }}</span>
                                </div>
                                @if($activeTournament->location)
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-mono-tech uppercase font-bold bg-white/5 border border-white/10 text-slate-300">
                                        <span>📍 {{ $activeTournament->location }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Title & Subheadline -->
                            <div class="space-y-3">
                                <h1 class="font-display text-3xl sm:text-5xl lg:text-6xl font-black uppercase text-white tracking-tight leading-[1.05]">
                                    {{ $heroHeadline }}
                                </h1>
                                <p class="text-slate-300 text-sm sm:text-base lg:text-lg max-w-xl font-normal leading-relaxed">
                                    {{ $heroSubheadline }}
                                </p>
                            </div>

                            <!-- Quick CTAs -->
                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                <a href="{{ url('/mukhyadwar/register') }}" class="px-7 py-3.5 rounded-xl btn-primary-action text-xs font-black flex items-center gap-2">
                                    <span>Register Squad</span>
                                    <span>→</span>
                                </a>

                                @if($activeTournament->rules_doc_link)
                                    <a href="{{ $activeTournament->rules_doc_link }}" target="_blank" class="px-5 py-3.5 rounded-xl btn-secondary-action text-xs flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Official Rulebook</span>
                                    </a>
                                @endif

                                @if($activeTournament->discord_server_url)
                                    <a href="{{ $activeTournament->discord_server_url }}" target="_blank" class="px-5 py-3.5 rounded-xl btn-secondary-action text-xs flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#5865F2]" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994.021-.041.001-.09-.041-.106a13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128c.126-.093.252-.19.373-.287a.074.074 0 0 1 .078-.01c3.927 1.793 8.18 1.793 12.061 0a.074.074 0 0 1 .079.009c.12.098.245.195.372.288a.077.077 0 0 1-.006.127c-.598.35-1.22.656-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.028z"/></svg>
                                        <span>Discord</span>
                                    </a>
                                @endif
                            </div>

                        </div>

                        <!-- RIGHT METRICS & COUNTDOWN CARD -->
                        <div class="lg:col-span-5">
                            <div class="editorial-card-featured p-6 sm:p-7 rounded-2xl space-y-5 relative overflow-hidden tilt-card border border-emerald-500/30">
                                
                                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                                    @if($prizePool > 0)
                                        <div>
                                            <div class="text-[10px] font-mono-tech uppercase font-bold text-slate-400 tracking-wider">PRIZE POOL</div>
                                            <div class="font-display font-black text-2xl sm:text-3xl text-white mt-0.5 tracking-tight">
                                                Rs. {{ number_format($prizePool) }}
                                            </div>
                                        </div>
                                    @endif
                                    <div class="{{ $prizePool > 0 ? 'text-right' : 'text-left' }}">
                                        <div class="text-[10px] font-mono-tech uppercase font-bold text-slate-400 tracking-wider">REGISTRATION PASS</div>
                                        <div class="font-display font-bold text-lg sm:text-xl text-white mt-0.5">
                                            Rs. {{ number_format($entryFee) }} <span class="text-xs font-mono-tech text-slate-400 font-normal">/{{ $entryFeeSuffix }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- COUNTDOWN (IF END DATE SET) -->
                                @if($registrationEnd)
                                    <div id="countdown-target" data-date="{{ $registrationEnd }}">
                                        <div class="text-[10px] font-mono-tech uppercase tracking-wider text-emerald-400 font-bold mb-2.5">
                                            ⚡ REGISTRATION CLOSES IN
                                        </div>
                                        <div class="grid grid-cols-4 gap-2 text-center font-display">
                                            <div class="p-2.5 rounded-xl bg-[#020406] border border-white/10 shadow-inner">
                                                <div id="cd-days" class="text-lg sm:text-xl font-black text-white">00</div>
                                                <div class="text-[9px] font-mono-tech text-slate-400 uppercase">Days</div>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-[#020406] border border-white/10 shadow-inner">
                                                <div id="cd-hours" class="text-lg sm:text-xl font-black text-white">00</div>
                                                <div class="text-[9px] font-mono-tech text-slate-400 uppercase">Hours</div>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-[#020406] border border-white/10 shadow-inner">
                                                <div id="cd-mins" class="text-lg sm:text-xl font-black text-white">00</div>
                                                <div class="text-[9px] font-mono-tech text-slate-400 uppercase">Mins</div>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-[#020406] border border-emerald-500/40 shadow-inner">
                                                <div id="cd-secs" class="text-lg sm:text-xl font-black text-emerald-400">00</div>
                                                <div class="text-[9px] font-mono-tech text-slate-400 uppercase">Secs</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- STATS TILES -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="p-3 rounded-xl bg-[#020406]/70 border border-white/10">
                                        <div class="text-[10px] font-mono-tech uppercase text-slate-400">Titles</div>
                                        <div class="font-display font-bold text-sm sm:text-base text-white mt-0.5">{{ $gameTitles->count() }} Games</div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-[#020406]/70 border border-white/10">
                                        <div class="text-[10px] font-mono-tech uppercase text-slate-400">Registered</div>
                                        <div class="font-display font-bold text-sm sm:text-base text-white mt-0.5">{{ $registrationCount }} Squads</div>
                                    </div>
                                </div>

                                <a href="{{ url('/mukhyadwar/register') }}" class="w-full py-3 rounded-xl btn-primary-action text-xs font-black text-center block">
                                    Enter Tournament →
                                </a>

                            </div>
                        </div>

                    </div>
                @else
                    <div class="py-16 text-center max-w-xl mx-auto space-y-4">
                        <div class="inline-flex px-3 py-1 rounded text-xs font-mono-tech uppercase status-pill">Offseason Preparation</div>
                        <h1 class="font-display text-3xl sm:text-4xl font-black uppercase text-white">Next Championship Series In Preparation</h1>
                        <p class="text-slate-400 text-sm">Register your player profile on the portal to get ready for upcoming events.</p>
                        <a href="{{ url('/mukhyadwar/register') }}" class="inline-block px-6 py-3 rounded-xl btn-primary-action text-xs font-black">Create Player Profile</a>
                    </div>
                @endif
            </div>
        </section>

        <!-- KINETIC SCROLLING MARQUEE TICKER 1 -->
        <div class="w-full bg-[#03070d] border-y border-white/10 py-3.5 overflow-hidden">
            <div class="marquee-container">
                <div class="marquee-content text-xs sm:text-sm font-mono-tech font-bold uppercase tracking-widest text-slate-300">
                    <span class="flex items-center gap-2.5"><span class="text-emerald-400">⚡</span> NEPAL'S BIGGEST ESPORTS BATTLE</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-cyan-400">🎮</span> PUBG MOBILE • MLBB OPEN & WOMEN'S • VALORANT</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-amber-400">🏆</span> RS. {{ $prizePool > 0 ? number_format($prizePool) : '500,000+' }} PRIZE POOL</span>
                    <span class="text-slate-600">//</span>
                    @if($activeTournament && $activeTournament->is_lan && $activeTournament->venue_name)
                        <span class="flex items-center gap-2.5"><span class="text-purple-400">📍</span> LAN FINALS AT {{ strtoupper($activeTournament->venue_name) }}</span>
                    @else
                        <span class="flex items-center gap-2.5"><span class="text-purple-400">📍</span> NATIONAL CHAMPIONSHIP FINALS</span>
                    @endif
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-emerald-400">🔥</span> ZERO ANTI-CHEAT TOLERANCE</span>
                    <span class="text-slate-600">//</span>
                </div>
                <div class="marquee-content text-xs sm:text-sm font-mono-tech font-bold uppercase tracking-widest text-slate-300" aria-hidden="true">
                    <span class="flex items-center gap-2.5"><span class="text-emerald-400">⚡</span> NEPAL'S BIGGEST ESPORTS BATTLE</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-cyan-400">🎮</span> PUBG MOBILE • MLBB OPEN & WOMEN'S • VALORANT</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-amber-400">🏆</span> RS. {{ $prizePool > 0 ? number_format($prizePool) : '500,000+' }} PRIZE POOL</span>
                    <span class="text-slate-600">//</span>
                    @if($activeTournament && $activeTournament->is_lan && $activeTournament->venue_name)
                        <span class="flex items-center gap-2.5"><span class="text-purple-400">📍</span> LAN FINALS AT {{ strtoupper($activeTournament->venue_name) }}</span>
                    @else
                        <span class="flex items-center gap-2.5"><span class="text-purple-400">📍</span> NATIONAL CHAMPIONSHIP FINALS</span>
                    @endif
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-emerald-400">🔥</span> ZERO ANTI-CHEAT TOLERANCE</span>
                    <span class="text-slate-600">//</span>
                </div>
            </div>
        </div>

        <!-- 2. GAME DISCIPLINES (DYNAMIC ONLY) -->
        @if($gameTitles->isNotEmpty())
            <section id="games" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-10 gap-3">
                    <div>
                        <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                            CHAMPIONSHIP DISCIPLINES
                        </span>
                        <h2 class="font-display text-2xl sm:text-4xl font-black uppercase text-white">
                            Game Battlefield
                        </h2>
                    </div>
                    <div class="text-xs font-mono-tech text-slate-400">
                        {{ $gameTitles->count() }} Disciplines Active
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($gameTitles as $game)
                        @php
                            $allocatedPrize = $game->pivot?->prize_pool ? (float) $game->pivot->prize_pool : 0;
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
                        <div class="editorial-card rounded-2xl p-6 flex flex-col justify-between group tilt-card">
                            <div class="space-y-5">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="w-14 h-14 rounded-xl bg-[#080c14] border border-white/10 flex items-center justify-center p-2 shrink-0 group-hover:border-emerald-500/50 transition-colors shadow-md">
                                        @if($game->logo_path && file_exists(public_path('storage/' . $game->logo_path)))
                                            <img src="{{ asset('storage/' . $game->logo_path) }}" alt="{{ $game->name }}" class="w-full h-full object-contain">
                                        @else
                                            <span class="text-2xl">🎮</span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-mono-tech uppercase font-bold bg-white/5 border border-white/10 text-emerald-400 block mb-0.5">
                                            {{ str_replace('_', ' ', $game->game_type) }}
                                        </span>
                                        @if($game->developer)
                                            <span class="text-[10px] font-mono-tech text-slate-500 uppercase">
                                                {{ $game->developer }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-display font-black text-xl text-white group-hover:text-emerald-400 transition-colors">
                                        {{ $game->name }}
                                    </h3>
                                    <div class="text-xs font-mono-tech text-slate-400 mt-1 flex items-center gap-2">
                                        <span>{{ $game->min_main_players ?? 5 }} Players</span>
                                        @if($game->max_substitutes)
                                            <span>•</span>
                                            <span>{{ $game->max_substitutes }} Subs</span>
                                        @endif
                                    </div>
                                </div>

                                @if($allocatedPrize > 0)
                                    <div class="p-3 rounded-xl bg-[#020406] border border-white/10 flex items-center justify-between">
                                        <span class="text-xs font-mono-tech text-slate-400 uppercase">Prize Pool</span>
                                        <span class="font-display font-black text-base text-emerald-400">
                                            Rs. {{ number_format($allocatedPrize) }}
                                        </span>
                                    </div>
                                @endif

                                @if(!empty($distributionItems))
                                    <div class="pt-2 border-t border-white/5 space-y-1.5 text-xs font-mono-tech">
                                        @foreach($distributionItems as $rank => $amount)
                                            <div class="flex items-center justify-between text-slate-300">
                                                <span class="text-slate-400">{{ $rank }}</span>
                                                <span class="font-bold text-white font-display">Rs. {{ is_numeric($amount) ? number_format((float)$amount) : $amount }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="mt-6 pt-4 border-t border-white/5">
                                <a href="{{ url('/mukhyadwar/register') }}" class="w-full py-2.5 rounded-xl text-xs font-black uppercase text-center block btn-secondary-action group-hover:btn-primary-action transition-all">
                                    Register Squad →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 3. TOURNAMENT SCHEDULE / EVENT DAYS (DYNAMIC ONLY) -->
        @if($eventDays->isNotEmpty())
            <section id="schedule" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
                <div class="mb-8 sm:mb-10">
                    <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                        EVENT TIMELINE
                    </span>
                    <h2 class="font-display text-2xl sm:text-4xl font-black uppercase text-white">
                        Match Schedule & Event Days
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($eventDays as $day)
                        <div class="editorial-card rounded-2xl p-5 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded text-[10px] font-mono-tech uppercase font-bold status-pill">
                                    {{ $day->day_name }}
                                </span>
                                @if($day->event_date)
                                    <span class="text-xs font-mono-tech text-slate-400">
                                        {{ $day->event_date->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                            @if($day->notes)
                                <p class="text-xs text-slate-300 leading-relaxed font-mono-tech">
                                    {{ $day->notes }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 4. CONFIRMED CONTENDERS (DYNAMIC ONLY) -->
        @if($approvedRegistrations->isNotEmpty())
            <section id="teams" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-10 gap-3">
                    <div>
                        <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                            CONFIRMED SQUADS
                        </span>
                        <h2 class="font-display text-2xl sm:text-4xl font-black uppercase text-white">
                            Tournament Contenders
                        </h2>
                    </div>
                    <a href="{{ url('/mukhyadwar/register') }}" class="text-xs font-mono-tech font-bold uppercase text-emerald-400 hover:text-emerald-300">
                        Join The Roster →
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($approvedRegistrations as $reg)
                        @php $team = $reg->team; @endphp
                        @if($team)
                            <div class="editorial-card rounded-2xl p-4 text-center flex flex-col items-center justify-between group tilt-card">
                                <div class="w-14 h-14 rounded-2xl bg-[#080c14] border border-white/10 flex items-center justify-center p-2 mb-2.5 shadow-md group-hover:border-emerald-500/40 transition-colors">
                                    @if($team->logo_path && file_exists(public_path('storage/' . $team->logo_path)))
                                        <img src="{{ asset('storage/' . $team->logo_path) }}" alt="{{ $team->name }}" class="w-full h-full object-contain">
                                    @else
                                        <span class="font-display font-black text-sm text-emerald-400">{{ strtoupper(substr($team->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                
                                <div class="w-full">
                                    <h4 class="font-display font-bold text-xs text-white truncate group-hover:text-emerald-400 transition-colors">{{ $team->name }}</h4>
                                    @if($team->tag)
                                        <span class="text-[10px] font-mono-tech text-slate-400 block">[{{ $team->tag }}]</span>
                                    @endif
                                </div>

                                <div class="mt-2.5 pt-2 border-t border-white/5 w-full">
                                    <span class="text-[9px] font-mono-tech uppercase text-emerald-400 truncate block font-bold">
                                        {{ $team->gameTitle?->name ?? 'Discipline' }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 5. OFFICIAL COMMUNITY SOCIALS HUB (INSTAGRAM, TIKTOK & FACEBOOK) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="border-b border-white/10 pb-4 mb-8 sm:mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
                <div>
                    <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-pink-400 block mb-1">
                        OFFICIAL COMMUNITY & SOCIAL FEEDS
                    </span>
                    <h2 class="font-display text-2xl sm:text-4xl font-black uppercase text-white">
                        Follow Outlaw Esports
                    </h2>
                </div>
                <span class="text-xs font-mono-tech text-slate-400">Live feeds from our official Instagram, TikTok & Facebook channels</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- 1. INSTAGRAM FEED -->
                <div class="editorial-card rounded-3xl p-6 sm:p-7 space-y-5 relative overflow-hidden group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-500 via-pink-500 to-purple-600 p-0.5 shadow-lg shrink-0">
                                    <div class="w-full h-full bg-[#080c14] rounded-2xl flex items-center justify-center text-lg">
                                        📸
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-display font-black text-sm text-white truncate">@outlaw_esportsnepal</h3>
                                    <span class="text-[10px] font-mono-tech text-pink-400 uppercase font-bold block">Instagram Feed</span>
                                </div>
                            </div>
                            <a href="https://www.instagram.com/outlaw_esportsnepal/" target="_blank" rel="noopener noreferrer" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-pink-500 to-purple-600 hover:opacity-90 text-white text-[11px] font-mono-tech font-bold uppercase tracking-wider transition-all shrink-0">
                                Follow ↗
                            </a>
                        </div>

                        <!-- LATEST INSTAGRAM POSTS PREVIEW TILES -->
                        <div class="grid grid-cols-2 gap-2.5 mt-4">
                            <a href="https://www.instagram.com/outlaw_esportsnepal/" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-pink-500/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">🏆</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">Trophy Reveal</span>
                                <span class="text-[9px] font-mono-tech text-slate-400 mt-0.5">Showdown 2026</span>
                            </a>
                            <a href="https://www.instagram.com/outlaw_esportsnepal/" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-pink-500/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">🔥</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">Match Schedules</span>
                                <span class="text-[9px] font-mono-tech text-slate-400 mt-0.5">Live Brackets</span>
                            </a>
                            <a href="https://www.instagram.com/outlaw_esportsnepal/" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-pink-500/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">👑</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">MVP Leaderboards</span>
                                <span class="text-[9px] font-mono-tech text-slate-400 mt-0.5">Top Fraggers</span>
                            </a>
                            <a href="https://www.instagram.com/outlaw_esportsnepal/" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-pink-500/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">🎬</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">Stage Highlights</span>
                                <span class="text-[9px] font-mono-tech text-slate-400 mt-0.5">Photo Gallery</span>
                            </a>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-white/5">
                        <a href="https://www.instagram.com/outlaw_esportsnepal/" target="_blank" rel="noopener noreferrer" class="text-xs font-mono-tech text-slate-300 hover:text-pink-400 transition-colors flex items-center justify-between">
                            <span>View All Instagram Posts</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                <!-- 2. TIKTOK FEED -->
                <div class="editorial-card rounded-3xl p-6 sm:p-7 space-y-5 relative overflow-hidden group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-cyan-400 to-pink-500 p-0.5 shadow-lg shrink-0">
                                    <div class="w-full h-full bg-[#080c14] rounded-2xl flex items-center justify-center text-lg">
                                        🎵
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-display font-black text-sm text-white truncate">@outlaw.esports6</h3>
                                    <span class="text-[10px] font-mono-tech text-cyan-400 uppercase font-bold block">TikTok Reels</span>
                                </div>
                            </div>
                            <a href="https://www.tiktok.com/@outlaw.esports6" target="_blank" rel="noopener noreferrer" class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-200 text-black text-[11px] font-mono-tech font-bold uppercase tracking-wider transition-all shrink-0">
                                Watch ↗
                            </a>
                        </div>

                        <!-- LATEST TIKTOK VIRAL REELS TILES -->
                        <div class="grid grid-cols-2 gap-2.5 mt-4">
                            <a href="https://www.tiktok.com/@outlaw.esports6" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-cyan-400/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">⚡</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">1v4 Clutch Ace</span>
                                <span class="text-[9px] font-mono-tech text-cyan-400 mt-0.5">Viral Clip</span>
                            </a>
                            <a href="https://www.tiktok.com/@outlaw.esports6" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-cyan-400/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">🎮</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">LAN Walkouts</span>
                                <span class="text-[9px] font-mono-tech text-cyan-400 mt-0.5">Player Intros</span>
                            </a>
                            <a href="https://www.tiktok.com/@outlaw.esports6" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-cyan-400/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">🗣️</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">Mic'd Up Teams</span>
                                <span class="text-[9px] font-mono-tech text-cyan-400 mt-0.5">Voice Comms</span>
                            </a>
                            <a href="https://www.tiktok.com/@outlaw.esports6" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-cyan-400/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">💥</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">Crowd Reactions</span>
                                <span class="text-[9px] font-mono-tech text-cyan-400 mt-0.5">Stadium Energy</span>
                            </a>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-white/5">
                        <a href="https://www.tiktok.com/@outlaw.esports6" target="_blank" rel="noopener noreferrer" class="text-xs font-mono-tech text-slate-300 hover:text-cyan-400 transition-colors flex items-center justify-between">
                            <span>Watch All TikTok Videos</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                <!-- 3. FACEBOOK COMMUNITY FEED -->
                <div class="editorial-card rounded-3xl p-6 sm:p-7 space-y-5 relative overflow-hidden group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 p-0.5 shadow-lg shrink-0">
                                    <div class="w-full h-full bg-[#080c14] rounded-2xl flex items-center justify-center text-lg text-blue-500 font-black">
                                        f
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-display font-black text-sm text-white truncate">Outlaw Esports</h3>
                                    <span class="text-[10px] font-mono-tech text-blue-400 uppercase font-bold block">Facebook Page</span>
                                </div>
                            </div>
                            <a href="https://www.facebook.com/OutlawESports" target="_blank" rel="noopener noreferrer" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-[11px] font-mono-tech font-bold uppercase tracking-wider transition-all shrink-0">
                                Join ↗
                            </a>
                        </div>

                        <!-- LATEST FACEBOOK COMMUNITY TILES -->
                        <div class="grid grid-cols-2 gap-2.5 mt-4">
                            <a href="https://www.facebook.com/OutlawESports" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-blue-500/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">📢</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">Official Updates</span>
                                <span class="text-[9px] font-mono-tech text-blue-400 mt-0.5">Press Releases</span>
                            </a>
                            <a href="https://www.facebook.com/OutlawESports" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-blue-500/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">🎫</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">Ticket Passes</span>
                                <span class="text-[9px] font-mono-tech text-blue-400 mt-0.5">Early Bird Entry</span>
                            </a>
                            <a href="https://www.facebook.com/OutlawESports" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-blue-500/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">🤝</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">Partner News</span>
                                <span class="text-[9px] font-mono-tech text-blue-400 mt-0.5">Brand Alliances</span>
                            </a>
                            <a href="https://www.facebook.com/OutlawESports" target="_blank" rel="noopener noreferrer" class="rounded-xl overflow-hidden bg-[#040608] border border-white/10 p-3 text-center group/item hover:border-blue-500/50 transition-all flex flex-col items-center justify-center min-h-[110px]">
                                <span class="text-2xl mb-1.5">💬</span>
                                <span class="text-[10px] font-mono-tech text-white font-bold uppercase block">Fan Discussions</span>
                                <span class="text-[9px] font-mono-tech text-blue-400 mt-0.5">Community Hub</span>
                            </a>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-white/5">
                        <a href="https://www.facebook.com/OutlawESports" target="_blank" rel="noopener noreferrer" class="text-xs font-mono-tech text-slate-300 hover:text-blue-400 transition-colors flex items-center justify-between">
                            <span>Open Facebook Page</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. DYNAMIC LAN ARENA & VENUE EXPERIENCE + GOOGLE MAPS (ONLY SHOWN IF CONFIGURED AS LAN EVENT) -->
        @if($activeTournament && $activeTournament->is_lan && ($activeTournament->venue_name || $activeTournament->venue_map_url))
            @php
                $venueQuery = urlencode($activeTournament->venue_address ?: ($activeTournament->venue_name ?: 'Bhrikutimandap, Kathmandu'));
                $venueDirectionLink = $activeTournament->venue_map_url ?: 'https://maps.google.com/?q=' . $venueQuery;
            @endphp
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="border-b border-white/10 pb-4 mb-8 sm:mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
                    <div>
                        <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                            OFFICIAL LAN CHAMPIONSHIP VENUE
                        </span>
                        <h2 class="font-display text-2xl sm:text-4xl font-black uppercase text-white">
                            {{ $activeTournament->venue_name ?: 'LAN Grand Finals Arena' }}
                        </h2>
                    </div>
                    <a href="{{ $venueDirectionLink }}" target="_blank" rel="noopener noreferrer" class="text-xs font-mono-tech font-bold uppercase text-emerald-400 hover:text-emerald-300 transition-colors flex items-center gap-1.5">
                        <span>Get Directions on Google Maps</span>
                        <span>↗</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                    <!-- VENUE SPECS & HIGHLIGHTS -->
                    <div class="lg:col-span-5 space-y-6 flex flex-col justify-between">
                        <div class="editorial-card rounded-3xl p-6 sm:p-8 space-y-6">
                            <div>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-mono-tech uppercase font-bold bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 inline-block mb-2">
                                    Offline Finals Stadium • Kathmandu
                                </span>
                                <h3 class="font-display text-xl sm:text-2xl font-black text-white">
                                    {{ $activeTournament->venue_name }}
                                </h3>
                                @if($activeTournament->venue_address)
                                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed mt-2 font-mono-tech">
                                        📍 {{ $activeTournament->venue_address }}
                                    </p>
                                @endif
                                @if($activeTournament->venue_notes)
                                    <p class="text-emerald-400/90 text-xs font-mono-tech mt-2.5 p-2.5 rounded-xl bg-emerald-500/5 border border-emerald-500/20">
                                        ℹ️ {{ $activeTournament->venue_notes }}
                                    </p>
                                @endif
                            </div>

                            <div class="space-y-3.5 text-xs font-mono-tech">
                                <div class="flex items-start gap-3">
                                    <span class="text-emerald-400 font-bold text-base">🖥️</span>
                                    <div>
                                        <span class="text-white font-bold block">240Hz Pro Tournament Stations</span>
                                        <span class="text-slate-400 text-[11px]">Standardized esports tournament PC rigs & mobile gaming setups.</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="text-cyan-400 font-bold text-base">🎧</span>
                                    <div>
                                        <span class="text-white font-bold block">Sound-Isolated Stage Pods</span>
                                        <span class="text-slate-400 text-[11px]">Studio-grade voice communication and noise cancellation.</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="text-purple-400 font-bold text-base">📺</span>
                                    <div>
                                        <span class="text-white font-bold block">Ultra HD Spectator LED Stage</span>
                                        <span class="text-slate-400 text-[11px]">High-fidelity live multi-angle tournament stadium display.</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="text-amber-400 font-bold text-base">⚡</span>
                                    <div>
                                        <span class="text-white font-bold block">Low-Latency Dedicated Fiber</span>
                                        <span class="text-slate-400 text-[11px]">Redundant gigabit connectivity for zero-ping competitive play.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                                <a href="{{ $venueDirectionLink }}" target="_blank" rel="noopener noreferrer" class="w-full py-3 rounded-xl btn-primary-action text-xs font-black text-center block uppercase tracking-wider">
                                    Open in Google Maps ↗
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- GOOGLE MAPS INTERACTIVE EMBED -->
                    <div class="lg:col-span-7 editorial-card rounded-3xl p-2.5 overflow-hidden min-h-[380px] flex">
                        <iframe 
                            src="https://maps.google.com/maps?q={{ $venueQuery }}&hl=en&z=16&output=embed" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade" 
                            class="w-full h-full min-h-[360px] sm:min-h-[440px] rounded-2xl grayscale contrast-125 opacity-85 hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                        </iframe>
                    </div>
                </div>
            </section>
        @endif

        <!-- 8. FREQUENTLY ASKED QUESTIONS -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="border-b border-white/10 pb-4 mb-8 sm:mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
                <div>
                    <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                        QUESTIONS & OPERATIONAL INTEL
                    </span>
                    <h2 class="font-display text-2xl sm:text-4xl font-black uppercase text-white">
                        Frequently Asked Questions
                    </h2>
                </div>
                <a href="{{ url('/guide') }}" class="text-xs font-mono-tech font-bold uppercase text-emerald-400 hover:text-emerald-300 transition-colors">
                    Read Manager Guide →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <details class="editorial-card rounded-2xl p-5 text-xs font-mono-tech group cursor-pointer">
                    <summary class="font-display font-bold text-sm text-white flex items-center justify-between list-none">
                        <span>How do team managers register their squads?</span>
                        <span class="text-emerald-400 text-lg transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-slate-300 leading-relaxed pt-2 border-t border-white/5">
                        Team Managers create an account on the Player Portal (<a href="{{ url('/mukhyadwar/register') }}" class="text-emerald-400 font-bold underline">/mukhyadwar/register</a>), create their squad profile under "My Teams", add player IGNs and photos, and submit entry with payment receipt screenshot.
                    </p>
                </details>

                <details class="editorial-card rounded-2xl p-5 text-xs font-mono-tech group cursor-pointer">
                    <summary class="font-display font-bold text-sm text-white flex items-center justify-between list-none">
                        <span>What device rules apply for PUBG Mobile & Valorant?</span>
                        <span class="text-emerald-400 text-lg transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-slate-300 leading-relaxed pt-2 border-t border-white/5">
                        PUBG Mobile is strictly handheld phones only (iOS & Android). iPads, tablets, and emulators are strictly prohibited. Valorant requires standard PC with active Riot Vanguard anti-cheat.
                    </p>
                </details>

                <details class="editorial-card rounded-2xl p-5 text-xs font-mono-tech group cursor-pointer">
                    <summary class="font-display font-bold text-sm text-white flex items-center justify-between list-none">
                        <span>How is the tournament prize pool paid out?</span>
                        <span class="text-emerald-400 text-lg transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-slate-300 leading-relaxed pt-2 border-t border-white/5">
                        Prize pools are transferred directly to verified Team Managers via official bank transfer or digital wallet (eSewa/Khalti) within 7 business days following tournament grand finals verification.
                    </p>
                </details>

                <details class="editorial-card rounded-2xl p-5 text-xs font-mono-tech group cursor-pointer">
                    <summary class="font-display font-bold text-sm text-white flex items-center justify-between list-none">
                        <span>Can spectators attend the LAN Finals in person?</span>
                        <span class="text-emerald-400 text-lg transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-slate-300 leading-relaxed pt-2 border-t border-white/5">
                        Yes! In-person spectator attendance is hosted at Outlaw Gaming & Tech LAN arena in Kathmandu. Limited VIP and general spectator passes will be announced on our social channels.
                    </p>
                </details>

                <details class="editorial-card rounded-2xl p-5 text-xs font-mono-tech group cursor-pointer">
                    <summary class="font-display font-bold text-sm text-white flex items-center justify-between list-none">
                        <span>What happens if a player disconnects during match play?</span>
                        <span class="text-emerald-400 text-lg transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-slate-300 leading-relaxed pt-2 border-t border-white/5">
                        Official tournament referee pause protocols apply as specified in the tournament rulebook. Teams are permitted up to 2 substitutes who can be rotated in between matches.
                    </p>
                </details>

                <details class="editorial-card rounded-2xl p-5 text-xs font-mono-tech group cursor-pointer">
                    <summary class="font-display font-bold text-sm text-white flex items-center justify-between list-none">
                        <span>Where do teams receive match room IDs & passwords?</span>
                        <span class="text-emerald-400 text-lg transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-slate-300 leading-relaxed pt-2 border-t border-white/5">
                        Custom match room credentials and schedule calls are distributed exclusively in the verified Team Captains channel on the Outlaw Esports Nepal Discord server 15-30 minutes before match time.
                    </p>
                </details>
            </div>
        </section>

        <!-- KINETIC SCROLLING MARQUEE TICKER 2 -->
        <div class="w-full bg-[#03070d] border-y border-white/10 py-3.5 overflow-hidden">
            <div class="marquee-container marquee-reverse">
                <div class="marquee-content text-xs sm:text-sm font-mono-tech font-bold uppercase tracking-widest text-slate-300">
                    <span class="flex items-center gap-2.5"><span class="text-emerald-400">🏆</span> CHAMPIONS RISE HERE</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-cyan-400">🛡️</span> ZERO TOLERANCE ANTI-CHEAT</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-purple-400">🔥</span> 100% VERIFIED NATIONAL ROSTERS</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-amber-400">👑</span> BE LEGENDARY • OUTLAW ESPORTS NEPAL</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-emerald-400">⚡</span> PRO ESPORTS CULTURE & COMMUNITY</span>
                    <span class="text-slate-600">//</span>
                </div>
                <div class="marquee-content text-xs sm:text-sm font-mono-tech font-bold uppercase tracking-widest text-slate-300" aria-hidden="true">
                    <span class="flex items-center gap-2.5"><span class="text-emerald-400">🏆</span> CHAMPIONS RISE HERE</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-cyan-400">🛡️</span> ZERO TOLERANCE ANTI-CHEAT</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-purple-400">🔥</span> 100% VERIFIED NATIONAL ROSTERS</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-amber-400">👑</span> BE LEGENDARY • OUTLAW ESPORTS NEPAL</span>
                    <span class="text-slate-600">//</span>
                    <span class="flex items-center gap-2.5"><span class="text-emerald-400">⚡</span> PRO ESPORTS CULTURE & COMMUNITY</span>
                    <span class="text-slate-600">//</span>
                </div>
            </div>
        </div>

        <!-- 9. SPONSORS & PARTNERS (UNBOXED HIGH-VISIBILITY SHOWCASE) -->
        @if($sponsors->isNotEmpty() || $partners->isNotEmpty())
            @php
                $resolveLogo = function (?string $url) {
                    if (!$url) return null;
                    if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                        return $url;
                    }
                    if (file_exists(public_path('storage/' . $url))) {
                        return asset('storage/' . $url);
                    }
                    if (file_exists(public_path($url))) {
                        return asset($url);
                    }
                    return null;
                };

                $groupedSponsors = $sponsors->toBase();
                $titleSponsors = $groupedSponsors->get('title', collect());
                $platinumSponsors = $groupedSponsors->get('platinum', collect());
                $goldSponsors = $groupedSponsors->get('gold', collect());
                $silverSponsors = $groupedSponsors->get('silver', collect());
                $otherSponsors = $groupedSponsors->except(['title', 'platinum', 'gold', 'silver'])->flatten();
            @endphp

            <section id="sponsors" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
                
                <!-- SECTION HEADER -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 sm:mb-16 gap-4 border-b border-white/10 pb-6">
                    <div>
                        <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                            OFFICIAL ALLIANCES & ECOSYSTEM
                        </span>
                        <h2 class="font-display text-2xl sm:text-4xl lg:text-5xl font-black uppercase text-white tracking-tight">
                            Partners & Sponsors
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-xl">
                            Empowering Nepal's premier national esports championship in collaboration with industry-leading brands and media networks.
                        </p>
                    </div>
                    <button onclick="document.getElementById('sponsor-modal').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-emerald-500/10 border border-white/10 hover:border-emerald-500/30 text-xs font-mono-tech font-bold uppercase text-slate-300 hover:text-emerald-400 transition-all cursor-pointer flex items-center gap-2 self-start sm:self-auto shrink-0 shadow-sm">
                        <span>🤝</span>
                        <span>Partner Inquiry →</span>
                    </button>
                </div>

                <div class="space-y-16 sm:space-y-20">

                    <!-- 1. TITLE / HEADLINE SPONSORS (HEROIC SHOWCASE) -->
                    @if($titleSponsors->isNotEmpty())
                        <div class="space-y-8 text-center">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-[11px] font-mono-tech font-bold uppercase tracking-widest">
                                <span>⚡</span> TITLE SPONSOR
                            </div>

                            <div class="flex flex-wrap items-center justify-center gap-10 sm:gap-16">
                                @foreach($titleSponsors as $sponsor)
                                    @php $logoSrc = $resolveLogo($sponsor->logo_url); @endphp
                                    <a href="{{ $sponsor->website_url ?: '#' }}" target="{{ $sponsor->website_url ? '_blank' : '_self' }}" class="group flex flex-col items-center justify-center transition-all duration-300 hover:-translate-y-1.5 focus:outline-none">
                                        <div class="relative p-2 flex items-center justify-center">
                                            <div class="absolute inset-0 bg-amber-500/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                                            @if($logoSrc)
                                                <img src="{{ $logoSrc }}" alt="{{ $sponsor->name }}" class="h-20 sm:h-28 md:h-32 w-auto max-w-[280px] sm:max-w-[360px] object-contain drop-shadow-[0_8px_30px_rgba(0,0,0,0.8)] transition-transform duration-300 group-hover:scale-105">
                                            @else
                                                <div class="font-display font-black text-2xl sm:text-4xl md:text-5xl text-white uppercase tracking-wider group-hover:text-amber-400 transition-colors drop-shadow-[0_4px_20px_rgba(0,0,0,0.8)]">
                                                    {{ $sponsor->name }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-4 flex items-center gap-2 text-xs font-mono-tech font-bold uppercase text-slate-300 group-hover:text-amber-400 transition-colors">
                                            <span>{{ $sponsor->name }}</span>
                                            <span class="text-amber-400 text-sm">↗</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 2. PLATINUM & GOLD SPONSORS -->
                    @if($platinumSponsors->isNotEmpty() || $goldSponsors->isNotEmpty())
                        <div class="space-y-8">
                            <div class="flex items-center gap-4">
                                <span class="h-px bg-white/10 flex-grow"></span>
                                <span class="text-[11px] font-mono-tech font-bold uppercase tracking-widest text-slate-400">
                                    PRINCIPAL SPONSORS
                                </span>
                                <span class="h-px bg-white/10 flex-grow"></span>
                            </div>

                            <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-14 md:gap-20">
                                @foreach($platinumSponsors->concat($goldSponsors) as $sponsor)
                                    @php $logoSrc = $resolveLogo($sponsor->logo_url); @endphp
                                    <a href="{{ $sponsor->website_url ?: '#' }}" target="{{ $sponsor->website_url ? '_blank' : '_self' }}" class="group flex flex-col items-center justify-center transition-all duration-300 hover:-translate-y-1 focus:outline-none">
                                        <div class="relative p-2 flex items-center justify-center">
                                            <div class="absolute inset-0 bg-emerald-500/10 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                                            @if($logoSrc)
                                                <img src="{{ $logoSrc }}" alt="{{ $sponsor->name }}" class="h-14 sm:h-18 md:h-22 w-auto max-w-[220px] sm:max-w-[280px] object-contain drop-shadow-[0_6px_20px_rgba(0,0,0,0.7)] transition-transform duration-300 group-hover:scale-105">
                                            @else
                                                <div class="font-display font-black text-xl sm:text-2xl md:text-3xl text-white uppercase tracking-wider group-hover:text-emerald-400 transition-colors drop-shadow-[0_4px_16px_rgba(0,0,0,0.8)]">
                                                    {{ $sponsor->name }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-3 text-center">
                                            <div class="font-display font-bold text-xs sm:text-sm text-slate-200 group-hover:text-emerald-400 transition-colors">
                                                {{ $sponsor->name }}
                                            </div>
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-mono-tech uppercase font-bold bg-white/5 border border-white/10 text-emerald-400">
                                                {{ strtoupper($sponsor->level) }} TIER
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 3. SILVER & GENERAL SPONSORS -->
                    @if($silverSponsors->isNotEmpty() || $otherSponsors->isNotEmpty())
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <span class="h-px bg-white/10 flex-grow"></span>
                                <span class="text-[10px] font-mono-tech font-bold uppercase tracking-widest text-slate-500">
                                    SUPPORTING SPONSORS
                                </span>
                                <span class="h-px bg-white/10 flex-grow"></span>
                            </div>

                            <div class="flex flex-wrap items-center justify-center gap-6 sm:gap-10 md:gap-14">
                                @foreach($silverSponsors->concat($otherSponsors) as $sponsor)
                                    @php $logoSrc = $resolveLogo($sponsor->logo_url); @endphp
                                    <a href="{{ $sponsor->website_url ?: '#' }}" target="{{ $sponsor->website_url ? '_blank' : '_self' }}" class="group flex flex-col items-center justify-center transition-all duration-300 hover:-translate-y-0.5 focus:outline-none">
                                        <div class="p-2 flex items-center justify-center">
                                            @if($logoSrc)
                                                <img src="{{ $logoSrc }}" alt="{{ $sponsor->name }}" class="h-10 sm:h-14 md:h-16 w-auto max-w-[180px] object-contain drop-shadow-[0_4px_16px_rgba(0,0,0,0.6)] transition-transform duration-300 group-hover:scale-105">
                                            @else
                                                <div class="font-display font-bold text-base sm:text-xl text-slate-300 uppercase tracking-wide group-hover:text-emerald-400 transition-colors">
                                                    {{ $sponsor->name }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-2 text-center">
                                            <span class="text-[10px] font-mono-tech uppercase font-bold text-slate-400 group-hover:text-slate-200 transition-colors block">
                                                {{ $sponsor->name }}
                                            </span>
                                            <span class="text-[9px] font-mono-tech text-slate-500 uppercase">
                                                {{ strtoupper($sponsor->level ?: 'Sponsor') }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 4. OFFICIAL ALLIANCES & STRATEGIC PARTNERS -->
                    @if($partners->isNotEmpty())
                        <div class="space-y-8 pt-4 text-center">
                            <div class="flex items-center gap-4">
                                <span class="h-px bg-white/10 flex-grow"></span>
                                <span class="text-[11px] font-mono-tech font-bold uppercase tracking-widest text-cyan-400">
                                    STRATEGIC & BROADCAST ALLIANCES
                                </span>
                                <span class="h-px bg-white/10 flex-grow"></span>
                            </div>

                            <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-12 md:gap-14">
                                @foreach($partners->flatten() as $partner)
                                    @php $logoSrc = $resolveLogo($partner->logo_url); @endphp
                                    <a href="{{ $partner->website_url ?: '#' }}" target="{{ $partner->website_url ? '_blank' : '_self' }}" class="w-40 sm:w-48 md:w-52 group flex flex-col items-center justify-center text-center p-3 transition-all duration-300 hover:-translate-y-1 focus:outline-none">
                                        <div class="relative p-2 flex items-center justify-center min-h-[60px] sm:min-h-[76px]">
                                            <div class="absolute inset-0 bg-cyan-500/10 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                                            @if($logoSrc)
                                                <img src="{{ $logoSrc }}" alt="{{ $partner->name }}" class="h-12 sm:h-16 md:h-18 w-auto max-w-[180px] object-contain drop-shadow-[0_4px_16px_rgba(0,0,0,0.7)] transition-transform duration-300 group-hover:scale-105 mx-auto">
                                            @else
                                                <div class="font-display font-black text-sm sm:text-base md:text-lg text-white uppercase tracking-wide group-hover:text-cyan-400 transition-colors drop-shadow-[0_2px_12px_rgba(0,0,0,0.8)] text-center">
                                                    {{ $partner->name }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-3 w-full text-center">
                                            <div class="font-display font-bold text-xs text-slate-200 group-hover:text-cyan-400 transition-colors truncate mx-auto">
                                                {{ $partner->name }}
                                            </div>
                                            @if($partner->title)
                                                <span class="inline-block mt-1 px-2.5 py-0.5 rounded text-[9px] font-mono-tech uppercase font-bold bg-cyan-500/10 border border-cyan-500/25 text-cyan-400 group-hover:bg-cyan-500/20 transition-colors">
                                                    {{ $partner->title }}
                                                </span>
                                            @else
                                                <span class="text-[9px] font-mono-tech uppercase text-slate-500 mt-1 block">
                                                    Official Partner
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

            </section>
        @endif

        <!-- 6. OTHER TOURNAMENT CIRCUITS (DYNAMIC ONLY) -->
        @if($otherTournaments->isNotEmpty())
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                        CIRCUIT ARCHIVE
                    </span>
                    <h2 class="font-display text-xl sm:text-3xl font-black uppercase text-white">
                        Other Tournaments
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($otherTournaments as $tournament)
                        <div class="editorial-card rounded-2xl p-5 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono-tech uppercase font-bold bg-white/5 border border-white/10 text-slate-400">
                                    {{ str_replace('_', ' ', $tournament->status) }}
                                </span>
                                <span class="text-[10px] font-mono-tech text-slate-500">
                                    {{ $tournament->season_version }}
                                </span>
                            </div>
                            <h4 class="font-display font-bold text-sm text-white truncate">{{ $tournament->name }}</h4>
                            <div class="text-[11px] font-mono-tech text-slate-400 flex items-center justify-between pt-2 border-t border-white/5">
                                <span>{{ $tournament->gameTitles->count() }} Disciplines</span>
                                @if($tournament->prize_pool_total)
                                    <span class="text-emerald-400 font-bold">Rs. {{ number_format($tournament->prize_pool_total) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 7. CLEAN SQUAD REGISTRATION FOOTER BANNER -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="editorial-card-featured rounded-3xl p-8 sm:p-12 text-center relative overflow-hidden border border-emerald-500/30">
                <div class="max-w-2xl mx-auto space-y-5">
                    <h3 class="font-display text-2xl sm:text-4xl font-black uppercase text-white">
                        Ready To Compete?
                    </h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Register your team roster on the player portal to secure your spot in {{ $tournamentName }}.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                        <a href="{{ url('/mukhyadwar/register') }}" class="px-8 py-3.5 rounded-xl btn-primary-action text-xs font-black">
                            Register Your Squad →
                        </a>
                        <button onclick="document.getElementById('sponsor-modal').classList.remove('hidden')" class="px-6 py-3.5 rounded-xl btn-secondary-action text-xs font-bold cursor-pointer">
                            Partner With Us
                        </button>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- LIVEWIRE SPONSOR INQUIRY MODAL -->
    <div id="sponsor-modal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-black/85 backdrop-blur-xl hidden [&:not(.hidden)]:flex">
        <div class="relative w-full max-w-lg rounded-2xl bg-[#0a0e14] border border-white/15 p-6 sm:p-8 shadow-2xl max-h-[90vh] overflow-y-auto">
            <button onclick="document.getElementById('sponsor-modal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-white text-xl font-bold cursor-pointer transition-colors">✕</button>
            <div class="text-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-2xl mx-auto mb-3 text-emerald-400">
                    🤝
                </div>
                <h3 class="font-display text-xl font-black uppercase text-white">Partner Inquiry</h3>
                <p class="text-xs text-slate-400 mt-1 font-mono-tech">Collaborate with Outlaw Esports Nepal.</p>
            </div>
            <livewire:sponsor-query-form />
        </div>
    </div>

    <!-- CLEAN FOOTER -->
    <footer class="border-t border-white/5 bg-[#020406] py-10 text-xs font-mono-tech text-slate-400 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pb-6 border-b border-white/5">
                <div class="flex items-center gap-3 text-center sm:text-left">
                    <span class="font-display font-black text-white text-sm uppercase tracking-wider">{{ $tournamentName }}</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-slate-400 text-xs">Outlaw Esports Nepal</span>
                </div>

                <!-- SOCIAL CHANNELS -->
                <div class="flex flex-wrap items-center justify-center gap-4 text-xs">
                    <a href="https://www.facebook.com/OutlawESports" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-blue-400 transition-colors flex items-center gap-1.5">
                        <span>Facebook</span>
                        <span>↗</span>
                    </a>
                    <a href="https://www.instagram.com/outlaw_esportsnepal/" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-pink-400 transition-colors flex items-center gap-1.5">
                        <span>Instagram</span>
                        <span>↗</span>
                    </a>
                    <a href="https://www.tiktok.com/@outlaw.esports6" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-cyan-400 transition-colors flex items-center gap-1.5">
                        <span>TikTok</span>
                        <span>↗</span>
                    </a>
                    <a href="https://www.youtube.com/@Outlawesportsnepal/featured?sub_confirmation=1" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-red-400 transition-colors flex items-center gap-1.5">
                        <span>YouTube</span>
                        <span>↗</span>
                    </a>
                    @if($activeTournament && $activeTournament->is_lan && ($activeTournament->venue_map_url || $activeTournament->venue_address || $activeTournament->venue_name))
                        <a href="{{ $activeTournament->venue_map_url ?: ('https://maps.google.com/?q=' . urlencode($activeTournament->venue_address ?: $activeTournament->venue_name)) }}" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-emerald-400 transition-colors flex items-center gap-1.5">
                            <span>LAN Venue Map</span>
                            <span>↗</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
                <span>© {{ date('Y') }} Outlaw Esports Nepal. All rights reserved.</span>
                <div class="flex flex-wrap items-center justify-center gap-5 text-slate-400">
                    <a href="{{ url('/guide') }}" class="hover:text-emerald-400 transition-colors">Manager Guide</a>
                    <a href="{{ url('/privacy-policy') }}" class="hover:text-emerald-400 transition-colors">Privacy Policy</a>
                    <a href="{{ url('/terms-of-service') }}" class="hover:text-emerald-400 transition-colors">Terms of Service</a>
                    <a href="{{ url('/mukhyadwar/login') }}" class="hover:text-emerald-400 transition-colors">Player Portal</a>
                    <a href="{{ url('/maidan/login') }}" class="hover:text-emerald-400 transition-colors">Admin Login</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    <script>
        function toggleMobileNav() {
            const menu = document.getElementById('mobile-menu');
            const bars = document.getElementById('menu-bars');
            const close = document.getElementById('menu-close');

            if (!menu) return;

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                bars?.classList.add('hidden');
                close?.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
                bars?.classList.remove('hidden');
                close?.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
