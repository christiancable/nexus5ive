<?php

namespace App\Http\Controllers\Nexus;

use App\Comment;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\StoreComment;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

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
     * @param  StoreComment $request
     * @return RedirectResponse
     */
    public function store(StoreComment $request)
    {
        $input = $request->all();
        $input['author_id'] = $request->user()->id;
        
        // if a user is posting on their own profile then assume that they have read the comment
        if ($input['author_id'] === $input['user_id']) {
            $input['read'] = true;
        } else {
            $input['read'] = false;
        }
   
        Comment::create($input);

        return redirect("/users/" . $input['redirect_user'] . '#comments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function destroy(Request $request, Comment $comment)
    {
        $this->authorize('destroy', $comment);
        $comment->delete();
        
        return redirect(action('Nexus\UserController@show', ['user_name' => $request->user()->username]));
    }

    /**
     * removes all comments belonging to the logged in user
     *
     * @param Request $request
     * @return RedirectResponse - redirection to the calling page
     */
    public function destroyAll(Request $request)
    {
        $request->user()->clearComments();
        return back();
    }
}
