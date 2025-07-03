<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function createInvestor(User $user)
    {
        return $user->hasPermissionTo('create-investor','api');
    }

    public function viewInvestor(User $user)
    {
        return $user->hasPermissionTo('view-investor','api');
    }

    public function updateInvestor(User $user)
    {
        return $user->hasPermissionTo('edit-investor','api');
    }

    public function deleteInvestor(User $user)
    {
        return $user->hasPermissionTo('delete-investor','api');
    }

    public function createTenant(User $user)
    {
        return $user->hasPermissionTo('create-tenant','api');
    }

    public function viewTenant(User $user)
    {
        return $user->hasPermissionTo('view-tenant','api');
    }

    public function updateTenant(User $user)
    {
        return $user->hasPermissionTo('edit-tenant','api');
    }

    public function deleteTenant(User $user)
    {
        return $user->hasPermissionTo('delete-tenant','api');
    }
}
