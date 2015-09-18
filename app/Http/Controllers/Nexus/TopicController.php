<?php

namespace Nexus\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use Nexus\Http\Requests;
use Nexus\Http\Controllers\Controller;

class TopicController extends Controller
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
    public function show($topic_id)
    {

        $posts = \Nexus\Post::where('topic_id', $topic_id)->orderBy('message_id', 'dsc');
        $topic = \Nexus\Topic::where('topic_id', $topic_id)->first();

        // is this topic readonly to the authenticated user?
        $readonly = true;

        if ($topic->read_only = false) {
            $readonly = false;
        }

        if ($topic->section->moderator->id === \Auth::user()->id) {
            $readonly = false;
        }

        if (\Auth::user()->administrator) {
            $readonly = false;
        }

        // is this topic secret to the authenticated user?
        $userCanSeeSecrets = false;

        if ($topic->section->moderator->id === \Auth::user()->id) {
            $userCanSeeSecrets = true;
        }

        if (\Auth::user()->administrator) {
            $userCanSeeSecrets = true;
        }

        // store the user's read progress of the current topic
    
        $latestPost = $posts->first();
        $lastestView = \Nexus\View::where('topic_id', $topic_id)->where('user_id', \Auth::user()->id)->first();

        if ($lastestView) {
            $lastestView->msg_date = $latestPost->message_time;
            $lastestView->update();
        } else {
            $view = new \Nexus\View;
            $view->user_id = \Auth::user()->id;
            $view->topic_id = $topic->topic_id;
            $view->msg_date = $latestPost->message_time;
            $view->save();
        }

        return view('topics.index', compact('topic', 'posts', 'readonly', 'userCanSeeSecrets'));
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
