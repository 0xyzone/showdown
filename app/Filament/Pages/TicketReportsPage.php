<?php

namespace App\Filament\Pages;

use App\Exports\TicketSalesReportExport;
use App\Models\Ticket;
use App\Models\TicketAttendance;
use App\Models\TicketPurchase;
use App\Models\Tournament;
use App\Models\TournamentEventDay;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TicketReportsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Ticket Reports & Analytics';

    protected static string|\UnitEnum|null $navigationGroup = 'Tournament Management';

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.ticket-reports-page';

    public ?int $tournament_id = null;

    public ?int $ticket_package_id = null;

    public ?int $seller_id = null;

    public ?string $payment_status = null;

    public ?int $payment_method_id = null;

    public ?string $date_from = null;

    public ?string $date_to = null;

    public ?int $event_day_id = null;

    public function mount(): void
    {
        $activeTournament = Tournament::where('is_active', true)->first();
        if ($activeTournament) {
            $this->tournament_id = $activeTournament->id;
        }
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasRole('super_admin')
            || $user->can('View:TicketReport')
            || $user->can('ViewAny:TicketPurchase');
    }

    public function resetFilters(): void
    {
        $this->tournament_id = null;
        $this->ticket_package_id = null;
        $this->seller_id = null;
        $this->payment_status = null;
        $this->payment_method_id = null;
        $this->date_from = null;
        $this->date_to = null;
        $this->event_day_id = null;
    }

    public function getFilteredPurchasesQuery()
    {
        $query = TicketPurchase::with(['tournament', 'seller', 'ticketPackage', 'paymentMethod', 'tickets'])
            ->orderBy('created_at', 'desc');

        if ($this->tournament_id) {
            $query->where('tournament_id', $this->tournament_id);
        }

        if ($this->ticket_package_id) {
            $query->where('ticket_package_id', $this->ticket_package_id);
        }

        if ($this->seller_id) {
            $query->where('seller_id', $this->seller_id);
        }

        if ($this->payment_status) {
            $query->where('payment_status', $this->payment_status);
        }

        if ($this->payment_method_id) {
            $query->where('payment_method_id', $this->payment_method_id);
        }

        if ($this->date_from) {
            $query->whereDate('created_at', '>=', Carbon::parse($this->date_from)->toDateString());
        }

        if ($this->date_to) {
            $query->whereDate('created_at', '<=', Carbon::parse($this->date_to)->toDateString());
        }

        return $query;
    }

    public function getSummaryStats(): array
    {
        $query = $this->getFilteredPurchasesQuery();
        $purchases = $query->get();

        $totalPurchases = $purchases->count();
        $paidPurchases = $purchases->where('payment_status', 'paid');
        $totalTicketsSold = $paidPurchases->sum('quantity');
        $totalRevenue = $paidPurchases->sum('total_amount');

        $purchaseIds = $purchases->pluck('id');
        $ticketsQuery = Ticket::whereIn('ticket_purchase_id', $purchaseIds);
        $totalTicketsCount = $ticketsQuery->count();
        $checkedInTicketsCount = (clone $ticketsQuery)->where('is_used', true)->count();
        $unusedTicketsCount = max(0, $totalTicketsCount - $checkedInTicketsCount);

        return [
            'total_purchases' => $totalPurchases,
            'total_tickets_sold' => $totalTicketsSold,
            'total_revenue' => $totalRevenue,
            'checked_in_count' => $checkedInTicketsCount,
            'unused_count' => $unusedTicketsCount,
        ];
    }

    public function getStaffSalesSummary(): array
    {
        $purchases = $this->getFilteredPurchasesQuery()->get();

        return $purchases->groupBy('seller_id')->map(function ($group) {
            $seller = $group->first()->seller ?? null;
            $paid = $group->where('payment_status', 'paid');

            return [
                'seller_name' => $seller?->name ?? 'Unassigned / System',
                'orders_count' => $group->count(),
                'tickets_sold' => $paid->sum('quantity'),
                'total_revenue' => $paid->sum('total_amount'),
            ];
        })->sortByDesc('total_revenue')->values()->toArray();
    }

    public function getPackageSalesSummary(): array
    {
        $purchases = $this->getFilteredPurchasesQuery()->get();

        return $purchases->groupBy('ticket_package_id')->map(function ($group) {
            $pkgName = $group->first()->package_name ?? ($group->first()->ticketPackage?->name ?? 'Standard Admission');
            $paid = $group->where('payment_status', 'paid');

            return [
                'package_name' => $pkgName,
                'orders_count' => $group->count(),
                'tickets_sold' => $paid->sum('quantity'),
                'total_revenue' => $paid->sum('total_amount'),
            ];
        })->sortByDesc('total_revenue')->values()->toArray();
    }

    public function getEventDayAttendanceSummary(): array
    {
        $tournamentId = $this->tournament_id;
        $eventDays = TournamentEventDay::when($tournamentId, fn ($q) => $q->where('tournament_id', $tournamentId))
            ->orderBy('event_date')
            ->get();

        return $eventDays->map(function ($day) {
            $checkedInCount = TicketAttendance::where('tournament_event_day_id', $day->id)->count();
            $validTicketsCount = DB::table('ticket_event_day')->where('tournament_event_day_id', $day->id)->count();

            return [
                'day_name' => $day->day_name,
                'event_date' => $day->event_date ? $day->event_date->format('M d, Y') : 'N/A',
                'valid_tickets' => $validTicketsCount,
                'checked_in' => $checkedInCount,
            ];
        })->toArray();
    }

    public function exportExcel()
    {
        $filters = [
            'tournament_id' => $this->tournament_id,
            'ticket_package_id' => $this->ticket_package_id,
            'seller_id' => $this->seller_id,
            'payment_status' => $this->payment_status,
            'payment_method_id' => $this->payment_method_id,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
        ];

        $filename = 'ticket-sales-report-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download(new TicketSalesReportExport($filters), $filename);
    }
}
