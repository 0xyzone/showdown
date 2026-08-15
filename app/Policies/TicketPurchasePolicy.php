<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TicketPurchase;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPurchasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TicketPurchase');
    }

    public function view(AuthUser $authUser, TicketPurchase $ticketPurchase): bool
    {
        return $authUser->can('View:TicketPurchase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TicketPurchase');
    }

    public function update(AuthUser $authUser, TicketPurchase $ticketPurchase): bool
    {
        return $authUser->can('Update:TicketPurchase');
    }

    public function delete(AuthUser $authUser, TicketPurchase $ticketPurchase): bool
    {
        return $authUser->can('Delete:TicketPurchase');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TicketPurchase');
    }

    public function restore(AuthUser $authUser, TicketPurchase $ticketPurchase): bool
    {
        return $authUser->can('Restore:TicketPurchase');
    }

    public function forceDelete(AuthUser $authUser, TicketPurchase $ticketPurchase): bool
    {
        return $authUser->can('ForceDelete:TicketPurchase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TicketPurchase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TicketPurchase');
    }

    public function replicate(AuthUser $authUser, TicketPurchase $ticketPurchase): bool
    {
        return $authUser->can('Replicate:TicketPurchase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TicketPurchase');
    }

}