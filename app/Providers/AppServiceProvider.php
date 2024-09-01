<?php

namespace App\Providers;

use App\Mode;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
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

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
