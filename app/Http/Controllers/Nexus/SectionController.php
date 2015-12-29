<?php

namespace Nexus\Http\Controllers\Nexus;

use Illuminate\Http\Request;
use Nexus\Http\Requests;
use Nexus\Http\Controllers\Controller;

class SectionController extends Controller
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
     * @param  int  $section_id - default to the first section
     * @return Response
     */
    public function show($section_id = null)
    {
        if (!$section_id) {
            $section = \Nexus\Section::with('sections', 'topics')->first();
        } else {
            $section = \Nexus\Section::with('sections', 'topics')->where('id', $section_id)->first();
        }
        return view('sections.index')->with('section', $section);
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


    public function recentTopics($maxresults = 10)
    {
        $latestPosts = \Nexus\Post::orderBy('id', 'desc')->take($maxresults)->get(['topic_id'])->groupBy('topic_id');

        $topics = array();
        foreach ($latestPosts as $topic) {
            $topics[] = $topic[0]->topic;
        }

        return $topics;
    }

    public function latest()
    {
        $heading = 'Latest Posts';
        $lead = "The most recent posts from across Nexus";
        $topics = $this::recentTopics();
        return view('topics.unread', compact('topics', 'heading', 'lead'));
    }

    /**
     * redirects a visitor to a section  with an updated topic
     */
    public function leap()
    {
         // should we be passing the user_id into this method?
        $views = \Nexus\View::with('topic')->where('user_id', \Auth::user()->id)->where('unsubscribed', 0)->get();
    
        $topics = array();

        // trying to avoid N+1 problem by breaking out as soon as we have a result
        foreach ($views as $view) {
            if (!is_null($view->topic)) {
                if (($view->latest_view_date != $view->topic->most_recent_post_time)
                    && ($view->topic->most_recent_post_time)) {
                    $topics[] =  $view->topic;
                    break;
                }
            }
        }

        if (count($topics)) {
            // the parent section of the unread topic
            // redirect to section
            return redirect()->action('Nexus\SectionController@show', [$topics[0]->section->id])->with('topic', $topics[0]);
        } else {
            // set alert
            // redirect to main menu
            return redirect('/')->with('alert', true);
        }
    }
}
