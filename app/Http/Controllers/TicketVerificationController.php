<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketVerificationController
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    /**
     * Display the standalone event staff ticket verification page.
     */
    public function show(Request $request, ?string $token = null)
    {
        $ticket = null;
        $activeEventDay = null;
        $allEventDays = collect();
        $isTodayValid = true;
        $isCheckedInToday = false;
        $todayAttendance = null;

        if ($token) {
            $ticket = Ticket::where('verification_token', $token)
                ->with(['tournament.eventDays', 'validEventDays', 'ticketPurchase', 'ticketPackage', 'attendances.eventDay', 'attendances.verifiedBy', 'verifiedBy'])
                ->first();

            if ($ticket && $ticket->tournament) {
                $allEventDays = $ticket->tournament->eventDays;
                $activeEventDay = $this->ticketService->getActiveEventDayForTournament($ticket->tournament);

                if ($activeEventDay) {
                    $isTodayValid = $ticket->isValidForDay($activeEventDay);
                    $todayAttendance = $ticket->attendances->firstWhere('tournament_event_day_id', $activeEventDay->id);
                    $isCheckedInToday = $todayAttendance !== null;
                } else {
                    $isCheckedInToday = $ticket->is_used;
                }
            }
        }

        return view('tickets.verify', [
            'ticket' => $ticket,
            'token' => $token,
            'user' => Auth::guard('web')->user(),
            'activeEventDay' => $activeEventDay,
            'allEventDays' => $allEventDays,
            'isTodayValid' => $isTodayValid,
            'isCheckedInToday' => $isCheckedInToday,
            'todayAttendance' => $todayAttendance,
        ]);
    }

    /**
     * Mark a ticket as attended for today's event day.
     */
    public function markAttended(Request $request, string $token): JsonResponse
    {
        $user = Auth::guard('web')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please log in as gate staff to verify tickets.',
            ], 401);
        }

        // Check if user has permission to verify tickets
        $canVerify = $user->hasRole('super_admin')
            || $user->hasRole('ticket_verification_staff')
            || $user->hasRole('ground_staff')
            || $user->can('Verify:Ticket')
            || $user->can('verify_tickets')
            || $user->can('View:Ticket');

        if (! $canVerify) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to verify admission tickets.',
            ], 403);
        }

        $eventDayId = $request->input('event_day_id') ? (int) $request->input('event_day_id') : null;
        $method = $request->input('method', 'qr_scan');

        $result = $this->ticketService->markTicketAttended($token, $user, $eventDayId, $method);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Download the PDF tickets for a purchase.
     */
    public function downloadPdf(TicketPurchase $purchase)
    {
        $user = Auth::guard('web')->user();

        if (! $user) {
            abort(403, 'Unauthorized access.');
        }

        // If ticket sales staff, ensure they own the purchase or have super_admin
        if (! $user->hasRole('super_admin') && $purchase->seller_id && $purchase->seller_id !== $user->id) {
            if (! $user->can('ViewAny:TicketPurchase') && ! $user->can('View:TicketPurchase')) {
                abort(403, 'You are not authorized to download tickets sold by another staff member.');
            }
        }

        if ($purchase->payment_status !== 'paid') {
            abort(400, 'Cannot generate ticket PDF for unpaid purchases.');
        }

        $pdf = $this->ticketService->generateTicketPdf($purchase);

        return $pdf->download("tickets-{$purchase->order_number}.pdf");
    }

    /**
     * Securely download or view an uploaded payment receipt.
     */
    public function downloadReceipt(TicketPurchase $purchase)
    {
        $user = Auth::guard('web')->user();

        if (! $user) {
            abort(403, 'Unauthorized access.');
        }

        if (! $purchase->payment_receipt_path || ! Storage::disk('local')->exists($purchase->payment_receipt_path)) {
            abort(404, 'Receipt file not found.');
        }

        return Storage::disk('local')->response($purchase->payment_receipt_path);
    }
}
