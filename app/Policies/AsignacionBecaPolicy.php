<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AsignacionBeca;
use Illuminate\Auth\Access\HandlesAuthorization;

class AsignacionBecaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AsignacionBeca');
    }

    public function view(AuthUser $authUser, AsignacionBeca $asignacionBeca): bool
    {
        return $authUser->can('View:AsignacionBeca');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AsignacionBeca');
    }

    public function update(AuthUser $authUser, AsignacionBeca $asignacionBeca): bool
    {
        return $authUser->can('Update:AsignacionBeca');
    }

    public function delete(AuthUser $authUser, AsignacionBeca $asignacionBeca): bool
    {
        return $authUser->can('Delete:AsignacionBeca');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AsignacionBeca');
    }

    public function restore(AuthUser $authUser, AsignacionBeca $asignacionBeca): bool
    {
        return $authUser->can('Restore:AsignacionBeca');
    }

    public function forceDelete(AuthUser $authUser, AsignacionBeca $asignacionBeca): bool
    {
        return $authUser->can('ForceDelete:AsignacionBeca');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AsignacionBeca');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AsignacionBeca');
    }

    public function replicate(AuthUser $authUser, AsignacionBeca $asignacionBeca): bool
    {
        return $authUser->can('Replicate:AsignacionBeca');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AsignacionBeca');
    }

}