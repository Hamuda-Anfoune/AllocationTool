<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-configuration', fn (User $user): Response => $user->isAdmin()
            ? Response::allow()
            : Response::deny('You are not authorized to manage configuration.'));

        $this->configurePasswordResetUrl();
    }

    /**
     * Provide a placeholder password reset link builder.
     *
     * No frontend UI exists yet for this API-only app, so there is no
     * `password.reset` named route for the default notification to build a
     * link from (it would otherwise throw RouteNotFoundException). Once a
     * frontend exists, replace this closure to build a real URL against it,
     * e.g. https://frontend.example/reset-password?token=...&email=....
     */
    private function configurePasswordResetUrl(): void
    {
        ResetPassword::createUrlUsing(fn (User $user, string $token): string => $token);
    }
}
