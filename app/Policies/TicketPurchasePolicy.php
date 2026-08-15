<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TicketPurchase;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TicketPurchasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TicketPurchase');
    }

    public function view(AuthUser $authUser, TicketPurchase $ticketPurchase): bool
    {
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        return $authUser->can('View:TicketPurchase')
            && ($ticketPurchase->seller_id === $authUser->id || $ticketPurchase->created_by === $authUser->id);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TicketPurchase');
    }

    public function update(AuthUser $authUser, TicketPurchase $ticketPurchase): bool
    {
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        return $authUser->can('Update:TicketPurchase')
            && ($ticketPurchase->seller_id === $authUser->id || $ticketPurchase->created_by === $authUser->id);
    }

    public function delete(AuthUser $authUser, TicketPurchase $ticketPurchase): bool
    {
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        return $authUser->can('Delete:TicketPurchase')
            && ($ticketPurchase->seller_id === $authUser->id || $ticketPurchase->created_by === $authUser->id);
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
