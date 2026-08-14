<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Models\Tournament;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Tournament $tournament;

    protected PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Gatekeeper',
            'email' => 'admin@showdown.test',
        ]);

        $this->tournament = Tournament::create([
            'name' => 'Championship 2026',
            'slug' => 'championship-2026',
            'season_version' => '2026 Vol-I',
            'ticket_price' => 200.00,
            'is_active' => true,
            'status' => 'registration_open',
        ]);

        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Cash Counter',
            'code' => 'cash',
            'account_details' => 'Main Gate Counter',
            'is_active' => true,
        ]);
    }

    public function test_paid_ticket_purchase_automatically_generates_tickets_with_unique_tokens(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'created_by' => $this->adminUser->id,
            'payment_method_id' => $this->paymentMethod->id,
            'customer_name' => 'Rohan Shrestha',
            'customer_phone' => '9801234567',
            'quantity' => 3,
            'unit_price' => 200.00,
            'total_amount' => 600.00,
            'payment_status' => 'paid',
            'payment_source' => 'Cash Counter',
            'paid_at' => now(),
        ]);

        app(TicketService::class)->issueTicketsForPurchase($purchase);

        $this->assertDatabaseCount('tickets', 3);

        $tickets = Ticket::where('ticket_purchase_id', $purchase->id)->get();
        $this->assertCount(3, $tickets);

        $tokens = $tickets->pluck('verification_token')->toArray();
        $this->assertCount(3, array_unique($tokens));

        foreach ($tickets as $ticket) {
            $this->assertEquals('valid', $ticket->status);
            $this->assertFalse($ticket->is_used);
            $this->assertNotNull($ticket->verification_token);
            $this->assertNotNull($ticket->ticket_number);
        }
    }

    public function test_unpaid_ticket_purchase_does_not_issue_usable_tickets(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'created_by' => $this->adminUser->id,
            'payment_method_id' => $this->paymentMethod->id,
            'customer_name' => 'Unpaid User',
            'customer_phone' => '9800000000',
            'quantity' => 2,
            'unit_price' => 200.00,
            'total_amount' => 400.00,
            'payment_status' => 'unpaid',
            'payment_source' => 'Cash Counter',
        ]);

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_ticket_verification_page_displays_ticket_information(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'created_by' => $this->adminUser->id,
            'customer_name' => 'Kiran Sharma',
            'customer_phone' => '9812345678',
            'quantity' => 1,
            'unit_price' => 200.00,
            'total_amount' => 200.00,
            'payment_status' => 'paid',
        ]);

        app(TicketService::class)->issueTicketsForPurchase($purchase);
        $ticket = Ticket::first();

        $response = $this->get(route('ticket.verify', ['token' => $ticket->verification_token]));

        $response->assertStatus(200);
        $response->assertSee('Championship 2026');
        $response->assertSee('Kiran Sharma');
        $response->assertSee($ticket->ticket_number);
        $response->assertSee('VALID ADMISSION TICKET');
    }

    public function test_staff_can_mark_ticket_as_attended_and_prevents_double_checkin(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'created_by' => $this->adminUser->id,
            'customer_name' => 'Suresh Thapa',
            'customer_phone' => '9841000000',
            'quantity' => 1,
            'unit_price' => 200.00,
            'total_amount' => 200.00,
            'payment_status' => 'paid',
        ]);

        app(TicketService::class)->issueTicketsForPurchase($purchase);
        $ticket = Ticket::first();

        // Check in as staff
        $response = $this->actingAs($this->adminUser, 'web')
            ->postJson(route('ticket.check-in', ['token' => $ticket->verification_token]), [
                'method' => 'qr_scan',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $ticket->refresh();
        $this->assertTrue($ticket->is_used);
        $this->assertEquals('used', $ticket->status);
        $this->assertEquals($this->adminUser->id, $ticket->verified_by);
        $this->assertNotNull($ticket->used_at);

        // Attempt second check in (double check in prevention)
        $secondAttempt = $this->actingAs($this->adminUser, 'web')
            ->postJson(route('ticket.check-in', ['token' => $ticket->verification_token]));

        $secondAttempt->assertStatus(422);
        $secondAttempt->assertJson([
            'success' => false,
            'message' => 'Ticket has already been used and checked in.',
        ]);
    }

    public function test_invalid_token_returns_proper_error(): void
    {
        $response = $this->get(route('ticket.verify', ['token' => 'invalid-uuid-token']));

        $response->assertStatus(200);
        $response->assertSee('INVALID TICKET');
    }

    public function test_admin_can_download_ticket_pdf_for_paid_purchase(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'created_by' => $this->adminUser->id,
            'customer_name' => 'Bikash Rana',
            'customer_phone' => '9851000000',
            'quantity' => 2,
            'unit_price' => 200.00,
            'total_amount' => 400.00,
            'payment_status' => 'paid',
        ]);

        app(TicketService::class)->issueTicketsForPurchase($purchase);

        $response = $this->actingAs($this->adminUser, 'web')
            ->get(route('admin.ticket-purchases.pdf', $purchase));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
