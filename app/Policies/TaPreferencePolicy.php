<?php

namespace App\Policies;

use App\Models\TaPreference;
use App\Models\User;
use App\Policies\Concerns\AuthorizesWithMessage;
use Illuminate\Auth\Access\Response;

class TaPreferencePolicy
{
    use AuthorizesWithMessage;

    /**
     * Determine whether the user can submit a new TA preference for themselves.
     */
    public function create(User $user): bool
    {
        return $user->isTaOrGta();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaPreference $taPreference): Response
    {
        return $this->authorizeAccessTo($user, $taPreference);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaPreference $taPreference): Response
    {
        return $this->authorizeAccessTo($user, $taPreference);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaPreference $taPreference): Response
    {
        return $this->authorizeAccessTo($user, $taPreference);
    }

    /**
     * Admins may access any TA preference; a TA/GTA may only access their own. Convenors may not access this at all.
     */
    private function authorizeAccessTo(User $user, TaPreference $taPreference): Response
    {
        return $this->allowIf(
            $user->isAdmin() || ($user->isTaOrGta() && $user->email === $taPreference->ta_email),
            'You are not authorized to access this preference.'
        );
    }
}
