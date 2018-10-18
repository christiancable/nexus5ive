<?php
namespace App\Http\Controllers\Nexus;

use Auth;
use Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class MentionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Remove all mentions for the logged-in user
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function destroyAll(Request $request)
    {
        Auth::user()->clearMentions();
        return back();
    }
}
