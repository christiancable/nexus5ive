<?php

namespace App\Http\Controllers\Nexus;

use Auth;
use App\User;
use App\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recipients = Message::with('author', 'user')
           ->where('author_id', Auth::id())
           ->get()->pluck('user.username')->unique();
        $senders = Message::with('author', 'user')
           ->where('user_id', Auth::id())
           ->get()->pluck('author.username')->unique();

        $conversationPartners = $recipients->merge($senders)->unique()->sort(function ($a, $b) {
            return strnatcasecmp($a, $b);
        });

        /* hacky until refactor of chat */
        $chats = [];

        foreach ($conversationPartners as $username) {
            try {
                $conversationPartner = User::where('username', $username)->firstOrFail();
                $unreadCount = Message::where(
                    [
                        ['author_id', $conversationPartner->id],
                        ['user_id', Auth::id()],
                        ['read',0]
                    ]
                )->count();
                $chats[] = [
                    'username' => $username,
                    'unread'   => $unreadCount
                ];
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return $chats;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function chatPartnerIndex()
    {
        return User::verified()->orderBy('username')->pluck('username');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $conversation
     * @return \Illuminate\Http\Response
     */
    public function show(String $conversation)
    {
        $user = User::where('username', $conversation)->first();
        
        if (null === $user) {
            return [];
        }
        
        $sideOneMessages = Message::with('author:id,username')
           ->with('user:id,username')
           ->where('user_id', Auth::id())
           ->where('author_id', $user->id);

        // TODO mark messages in conversation as read
        $sideOneMessages->update(['read' => 1]);

        $sideOne = $sideOneMessages->get();
        
        $sideTwo = Message::with('author:id,username')
           ->with('user:id,username')
           ->where('user_id', $user->id)
           ->where('author_id', Auth::id())->get();
        
        $conversation = $sideOne->merge($sideTwo)
           ->sortBy('id');
        
        // turn this into an array
        $return = [];
        foreach ($conversation->toArray() as $message) {
            $return[] = $message;
        }

        return $return;
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
}
