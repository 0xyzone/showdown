<?php

namespace Tests\Feature;

use App\Exports\StaffAttendanceReportExport;
use App\Models\AttendanceSetting;
use App\Models\StaffAttendance;
use App\Models\StaffAttendanceProfile;
use App\Models\User;
use App\Services\StaffAttendanceService;
use App\Services\WebAuthnService;
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

    public function test_guest_is_redirected_to_login_when_accessing_attendance_terminal(): void
    {
        $response = $this->get(route('attendance.index'));

        $response->assertRedirect('/maidan/login');
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

    public function test_webauthn_passkey_registration_and_authentication_signature_verification(): void
    {
        $service = app(WebAuthnService::class);

        // 1. Generate an ECDSA P-256 key pair to simulate a physical WebAuthn device
        $ecKey = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        $details = openssl_pkey_get_details($ecKey);
        $x = $details['ec']['x'];
        $y = $details['ec']['y'];

        // Build CBOR COSE key map: 1 => 2 (EC2), 3 => -7 (ES256), -1 => 1 (P-256), -2 => X (32B), -3 => Y (32B)
        $coseKeyBytes = "\xa5\x01\x02\x03\x26\x20\x01\x21\x58\x20".$x."\x22\x58\x20".$y;

        $rpIdHash = hash('sha256', 'localhost', true);
        $flags = chr(0x45); // UP + UV + AT
        $signCount = pack('N', 0);
        $aaguid = random_bytes(16);
        $credId = random_bytes(32);
        $credIdLen = pack('n', strlen($credId));

        $authData = $rpIdHash.$flags.$signCount.$aaguid.$credIdLen.$credId.$coseKeyBytes;
        $attestationBytes = "\xa3\x63fmt\x64none\x67attStmt\xa0\x68authData\x59".pack('n', strlen($authData)).$authData;

        $regChallenge = $service->generateChallenge();
        session(['webauthn_register_challenge' => $regChallenge]);

        $clientDataJSON = json_encode([
            'type' => 'webauthn.create',
            'challenge' => $regChallenge,
            'origin' => 'http://localhost',
        ]);

        $credential = $service->verifyRegistration($this->staffUser, [
            'id' => $service->base64UrlEncode($credId),
            'clientDataJSON' => $service->base64UrlEncode($clientDataJSON),
            'attestationObject' => $service->base64UrlEncode($attestationBytes),
        ], 'MacBook Touch ID');

        $this->assertNotNull($credential);
        $this->assertEquals('MacBook Touch ID', $credential->name);
        $this->assertStringContainsString('BEGIN PUBLIC KEY', $credential->public_key);

        // 2. Simulate WebAuthn Authentication during Punch In
        $authChallenge = $service->generateChallenge();
        session(['webauthn_auth_challenge' => $authChallenge]);

        $authClientDataJSON = json_encode([
            'type' => 'webauthn.get',
            'challenge' => $authChallenge,
            'origin' => 'http://localhost',
        ]);

        $authAuthenticatorData = $rpIdHash.chr(0x01).pack('N', 1); // UP flag set
        $dataToSign = $authAuthenticatorData.hash('sha256', $authClientDataJSON, true);

        openssl_sign($dataToSign, $signature, $ecKey, OPENSSL_ALGO_SHA256);

        $authPayload = [
            'id' => $service->base64UrlEncode($credId),
            'clientDataJSON' => $service->base64UrlEncode($authClientDataJSON),
            'authenticatorData' => $service->base64UrlEncode($authAuthenticatorData),
            'signature' => $service->base64UrlEncode($signature),
        ];

        $verifiedCred = $service->verifyAuthentication($this->staffUser, $authPayload);
        $this->assertEquals($credential->id, $verifiedCred->id);
        $this->assertEquals(1, $verifiedCred->counter);
    }

    public function test_admin_can_view_staff_attendance_profiles_resource(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->get('/maidan/staff-attendance-profiles');

        $response->assertStatus(200);
        $response->assertSee('Staff Attendance Policies');
        $response->assertSee('Office Staff John');
    }
}
