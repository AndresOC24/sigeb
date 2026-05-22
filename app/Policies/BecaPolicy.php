<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Beca;
use Illuminate\Auth\Access\HandlesAuthorization;

class BecaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Beca');
    }

    public function view(AuthUser $authUser, Beca $beca): bool
    {
        return $authUser->can('View:Beca');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Beca');
    }

    public function update(AuthUser $authUser, Beca $beca): bool
    {
        return $authUser->can('Update:Beca');
    }

    public function delete(AuthUser $authUser, Beca $beca): bool
    {
        return $authUser->can('Delete:Beca');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Beca');
    }

    public function restore(AuthUser $authUser, Beca $beca): bool
    {
        return $authUser->can('Restore:Beca');
    }

    public function forceDelete(AuthUser $authUser, Beca $beca): bool
    {
        return $authUser->can('ForceDelete:Beca');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Beca');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Beca');
    }

    public function replicate(AuthUser $authUser, Beca $beca): bool
    {
        return $authUser->can('Replicate:Beca');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Beca');
    }

}