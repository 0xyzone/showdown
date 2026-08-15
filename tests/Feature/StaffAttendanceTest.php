<?php

namespace Tests\Feature;

use App\Exports\StaffAttendanceReportExport;
use App\Models\AttendanceSetting;
use App\Models\StaffAttendance;
use App\Models\StaffAttendanceProfile;
use App\Models\User;
use App\Services\StaffAttendanceService;
use Carbon\Carbon;
use Database\Seeders\StaffAttendanceRolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Tests\TestCase;

class StaffAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $staffUser;

    protected User $remoteStaffUser;

    protected AttendanceSetting $setting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(StaffAttendanceRolesSeeder::class);

        $this->superAdmin = User::factory()->create([
            'name' => 'Admin Boss',
            'email' => 'admin@showdown.test',
        ]);
        $this->superAdmin->assignRole('super_admin');

        $this->staffUser = User::factory()->create([
            'name' => 'Office Staff John',
            'email' => 'john@showdown.test',
        ]);
        $this->staffUser->assignRole('staff');

        StaffAttendanceProfile::updateOrCreate(
            ['user_id' => $this->staffUser->id],
            [
                'attendance_mode' => 'office_only',
                'is_biometric_exempt' => true, // To test GPS geofence independently
            ]
        );

        $this->remoteStaffUser = User::factory()->create([
            'name' => 'Remote Worker Alice',
            'email' => 'alice@showdown.test',
        ]);
        $this->remoteStaffUser->assignRole('staff');

        StaffAttendanceProfile::updateOrCreate(
            ['user_id' => $this->remoteStaffUser->id],
            [
                'attendance_mode' => 'remote_allowed',
                'is_biometric_exempt' => true,
            ]
        );

        // Office at 27.7172453, 85.3239605, radius 150m
        $this->setting = AttendanceSetting::current();
        $this->setting->update([
            'office_latitude' => 27.7172453,
            'office_longitude' => 85.3239605,
            'allowed_radius_meters' => 150,
            'max_gps_accuracy_meters' => 50,
            'require_biometric' => false,
        ]);
    }

    public function test_staff_can_view_attendance_terminal_when_authenticated(): void
    {
        $response = $this->actingAs($this->staffUser, 'web')
            ->get(route('attendance.index'));

        $response->assertStatus(200);
        $response->assertSee('Attendance Terminal');
        $response->assertSee('Office Staff John');
    }

    public function test_staff_punch_in_within_office_geofence_succeeds(): void
    {
        // Inside office (exact coordinates, accuracy 10m)
        $response = $this->actingAs($this->staffUser, 'web')
            ->postJson(route('attendance.punch-in'), [
                'latitude' => 27.7172453,
                'longitude' => 85.3239605,
                'accuracy' => 10,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('staff_attendances', [
            'user_id' => $this->staffUser->id,
            'status' => 'working',
            'location_mode' => 'office',
        ]);

        $attendance = StaffAttendance::where('user_id', $this->staffUser->id)->first();
        $this->assertNotNull($attendance);
        $this->assertEquals(Carbon::today()->toDateString(), $attendance->date->toDateString());

        $this->assertDatabaseHas('staff_punch_events', [
            'user_id' => $this->staffUser->id,
            'event_type' => 'punch_in',
            'status' => 'success',
        ]);
    }

    public function test_staff_punch_in_outside_office_geofence_is_rejected_for_office_staff(): void
    {
        // 5 km away from office
        $response = $this->actingAs($this->staffUser, 'web')
            ->postJson(route('attendance.punch-in'), [
                'latitude' => 27.7600000,
                'longitude' => 85.3500000,
                'accuracy' => 10,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertStringContainsString('away from', $response->json('message'));

        $this->assertDatabaseMissing('staff_attendances', [
            'user_id' => $this->staffUser->id,
            'date' => Carbon::today()->toDateString(),
        ]);
    }

    public function test_staff_punch_in_with_poor_gps_accuracy_is_rejected(): void
    {
        // Inside coordinates but with terrible GPS accuracy of 200m (threshold is 50m)
        $response = $this->actingAs($this->staffUser, 'web')
            ->postJson(route('attendance.punch-in'), [
                'latitude' => 27.7172453,
                'longitude' => 85.3239605,
                'accuracy' => 200,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertStringContainsString('accuracy', $response->json('message'));
    }

    public function test_remote_exempt_staff_can_punch_in_from_anywhere(): void
    {
        // Remote worker punching in from far away coordinates
        $response = $this->actingAs($this->remoteStaffUser, 'web')
            ->postJson(route('attendance.punch-in'), [
                'latitude' => 28.2096,
                'longitude' => 83.9856, // Pokhara
                'accuracy' => 20,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('staff_attendances', [
            'user_id' => $this->remoteStaffUser->id,
            'status' => 'remote',
            'location_mode' => 'remote',
        ]);
    }

    public function test_prevent_duplicate_punch_in_on_same_day(): void
    {
        // First punch in
        $this->actingAs($this->staffUser, 'web')
            ->postJson(route('attendance.punch-in'), [
                'latitude' => 27.7172453,
                'longitude' => 85.3239605,
                'accuracy' => 10,
            ])
            ->assertStatus(200);

        // Second punch in attempt
        $second = $this->actingAs($this->staffUser, 'web')
            ->postJson(route('attendance.punch-in'), [
                'latitude' => 27.7172453,
                'longitude' => 85.3239605,
                'accuracy' => 10,
            ]);

        $second->assertStatus(422);
        $second->assertJson(['success' => false]);
        $this->assertStringContainsString('already clocked in', $second->json('message'));
    }

    public function test_staff_punch_out_calculates_correct_worked_duration_and_completes_session(): void
    {
        // Create an attendance record clocked in 3 hours ago
        $attendance = StaffAttendance::create([
            'user_id' => $this->staffUser->id,
            'date' => Carbon::today(),
            'punch_in_at' => Carbon::now()->subHours(3),
            'status' => 'working',
            'location_mode' => 'office',
        ]);

        $response = $this->actingAs($this->staffUser, 'web')
            ->postJson(route('attendance.punch-out'), [
                'latitude' => 27.7172453,
                'longitude' => 85.3239605,
                'accuracy' => 10,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $attendance->refresh();
        $this->assertNotNull($attendance->punch_out_at);
        $this->assertEquals('completed', $attendance->status);
        $this->assertGreaterThanOrEqual(179, $attendance->worked_minutes); // ~180 minutes
    }

    public function test_prevent_punch_out_without_active_punch_in(): void
    {
        $response = $this->actingAs($this->staffUser, 'web')
            ->postJson(route('attendance.punch-out'), [
                'latitude' => 27.7172453,
                'longitude' => 85.3239605,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_prevent_duplicate_punch_out(): void
    {
        StaffAttendance::create([
            'user_id' => $this->staffUser->id,
            'date' => Carbon::today(),
            'punch_in_at' => Carbon::now()->subHours(8),
            'punch_out_at' => Carbon::now(),
            'worked_minutes' => 480,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->staffUser, 'web')
            ->postJson(route('attendance.punch-out'), [
                'latitude' => 27.7172453,
                'longitude' => 85.3239605,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_admin_can_perform_audited_manual_correction(): void
    {
        $attendance = StaffAttendance::create([
            'user_id' => $this->staffUser->id,
            'date' => Carbon::today(),
            'punch_in_at' => Carbon::now()->subHours(4),
            'punch_out_at' => null,
            'status' => 'working',
        ]);

        $newPunchOut = Carbon::now()->toDateTimeString();

        $service = app(StaffAttendanceService::class);
        $service->manualCorrection($attendance, $this->superAdmin, [
            'punch_in_at' => $attendance->punch_in_at->toDateTimeString(),
            'punch_out_at' => $newPunchOut,
            'correction_reason' => 'Staff forgot to punch out due to device battery issue.',
        ]);

        $attendance->refresh();
        $this->assertTrue($attendance->is_manually_corrected);
        $this->assertEquals($this->superAdmin->id, $attendance->corrected_by);
        $this->assertNotNull($attendance->corrected_at);
        $this->assertStringContainsString('battery', $attendance->correction_reason);

        $this->assertDatabaseHas('staff_punch_events', [
            'user_id' => $this->staffUser->id,
            'event_type' => 'manual_correction',
            'status' => 'success',
        ]);
    }

    public function test_timesheet_reports_page_and_excel_export(): void
    {
        StaffAttendance::create([
            'user_id' => $this->staffUser->id,
            'date' => Carbon::today(),
            'punch_in_at' => Carbon::now()->subHours(6),
            'punch_out_at' => Carbon::now(),
            'worked_minutes' => 360,
            'status' => 'completed',
            'location_mode' => 'office',
        ]);

        // Super Admin access
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get('/maidan/attendance-reports-page');

        $response->assertStatus(200);
        $response->assertSee('Office Staff John');
        $response->assertSee('Detailed Staff Attendance Timesheets');

        // Excel Export test
        $export = new StaffAttendanceReportExport;
        $collection = $export->collection();
        $this->assertGreaterThanOrEqual(1, $collection->count());
        $this->assertNotNull($export->styles(new Worksheet));
    }
}
