<?php

namespace Nexus\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use Nexus\Http\Requests;
use Nexus\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Displays a list of messages sent to the logged in user
     * @todo - generate $activeUsers array from a list of active users
     * @return Response
     */
    public function index()
    {
        $allMessages = \Nexus\Message::with('user')->with('author')->where('user_id', \Auth::user()->id)->orderBy('id', 'desc')->get()->all();
        $messages = array_slice($allMessages, 5);
        $recentMessages = array_reverse(array_slice($allMessages, 0, 5));
        $activeUsers = ['1' => 'Fraggle', '2' => 'Blew' ];
        // remove the current user from activeUsers
        return view('messages.index')->with(compact('messages', 'recentMessages', 'activeUsers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateCommentRequest  $request
     * @return Response
     */
    public function store(Requests\Message\CreateRequest $request)
    {
        $input = $request->all();
        $input['read'] = false;
        $input['user_id'] = $input['user_id'];
        $input['time'] = time();
        $input['author_id'] = \Auth::user()->id;

        \Nexus\Message::create($input);

        return redirect(action('Nexus\MessageController@index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
