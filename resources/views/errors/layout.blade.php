@php
    $sessionTheme = session('theme') ?? session('filament_theme') ?? session('filament.theme') ?? session('theme_mode');
    $isDark = ($sessionTheme !== 'light');
    $themeClass = $isDark ? 'dark' : 'light';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $themeClass }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'System Exception') // SHOWDOWN ESPORTS</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Orbitron:wght@400..900&display=swap" rel="stylesheet">

    <!-- Vite Assets (CSS & JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cyber-bg text-emerald-100 font-sans antialiased min-h-screen flex flex-col justify-between overflow-x-hidden relative selection:bg-emerald-500 selection:text-black">

    <!-- Background Particle Canvas -->
    <canvas id="cyber-canvas" class="fixed inset-0 pointer-events-none z-0 opacity-60"></canvas>

    <!-- Scanline Overlay -->
    <div class="fixed inset-0 pointer-events-none z-10 scanlines opacity-40"></div>

    <!-- HUD Header -->
    <header class="relative z-20 border-b border-emerald-500/20 bg-emerald-950/40 backdrop-blur-md px-4 lg:px-8 py-3">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Brand Badge -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-400/50 flex items-center justify-center shadow-[0_0_15px_#10b981] group-hover:scale-105 transition-transform">
                        <span class="font-mono text-emerald-400 font-bold text-lg">⚡</span>
                    </div>
                    <span class="font-['Orbitron'] font-black tracking-wider text-xl bg-gradient-to-r from-emerald-300 via-emerald-400 to-teal-200 bg-clip-text text-transparent">
                        SHOWDOWN
                    </span>
                </a>
                <span class="hidden sm:inline-block px-2.5 py-0.5 text-[10px] font-mono uppercase tracking-widest bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 clip-corner-sm">
                    ESPORTS HUD v2.4
                </span>
            </div>

            <!-- System Indicators -->
            <div class="flex items-center gap-4 text-xs font-mono">
                <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-emerald-950/60 border border-emerald-500/20 rounded">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-emerald-300">TICK: <strong class="text-emerald-400">64</strong></span>
                    <span class="text-emerald-700">|</span>
                    <span class="text-emerald-300">PING: <strong class="text-emerald-400">14ms</strong></span>
                </div>

                <!-- Sound FX Toggle -->
                <button id="sound-toggle-btn" type="button" title="Toggle Sound FX" class="p-2 bg-emerald-950/60 border border-emerald-500/30 hover:border-emerald-400 text-emerald-400 rounded transition-colors flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                    </svg>
                    <span class="hidden sm:inline text-[11px] uppercase tracking-wider">Audio</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Error Content Container -->
    <main class="relative z-20 my-auto px-4 py-8 max-w-5xl mx-auto w-full">
        <div class="esports-card clip-corner p-6 sm:p-10 relative overflow-hidden shadow-[0_0_50px_rgba(16,185,129,0.15)]">
            <!-- Decorative Laser Scan Beam -->
            <div class="absolute inset-x-0 h-0.5 bg-gradient-to-r from-transparent via-emerald-400 to-transparent animate-scan-beam pointer-events-none"></div>
            
            <!-- Corner Decorative Elements -->
            <div class="absolute top-2 left-2 text-[10px] font-mono text-emerald-500/40 select-none">SYS_ERR // SECTION_09</div>
            <div class="absolute top-2 right-2 text-[10px] font-mono text-emerald-500/40 select-none">MATCH_ID: #@yield('code', '000')</div>
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Main Message Section -->
                <div class="@yield('main_cols', 'lg:col-span-7') space-y-6">
                    <!-- Status Pill -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/40 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span class="font-mono text-xs text-emerald-400 uppercase tracking-widest font-semibold">
                            @yield('subhead', 'CRITICAL GAME EVENT')
                        </span>
                    </div>

                    <!-- Huge Code Tag -->
                    <h1 class="font-['Orbitron'] font-black text-6xl sm:text-8xl tracking-tight bg-gradient-to-b from-white via-emerald-200 to-emerald-500 bg-clip-text text-transparent cyber-glow-text animate-float">
                        @yield('code', '404')
                    </h1>

                    <!-- Title -->
                    <h2 class="font-['Orbitron'] font-bold text-2xl sm:text-3xl text-emerald-300 tracking-wide">
                        @yield('message_title', 'MATCH DISCONNECTED')
                    </h2>

                    <!-- Playful Esports Content -->
                    <p class="text-emerald-100/80 leading-relaxed text-sm sm:text-base border-l-2 border-emerald-500/40 pl-4 py-1 bg-emerald-950/20">
                        @yield('message_body', 'Your request has wandered off into the fog of war. The map bounds could not resolve this tactical vector.')
                    </p>

                    <!-- Tactical Action Buttons -->
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ url('/') }}" class="clip-corner-sm px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-black font-['Orbitron'] font-bold text-xs uppercase tracking-widest shadow-[0_0_20px_#10b981] transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-2">
                            <span>⚡ RESPAWN AT BASE</span>
                        </a>

                        <button type="button" onclick="window.location.reload()" class="clip-corner-sm px-6 py-3 bg-emerald-950/80 hover:bg-emerald-900 border border-emerald-500/40 hover:border-emerald-400 text-emerald-300 font-['Orbitron'] font-bold text-xs uppercase tracking-widest transition-colors flex items-center gap-2 cursor-pointer">
                            <span>🔄 RECONNECT MATCH</span>
                        </button>
                    </div>
                </div>

                <!-- Interactive Aim Lab Mini Game Widget -->
                <div class="lg:col-span-5 border border-emerald-500/30 bg-emerald-950/60 p-4 rounded-xl relative">
                    <div class="flex items-center justify-between mb-3 border-b border-emerald-500/20 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-['Orbitron'] font-bold text-emerald-400 tracking-wider">🎯 AIM LAB WIDGET</span>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 bg-emerald-500/20 text-emerald-300 rounded">INTERACTIVE</span>
                        </div>
                        <button id="aim-reset-btn" type="button" class="text-[10px] font-mono text-emerald-400 hover:text-emerald-200 underline cursor-pointer">
                            [RESET TARGETS]
                        </button>
                    </div>

                    <!-- Mini Stats HUD -->
                    <div class="grid grid-cols-4 gap-2 mb-3 text-center font-mono text-[11px]">
                        <div class="bg-black/40 p-1.5 rounded border border-emerald-500/20">
                            <div class="text-emerald-500/70 text-[9px]">SCORE</div>
                            <div id="aim-score" class="font-bold text-emerald-300">0</div>
                        </div>
                        <div class="bg-black/40 p-1.5 rounded border border-emerald-500/20">
                            <div class="text-emerald-500/70 text-[9px]">STREAK</div>
                            <div id="aim-streak" class="font-bold text-emerald-300">0</div>
                        </div>
                        <div class="bg-black/40 p-1.5 rounded border border-emerald-500/20">
                            <div class="text-emerald-500/70 text-[9px]">APM</div>
                            <div id="aim-apm" class="font-bold text-emerald-300">0</div>
                        </div>
                        <div class="bg-black/40 p-1.5 rounded border border-emerald-500/20">
                            <div class="text-emerald-500/70 text-[9px]">HIGH</div>
                            <div id="aim-high-score" class="font-bold text-emerald-400">0</div>
                        </div>
                    </div>

                    <!-- Interactive Target Arena -->
                    <div id="esports-aim-arena" class="arena-bg relative h-48 sm:h-56 bg-black/60 rounded-lg border border-emerald-500/30 overflow-hidden cursor-crosshair flex items-center justify-center">
                        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(16,185,129,0.08)_0%,transparent_70%)]"></div>
                        <div class="pointer-events-none text-[10px] font-mono text-emerald-500/30 select-none">CLICK THE EMERALD TARGETS TO TRAIN WHILE WAITING</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- HUD Footer -->
    <footer class="relative z-20 border-t border-emerald-500/20 bg-emerald-950/40 backdrop-blur-md px-4 py-3 text-xs font-mono">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-emerald-400/80">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span>STATUS: OPERATIONAL</span>
                <span class="text-emerald-700">|</span>
                <span>REGION: AP-SOUTH-1</span>
            </div>
            <div class="text-emerald-500/60 text-[11px]">
                SHOWDOWN ENGINE &copy; {{ date('Y') }} // ALL RIGHTS RESERVED
            </div>
        </div>
    </footer>

</body>
</html>
