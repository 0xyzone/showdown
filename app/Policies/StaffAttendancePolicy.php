<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StaffAttendance;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class StaffAttendancePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StaffAttendance') || $authUser->can('View:StaffAttendance');
    }

    public function view(AuthUser $authUser, StaffAttendance $staffAttendance): bool
    {
        if ($authUser->hasRole('super_admin') || $authUser->hasRole('attendance_manager') || $authUser->can('ViewAny:StaffAttendance')) {
            return true;
        }

        return $staffAttendance->user_id === $authUser->id;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StaffAttendance') || $authUser->hasRole('super_admin');
    }

    public function update(AuthUser $authUser, StaffAttendance $staffAttendance): bool
    {
        return $authUser->hasRole('super_admin') || $authUser->hasRole('attendance_manager') || $authUser->can('Correct:StaffAttendance');
    }

    public function delete(AuthUser $authUser, StaffAttendance $staffAttendance): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function restore(AuthUser $authUser, StaffAttendance $staffAttendance): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function forceDelete(AuthUser $authUser, StaffAttendance $staffAttendance): bool
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

    public function replicate(AuthUser $authUser, StaffAttendance $staffAttendance): bool
    {
        return $authUser->hasRole('super_admin');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }
}
