<?php

namespace App\Policies;

use App\Models\User;

class AllocationPolicy
{
    /**
     * Determine whether the user can view allocation data.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can run a new allocation.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete allocation data.
     */
    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }
}
