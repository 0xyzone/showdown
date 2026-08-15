<x-filament-panels::page>
    <div class="space-y-6">

        <!-- FILTER TOOLBAR -->
        <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📊</span>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-white">Filter Timesheet & Attendance Data</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="exportExcel" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 transition-colors shadow-lg shadow-emerald-900/30 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Export Timesheet (.xlsx)</span>
                    </button>
                    <button wire:click="resetFilters" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-medium rounded-xl transition-colors cursor-pointer">
                        Reset Filters
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @if(auth()->user()?->hasRole('super_admin') || auth()->user()?->hasRole('attendance_manager'))
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Staff Member</label>
                        <select wire:model.live="user_id" class="w-full px-3 py-2 rounded-xl bg-gray-950 border border-gray-700 text-xs text-white focus:border-emerald-500 focus:outline-none">
                            <option value="">All Staff Members</option>
                            @foreach(\App\Models\User::all() as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Attendance Status</label>
                    <select wire:model.live="status" class="w-full px-3 py-2 rounded-xl bg-gray-950 border border-gray-700 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="working">Currently Working</option>
                        <option value="completed">Completed</option>
                        <option value="remote">Remote Work</option>
                        <option value="half_day">Half Day</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Location Mode</label>
                    <select wire:model.live="location_mode" class="w-full px-3 py-2 rounded-xl bg-gray-950 border border-gray-700 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">All Locations</option>
                        <option value="office">Office Geofence</option>
                        <option value="remote">Remote / WFH</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Date From</label>
                    <input type="date" wire:model.live="date_from" class="w-full px-3 py-2 rounded-xl bg-gray-950 border border-gray-700 text-xs text-white focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Date To</label>
                    <input type="date" wire:model.live="date_to" class="w-full px-3 py-2 rounded-xl bg-gray-950 border border-gray-700 text-xs text-white focus:border-emerald-500 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- SUMMARY METRICS CARDS -->
        @php
            $stats = $this->getSummaryStats();
            $filteredRecords = $this->getFilteredAttendanceQuery()->paginate(15);
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Total Timesheet Days</span>
                <span class="text-xl font-black text-white">{{ number_format($stats['total_days']) }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Total Hours Worked</span>
                <span class="text-xl font-black text-emerald-400">{{ $stats['total_hours'] }} hrs</span>
            </div>
            <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Completed Shifts</span>
                <span class="text-xl font-black text-sky-400">{{ number_format($stats['completed_count']) }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Remote WFH Days</span>
                <span class="text-xl font-black text-amber-400">{{ number_format($stats['remote_count']) }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Currently Active</span>
                <span class="text-xl font-black text-teal-400">{{ number_format($stats['active_count']) }}</span>
            </div>
        </div>

        <!-- DETAILED TIMESHEET LIST TABLE -->
        <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 flex items-center gap-1.5">
                <span>📋</span> Detailed Staff Attendance Timesheets
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-800 text-[10px] text-gray-500 uppercase">
                            <th class="pb-2">Staff Member</th>
                            <th class="pb-2">Date</th>
                            <th class="pb-2">Clock In</th>
                            <th class="pb-2">Clock Out</th>
                            <th class="pb-2 text-right">Worked Time</th>
                            <th class="pb-2 text-center">Status</th>
                            <th class="pb-2 text-center">Location</th>
                            <th class="pb-2 text-center">Biometric</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60 font-mono">
                        @forelse($filteredRecords as $att)
                            <tr>
                                <td class="py-2.5 font-sans font-bold text-white">{{ $att->user?->name ?? '—' }}</td>
                                <td class="py-2.5 text-gray-300">{{ $att->date->format('M d, Y (D)') }}</td>
                                <td class="py-2.5 text-gray-400">{{ $att->punch_in_at ? $att->punch_in_at->format('h:i A') : '—' }}</td>
                                <td class="py-2.5 text-gray-400">{{ $att->punch_out_at ? $att->punch_out_at->format('h:i A') : '—' }}</td>
                                <td class="py-2.5 text-right font-bold text-emerald-400">{{ $att->formatted_worked_time }}</td>
                                <td class="py-2.5 text-center font-sans">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $att->status === 'completed' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : ($att->status === 'working' ? 'bg-sky-950 text-sky-400 border border-sky-800' : 'bg-amber-950 text-amber-400 border border-amber-800') }}">
                                        {{ $att->status }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-center font-sans">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $att->location_mode === 'remote' ? 'bg-purple-950 text-purple-400' : 'bg-gray-800 text-gray-300' }}">
                                        {{ ucfirst($att->location_mode) }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-center">
                                    @if($att->punch_in_verified_biometric)
                                        <span class="text-emerald-400">✓</span>
                                    @else
                                        <span class="text-gray-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-gray-500 font-sans">No matching attendance timesheets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-3 border-t border-gray-800">
                {{ $filteredRecords->links() }}
            </div>
        </div>

    </div>
</x-filament-panels::page>
