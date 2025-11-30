<?php

namespace App\Http\Controllers\Nexus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Nexus\DestroyComment;
use App\Http\Requests\Nexus\StoreComment;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(StoreComment $request)
    {
        $input = $request->validated();
        $commentedUser = User::findOrFail($input['user_id']);

        $input['author_id'] = $request->user()->id;

        // if a user is posting on their own profile then assume that they have read the comment
        if ($input['author_id'] === $input['user_id']) {
            $input['read'] = true;
        } else {
            $input['read'] = false;
        }

        Comment::create($input);

        return redirect()->route('users.show', ['user' => $commentedUser])->withFragment('#comments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(DestroyComment $request, Comment $comment)
    {
        $comment->delete();
        return redirect()->route('users.show', ['user' => $request->user()]);
    }

    /**
     * removes all comments belonging to the logged in user
     *
     * @return RedirectResponse - redirection to the calling page
     */
    public function destroyAll(Request $request)
    {
        $request->user()->clearComments();

        return back();
    }
}
