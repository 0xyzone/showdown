<div class="space-y-6" x-data="{
    draggedId: null,
    onDragStart(e, id) {
        this.draggedId = id;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', id);
    },
    onDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    },
    onDrop(e, status) {
        e.preventDefault();
        const id = this.draggedId || e.dataTransfer.getData('text/plain');
        if (id) {
            $wire.updateCampaignStatus(parseInt(id), status);
        }
        this.draggedId = null;
    }
}">
    {{-- Kanban Top Filters & Controls --}}
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-xs">
        <div class="flex items-center gap-3 flex-1">
            <div class="relative flex-1 max-w-md">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search campaigns by name or code..." 
                    class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-gray-900 dark:text-gray-100"
                />
                <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <select 
                wire:model.live="priorityFilter" 
                class="py-2 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-amber-500 text-gray-900 dark:text-gray-100"
            >
                <option value="">All Priorities</option>
                @foreach ($priorities as $pri)
                    <option value="{{ $pri->value }}">{{ $pri->getLabel() }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Drag & Drop cards between columns to transition status
        </div>
    </div>

    {{-- Kanban Columns Grid --}}
    <div class="flex gap-4 overflow-x-auto pb-6 scrollbar-thin">
        @foreach ($columns as $statusKey => $col)
            @php
                $statusEnum = $col['status'];
                $items = $col['items'];
            @endphp
            <div 
                class="flex-shrink-0 w-80 bg-gray-50/80 dark:bg-gray-900/60 rounded-xl border border-gray-200 dark:border-gray-800 flex flex-col max-h-[calc(100vh-220px)]"
                @dragover="onDragOver($event)"
                @drop="onDrop($event, '{{ $statusKey }}')"
            >
                {{-- Column Header --}}
                <div class="p-3.5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-gray-100/60 dark:bg-gray-800/40 rounded-t-xl">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-xs tracking-wider uppercase text-gray-700 dark:text-gray-300">
                            {{ $statusEnum->getLabel() }}
                        </span>
                    </div>
                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        {{ $items->count() }}
                    </span>
                </div>

                {{-- Column Items Drop Zone --}}
                <div class="p-3 overflow-y-auto space-y-3 flex-1">
                    @forelse ($items as $campaign)
                        <div 
                            draggable="true"
                            @dragstart="onDragStart($event, {{ $campaign->id }})"
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 shadow-xs hover:shadow-md transition-all cursor-grab active:cursor-grabbing group hover:border-amber-400 dark:hover:border-amber-500"
                        >
                            {{-- Header: Code & Priority --}}
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-medium">
                                    {{ $campaign->campaign_code }}
                                </span>
                                
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ match($campaign->priority?->value) {
                                    'urgent' => 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-400',
                                    'high' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400',
                                    'medium' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                } }}">
                                    {{ $campaign->priority?->getLabel() ?? 'Normal' }}
                                </span>
                            </div>

                            {{-- Title & Objectives --}}
                            <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 line-clamp-2 mb-1 group-hover:text-amber-600 dark:group-hover:text-amber-400">
                                {{ $campaign->title }}
                            </h4>
                            @if ($campaign->objectives)
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ $campaign->objectives }}
                                </p>
                            @endif

                            {{-- Platforms --}}
                            @if (!empty($campaign->platforms))
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach (array_slice($campaign->platforms, 0, 3) as $plt)
                                        <span class="text-[10px] uppercase font-semibold tracking-wider px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">
                                            {{ $plt }}
                                        </span>
                                    @endforeach
                                    @if (count($campaign->platforms) > 3)
                                        <span class="text-[10px] font-semibold px-1 py-0.5 text-gray-400">
                                            +{{ count($campaign->platforms) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            {{-- Footer: Deliverables & Budget --}}
                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-1 font-medium">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <span>{{ $campaign->deliverables->count() }} assets</span>
                                </div>

                                <div class="font-semibold text-gray-800 dark:text-gray-200">
                                    Rs. {{ number_format($campaign->budget, 0) }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-24 flex items-center justify-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-lg text-xs text-gray-400">
                            Drop items here
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
