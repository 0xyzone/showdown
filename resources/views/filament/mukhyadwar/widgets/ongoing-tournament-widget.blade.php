<x-filament-widgets::widget>
    <x-filament::section>
        @if($tournament)
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse">
                            FEATURED TOURNAMENT
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                            {{ ucfirst(str_replace('_', ' ', $tournament->status)) }}
                        </span>
                    </div>

                    <h2 class="text-2xl font-black text-white tracking-wide">
                        {{ $tournament->name }} ({{ $tournament->season_version }})
                    </h2>

                    <p class="text-sm text-slate-300 max-w-2xl">
                        {{ Str::limit(strip_tags($tournament->hero_subheadline ?: $tournament->description), 160) }}
                    </p>

                    <div class="flex flex-wrap items-center gap-4 text-xs font-mono font-semibold text-slate-400 pt-2">
                        <div class="flex items-center gap-1.5">
                            <span class="text-emerald-400">💰 Prize Pool:</span>
                            <span class="text-white font-bold">Rs. {{ number_format($tournament->prize_pool_total) }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-cyan-400">🎟️ Entry Fee:</span>
                            <span class="text-white font-bold">{{ $tournament->formatted_entry_fee }}</span>
                        </div>
                    </div>
                </div>

                <div class="shrink-0 flex flex-col items-stretch md:items-end gap-3 w-full md:w-auto">
                    @if($isRegistered)
                        <div class="px-5 py-3 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold text-sm text-center flex items-center justify-center gap-2">
                            <span>✅ Team Registered & Applied</span>
                        </div>
                    @else
                        <x-filament::button wire:click="mountAction('registerAction')" color="success" icon="heroicon-o-sparkles">
                            Register Team Now
                        </x-filament::button>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-6 text-slate-400 text-sm">
                No active or ongoing tournament registration is currently open. Check back soon!
            </div>
        @endif

        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>
