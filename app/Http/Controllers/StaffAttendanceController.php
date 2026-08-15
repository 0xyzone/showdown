<?php

namespace App\Http\Controllers;

use App\Models\StaffAttendance;
use App\Models\StaffBiometricCredential;
use App\Services\StaffAttendanceService;
use App\Services\WebAuthnService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffAttendanceController extends Controller
{
    public function __construct(
        protected StaffAttendanceService $attendanceService,
        protected WebAuthnService $webAuthnService
    ) {}

    /**
     * Show dedicated mobile-first staff attendance portal.
     */
    public function index(): View
    {
        $user = auth()->user();
        $status = $this->attendanceService->getTodayStatus($user);

        $recentAttendances = StaffAttendance::where('user_id', $user->id)
            ->whereDate('date', '<=', Carbon::today())
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        $credentials = $user->biometricCredentials()
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('attendance.index', compact('status', 'recentAttendances', 'credentials'));
    }

    /**
     * Generate WebAuthn registration options.
     */
    public function registerOptions(): JsonResponse
    {
        $user = auth()->user();

        // Check device limit
        $activeCount = $user->biometricCredentials()->where('is_active', true)->count();
        $limit = config('attendance.max_devices', 3);
        if ($activeCount >= $limit) {
            return response()->json([
                'success' => false,
                'message' => "You have reached the maximum limit of {$limit} registered biometric devices. Please remove an old device first.",
            ], 422);
        }

        try {
            $options = $this->webAuthnService->generateRegisterOptions($user);

            return response()->json($options);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Verify WebAuthn registration response and save credential.
     */
    public function registerVerify(Request $request): JsonResponse
    {
        $request->validate([
            'response' => 'required|array',
            'device_name' => 'nullable|string|max:100',
        ]);

        try {
            $credential = $this->webAuthnService->verifyRegistration(
                auth()->user(),
                $request->input('response'),
                $request->input('device_name', 'Biometric Passkey')
            );

            return response()->json([
                'success' => true,
                'message' => "Device '{$credential->name}' registered successfully!",
                'credential' => $credential,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Generate WebAuthn authentication options.
     */
    public function authOptions(): JsonResponse
    {
        try {
            $options = $this->webAuthnService->generateAuthOptions(auth()->user());

            return response()->json($options);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Process staff punch-in request.
     */
    public function punchIn(Request $request): JsonResponse
    {
        $payload = $request->only([
            'latitude',
            'longitude',
            'accuracy',
            'webauthn_response',
        ]);

        $result = $this->attendanceService->punchIn(auth()->user(), $payload);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Process staff punch-out request.
     */
    public function punchOut(Request $request): JsonResponse
    {
        $payload = $request->only([
            'latitude',
            'longitude',
            'accuracy',
            'webauthn_response',
        ]);

        $result = $this->attendanceService->punchOut(auth()->user(), $payload);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Deactivate a registered biometric passkey device.
     */
    public function revokeDevice(StaffBiometricCredential $credential): JsonResponse
    {
        if ($credential->user_id !== auth()->id() && ! auth()->user()->hasRole('super_admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $credential->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => "Device '{$credential->name}' has been deactivated.",
        ]);
    }
}
