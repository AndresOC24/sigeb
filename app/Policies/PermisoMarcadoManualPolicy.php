<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PermisoMarcadoManual;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermisoMarcadoManualPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PermisoMarcadoManual');
    }

    public function view(AuthUser $authUser, PermisoMarcadoManual $permisoMarcadoManual): bool
    {
        return $authUser->can('View:PermisoMarcadoManual');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PermisoMarcadoManual');
    }

    public function update(AuthUser $authUser, PermisoMarcadoManual $permisoMarcadoManual): bool
    {
        return $authUser->can('Update:PermisoMarcadoManual');
    }

    public function delete(AuthUser $authUser, PermisoMarcadoManual $permisoMarcadoManual): bool
    {
        return $authUser->can('Delete:PermisoMarcadoManual');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PermisoMarcadoManual');
    }

    public function restore(AuthUser $authUser, PermisoMarcadoManual $permisoMarcadoManual): bool
    {
        return $authUser->can('Restore:PermisoMarcadoManual');
    }

    public function forceDelete(AuthUser $authUser, PermisoMarcadoManual $permisoMarcadoManual): bool
    {
        return $authUser->can('ForceDelete:PermisoMarcadoManual');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PermisoMarcadoManual');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PermisoMarcadoManual');
    }

    public function replicate(AuthUser $authUser, PermisoMarcadoManual $permisoMarcadoManual): bool
    {
        return $authUser->can('Replicate:PermisoMarcadoManual');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PermisoMarcadoManual');
    }

}