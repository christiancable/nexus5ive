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
    public function show($section_id = 1)
    {
        $section = \Nexus\Section::with('sections', 'topics')->where('id', $section_id)->first();
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


        /**
     * Retuns a list of updated topics
     * @param  integer $maxResults maximum number of topics to return
     * @return Collection list of topics
     */
    public function updatedTopics($maxResults = 10)
    {

        // should we be passing the user_id into this method?
        $views = \Nexus\View::with('topic')->where('user_id', \Auth::user()->id)->where('unsubscribe', 0)->get();
        // @todo - this query fetches in views where we might not have an existant topic - we skip these in the loop
        // can we just update this to prevent fetching them at all?

        $breakoutCount = 0;
        $topics = array();

        // N+1 problem
        foreach ($views as $view) {
            if (!is_null($view->topic)) {
                if ($view->msg_date != $view->topic->most_recent_post_time) {
                    $topics[] =  $view->topic;
                    
                    // terrible code
                    $breakoutCount++;
                    if ($breakoutCount >= $maxResults) {
                        break;
                    }
                }
            }
        }

        return $topics;
    }

    public function recentTopics($maxresults = 10)
    {
       
        $latestPosts = \Nexus\Post::orderBy('message_id', 'desc')->take($maxresults)->get(['topic_id'])->groupBy('topic_id');

        $topics = array();
        foreach ($latestPosts as $topic) {
            $topics[] = $topic[0]->topic;
        }

        return $topics;
    }

    /**
     *
     * show a list of unread topics
     */
    public function unread()
    {

        $topics = $this::updatedTopics();
        return view('topics.unread', compact('topics'));
    }

    public function latest()
    {
        $heading = 'Latest Posts';
        $lead = "The most recent posts from across Nexus";
        $topics = $this::recentTopics();
        return view('topics.unread', compact('topics', 'heading', 'lead'));
    }

    /**
     * redirects a visitor to a section with an updated topic
     */
    public function leap()
    {
        $numberOfTopics = 1;
        $topics = $this::updatedTopics($numberOfTopics);

        if (count($topics)) {
            // the parent section of the unread topic
            // redirect to section
            return redirect()->action('Nexus\SectionController@show', [$topics[0]->section->section_id])->with('topic', $topics[0]);
        } else {
            // set alert
            // redirect to main menu
            return redirect('/')->with('alert', true);
        }
    }
}
