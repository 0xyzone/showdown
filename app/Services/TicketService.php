<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketAttendance;
use App\Models\TicketPackage;
use App\Models\TicketPurchase;
use App\Models\Tournament;
use App\Models\TournamentEventDay;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketService
{
    /**
     * Issue tickets for a confirmed paid purchase within a database transaction.
     *
     * @param  array<int>  $customEventDayIds
     */
    public function issueTicketsForPurchase(TicketPurchase $purchase, array $customEventDayIds = []): TicketPurchase
    {
        return DB::transaction(function () use ($purchase, $customEventDayIds) {
            // Lock purchase row for atomic update
            $lockedPurchase = TicketPurchase::where('id', $purchase->id)->lockForUpdate()->first();

            if ($lockedPurchase->payment_status !== 'paid') {
                $lockedPurchase->payment_status = 'paid';
                $lockedPurchase->paid_at = $lockedPurchase->paid_at ?? now();
            }

            if (empty($lockedPurchase->seller_id)) {
                $lockedPurchase->seller_id = auth()->id() ?? $lockedPurchase->created_by;
            }

            // Sync package name snapshot if package is assigned
            if ($lockedPurchase->ticket_package_id && empty($lockedPurchase->package_name)) {
                $package = TicketPackage::find($lockedPurchase->ticket_package_id);
                $lockedPurchase->package_name = $package?->name;
            }

            $lockedPurchase->save();

            // Determine valid event days for these tickets
            $eventDayIds = $this->resolveValidEventDayIds($lockedPurchase, $customEventDayIds);

            // Generate individual ticket records if not already existing
            $existingCount = $lockedPurchase->tickets()->count();
            $neededCount = $lockedPurchase->quantity - $existingCount;

            for ($i = 0; $i < $neededCount; $i++) {
                $ticket = Ticket::create([
                    'ticket_purchase_id' => $lockedPurchase->id,
                    'tournament_id' => $lockedPurchase->tournament_id,
                    'ticket_package_id' => $lockedPurchase->ticket_package_id,
                    'package_name' => $lockedPurchase->package_name,
                    'ticket_number' => 'TCK-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4)),
                    'verification_token' => (string) Str::uuid(),
                    'customer_name' => $lockedPurchase->customer_name,
                    'customer_phone' => $lockedPurchase->customer_phone,
                    'price' => $lockedPurchase->unit_price,
                    'status' => 'valid',
                    'is_used' => false,
                ]);

                if (! empty($eventDayIds)) {
                    $ticket->validEventDays()->sync($eventDayIds);
                }
            }

            return $lockedPurchase->fresh(['tickets.validEventDays', 'tournament.eventDays', 'createdBy', 'seller', 'ticketPackage']);
        });
    }

    /**
     * Resolve the array of event day IDs that tickets in this purchase should be valid for.
     */
    protected function resolveValidEventDayIds(TicketPurchase $purchase, array $customEventDayIds = []): array
    {
        if (! empty($customEventDayIds)) {
            return $customEventDayIds;
        }

        if ($purchase->ticket_package_id) {
            $package = TicketPackage::with('eventDays')->find($purchase->ticket_package_id);
            if ($package && $package->eventDays->isNotEmpty()) {
                return $package->eventDays->pluck('id')->toArray();
            }
        }

        // Default: valid for all active event days of the tournament
        return TournamentEventDay::where('tournament_id', $purchase->tournament_id)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Generate inline Base64 SVG QR code for a given verification URL.
     */
    public function generateQrCodeBase64(string $url): string
    {
        $svg = QrCode::format('svg')
            ->size(160)
            ->margin(1)
            ->color(16, 185, 129)
            ->generate($url);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Generate and download / stream ticket PDF for a purchase.
     */
    public function generateTicketPdf(TicketPurchase $purchase)
    {
        $purchase->loadMissing(['tickets.validEventDays', 'tournament.eventDays', 'createdBy', 'seller', 'ticketPackage']);

        $ticketsWithQr = $purchase->tickets->map(function ($ticket) {
            $qrData = $this->generateQrCodeBase64($ticket->verification_url);

            return [
                'ticket' => $ticket,
                'qr_base64' => $qrData,
                'valid_days_text' => $this->formatValidDaysText($ticket),
            ];
        });

        $pdf = Pdf::loadView('tickets.pdf', [
            'purchase' => $purchase,
            'tournament' => $purchase->tournament,
            'ticketsWithQr' => $ticketsWithQr,
        ])->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Format human-readable string of valid event days for a ticket.
     */
    public function formatValidDaysText(Ticket $ticket): string
    {
        if ($ticket->validEventDays->isEmpty()) {
            return 'All Event Days (Full Tournament Access)';
        }

        return $ticket->validEventDays->map(function ($day) {
            return "{$day->day_name} (".($day->event_date ? $day->event_date->format('M d, Y') : '').')';
        })->join(' • ');
    }

    /**
     * Get the active event day for a tournament for today's date (or closest/selected).
     */
    public function getActiveEventDayForTournament(Tournament $tournament, ?string $date = null): ?TournamentEventDay
    {
        $targetDate = $date ? Carbon::parse($date)->toDateString() : now(config('app.timezone', 'Asia/Kathmandu'))->toDateString();

        // 1. Exact match on event_date
        $eventDay = TournamentEventDay::where('tournament_id', $tournament->id)
            ->whereDate('event_date', $targetDate)
            ->where('is_active', true)
            ->first();

        if ($eventDay) {
            return $eventDay;
        }

        // 2. If only one event day exists, use it
        $days = TournamentEventDay::where('tournament_id', $tournament->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('event_date')
            ->get();

        if ($days->count() === 1) {
            return $days->first();
        }

        // 3. Find first upcoming or latest event day
        return $days->first();
    }

    /**
     * Mark a ticket as attended with atomic database locking to prevent double check-ins.
     *
     * @return array{success: bool, message: string, ticket: ?Ticket, attendance: ?TicketAttendance, event_day: ?TournamentEventDay}
     */
    public function markTicketAttended(string $token, ?User $staffUser = null, ?int $eventDayId = null, string $method = 'qr_scan'): array
    {
        return DB::transaction(function () use ($token, $staffUser, $eventDayId, $method) {
            $ticket = Ticket::where('verification_token', $token)
                ->with(['tournament.eventDays', 'validEventDays', 'ticketPurchase', 'attendances.eventDay', 'ticketPackage'])
                ->lockForUpdate()
                ->first();

            if (! $ticket) {
                return [
                    'success' => false,
                    'message' => 'Invalid ticket token. No matching ticket record exists.',
                    'ticket' => null,
                    'attendance' => null,
                    'event_day' => null,
                ];
            }

            if ($ticket->status === 'cancelled') {
                return [
                    'success' => false,
                    'message' => 'Ticket has been cancelled and cannot be used for admission.',
                    'ticket' => $ticket,
                    'attendance' => null,
                    'event_day' => null,
                ];
            }

            // Determine the target event day
            $eventDay = null;
            if ($eventDayId) {
                $eventDay = TournamentEventDay::find($eventDayId);
            } else {
                $eventDay = $this->getActiveEventDayForTournament($ticket->tournament);
            }

            // If the tournament has event days configured
            if ($eventDay) {
                // Check if ticket is valid for this specific event day
                if (! $ticket->isValidForDay($eventDay)) {
                    $validDaysNames = $ticket->validEventDays->pluck('day_name')->join(', ');

                    return [
                        'success' => false,
                        'message' => "Ticket is NOT valid for today's event ({$eventDay->day_name}). Valid for: ".($validDaysNames ?: 'Other specific days'),
                        'ticket' => $ticket,
                        'attendance' => null,
                        'event_day' => $eventDay,
                    ];
                }

                // Check if already checked in for this event day
                $existingAttendance = TicketAttendance::where('ticket_id', $ticket->id)
                    ->where('tournament_event_day_id', $eventDay->id)
                    ->first();

                if ($existingAttendance) {
                    $time = $existingAttendance->verified_at ? $existingAttendance->verified_at->timezone(config('app.timezone', 'Asia/Kathmandu'))->format('h:i A') : '';

                    return [
                        'success' => false,
                        'message' => "Ticket has ALREADY been checked in for {$eventDay->day_name} (at {$time}). Double entry prevented.",
                        'ticket' => $ticket,
                        'attendance' => $existingAttendance,
                        'event_day' => $eventDay,
                    ];
                }

                // Create attendance record
                $attendance = TicketAttendance::create([
                    'ticket_id' => $ticket->id,
                    'tournament_event_day_id' => $eventDay->id,
                    'verified_by' => $staffUser?->id,
                    'verified_at' => now(),
                    'verification_method' => $method,
                ]);

                // Update ticket summary status
                $ticket->is_used = true;
                $ticket->used_at = now();
                $ticket->verified_by = $staffUser?->id;
                $ticket->verification_method = $method;

                // If all valid days have been checked in, mark overall status as used
                $totalValidDaysCount = $ticket->validEventDays->count();
                $attendedDaysCount = TicketAttendance::where('ticket_id', $ticket->id)->count();

                if ($totalValidDaysCount > 0 && $attendedDaysCount >= $totalValidDaysCount) {
                    $ticket->status = 'used';
                }

                $ticket->save();

                return [
                    'success' => true,
                    'message' => "Ticket verified successfully for {$eventDay->day_name}! Attendance recorded.",
                    'ticket' => $ticket->fresh(['tournament', 'ticketPurchase', 'verifiedBy', 'attendances.eventDay']),
                    'attendance' => $attendance,
                    'event_day' => $eventDay,
                ];
            }

            // Fallback for tournaments without configured event days: single check-in logic
            if ($ticket->is_used || $ticket->status === 'used') {
                return [
                    'success' => false,
                    'message' => 'Ticket has already been used and checked in.',
                    'ticket' => $ticket,
                    'attendance' => null,
                    'event_day' => null,
                ];
            }

            $ticket->is_used = true;
            $ticket->status = 'used';
            $ticket->used_at = now();
            $ticket->verified_by = $staffUser?->id;
            $ticket->verification_method = $method;
            $ticket->save();

            return [
                'success' => true,
                'message' => 'Ticket verified successfully. Attendance recorded.',
                'ticket' => $ticket->fresh(['tournament', 'ticketPurchase', 'verifiedBy']),
                'attendance' => null,
                'event_day' => null,
            ];
        });
    }
}
