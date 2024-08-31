<?php

namespace App\Listeners;

use App\Helpers\ActivityHelper;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserRemoveActivity
{
    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * wwhen a user logs out then remove their current activity
     *
     * @return void
     */
    public function handle()
    {
        $user = Auth::user();
        ActivityHelper::removeActivity($user->id);
    }
}
