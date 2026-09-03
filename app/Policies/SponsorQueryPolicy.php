<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SponsorQuery;
use Illuminate\Auth\Access\HandlesAuthorization;

class SponsorQueryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SponsorQuery');
    }

    public function view(AuthUser $authUser, SponsorQuery $sponsorQuery): bool
    {
        return $authUser->can('View:SponsorQuery');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SponsorQuery');
    }

    public function update(AuthUser $authUser, SponsorQuery $sponsorQuery): bool
    {
        return $authUser->can('Update:SponsorQuery');
    }

    public function delete(AuthUser $authUser, SponsorQuery $sponsorQuery): bool
    {
        return $authUser->can('Delete:SponsorQuery');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SponsorQuery');
    }

    public function restore(AuthUser $authUser, SponsorQuery $sponsorQuery): bool
    {
        return $authUser->can('Restore:SponsorQuery');
    }

    public function forceDelete(AuthUser $authUser, SponsorQuery $sponsorQuery): bool
    {
        return $authUser->can('ForceDelete:SponsorQuery');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SponsorQuery');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SponsorQuery');
    }

    public function replicate(AuthUser $authUser, SponsorQuery $sponsorQuery): bool
    {
        return $authUser->can('Replicate:SponsorQuery');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SponsorQuery');
    }

}