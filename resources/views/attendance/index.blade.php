<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Attendance Terminal &bull; {{ config('app.name', 'Showdown') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-slate-950 to-black p-4 sm:p-6 lg:p-8" x-data="staffAttendance()" x-init="initTerminal()">

    <!-- TOP NAVIGATION BAR -->
    <header class="max-w-xl mx-auto w-full flex items-center justify-between py-2 border-b border-slate-800/80 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-emerald-500 p-0.5 shadow-lg shadow-emerald-500/10 flex items-center justify-center">
                <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center font-black text-amber-400 text-lg">
                    ⚡
                </div>
            </div>
            <div>
                <h1 class="text-sm font-black uppercase tracking-wider text-white">Attendance Terminal</h1>
                <p class="text-[11px] text-slate-400 font-medium">{{ auth()->user()->name }} &bull; <span class="text-amber-400 font-bold uppercase">{{ auth()->user()->roles->first()?->name ?? 'Staff' }}</span></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button @click="showDevicesModal = true" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 hover:border-slate-600 text-xs font-semibold text-slate-300 flex items-center gap-1.5 transition cursor-pointer">
                <span>🔐</span>
                <span class="hidden sm:inline">Passkeys</span>
                <span class="px-1.5 py-0.2 bg-emerald-950 text-emerald-400 rounded text-[10px] font-bold border border-emerald-800">{{ count($credentials) }}</span>
            </button>
            <a href="{{ route('filament.maidan.pages.dashboard') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 hover:border-slate-600 text-xs font-semibold text-slate-300 flex items-center gap-1 transition">
                <span>Portal &rarr;</span>
            </a>
        </div>
    </header>

    <!-- MAIN TERMINAL CONTENT -->
    <main class="max-w-xl mx-auto w-full space-y-6 flex-1 flex flex-col justify-center">

        <!-- TIME & GREETING CARD -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-slate-900/90 to-slate-900/40 border border-slate-800 p-6 sm:p-8 backdrop-blur-xl text-center shadow-2xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700/60 text-xs font-bold text-slate-300 uppercase tracking-widest">
                <span class="w-2 h-2 rounded-full animate-ping" :class="isClockedIn ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                <span x-text="greetingText"></span>
            </div>

            <!-- DIGITAL CLOCK -->
            <div class="py-2">
                <div class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white font-mono" x-text="currentTime">
                    --:--:--
                </div>
                <div class="text-xs sm:text-sm font-semibold text-slate-400 mt-1 uppercase tracking-wider font-mono" x-text="todayDate">
                    {{ $status['today_date'] }}
                </div>
            </div>

            <!-- GEOFENCE LOCATION STATUS BADGE -->
            <div class="pt-2 border-t border-slate-800/80">
                <template x-if="isRemoteAllowed">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-sky-950/60 text-sky-400 border border-sky-800/60 text-xs font-bold">
                        <span>🏠</span>
                        <span>Remote Work Authorized &bull; Location-Exempt</span>
                    </div>
                </template>

                <template x-if="!isRemoteAllowed">
                    <div>
                        <template x-if="locationStatus === 'checking'">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 text-xs font-medium animate-pulse">
                                <span>📡</span>
                                <span>Verifying office location coordinates...</span>
                            </div>
                        </template>
                        <template x-if="locationStatus === 'inside'">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-950/80 text-emerald-400 border border-emerald-800 text-xs font-bold shadow-lg shadow-emerald-900/20">
                                <span>✓</span>
                                <span x-text="'Inside Office Geofence (' + currentDistance + 'm from HQ)'"></span>
                            </div>
                        </template>
                        <template x-if="locationStatus === 'outside'">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-rose-950/80 text-rose-400 border border-rose-800 text-xs font-bold shadow-lg shadow-rose-900/20">
                                <span>✕</span>
                                <span x-text="'Outside Office Geofence (' + currentDistance + 'm away &bull; Max ' + maxAllowedRadius + 'm)'"></span>
                            </div>
                        </template>
                        <template x-if="locationStatus === 'denied'">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-amber-950/80 text-amber-400 border border-amber-800 text-xs font-bold">
                                <span>⚠️</span>
                                <span>Location permission required. Please allow GPS.</span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <!-- ACTION BUTTON AREA -->
        <div class="space-y-4">

            <!-- NOT CLOCKED IN STATE -> PUNCH IN BUTTON -->
            <template x-if="!isClockedIn && !isCompleted">
                <button
                    @click="handlePunchIn()"
                    :disabled="isLoading || (!isRemoteAllowed && locationStatus === 'outside')"
                    class="w-full py-5 px-6 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 disabled:opacity-50 disabled:cursor-not-allowed text-white font-black text-lg uppercase tracking-wider shadow-xl shadow-emerald-950/50 flex items-center justify-center gap-3 transition-all transform active:scale-[0.99] cursor-pointer"
                >
                    <template x-if="isLoading">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Verifying & Recording...</span>
                        </div>
                    </template>
                    <template x-if="!isLoading">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">👉</span>
                            <span>PUNCH IN (START WORK)</span>
                        </div>
                    </template>
                </button>
            </template>

            <!-- CLOCKED IN STATE -> PUNCH OUT BUTTON WITH LIVE TIMER -->
            <template x-if="isClockedIn">
                <div class="space-y-3">
                    <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-800/80 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 block">Session Active</span>
                            <span class="text-xs text-slate-300 font-mono" x-text="'Clocked In: ' + punchInFormatted"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Worked Elapsed</span>
                            <span class="text-lg font-black text-white font-mono" x-text="sessionDuration">00:00:00</span>
                        </div>
                    </div>

                    <button
                        @click="handlePunchOut()"
                        :disabled="isLoading"
                        class="w-full py-5 px-6 rounded-2xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-500 hover:to-amber-500 disabled:opacity-50 text-white font-black text-lg uppercase tracking-wider shadow-xl shadow-rose-950/50 flex items-center justify-center gap-3 transition-all transform active:scale-[0.99] cursor-pointer"
                    >
                        <template x-if="isLoading">
                            <div class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Verifying & Clocking Out...</span>
                            </div>
                        </template>
                        <template x-if="!isLoading">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">⏹️</span>
                                <span>PUNCH OUT (END WORK)</span>
                            </div>
                        </template>
                    </button>
                </div>
            </template>

            <!-- COMPLETED STATE -->
            <template x-if="isCompleted">
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-emerald-950 border border-emerald-800 text-emerald-400 text-2xl mx-auto flex items-center justify-center">
                        ✓
                    </div>
                    <h3 class="text-base font-black text-white uppercase tracking-wider">Attendance Completed For Today</h3>
                    <p class="text-xs text-slate-400" x-text="'Clocked In: ' + punchInFormatted + ' • Clocked Out: ' + punchOutFormatted + ' • Total: ' + workedDurationText"></p>
                </div>
            </template>

            <!-- FEEDBACK BANNER -->
            <template x-if="feedbackMessage">
                <div class="p-4 rounded-xl text-xs font-semibold flex items-center gap-2 border" :class="feedbackType === 'success' ? 'bg-emerald-950/90 text-emerald-300 border-emerald-800' : 'bg-rose-950/90 text-rose-300 border-rose-800'">
                    <span x-text="feedbackType === 'success' ? '✓' : '⚠️'"></span>
                    <span x-text="feedbackMessage"></span>
                </div>
            </template>

        </div>

        <!-- TODAY'S ATTENDANCE STATS CARD -->
        <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-5 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                <span>Today's Time Card</span>
                <span class="text-[10px] font-mono text-slate-500">SERVER VERIFIED</span>
            </h3>

            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Punch In</span>
                    <span class="text-sm font-bold text-white font-mono" x-text="punchInFormatted || '—'"></span>
                </div>
                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Punch Out</span>
                    <span class="text-sm font-bold text-white font-mono" x-text="punchOutFormatted || '—'"></span>
                </div>
                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Duration</span>
                    <span class="text-sm font-bold text-emerald-400 font-mono" x-text="workedDurationText || '—'"></span>
                </div>
            </div>
        </div>

        <!-- RECENT ATTENDANCE HISTORY -->
        <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-5 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                <span>Recent History</span>
                <span class="text-[10px] text-slate-500">Past 10 Days</span>
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-[10px] text-slate-500 uppercase font-mono">
                            <th class="pb-2">Date</th>
                            <th class="pb-2">In</th>
                            <th class="pb-2">Out</th>
                            <th class="pb-2 text-right">Worked</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($recentAttendances as $att)
                            <tr>
                                <td class="py-2 text-slate-300">{{ $att->date->format('M d, D') }}</td>
                                <td class="py-2 text-slate-400">{{ $att->punch_in_at ? $att->punch_in_at->format('h:i A') : '—' }}</td>
                                <td class="py-2 text-slate-400">{{ $att->punch_out_at ? $att->punch_out_at->format('h:i A') : '—' }}</td>
                                <td class="py-2 text-right font-bold text-emerald-400">{{ $att->formatted_worked_time }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-600 font-sans">No recent attendance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- BIOMETRIC PASSEYS MODAL -->
    <div x-show="showDevicesModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🔐</span>
                    <h3 class="text-sm font-black uppercase tracking-wider text-white">Biometric Passkeys</h3>
                </div>
                <button @click="showDevicesModal = false" class="text-slate-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <!-- REGISTER DEVICE FORM -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800/80 space-y-3">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Register New Device</label>
                <div class="flex gap-2">
                    <input type="text" x-model="newDeviceName" placeholder="e.g. MacBook Touch ID, iPhone" class="flex-1 px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
                    <button @click="registerPasskey()" :disabled="isRegistering" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-slate-950 text-xs font-black rounded-xl transition cursor-pointer flex items-center gap-1">
                        <span>+ Register</span>
                    </button>
                </div>
                <p class="text-[10px] text-slate-500">Uses standard WebAuthn biometric (Fingerprint, Face ID, Windows Hello). Raw biometrics are never stored.</p>
            </div>

            <!-- REGISTERED DEVICES LIST -->
            <div class="space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Active Registered Devices ({{ count($credentials) }}/3)</span>
                <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                    @forelse($credentials as $cred)
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span>💻</span>
                                <div>
                                    <span class="font-bold text-white block">{{ $cred->name }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono">Last used: {{ $cred->last_used_at ? $cred->last_used_at->diffForHumans() : 'Never' }}</span>
                                </div>
                            </div>
                            <button @click="revokeDevice({{ $cred->id }})" class="text-[10px] text-rose-400 hover:text-rose-300 font-bold px-2 py-1 rounded bg-rose-950/60 border border-rose-800/60 cursor-pointer">
                                Revoke
                            </button>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-slate-600">No biometric passkeys registered yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="pt-2 border-t border-slate-800 text-right">
                <button @click="showDevicesModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- CLIENT SCRIPT -->
    <script>
        function staffAttendance() {
            return {
                currentTime: '',
                todayDate: '{{ $status["today_date"] }}',
                greetingText: 'Good Day, {{ auth()->user()->name }}',
                isClockedIn: {{ $status['is_clocked_in'] ? 'true' : 'false' }},
                isCompleted: {{ $status['is_completed'] ? 'true' : 'false' }},
                punchInFormatted: '{{ $status["today_attendance"]?->punch_in_at ? $status["today_attendance"]->punch_in_at->format("h:i A") : "" }}',
                punchOutFormatted: '{{ $status["today_attendance"]?->punch_out_at ? $status["today_attendance"]->punch_out_at->format("h:i A") : "" }}',
                workedDurationText: '{{ $status["today_attendance"] ? $status["today_attendance"]->formatted_worked_time : "" }}',
                punchInIso: '{{ $status["today_attendance"]?->punch_in_at ? $status["today_attendance"]->punch_in_at->toIso8601String() : "" }}',
                sessionDuration: '00:00:00',
                isRemoteAllowed: {{ $status['is_remote_allowed'] ? 'true' : 'false' }},
                officeLat: {{ $status['office_latitude'] }},
                officeLon: {{ $status['office_longitude'] }},
                maxAllowedRadius: {{ $status['allowed_radius_meters'] }},
                maxGpsAccuracy: {{ $status['max_gps_accuracy_meters'] }},

                locationStatus: 'checking', // checking, inside, outside, denied
                userLat: null,
                userLon: null,
                userAccuracy: null,
                currentDistance: null,

                isLoading: false,
                feedbackMessage: '',
                feedbackType: 'success',

                showDevicesModal: false,
                newDeviceName: '',
                isRegistering: false,

                initTerminal() {
                    this.updateLiveTime();
                    setInterval(() => this.updateLiveTime(), 1000);

                    if (this.isClockedIn && this.punchInIso) {
                        this.updateSessionDuration();
                        setInterval(() => this.updateSessionDuration(), 1000);
                    }

                    this.detectLocation();
                },

                updateLiveTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });

                    const hour = now.getHours();
                    if (hour < 12) this.greetingText = 'Good Morning, {{ auth()->user()->name }}';
                    else if (hour < 17) this.greetingText = 'Good Afternoon, {{ auth()->user()->name }}';
                    else this.greetingText = 'Good Evening, {{ auth()->user()->name }}';
                },

                updateSessionDuration() {
                    if (!this.punchInIso || !this.isClockedIn) return;
                    const start = new Date(this.punchInIso);
                    const now = new Date();
                    const diffMs = Math.max(0, now - start);
                    const totalSec = Math.floor(diffMs / 1000);

                    const h = Math.floor(totalSec / 3600).toString().padStart(2, '0');
                    const m = Math.floor((totalSec % 3600) / 60).toString().padStart(2, '0');
                    const s = (totalSec % 60).toString().padStart(2, '0');

                    this.sessionDuration = `${h}:${m}:${s}`;
                },

                detectLocation() {
                    if (this.isRemoteAllowed) {
                        this.locationStatus = 'inside';
                        return;
                    }

                    if (!navigator.geolocation) {
                        this.locationStatus = 'denied';
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            this.userLat = pos.coords.latitude;
                            this.userLon = pos.coords.longitude;
                            this.userAccuracy = pos.coords.accuracy;

                            this.currentDistance = this.computeDistance(this.userLat, this.userLon, this.officeLat, this.officeLon);

                            if (this.currentDistance <= this.maxAllowedRadius) {
                                this.locationStatus = 'inside';
                            } else {
                                this.locationStatus = 'outside';
                            }
                        },
                        (err) => {
                            console.warn('Geolocation error:', err);
                            this.locationStatus = 'denied';
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                },

                computeDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371000;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                              Math.sin(dLon/2) * Math.sin(dLon/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return Math.round(R * c);
                },

                async handlePunchIn() {
                    this.isLoading = true;
                    this.feedbackMessage = '';

                    if (!this.isRemoteAllowed && (!this.userLat || !this.userLon)) {
                        try {
                            const pos = await new Promise((resolve, reject) => {
                                if (!navigator.geolocation) return reject(new Error('Geolocation not supported'));
                                navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 8000 });
                            });
                            this.userLat = pos.coords.latitude;
                            this.userLon = pos.coords.longitude;
                            this.userAccuracy = pos.coords.accuracy;
                            this.currentDistance = this.computeDistance(this.userLat, this.userLon, this.officeLat, this.officeLon);
                            this.locationStatus = this.currentDistance <= this.maxAllowedRadius ? 'inside' : 'outside';
                        } catch (geoErr) {
                            console.warn('Geolocation prompt failed or denied:', geoErr);
                        }
                    }

                    let webauthnResponse = null;
                    if (window.PublicKeyCredential && {{ $status['active_devices_count'] }} > 0) {
                        try {
                            webauthnResponse = await this.performWebAuthnAuth();
                        } catch (e) {
                            console.warn('Biometric auth skipped or cancelled:', e);
                        }
                    }

                    try {
                        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch('{{ route("attendance.punch-in") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                            body: JSON.stringify({
                                latitude: this.userLat,
                                longitude: this.userLon,
                                accuracy: this.userAccuracy,
                                webauthn_response: webauthnResponse,
                            })
                        });

                        const data = await res.json();

                        if (res.ok && data.success) {
                            this.isClockedIn = true;
                            this.punchInFormatted = data.punch_in_time;
                            this.punchInIso = new Date().toISOString();
                            this.feedbackType = 'success';
                            this.feedbackMessage = data.message;
                            this.updateSessionDuration();
                            setInterval(() => this.updateSessionDuration(), 1000);
                        } else {
                            this.feedbackType = 'error';
                            this.feedbackMessage = data.message || 'Unable to punch in.';
                        }
                    } catch (e) {
                        this.feedbackType = 'error';
                        this.feedbackMessage = 'Network error. Please try again.';
                    } finally {
                        this.isLoading = false;
                    }
                },

                async handlePunchOut() {
                    this.isLoading = true;
                    this.feedbackMessage = '';

                    if (!this.isRemoteAllowed && (!this.userLat || !this.userLon)) {
                        try {
                            const pos = await new Promise((resolve, reject) => {
                                if (!navigator.geolocation) return reject(new Error('Geolocation not supported'));
                                navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 8000 });
                            });
                            this.userLat = pos.coords.latitude;
                            this.userLon = pos.coords.longitude;
                            this.userAccuracy = pos.coords.accuracy;
                        } catch (geoErr) {
                            console.warn('Geolocation prompt failed or denied:', geoErr);
                        }
                    }

                    let webauthnResponse = null;
                    if (window.PublicKeyCredential && {{ $status['active_devices_count'] }} > 0) {
                        try {
                            webauthnResponse = await this.performWebAuthnAuth();
                        } catch (e) {
                            console.warn('Biometric auth skipped or cancelled:', e);
                        }
                    }

                    try {
                        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch('{{ route("attendance.punch-out") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                            body: JSON.stringify({
                                latitude: this.userLat,
                                longitude: this.userLon,
                                accuracy: this.userAccuracy,
                                webauthn_response: webauthnResponse,
                            })
                        });

                        const data = await res.json();

                        if (res.ok && data.success) {
                            this.isClockedIn = false;
                            this.isCompleted = true;
                            this.punchOutFormatted = data.punch_out_time;
                            this.workedDurationText = data.worked_time;
                            this.feedbackType = 'success';
                            this.feedbackMessage = data.message;
                        } else {
                            this.feedbackType = 'error';
                            this.feedbackMessage = data.message || 'Unable to punch out.';
                        }
                    } catch (e) {
                        this.feedbackType = 'error';
                        this.feedbackMessage = 'Network error. Please try again.';
                    } finally {
                        this.isLoading = false;
                    }
                },

                async performWebAuthnAuth() {
                    const res = await fetch('{{ route("attendance.webauthn.auth.options") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                    });
                    const options = await res.json();
                    if (!options.challenge) return null;

                    options.challenge = this.bufferDecode(options.challenge);
                    if (options.allowCredentials) {
                        options.allowCredentials = options.allowCredentials.map(c => ({
                            ...c,
                            id: this.bufferDecode(c.id)
                        }));
                    }

                    const assertion = await navigator.credentials.get({ publicKey: options });
                    return {
                        id: assertion.id,
                        rawId: this.bufferEncode(assertion.rawId),
                        type: assertion.type,
                        clientDataJSON: this.bufferEncode(assertion.response.clientDataJSON),
                        authenticatorData: this.bufferEncode(assertion.response.authenticatorData),
                        signature: this.bufferEncode(assertion.response.signature),
                        userHandle: assertion.response.userHandle ? this.bufferEncode(assertion.response.userHandle) : null,
                    };
                },

                async registerPasskey() {
                    if (!window.PublicKeyCredential) {
                        alert('Your browser or device does not support WebAuthn biometrics / passkeys.');
                        return;
                    }

                    this.isRegistering = true;
                    try {
                        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch('{{ route("attendance.webauthn.register.options") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf }
                        });
                        const options = await res.json();

                        options.challenge = this.bufferDecode(options.challenge);
                        options.user.id = this.bufferDecode(options.user.id);
                        if (options.excludeCredentials) {
                            options.excludeCredentials = options.excludeCredentials.map(c => ({
                                ...c,
                                id: this.bufferDecode(c.id)
                            }));
                        }

                        const credential = await navigator.credentials.create({ publicKey: options });
                        const payload = {
                            device_name: this.newDeviceName || 'Biometric Passkey',
                            response: {
                                id: credential.id,
                                rawId: this.bufferEncode(credential.rawId),
                                type: credential.type,
                                clientDataJSON: this.bufferEncode(credential.response.clientDataJSON),
                                attestationObject: this.bufferEncode(credential.response.attestationObject),
                                transports: credential.response.getTransports ? credential.response.getTransports() : ['internal'],
                            }
                        };

                        const verifyRes = await fetch('{{ route("attendance.webauthn.register.verify") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                            body: JSON.stringify(payload)
                        });

                        const verifyData = await verifyRes.json();
                        if (verifyRes.ok && verifyData.success) {
                            alert('Device registered successfully!');
                            window.location.reload();
                        } else {
                            alert(verifyData.message || 'Device registration failed.');
                        }
                    } catch (e) {
                        console.error('Registration failed:', e);
                        alert('Registration cancelled or failed: ' + e.message);
                    } finally {
                        this.isRegistering = false;
                    }
                },

                async revokeDevice(id) {
                    if (!confirm('Are you sure you want to deactivate this device?')) return;
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch(`/attendance/devices/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf }
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to revoke device.');
                    }
                },

                bufferDecode(value) {
                    return Uint8Array.from(atob(value.replace(/-/g, "+").replace(/_/g, "/")), c => c.charCodeAt(0));
                },

                bufferEncode(value) {
                    return btoa(String.fromCharCode.apply(null, new Uint8Array(value)))
                        .replace(/\+/g, "-").replace(/\//g, "_").replace(/=/g, "");
                }
            };
        }
    </script>
</body>
</html>
