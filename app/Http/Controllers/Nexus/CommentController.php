<?php

namespace App\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
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
     * @param  Requests\Comment\Create  $request
     * @return Response
     */
    public function store(Requests\Comment\CreateRequest $request)
    {
        $input = $request->all();

        // dd($input);
        // @todo - this is the best way to get the current logged in user?
        $input['author_id'] = \Auth::user()->id;
        
        // if a user is posting on their own profile then assume that they have read the comment
        if ($input['author_id'] == $input['user_id']) {
            $input['read'] = true;
        } else {
            $input['read'] = false;
        }
   
        \App\Comment::create($input);

        return redirect("/users/" . $input['redirect_user']);
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
    public function destroy(Request $request, \App\Comment $comment)
    {
        $this->authorize('destroy', $comment);
        $comment->delete();
        
        return redirect(action('Nexus\UserController@show', ['user_name' => \Auth::user()->username]));
    }

    /**
     * removes all comments belonging to the logged in user
     *
     * @param Request $request
     * @return Response - redirection to the calling page
     */
    public function destroyAll(Request $request)
    {
        \Auth::user()->clearComments();
        return back();
    }
}
