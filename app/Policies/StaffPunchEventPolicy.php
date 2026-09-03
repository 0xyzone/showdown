<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StaffPunchEvent;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffPunchEventPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StaffPunchEvent');
    }

    public function view(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return $authUser->can('View:StaffPunchEvent');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StaffPunchEvent');
    }

    public function update(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return $authUser->can('Update:StaffPunchEvent');
    }

    public function delete(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return $authUser->can('Delete:StaffPunchEvent');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StaffPunchEvent');
    }

    public function restore(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return $authUser->can('Restore:StaffPunchEvent');
    }

    public function forceDelete(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return $authUser->can('ForceDelete:StaffPunchEvent');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StaffPunchEvent');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StaffPunchEvent');
    }

    public function replicate(AuthUser $authUser, StaffPunchEvent $staffPunchEvent): bool
    {
        return $authUser->can('Replicate:StaffPunchEvent');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StaffPunchEvent');
    }

}