<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GameTitle;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class GameTitlePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GameTitle');
    }

    public function view(AuthUser $authUser, GameTitle $gameTitle): bool
    {
        return $authUser->can('View:GameTitle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GameTitle');
    }

    public function update(AuthUser $authUser, GameTitle $gameTitle): bool
    {
        return $authUser->can('Update:GameTitle');
    }

    public function delete(AuthUser $authUser, GameTitle $gameTitle): bool
    {
        return $authUser->can('Delete:GameTitle');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GameTitle');
    }

    public function restore(AuthUser $authUser, GameTitle $gameTitle): bool
    {
        return $authUser->can('Restore:GameTitle');
    }

    public function forceDelete(AuthUser $authUser, GameTitle $gameTitle): bool
    {
        return $authUser->can('ForceDelete:GameTitle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GameTitle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GameTitle');
    }

    public function replicate(AuthUser $authUser, GameTitle $gameTitle): bool
    {
        return $authUser->can('Replicate:GameTitle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GameTitle');
    }
}
