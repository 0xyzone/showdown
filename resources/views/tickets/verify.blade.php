<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Event Gate — Ticket Verification</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- HTML5 QR Code Camera Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        body {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            background-color: #030712;
            color: #f3f4f6;
        }
        .font-display {
            font-family: 'Orbitron', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between p-4 sm:p-6 select-none">

    <!-- HEADER -->
    <header class="max-w-md mx-auto w-full flex items-center justify-between pb-4 border-b border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center font-display font-black text-emerald-400 text-sm">
                GT
            </div>
            <div>
                <h1 class="font-display font-bold text-sm text-white uppercase tracking-wider">Event Gate Verification</h1>
                <span class="text-[11px] text-gray-400">Staff Check-In Terminal</span>
            </div>
        </div>

        @if($user)
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-emerald-400 bg-emerald-950/60 border border-emerald-500/30 px-2.5 py-1 rounded-full">
                    {{ $user->name }}
                </span>
            </div>
        @else
            <a href="{{ url('/maidan/login') }}" class="text-xs font-bold text-amber-400 bg-amber-950/50 border border-amber-500/30 px-3 py-1.5 rounded-lg">
                Staff Login
            </a>
        @endif
    </header>

    <!-- MAIN INTERACTION AREA -->
    <main class="max-w-md mx-auto w-full my-auto py-6 space-y-6">

        <!-- SCANNER ACTIVATOR & CAMERA VIEW -->
        <div class="bg-gray-900/90 border border-gray-800 rounded-2xl p-5 shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Scanner Engine</span>
                <button id="toggle-camera-btn" onclick="toggleCameraScanner()" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-500 text-gray-950 hover:bg-emerald-400 transition-all flex items-center gap-1.5 cursor-pointer">
                    <span id="cam-icon">📷</span>
                    <span id="cam-btn-text">Open Camera Scanner</span>
                </button>
            </div>

            <!-- CAMERA READER CONTAINER -->
            <div id="reader-container" class="hidden overflow-hidden rounded-xl border-2 border-emerald-500/50 bg-black aspect-square relative">
                <div id="qr-reader" class="w-full h-full"></div>
            </div>

            <!-- MANUAL TOKEN / TICKET INPUT -->
            <form action="{{ url('/ticket/verify') }}" method="GET" onsubmit="event.preventDefault(); handleManualLookup();" class="flex gap-2">
                <input type="text" id="manual-token-input" placeholder="Paste Token / Scan Barcode..." value="{{ $token ?? '' }}" class="flex-1 px-4 py-3 rounded-xl bg-gray-950 border border-gray-700 text-white text-xs font-mono focus:outline-none focus:border-emerald-500">
                <button type="submit" class="px-4 py-3 rounded-xl bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold uppercase transition-colors cursor-pointer">
                    Lookup
                </button>
            </form>
        </div>

        <!-- TICKET VERIFICATION RESULT CARD -->
        <div id="result-card-container">
            @if(isset($ticket) && $ticket)
                @php
                    $isUsed = $ticket->is_used || $ticket->status === 'used';
                    $isCancelled = $ticket->status === 'cancelled';
                @endphp

                <div class="rounded-2xl p-6 border shadow-2xl space-y-5 transition-all {{ $isUsed ? 'bg-amber-950/40 border-amber-500/40' : ($isCancelled ? 'bg-red-950/40 border-red-500/40' : 'bg-emerald-950/40 border-emerald-500/50') }}">
                    
                    <!-- STATUS BANNER -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ $isUsed ? '⚠️' : ($isCancelled ? '❌' : '✅') }}</span>
                            <div>
                                <h2 class="font-display font-black text-lg tracking-wide uppercase {{ $isUsed ? 'text-amber-400' : ($isCancelled ? 'text-red-400' : 'text-emerald-400') }}">
                                    {{ $isUsed ? 'ALREADY USED' : ($isCancelled ? 'TICKET CANCELLED' : 'VALID ADMISSION TICKET') }}
                                </h2>
                                <span class="text-[11px] font-mono text-gray-400">{{ $ticket->ticket_number }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- TICKET DETAILS GRID -->
                    <div class="bg-gray-950/80 rounded-xl p-4 border border-gray-800/80 space-y-3 text-xs">
                        <div class="flex justify-between border-b border-gray-800 pb-2">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Tournament:</span>
                            <span class="font-bold text-white text-right">{{ $ticket->tournament->name ?? 'Esports Event' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800 pb-2">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Attendee Name:</span>
                            <span class="font-bold text-white text-right">{{ $ticket->customer_name }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800 pb-2">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Customer Phone:</span>
                            <span class="font-mono text-gray-300 text-right">{{ $ticket->customer_phone }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800 pb-2">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Price:</span>
                            <span class="font-bold text-emerald-400 text-right">Rs. {{ number_format($ticket->price, 2) }}</span>
                        </div>

                        @if($isUsed)
                            <div class="flex justify-between border-b border-gray-800 pb-2">
                                <span class="text-amber-400 font-bold uppercase text-[10px]">Checked In At:</span>
                                <span class="font-mono text-amber-300 text-right">{{ $ticket->used_at ? $ticket->used_at->format('M d, Y • h:i:s A') : 'Recorded' }}</span>
                            </div>
                            @if($ticket->verifiedBy)
                                <div class="flex justify-between">
                                    <span class="text-gray-400 font-bold uppercase text-[10px]">Verified By:</span>
                                    <span class="font-bold text-gray-300 text-right">{{ $ticket->verifiedBy->name }}</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- ACTION BUTTON -->
                    @if(! $isUsed && ! $isCancelled)
                        @if($user)
                            <button id="checkin-btn" onclick="executeCheckIn('{{ $ticket->verification_token }}')" class="w-full py-4 rounded-xl font-display font-black text-sm uppercase tracking-wider bg-emerald-500 hover:bg-emerald-400 text-gray-950 shadow-[0_0_25px_rgba(16,185,129,0.5)] transition-all cursor-pointer">
                                MARK AS ATTENDED (CHECK IN)
                            </button>
                        @else
                            <div class="p-3 bg-amber-950/60 border border-amber-500/30 rounded-xl text-center text-xs text-amber-300">
                                Staff login required to record attendance. <a href="{{ url('/maidan/login') }}" class="font-bold underline ml-1">Login here</a>
                            </div>
                        @endif
                    @elseif($isUsed)
                        <div class="p-3.5 bg-amber-500/10 border border-amber-500/30 rounded-xl text-center font-bold text-xs text-amber-400 uppercase">
                            Double Entry Prevented &bull; Ticket Voided
                        </div>
                    @endif

                </div>
            @elseif(isset($token) && !empty($token))
                <!-- INVALID TICKET STATE -->
                <div class="rounded-2xl p-6 bg-red-950/40 border border-red-500/50 shadow-2xl text-center space-y-4">
                    <div class="text-4xl">🚫</div>
                    <h2 class="font-display font-black text-lg uppercase text-red-400">INVALID TICKET</h2>
                    <p class="text-xs text-gray-300">No matching admission record found for this token. Please request original PDF from attendee.</p>
                </div>
            @else
                <!-- IDLE / READY STATE -->
                <div class="rounded-2xl p-8 bg-gray-900/40 border border-gray-800 text-center space-y-3">
                    <div class="text-3xl opacity-60">🎟️</div>
                    <div class="font-display font-bold text-xs uppercase tracking-widest text-gray-400">Terminal Ready</div>
                    <p class="text-xs text-gray-500 max-w-xs mx-auto">Scan QR code using camera or physical laser scanner to begin verification.</p>
                </div>
            @endif
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="max-w-md mx-auto w-full pt-4 border-t border-gray-800 text-center text-[11px] text-gray-500">
        Showdown Esports Admission &bull; High Security Access Control
    </footer>

    <!-- INTERACTION LOGIC -->
    <script>
        let html5QrCode = null;
        let isScannerRunning = false;

        function toggleCameraScanner() {
            const container = document.getElementById('reader-container');
            const btnText = document.getElementById('cam-btn-text');

            if (!isScannerRunning) {
                container.classList.remove('hidden');
                btnText.innerText = 'Stop Scanner';
                startQrScanner();
            } else {
                stopQrScanner();
                container.classList.add('hidden');
                btnText.innerText = 'Open Camera Scanner';
            }
        }

        function startQrScanner() {
            html5QrCode = new Html5Qrcode("qr-reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanError)
                .then(() => { isScannerRunning = true; })
                .catch(err => {
                    console.error("Camera access failed:", err);
                    alert("Unable to open camera. Please ensure camera permissions are granted or use manual token lookup.");
                    stopQrScanner();
                    document.getElementById('reader-container').classList.add('hidden');
                    document.getElementById('cam-btn-text').innerText = 'Open Camera Scanner';
                });
        }

        function stopQrScanner() {
            if (html5QrCode && isScannerRunning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    isScannerRunning = false;
                }).catch(err => console.error(err));
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            stopQrScanner();
            document.getElementById('reader-container').classList.add('hidden');
            document.getElementById('cam-btn-text').innerText = 'Open Camera Scanner';

            // Extract token if decodedText is a URL, e.g. http://domain.com/ticket/verify/UUID
            let token = decodedText;
            if (decodedText.includes('/ticket/verify/')) {
                const parts = decodedText.split('/ticket/verify/');
                token = parts[1].split('?')[0].split('#')[0];
            }

            window.location.href = `/ticket/verify/${token}`;
        }

        function onScanError(errorMessage) {
            // silent retry
        }

        function handleManualLookup() {
            let inputVal = document.getElementById('manual-token-input').value.trim();
            if (!inputVal) return;

            if (inputVal.includes('/ticket/verify/')) {
                const parts = inputVal.split('/ticket/verify/');
                inputVal = parts[1].split('?')[0].split('#')[0];
            }

            window.location.href = `/ticket/verify/${inputVal}`;
        }

        function executeCheckIn(token) {
            const btn = document.getElementById('checkin-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'PROCESSING CHECK-IN...';
                btn.classList.add('opacity-50');
            }

            fetch(`/ticket/verify/${token}/check-in`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ method: 'qr_scan' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('SUCCESS: Ticket attendance recorded!');
                    window.location.reload();
                } else {
                    alert('ERROR: ' + data.message);
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Check-in error. Please try again.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'MARK AS ATTENDED (CHECK IN)';
                    btn.classList.remove('opacity-50');
                }
            });
        }
    </script>
</body>
</html>
