<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Verified;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param  Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        $level = config('nexus.log_verified_user_level');
        Log::$level("🎉 User verified: {$event->user->username} - {$event->user->email}");
    }
}
