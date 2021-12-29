<?php

namespace App\Http\Controllers\Nexus;

use App\User;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Nexus\ChatApiController;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    public function index(Request $request)
    {
        return $this->noConversation($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $username)
    {
        $user = User::where('username', $username)->first();
        if (null === $user) {
            return redirect(action('Nexus\ChatController@noConversation'));
        }

        $input = $request->all();

        if (isset($input['text']) && $input['text']) {
            $message = new Message();
            $message->read = false;
            $message->text = $input['text'];
            $message->user_id = $user->id;
            $message->time = Carbon::now();
            $message->author_id = Auth::id();
            $message->save();
        }

        return redirect(action('Nexus\ChatController@conversation', ['username' => $username]));
    }

    public function noConversation(Request $request)
    {
        $conversation = [];
        $currentPartner = '';

        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Chat');

            ActivityHelper::updateActivity(
                Auth::id(),
                "Messages",
                action('Nexus\ChatController@index')
            );

        // $conversationPartners = $this->chatList();


        return view('chat.index', compact('currentPartner', 'breadcrumbs'));
    }


    public function conversation(Request $request, $username)
    {

        $breadcrumbs = BreadcrumbHelper::breadcrumbForChat($username);

        ActivityHelper::updateActivity(
            Auth::id(),
            "Messages",
            action('Nexus\ChatController@index')
        );

        // @todo deal with empty conversation
        // $conversation = $this->getConversation($username);
        // $conversationPartners = $this->chatList();
        $currentPartner = $username;

        return view('chat.index', compact('currentPartner', 'breadcrumbs'));
    }
}
