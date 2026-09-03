@php
    $plugin = \Saade\FilamentFullCalendar\FilamentFullCalendarPlugin::get();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex justify-end flex-1 mb-4">
            <x-filament::actions :actions="$this->getCachedHeaderActions()" class="shrink-0" />
        </div>

        <div wire:ignore x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-fullcalendar-alpine', 'saade/filament-fullcalendar') }}"
            x-ignore x-data="fullcalendar({
                locale: @js($plugin->getLocale()),
                plugins: @js($plugin->getPlugins()),
                schedulerLicenseKey: @js($plugin->getSchedulerLicenseKey()),
                timeZone: @js($plugin->getTimezone()),
                config: @js($this->getConfig()),
                editable: @json($plugin->isEditable()),
                selectable: @json($plugin->isSelectable()),
                eventClassNames: {!! htmlspecialchars($this->eventClassNames(), ENT_COMPAT) !!},
                eventContent: {!! htmlspecialchars($this->eventContent(), ENT_COMPAT) !!},
                eventDidMount: {!! htmlspecialchars($this->eventDidMount(), ENT_COMPAT) !!},
                eventWillUnmount: {!! htmlspecialchars($this->eventWillUnmount(), ENT_COMPAT) !!},
            })" class="filament-fullcalendar modern-3d-calendar-root"></div>
    </x-filament::section>

    <x-filament-actions::modals />

    {{-- Clean High-Impact 3D Volumetric Calendar CSS Overrides --}}
    <style>
        /* ===================================================================
           1. DARK MODE & WEEK DAY VISIBILITY FIX (CRITICAL)
           The white week headers in dark mode were unreadable.
           =================================================================== */
        .filament-fullcalendar,
        .modern-3d-calendar-root {
            --fc-page-bg-color: transparent !important;
            --fc-border-color: rgba(255, 255, 255, 0.08) !important;
            --fc-today-bg-color: transparent !important;
            font-family: inherit !important;
        }

        /* FullCalendar table container & background */
        .filament-fullcalendar .fc-view-harness,
        .filament-fullcalendar .fc-scrollgrid {
            border: none !important;
            background: transparent !important;
        }

        .filament-fullcalendar .fc-scrollgrid td,
        .filament-fullcalendar .fc-scrollgrid th {
            border-color: rgba(255, 255, 255, 0.06) !important;
        }

        /* Fix Weekday Table Header (Sunday, Monday, Tuesday...) */
        .filament-fullcalendar .fc-col-header {
            background: transparent !important;
            margin-bottom: 8px !important;
        }

        .filament-fullcalendar .fc-col-header-cell {
            padding: 10px 4px !important;
            border: none !important;
            background: transparent !important;
        }

        /* Weekday Header Labels: High Contrast & Crisp */
        .filament-fullcalendar .fc-col-header-cell-cushion {
            display: inline-block !important;
            padding: 6px 14px !important;
            border-radius: 9999px !important;
            font-size: 0.8rem !important;
            font-weight: 800 !important;
            letter-spacing: 0.08em !important;
            text-transform: uppercase !important;
            background: #f1f5f9 !important;
            color: #334155 !important;
            border: 1px solid rgba(203, 213, 225, 0.8) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
        }

        /* DARK MODE: Week day text & badge styling - Fixes the white bar issue! */
        html.dark .filament-fullcalendar .fc-col-header-cell-cushion {
            background: #1e1e24 !important;
            color: #f1f5f9 !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        }

        /* Strip any white background from table header row */
        .filament-fullcalendar thead,
        .filament-fullcalendar thead tr,
        .filament-fullcalendar thead th {
            background: transparent !important;
            border: none !important;
        }

        /* ===================================================================
           2. 3D VOLUMETRIC SQUARE BOXED CALENDAR CELLS (SQUARE NOT RECTANGLE)
           =================================================================== */
        /* Day Grid Rows Spacing */
        .filament-fullcalendar .fc-daygrid-body table {
            border-spacing: 6px 8px !important;
            border-collapse: separate !important;
        }

        /* Each 3D Box Cell: Force 1:1 True Square Aspect Ratio */
        .filament-fullcalendar .fc-daygrid-day {
            background: transparent !important;
            border: none !important;
            aspect-ratio: 1 / 1 !important;
            vertical-align: top !important;
            position: static !important;
            overflow: visible !important;
        }

        .filament-fullcalendar .fc-daygrid-day-frame {
            position: relative !important;
            aspect-ratio: 1 / 1 !important;
            width: 100% !important;
            min-height: 120px !important;
            display: flex !important;
            flex-direction: column !important;
            box-sizing: border-box !important;
            padding: 6px 8px !important;
            border-radius: 1.25rem !important;
            overflow: visible !important;
            z-index: auto !important;
            isolation: auto !important;
            transform: none !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease !important;
            
            /* Light Mode 3D Box: Layered bevel & drop shadow */
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 10px 15px -3px rgba(0, 0, 0, 0.03),
                inset 0 1px 0 rgba(255, 255, 255, 1),
                inset 0 -2px 0 rgba(0, 0, 0, 0.04) !important;
        }

        /* Dark Mode 3D Box: Obsidian Volumetric Floating Box */
        html.dark .filament-fullcalendar .fc-daygrid-day-frame {
            background: linear-gradient(180deg, #232329 0%, #17171b 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 
                0 6px 16px -2px rgba(0, 0, 0, 0.65),
                0 2px 4px -1px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.1),
                inset 0 -2px 0 rgba(0, 0, 0, 0.4) !important;
        }

        /* 3D Hover Effect on Days:
           CRITICAL: z-index remains auto so day boxes NEVER create an isolated stacking context
           that traps or covers multi-day spanning bars or adjacent pills! */
        .filament-fullcalendar .fc-daygrid-day-frame:hover {
            z-index: auto !important;
            transform: none !important;
            border-color: rgba(99, 102, 241, 0.8) !important;
            box-shadow: 
                0 0 0 1.5px rgba(99, 102, 241, 0.6),
                0 10px 22px -3px rgba(99, 102, 241, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 1),
                inset 0 -2px 0 rgba(99, 102, 241, 0.15) !important;
        }

        html.dark .filament-fullcalendar .fc-daygrid-day-frame:hover {
            z-index: auto !important;
            transform: none !important;
            background: linear-gradient(180deg, #2a2a32 0%, #1d1d23 100%) !important;
            border-color: #818cf8 !important;
            box-shadow: 
                0 0 0 1.5px #818cf8,
                0 12px 28px -4px rgba(99, 102, 241, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.15),
                inset 0 -2px 0 rgba(99, 102, 241, 0.3) !important;
        }

        /* Event Harnesses and Pills: Elevated above day boxes with shared relative coordinate space */
        .filament-fullcalendar .fc-daygrid-body {
            position: relative !important;
            z-index: 10 !important;
        }

        .filament-fullcalendar .fc-daygrid-day-events {
            position: relative !important;
            min-height: 2em !important;
            margin-top: 0 !important;
            overflow: visible !important;
            z-index: auto !important;
        }

        /* ELIMINATE GHOST SPACING: FullCalendar harness pseudo elements add 32px of ghost height */
        .filament-fullcalendar .fc-daygrid-event-harness::before,
        .filament-fullcalendar .fc-daygrid-event-harness::after {
            display: none !important;
            content: none !important;
            height: 0 !important;
            line-height: 0 !important;
            font-size: 0 !important;
        }

        .filament-fullcalendar .fc-daygrid-event-harness {
            position: relative !important;
            z-index: 25 !important;
            pointer-events: auto !important;
        }

        .filament-fullcalendar .fc-daygrid-event-harness-abs {
            position: absolute !important;
            z-index: 20 !important;
            pointer-events: auto !important;
        }

        .filament-fullcalendar .fc-event {
            position: relative !important;
            z-index: 30 !important;
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 0 3px 0 !important;
            box-shadow: none !important;
            display: block !important;
            height: 24px !important;
            pointer-events: auto !important;
        }

        /* Today's Special 3D Box */
        .filament-fullcalendar .fc-day-today .fc-daygrid-day-frame {
            border: 2px solid #6366f1 !important;
            background: linear-gradient(180deg, #ffffff 0%, #eef2ff 100%) !important;
            box-shadow: 
                0 12px 25px -4px rgba(99, 102, 241, 0.3),
                inset 0 0 20px rgba(99, 102, 241, 0.1) !important;
        }

        html.dark .filament-fullcalendar .fc-day-today .fc-daygrid-day-frame {
            border: 2px solid #818cf8 !important;
            background: linear-gradient(180deg, #252536 0%, #1b1b26 100%) !important;
            box-shadow: 
                0 14px 30px -4px rgba(99, 102, 241, 0.45),
                inset 0 0 24px rgba(99, 102, 241, 0.18) !important;
        }

        /* Day Numbers inside 3D boxes: Bold, High-contrast and Unobstructed */
        .filament-fullcalendar .fc-daygrid-day-top {
            display: flex !important;
            justify-content: flex-end !important;
            align-items: center !important;
            padding: 2px 6px 4px 6px !important;
            min-height: 28px !important;
            position: relative !important;
            z-index: 5 !important;
        }

        .filament-fullcalendar .fc-daygrid-day-number {
            font-size: 0.95rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            min-width: 26px !important;
            height: 26px !important;
            border-radius: 9999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 4px !important;
            letter-spacing: -0.02em !important;
            transition: all 0.2s ease !important;
        }

        html.dark .filament-fullcalendar .fc-daygrid-day-number {
            color: #ffffff !important;
            font-weight: 800 !important;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.9) !important;
        }

        .filament-fullcalendar .fc-day-today .fc-daygrid-day-number {
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            color: #ffffff !important;
            box-shadow: 0 3px 10px rgba(99, 102, 241, 0.6) !important;
        }

        /* Out-of-month dimmed 3D box: DO NOT set opacity on day-frame as it forms an isolated stacking context */
        .filament-fullcalendar .fc-day-other .fc-daygrid-day-frame {
            box-shadow: none !important;
            background: rgba(248, 250, 252, 0.45) !important;
        }

        html.dark .filament-fullcalendar .fc-day-other .fc-daygrid-day-frame {
            background: rgba(18, 18, 22, 0.45) !important;
            box-shadow: none !important;
            border-color: rgba(255, 255, 255, 0.03) !important;
        }

        .filament-fullcalendar .fc-day-other .fc-daygrid-day-top {
            opacity: 0.35 !important;
        }

        /* ===================================================================
           3. 3D FLOATING TOOLBAR
           =================================================================== */
        .filament-fullcalendar .fc-toolbar {
            margin-bottom: 1.75rem !important;
            padding: 0.85rem 1.25rem !important;
            border-radius: 1.5rem !important;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.9)) !important;
            backdrop-filter: blur(16px) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 1rem !important;
        }

        html.dark .filament-fullcalendar .fc-toolbar {
            background: linear-gradient(135deg, #202026, #141418) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 14px 30px -6px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
        }

        .filament-fullcalendar .fc-toolbar-title {
            font-size: 1.45rem !important;
            font-weight: 900 !important;
            letter-spacing: -0.035em !important;
            color: #0f172a !important;
        }

        html.dark .filament-fullcalendar .fc-toolbar-title {
            color: #f8fafc !important;
        }

        /* 3D Modern Buttons */
        .filament-fullcalendar .fc-button {
            border-radius: 0.875rem !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            padding: 0.55rem 1.1rem !important;
            border: 1px solid rgba(203, 213, 225, 0.8) !important;
            background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%) !important;
            color: #334155 !important;
            box-shadow: 0 3px 6px -1px rgba(0, 0, 0, 0.06), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            cursor: pointer !important;
        }

        html.dark .filament-fullcalendar .fc-button {
            border-color: rgba(63, 63, 70, 0.8) !important;
            background: linear-gradient(180deg, #2d2d34 0%, #1c1c20 100%) !important;
            color: #e4e4e7 !important;
            box-shadow: 0 4px 8px -2px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
        }

        .filament-fullcalendar .fc-button:hover:not([disabled]) {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 16px -2px rgba(79, 70, 229, 0.2), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
            border-color: #818cf8 !important;
            color: #4338ca !important;
        }

        html.dark .filament-fullcalendar .fc-button:hover:not([disabled]) {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px -2px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
            border-color: #6366f1 !important;
            color: #ffffff !important;
        }

        .filament-fullcalendar .fc-button:active:not([disabled]) {
            transform: translateY(1px) !important;
        }

        .filament-fullcalendar .fc-button-active {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
            border-color: #4338ca !important;
            color: #ffffff !important;
            box-shadow: 0 6px 14px -1px rgba(79, 70, 229, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        }

        html.dark .filament-fullcalendar .fc-button-active {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            border-color: #818cf8 !important;
            color: #ffffff !important;
            box-shadow: 0 6px 18px -1px rgba(99, 102, 241, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.35) !important;
        }

        /* ===================================================================
           4. 3D EVENT PILLS
           =================================================================== */
        .filament-fullcalendar .fc-event {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 0 3px 0 !important;
            box-shadow: none !important;
            display: block !important;
            height: 24px !important;
        }

        /* Spanning Campaign Bars: 3D Gradient Bar */
        .filament-fullcalendar .modern-campaign-pill {
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            border-radius: 0.5rem !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            box-shadow: 0 2px 6px -1px rgba(37, 99, 235, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.35) !important;
            height: 24px !important;
            min-height: 24px !important;
            line-height: 24px !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .filament-fullcalendar .modern-campaign-pill:hover {
            z-index: 60 !important;
            transform: translateY(-2px) scale(1.02) !important;
            box-shadow: 0 8px 16px -2px rgba(37, 99, 235, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.5) !important;
            filter: brightness(1.1) !important;
        }

        /* Deliverable Pill Micro-Card */
        .filament-fullcalendar .modern-deliverable-pill {
            background: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 2px 5px -1px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
            height: 24px !important;
            min-height: 24px !important;
            line-height: 24px !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        html.dark .filament-fullcalendar .modern-deliverable-pill {
            background: #202026 !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 2px 6px -1px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
        }

        .filament-fullcalendar .fc-event-main {
            padding: 0 !important;
            line-height: inherit !important;
            height: 100% !important;
        }

        .filament-fullcalendar .modern-deliverable-pill:hover {
            z-index: 60 !important;
            transform: translateY(-2px) scale(1.02) !important;
            border-color: #818cf8 !important;
            box-shadow: 0 8px 16px -2px rgba(99, 102, 241, 0.3), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
        }

        html.dark .filament-fullcalendar .modern-deliverable-pill:hover {
            z-index: 60 !important;
            transform: translateY(-2px) scale(1.02) !important;
            border-color: #a5b4fc !important;
            background: #282832 !important;
            box-shadow: 0 8px 20px -2px rgba(0, 0, 0, 0.7), 0 2px 8px -1px rgba(99, 102, 241, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
        }

        html.dark .deliverable-pill-content .deliverable-title {
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        /* More (+X) events pill */
        .filament-fullcalendar .fc-more-link {
            font-size: 0.725rem !important;
            font-weight: 800 !important;
            color: #4f46e5 !important;
            padding: 3px 9px !important;
            border-radius: 9999px !important;
            background: rgba(79, 70, 229, 0.12) !important;
            border: 1px solid rgba(79, 70, 229, 0.2) !important;
            transition: all 0.2s ease !important;
        }

        html.dark .filament-fullcalendar .fc-more-link {
            color: #c7d2fe !important;
            background: rgba(99, 102, 241, 0.25) !important;
            border-color: rgba(99, 102, 241, 0.4) !important;
        }

        .filament-fullcalendar .fc-more-link:hover {
            transform: translateY(-1px) scale(1.05) !important;
            background: rgba(79, 70, 229, 0.25) !important;
        }
    </style>
</x-filament-widgets::widget>
