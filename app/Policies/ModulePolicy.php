<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ModulePolicy
{
    /**
     * Determine whether the user can browse modules' preference status (with/without submitted preferences).
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isConvenor();
    }

    /**
     * Determine whether the user can create a new module.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can submit a module's preferences.
     */
    public function submitPreferences(User $user, Module $module): Response
    {
        return $this->authorizeOwnershipOf($user, $module);
    }

    /**
     * Determine whether the user can update the module's preferences.
     */
    public function update(User $user, Module $module): Response
    {
        return $this->authorizeOwnershipOf($user, $module);
    }

    /**
     * Determine whether the user can delete the module's preferences.
     */
    public function delete(User $user, Module $module): Response
    {
        return $this->authorizeOwnershipOf($user, $module);
    }

    /**
     * Admins may act on any module; a convenor may only act on their own.
     */
    private function authorizeOwnershipOf(User $user, Module $module): Response
    {
        if ($user->isAdmin() || ($user->isConvenor() && $user->email === $module->convenor_email)) {
            return Response::allow();
        }

        return Response::deny("You are not authorized to modify this module's preferences.");
    }
}
