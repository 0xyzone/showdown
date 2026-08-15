<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>{{ $activeTournament?->name ? $activeTournament->name . ' — Official Esports Championship' : 'Esports Arena — Championship Series' }}</title>
    <meta name="description" content="{{ $activeTournament?->hero_subheadline ?? 'Premier competitive esports championship circuit.' }}">

    <!-- Google Fonts: Space Grotesk (Editorial/Tech) & Orbitron (Display) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @php
        // Database-driven Dynamic Theme System
        $themeColor = $activeTournament?->theme_color ?? '#10b981';

        // Parse Hex to RGB
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

        // Calculate Relative Luminance according to WCAG Standards
        $luminance = ($r * 0.2126 + $g * 0.7152 + $b * 0.0722) / 255;
        $btnTextColor = $luminance > 0.55 ? '#05070a' : '#ffffff';
        $badgeTextColor = $luminance > 0.55 ? "rgb(" . max(0, $r - 40) . "," . max(0, $g - 40) . "," . max(0, $b - 40) . ")" : $themeColor;

        $tournamentName = $activeTournament?->name ?? 'SHOWDOWN CHAMPIONSHIP';
        $seasonVersion = $activeTournament?->season_version ?? 'SEASON 01';
        $entryFee = $activeTournament?->entry_fee ?? 0;
        $entryFeeSuffix = $activeTournament?->entry_fee_suffix ?: 'person';

        // Calculate overall prize pool accurately
        $calculatedPrizePool = 0;
        if ($activeTournament && $activeTournament->gameTitles->count() > 0) {
            foreach ($activeTournament->gameTitles as $g) {
                $calculatedPrizePool += (float) ($g->pivot->prize_pool ?? 0);
            }
        }
        $prizePool = $calculatedPrizePool > 0 ? $calculatedPrizePool : ($activeTournament?->prize_pool_total ?? 0);

        $heroHeadline = $activeTournament?->hero_headline ?: ($activeTournament?->name ?? 'COMPETE. DOMINATE. PREVAIL.');
        $heroSubheadline = $activeTournament?->hero_subheadline ?: "National competitive tournament arena. Verified squads, multi-title disciplines, and grand final showdowns.";
        $registrationEnd = $activeTournament?->registration_end ? $activeTournament->registration_end->toIso8601String() : null;
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
<body class="editorial-bg min-h-screen antialiased flex flex-col justify-between selection:bg-slate-800">

    <!-- 1. EDITORIAL TOP TICKER / STATUS BAR -->
    <div class="border-b border-white/5 bg-[#030508] text-[11px] font-mono-tech tracking-wider uppercase py-2 px-4 sm:px-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-2 overflow-hidden">
            <div class="flex items-center gap-2 sm:gap-3 truncate min-w-0">
                <span class="inline-block w-2 h-2 rounded-full shrink-0 animate-pulse" style="background-color: var(--primary);"></span>
                <span class="text-slate-400 font-semibold truncate">{{ $tournamentName }}</span>
                <span class="hidden sm:inline text-slate-600">/</span>
                <span class="hidden sm:inline text-slate-400">{{ $seasonVersion }}</span>
            </div>
            
            <div class="flex items-center gap-4 text-slate-400 shrink-0">
                @if($activeTournament)
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-500 hidden xs:inline">STATUS:</span>
                        <span class="font-bold text-white uppercase text-[10px] sm:text-[11px]">{{ str_replace('_', ' ', $activeTournament->status) }}</span>
                    </div>
                @endif
                <div class="hidden md:flex items-center gap-2">
                    <span class="text-slate-500">TOTAL PRIZE:</span>
                    <span class="font-bold text-white">NPR Rs. {{ number_format($prizePool) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. STREAMLINED EDITORIAL NAVBAR -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-[#05070a]/90 border-b border-white/5 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-16 sm:h-20 flex items-center justify-between gap-3">
            
            <!-- BRAND IDENTITY -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 sm:gap-3.5 group shrink-0 min-w-0">
                @if($activeTournament?->logo_path && file_exists(public_path('storage/' . $activeTournament->logo_path)))
                    <img src="{{ asset('storage/' . $activeTournament->logo_path) }}" alt="{{ $tournamentName }}" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg object-contain bg-[#0e131d] border border-white/10 p-1 group-hover:border-white/20 transition-all shrink-0">
                @else
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg btn-primary-action flex items-center justify-center font-display font-black text-xs sm:text-sm shrink-0">
                        SD
                    </div>
                @endif

                <div class="min-w-0">
                    <span class="font-display font-black text-xs sm:text-base lg:text-lg tracking-wider text-white block uppercase truncate max-w-32.5 sm:max-w-55 md:max-w-none">
                        {{ $tournamentName }}
                    </span>
                    <span class="text-[9px] sm:text-[10px] font-mono-tech tracking-widest text-slate-400 hidden xs:block uppercase truncate">
                        Championship Series
                    </span>
                </div>
            </a>

            <!-- MINIMAL 4-ITEM DESTINATION NAVIGATION -->
            <nav class="hidden lg:flex items-center gap-8 text-xs font-bold tracking-wider uppercase text-slate-300">
                <a href="#featured" class="hover:text-white transition-colors">Tournament</a>
                <a href="#disciplines" class="hover:text-white transition-colors">Disciplines</a>
                <a href="#contenders" class="hover:text-white transition-colors">Contenders</a>
                <a href="#circuit" class="hover:text-white transition-colors">Circuit</a>
            </nav>

            <!-- AUTH & ACTION HUB -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <button onclick="document.getElementById('sponsor-modal').classList.remove('hidden')" class="hidden sm:inline-flex items-center px-3.5 py-2 rounded-lg text-xs font-semibold tracking-wider uppercase btn-secondary-action">
                    Partner Inquiry
                </button>

                @auth('participant')
                    <a href="{{ url('/mukhyadwar') }}" class="px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-lg text-xs font-extrabold btn-primary-action flex items-center gap-1.5">
                        <span>Portal</span>
                        <span>→</span>
                    </a>
                @else
                    <a href="{{ url('/mukhyadwar/login') }}" class="px-2.5 sm:px-4 py-2 rounded-lg text-xs font-bold text-slate-300 hover:text-white transition-colors">
                        Sign In
                    </a>
                    <a href="{{ url('/mukhyadwar/register') }}" class="px-3 sm:px-5 py-2 rounded-lg text-xs font-extrabold btn-primary-action">
                        Register
                    </a>
                @endauth

                <!-- MOBILE HAMBURGER -->
                <button id="mobile-nav-toggle" onclick="toggleMobileNav()" aria-label="Toggle navigation menu" class="lg:hidden p-2 rounded-lg bg-[#0e131d] border border-white/10 text-slate-300 hover:text-white focus:outline-none cursor-pointer">
                    <svg id="menu-bars" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="menu-close" class="w-4 h-4 sm:w-5 sm:h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        </div>

        <!-- MOBILE MENU OVERLAY -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-white/10 bg-[#05070a]/98 px-6 py-6 space-y-4">
            <div class="flex flex-col space-y-3 text-xs font-bold tracking-wider uppercase text-slate-300">
                <a href="#featured" onclick="toggleMobileNav()" class="py-2 hover:text-white">Tournament</a>
                <a href="#disciplines" onclick="toggleMobileNav()" class="py-2 hover:text-white">Disciplines</a>
                <a href="#contenders" onclick="toggleMobileNav()" class="py-2 hover:text-white">Contenders</a>
                <a href="#circuit" onclick="toggleMobileNav()" class="py-2 hover:text-white">Circuit</a>
            </div>

            <div class="pt-4 border-t border-white/10 flex flex-col gap-2">
                <button onclick="toggleMobileNav(); document.getElementById('sponsor-modal').classList.remove('hidden')" class="w-full py-3 rounded-lg text-xs font-bold tracking-wider uppercase text-center btn-secondary-action">
                    Partner / Sponsor Inquiry
                </button>
            </div>
        </div>
    </header>

    <main>
        <!-- 3. SPLIT EDITORIAL HERO & LIVE COMPETITION STATS -->
        <section class="py-14 sm:py-20 border-b border-white/5 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-8">
                
                @if($activeTournament)
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                        
                        <!-- LEFT EDITORIAL COLUMN (7 Cols) -->
                        <div class="lg:col-span-7 space-y-7">
                            
                            <div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded text-xs font-mono-tech uppercase font-bold status-pill">
                                <span class="w-2 h-2 rounded-full animate-ping" style="background-color: var(--primary);"></span>
                                <span>{{ $activeTournament->status === 'registration_open' ? 'REGISTRATION ACTIVE' : strtoupper(str_replace('_', ' ', $activeTournament->status)) }}</span>
                            </div>

                            <h1 class="font-display text-3xl sm:text-5xl lg:text-6xl font-black uppercase text-white tracking-tight leading-[1.1]">
                                {{ $heroHeadline }}
                            </h1>

                            <p class="text-slate-300 text-sm sm:text-base lg:text-lg max-w-xl font-normal leading-relaxed">
                                {{ $heroSubheadline }}
                            </p>

                            <!-- ACTION CLUSTER -->
                            <div class="flex flex-wrap items-center gap-3.5 pt-2">
                                <a href="{{ url('/mukhyadwar') }}" class="px-8 py-4 rounded-lg btn-primary-action text-xs font-extrabold flex items-center gap-2">
                                    <span>Register Squad</span>
                                    <span>→</span>
                                </a>

                                @if($activeTournament->rules_doc_link)
                                    <a href="{{ $activeTournament->rules_doc_link }}" target="_blank" class="px-6 py-4 rounded-lg btn-secondary-action text-xs flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Rulebook</span>
                                    </a>
                                @endif

                                @if($activeTournament->discord_server_url)
                                    <a href="{{ $activeTournament->discord_server_url }}" target="_blank" class="px-6 py-4 rounded-lg btn-secondary-action text-xs flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#5865F2]" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994.021-.041.001-.09-.041-.106a13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128c.126-.093.252-.19.373-.287a.074.074 0 0 1 .078-.01c3.927 1.793 8.18 1.793 12.061 0a.074.074 0 0 1 .079.009c.12.098.245.195.372.288a.077.077 0 0 1-.006.127c-.598.35-1.22.656-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.028z"/></svg>
                                        <span>Discord</span>
                                    </a>
                                @endif
                            </div>

                        </div>

                        <!-- RIGHT METRICS & COUNTDOWN PANEL (5 Cols) -->
                        <div class="lg:col-span-5">
                            <div class="editorial-card-featured p-6 sm:p-8 rounded-xl space-y-6">
                                
                                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                                    <div>
                                        <div class="text-[10px] font-mono-tech uppercase font-bold text-slate-400 tracking-wider">Tournament Prize Pool</div>
                                        <div class="font-display font-black text-2xl sm:text-3xl text-white mt-0.5">
                                            Rs. {{ number_format($prizePool) }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] font-mono-tech uppercase font-bold text-slate-400 tracking-wider">Entry Fee</div>
                                        <div class="font-display font-bold text-lg sm:text-xl text-white mt-0.5">
                                            Rs. {{ number_format($entryFee) }} <span class="text-xs font-mono-tech text-slate-400 font-normal">/{{ $entryFeeSuffix }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- COUNTDOWN TIMER -->
                                @if($registrationEnd)
                                    <div id="countdown-target" data-date="{{ $registrationEnd }}">
                                        <div class="text-[10px] font-mono-tech uppercase tracking-widest text-slate-400 mb-3 flex items-center justify-between">
                                            <span>Registration Cutoff</span>
                                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: var(--primary);"></span>
                                        </div>

                                        <div class="grid grid-cols-4 gap-2 text-center font-display">
                                            <div class="p-3 rounded-lg bg-[#05070a] border border-white/5">
                                                <div id="cd-days" class="text-xl sm:text-2xl font-black text-white">00</div>
                                                <div class="text-[9px] font-mono-tech text-slate-500 uppercase mt-0.5">Days</div>
                                            </div>
                                            <div class="p-3 rounded-lg bg-[#05070a] border border-white/5">
                                                <div id="cd-hours" class="text-xl sm:text-2xl font-black text-white">00</div>
                                                <div class="text-[9px] font-mono-tech text-slate-500 uppercase mt-0.5">Hours</div>
                                            </div>
                                            <div class="p-3 rounded-lg bg-[#05070a] border border-white/5">
                                                <div id="cd-mins" class="text-xl sm:text-2xl font-black text-white">00</div>
                                                <div class="text-[9px] font-mono-tech text-slate-500 uppercase mt-0.5">Mins</div>
                                            </div>
                                            <div class="p-3 rounded-lg bg-[#05070a] border border-white/5">
                                                <div id="cd-secs" class="text-xl sm:text-2xl font-black" style="color: var(--primary);">00</div>
                                                <div class="text-[9px] font-mono-tech text-slate-500 uppercase mt-0.5">Secs</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- STATS FOOTER -->
                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <div class="p-3 rounded-lg bg-[#05070a]/60 border border-white/5">
                                        <div class="text-[10px] font-mono-tech uppercase text-slate-400">Disciplines</div>
                                        <div class="font-display font-bold text-base text-white mt-0.5">{{ $gameTitles->count() }} Games</div>
                                    </div>
                                    <div class="p-3 rounded-lg bg-[#05070a]/60 border border-white/5">
                                        <div class="text-[10px] font-mono-tech uppercase text-slate-400">Registered Teams</div>
                                        <div class="font-display font-bold text-base text-white mt-0.5">{{ $registrationCount }} Squads</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                @else
                    <div class="py-12 text-center max-w-2xl mx-auto space-y-4">
                        <div class="inline-flex px-3 py-1 rounded text-xs font-mono-tech uppercase status-pill">Offseason / Preparation</div>
                        <h1 class="font-display text-3xl font-black uppercase text-white">Next Championship Series In Preparation</h1>
                        <p class="text-slate-400 text-sm">Register your player account on the portal to get ready for upcoming events.</p>
                        <a href="{{ url('/mukhyadwar/register') }}" class="inline-block px-6 py-3 rounded-lg btn-primary-action text-xs font-bold">Create Player Profile</a>
                    </div>
                @endif

            </div>
        </section>

        <!-- 4. OFFICIAL GAME DISCIPLINES (INTERACTIVE EDITORIAL PRESENTATION) -->
        @if($activeTournament && $gameTitles->count() > 0)
            <section class="py-16 sm:py-20 border-b border-white/5 scroll-mt-20" id="disciplines">
                <div class="max-w-7xl mx-auto px-4 sm:px-8">
                    
                    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                        <div>
                            <span class="text-xs font-mono-tech font-bold uppercase tracking-wider text-slate-400 block mb-1">
                                COMPETITIVE DISCIPLINES
                            </span>
                            <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-white">
                                Titles & Prize Breakdown
                            </h2>
                        </div>
                        <div class="text-xs font-mono-tech text-slate-400">
                            {{ $gameTitles->count() }} ACTIVE DISCIPLINES • TOTAL NPR RS. {{ number_format($prizePool) }}
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
                                } elseif (is_string($distributionRaw) && ! empty($distributionRaw)) {
                                    $decoded = json_decode($distributionRaw, true);
                                    if (is_array($decoded)) {
                                        $distributionItems = $decoded;
                                    }
                                }
                            @endphp
                            <div class="editorial-card rounded-xl p-6 flex flex-col justify-between group">
                                <div class="space-y-4">
                                    <!-- HEADER: LOGO & CATEGORY -->
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="w-14 h-14 rounded-lg bg-[#0e131d] border border-white/10 flex items-center justify-center p-1.5 shrink-0">
                                            @if($game->logo_path && file_exists(public_path('storage/' . $game->logo_path)))
                                                <img src="{{ asset('storage/' . $game->logo_path) }}" alt="{{ $game->name }}" class="w-full h-full object-contain">
                                            @else
                                                <span class="text-2xl">🎮</span>
                                            @endif
                                        </div>
                                        <span class="px-2.5 py-1 rounded text-[10px] font-mono-tech uppercase font-bold bg-[#0e131d] border border-white/10 text-slate-300">
                                            {{ str_replace('_', ' ', $game->game_type) }}
                                        </span>
                                    </div>

                                    <!-- TITLE & METRICS -->
                                    <div>
                                        <h3 class="font-display font-bold text-lg text-white group-hover:text-white transition-colors">
                                            {{ $game->name }}
                                        </h3>
                                        <div class="text-xs font-mono-tech text-slate-400 mt-1">
                                            Roster: {{ $game->min_main_players ?? 5 }} Main @if($game->max_substitutes) + {{ $game->max_substitutes }} Subs @endif
                                        </div>
                                    </div>

                                    <!-- PRIZE POOL VALUE -->
                                    <div class="p-3.5 rounded-lg bg-[#05070a] border border-white/5 flex items-center justify-between">
                                        <span class="text-xs font-mono-tech text-slate-400">Prize Pool:</span>
                                        <span class="font-display font-extrabold text-sm text-white">
                                            {{ $allocatedPrize > 0 ? 'Rs. ' . number_format($allocatedPrize) : 'TBD' }}
                                        </span>
                                    </div>

                                    <!-- DISTRIBUTION TIERS -->
                                    @if(!empty($distributionItems))
                                        <div class="pt-2 border-t border-white/5 space-y-1.5 text-xs font-mono-tech">
                                            @foreach($distributionItems as $rank => $amount)
                                                <div class="flex items-center justify-between text-slate-300 py-0.5">
                                                    <span class="text-slate-400">{{ $rank }}</span>
                                                    <span class="font-semibold text-white">Rs. {{ is_numeric($amount) ? number_format((float)$amount) : $amount }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-6 pt-4 border-t border-white/5">
                                    <a href="{{ url('/mukhyadwar') }}" class="w-full py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider text-center block btn-secondary-action">
                                        Enter Discipline →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </section>
        @endif

        <!-- 5. VERIFIED CONTENDER SQUADS -->
        @if($approvedRegistrations->count() > 0)
            <section class="py-16 sm:py-20 border-b border-white/5 scroll-mt-20" id="contenders">
                <div class="max-w-7xl mx-auto px-4 sm:px-8">
                    
                    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                        <div>
                            <span class="text-xs font-mono-tech font-bold uppercase tracking-wider text-slate-400 block mb-1">
                                COMPETITION ROSTER
                            </span>
                            <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-white">
                                Confirmed Squads
                            </h2>
                        </div>
                        <a href="{{ url('/mukhyadwar') }}" class="text-xs font-mono-tech font-bold uppercase tracking-wider text-white hover:underline flex items-center gap-1">
                            <span>Register Squad</span>
                            <span>→</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                        @foreach($approvedRegistrations as $reg)
                            @php $team = $reg->team; @endphp
                            @if($team)
                                <div class="editorial-card rounded-xl p-4 text-center flex flex-col items-center justify-between group">
                                    <div class="w-14 h-14 rounded-lg bg-[#0e131d] border border-white/10 flex items-center justify-center p-2 mb-3">
                                        @if($team->logo_path && file_exists(public_path('storage/' . $team->logo_path)))
                                            <img src="{{ asset('storage/' . $team->logo_path) }}" alt="{{ $team->name }}" class="w-full h-full object-contain">
                                        @else
                                            <span class="font-display font-black text-sm text-slate-400">{{ strtoupper(substr($team->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="w-full">
                                        <h4 class="font-display font-bold text-xs text-white truncate">{{ $team->name }}</h4>
                                        @if($team->tag)
                                            <span class="text-[10px] font-mono-tech text-slate-400 block">[{{ $team->tag }}]</span>
                                        @endif
                                    </div>

                                    <div class="mt-2.5 pt-2 border-t border-white/5 w-full">
                                        <span class="text-[9px] font-mono-tech uppercase text-slate-400 truncate block">
                                            {{ $team->gameTitle?->name ?? 'Discipline' }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                </div>
            </section>
        @endif

        <!-- 6. TOURNAMENT CIRCUIT -->
        @if($otherTournaments->count() > 0)
            <section class="py-16 sm:py-20 border-b border-white/5 scroll-mt-20" id="circuit">
                <div class="max-w-7xl mx-auto px-4 sm:px-8">
                    
                    <div class="mb-10">
                        <span class="text-xs font-mono-tech font-bold uppercase tracking-wider text-slate-400 block mb-1">
                            NATIONAL SERIES
                        </span>
                        <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-white">
                            Championship Circuit
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($otherTournaments as $tournament)
                            <div class="editorial-card rounded-xl p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-3 mb-3">
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-mono-tech uppercase font-bold bg-[#0e131d] border border-white/10 text-slate-300">
                                            {{ strtoupper(str_replace('_', ' ', $tournament->status)) }}
                                        </span>
                                        <span class="text-xs font-mono-tech text-slate-400">
                                            {{ $tournament->start_date ? $tournament->start_date->format('M d, Y') : 'Date TBD' }}
                                        </span>
                                    </div>

                                    <h3 class="font-display text-lg font-bold text-white mb-2">
                                        {{ $tournament->name }}
                                    </h3>

                                    @if($tournament->description)
                                        <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed mb-4">
                                            {{ strip_tags($tournament->description) }}
                                        </p>
                                    @endif
                                </div>

                                <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                                    <span class="font-display font-bold text-sm text-white">
                                        Rs. {{ number_format($tournament->prize_pool_total) }} Pool
                                    </span>
                                    <a href="{{ url('/mukhyadwar') }}" class="text-xs font-mono-tech font-bold uppercase tracking-wider text-white hover:underline">
                                        View Portal →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </section>
        @endif

        <!-- 7. SPONSORS & PARTNERS SHOWCASE -->
        @if($sponsors->count() > 0 || $partners->count() > 0)
            <section class="py-16 sm:py-20 border-b border-white/5">
                <div class="max-w-7xl mx-auto px-4 sm:px-8">
                    
                    <div class="text-center max-w-xl mx-auto mb-12">
                        <span class="text-xs font-mono-tech font-bold uppercase tracking-wider text-slate-400 block mb-1">
                            OFFICIAL SUPPORTERS
                        </span>
                        <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-white">
                            Sponsors & Alliance
                        </h2>
                    </div>

                    <!-- SPONSORS -->
                    @if($sponsors->count() > 0)
                        <div class="flex flex-wrap items-center justify-center gap-4 mb-8">
                            @foreach($sponsors->flatten() as $sponsor)
                                <a href="{{ $sponsor->website_url ?: '#' }}" target="{{ $sponsor->website_url ? '_blank' : '_self' }}" class="editorial-card rounded-xl p-4 w-40 sm:w-48 h-20 flex items-center justify-center group">
                                    @if($sponsor->logo_url && file_exists(public_path('storage/' . $sponsor->logo_url)))
                                        <img src="{{ asset('storage/' . $sponsor->logo_url) }}" alt="{{ $sponsor->name }}" class="max-h-10 w-auto object-contain filter grayscale group-hover:grayscale-0 transition-all">
                                    @else
                                        <span class="font-display font-bold text-xs text-slate-400 group-hover:text-white">{{ $sponsor->name }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- PARTNERS -->
                    @if($partners->count() > 0)
                        <div class="flex flex-wrap items-center justify-center gap-4">
                            @foreach($partners->flatten() as $partner)
                                <a href="{{ $partner->website_url ?: '#' }}" target="{{ $partner->website_url ? '_blank' : '_self' }}" class="editorial-card rounded-xl p-4 w-40 sm:w-52 h-20 flex flex-col items-center justify-center group">
                                    @if($partner->logo_url && file_exists(public_path('storage/' . $partner->logo_url)))
                                        <img src="{{ asset('storage/' . $partner->logo_url) }}" alt="{{ $partner->name }}" class="max-h-8 w-auto object-contain filter grayscale group-hover:grayscale-0 transition-all">
                                    @else
                                        <span class="font-display font-bold text-xs text-slate-400 group-hover:text-white truncate">{{ $partner->name }}</span>
                                    @endif
                                    @if($partner->title)
                                        <span class="text-[9px] font-mono-tech text-slate-500 group-hover:text-slate-400 mt-0.5 truncate">{{ $partner->title }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif

                </div>
            </section>
        @endif

        <!-- 8. HIGH-IMPACT FINAL CTA -->
        <section class="py-16 sm:py-24">
            <div class="max-w-4xl mx-auto px-4 sm:px-8 text-center space-y-6">
                <span class="text-xs font-mono-tech font-bold uppercase tracking-wider status-pill px-3 py-1.5 rounded inline-block">
                    READY TO COMPETE?
                </span>
                <h2 class="font-display text-3xl sm:text-5xl font-black uppercase text-white">
                    Step Into The Arena
                </h2>
                <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto">
                    Form your team, enter the championship bracket, and fight for the national championship title and prize pool.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                    <a href="{{ url('/mukhyadwar/register') }}" class="px-8 py-4 rounded-lg btn-primary-action text-xs font-extrabold">
                        Register Squad Now
                    </a>
                    <button onclick="document.getElementById('sponsor-modal').classList.remove('hidden')" class="px-8 py-4 rounded-lg btn-secondary-action text-xs font-bold">
                        Partner Inquiries
                    </button>
                </div>
            </div>
        </section>
    </main>

    <!-- LIVEWIRE SPONSOR INQUIRY MODAL -->
    <div id="sponsor-modal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-black/80 backdrop-blur-md hidden [&:not(.hidden)]:flex">
        <div class="relative w-full max-w-lg rounded-xl bg-[#0e131d] border border-white/15 p-6 sm:p-8 shadow-2xl max-h-[90vh] overflow-y-auto">
            <button onclick="document.getElementById('sponsor-modal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-white text-xl font-bold cursor-pointer">✕</button>
            <div class="text-center mb-6">
                <h3 class="font-display text-lg font-bold uppercase text-white">Partner / Sponsor Inquiry</h3>
                <p class="text-xs text-slate-400 mt-1 font-mono-tech">Submit your brand inquiry to partner with our national tournament series.</p>
            </div>
            <livewire:sponsor-query-form />
        </div>
    </div>

    <!-- 9. MINIMAL TECHNICAL FOOTER -->
    <footer class="border-t border-white/5 bg-[#030508] py-8 text-xs font-mono-tech text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="font-display font-bold text-white text-sm uppercase">{{ $tournamentName }}</span>
                <span>•</span>
                <span>© {{ date('Y') }} All rights reserved</span>
            </div>

            <div class="flex items-center gap-6 text-[11px]">
                <a href="{{ url('/mukhyadwar/login') }}" class="hover:text-slate-300">Player Login</a>
                <a href="{{ url('/maidan/login') }}" class="hover:text-slate-300">Admin Control</a>
                <a href="{{ url('/up') }}" class="hover:text-slate-300">Status</a>
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
