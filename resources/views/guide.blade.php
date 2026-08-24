<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Team Manager Registration Guide — Outlaw Showdown Esports</title>
    <meta name="description" content="Official knowledge base and step-by-step registration guide for team managers registering squads for PUBG Mobile, MLBB Open, MLBB Women's, and Valorant in Outlaw Showdown.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $themeColor = $activeTournament?->theme_color ?? '#10b981';
        $tournamentName = $activeTournament?->name ?? 'Outlaw Showdown 2026';
    @endphp

    <style>
        :root {
            --primary: {{ $themeColor }};
            --bg-dark: #020406;
            --bg-card: rgba(8, 12, 20, 0.85);
            --border-card: rgba(255, 255, 255, 0.08);
            --border-accent: rgba(16, 185, 129, 0.35);
        }
        body {
            background-color: var(--bg-dark);
            color: #f1f5f9;
            font-family: 'Space Grotesk', sans-serif;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.06) 0%, transparent 40%);
            background-attachment: fixed;
        }
        .font-display { font-family: 'Orbitron', sans-serif; }
        .font-mono-tech { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
        .guide-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            backdrop-filter: blur(16px);
            transition: all 0.25s ease;
        }
        .guide-card:hover {
            border-color: var(--border-accent);
        }
        .step-badge {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(6, 182, 212, 0.1));
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #10b981;
        }
        .tab-btn.active {
            background: #10b981;
            color: #020406;
            font-weight: 800;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-black">

    <!-- FLOATING TOP NAVIGATION -->
    <header class="sticky top-0 z-40 w-full backdrop-blur-xl bg-[#020406]/85 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <span class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center font-display font-black text-emerald-400 text-sm group-hover:scale-105 transition-transform">⚡</span>
                    <span class="font-display font-black text-base sm:text-lg uppercase tracking-wider text-white">
                        OUTLAW<span class="text-emerald-400">SHOWDOWN</span>
                    </span>
                </a>
                <span class="hidden sm:inline-block px-2.5 py-0.5 rounded text-[10px] font-mono-tech uppercase font-bold bg-white/5 border border-white/10 text-slate-400">
                    Knowledge Base
                </span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="text-xs font-mono-tech text-slate-400 hover:text-white transition-colors px-3 py-1.5 rounded-lg">
                    ← Back to Tournament
                </a>
                <a href="{{ url('/mukhyadwar/login') }}" class="text-xs font-mono-tech text-slate-300 hover:text-emerald-400 transition-colors px-3 py-1.5 rounded-lg border border-white/10 hover:border-emerald-500/30">
                    Sign In
                </a>
                <a href="{{ url('/mukhyadwar/register') }}" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-black uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                    Register Squad →
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN GUIDE CONTENT -->
    <main class="grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-16">

        <!-- 1. HERO BANNER -->
        <section class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-mono-tech font-bold uppercase tracking-wider">
                <span>📖</span> Official Team Manager Manual
            </div>
            <h1 class="font-display text-3xl sm:text-5xl font-black uppercase text-white tracking-tight leading-tight">
                Tournament Registration <span class="text-emerald-400">Knowledge Base</span>
            </h1>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                Step-by-step master guide for esports team managers to create manager accounts, assemble rosters, attach required player verifications, and submit squad applications for <strong class="text-white">{{ $tournamentName }}</strong>.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                <a href="#quick-steps" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/15 text-white text-xs font-bold transition-all">
                    4-Step Quickstart ↓
                </a>
                <a href="#disciplines" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/15 text-white text-xs font-bold transition-all">
                    Game Rules & Roster Limits ↓
                </a>
                <a href="#compliance" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/15 text-white text-xs font-bold transition-all">
                    Verification Checklist ↓
                </a>
            </div>
        </section>

        <!-- 2. QUICK OVERVIEW STATS -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="guide-card rounded-2xl p-5 text-center">
                <div class="text-2xl font-display font-black text-emerald-400">100%</div>
                <div class="text-xs font-mono-tech text-slate-400 uppercase mt-1">Digital Roster Verification</div>
            </div>
            <div class="guide-card rounded-2xl p-5 text-center">
                <div class="text-2xl font-display font-black text-cyan-400">4 Titles</div>
                <div class="text-xs font-mono-tech text-slate-400 uppercase mt-1">PUBGm, MLBB (Open & Women), Valorant</div>
            </div>
            <div class="guide-card rounded-2xl p-5 text-center">
                <div class="text-2xl font-display font-black text-purple-400">12-24 Hrs</div>
                <div class="text-xs font-mono-tech text-slate-400 uppercase mt-1">Admin Verification Window</div>
            </div>
            <div class="guide-card rounded-2xl p-5 text-center">
                <div class="text-2xl font-display font-black text-amber-400">eSewa / Khalti</div>
                <div class="text-xs font-mono-tech text-slate-400 uppercase mt-1">Official Entry Fee Gateways</div>
            </div>
        </section>

        <!-- 3. THE 4-STEP REGISTRATION PIPELINE -->
        <section id="quick-steps" class="space-y-8 scroll-mt-24">
            <div class="border-b border-white/10 pb-4 flex flex-col sm:flex-row sm:items-end justify-between gap-2">
                <div>
                    <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                        PIPELINE WALKTHROUGH
                    </span>
                    <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-white">
                        4-Step Squad Registration Process
                    </h2>
                </div>
                <span class="text-xs font-mono-tech text-slate-400">Estimated completion time: ~5-10 minutes</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- STEP 1 -->
                <div class="guide-card rounded-2xl p-6 sm:p-7 space-y-4 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="step-badge px-3 py-1 rounded-lg text-xs font-mono-tech font-bold">
                            STEP 01
                        </span>
                        <span class="text-xs font-mono-tech text-slate-400">Manager Access</span>
                    </div>
                    <h3 class="font-display text-xl font-black text-white">
                        Create Your Team Manager Account
                    </h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Every squad must be registered by a designated Team Manager or Squad Captain.
                    </p>
                    <ul class="space-y-2 text-xs text-slate-300 font-mono-tech">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Visit <strong class="text-white">showdown.outlawnp.com/mukhyadwar/register</strong></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Provide Manager Full Legal Name, active Email Address, and secure Password.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Sign in immediately to access your <strong>Mukhyadwar Management Dashboard</strong>.</span>
                        </li>
                    </ul>
                    <div class="pt-2">
                        <a href="{{ url('/mukhyadwar/register') }}" class="inline-flex items-center gap-1.5 text-xs font-mono-tech text-emerald-400 hover:text-emerald-300 font-bold">
                            Open Registration Page →
                        </a>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div class="guide-card rounded-2xl p-6 sm:p-7 space-y-4 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="step-badge px-3 py-1 rounded-lg text-xs font-mono-tech font-bold">
                            STEP 02
                        </span>
                        <span class="text-xs font-mono-tech text-slate-400">Roster Assembly</span>
                    </div>
                    <h3 class="font-display text-xl font-black text-white">
                        Create Squad & Add Player Profiles
                    </h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Navigate to <strong>"My Teams"</strong> in the sidebar menu and click <strong>"New Team"</strong>.
                    </p>
                    <ul class="space-y-2 text-xs text-slate-300 font-mono-tech">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span><strong>Squad Profile:</strong> Enter Team Name, Short Tag/Prefix (e.g. OTL), select Target Game Title, and upload a clean 1:1 PNG Crest.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span><strong>Player Details:</strong> Full Name, In-Game Name (IGN), In-Game Role, WhatsApp Number, and Date of Birth.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span><strong>Visual Proof:</strong> Player front photo (hands folded) + In-Game Profile / Career Stats screenshot.</span>
                        </li>
                    </ul>
                </div>

                <!-- STEP 3 -->
                <div class="guide-card rounded-2xl p-6 sm:p-7 space-y-4 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="step-badge px-3 py-1 rounded-lg text-xs font-mono-tech font-bold">
                            STEP 03
                        </span>
                        <span class="text-xs font-mono-tech text-slate-400">Tournament Entry</span>
                    </div>
                    <h3 class="font-display text-xl font-black text-white">
                        Enter Tournament & Attach Payment Receipt
                    </h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Navigate to <strong>"Tournaments"</strong> or use the <strong>"Register Team Now"</strong> button on your dashboard.
                    </p>
                    <ul class="space-y-2 text-xs text-slate-300 font-mono-tech">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Select your created Team Squad from the dropdown.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Check all participating roster players (Ensure minimum main player count and max substitute rules are met).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Upload the clear payment screenshot (eSewa/Khalti) showing the exact Entry Fee amount and Transaction ID.</span>
                        </li>
                    </ul>
                </div>

                <!-- STEP 4 -->
                <div class="guide-card rounded-2xl p-6 sm:p-7 space-y-4 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="step-badge px-3 py-1 rounded-lg text-xs font-mono-tech font-bold">
                            STEP 04
                        </span>
                        <span class="text-xs font-mono-tech text-slate-400">Verification & Match Days</span>
                    </div>
                    <h3 class="font-display text-xl font-black text-white">
                        Track Application & Discord Check-In
                    </h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Monitor your submission status under <strong>"Tournament Applications"</strong>.
                    </p>
                    <ul class="space-y-2 text-xs text-slate-300 font-mono-tech">
                        <li class="flex items-start gap-2">
                            <span class="text-amber-400 font-bold">⏳</span>
                            <span><strong>Status: Pending:</strong> Referees are reviewing roster credentials and verifying payment receipt.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span><strong>Status: Approved:</strong> Your squad is confirmed and seeded into the tournament brackets!</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-cyan-400 font-bold">⚡</span>
                            <span>Join the Official Outlaw Esports Discord to receive Room IDs, Passwords, and Match Lobby schedules.</span>
                        </li>
                    </ul>
                </div>

            </div>
        </section>

        <!-- 4. GAME-BY-GAME SPECIFICATIONS (4 TITLES) -->
        <section id="disciplines" class="space-y-8 scroll-mt-24">
            <div class="border-b border-white/10 pb-4">
                <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                    DISCIPLINE CRITERIA
                </span>
                <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-white">
                    Game-Specific Roster & Proof Requirements
                </h2>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">Select a game title below to view exact roster composition limits, verification requirements, and prize pools.</p>
            </div>

            <!-- TABS CONTROLLER -->
            <div class="flex flex-wrap gap-2 p-1.5 rounded-2xl bg-[#080c14] border border-white/10">
                <button onclick="switchTab('pubgm')" id="tab-btn-pubgm" class="tab-btn active px-4 py-2.5 rounded-xl text-xs font-mono-tech font-bold transition-all flex items-center gap-2 cursor-pointer">
                    <span>🔫</span> PUBG Mobile
                </button>
                <button onclick="switchTab('mlbb-open')" id="tab-btn-mlbb-open" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-mono-tech font-bold text-slate-400 hover:text-white transition-all flex items-center gap-2 cursor-pointer">
                    <span>⚔️</span> MLBB (Open)
                </button>
                <button onclick="switchTab('mlbb-women')" id="tab-btn-mlbb-women" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-mono-tech font-bold text-slate-400 hover:text-white transition-all flex items-center gap-2 cursor-pointer">
                    <span>👑</span> MLBB (Women's)
                </button>
                <button onclick="switchTab('valorant')" id="tab-btn-valorant" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-mono-tech font-bold text-slate-400 hover:text-white transition-all flex items-center gap-2 cursor-pointer">
                    <span>🎯</span> Valorant
                </button>
            </div>

            <!-- TAB 1: PUBG MOBILE -->
            <div id="tab-content-pubgm" class="tab-pane space-y-6">
                <div class="guide-card rounded-2xl p-6 sm:p-8 space-y-6 border-l-4 border-l-emerald-500">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
                        <div>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-mono-tech uppercase font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 inline-block mb-1">
                                Battle Royale Discipline
                            </span>
                            <h3 class="font-display text-2xl font-black text-white">PUBG Mobile (Squad TPP)</h3>
                        </div>
                        <div class="text-right sm:text-right">
                            <span class="text-xs font-mono-tech text-slate-400 block uppercase">Allocated Prize Pool</span>
                            <span class="font-display font-black text-xl text-emerald-400">Rs. 150,000</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs font-mono-tech">
                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-emerald-400 font-bold uppercase text-[11px]">Roster Composition</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>Main Players:</strong> Exactly 4 players</li>
                                <li>• <strong>Substitutes:</strong> Up to 2 players (Optional)</li>
                                <li>• <strong>Coach / Manager:</strong> 1 max</li>
                                <li>• <strong>Total Squad Size:</strong> Max 6 players</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-cyan-400 font-bold uppercase text-[11px]">Required In-Game Info</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>In-Game Name (IGN):</strong> Exact case-sensitive handle</li>
                                <li>• <strong>PUBG Character ID:</strong> 9-11 digit numeric ID</li>
                                <li>• <strong>In-Game Role:</strong> IGL, Assaulter, Sniper, Support</li>
                                <li>• <strong>Device:</strong> Handheld Phone only (No iPads / Emulators)</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-purple-400 font-bold uppercase text-[11px]">Proof & Attachments</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>Player Photo:</strong> Front-facing with hands folded</li>
                                <li>• <strong>Profile Screenshot:</strong> In-game Basic Info screen showing Character ID & Season Tier</li>
                                <li>• <strong>Team Crest:</strong> Transparent PNG (1:1 Ratio)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-xs text-amber-300 leading-relaxed font-mono-tech">
                        ⚠️ <strong>PUBG Mobile Device Rule:</strong> Strictly handheld mobile phones (iOS / Android) only. iPads, Tablets, and PC Emulators (Gameloop, BlueStacks) are strictly prohibited and will lead to instant disqualification.
                    </div>
                </div>
            </div>

            <!-- TAB 2: MLBB OPEN -->
            <div id="tab-content-mlbb-open" class="tab-pane space-y-6 hidden">
                <div class="guide-card rounded-2xl p-6 sm:p-8 space-y-6 border-l-4 border-l-cyan-500">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
                        <div>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-mono-tech uppercase font-bold bg-cyan-500/20 text-cyan-400 border border-cyan-500/40 inline-block mb-1">
                                5v5 MOBA Discipline (Open Division)
                            </span>
                            <h3 class="font-display text-2xl font-black text-white">Mobile Legends: Bang Bang (Open)</h3>
                        </div>
                        <div class="text-right sm:text-right">
                            <span class="text-xs font-mono-tech text-slate-400 block uppercase">Allocated Prize Pool</span>
                            <span class="font-display font-black text-xl text-cyan-400">Rs. 120,000</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs font-mono-tech">
                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-cyan-400 font-bold uppercase text-[11px]">Roster Composition</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>Main Players:</strong> Exactly 5 players</li>
                                <li>• <strong>Substitutes:</strong> Up to 2 players</li>
                                <li>• <strong>Coach / Analyst:</strong> 1 max</li>
                                <li>• <strong>Total Squad Size:</strong> Max 7 players</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-emerald-400 font-bold uppercase text-[11px]">Required In-Game Info</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>IGN:</strong> In-Game Name</li>
                                <li>• <strong>MLBB ID & Server:</strong> e.g. <span class="text-white">12345678 (1234)</span></li>
                                <li>• <strong>Lane Position:</strong> EXP Lane, Jungler, Mid, Gold Lane, Roamer</li>
                                <li>• <strong>Current Rank:</strong> Mythic or above recommended</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-purple-400 font-bold uppercase text-[11px]">Proof & Attachments</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>Player Photo:</strong> Hands folded studio pose</li>
                                <li>• <strong>Profile Screenshot:</strong> In-game account profile page showing User ID, Zone ID & Highest Rank</li>
                                <li>• <strong>Team Logo:</strong> High-res PNG crest</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: MLBB WOMEN'S -->
            <div id="tab-content-mlbb-women" class="tab-pane space-y-6 hidden">
                <div class="guide-card rounded-2xl p-6 sm:p-8 space-y-6 border-l-4 border-l-pink-500">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
                        <div>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-mono-tech uppercase font-bold bg-pink-500/20 text-pink-400 border border-pink-500/40 inline-block mb-1">
                                5v5 MOBA (Women's Championship)
                            </span>
                            <h3 class="font-display text-2xl font-black text-white">Mobile Legends: Bang Bang (Women's)</h3>
                        </div>
                        <div class="text-right sm:text-right">
                            <span class="text-xs font-mono-tech text-slate-400 block uppercase">Allocated Prize Pool</span>
                            <span class="font-display font-black text-xl text-pink-400">Rs. 80,000</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs font-mono-tech">
                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-pink-400 font-bold uppercase text-[11px]">Roster & Gender Criteria</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>100% Female Roster:</strong> All 5 main players and substitutes must be female.</li>
                                <li>• <strong>Main Players:</strong> Exactly 5 players</li>
                                <li>• <strong>Substitutes:</strong> Up to 2 players</li>
                                <li>• <strong>Coach/Manager:</strong> Any gender</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-cyan-400 font-bold uppercase text-[11px]">Required In-Game Info</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>IGN:</strong> In-Game Name</li>
                                <li>• <strong>MLBB User ID + Server:</strong> e.g. <span class="text-white">87654321 (9876)</span></li>
                                <li>• <strong>Laning Role:</strong> EXP, Jungle, Mid, Gold, Roam</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-purple-400 font-bold uppercase text-[11px]">Identity Verification</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>Player Photo:</strong> Hands folded facing camera</li>
                                <li>• <strong>ID Proof:</strong> Citizenship / National ID / Student ID (front & back upload)</li>
                                <li>• <strong>Profile Screenshot:</strong> In-game profile screenshot</li>
                            </ul>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-pink-500/10 border border-pink-500/30 text-xs text-pink-300 leading-relaxed font-mono-tech">
                        👑 <strong>Women's Division Integrity:</strong> All participant identities are verified during check-in. Video verification may be requested prior to knockout matches.
                    </div>
                </div>
            </div>

            <!-- TAB 4: VALORANT -->
            <div id="tab-content-valorant" class="tab-pane space-y-6 hidden">
                <div class="guide-card rounded-2xl p-6 sm:p-8 space-y-6 border-l-4 border-l-red-500">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
                        <div>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-mono-tech uppercase font-bold bg-red-500/20 text-red-400 border border-red-500/40 inline-block mb-1">
                                PC Tactical Shooter Discipline
                            </span>
                            <h3 class="font-display text-2xl font-black text-white">Valorant (5v5 Standard)</h3>
                        </div>
                        <div class="text-right sm:text-right">
                            <span class="text-xs font-mono-tech text-slate-400 block uppercase">Allocated Prize Pool</span>
                            <span class="font-display font-black text-xl text-red-400">Rs. 80,000</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs font-mono-tech">
                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-red-400 font-bold uppercase text-[11px]">Roster Composition</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>Main Players:</strong> Exactly 5 players</li>
                                <li>• <strong>Substitutes:</strong> Up to 2 players</li>
                                <li>• <strong>Coach:</strong> 1 max</li>
                                <li>• <strong>Platform:</strong> PC (Riot Client - Mumbai / APAC Server)</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-cyan-400 font-bold uppercase text-[11px]">Required Riot ID Info</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>Riot ID with Tagline:</strong> e.g. <span class="text-white">TenZ#1234</span> or <span class="text-white">Player#NEP</span></li>
                                <li>• <strong>In-Game Role:</strong> Duelist, Initiator, Controller, Sentinel, IGL</li>
                                <li>• <strong>Discord Tag:</strong> Required for lobby communications</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-xl bg-[#020406] border border-white/10 space-y-2">
                            <div class="text-emerald-400 font-bold uppercase text-[11px]">Proof & Attachments</div>
                            <ul class="space-y-1.5 text-slate-300">
                                <li>• <strong>Player Photo:</strong> Hands folded front photo</li>
                                <li>• <strong>Career Screenshot:</strong> In-game Career tab showing Riot ID, Tagline, and Rank icon</li>
                                <li>• <strong>Team Crest:</strong> Transparent PNG</li>
                            </ul>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-xs text-red-300 leading-relaxed font-mono-tech">
                        🛡️ <strong>Anti-Cheat Notice:</strong> Riot Vanguard must be active on all player PCs. Any Vanguard flag, script usage, or hardware tampering will result in permanent disqualification and blacklisting.
                    </div>
                </div>
            </div>

        </section>

        <!-- 5. COMPLIANCE & COMMON REJECTION REASONS -->
        <section id="compliance" class="space-y-6 scroll-mt-24">
            <div class="border-b border-white/10 pb-4">
                <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                    VERIFICATION STANDARDS
                </span>
                <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-white">
                    Compliance & Common Rejection Reasons
                </h2>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">Avoid delays in your squad approval by verifying your application against these essential checkpoints.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                
                <div class="guide-card rounded-2xl p-5 space-y-3 border-l-2 border-l-red-500">
                    <div class="font-display font-bold text-sm text-white flex items-center gap-2">
                        <span class="text-red-400">✕</span> Unclear Payment Screenshot
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed font-mono-tech">
                        Payment screenshots that crop out the <strong>Transaction ID</strong>, Reference Number, or Date/Time will be rejected. Always upload the full receipt from eSewa / Khalti.
                    </p>
                </div>

                <div class="guide-card rounded-2xl p-5 space-y-3 border-l-2 border-l-red-500">
                    <div class="font-display font-bold text-sm text-white flex items-center gap-2">
                        <span class="text-red-400">✕</span> Duplicate Player Roster
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed font-mono-tech">
                        A player cannot be registered in more than one team in the same game title. Any player detected on multiple rosters will have both teams put on hold.
                    </p>
                </div>

                <div class="guide-card rounded-2xl p-5 space-y-3 border-l-2 border-l-red-500">
                    <div class="font-display font-bold text-sm text-white flex items-center gap-2">
                        <span class="text-red-400">✕</span> Mismatched In-Game Handles
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed font-mono-tech">
                        The IGN and numeric ID submitted in the roster must match the player joining the match lobby. Unregistered stand-ins will forfeit the match.
                    </p>
                </div>

            </div>
        </section>

        <!-- 6. FREQUENTLY ASKED QUESTIONS -->
        <section class="space-y-6">
            <div class="border-b border-white/10 pb-4">
                <span class="text-xs font-mono-tech font-bold uppercase tracking-widest text-emerald-400 block mb-1">
                    HELP & SUPPORT
                </span>
                <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-white">
                    Frequently Asked Questions
                </h2>
            </div>

            <div class="space-y-3">
                <details class="guide-card rounded-xl p-4 text-xs font-mono-tech group cursor-pointer">
                    <summary class="font-display font-bold text-sm text-white flex items-center justify-between list-none">
                        <span>Can I register multiple teams under one manager account?</span>
                        <span class="text-emerald-400 text-lg transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-slate-300 leading-relaxed">
                        Yes! Team Managers can create multiple squads (e.g. one for PUBG Mobile and another for Valorant) under the same manager account and register each squad to its respective tournament discipline.
                    </p>
                </details>

                <details class="guide-card rounded-xl p-4 text-xs font-mono-tech group cursor-pointer">
                    <summary class="font-display font-bold text-sm text-white flex items-center justify-between list-none">
                        <span>Can I edit my roster after submitting registration?</span>
                        <span class="text-emerald-400 text-lg transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-slate-300 leading-relaxed">
                        While your application status is <strong>"Pending"</strong>, you can update your squad roster. Once the application is <strong>"Approved"</strong>, rosters are locked for bracket seeding. Any urgent roster changes must be requested through tournament admin on Discord.
                    </p>
                </details>

                <details class="guide-card rounded-xl p-4 text-xs font-mono-tech group cursor-pointer">
                    <summary class="font-display font-bold text-sm text-white flex items-center justify-between list-none">
                        <span>Where do we receive custom room IDs and match credentials?</span>
                        <span class="text-emerald-400 text-lg transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-slate-300 leading-relaxed">
                        Match room credentials and lobby passwords will be shared in the verified Team Captains channel on the official Outlaw Esports Nepal Discord server 15-30 minutes prior to scheduled match times.
                    </p>
                </details>
            </div>
        </section>

        <!-- 7. FINAL CALL TO ACTION -->
        <section class="guide-card rounded-3xl p-8 sm:p-12 text-center relative overflow-hidden border-emerald-500/30">
            <div class="max-w-2xl mx-auto space-y-4">
                <h3 class="font-display text-2xl sm:text-4xl font-black uppercase text-white">
                    Ready to Enter the Battlefield?
                </h3>
                <p class="text-slate-300 text-xs sm:text-sm">
                    Create your manager account today and secure your squad's place in Nepal's biggest esports showdown.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                    <a href="{{ url('/mukhyadwar/register') }}" class="px-8 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-black uppercase tracking-wider transition-all shadow-[0_0_25px_rgba(16,185,129,0.4)]">
                        Register Your Squad →
                    </a>
                    <a href="{{ url('/mukhyadwar/login') }}" class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/15 text-white text-xs font-bold transition-all">
                        Sign In To Manager Portal
                    </a>
                </div>
            </div>
        </section>

    </main>

    <!-- CLEAN FOOTER -->
    <footer class="border-t border-white/5 bg-[#020406] py-8 text-xs font-mono-tech text-slate-400 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5 text-center sm:text-left">
                <span class="font-display font-black text-white text-xs uppercase">{{ $tournamentName }}</span>
                <span class="text-slate-600">•</span>
                <span class="text-slate-400">© {{ date('Y') }} Outlaw Esports Nepal</span>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-5 text-[11px]">
                <a href="{{ url('/') }}" class="hover:text-emerald-400 transition-colors">Home</a>
                <a href="{{ url('/guide') }}" class="text-emerald-400 font-bold">Manager Guide</a>
                <a href="{{ url('/privacy-policy') }}" class="hover:text-emerald-400 transition-colors">Privacy</a>
                <a href="{{ url('/terms-of-service') }}" class="hover:text-emerald-400 transition-colors">Terms</a>
                <a href="{{ url('/mukhyadwar/login') }}" class="hover:text-emerald-400 transition-colors">Player Portal</a>
            </div>
        </div>
    </footer>

    <!-- INTERACTIVE TAB SWITCHING SCRIPT -->
    <script>
        function switchTab(disciplineId) {
            // Hide all tab panes
            const panes = document.querySelectorAll('.tab-pane');
            panes.forEach(pane => pane.classList.add('hidden'));

            // Deactivate all tab buttons
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('text-slate-400');
            });

            // Activate chosen tab pane & button
            const activePane = document.getElementById('tab-content-' + disciplineId);
            const activeBtn = document.getElementById('tab-btn-' + disciplineId);

            if (activePane) activePane.classList.remove('hidden');
            if (activeBtn) {
                activeBtn.classList.add('active');
                activeBtn.classList.remove('text-slate-400');
            }
        }
    </script>
</body>
</html>
