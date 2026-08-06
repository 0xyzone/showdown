<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LeadStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadStatusPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeadStatus');
    }

    public function view(AuthUser $authUser, LeadStatus $leadStatus): bool
    {
        return $authUser->can('View:LeadStatus');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeadStatus');
    }

    public function update(AuthUser $authUser, LeadStatus $leadStatus): bool
    {
        return $authUser->can('Update:LeadStatus');
    }

    public function delete(AuthUser $authUser, LeadStatus $leadStatus): bool
    {
        return $authUser->can('Delete:LeadStatus');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LeadStatus');
    }

    public function restore(AuthUser $authUser, LeadStatus $leadStatus): bool
    {
        return $authUser->can('Restore:LeadStatus');
    }

    public function forceDelete(AuthUser $authUser, LeadStatus $leadStatus): bool
    {
        return $authUser->can('ForceDelete:LeadStatus');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeadStatus');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeadStatus');
    }

    public function replicate(AuthUser $authUser, LeadStatus $leadStatus): bool
    {
        return $authUser->can('Replicate:LeadStatus');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeadStatus');
    }

}