<?php

namespace App\Policies;

use App\Models\User;

class AcademicYearPolicy
{
    /**
     * Determine whether the user can update the current academic year.
     */
    public function update(User $user): bool
    {
        return $user->isAdmin();
    }
}
