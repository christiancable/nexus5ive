<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Verified;

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
     */
    public function handle(Verified $event): void
    {
        /** @var \App\Models\User $user */
        $user = $event->user;
        $level = config('nexus.log_verified_user_level');
        Log::$level("🎉 User verified: {$user->username} - {$user->email}");
    }
}
