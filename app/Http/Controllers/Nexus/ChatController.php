<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request, ?User $user)
    {
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Chat');
        $selectedUser = null;

        // if there is not user then an empty $user passed to the controller
        if ($user->id ?? null !== null) {
            $selectedUser = $user;
        }
        if ($selectedUser) {
            // stop users talking to themselves
            if ($selectedUser->id == $request->user()->id) {
                $selectedUser = null;
            }
        }

        ActivityHelper::updateActivity(
            $request->user()->id,
            'Messages',
            action('App\Http\Controllers\Nexus\ChatController@index')
        );

        return view('nexus.chat.index', compact('breadcrumbs', 'selectedUser'));
    }
}
