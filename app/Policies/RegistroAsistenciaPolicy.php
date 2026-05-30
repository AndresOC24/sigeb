<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RegistroAsistencia;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegistroAsistenciaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RegistroAsistencia');
    }

    public function view(AuthUser $authUser, RegistroAsistencia $registroAsistencia): bool
    {
        return $authUser->can('View:RegistroAsistencia');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RegistroAsistencia');
    }

    public function update(AuthUser $authUser, RegistroAsistencia $registroAsistencia): bool
    {
        return $authUser->can('Update:RegistroAsistencia');
    }

    public function delete(AuthUser $authUser, RegistroAsistencia $registroAsistencia): bool
    {
        return $authUser->can('Delete:RegistroAsistencia');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RegistroAsistencia');
    }

    public function restore(AuthUser $authUser, RegistroAsistencia $registroAsistencia): bool
    {
        return $authUser->can('Restore:RegistroAsistencia');
    }

    public function forceDelete(AuthUser $authUser, RegistroAsistencia $registroAsistencia): bool
    {
        return $authUser->can('ForceDelete:RegistroAsistencia');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RegistroAsistencia');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RegistroAsistencia');
    }

    public function replicate(AuthUser $authUser, RegistroAsistencia $registroAsistencia): bool
    {
        return $authUser->can('Replicate:RegistroAsistencia');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RegistroAsistencia');
    }

}