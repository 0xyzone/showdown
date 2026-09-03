<div class="space-y-6">
    {{-- Header Navigation & View Range Toggle --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-xs">
        <div class="flex items-center gap-3">
            <button 
                wire:click="previousPeriod" 
                class="p-2 rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                {{ $startDate->format('M Y') }} @if ($viewRange === 'quarter') - {{ $endDate->format('M Y') }} @endif
            </h3>

            <button 
                wire:click="nextPeriod" 
                class="p-2 rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

        <div class="flex items-center gap-2">
            <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 p-1 bg-gray-50 dark:bg-gray-800">
                <button 
                    wire:click="$set('viewRange', 'month')" 
                    class="px-3 py-1 text-xs font-semibold rounded-md transition {{ $viewRange === 'month' ? 'bg-white dark:bg-gray-700 text-amber-600 dark:text-amber-400 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
                >
                    Month
                </button>
                <button 
                    wire:click="$set('viewRange', 'quarter')" 
                    class="px-3 py-1 text-xs font-semibold rounded-md transition {{ $viewRange === 'quarter' ? 'bg-white dark:bg-gray-700 text-amber-600 dark:text-amber-400 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
                >
                    Quarter
                </button>
            </div>
        </div>
    </div>

    {{-- Timeline / Gantt Board --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-xs overflow-hidden">
        {{-- Days / Milestones Header Scale --}}
        <div class="border-b border-gray-200 dark:border-gray-800 flex bg-gray-50/70 dark:bg-gray-800/40">
            <div class="w-64 flex-shrink-0 p-3 font-semibold text-xs text-gray-500 uppercase tracking-wider border-r border-gray-200 dark:border-gray-800">
                Campaign / Roadmap
            </div>

            <div class="flex-1 grid" style="grid-template-columns: repeat({{ $totalDays }}, minmax(0, 1fr));">
                @for ($d = 0; $d < $totalDays; $d++)
                    @php
                        $day = $startDate->copy()->addDays($d);
                        $isToday = $day->isToday();
                        $isWeekend = $day->isWeekend();
                    @endphp
                    <div class="py-2 text-center border-r border-gray-100 dark:border-gray-800/60 {{ $isToday ? 'bg-amber-500/10 font-bold text-amber-600' : ($isWeekend ? 'bg-gray-100/40 dark:bg-gray-800/20 text-gray-400' : 'text-gray-500 dark:text-gray-400') }}">
                        <div class="text-[10px] uppercase tracking-tighter">{{ $day->format('D')[0] }}</div>
                        <div class="text-xs">{{ $day->format('j') }}</div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Campaign Gantt Rows --}}
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($campaigns as $campaign)
                @php
                    $campStart = $campaign->start_date;
                    $campEnd = $campaign->end_date;

                    // Clamped start & duration inside current viewport
                    $clampedStart = $campStart->lt($startDate) ? $startDate : $campStart;
                    $clampedEnd = $campEnd->gt($endDate) ? $endDate : $campEnd;

                    $startOffsetDays = max(0, $startDate->diffInDays($clampedStart, false));
                    $durationDays = max(1, $clampedStart->diffInDays($clampedEnd) + 1);

                    $leftPercent = round(($startOffsetDays / $totalDays) * 100, 2);
                    $widthPercent = round(($durationDays / $totalDays) * 100, 2);
                @endphp

                <div class="flex items-center hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition py-3">
                    {{-- Left Title Column --}}
                    <div class="w-64 flex-shrink-0 px-4 border-r border-gray-200 dark:border-gray-800">
                        <div class="font-bold text-xs text-gray-900 dark:text-gray-100 truncate" title="{{ $campaign->title }}">
                            {{ $campaign->title }}
                        </div>
                        <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                            <span class="font-mono">{{ $campaign->campaign_code }}</span>
                            <span>•</span>
                            <span>{{ $campaign->deliverables->count() }} assets</span>
                        </div>
                    </div>

                    {{-- Right Bar Span Canvas --}}
                    <div class="flex-1 relative h-9 px-2 flex items-center">
                        {{-- Duration Bar --}}
                        <div 
                            class="absolute h-7 rounded-md shadow-xs flex items-center px-2.5 text-xs font-semibold text-white transition-all overflow-hidden {{ match($campaign->status?->value) {
                                'running' => 'bg-emerald-600 hover:bg-emerald-500',
                                'in_production' => 'bg-amber-600 hover:bg-amber-500',
                                'scheduled' => 'bg-purple-600 hover:bg-purple-500',
                                'review' => 'bg-orange-600 hover:bg-orange-500',
                                'completed' => 'bg-teal-700 hover:bg-teal-600',
                                'cancelled' => 'bg-red-700 hover:bg-red-600',
                                default => 'bg-gray-600 hover:bg-gray-500',
                            } }}"
                            style="left: {{ $leftPercent }}%; width: {{ $widthPercent }}%;"
                            title="{{ $campaign->title }} ({{ $campStart->format('M d') }} - {{ $campEnd->format('M d') }})"
                        >
                            <span class="truncate">{{ $campaign->title }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-gray-400">
                    No campaigns scheduled within this date window.
                </div>
            @endforelse
        </div>
    </div>
</div>
