<?php

namespace App\Http\Controllers\Nexus;

use App\Post;
use App\Topic;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\MentionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
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
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        // validate - errors are passed back to the vue form automatically
        Validator::make(
            $request->all(),
            [
                'text' => 'required',
                'topic_id' => 'required|exists:topics,id'
            ],
            [
                "text.required" => 'Text is required. You cannot leave empty posts',
            ]
        )->validate();

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
            $redirect = action('Nexus\TopicController@show', ['id' => $post->topic_id]);
        } else {
            $redirect = action('Nexus\TopicController@show', ['id' => $post->topic_id]) . '#'  . $post->id;
        }
        return redirect($redirect);
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
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required:in' . $id . '|exists:posts,id',
                'form.'.$request->input('id').'.text' => 'required',
            ],
            [
                'form.'.$request->input('id').'.text.required' => 'Posts cannot be empty',
                'id.required' => 'Post does not exist'
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator, "postUpdate$id")->withInput();
        }

        // get post and auth
        $post = Post::findOrFail($id);
        $this->authorize('update', [Post::class, $post]);
        
        $input = $request->all();
        $updatedPost = $input['form']["$id"];
        $updatedPost['update_user_id'] = $request->user()->id;
        
        $post->update($updatedPost);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $this->authorize('delete', $post);

        // using forceDelete here because in this case we do not want a soft delete
        $topicID = $post->topic_id;
        $post->forceDelete();
        return redirect()->route('topic.show', ['id' => $post->topic_id]);
    }
}
