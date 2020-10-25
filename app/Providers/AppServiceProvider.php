<?php

namespace App\Providers;

use App\Mode;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
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
        // @todo make this bit suck less
        $seconds_to_cache = 60;
        $mode = Cache::remember('bbs_mode', $seconds_to_cache, function () {
            return Mode::active()->with('theme')->first();
        });

        View::share('mode', $mode);
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
