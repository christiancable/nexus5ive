<?php

namespace App\Http\Controllers\Nexus;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MentionController extends Controller
{
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
