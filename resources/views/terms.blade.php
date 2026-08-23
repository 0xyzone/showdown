<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Terms of Service — {{ config('app.name', 'Showdown Esports Hub') }}</title>
    <meta name="description" content="Terms of service, tournament participation guidelines, and rules for Showdown.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #10b981;
            --primary-rgb: 16, 185, 129;
            --text-on-primary: #ffffff;
            --primary-badge-text: #10b981;
        }
    </style>
</head>
<body class="editorial-bg min-h-screen antialiased flex flex-col justify-between selection:bg-slate-800 text-slate-300">

    <!-- HEADER / NAVIGATION -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-[#05070a]/90 border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-16 sm:h-20 flex items-center justify-between gap-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 sm:gap-3.5 group">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg btn-primary-action flex items-center justify-center font-display font-black text-xs sm:text-sm shrink-0">
                    SD
                </div>
                <div>
                    <span class="font-display font-black text-xs sm:text-base lg:text-lg tracking-wider text-white block uppercase">
                        {{ config('app.name', 'SHOWDOWN') }}
                    </span>
                    <span class="text-[9px] sm:text-[10px] font-mono-tech tracking-widest text-slate-400 block uppercase">
                        Esports & Tournament Arena
                    </span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold tracking-wider uppercase btn-secondary-action">
                    ← Back to Home
                </a>
                <a href="{{ url('/privacy-policy') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold tracking-wider uppercase hover:text-white transition-colors">
                    Privacy Policy
                </a>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="py-12 sm:py-16 flex-grow">
        <div class="max-w-4xl mx-auto px-4 sm:px-8 space-y-10">
            
            <!-- TITLE HEADER -->
            <div class="border-b border-white/10 pb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded text-xs font-mono-tech uppercase font-bold status-pill mb-4">
                    <span>Platform Rules & Guidelines</span>
                </div>
                <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-black uppercase text-white tracking-tight">
                    Terms of Service
                </h1>
                <p class="text-xs sm:text-sm font-mono-tech text-slate-400 mt-2">
                    Last Updated: {{ date('F d, Y') }} • Effective Date: January 1, 2026
                </p>
            </div>

            <!-- LEGAL CONTENT SECTIONS -->
            <div class="space-y-8 text-sm sm:text-base leading-relaxed text-slate-300">
                
                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">01.</span> Acceptance of Terms
                    </h2>
                    <p>
                        By accessing, browsing, registering for an account, purchasing tournament tickets, or competing in any events organized by <strong>{{ config('app.name', 'Showdown') }}</strong>, you agree to be legally bound by these Terms of Service, along with all applicable tournament rulebooks and our Privacy Policy.
                    </p>
                </section>

                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">02.</span> Eligibility & Player Accounts
                    </h2>
                    <ul class="list-disc list-inside space-y-2 pl-2 text-slate-300">
                        <li><strong>Registration:</strong> Participants must provide accurate, current, and verifiable information during team/player registration.</li>
                        <li><strong>Age Requirements:</strong> Participants under the age of majority must have parental or guardian consent where required by tournament guidelines.</li>
                        <li><strong>Account Security:</strong> You are solely responsible for maintaining the confidentiality of your login credentials and for all activities that occur under your account.</li>
                    </ul>
                </section>

                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">03.</span> Competitive Integrity & Code of Conduct
                    </h2>
                    <p>Fair play is the core foundation of our esports circuits. The following are strictly prohibited and will result in immediate disqualification, ban, and forfeiture of prize claims:</p>
                    <ul class="list-disc list-inside space-y-2 pl-2 text-slate-300">
                        <li>Use of unauthorized cheats, hacks, automated macros, exploits, or third-party assistive software.</li>
                        <li>Smurfing, account sharing, or playing under false identities / non-registered player rosters.</li>
                        <li>Match-fixing, collusion, intentional disconnects, or unsportsmanlike toxicity toward staff or competitors.</li>
                    </ul>
                </section>

                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">04.</span> Entry Fees, Ticketing & Prize Distributions
                    </h2>
                    <ul class="list-disc list-inside space-y-2 pl-2 text-slate-300">
                        <li><strong>Entry Fees & Passes:</strong> Tournament entry fees and spectator tickets are non-refundable once confirmed, except in the event of tournament cancellation by the organizers.</li>
                        <li><strong>Prize Distribution:</strong> Prize payouts will be processed to the verified team manager or individual contenders according to the published tournament prize distribution and tax regulations.</li>
                    </ul>
                </section>

                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">05.</span> Intellectual Property & Media Rights
                    </h2>
                    <p>
                        All tournament logos, website designs, branding, and broadcast streams are the property of {{ config('app.name', 'Showdown') }} or their respective owners. By participating in streamed tournaments, teams and players grant permission for match broadcasts, photos, and roster names to be used for promotional and coverage purposes.
                    </p>
                </section>

                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">06.</span> Limitation of Liability & Termination
                    </h2>
                    <p>
                        {{ config('app.name', 'Showdown') }} shall not be held liable for indirect, incidental, or third-party platform server outages (such as game publisher downtime). We reserve the right to suspend or terminate accounts violating these Terms at our sole discretion.
                    </p>
                </section>

            </div>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="border-t border-white/5 bg-[#030508] py-8 text-xs font-mono-tech text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="font-display font-bold text-white text-sm uppercase">{{ config('app.name', 'SHOWDOWN') }}</span>
                <span>•</span>
                <span>© {{ date('Y') }} All rights reserved</span>
            </div>

            <div class="flex items-center gap-6 text-[11px]">
                <a href="{{ url('/privacy-policy') }}" class="hover:text-slate-300">Privacy Policy</a>
                <a href="{{ url('/terms-of-service') }}" class="text-white hover:underline">Terms of Service</a>
                <a href="{{ url('/mukhyadwar/login') }}" class="hover:text-slate-300">Player Portal</a>
            </div>
        </div>
    </footer>

</body>
</html>
