<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;

class LogUnverifiedUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(object $event): void
    {
        Log::notice("User created: {$event->user->username} - {$event->user->email}");
    }
}
