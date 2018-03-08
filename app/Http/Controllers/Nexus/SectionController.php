<?php

namespace App\Http\Controllers\Nexus;

use App\Section;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $parentSection = Section::findOrFail(request('parent_id'));
        $this->authorize('create', [Section::class, $parentSection]);

        $section = Section::create([
            'user_id'   => auth()->id(),
            'parent_id' => request('parent_id'),
            'title'     => request('title'),
            'intro'     => request('intro')
        ]);

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
            $section = \App\Section::with('sections', 'topics')->first();
        } else {
            $section = \App\Section::with('sections', 'topics')->where('id', $section_id)->first();
        }

        \App\Helpers\ActivityHelper::updateActivity(
            \Auth::user()->id,
            "Browsing <em>{$section->title}</em>",
            action('Nexus\SectionController@show', ['id' => $section->id])
        );

        $breadcrumbs = \App\Helpers\BreadcrumbHelper::breadcrumbForSection($section);
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
        $section = Section::findOrFail($id);
        
        $input = $request->all();
        $formName = "section{$id}";
        $updatedSectionDetails = [
            "id" => $id,
            "intro" => $input['form'][$formName]['intro'],
            "parent_id" => $input['form'][$formName]['parent_id'],
            "title" => $input['form'][$formName]['title'],
            "user_id" => $input['form'][$formName]['user_id'],
            "weight" => $input['form'][$formName]['weight']
        ];

        // if parent_id is an empty string then we are updating the root section so set parent to null
        if (strlen($updatedSectionDetails["parent_id"]) === 0) {
            $updatedSectionDetails["parent_id"] = null;
        }

        if ($updatedSectionDetails['parent_id'] == $section->parent_id) {
            $destinationSection = $section->parent;
        } else {
            $destinationSection = Section::findOrFail($updatedSectionDetails['parent_id']);
        }

        // can user update the details?
        $this->authorize('update', $section);

        if ($updatedSectionDetails['parent_id'] != $section->parent_id) {
            // can the user move the section?
            $this->authorize('move', [Section::class, $section, $destinationSection]);
        }
        
        $section->update($updatedSectionDetails);

        return redirect()->route('section.show', ['id' => $section->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  section id  $id
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $section = \App\Section::findOrFail($id);
        $parent_id = $section->parent_id;

        $this->authorize('delete', $section);
        $section->delete();
        $redirect = action('Nexus\SectionController@show', ['id' => $parent_id]);
        return redirect($redirect);
    }

    public function latest()
    {
        $heading = 'Latest Posts';
        $lead = "The most recent posts from across Nexus";
        $topics = \App\Helpers\TopicHelper::recentTopics();
        $breadcrumbs = \App\Helpers\BreadcrumbHelper::breadcumbForUtility($heading);

        return view('topics.unread', compact('topics', 'heading', 'lead', 'breadcrumbs'));
    }

    /**
     * redirects a visitor to a section  with an updated topic
     */
    public function leap()
    {
         // should we be passing the user_id into this method?
        $views = \App\View::with('topic')
            ->where('user_id', \Auth::user()->id)
            ->where('latest_view_date', '!=', "0000-00-00 00:00:00")
            ->where('unsubscribed', 0)->get();
    
        $topics = $views->map(function ($view, $key) {
            if (!is_null($view->topic)) {
                if ($view->latest_view_date != $view->topic->most_recent_post_time) {
                    return $view;
                }
            }
        })->reject(function ($view) {
            return empty($view);
        });

        if ($topics->count()) {
            $destinationTopic = $topics->first()->topic;

            // set alert
            $topicURL = action('Nexus\TopicController@show', ['topic_id' => $destinationTopic->id]);
            // force the url to be relative so we don't later make this open in the new window
            $topicURL = str_replace(url('/'), '', $topicURL);
            $topicTitle = $destinationTopic->title;
            $subscribeAllURL = action('Nexus\TopicController@markAllSubscribedTopicsAsRead');
            $subscribeAllURL = str_replace(url('/'), '', $subscribeAllURL);
            $message = <<< Markdown
People have been talking! New posts found in **[$topicTitle]($topicURL)**

Seeing too many old topics then **[mark all subscribed topics as read]($subscribeAllURL)**
Markdown;
            \App\Helpers\FlashHelper::showAlert($message, 'success');
            
            // redirect to the parent section of the unread topic
            return redirect()->action('Nexus\SectionController@show', [$destinationTopic->section->id]);
        } else {
            // set alert
            $message = 'No updated topics found. Why not start a new conversation or read more sections?';
            \App\Helpers\FlashHelper::showAlert($message, 'warning');
            
            // redirect to main menu
            return redirect('/');
        }
    }
}
