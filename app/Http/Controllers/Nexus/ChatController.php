<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        return $this->noConversation($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $username)
    {
        $user = User::where('username', $username)->first();
        if ($user === null) {
            return redirect(action('App\Http\Controllers\Nexus\ChatController@noConversation'));
        }

        $input = $request->all();

        if (isset($input['text']) && $input['text']) {
            $message = new Message;
            $message->read = false;
            $message->text = $input['text'];
            $message->user_id = $user->id;
            $message->time = Carbon::now();
            $message->author_id = Auth::id();
            $message->save();
        }

        return redirect(action('App\Http\Controllers\Nexus\ChatController@conversation', ['username' => $username]));
    }

    public function noConversation(Request $request)
    {
        $conversation = [];
        $currentPartner = '';

        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Chat');

        ActivityHelper::updateActivity(
            Auth::id(),
            'Messages',
            action('App\Http\Controllers\Nexus\ChatController@index')
        );

        // $conversationPartners = $this->chatList();

        return view('nexus.chat.index', compact('currentPartner', 'breadcrumbs'));
    }

    public function conversation(Request $request, $username)
    {

        $breadcrumbs = BreadcrumbHelper::breadcrumbForChat($username);

        ActivityHelper::updateActivity(
            Auth::id(),
            'Messages',
            action('App\Http\Controllers\Nexus\ChatController@index')
        );

        // @todo deal with empty conversation
        // $conversation = $this->getConversation($username);
        // $conversationPartners = $this->chatList();
        $currentPartner = $username;

        return view('nexus.chat.index', compact('currentPartner', 'breadcrumbs'));
    }
}
