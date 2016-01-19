<?php

namespace Nexus\Handlers\Events;

use Nexus\Events;
use Nexus\User;
use Nexus\Helpers\ActivityHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class AuthLogoutEventHandler
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
     * @param  The logged in User  $user
     * @return void
     */
    public function handle(User $user)
    {
        ActivityHelper::removeActivity($user->id);
    }
}
