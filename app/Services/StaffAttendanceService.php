<?php

namespace App\Services;

use App\Models\AttendanceSetting;
use App\Models\StaffAttendance;
use App\Models\StaffPunchEvent;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffAttendanceService
{
    public function __construct(
        protected WebAuthnService $webAuthnService
    ) {}

    /**
     * Calculate Haversine distance in meters between two GPS coordinates.
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return (int) round($angle * $earthRadius);
    }

    /**
     * Get real-time attendance status and policy context for a staff user today.
     */
    public function getTodayStatus(User $user): array
    {
        $settings = AttendanceSetting::current();
        $profile = $user->attendanceProfile;
        $todayAttendance = StaffAttendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        $activeDevicesCount = $user->biometricCredentials()->where('is_active', true)->count();
        $isRemoteAllowed = $profile ? $profile->isRemoteAllowed() : false;
        $isBiometricRequired = $settings->require_biometric && ! ($profile?->is_biometric_exempt);

        $isClockedIn = $todayAttendance && $todayAttendance->punch_in_at && ! $todayAttendance->punch_out_at;
        $isCompleted = $todayAttendance && $todayAttendance->punch_out_at;

        return [
            'today_date' => Carbon::today()->format('F d, Y'),
            'today_attendance' => $todayAttendance,
            'is_clocked_in' => $isClockedIn,
            'is_completed' => $isCompleted,
            'can_punch_in' => ! $todayAttendance || ! $todayAttendance->punch_in_at,
            'can_punch_out' => $isClockedIn,
            'active_devices_count' => $activeDevicesCount,
            'is_remote_allowed' => $isRemoteAllowed,
            'is_biometric_required' => $isBiometricRequired,
            'attendance_mode' => $profile?->attendance_mode ?? 'office_only',
            'office_name' => $settings->office_name,
            'office_latitude' => (float) $settings->office_latitude,
            'office_longitude' => (float) $settings->office_longitude,
            'allowed_radius_meters' => (int) $settings->allowed_radius_meters,
            'max_gps_accuracy_meters' => (int) $settings->max_gps_accuracy_meters,
        ];
    }

    /**
     * Process staff punch-in with atomic row locking and verification.
     */
    public function punchIn(User $user, array $payload = []): array
    {
        $settings = AttendanceSetting::current();
        $profile = $user->attendanceProfile;
        $today = Carbon::today();
        $now = Carbon::now();

        $ip = request()->ip() ?? $payload['ip'] ?? '127.0.0.1';
        $userAgent = request()->userAgent() ?? 'Staff Attendance Terminal';

        $lat = isset($payload['latitude']) && $payload['latitude'] !== null ? (float) $payload['latitude'] : null;
        $lon = isset($payload['longitude']) && $payload['longitude'] !== null ? (float) $payload['longitude'] : null;
        $accuracy = isset($payload['accuracy']) && $payload['accuracy'] !== null ? (float) $payload['accuracy'] : null;

        $isRemoteAllowed = $profile ? $profile->isRemoteAllowed() : false;
        $locationMode = $isRemoteAllowed ? 'remote' : 'office';

        // 1. Verify Geolocation / Geofence
        $distanceMeters = null;
        if (! $isRemoteAllowed) {
            if ($lat === null || $lon === null) {
                $this->logPunchEvent($user, null, 'punch_in', $now, 'rejected', 'GPS location coordinates are required for office attendance.', $locationMode, $lat, $lon, $accuracy, null, $ip, $userAgent);

                return [
                    'success' => false,
                    'message' => 'Location permission is required to verify office attendance. Please allow GPS location access.',
                ];
            }

            if ($accuracy !== null && $accuracy > $settings->max_gps_accuracy_meters) {
                $this->logPunchEvent($user, null, 'punch_in', $now, 'rejected', "GPS accuracy is too low ({$accuracy}m > max {$settings->max_gps_accuracy_meters}m).", $locationMode, $lat, $lon, $accuracy, null, $ip, $userAgent);

                return [
                    'success' => false,
                    'message' => "Your device GPS accuracy ({$accuracy}m) is too poor to reliably verify office location. Please move near a window or ensure Wi-Fi is enabled.",
                ];
            }

            $distanceMeters = $this->calculateDistance($lat, $lon, (float) $settings->office_latitude, (float) $settings->office_longitude);

            if ($distanceMeters > $settings->allowed_radius_meters) {
                $this->logPunchEvent($user, null, 'punch_in', $now, 'rejected', "Outside office geofence ({$distanceMeters}m away from {$settings->office_name}, allowed: {$settings->allowed_radius_meters}m).", $locationMode, $lat, $lon, $accuracy, $distanceMeters, $ip, $userAgent);

                return [
                    'success' => false,
                    'message' => "You are {$distanceMeters}m away from {$settings->office_name}. Attendance is only allowed within {$settings->allowed_radius_meters}m of the office.",
                ];
            }
        }

        // 2. Verify WebAuthn Biometric / Passkey if provided or required
        $verifiedBiometric = false;
        $credentialId = null;
        if (! empty($payload['webauthn_response'])) {
            try {
                $credential = $this->webAuthnService->verifyAuthentication($user, $payload['webauthn_response']);
                $verifiedBiometric = true;
                $credentialId = $credential->credential_id;
            } catch (Exception $e) {
                $this->logPunchEvent($user, null, 'punch_in', $now, 'rejected', 'Biometric verification failed: '.$e->getMessage(), $locationMode, $lat, $lon, $accuracy, $distanceMeters, $ip, $userAgent);

                return [
                    'success' => false,
                    'message' => 'Biometric authentication failed: '.$e->getMessage(),
                ];
            }
        } elseif ($settings->require_biometric && ! ($profile?->is_biometric_exempt)) {
            // Check if user has registered credentials
            $hasCredentials = $user->biometricCredentials()->where('is_active', true)->exists();
            if ($hasCredentials) {
                return [
                    'success' => false,
                    'message' => 'Biometric verification is required to punch in. Please authenticate with your device fingerprint or Face ID.',
                ];
            }
        }

        // 3. Atomic Database Transaction with Row Lock
        return DB::transaction(function () use ($user, $today, $now, $locationMode, $lat, $lon, $accuracy, $distanceMeters, $ip, $userAgent, $verifiedBiometric, $credentialId) {
            $attendance = StaffAttendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->lockForUpdate()
                ->first();

            if ($attendance && $attendance->punch_in_at) {
                $this->logPunchEvent($user, $attendance->id, 'punch_in', $now, 'rejected', 'Already punched in for today.', $locationMode, $lat, $lon, $accuracy, $distanceMeters, $ip, $userAgent, $credentialId);

                return [
                    'success' => false,
                    'message' => 'You are already clocked in for today at '.$attendance->punch_in_at->format('h:i A').'.',
                ];
            }

            if (! $attendance) {
                $attendance = new StaffAttendance([
                    'user_id' => $user->id,
                    'date' => $today->toDateString(),
                ]);
            }

            $attendance->punch_in_at = $now;
            $attendance->status = $locationMode === 'remote' ? 'remote' : 'working';
            $attendance->location_mode = $locationMode;
            $attendance->punch_in_latitude = $lat;
            $attendance->punch_in_longitude = $lon;
            $attendance->punch_in_accuracy = $accuracy;
            $attendance->punch_in_distance_meters = $distanceMeters;
            $attendance->punch_in_ip = $ip;
            $attendance->punch_in_method = $verifiedBiometric ? 'webauthn' : 'device_auth';
            $attendance->punch_in_verified_biometric = $verifiedBiometric;
            $attendance->save();

            $this->logPunchEvent($user, $attendance->id, 'punch_in', $now, 'success', null, $locationMode, $lat, $lon, $accuracy, $distanceMeters, $ip, $userAgent, $credentialId);

            return [
                'success' => true,
                'message' => 'Clocked in successfully at '.$now->format('h:i A').'. Have a productive day!',
                'punch_in_time' => $now->format('h:i A'),
                'attendance' => $attendance,
            ];
        });
    }

    /**
     * Process staff punch-out with atomic row locking and duration calculation.
     */
    public function punchOut(User $user, array $payload = []): array
    {
        $settings = AttendanceSetting::current();
        $profile = $user->attendanceProfile;
        $today = Carbon::today();
        $now = Carbon::now();

        $ip = request()->ip() ?? $payload['ip'] ?? '127.0.0.1';
        $userAgent = request()->userAgent() ?? 'Staff Attendance Terminal';

        $lat = isset($payload['latitude']) && $payload['latitude'] !== null ? (float) $payload['latitude'] : null;
        $lon = isset($payload['longitude']) && $payload['longitude'] !== null ? (float) $payload['longitude'] : null;
        $accuracy = isset($payload['accuracy']) && $payload['accuracy'] !== null ? (float) $payload['accuracy'] : null;

        $isRemoteAllowed = $profile ? $profile->isRemoteAllowed() : false;
        $locationMode = $isRemoteAllowed ? 'remote' : 'office';

        $distanceMeters = null;
        if ($lat !== null && $lon !== null) {
            $distanceMeters = $this->calculateDistance($lat, $lon, (float) $settings->office_latitude, (float) $settings->office_longitude);
        }

        // 1. Verify WebAuthn Biometric / Passkey if provided or required
        $verifiedBiometric = false;
        $credentialId = null;
        if (! empty($payload['webauthn_response'])) {
            try {
                $credential = $this->webAuthnService->verifyAuthentication($user, $payload['webauthn_response']);
                $verifiedBiometric = true;
                $credentialId = $credential->credential_id;
            } catch (Exception $e) {
                $this->logPunchEvent($user, null, 'punch_out', $now, 'rejected', 'Biometric verification failed: '.$e->getMessage(), $locationMode, $lat, $lon, $accuracy, $distanceMeters, $ip, $userAgent);

                return [
                    'success' => false,
                    'message' => 'Biometric authentication failed: '.$e->getMessage(),
                ];
            }
        }

        // 2. Atomic Database Transaction with Row Lock
        return DB::transaction(function () use ($user, $today, $now, $locationMode, $lat, $lon, $accuracy, $distanceMeters, $ip, $userAgent, $verifiedBiometric, $credentialId) {
            $attendance = StaffAttendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->lockForUpdate()
                ->first();

            if (! $attendance || ! $attendance->punch_in_at) {
                $this->logPunchEvent($user, null, 'punch_out', $now, 'rejected', 'Cannot punch out without active punch in.', $locationMode, $lat, $lon, $accuracy, $distanceMeters, $ip, $userAgent, $credentialId);

                return [
                    'success' => false,
                    'message' => 'You cannot clock out because no active clock-in was recorded for today.',
                ];
            }

            if ($attendance->punch_out_at) {
                $this->logPunchEvent($user, $attendance->id, 'punch_out', $now, 'rejected', 'Already punched out for today.', $locationMode, $lat, $lon, $accuracy, $distanceMeters, $ip, $userAgent, $credentialId);

                return [
                    'success' => false,
                    'message' => 'You have already clocked out today at '.$attendance->punch_out_at->format('h:i A').'.',
                ];
            }

            $punchIn = Carbon::parse($attendance->punch_in_at);
            $workedMinutes = max(0, (int) round(abs($now->diffInMinutes($punchIn))));

            $attendance->punch_out_at = $now;
            $attendance->worked_minutes = $workedMinutes;
            $attendance->status = $attendance->location_mode === 'remote' ? 'remote' : 'completed';
            $attendance->punch_out_latitude = $lat;
            $attendance->punch_out_longitude = $lon;
            $attendance->punch_out_accuracy = $accuracy;
            $attendance->punch_out_distance_meters = $distanceMeters;
            $attendance->punch_out_ip = $ip;
            $attendance->punch_out_method = $verifiedBiometric ? 'webauthn' : 'device_auth';
            $attendance->punch_out_verified_biometric = $verifiedBiometric;
            $attendance->save();

            $this->logPunchEvent($user, $attendance->id, 'punch_out', $now, 'success', null, $locationMode, $lat, $lon, $accuracy, $distanceMeters, $ip, $userAgent, $credentialId);

            $hours = floor($workedMinutes / 60);
            $mins = $workedMinutes % 60;

            return [
                'success' => true,
                'message' => "Clocked out successfully at {$now->format('h:i A')}. Total time worked: {$hours}h {$mins}m.",
                'punch_out_time' => $now->format('h:i A'),
                'worked_time' => "{$hours}h {$mins}m",
                'attendance' => $attendance,
            ];
        });
    }

    /**
     * Perform an audited manual correction by an authorized administrator.
     */
    public function manualCorrection(StaffAttendance $attendance, User $adminUser, array $data): StaffAttendance
    {
        $oldIn = $attendance->punch_in_at ? $attendance->punch_in_at->format('Y-m-d H:i') : 'None';
        $oldOut = $attendance->punch_out_at ? $attendance->punch_out_at->format('Y-m-d H:i') : 'None';

        if (! empty($data['punch_in_at'])) {
            $attendance->punch_in_at = Carbon::parse($data['punch_in_at']);
        }
        if (! empty($data['punch_out_at'])) {
            $attendance->punch_out_at = Carbon::parse($data['punch_out_at']);
        }

        if ($attendance->punch_in_at && $attendance->punch_out_at) {
            $attendance->worked_minutes = max(0, (int) round($attendance->punch_out_at->diffInMinutes($attendance->punch_in_at)));
            $attendance->status = 'completed';
        }

        $attendance->is_manually_corrected = true;
        $attendance->correction_reason = $data['correction_reason'] ?? 'Admin manual adjustment';
        $attendance->corrected_by = $adminUser->id;
        $attendance->corrected_at = now();
        $attendance->save();

        StaffPunchEvent::create([
            'user_id' => $attendance->user_id,
            'staff_attendance_id' => $attendance->id,
            'event_type' => 'manual_correction',
            'occurred_at' => now(),
            'status' => 'success',
            'details' => [
                'admin_id' => $adminUser->id,
                'admin_name' => $adminUser->name,
                'reason' => $attendance->correction_reason,
                'old_punch_in' => $oldIn,
                'new_punch_in' => $attendance->punch_in_at?->format('Y-m-d H:i'),
                'old_punch_out' => $oldOut,
                'new_punch_out' => $attendance->punch_out_at?->format('Y-m-d H:i'),
                'worked_minutes' => $attendance->worked_minutes,
            ],
        ]);

        return $attendance;
    }

    /**
     * Log an immutable punch event for audit.
     */
    protected function logPunchEvent(
        User $user,
        ?int $attendanceId,
        string $eventType,
        Carbon $occurredAt,
        string $status,
        ?string $failureReason = null,
        ?string $locationMode = null,
        ?float $lat = null,
        ?float $lon = null,
        ?float $accuracy = null,
        ?int $distance = null,
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $credentialId = null
    ): StaffPunchEvent {
        return StaffPunchEvent::create([
            'user_id' => $user->id,
            'staff_attendance_id' => $attendanceId,
            'event_type' => $eventType,
            'occurred_at' => $occurredAt,
            'status' => $status,
            'failure_reason' => $failureReason,
            'location_mode' => $locationMode,
            'latitude' => $lat,
            'longitude' => $lon,
            'accuracy' => $accuracy,
            'distance_meters' => $distance,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'verification_method' => $credentialId ? 'webauthn' : 'gps_location',
            'credential_id' => $credentialId,
        ]);
    }
}
