<?php

namespace App\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TopicController extends Controller
{
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($section_id, $topic_id)
    {

        $posts = \App\Nexus\Post::where('topic_id', $topic_id)->orderBy('message_id', 'dsc');
        $topic = \App\Nexus\Topic::where('topic_id', $topic_id)->where('section_id', $section_id)->first();


        // {
        //     return $this->hasMany('App\Nexus\Post', 'topic_id', 'topic_id')->orderBy('message_id', 'asc');
        // }
        // $posts = $topic->posts->paginate(10);
        return view('topics.index')->with('topic', $topic)->with('posts', $posts);

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
