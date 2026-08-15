<?php

namespace Database\Seeders;

use App\Models\AttendanceSetting;
use App\Models\StaffAttendanceProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StaffAttendanceRolesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. System Settings Initialized
        AttendanceSetting::current();

        // 2. Create Permissions
        $permissions = [
            // Staff Attendance Records
            'ViewAny:StaffAttendance',
            'View:StaffAttendance',
            'Create:StaffAttendance',
            'Update:StaffAttendance',
            'Delete:StaffAttendance',
            'Correct:StaffAttendance',
            // Staff Profiles / Policies
            'ViewAny:StaffAttendanceProfile',
            'View:StaffAttendanceProfile',
            'Create:StaffAttendanceProfile',
            'Update:StaffAttendanceProfile',
            'Delete:StaffAttendanceProfile',
            // Attendance Settings
            'View:AttendanceSetting',
            'Update:AttendanceSetting',
            // Punch Event Security Audit
            'ViewAny:StaffPunchEvent',
            'View:StaffPunchEvent',
            // Reports & Exports
            'View:AttendanceReport',
            'Export:AttendanceReport',
            // Widgets
            'widget_AttendanceOverviewStats',
            'widget_CurrentlyWorkingStaffWidget',
            'widget_MyAttendanceStatsWidget',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 3. Super Admin Role
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo(Permission::all());

        // 4. Attendance Manager Role
        $managerRole = Role::firstOrCreate(['name' => 'attendance_manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions([
            'ViewAny:StaffAttendance',
            'View:StaffAttendance',
            'Update:StaffAttendance',
            'Correct:StaffAttendance',
            'ViewAny:StaffAttendanceProfile',
            'View:StaffAttendanceProfile',
            'Update:StaffAttendanceProfile',
            'View:AttendanceSetting',
            'Update:AttendanceSetting',
            'ViewAny:StaffPunchEvent',
            'View:StaffPunchEvent',
            'View:AttendanceReport',
            'Export:AttendanceReport',
            'widget_AttendanceOverviewStats',
            'widget_CurrentlyWorkingStaffWidget',
            'widget_MyAttendanceStatsWidget',
        ]);

        // 5. Staff Role
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staffRole->syncPermissions([
            'View:StaffAttendance',
            'widget_MyAttendanceStatsWidget',
        ]);

        // 6. Seed profiles for all existing users if missing
        foreach (User::all() as $user) {
            StaffAttendanceProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'attendance_mode' => 'office_only',
                    'is_biometric_exempt' => false,
                ]
            );
        }
    }
}
