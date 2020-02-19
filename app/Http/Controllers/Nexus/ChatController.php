<?php

namespace App\Http\Controllers\Nexus;

use App\User;
use App\Message;
use Illuminate\Http\Request;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        xdebug_break();
        //
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
            $message = new Message;
            $message->read = false;
            $message->text = $input['text'];
            $message->user_id = $user->id;
            $message->time = time();
            $message->author_id = Auth::id();
            $message->save();
        }

        return redirect(action('Nexus\ChatController@conversation', ['username' => $username]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return the conversation between the logged in user
        // and $username
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
