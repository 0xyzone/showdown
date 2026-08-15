@php
    $user = auth()->user();
    $today = $user?->todayAttendance();
    $isWorking = $today && $today->punch_in_at && ! $today->punch_out_at;
    $isCompleted = $today && $today->punch_out_at;
@endphp

@if($user)
    <div class="flex items-center me-3">
        <a 
            href="{{ route('attendance.index') }}" 
            title="Click to open Attendance Terminal"
            class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 shadow-sm hover:scale-[1.02] active:scale-[0.98] cursor-pointer
                @if($isWorking)
                    bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20 hover:border-emerald-500/50 shadow-emerald-950/40
                @elseif($isCompleted)
                    bg-sky-500/10 text-sky-400 border border-sky-500/30 hover:bg-sky-500/20 hover:border-sky-500/50 shadow-sky-950/40
                @else
                    bg-amber-500/10 text-amber-400 border border-amber-500/30 hover:bg-amber-500/20 hover:border-amber-500/50 shadow-amber-950/40
                @endif
            "
        >
            @if($isWorking)
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="font-bold">Clocked In</span>
                <span class="hidden sm:inline text-[11px] opacity-80 font-mono">{{ $today->punch_in_at->format('h:i A') }}</span>
            @elseif($isCompleted)
                <span class="text-xs">✓</span>
                <span class="font-bold">Clocked Out</span>
                <span class="hidden sm:inline text-[11px] opacity-80 font-mono">({{ $today->formatted_worked_time }})</span>
            @else
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                <span class="font-bold">Not Clocked In</span>
                <span class="hidden sm:inline text-[11px] opacity-80">&rarr; Punch In</span>
            @endif
        </a>
    </div>
@endif
