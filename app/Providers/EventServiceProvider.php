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
        \App\Events\SomeEvent::class => [
            \App\Listeners\EventListener::class,
        ],
        'Illuminate\Auth\Events\Login' => [
            \App\Listeners\UserIncreaseTotalVisits::class,
        ],
        'Illuminate\Auth\Events\Logout' => [
            \App\Listeners\UserRemoveActivity::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\TopicJumpCacheBecameDirty' => [
            'App\Listeners\DeleteTopicJumpCache'
        ],
        'App\Events\UserCreated' => [
            'App\Listeners\LogUnverifiedUser'
        ],
        'Illuminate\Auth\Events\Verified' => [
            'App\Listeners\LogVerifiedUser'
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
