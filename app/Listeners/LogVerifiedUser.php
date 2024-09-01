<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;

class LogVerifiedUser
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
    public function handle(Verified $event)
    {
        $level = config('nexus.log_verified_user_level');
        Log::$level("🎉 User verified: {$event->user->username} - {$event->user->email}");
    }
}
