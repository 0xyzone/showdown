<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TicketPackage;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPackagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TicketPackage');
    }

    public function view(AuthUser $authUser, TicketPackage $ticketPackage): bool
    {
        return $authUser->can('View:TicketPackage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TicketPackage');
    }

    public function update(AuthUser $authUser, TicketPackage $ticketPackage): bool
    {
        return $authUser->can('Update:TicketPackage');
    }

    public function delete(AuthUser $authUser, TicketPackage $ticketPackage): bool
    {
        return $authUser->can('Delete:TicketPackage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TicketPackage');
    }

    public function restore(AuthUser $authUser, TicketPackage $ticketPackage): bool
    {
        return $authUser->can('Restore:TicketPackage');
    }

    public function forceDelete(AuthUser $authUser, TicketPackage $ticketPackage): bool
    {
        return $authUser->can('ForceDelete:TicketPackage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TicketPackage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TicketPackage');
    }

    public function replicate(AuthUser $authUser, TicketPackage $ticketPackage): bool
    {
        return $authUser->can('Replicate:TicketPackage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TicketPackage');
    }

}