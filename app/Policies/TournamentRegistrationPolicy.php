<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TournamentRegistration;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TournamentRegistrationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TournamentRegistration');
    }

    public function view(AuthUser $authUser, TournamentRegistration $tournamentRegistration): bool
    {
        return $authUser->can('View:TournamentRegistration');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TournamentRegistration');
    }

    public function update(AuthUser $authUser, TournamentRegistration $tournamentRegistration): bool
    {
        return $authUser->can('Update:TournamentRegistration');
    }

    public function delete(AuthUser $authUser, TournamentRegistration $tournamentRegistration): bool
    {
        return $authUser->can('Delete:TournamentRegistration');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TournamentRegistration');
    }

    public function restore(AuthUser $authUser, TournamentRegistration $tournamentRegistration): bool
    {
        return $authUser->can('Restore:TournamentRegistration');
    }

    public function forceDelete(AuthUser $authUser, TournamentRegistration $tournamentRegistration): bool
    {
        return $authUser->can('ForceDelete:TournamentRegistration');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TournamentRegistration');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TournamentRegistration');
    }

    public function replicate(AuthUser $authUser, TournamentRegistration $tournamentRegistration): bool
    {
        return $authUser->can('Replicate:TournamentRegistration');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TournamentRegistration');
    }
}
