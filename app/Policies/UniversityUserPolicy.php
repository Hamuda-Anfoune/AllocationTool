<?php

namespace App\Policies;

use App\Models\User;

class UniversityUserPolicy
{
    /**
     * Determine whether the user can view any university users.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can pre-authorize a new university user.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }
}
