<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\Ticket;
use App\Models\TicketAttendance;
use App\Models\TicketPackage;
use App\Models\TicketPurchase;
use App\Models\Tournament;
use App\Models\TournamentEventDay;
use App\Models\User;
use App\Services\TicketService;
use Carbon\Carbon;
use Database\Seeders\TicketSystemRolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $salesStaff;

    protected User $verifyStaff;

    protected Tournament $tournament;

    protected TournamentEventDay $day1;

    protected TournamentEventDay $day2;

    protected TournamentEventDay $day3;

    protected TicketPackage $vipPackage;

    protected TicketPackage $singleDayPackage;

    protected PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TicketSystemRolesSeeder::class);

        $this->superAdmin = User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@showdown.test',
        ]);
        $this->superAdmin->assignRole('super_admin');

        $this->salesStaff = User::factory()->create([
            'name' => 'Jane Salesperson',
            'email' => 'sales@showdown.test',
        ]);
        $this->salesStaff->assignRole('ticket_sales_staff');

        $this->verifyStaff = User::factory()->create([
            'name' => 'Bob Gatekeeper',
            'email' => 'gate@showdown.test',
        ]);
        $this->verifyStaff->assignRole('ticket_verification_staff');

        $this->tournament = Tournament::create([
            'name' => 'Esports National Championship 2026',
            'slug' => 'esports-national-2026',
            'season_version' => '2026 Vol-I',
            'ticket_price' => 200.00,
            'is_active' => true,
            'status' => 'registration_open',
        ]);

        $this->day1 = TournamentEventDay::create([
            'tournament_id' => $this->tournament->id,
            'day_name' => 'Day 1 - Group Stage',
            'event_date' => Carbon::today(),
            'order' => 1,
            'is_active' => true,
        ]);

        $this->day2 = TournamentEventDay::create([
            'tournament_id' => $this->tournament->id,
            'day_name' => 'Day 2 - Semifinals',
            'event_date' => Carbon::today()->addDay(),
            'order' => 2,
            'is_active' => true,
        ]);

        $this->day3 = TournamentEventDay::create([
            'tournament_id' => $this->tournament->id,
            'day_name' => 'Day 3 - Grand Finals',
            'event_date' => Carbon::today()->addDays(2),
            'order' => 3,
            'is_active' => true,
        ]);

        $this->vipPackage = TicketPackage::create([
            'tournament_id' => $this->tournament->id,
            'name' => 'VIP 3-Day Pass',
            'price' => 600.00,
            'validity_type' => 'all_days',
            'is_active' => true,
            'order' => 1,
        ]);
        $this->vipPackage->eventDays()->sync([$this->day1->id, $this->day2->id, $this->day3->id]);

        $this->singleDayPackage = TicketPackage::create([
            'tournament_id' => $this->tournament->id,
            'name' => 'Day 1 Only Pass',
            'price' => 200.00,
            'validity_type' => 'single_day',
            'is_active' => true,
            'order' => 2,
        ]);
        $this->singleDayPackage->eventDays()->sync([$this->day1->id]);

        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Counter Cash',
            'code' => 'counter_cash',
            'account_details' => 'Main Ticket Counter',
            'is_active' => true,
        ]);
        $this->tournament->paymentMethods()->attach($this->paymentMethod->id);
    }

    public function test_paid_purchase_generates_tickets_with_package_and_valid_event_days(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'ticket_package_id' => $this->vipPackage->id,
            'package_name' => $this->vipPackage->name,
            'created_by' => $this->salesStaff->id,
            'seller_id' => $this->salesStaff->id,
            'payment_method_id' => $this->paymentMethod->id,
            'customer_name' => 'Rohan Shrestha',
            'customer_phone' => '9801234567',
            'quantity' => 2,
            'unit_price' => 600.00,
            'total_amount' => 1200.00,
            'payment_status' => 'paid',
            'payment_source' => 'Counter Cash',
            'paid_at' => now(),
        ]);

        app(TicketService::class)->issueTicketsForPurchase($purchase);

        $this->assertDatabaseCount('tickets', 2);

        $tickets = Ticket::where('ticket_purchase_id', $purchase->id)->get();
        $this->assertCount(2, $tickets);

        foreach ($tickets as $ticket) {
            $this->assertEquals('valid', $ticket->status);
            $this->assertEquals($this->vipPackage->id, $ticket->ticket_package_id);
            $this->assertEquals('VIP 3-Day Pass', $ticket->package_name);
            $this->assertCount(3, $ticket->validEventDays);
        }
    }

    public function test_unpaid_purchase_does_not_issue_usable_tickets(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'created_by' => $this->salesStaff->id,
            'seller_id' => $this->salesStaff->id,
            'customer_name' => 'Unpaid Customer',
            'customer_phone' => '9800000000',
            'quantity' => 2,
            'unit_price' => 200.00,
            'total_amount' => 400.00,
            'payment_status' => 'unpaid',
        ]);

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_single_day_ticket_is_valid_only_on_its_event_day(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'ticket_package_id' => $this->singleDayPackage->id,
            'package_name' => $this->singleDayPackage->name,
            'created_by' => $this->salesStaff->id,
            'seller_id' => $this->salesStaff->id,
            'customer_name' => 'Aayush Karki',
            'customer_phone' => '9811111111',
            'quantity' => 1,
            'unit_price' => 200.00,
            'total_amount' => 200.00,
            'payment_status' => 'paid',
        ]);

        app(TicketService::class)->issueTicketsForPurchase($purchase);
        $ticket = Ticket::first();

        // 1. Day 1 verification -> Valid
        $checkInDay1 = app(TicketService::class)->markTicketAttended(
            $ticket->verification_token,
            $this->verifyStaff,
            $this->day1->id
        );
        $this->assertTrue($checkInDay1['success']);
        $this->assertDatabaseHas('ticket_attendances', [
            'ticket_id' => $ticket->id,
            'tournament_event_day_id' => $this->day1->id,
            'verified_by' => $this->verifyStaff->id,
        ]);

        // 2. Day 1 second check-in attempt -> Double check-in prevented
        $doubleCheckIn = app(TicketService::class)->markTicketAttended(
            $ticket->verification_token,
            $this->verifyStaff,
            $this->day1->id
        );
        $this->assertFalse($doubleCheckIn['success']);
        $this->assertStringContainsString('ALREADY been checked in', $doubleCheckIn['message']);

        // 3. Day 2 verification attempt -> Invalid for Day 2
        $checkInDay2 = app(TicketService::class)->markTicketAttended(
            $ticket->verification_token,
            $this->verifyStaff,
            $this->day2->id
        );
        $this->assertFalse($checkInDay2['success']);
        $this->assertStringContainsString('NOT valid for', $checkInDay2['message']);
    }

    public function test_multi_day_ticket_allows_separate_checkin_on_each_valid_day(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'ticket_package_id' => $this->vipPackage->id,
            'package_name' => $this->vipPackage->name,
            'created_by' => $this->salesStaff->id,
            'seller_id' => $this->salesStaff->id,
            'customer_name' => 'Suresh Shrestha',
            'customer_phone' => '9841000000',
            'quantity' => 1,
            'unit_price' => 600.00,
            'total_amount' => 600.00,
            'payment_status' => 'paid',
        ]);

        app(TicketService::class)->issueTicketsForPurchase($purchase);
        $ticket = Ticket::first();

        // Check in on Day 1
        $resDay1 = app(TicketService::class)->markTicketAttended(
            $ticket->verification_token,
            $this->verifyStaff,
            $this->day1->id
        );
        $this->assertTrue($resDay1['success']);

        // Check in on Day 2 -> Allowed!
        $resDay2 = app(TicketService::class)->markTicketAttended(
            $ticket->verification_token,
            $this->verifyStaff,
            $this->day2->id
        );
        $this->assertTrue($resDay2['success']);

        // Verify total attendances recorded
        $this->assertEquals(2, TicketAttendance::where('ticket_id', $ticket->id)->count());
    }

    public function test_verification_endpoint_with_auth_staff_succeeds(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'customer_name' => 'Hari Prasad',
            'customer_phone' => '9800000001',
            'quantity' => 1,
            'unit_price' => 200.00,
            'total_amount' => 200.00,
            'payment_status' => 'paid',
            'seller_id' => $this->salesStaff->id,
        ]);

        app(TicketService::class)->issueTicketsForPurchase($purchase);
        $ticket = Ticket::first();

        $response = $this->actingAs($this->verifyStaff, 'web')
            ->postJson(route('ticket.check-in', ['token' => $ticket->verification_token]), [
                'event_day_id' => $this->day1->id,
                'method' => 'qr_scan',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_ticket_sales_report_page_and_excel_export_works(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'customer_name' => 'Excel Tester',
            'customer_phone' => '9800000002',
            'quantity' => 2,
            'unit_price' => 200.00,
            'total_amount' => 400.00,
            'payment_status' => 'paid',
            'seller_id' => $this->salesStaff->id,
        ]);

        app(TicketService::class)->issueTicketsForPurchase($purchase);

        // Super Admin access
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get('/maidan/ticket-reports-page');

        $response->assertStatus(200);
        $response->assertSee('Ticket Reports & Analytics');
        $response->assertSee('Excel Tester');
    }

    public function test_admin_can_download_ticket_pdf(): void
    {
        $purchase = TicketPurchase::create([
            'tournament_id' => $this->tournament->id,
            'created_by' => $this->superAdmin->id,
            'seller_id' => $this->superAdmin->id,
            'customer_name' => 'Bikash Rana',
            'customer_phone' => '9851000000',
            'quantity' => 2,
            'unit_price' => 200.00,
            'total_amount' => 400.00,
            'payment_status' => 'paid',
        ]);

        app(TicketService::class)->issueTicketsForPurchase($purchase);

        $response = $this->actingAs($this->superAdmin, 'web')
            ->get(route('admin.ticket-purchases.pdf', $purchase));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
