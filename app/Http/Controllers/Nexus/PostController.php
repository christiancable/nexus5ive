<?php

namespace App\Http\Controllers\Nexus;

use App\Post;
use App\Topic;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
     * @return Response
     */
    public function store(Requests\Post\CreateRequest $request)
    {
        $topic = Topic::findOrFail($request->topic_id);
        $this->authorize('create', [Post::class, $topic]);

        $input = $request->all();
        $input['user_id'] = \Auth::user()->id;
        $input['popname'] = \Auth::user()->popname;
        $input['time'] = time();
        $post = \App\Post::create($input);
        \Auth::user()->incrementTotalPosts();

        // scan post for mentions
        \App\Helpers\MentionHelper::makeMentions($post);
        
        
        // if we are viewing the topic with the most recent post at the bottom then
        // redirect to that point in the page
        if (\Auth::user()->viewLatestPostFirst) {
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
     * @return Response
     */
    public function update(Requests\Post\UpdateRequest $request, $id)
    {
        // get post and autheorize
        $post = \App\Post::findOrFail($id);
        $this->authorize('update', [Post::class, $post]);
        
        // copy the namespaced input files back into top level input
        $input = $request->all();
        $input['title'] = $input['form'][$id]['title'];
        $input['text'] = $input['form'][$id]['text'];
        
        // update who last updated the post
        $input['update_user_id'] = \Auth::user()->id;
        $post->update($input);
        return redirect()->route('topic.show', ['id' => $post->topic_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $post = \App\Post::findOrFail($id);
        $this->authorize('delete', $post);

        // using forceDelete here because in this case we do not want a soft delete
        $topicID = $post->topic_id;
        $post->forceDelete();
        return redirect()->route('topic.show', ['id' => $post->topic_id]);
    }

    /**
     * @param request
     * @return json - including markdown rendered version of the text field from the request
     */
    public function previewPost(Request $request)
    {
        if (\Request::ajax()) {
            $data = \Input::all();
            $response = [];
            $response['text'] = \App\Helpers\NxCodeHelper::nxDecode($data['text']);
            return \Response::json($response);
        }
    }
}
