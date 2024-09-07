<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserIncreaseTotalVisits
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
    public function handle(object $event): void
    {
        $user = Auth::user();
        $user->latestLogin = Carbon::now();
        $user->totalVisits = $user->totalVisits + 1;

        $user->save();
    }
}
