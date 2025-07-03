<?php

namespace App\Policies;

use App\Models\User;

class PhasePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create-phase','api');
    }

    public function view(User $user)
    {
        return $user->hasPermissionTo('view-phase','api');
    }

    public function update(User $user)
    {
        return $user->hasPermissionTo('edit-phase','api');
    }

    public function delete(User $user)
    {
        return $user->hasPermissionTo('delete-phase','api');
    }

}
