<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
     * @return void
     */
    public function handle()
    {
        $user = Auth::user();
        $user->latestLogin = Carbon::now();
        $user->totalVisits = $user->totalVisits + 1;

        $user->save();
    }
}
