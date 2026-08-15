<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LeadType;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class LeadTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeadType');
    }

    public function view(AuthUser $authUser, LeadType $leadType): bool
    {
        return $authUser->can('View:LeadType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeadType');
    }

    public function update(AuthUser $authUser, LeadType $leadType): bool
    {
        return $authUser->can('Update:LeadType');
    }

    public function delete(AuthUser $authUser, LeadType $leadType): bool
    {
        return $authUser->can('Delete:LeadType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LeadType');
    }

    public function restore(AuthUser $authUser, LeadType $leadType): bool
    {
        return $authUser->can('Restore:LeadType');
    }

    public function forceDelete(AuthUser $authUser, LeadType $leadType): bool
    {
        return $authUser->can('ForceDelete:LeadType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeadType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeadType');
    }

    public function replicate(AuthUser $authUser, LeadType $leadType): bool
    {
        return $authUser->can('Replicate:LeadType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeadType');
    }
}
