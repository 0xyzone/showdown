<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Futuristic 2026 Ambient Hero Header --}}
        <div class="calendar-hero-glass relative overflow-hidden rounded-3xl p-6 md:p-8">
            {{-- Glowing ambient gradient mesh in background --}}
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
            <div class="hero-orb hero-orb-3"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="flex items-center gap-4 md:gap-5">
                    <div class="hero-3d-badge">
                        <x-heroicon-o-calendar-days class="w-8 h-8 text-white drop-shadow-md" />
                        <span class="badge-pulse-glow"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                Editorial & Release Matrix
                            </h2>
                            <span class="live-pill">
                                <span class="live-dot"></span>
                                2026 Live Matrix
                            </span>
                        </div>
                        <p class="text-xs md:text-sm text-slate-600 dark:text-slate-300 font-medium mt-1">
                            Click any deliverable to trigger the instant 3D inspection modal.
                        </p>
                    </div>
                </div>

                {{-- Interactive 3D Legend Tags --}}
                <div class="flex flex-wrap items-center gap-2.5 text-xs font-semibold">
                    <div class="legend-3d-item legend-blue">
                        <span class="legend-indicator bg-blue-500"></span>
                        <span>Campaign Bar</span>
                    </div>
                    <div class="legend-3d-item legend-emerald">
                        <span class="legend-indicator bg-emerald-500"></span>
                        <span>Approved</span>
                    </div>
                    <div class="legend-3d-item legend-amber">
                        <span class="legend-indicator bg-amber-500"></span>
                        <span>In Review</span>
                    </div>
                    <div class="legend-3d-item legend-rose">
                        <span class="legend-indicator bg-rose-500"></span>
                        <span>Revisions</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Clean 2026 Fresh 3D Boxed Calendar CSS Overrides --}}
    <style>
        /* ===================================================================
           1. 2026 HERO & GLASSMORPHISM AESTHETICS
           =================================================================== */
        .calendar-hero-glass {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(243, 244, 246, 0.85));
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0, 0, 0, 0.03);
            backdrop-filter: blur(20px);
        }

        html.dark .calendar-hero-glass {
            background: linear-gradient(135deg, rgba(24, 24, 28, 0.95), rgba(15, 15, 18, 0.9));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.06);
        }

        .hero-orb {
            position: absolute;
            border-radius: 9999px;
            pointer-events: none;
            filter: blur(60px);
            opacity: 0.6;
        }

        .hero-orb-1 {
            top: -50px;
            left: -50px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.4), transparent 70%);
        }

        .hero-orb-2 {
            bottom: -60px;
            right: 10%;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.35), transparent 70%);
        }

        .hero-orb-3 {
            top: 20%;
            right: -40px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.25), transparent 70%);
        }

        .hero-3d-badge {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3.75rem;
            height: 3.75rem;
            border-radius: 1.25rem;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            box-shadow: 0 10px 20px -3px rgba(79, 70, 229, 0.45), inset 0 2px 4px rgba(255, 255, 255, 0.4);
            transform: perspective(600px) rotateX(4deg) rotateY(-4deg);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .hero-3d-badge:hover {
            transform: perspective(600px) rotateX(0deg) rotateY(0deg) scale(1.06) translateY(-2px);
            box-shadow: 0 15px 25px -3px rgba(79, 70, 229, 0.6), inset 0 2px 4px rgba(255, 255, 255, 0.6);
        }

        .badge-pulse-glow {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 12px;
            height: 12px;
            border-radius: 9999px;
            background: #ec4899;
            border: 2px solid white;
            box-shadow: 0 0 10px #ec4899;
        }

        .live-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 700;
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, 0.25);
            backdrop-filter: blur(8px);
        }

        html.dark .live-pill {
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
            border-color: rgba(99, 102, 241, 0.4);
        }

        .live-dot {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background-color: #6366f1;
            box-shadow: 0 0 6px #6366f1;
            animation: pulseDot 2s infinite ease-in-out;
        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.6; }
        }

        /* 3D Legend items */
        .legend-3d-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.85rem;
            border-radius: 0.875rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: default;
        }

        .legend-3d-item:hover {
            transform: translateY(-2px);
        }

        .legend-indicator {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
        }

        .legend-blue {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.25);
            color: #1d4ed8;
        }
        html.dark .legend-blue {
            background: rgba(30, 58, 138, 0.25);
            border-color: rgba(59, 130, 246, 0.4);
            color: #93c5fd;
        }

        .legend-emerald {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #047857;
        }
        html.dark .legend-emerald {
            background: rgba(6, 78, 59, 0.25);
            border-color: rgba(16, 185, 129, 0.4);
            color: #6ee7b7;
        }

        .legend-amber {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.25);
            color: #b45309;
        }
        html.dark .legend-amber {
            background: rgba(120, 53, 15, 0.25);
            border-color: rgba(245, 158, 11, 0.4);
            color: #fcd34d;
        }

        .legend-rose {
            background: rgba(244, 63, 94, 0.08);
            border: 1px solid rgba(244, 63, 94, 0.25);
            color: #be123c;
        }
        html.dark .legend-rose {
            background: rgba(136, 19, 55, 0.25);
            border-color: rgba(244, 63, 94, 0.4);
            color: #fda4af;
        }

        /* ===================================================================
           2. FULLCALENDAR 3D FLOATING TOOLBAR
           =================================================================== */
        .filament-fullcalendar {
            --fc-border-color: transparent !important;
            --fc-page-bg-color: transparent !important;
            --fc-today-bg-color: transparent !important;
            font-family: inherit !important;
            padding-top: 0.5rem !important;
        }

        .filament-fullcalendar-widget {
            background: transparent !important;
        }

        .fi-section {
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }

        .filament-fullcalendar .fc-toolbar {
            margin-bottom: 2rem !important;
            padding: 0.85rem 1.25rem !important;
            border-radius: 1.5rem !important;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.8)) !important;
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
            background: linear-gradient(135deg, rgba(24, 24, 28, 0.9), rgba(18, 18, 22, 0.85)) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 14px 30px -6px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.06) !important;
        }

        .filament-fullcalendar .fc-toolbar-title {
            font-size: 1.45rem !important;
            font-weight: 900 !important;
            letter-spacing: -0.035em !important;
            background: linear-gradient(135deg, #0f172a 0%, #4338ca 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
        }

        html.dark .filament-fullcalendar .fc-toolbar-title {
            background: linear-gradient(135deg, #ffffff 30%, #a5b4fc 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
        }

        /* 3D Modern Tactile Action Buttons */
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
           3. 3D SEPARATED BOXES GRID (No flat rectangle table)
           Each day cell is an isolated floating 3D volumetric box!
           =================================================================== */
        .filament-fullcalendar .fc-theme-standard th,
        .filament-fullcalendar .fc-theme-standard td,
        .filament-fullcalendar .fc-scrollgrid {
            border: none !important;
        }

        /* Header day names */
        .filament-fullcalendar .fc-col-header-cell {
            padding: 0.5rem 0.5rem 0.75rem 0.5rem !important;
            background: transparent !important;
            border: none !important;
        }

        .filament-fullcalendar .fc-col-header-cell-cushion {
            font-size: 0.75rem !important;
            font-weight: 800 !important;
            letter-spacing: 0.1em !important;
            text-transform: uppercase !important;
            color: #64748b !important;
        }

        html.dark .filament-fullcalendar .fc-col-header-cell-cushion {
            color: #94a3b8 !important;
        }

        /* 3D Box Styling for Days */
        .filament-fullcalendar .fc-daygrid-day-frame {
            margin: 5px !important;
            border-radius: 1.25rem !important;
            padding: 8px !important;
            min-height: 120px !important;
            position: relative !important;
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1) !important;

            /* Light mode 3D volumetric box: layered shadows & light bevel */
            background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.04),
                0 2px 4px -2px rgba(0, 0, 0, 0.03),
                0 12px 20px -8px rgba(0, 0, 0, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.95),
                inset 0 -2px 0 rgba(0, 0, 0, 0.02) !important;
        }

        /* Dark mode 3D volumetric box: obsidian surface with neon bevel */
        html.dark .filament-fullcalendar .fc-daygrid-day-frame {
            background: linear-gradient(180deg, #222228 0%, #18181c 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.07) !important;
            box-shadow: 
                0 6px 16px -2px rgba(0, 0, 0, 0.6),
                0 2px 4px -1px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.08),
                inset 0 -2px 0 rgba(0, 0, 0, 0.3) !important;
        }

        /* 3D Box Hover Lift Effect */
        .filament-fullcalendar .fc-daygrid-day-frame:hover {
            transform: translateY(-5px) scale(1.02) !important;
            z-index: 20 !important;
            border-color: rgba(129, 140, 248, 0.7) !important;
            box-shadow: 
                0 16px 30px -4px rgba(79, 70, 229, 0.16),
                0 6px 12px -2px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 1) !important;
        }

        html.dark .filament-fullcalendar .fc-daygrid-day-frame:hover {
            transform: translateY(-5px) scale(1.02) !important;
            z-index: 20 !important;
            background: linear-gradient(180deg, #2b2b34 0%, #1e1e24 100%) !important;
            border-color: rgba(99, 102, 241, 0.7) !important;
            box-shadow: 
                0 20px 35px -6px rgba(0, 0, 0, 0.8),
                0 8px 18px -4px rgba(99, 102, 241, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
        }

        /* Out-of-month dimmed 3D box */
        .filament-fullcalendar .fc-day-other .fc-daygrid-day-frame {
            opacity: 0.35 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02) !important;
            background: rgba(248, 250, 252, 0.6) !important;
        }

        html.dark .filament-fullcalendar .fc-day-other .fc-daygrid-day-frame {
            opacity: 0.28 !important;
            background: rgba(20, 20, 24, 0.5) !important;
            box-shadow: none !important;
            border-color: rgba(255, 255, 255, 0.03) !important;
        }

        /* Today 3D Box: Vivid Neon Halo */
        .filament-fullcalendar .fc-day-today .fc-daygrid-day-frame {
            border: 1.5px solid #6366f1 !important;
            background: linear-gradient(180deg, #ffffff 0%, #eef2ff 100%) !important;
            box-shadow: 
                0 10px 25px -3px rgba(99, 102, 241, 0.25),
                0 4px 6px -2px rgba(99, 102, 241, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 1),
                inset 0 0 20px rgba(99, 102, 241, 0.08) !important;
        }

        html.dark .filament-fullcalendar .fc-day-today .fc-daygrid-day-frame {
            border: 1.5px solid #818cf8 !important;
            background: linear-gradient(180deg, #242434 0%, #1a1a24 100%) !important;
            box-shadow: 
                0 12px 30px -4px rgba(99, 102, 241, 0.4),
                0 4px 10px -2px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.15),
                inset 0 0 20px rgba(99, 102, 241, 0.15) !important;
        }

        /* Day Numbers inside 3D boxes */
        .filament-fullcalendar .fc-daygrid-day-top {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 2px 4px 6px 4px !important;
        }

        .filament-fullcalendar .fc-daygrid-day-number {
            font-size: 0.85rem !important;
            font-weight: 800 !important;
            color: #475569 !important;
            width: 28px !important;
            height: 28px !important;
            border-radius: 9999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
        }

        html.dark .filament-fullcalendar .fc-daygrid-day-number {
            color: #cbd5e1 !important;
        }

        .filament-fullcalendar .fc-day-today .fc-daygrid-day-number {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.45) !important;
        }

        /* ===================================================================
           4. 3D EVENT PILLS INSIDE BOXES
           =================================================================== */
        .filament-fullcalendar .fc-event {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin-bottom: 5px !important;
            box-shadow: none !important;
        }

        /* Spanning Campaign Bars: 3D Gradient Cylinder */
        .filament-fullcalendar .modern-campaign-pill {
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            border-radius: 0.75rem !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            box-shadow: 0 4px 10px -2px rgba(37, 99, 235, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.35) !important;
            margin-bottom: 5px !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .filament-fullcalendar .modern-campaign-pill:hover {
            transform: translateY(-2px) scale(1.02) !important;
            box-shadow: 0 8px 16px -2px rgba(37, 99, 235, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.5) !important;
            filter: brightness(1.1) !important;
        }

        /* Deliverable Pill Micro-Card */
        .filament-fullcalendar .modern-deliverable-pill {
            background: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 3px 6px -1px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
            margin-bottom: 5px !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        html.dark .filament-fullcalendar .modern-deliverable-pill {
            background: #2a2a30 !important;
            border: 1px solid rgba(255, 255, 255, 0.09) !important;
            box-shadow: 0 4px 10px -2px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
        }

        .filament-fullcalendar .modern-deliverable-pill:hover {
            transform: translateY(-2px) scale(1.02) !important;
            border-color: #818cf8 !important;
            box-shadow: 0 10px 20px -3px rgba(99, 102, 241, 0.25), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
        }

        html.dark .filament-fullcalendar .modern-deliverable-pill:hover {
            transform: translateY(-2px) scale(1.02) !important;
            border-color: #a5b4fc !important;
            background: #32323a !important;
            box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.8), 0 4px 10px -2px rgba(99, 102, 241, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
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

        /* List view card styling */
        .filament-fullcalendar .fc-list {
            border: none !important;
            background: transparent !important;
        }

        .filament-fullcalendar .fc-list-table {
            border-spacing: 0 8px !important;
            border-collapse: separate !important;
        }

        .filament-fullcalendar .fc-list-event {
            background: #ffffff !important;
            border-radius: 1rem !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            transition: all 0.2s ease !important;
        }

        html.dark .filament-fullcalendar .fc-list-event {
            background: #222228 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.5) !important;
        }

        .filament-fullcalendar .fc-list-event:hover {
            transform: translateX(4px) !important;
            border-color: #818cf8 !important;
        }
    </style>
</x-filament-panels::page>
