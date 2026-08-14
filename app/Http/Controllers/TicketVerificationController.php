<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketVerificationController extends Controller
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

        if ($token) {
            $ticket = Ticket::where('verification_token', $token)
                ->with(['tournament', 'ticketPurchase', 'verifiedBy'])
                ->first();
        }

        return view('tickets.verify', [
            'ticket' => $ticket,
            'token' => $token,
            'user' => Auth::guard('web')->user(),
        ]);
    }

    /**
     * Mark a ticket as attended via AJAX / Form submission.
     */
    public function markAttended(Request $request, string $token): JsonResponse
    {
        // Ground staff or admins must be authenticated with permission or staff role
        $user = Auth::guard('web')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please log in as event staff to verify tickets.',
            ], 401);
        }

        $method = $request->input('method', 'qr_scan');
        $result = $this->ticketService->markTicketAttended($token, $user, $method);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Download the PDF tickets for a purchase.
     */
    public function downloadPdf(TicketPurchase $purchase)
    {
        // Admin authorization: must be logged in as web user
        if (! Auth::guard('web')->check()) {
            abort(403, 'Unauthorized access.');
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
        if (! Auth::guard('web')->check()) {
            abort(403, 'Unauthorized access.');
        }

        if (! $purchase->payment_receipt_path || ! Storage::disk('local')->exists($purchase->payment_receipt_path)) {
            abort(404, 'Receipt file not found.');
        }

        return Storage::disk('local')->response($purchase->payment_receipt_path);
    }
}
