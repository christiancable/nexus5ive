<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        // use bootstrap for pagination
        Paginator::useBootstrapFive();

        // In archive mode, deny all write operations for every user.
        // The admin can set NEXUS_ARCHIVE_MODE=false temporarily for maintenance.
        // The hook is always registered but checks the config at call time so that
        // tests can override it with Config::set('nexus.archive_mode', true/false).
        Gate::before(function ($user, string $ability) {
            if (config('nexus.archive_mode') && in_array($ability, ['create', 'update', 'delete', 'restore'])) {
                return false;
            }
        });
    }
}
