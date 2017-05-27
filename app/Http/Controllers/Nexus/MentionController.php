<?php

namespace App\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Redirect;

class MentionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Remove all mentions for the logged-in user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        Auth::user()->clearMentions();
        return back();
    }
}
