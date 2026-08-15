<?php

namespace Database\Seeders;

use App\Models\TicketPackage;
use App\Models\Tournament;
use App\Models\TournamentEventDay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TicketSystemRolesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create permissions
        $permissions = [
            // Tournaments
            'ViewAny:Tournament',
            'View:Tournament',
            'Create:Tournament',
            'Update:Tournament',
            'Delete:Tournament',
            // Ticket Purchases
            'ViewAny:TicketPurchase',
            'View:TicketPurchase',
            'Create:TicketPurchase',
            'Update:TicketPurchase',
            'Delete:TicketPurchase',
            'DeleteAny:TicketPurchase',
            // Individual Tickets
            'ViewAny:Ticket',
            'View:Ticket',
            'Create:Ticket',
            'Update:Ticket',
            'Delete:Ticket',
            'Verify:Ticket',
            // Ticket Packages
            'ViewAny:TicketPackage',
            'View:TicketPackage',
            'Create:TicketPackage',
            'Update:TicketPackage',
            'Delete:TicketPackage',
            // Payment Methods
            'ViewAny:PaymentMethod',
            'View:PaymentMethod',
            'Create:PaymentMethod',
            'Update:PaymentMethod',
            'Delete:PaymentMethod',
            // Ticket Reports
            'View:TicketReport',
            'Export:TicketReport',
            // Widgets
            'widget_TicketSalesStatsOverview',
            'widget_RecentTicketSalesWidget',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 2. Super Admin Role
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Assign super_admin to existing admin users
        $admin = User::where('email', 'like', '%admin%')->orWhere('id', 1)->first();
        if ($admin && ! $admin->hasRole('super_admin')) {
            $admin->assignRole($superAdminRole);
        }

        // 3. Ticket Sales Staff Role
        $salesRole = Role::firstOrCreate(['name' => 'ticket_sales_staff', 'guard_name' => 'web']);
        $salesRole->syncPermissions([
            'ViewAny:TicketPurchase',
            'View:TicketPurchase',
            'Create:TicketPurchase',
            'Update:TicketPurchase',
            'ViewAny:Tournament',
            'View:Tournament',
            'ViewAny:TicketPackage',
            'View:TicketPackage',
            'ViewAny:PaymentMethod',
            'View:PaymentMethod',
            'widget_TicketSalesStatsOverview',
            'widget_RecentTicketSalesWidget',
        ]);

        // 4. Ticket Verification Staff Role
        $verifyRole = Role::firstOrCreate(['name' => 'ticket_verification_staff', 'guard_name' => 'web']);
        $verifyRole->syncPermissions([
            'Verify:Ticket',
            'ViewAny:Ticket',
            'View:Ticket',
            'ViewAny:Tournament',
            'View:Tournament',
        ]);

        // 5. Seed sample Event Days and Ticket Packages for active tournament
        $tournament = Tournament::where('is_active', true)->first() ?? Tournament::first();

        if ($tournament) {
            // Event Days
            $day1 = TournamentEventDay::firstOrCreate(
                ['tournament_id' => $tournament->id, 'day_name' => 'Day 1 - Group Stage'],
                [
                    'event_date' => Carbon::today(),
                    'order' => 1,
                    'is_active' => true,
                    'notes' => 'Opening Ceremony and preliminary group stages.',
                ]
            );

            $day2 = TournamentEventDay::firstOrCreate(
                ['tournament_id' => $tournament->id, 'day_name' => 'Day 2 - Semifinals'],
                [
                    'event_date' => Carbon::today()->addDay(),
                    'order' => 2,
                    'is_active' => true,
                    'notes' => 'Playoffs and Semifinal matchups.',
                ]
            );

            $day3 = TournamentEventDay::firstOrCreate(
                ['tournament_id' => $tournament->id, 'day_name' => 'Day 3 - Grand Finals'],
                [
                    'event_date' => Carbon::today()->addDays(2),
                    'order' => 3,
                    'is_active' => true,
                    'notes' => 'Grand Finals showdown and award ceremony.',
                ]
            );

            // Ticket Packages
            $vipPackage = TicketPackage::firstOrCreate(
                ['tournament_id' => $tournament->id, 'name' => 'VIP 3-Day Season Pass'],
                [
                    'description' => 'Full 3-day access, front-row premium seating, player lounge access, and exclusive tournament jersey.',
                    'price' => 500.00,
                    'validity_type' => 'all_days',
                    'order' => 1,
                    'is_active' => true,
                ]
            );
            $vipPackage->eventDays()->sync([$day1->id, $day2->id, $day3->id]);

            $day1Pass = TicketPackage::firstOrCreate(
                ['tournament_id' => $tournament->id, 'name' => 'Day 1 Opening Pass'],
                [
                    'description' => 'Single day admission pass for Day 1 Group Stages.',
                    'price' => 150.00,
                    'validity_type' => 'single_day',
                    'order' => 2,
                    'is_active' => true,
                ]
            );
            $day1Pass->eventDays()->sync([$day1->id]);

            $finalsPass = TicketPackage::firstOrCreate(
                ['tournament_id' => $tournament->id, 'name' => 'Grand Finals Day Pass'],
                [
                    'description' => 'Single day admission pass for Day 3 Grand Finals.',
                    'price' => 250.00,
                    'validity_type' => 'single_day',
                    'order' => 3,
                    'is_active' => true,
                ]
            );
            $finalsPass->eventDays()->sync([$day3->id]);
        }
    }
}
