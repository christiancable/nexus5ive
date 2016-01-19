<?php

namespace Nexus\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Nexus\Events\SomeEvent' => [
            'Nexus\Listeners\EventListener',
        ],
        'auth.login' => [
            'Nexus\Handlers\Events\AuthLoginEventHandler',
        ],
        'auth.logout' => [
            'Nexus\Handlers\Events\AuthLogoutEventHandler',
        ],

    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
