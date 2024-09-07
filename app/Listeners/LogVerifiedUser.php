<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;

class LogVerifiedUser
{
    /**
     * Create the event listener.
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
    public function handle(object $event)
    {
        $level = config('nexus.log_verified_user_level');
        Log::$level("🎉 User verified: {$event->user->username} - {$event->user->email}");
    }
}
