<?php

namespace App\Policies;

use App\Models\User;

class MilestonePolicy
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
        return $user->hasPermissionTo('create-milestone','api');
    }

    public function view(User $user)
    {
        return $user->hasPermissionTo('view-milestone','api');
    }

    public function update(User $user)
    {
        return $user->hasPermissionTo('edit-milestone','api');
    }

    public function delete(User $user)
    {
        return $user->hasPermissionTo('delete-milestone','api');
    }
}
