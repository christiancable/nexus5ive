<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityHelper;

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
     * wwhen a user logs out then remove their current activity
     */
    public function handle(object $event): void
    {
        $user = Auth::user();
        ActivityHelper::removeActivity($user->id);
    }
}
