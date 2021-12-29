<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // user reg
        \App\Events\SomeEvent::class => [
            \App\Listeners\EventListener::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\UserCreated::class => [
            \App\Listeners\LogUnverifiedUser::class
        ],
        \Illuminate\Auth\Events\Verified::class => [
            \App\Listeners\LogVerifiedUser::class
        ],

        // user activities
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\UserIncreaseTotalVisits::class,
        ],
        \Illuminate\Auth\Events\Logout::class => [
            \App\Listeners\UserRemoveActivity::class,
        ],

        // manage caches
        \App\Events\MostRecentPostForSectionBecameDirty::class => [
            \App\Listeners\DeleteSectionMostRecentPostCache::class
        ],
        \App\Events\TreeCacheBecameDirty::class => [
            \App\Listeners\DeleteTreeCache::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }
}
