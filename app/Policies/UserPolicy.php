<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view admin-only listings (all users, TAs, convenors, missing-preference lists).
     */
    public function viewAny(User $user): Response
    {
        if ($user->isAdmin()) {
            return Response::allow();
        }

        return Response::deny('Sorry, only admins can view this information.');
    }

    /**
     * Determine whether the user can view the full list of admins.
     */
    public function viewAdmins(User $user): Response
    {
        if ($user->isSuperAdmin()) {
            return Response::allow();
        }

        return Response::deny('Sorry, only super admins can view this information.');
    }

    /**
     * Admins may view any TA's preferences; a TA/GTA may only view their own.
     */
    public function viewTaPreferences(User $user, User $target): Response
    {
        if ($user->isAdmin() || ($user->isTaOrGta() && $user->email === $target->email)) {
            return Response::allow();
        }

        return Response::deny('Sorry, only admins and teaching assistants can view this information.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $target): bool
    {
        return $user->isAdmin();
    }
}
