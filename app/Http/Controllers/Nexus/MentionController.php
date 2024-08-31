<?php

namespace App\Http\Controllers\Nexus;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
     * @return RedirectResponse
     */
    public function destroyAll(Request $request)
    {
        $request->user()->clearMentions();

        return back();
    }
}
