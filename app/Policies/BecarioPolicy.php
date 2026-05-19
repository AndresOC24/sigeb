<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Becario;
use Illuminate\Auth\Access\HandlesAuthorization;

class BecarioPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Becario');
    }

    public function view(AuthUser $authUser, Becario $becario): bool
    {
        return $authUser->can('View:Becario');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Becario');
    }

    public function update(AuthUser $authUser, Becario $becario): bool
    {
        return $authUser->can('Update:Becario');
    }

    public function delete(AuthUser $authUser, Becario $becario): bool
    {
        return $authUser->can('Delete:Becario');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Becario');
    }

    public function restore(AuthUser $authUser, Becario $becario): bool
    {
        return $authUser->can('Restore:Becario');
    }

    public function forceDelete(AuthUser $authUser, Becario $becario): bool
    {
        return $authUser->can('ForceDelete:Becario');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Becario');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Becario');
    }

    public function replicate(AuthUser $authUser, Becario $becario): bool
    {
        return $authUser->can('Replicate:Becario');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Becario');
    }

}