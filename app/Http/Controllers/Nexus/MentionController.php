<?php

namespace App\Http\Controllers\Nexus;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class MentionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Remove all mentions for the logged-in user
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function destroyAll(Request $request)
    {
        $request->user()->clearMentions();
        return back();
    }
}
