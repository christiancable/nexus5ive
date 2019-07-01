<?php

namespace App\Http\Controllers\Nexus;

use Auth;
use App\User;
use App\Message;
use Illuminate\Http\Request;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      /**
     @param string $username
     @return Collection - messages between the auth'd user and the $user
*/
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

    public function conversation(Request $request, $username)
    {
        $user = User::where('username', $username)->first();
        if (null === $user) {
            return redirect(action('Nexus\MessageController@index'));
        }

        $sideOne = Message::with('author:id,username')
           ->with('user:id,username')
           ->where('user_id', Auth::id())
           ->where('author_id', $user->id)->get();
        
        $sideTwo = Message::with('author:id,username')
           ->with('user:id,username')
           ->where('user_id', $user->id)
           ->where('author_id', Auth::id())->get();
        
        $conversation = $sideOne->merge($sideTwo)
           ->sortBy('id');


        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Chat');

        // @TODO ADD THIS ELSEWHERE
        $recipients = Message::with('author', 'user')
           ->where('author_id', Auth::id())
           ->get()->pluck('user.username')->unique();
        $senders = Message::with('author', 'user')
           ->where('user_id', Auth::id())
           ->get()->pluck('author.username')->unique();

        $conversationPartners = $recipients->merge($senders)->unique()->sort(function ($a, $b) {
            return strnatcasecmp($a, $b);
        });
        
        return view('chat.index', compact('conversation', 'conversationPartners', 'breadcrumbs'));
        // return $conversation;
    }
}
