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
    public function store(Requests\Section\CreateRequest $request)
    {
        $formName = "sectionCreate";
        $input = $request->all();
        $input['parent_id'] = $input['form'][$formName]['parent_id'];
        $input['user_id'] = $input['form'][$formName]['user_id'];
        $input['title'] = $input['form'][$formName]['title'];
        $input['intro'] = $input['form'][$formName]['intro'];
        
        $section = \Nexus\Section::create($input);
        $redirect = action('Nexus\SectionController@show', ['id' => $section->id]);
        return redirect($redirect);
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

        \Nexus\Helpers\ActivityHelper::updateActivity(
            "Browsing <em>{$section->title}</em>",
            action('Nexus\SectionController@show', ['id' => $section->id]),
            \Auth::user()->id
        );

        $breadcrumbs = \Nexus\Helpers\BreadcrumbHelper::breadcrumbForSection($section);
        return view('sections.index', compact('section', 'breadcrumbs'));
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
    public function update(Requests\Section\UpdateRequest $request, $id)
    {
        $input = $request->all();
        $formName = "section{$id}";

        /*
        main menu has no parent so gets empty string 
        we need to explicitly set this to null
        */
        if (strlen($input['form'][$formName]['parent_id']) !== 0) {
            $input['parent_id'] = $input['form'][$formName]['parent_id'];
        } else {
            $input['parent_id'] = null;
        }
        
        $input['title'] = $input['form'][$formName]['title'];
        $input['intro'] = $input['form'][$formName]['intro'];
        $input['weight'] = $input['form'][$formName]['weight'];
        $input['user_id'] = $input['form'][$formName]['user_id'];

        $section = \Nexus\Section::findOrFail($id);
        $section->update($input);

        return redirect()->route('section.show', ['id' => $section->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  section id  $id
     * @param  Section\Destroy $request
     * @return Response
     */
    public function destroy(Requests\Section\DestroyRequest $request, $id)
    {
        $section = \Nexus\Section::findOrFail($id);
        $parent_id = $section->parent_id;
        $section->delete();
        $redirect = action('Nexus\SectionController@show', ['id' => $parent_id]);
        return redirect($redirect);
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
        $breadcrumbs = \Nexus\Helpers\BreadcrumbHelper::breadcumbForUtility($heading);

        return view('topics.unread', compact('topics', 'heading', 'lead', 'breadcrumbs'));
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
            $message = 'No updated topics found. Why not start a new conversation or read more sections?';
            \Session::flash('headerWarning', $message);
            
            // redirect to main menu
            return redirect('/');
        }
    }
}
