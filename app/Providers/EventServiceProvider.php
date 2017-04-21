<?php

namespace Nexus\Providers;

use Illuminate\Support\Facades\Event;
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
            'Nexus\Listeners\UserIncreaseTotalVisits',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'Nexus\Listeners\UserRemoveActivity',
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
