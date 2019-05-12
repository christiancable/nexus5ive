<?php

namespace App\Listeners;

use App\User;
use App\Events;
use Carbon\Carbon;
use App\Helpers\ActivityHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
