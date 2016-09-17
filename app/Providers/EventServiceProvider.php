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
        'Illuminate\Auth\Events\Login' => [
            'Nexus\Handlers\Events\AuthLoginEventHandler',
        ],
        'Illuminate\Auth\Events\Logout' => [
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
