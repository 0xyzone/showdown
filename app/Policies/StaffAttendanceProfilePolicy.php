<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StaffAttendanceProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffAttendanceProfilePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StaffAttendanceProfile');
    }

    public function view(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->can('View:StaffAttendanceProfile');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StaffAttendanceProfile');
    }

    public function update(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->can('Update:StaffAttendanceProfile');
    }

    public function delete(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->can('Delete:StaffAttendanceProfile');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StaffAttendanceProfile');
    }

    public function restore(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->can('Restore:StaffAttendanceProfile');
    }

    public function forceDelete(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->can('ForceDelete:StaffAttendanceProfile');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StaffAttendanceProfile');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StaffAttendanceProfile');
    }

    public function replicate(AuthUser $authUser, StaffAttendanceProfile $staffAttendanceProfile): bool
    {
        return $authUser->can('Replicate:StaffAttendanceProfile');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StaffAttendanceProfile');
    }

}