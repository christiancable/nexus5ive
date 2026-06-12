<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request, ?User $user): View
    {
        Gate::authorize('viewAny', Chat::class);

        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Chat');
        $selectedUser = null;

        // if there is not user then an empty $user passed to the controller
        if (($user->id ?? null) !== null) {
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
            'Chat',
            action('App\Http\Controllers\Nexus\ChatController@index')
        );

        return view('nexus.chat.index', compact('breadcrumbs', 'selectedUser'));
    }
}
