<?php

namespace App\Listeners;

use App\Events;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class UserIncreaseTotalVisits
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
     * @param  Event  $event
     * @return void
     */
    public function handle($event)
    {

        $user = \Auth::user();
        $user->latestLogin = Carbon::now();

        // incrememt the total number of visits
        $user->totalVisits = $user->totalVisits + 1;

        $user->save();
    }
}
