<?php

namespace Nexus\Handlers\Events;

use Nexus\Events;
use Nexus\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class AuthLoginEventHandler
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
     * Handle the event.
     *
     * @param  The logged in User  $user
     * @param  $remember
     * @return void
     */
    public function handle(User $user, $remember)
    {

        // update the lastLogin
        $user->latestLogin = Carbon::now();

        // incrememt the total number of visits
        $user->totalVisits = $user->totalVisits + 1;

        $user->save();
    }
}