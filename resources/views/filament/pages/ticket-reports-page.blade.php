<x-filament-panels::page>
    <div class="space-y-6">

        <!-- FILTER TOOLBAR -->
        <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📊</span>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-white">Filter Report Data</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="exportExcel" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 transition-colors shadow-lg shadow-emerald-900/30 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Export to Excel (.xlsx)</span>
                    </button>
                    <button wire:click="resetFilters" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-medium rounded-xl transition-colors cursor-pointer">
                        Reset Filters
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tournament</label>
                    <select wire:model.live="tournament_id" class="w-full px-3 py-2 rounded-xl bg-gray-950 border border-gray-700 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">All Tournaments</option>
                        @foreach(\App\Models\Tournament::all() as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Ticket Package</label>
                    <select wire:model.live="ticket_package_id" class="w-full px-3 py-2 rounded-xl bg-gray-950 border border-gray-700 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">All Packages</option>
                        @foreach(\App\Models\TicketPackage::all() as $pkg)
                            <option value="{{ $pkg->id }}">{{ $pkg->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->user()?->hasRole('super_admin') || auth()->user()?->can('ViewAny:TicketPurchase'))
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Sold By Staff</label>
                        <select wire:model.live="seller_id" class="w-full px-3 py-2 rounded-xl bg-gray-950 border border-gray-700 text-xs text-white focus:border-emerald-500 focus:outline-none">
                            <option value="">All Staff Members</option>
                            @foreach(\App\Models\User::all() as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Payment Status</label>
                    <select wire:model.live="payment_status" class="w-full px-3 py-2 rounded-xl bg-gray-950 border border-gray-700 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Payment Method</label>
                    <select wire:model.live="payment_method_id" class="w-full px-3 py-2 rounded-xl bg-gray-950 border border-gray-700 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">All Payment Sources</option>
                        @foreach(\App\Models\PaymentMethod::all() as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
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
            $staffSummary = $this->getStaffSalesSummary();
            $packageSummary = $this->getPackageSalesSummary();
            $eventDaySummary = $this->getEventDayAttendanceSummary();
            $filteredPurchases = $this->getFilteredPurchasesQuery()->paginate(15);
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Total Revenue</span>
                <span class="text-xl font-black text-emerald-400">Rs. {{ number_format($stats['total_revenue'], 2) }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Paid Tickets Sold</span>
                <span class="text-xl font-black text-white">{{ number_format($stats['total_tickets_sold']) }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Orders / Transactions</span>
                <span class="text-xl font-black text-white">{{ number_format($stats['total_purchases']) }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Tickets Checked In</span>
                <span class="text-xl font-black text-sky-400">{{ number_format($stats['checked_in_count']) }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Unused / Pending Entry</span>
                <span class="text-xl font-black text-amber-400">{{ number_format($stats['unused_count']) }}</span>
            </div>
        </div>

        <!-- BREAKDOWN MATRICES GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- STAFF SALES BREAKDOWN -->
            <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 flex items-center gap-1.5">
                    <span>👤</span> Staff Sales Breakdown
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-gray-800 text-[10px] text-gray-500 uppercase">
                                <th class="pb-2">Staff Member</th>
                                <th class="pb-2 text-center">Orders</th>
                                <th class="pb-2 text-center">Tickets</th>
                                <th class="pb-2 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60">
                            @forelse($staffSummary as $row)
                                <tr>
                                    <td class="py-2.5 font-bold text-white">{{ $row['seller_name'] }}</td>
                                    <td class="py-2.5 text-center text-gray-400">{{ $row['orders_count'] }}</td>
                                    <td class="py-2.5 text-center font-bold text-emerald-400">{{ $row['tickets_sold'] }}</td>
                                    <td class="py-2.5 text-right font-bold text-emerald-400">Rs. {{ number_format($row['total_revenue'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">No staff sales data found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PACKAGE SALES BREAKDOWN -->
            <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 flex items-center gap-1.5">
                    <span>🎁</span> Package Sales Breakdown
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-gray-800 text-[10px] text-gray-500 uppercase">
                                <th class="pb-2">Package Tier</th>
                                <th class="pb-2 text-center">Orders</th>
                                <th class="pb-2 text-center">Tickets</th>
                                <th class="pb-2 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60">
                            @forelse($packageSummary as $row)
                                <tr>
                                    <td class="py-2.5 font-bold text-white">{{ $row['package_name'] }}</td>
                                    <td class="py-2.5 text-center text-gray-400">{{ $row['orders_count'] }}</td>
                                    <td class="py-2.5 text-center font-bold text-emerald-400">{{ $row['tickets_sold'] }}</td>
                                    <td class="py-2.5 text-right font-bold text-emerald-400">Rs. {{ number_format($row['total_revenue'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">No package sales data found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- EVENT DAYS ATTENDANCE BREAKDOWN -->
        @if(!empty($eventDaySummary))
            <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 flex items-center gap-1.5">
                    <span>🎟️</span> Event Days Attendance Breakdown
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-gray-800 text-[10px] text-gray-500 uppercase">
                                <th class="pb-2">Event Schedule Day</th>
                                <th class="pb-2">Date</th>
                                <th class="pb-2 text-center">Total Valid Passes</th>
                                <th class="pb-2 text-center">Gate Checked-In</th>
                                <th class="pb-2 text-right">Attendance Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60">
                            @foreach($eventDaySummary as $dayRow)
                                @php
                                    $rate = $dayRow['valid_tickets'] > 0 ? round(($dayRow['checked_in'] / $dayRow['valid_tickets']) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td class="py-2.5 font-bold text-white">{{ $dayRow['day_name'] }}</td>
                                    <td class="py-2.5 font-mono text-gray-400">{{ $dayRow['event_date'] }}</td>
                                    <td class="py-2.5 text-center font-bold text-gray-300">{{ $dayRow['valid_tickets'] }}</td>
                                    <td class="py-2.5 text-center font-bold text-emerald-400">{{ $dayRow['checked_in'] }}</td>
                                    <td class="py-2.5 text-right font-bold text-sky-400">{{ $rate }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- FILTERED TRANSACTION LIST -->
        <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300 flex items-center gap-1.5">
                <span>📋</span> Detailed Sales Transactions
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-800 text-[10px] text-gray-500 uppercase">
                            <th class="pb-2">Order #</th>
                            <th class="pb-2">Tournament</th>
                            <th class="pb-2">Customer</th>
                            <th class="pb-2">Package</th>
                            <th class="pb-2 text-center">Qty</th>
                            <th class="pb-2 text-right">Total Paid</th>
                            <th class="pb-2">Sold By</th>
                            <th class="pb-2 text-center">Status</th>
                            <th class="pb-2 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @forelse($filteredPurchases as $purchase)
                            <tr>
                                <td class="py-2.5 font-mono font-bold text-white">{{ $purchase->order_number }}</td>
                                <td class="py-2.5 text-gray-300">{{ $purchase->tournament?->name ?? '—' }}</td>
                                <td class="py-2.5 font-medium text-white">{{ $purchase->customer_name }}</td>
                                <td class="py-2.5 text-sky-400">{{ $purchase->package_name ?? 'Standard' }}</td>
                                <td class="py-2.5 text-center font-bold text-white">{{ $purchase->quantity }}</td>
                                <td class="py-2.5 text-right font-bold text-emerald-400">Rs. {{ number_format($purchase->total_amount, 2) }}</td>
                                <td class="py-2.5 text-gray-400">{{ $purchase->seller?->name ?? 'Admin' }}</td>
                                <td class="py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $purchase->payment_status === 'paid' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-amber-950 text-amber-400 border border-amber-800' }}">
                                        {{ $purchase->payment_status }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-right text-gray-400">{{ $purchase->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-6 text-center text-gray-500">No matching ticket transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-3 border-t border-gray-800">
                {{ $filteredPurchases->links() }}
            </div>
        </div>

    </div>
</x-filament-panels::page>
