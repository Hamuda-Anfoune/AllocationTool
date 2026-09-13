<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\AuthorizesWithMessage;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use AuthorizesWithMessage;

    /**
     * Determine whether the user can view admin-only listings (all users, TAs, convenors, missing-preference lists).
     */
    public function viewAny(User $user): Response
    {
        return $this->allowIf($user->isAdmin(), 'Sorry, only admins can view this information.');
    }

    /**
     * Determine whether the user can view the full list of admins.
     */
    public function viewAdmins(User $user): Response
    {
        return $this->allowIf($user->isSuperAdmin(), 'Sorry, only super admins can view this information.');
    }

    /**
     * Admins may view any TA's preferences; a TA/GTA may only view their own.
     */
    public function viewTaPreferences(User $user, User $target): Response
    {
        return $this->allowIf(
            $user->isAdmin() || ($user->isTaOrGta() && $user->email === $target->email),
            'Sorry, only admins and teaching assistants can view this information.'
        );
    }

    /**
     * Determine whether the user can delete a user account.
     */
    public function delete(User $user): Response
    {
        return $this->allowIf($user->isAdmin(), 'Sorry, only admins can delete users.');
    }
}
