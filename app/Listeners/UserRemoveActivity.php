<?php

namespace Nexus\Listeners;

use Nexus\Events;
use Nexus\User;
use Nexus\Helpers\ActivityHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

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
     * @param  Event  $event
     * @return void
     */
    public function handle($user)
    {
        $user = \Auth::user();
        ActivityHelper::removeActivity($user->id);
    }
}