# SHOWDOWN (Outlaw Showcase) — Architecture & Developer Memory

> **Purpose**: This memory document provides comprehensive architectural context, domain knowledge, data relationships, business rules, API/route maps, and design conventions for AI agents and developers working on this codebase.

---

## 1. Technical Stack & Foundation

- **Framework**: Laravel 13.x (PHP 8.4)
- **Admin Panel**: Filament PHP v5 (panel ID: `maidan`, URL path: `/maidan`)
- **Panel Provider**: [`app/Providers/Filament/MaidanPanelProvider.php`](file:///Applications/MAMP/htdocs/showdown/app/Providers/Filament/MaidanPanelProvider.php)
- **Authentication & Roles**: Filament Shield (Spatie Laravel-Permission with `guard_name = 'web'`)
- **Frontend / Styling**: Tailwind CSS v4, Blade templates, Alpine.js, Vanilla CSS, Google Fonts (`Outfit` & `JetBrains Mono`)
- **Spreadsheet Engine**: `maatwebsite/excel` (PhpSpreadsheet)
- **Cryptography**: Standard OpenSSL for W3C WebAuthn Level 2/3 (ECDSA P-256 / secp256r1 & RSA)
- **Timezone**: `Asia/Kathmandu` (`APP_TIMEZONE='Asia/Kathmandu'`)
- **Test Suite**: PHPUnit 12.x in `tests/Feature` and `tests/Unit` (strict rule: PHPUnit classes only, no Pest syntax)

---

## 2. Core Domain Subsystems

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                               SHOWDOWN PLATFORM                                │
├────────────────────────┬──────────────────────────┬─────────────────────────────┤
│ 1. Public Portal (/)   │ 2. Gate & Tickets        │ 3. Staff & Attendance       │
│ - Modern Esports UI    │ - Tiered Packages        │ - Dedicated /attendance     │
│ - Active Tournaments   │ - Multi-Day Event Sched  │ - WebAuthn Biometrics/Pass  │
│ - Sponsors & Partners  │ - Fast Cashier Sales     │ - Haversine GPS Geofencing  │
│ - Contenders & Rosters │ - Standalone Gate Check  │ - Timesheet Excel Exports   │
├────────────────────────┴──────────────────────────┴─────────────────────────────┤
│ 4. Admin Panel (/maidan)                                                        │
│ - Attendance Topbar Badge & Top Dashboard Widgets                              │
│ - Official Members Management (Auto-generated passwords, Force Password Change) │
│ - Financial Tracking (Income & Expenses)                                        │
│ - Filament Shield Permissions & Scoped Role Access                              │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

### A. Public Homepage (`/`)
- **File**: [`resources/views/welcome.blade.php`](file:///Applications/MAMP/htdocs/showdown/resources/views/welcome.blade.php)
- **Style**: Esports tournament landing page (slate/zinc `#090d16`, amber `#f59e0b`, emerald `#10b981`, and cyan accents).
- **Features**:
  - Live tournament countdown timer.
  - Active tournament spotlight with prizepool distribution.
  - Supported game titles roster (5v5 MOBA, Battle Royale, FPS, 1v1).
  - Categorized sponsors and partners grid.
  - Approved contender teams showcase.
  - *Note*: Challonge has been completely removed from the entire application.

---

### B. Tournaments & Multi-Day Schedules
- **Models**:
  - [`Tournament`](file:///Applications/MAMP/htdocs/showdown/app/Models/Tournament.php): Main event (`name`, `start_date`, `end_date`, `prize_pool_total`, `is_active`, `status`).
  - [`TournamentEventDay`](file:///Applications/MAMP/htdocs/showdown/app/Models/TournamentEventDay.php): Specific scheduled event days (e.g., *Day 1 - Group Stage*, *Day 2 - Semifinals*, *Finals Day*).
  - [`GameTitle`](file:///Applications/MAMP/htdocs/showdown/app/Models/GameTitle.php): Games attached to tournaments (`game_type`, `team_size`, `min_players`, `max_players`).
  - [`Team`](file:///Applications/MAMP/htdocs/showdown/app/Models/Team.php) & [`TeamPlayer`](file:///Applications/MAMP/htdocs/showdown/app/Models/TeamPlayer.php): Teams and rosters registered for tournaments.
  - [`TournamentRegistration`](file:///Applications/MAMP/htdocs/showdown/app/Models/TournamentRegistration.php): Registrations with approval workflows (`pending`, `approved`, `rejected`, `disqualified`).
- **Filament Resources**:
  - `Tournaments` (includes inline repeater for `TournamentEventDay` schedule in [`TournamentForm.php`](file:///Applications/MAMP/htdocs/showdown/app/Filament/Resources/Tournaments/Schemas/TournamentForm.php)).
  - `GameTitles`, `TournamentRegistrations`, `Sponsors`, `Partners`, `SponsorQueries`.

---

### C. Tournament Ticket Management, Sales & Gate Verification
- **Models**:
  - [`TicketPackage`](file:///Applications/MAMP/htdocs/showdown/app/Models/TicketPackage.php): Tiered ticket packages (VIP Pass, Weekend Pass, Single Day) linked to valid `TournamentEventDay` records via `ticket_package_event_day`.
  - [`PaymentMethod`](file:///Applications/MAMP/htdocs/showdown/app/Models/PaymentMethod.php): Payment options (eSewa, Khalti, Bank Transfer, Cash) linked to tournaments via `payment_method_tournament`.
  - [`TicketPurchase`](file:///Applications/MAMP/htdocs/showdown/app/Models/TicketPurchase.php): Cashier sales transaction (`order_number`, `customer_name`, `customer_phone`, `quantity`, `seller_id`, `payment_status`, `receipt_image_path`).
  - [`Ticket`](file:///Applications/MAMP/htdocs/showdown/app/Models/Ticket.php): Individual admission tickets with secure unique QR verification tokens (`ticket_number`, `qr_token`, `status`).
  - [`TicketAttendance`](file:///Applications/MAMP/htdocs/showdown/app/Models/TicketAttendance.php): Per-day check-in log with a unique constraint on `(ticket_id, tournament_event_day_id)` preventing double entry on the same event day.
- **Cashier Sales Flow**:
  - Located at `/maidan/ticket-purchases/create` ([`TicketPurchaseForm.php`](file:///Applications/MAMP/htdocs/showdown/app/Filament/Resources/TicketPurchases/Schemas/TicketPurchaseForm.php)).
  - Seller is automatically captured as `auth()->id()` (never manually selectable).
  - Cashiers only see their own sales transactions unless they hold `super_admin`.
- **Standalone Gate Verification Terminal**:
  - **Route**: `GET /ticket/verify/{token?}` and `POST /ticket/verify/{token}/check-in` ([`TicketVerificationController.php`](file:///Applications/MAMP/htdocs/showdown/app/Http/Controllers/TicketVerificationController.php)).
  - Checks: (1) Ticket validity, (2) Match with today's active event day, (3) Double check-in prevention using row locks.
- **Reporting & Excel Export**:
  - [`TicketReportsPage.php`](file:///Applications/MAMP/htdocs/showdown/app/Filament/Pages/TicketReportsPage.php) & [`TicketSalesReportExport.php`](file:///Applications/MAMP/htdocs/showdown/app/Exports/TicketSalesReportExport.php).

---

### D. Staff Attendance & Timesheet System
- **Models**:
  - [`AttendanceSetting`](file:///Applications/MAMP/htdocs/showdown/app/Models/AttendanceSetting.php): Singleton defining office coordinates (`office_latitude`, `office_longitude`), `allowed_radius_meters` (default 150m), `max_gps_accuracy_meters` (default 100m), `require_biometric`, `max_devices_per_user` (3), and IP allowlists.
  - [`StaffAttendanceProfile`](file:///Applications/MAMP/htdocs/showdown/app/Models/StaffAttendanceProfile.php): Individual policy mode (`office_only`, `remote_allowed`, `office_and_network`, `flexible`) and `is_biometric_exempt`.
  - [`StaffBiometricCredential`](file:///Applications/MAMP/htdocs/showdown/app/Models/StaffBiometricCredential.php): Stored W3C WebAuthn passkey public keys in PEM format, AAGUID, counter, and transports. No raw biometric templates are ever stored.
  - [`StaffAttendance`](file:///Applications/MAMP/htdocs/showdown/app/Models/StaffAttendance.php): Daily attendance records (`punch_in_at`, `punch_out_at`, `worked_minutes`, `status`, `location_mode`, geolocation audit data, manual correction metadata).
  - [`StaffPunchEvent`](file:///Applications/MAMP/htdocs/showdown/app/Models/StaffPunchEvent.php): Immutable security audit log of every punch attempt, distance check, device registration, and failure reason.
- **Dedicated Staff Attendance Terminal**:
  - **Route**: `/attendance` (protected by `auth:web`, redirects guests to `/maidan/login`).
  - **View**: [`resources/views/attendance/index.blade.php`](file:///Applications/MAMP/htdocs/showdown/resources/views/attendance/index.blade.php).
  - Real-time digital clock, geofence distance calculation, active worked elapsed timer (`00:00:xx`), biometric registration modal (up to 3 devices), and 10-day history.
- **Cryptographic WebAuthn Engine**:
  - [`WebAuthnService.php`](file:///Applications/MAMP/htdocs/showdown/app/Services/WebAuthnService.php): Implements RFC 8949 recursive CBOR decoding, COSE EC2 (ECDSA P-256 / prime256v1) and RSA parsing, ASN.1 DER signature normalization, and OpenSSL signature verification against `$authenticatorData . sha256(clientDataJSON)`.
- **Admin Panel Resources & Reports**:
  - `StaffAttendanceResource` (scoped table with audited manual correction action modal).
  - `StaffAttendanceProfileResource` (configure staff policies).
  - `AttendanceSettingsPage` (configure office geofence & biometric rules).
  - `StaffPunchEventResource` (security audit log viewer).
  - `AttendanceReportsPage` & [`StaffAttendanceReportExport.php`](file:///Applications/MAMP/htdocs/showdown/app/Exports/StaffAttendanceReportExport.php).

---

### E. Official Members & Password Lifecycle
- **Resource**: `UserResource` labeled as **"Official Members"** in Filament.
- **Password Creation**:
  - Passwords are automatically generated (12-char random string) and emailed to the new member via [`OfficialMemberCredentialsMail.php`](file:///Applications/MAMP/htdocs/showdown/app/Mail/OfficialMemberCredentialsMail.php).
  - `must_change_password` flag is set to `true`.
  - On initial login, [`ForcePasswordChangeMiddleware.php`](file:///Applications/MAMP/htdocs/showdown/app/Http/Middleware/ForcePasswordChangeMiddleware.php) intercepts requests and directs users to [`ForcePasswordChange.php`](file:///Applications/MAMP/htdocs/showdown/app/Filament/Pages/ForcePasswordChange.php).
- **Default Role & Profile Assignment**:
  - [`UserObserver.php`](file:///Applications/MAMP/htdocs/showdown/app/Observers/UserObserver.php) and [`UserForm.php`](file:///Applications/MAMP/htdocs/showdown/app/Filament/Resources/Users/Schemas/UserForm.php) automatically assign the `staff` role and initialize a `StaffAttendanceProfile` for every created member.

---

## 3. Topbar & Dashboard Layout Configuration

### Top Navigation Bar
- **Global Search**: Disabled via `->globalSearch(false)` in `MaidanPanelProvider`.
- **Attendance Status Indicator**: Registered via `PanelsRenderHook::GLOBAL_SEARCH_AFTER` to appear **before the database notifications bell icon**.
  - Component: [`resources/views/filament/components/topbar-attendance-badge.blade.php`](file:///Applications/MAMP/htdocs/showdown/resources/views/filament/components/topbar-attendance-badge.blade.php).
  - Shows dynamic status (*Clocked In*, *Clocked Out*, or *Not Clocked In*) and links directly to `/attendance`.

### Dashboard Widget Ordering
Widgets in `app/Filament/Widgets` use explicit `$sort` priority:
1. `AttendanceOverviewStats.php` / `MyAttendanceStatsWidget.php` &rarr; `$sort = -20` (Top row)
2. `CurrentlyWorkingStaffWidget.php` &rarr; `$sort = -15` (Second row full table)
3. `TournamentStatsOverview.php` &rarr; `$sort = 10`
4. `TicketSalesStatsOverview.php` &rarr; `$sort = 12`
5. `FinanceOverviewStats.php` &rarr; `$sort = 15`
6. `IncomeExpenseChart.php` &rarr; `$sort = 20`
7. `RecentTicketSalesWidget.php` &rarr; `$sort = 25`
8. `LatestRegistrationsWidget.php` &rarr; `$sort = 30`

---

## 4. Filament Shield Roles & Permissions Map

| Role Name | Core Responsibilities |
|---|---|
| `super_admin` | Unrestricted full access across all tournaments, finances, settings, sales, staff, and audit trails. |
| `attendance_manager` | Manage staff attendance profiles, view all attendances, perform manual corrections, update office settings, and export timesheet reports. |
| `ticket_sales_staff` | Create sales orders, view own sales/tickets, download receipts/PDFs, view own sales KPIs. |
| `ticket_verification_staff` | Access gate check-in terminal (`/ticket/verify`), scan QR passes, validate event days, check in attendees. |
| `staff` | Punch in/out at `/attendance`, register passkey devices, view own attendance history and own dashboard widget. |

---

## 5. Route Map

| Method | URI | Name | Access / Middleware | Purpose |
|---|---|---|---|---|
| `GET` | `/` | — | Public | Esports Tournament Landing Page |
| `GET` | `/login` | `login` | Public | Fallback redirect to `/maidan/login` |
| `GET` | `/maidan` | `filament.maidan.pages.dashboard` | `auth:web` | Admin & Staff Dashboard |
| `GET` | `/maidan/login` | `filament.maidan.auth.login` | Guest | Admin Panel Login |
| `GET` | `/attendance` | `attendance.index` | `auth:web` | Staff Attendance Terminal |
| `POST` | `/attendance/punch-in` | `attendance.punch-in` | `auth:web` | Staff Punch In |
| `POST` | `/attendance/punch-out` | `attendance.punch-out` | `auth:web` | Staff Punch Out |
| `POST` | `/attendance/webauthn/register/options` | `attendance.webauthn.register.options` | `auth:web` | Passkey Registration Challenge |
| `POST` | `/attendance/webauthn/register/verify` | `attendance.webauthn.register.verify` | `auth:web` | Passkey Verification & Save |
| `POST` | `/attendance/webauthn/auth/options` | `attendance.webauthn.auth.options` | `auth:web` | Passkey Assertion Challenge |
| `DELETE` | `/attendance/devices/{credential}` | `attendance.devices.revoke` | `auth:web` | Deactivate Passkey Device |
| `GET` | `/ticket/verify/{token?}` | `ticket.verify` | `auth:web` | Standalone Gate QR Verification |
| `POST` | `/ticket/verify/{token}/check-in` | `ticket.check-in` | `auth:web` | Gate Admission Check-In |
| `GET` | `/admin/ticket-purchases/{purchase}/pdf` | `admin.ticket-purchases.pdf` | `auth:web` | Download PDF Ticket Sheet |
| `GET` | `/admin/ticket-purchases/{purchase}/receipt` | `admin.ticket-purchases.receipt` | `auth:web` | Download Payment Receipt |

---

## 6. Coding Conventions & Important Gotchas

1. **Filament v4/v5 Namespaces**:
   - Layout components (`Section`, `Grid`, `Fieldset`, `Tabs`, `Wizard`) must be imported from `Filament\Schemas\Components\`, **NOT** `Filament\Forms\Components\`.
   - Form inputs (`TextInput`, `Select`, `Textarea`, `Toggle`, `TagsInput`, `FileUpload`) belong in `Filament\Forms\Components\`.
   - Actions must be imported from `Filament\Actions\`.
2. **Eloquent Query Builders in Filament**:
   - Always explicitly import `use Illuminate\Database\Eloquent\Builder;` when typing `$query` or `$q` closures in tables and widgets to avoid resolving to the local component namespace.
3. **Maatwebsite Excel Interface Compatibility**:
   - In classes implementing `FromCollection`, `WithMapping`, and `WithStyles`:
     - `collection(): \Illuminate\Support\Enumerable`
     - `styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): ?array`
     - `map(mixed $row): array`
4. **Row Locking on Atomic Actions**:
   - Both ticket check-ins (`TicketVerificationController`) and attendance punches (`StaffAttendanceService`) utilize `DB::transaction()` and `->lockForUpdate()` to prevent concurrent duplicate submissions.
5. **Code Quality & Testing**:
   - Always run `vendor/bin/pint --format agent` after modifying PHP files.
   - Always write and run feature tests in PHPUnit (`php artisan test`).

---

*Last Updated: 2026-08-16*
