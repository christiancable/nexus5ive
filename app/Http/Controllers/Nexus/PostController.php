<?php

namespace App\Http\Controllers\Nexus;

use App\Post;
use App\Topic;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\MentionHelper;
use App\Http\Requests\StorePost;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StorePost  $request
     * @return RedirectResponse
     */
    public function store(StorePost $request)
    {
        $topic = Topic::findOrFail($request->topic_id);
        $this->authorize('create', [Post::class, $topic]);

        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $input['popname'] = $request->user()->popname;
        $input['time'] = time();
        $post = Post::create($input);
        $request->user()->incrementTotalPosts();

        // scan post for mentions
        MentionHelper::makeMentions($post);
           
        // if we are viewing the topic with the most recent post at the bottom then
        // redirect to that point in the page
        if ($request->user()->viewLatestPostFirst) {
            $redirect = action('Nexus\TopicController@show', ['topic' => $post->topic_id]);
        } else {
            $redirect = action('Nexus\TopicController@show', ['topic' => $post->topic_id]) . '#'  . $post->id;
        }
        return redirect($redirect);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return RedirectResponse
     */
    public function update(Request $request, Post $post)
    {
        // manually create validator here to dynamically name the errorbag based on the post id
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required:in' . $post->id . '|exists:posts,id',
                'form.'.$request->input('id').'.text' => 'required',
            ],
            [
                'form.'.$request->input('id').'.text.required' => 'Posts cannot be empty',
                'id.required' => 'Post does not exist'
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator, "postUpdate{$post->id}")->withInput();
        }

        $this->authorize('update', [Post::class, $post]);
        
        $input = $request->all();
        $updatedPost = $input['form']["$post->id"];
        $updatedPost['update_user_id'] = $request->user()->id;
        
        $post->update($updatedPost);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Post $post
     * @return RedirectResponse
     */
    public function destroy(Request $request, Post $post)
    {
        $this->authorize('delete', $post);

        // using forceDelete here because in this case we do not want a soft delete
        $post->forceDelete();
        return redirect()->route('topic.show', ['topic' => $post->topic_id]);
    }
}
