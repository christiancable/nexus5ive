<?php

namespace App\Http\Controllers\Nexus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    /*
    this is to keep the routes used by the vue based notifications widget until 
    that is replaced by a livewire component

    they live here because closures in the routes file cannot be cached and this blocks deployment
    */

    public function notificationCount()
    {
        return Auth::user()->notificationCount();
    }

    public function toolbar()
    {
        return response()->view('_toolbar');
    }

}
