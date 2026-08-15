<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StaffAttendanceProfile;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class StaffAttendanceProfilePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StaffAttendanceProfile') || $authUser->hasRole('super_admin') || $authUser->hasRole('attendance_manager');
    }

    public function view(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->can('View:StaffAttendanceProfile') || $authUser->hasRole('super_admin') || $authUser->hasRole('attendance_manager');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StaffAttendanceProfile') || $authUser->hasRole('super_admin');
    }

    public function update(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->can('Update:StaffAttendanceProfile') || $authUser->hasRole('super_admin') || $authUser->hasRole('attendance_manager');
    }

    public function delete(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function restore(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function forceDelete(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function replicate(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }
}
