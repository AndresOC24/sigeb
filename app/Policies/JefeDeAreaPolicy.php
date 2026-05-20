<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\JefeDeArea;
use Illuminate\Auth\Access\HandlesAuthorization;

class JefeDeAreaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JefeDeArea');
    }

    public function view(AuthUser $authUser, JefeDeArea $jefeDeArea): bool
    {
        return $authUser->can('View:JefeDeArea');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JefeDeArea');
    }

    public function update(AuthUser $authUser, JefeDeArea $jefeDeArea): bool
    {
        return $authUser->can('Update:JefeDeArea');
    }

    public function delete(AuthUser $authUser, JefeDeArea $jefeDeArea): bool
    {
        return $authUser->can('Delete:JefeDeArea');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JefeDeArea');
    }

    public function restore(AuthUser $authUser, JefeDeArea $jefeDeArea): bool
    {
        return $authUser->can('Restore:JefeDeArea');
    }

    public function forceDelete(AuthUser $authUser, JefeDeArea $jefeDeArea): bool
    {
        return $authUser->can('ForceDelete:JefeDeArea');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JefeDeArea');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JefeDeArea');
    }

    public function replicate(AuthUser $authUser, JefeDeArea $jefeDeArea): bool
    {
        return $authUser->can('Replicate:JefeDeArea');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JefeDeArea');
    }

}