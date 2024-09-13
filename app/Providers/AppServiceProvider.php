<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

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
           // share bbs mode to all views
        // wrap in try because this isn't aways run when we have a db
        // @todo make this bit suck less
        try {
            $seconds_to_cache = 3600;
            $mode = Cache::remember('bbs_mode', $seconds_to_cache, function () {
                return Mode::active()->with('theme')->first();
            });
        } catch (\Throwable $th) {
            $mode = null;
        }

        View::share('mode', $mode);

        // use bootstrap for pagination
        Paginator::useBootstrap();
    }
}
