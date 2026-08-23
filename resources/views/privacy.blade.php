<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Privacy Policy — {{ config('app.name', 'Showdown Esports Hub') }}</title>
    <meta name="description" content="Privacy policy, data protection, and Google API user data disclosures for Showdown.">

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
                <a href="{{ url('/terms-of-service') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold tracking-wider uppercase hover:text-white transition-colors">
                    Terms of Service
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
                    <span>Legal & Data Protection</span>
                </div>
                <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-black uppercase text-white tracking-tight">
                    Privacy Policy
                </h1>
                <p class="text-xs sm:text-sm font-mono-tech text-slate-400 mt-2">
                    Last Updated: {{ date('F d, Y') }} • Effective Date: January 1, 2026
                </p>
            </div>

            <!-- LEGAL CONTENT SECTIONS -->
            <div class="space-y-8 text-sm sm:text-base leading-relaxed text-slate-300">
                
                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">01.</span> Introduction
                    </h2>
                    <p>
                        Welcome to <strong>{{ config('app.name', 'Showdown') }}</strong> ("we," "our," or "us"). We are committed to safeguarding your personal data and ensuring transparency in how we collect, process, and protect your information when you access our competitive esports platform, website, tournament portals, and connected services.
                    </p>
                </section>

                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">02.</span> Information We Collect
                    </h2>
                    <p>We may collect and process the following categories of data:</p>
                    <ul class="list-disc list-inside space-y-2 pl-2 text-slate-300">
                        <li><strong>Account & Player Information:</strong> Name, in-game name (IGN), email address, contact phone number, team registrations, and avatar images.</li>
                        <li><strong>Tournament & Sponsorship Data:</strong> Team rosters, partner and sponsor inquiries, match results, ticket purchases, and billing receipt confirmations.</li>
                        <li><strong>Staff & Administrative Logs:</strong> Attendance records, authentication events, and administrative notes.</li>
                        <li><strong>Technical Data:</strong> IP address, device specifications, browser type, and interaction cookies.</li>
                    </ul>
                </section>

                <!-- GOOGLE API DISCLOSURE (CRITICAL FOR GOOGLE OAUTH VERIFICATION) -->
                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4 border-emerald-500/30 bg-emerald-500/5">
                    <div class="flex items-center gap-2.5 text-emerald-400 font-mono-tech text-xs uppercase font-bold tracking-wider">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Google API Services User Data Policy Compliance</span>
                    </div>
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white">
                        03. Google Calendar & OAuth Permissions
                    </h2>
                    <p>
                        Our platform provides optional Google Calendar synchronization for authenticated administrators and staff members to schedule meetings with sponsors, partners, and tournament leads.
                    </p>
                    <div class="space-y-3 pt-2 text-xs sm:text-sm font-mono-tech bg-[#05070a] p-4 rounded-lg border border-white/5">
                        <p><strong>Specific Scopes Requested:</strong> <code class="text-emerald-400">https://www.googleapis.com/auth/calendar.events</code>, <code class="text-emerald-400">userinfo.email</code>, <code class="text-emerald-400">openid</code>.</p>
                        <p><strong>Usage:</strong> Google account access is strictly used to create, synchronize, update, and manage scheduled lead meetings and generate Google Meet conference links on the user's primary calendar.</p>
                        <p><strong>Storage & Sharing:</strong> We securely store OAuth access/refresh tokens in encrypted database records associated with the user's staff account. We <strong>never</strong> sell, share, transfer, or use Google user data for advertising, external profiling, or training artificial intelligence models.</p>
                        <p><strong>Revocation:</strong> Users can disconnect their Google account at any time via the Lead Management portal or through their Google Account security settings.</p>
                    </div>
                </section>

                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">04.</span> How We Use Your Information
                    </h2>
                    <p>We use collected information solely for legitimate operational purposes:</p>
                    <ul class="list-disc list-inside space-y-2 pl-2 text-slate-300">
                        <li>Facilitating tournament registrations, bracket organization, and prize pool distribution.</li>
                        <li>Verifying attendee ticketing, check-in barcodes, and official credentials.</li>
                        <li>Managing client, sponsor, and partner relationships.</li>
                        <li>Ensuring fair play, security, and compliance with competitive esports rules.</li>
                    </ul>
                </section>

                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">05.</span> Data Security & Retention
                    </h2>
                    <p>
                        We employ enterprise-grade security practices, encrypted connections (TLS/SSL), role-based access control, and secure token storage. Personal data is retained only for as long as necessary to fulfill tournament operations, regulatory compliance, and account maintenance.
                    </p>
                </section>

                <section class="editorial-card rounded-xl p-6 sm:p-8 space-y-4">
                    <h2 class="font-display text-lg sm:text-xl font-bold uppercase text-white flex items-center gap-2">
                        <span class="text-emerald-400 font-mono-tech text-base">06.</span> Contact & Inquiries
                    </h2>
                    <p>
                        If you have questions regarding this Privacy Policy, your personal data, or wish to request data erasure, please reach out to our privacy administration:
                    </p>
                    <div class="p-4 rounded-lg bg-[#05070a] border border-white/5 font-mono-tech text-xs sm:text-sm space-y-1">
                        <p><strong>Email:</strong> privacy@showdown.gg / contact@outlawesports.com</p>
                        <p><strong>Organization:</strong> {{ config('app.name', 'Showdown Esports Hub') }}</p>
                    </div>
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
                <a href="{{ url('/privacy-policy') }}" class="text-white hover:underline">Privacy Policy</a>
                <a href="{{ url('/terms-of-service') }}" class="hover:text-slate-300">Terms of Service</a>
                <a href="{{ url('/mukhyadwar/login') }}" class="hover:text-slate-300">Player Portal</a>
            </div>
        </div>
    </footer>

</body>
</html>
