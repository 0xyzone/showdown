<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketService
{
    /**
     * Issue tickets for a confirmed paid purchase within a database transaction.
     */
    public function issueTicketsForPurchase(TicketPurchase $purchase): TicketPurchase
    {
        return DB::transaction(function () use ($purchase) {
            // Lock purchase row for atomic update
            $lockedPurchase = TicketPurchase::where('id', $purchase->id)->lockForUpdate()->first();

            if ($lockedPurchase->payment_status !== 'paid') {
                $lockedPurchase->payment_status = 'paid';
                $lockedPurchase->paid_at = $lockedPurchase->paid_at ?? now();
                $lockedPurchase->save();
            }

            // Generate individual ticket records if not already existing
            $existingCount = $lockedPurchase->tickets()->count();
            $neededCount = $lockedPurchase->quantity - $existingCount;

            for ($i = 0; $i < $neededCount; $i++) {
                Ticket::create([
                    'ticket_purchase_id' => $lockedPurchase->id,
                    'tournament_id' => $lockedPurchase->tournament_id,
                    'ticket_number' => 'TCK-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4)),
                    'verification_token' => (string) Str::uuid(),
                    'customer_name' => $lockedPurchase->customer_name,
                    'customer_phone' => $lockedPurchase->customer_phone,
                    'price' => $lockedPurchase->unit_price,
                    'status' => 'valid',
                    'is_used' => false,
                ]);
            }

            return $lockedPurchase->fresh(['tickets', 'tournament', 'createdBy']);
        });
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
        $purchase->loadMissing(['tickets', 'tournament', 'createdBy']);

        $ticketsWithQr = $purchase->tickets->map(function ($ticket) {
            $qrData = $this->generateQrCodeBase64($ticket->verification_url);

            return [
                'ticket' => $ticket,
                'qr_base64' => $qrData,
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
     * Mark a ticket as attended with atomic database locking to prevent double check-ins.
     *
     * @return array{success: bool, message: string, ticket: ?Ticket}
     */
    public function markTicketAttended(string $token, ?User $staffUser = null, string $method = 'qr_scan'): array
    {
        return DB::transaction(function () use ($token, $staffUser, $method) {
            $ticket = Ticket::where('verification_token', $token)
                ->with(['tournament', 'ticketPurchase'])
                ->lockForUpdate()
                ->first();

            if (! $ticket) {
                return [
                    'success' => false,
                    'message' => 'Invalid ticket token. No matching ticket record exists.',
                    'ticket' => null,
                ];
            }

            if ($ticket->is_used || $ticket->status === 'used') {
                return [
                    'success' => false,
                    'message' => 'Ticket has already been used and checked in.',
                    'ticket' => $ticket,
                ];
            }

            if ($ticket->status === 'cancelled') {
                return [
                    'success' => false,
                    'message' => 'Ticket has been cancelled and cannot be used.',
                    'ticket' => $ticket,
                ];
            }

            // Atomic mark as used
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
            ];
        });
    }
}
