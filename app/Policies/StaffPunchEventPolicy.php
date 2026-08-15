<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StaffPunchEvent;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class StaffPunchEventPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StaffPunchEvent') || $authUser->hasRole('super_admin') || $authUser->hasRole('attendance_manager');
    }

    public function view(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return $authUser->can('View:StaffPunchEvent') || $authUser->hasRole('super_admin') || $authUser->hasRole('attendance_manager');
    }

    public function create(AuthUser $authUser): bool
    {
        return false; // Immutable audit log
    }

    public function update(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return false; // Immutable audit log
    }

    public function delete(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return false; // Immutable audit log
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return false;
    }

    public function restore(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return false;
    }

    public function forceDelete(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return false;
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return false;
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return false;
    }

    public function replicate(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
    }
}
