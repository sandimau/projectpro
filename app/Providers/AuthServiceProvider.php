<?php

namespace App\Providers;

use App\Auth\Permissions;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            if (! $user instanceof User) {
                return null;
            }

            $aliases = Permissions::legacyAliases();
            if (! isset($aliases[$ability])) {
                return null;
            }

            return $user->hasAnyPermission($aliases[$ability]) ? true : null;
        });
    }
}
