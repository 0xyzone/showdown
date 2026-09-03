<x-filament-panels::page>
    <div class="space-y-5">
        {{-- Calendar description banner & legend --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-lg bg-primary-600 text-white shadow-xs">
                    <x-heroicon-o-calendar-days class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-900 dark:text-gray-100">Editorial & Campaign Release Calendar</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Click any deliverable pill to view or edit details, or click a campaign banner to jump to the campaign overview.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 font-medium">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span> Campaign Timeline
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Approved Deliverable
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 font-medium">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Pending Review
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300 font-medium">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Needs Revisions
                </span>
            </div>
        </div>
    </div>

    {{-- Calendar Custom CSS for clean, readable typography and event pills --}}
    <style>
        .filament-fullcalendar .fc-theme-standard th {
            padding: 0.65rem 0.25rem !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            font-weight: 600 !important;
        }
        .filament-fullcalendar .fc-daygrid-day-number {
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
            padding: 0.35rem 0.5rem !important;
        }
        .filament-fullcalendar .fc-event {
            border-radius: 6px !important;
            font-size: 0.75rem !important;
            line-height: 1.25 !important;
            padding: 2px 4px !important;
            margin-bottom: 2px !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
        .filament-fullcalendar .fc-event-title {
            font-weight: 500 !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }
        .filament-fullcalendar .fc-toolbar-title {
            font-size: 1.15rem !important;
            font-weight: 700 !important;
        }
        .filament-fullcalendar .fc-button {
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
        }
        .filament-fullcalendar .fc-list-event-title {
            font-weight: 600 !important;
        }
    </style>
</x-filament-panels::page>
