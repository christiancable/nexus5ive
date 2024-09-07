<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityHelper;
use App\User;

class UserRemoveActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * when a user logs out then remove their current activity
     */
    public function handle()
    {
        $user = Auth::user();
        ActivityHelper::removeActivity($user->id);
    }
}
