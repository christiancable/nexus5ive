<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator; // BS3 pagination - laravel upgrade 5.6
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
        // use Bootsrap 3 pagination - Upgrade Laravel 5.6
        Paginator::useBootstrapThree();
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
