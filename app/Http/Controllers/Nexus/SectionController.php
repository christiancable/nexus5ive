<?php

namespace App\Http\Controllers\Nexus;

use App\User;
use App\View;
use App\Section;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\FlashHelper;
use App\Helpers\TopicHelper;
use App\Helpers\ActivityHelper;
use Illuminate\Validation\Rule;
use App\Helpers\BreadcrumbHelper;
use App\Http\Requests\StoreSection;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
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
     * @param  \App\Http\Requests\StoreSection  $request
     * @return RedirectResponse
     */
    public function store(StoreSection $request)
    {
        $parentSection = Section::findOrFail(request('parent_id'));
        $this->authorize('create', [Section::class, $parentSection]);
        
        $section = Section::create([
            'user_id'   => $request->user()->id,
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
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $section_id = null)
    {
        if (!$section_id) {
            $section = Section::with([
                'moderator:id,username',
                'sections.moderator:id,username',
                'sections.sections',
                'topics.most_recent_post.author:id,username',
                'sections.topics.posts:id,time'
            ])->firstOrFail();
        } else {
            $section = Section::with([
                'moderator:id,username',
                'sections.moderator:id,username',
                'sections.sections',
                'topics.most_recent_post.author:id,username',
                'sections.topics.posts:id,time'
            ])->findOrFail($section_id);
        }

        ActivityHelper::updateActivity(
            $request->user()->id,
            "Browsing <em>{$section->title}</em>",
            action('Nexus\SectionController@show', ['id' => $section->id])
        );
        
        // if the user can moderate the section then they could potentially update subsections
        if ($section->moderator->id === $request->user()->id) {
            $potentialModerators = User::all(['id','username'])->pluck('username', 'id')->toArray();
            $moderatedSections = $request->user()
                ->sections()
                ->select('title', 'id')
                ->get()
                ->pluck('title', 'id')
                ->toArray();
        } else {
            $potentialModerators = [];
            $moderatedSections = [];
        }
        $breadcrumbs = BreadcrumbHelper::breadcrumbForSection($section);

        return view('sections.index', compact('section', 'breadcrumbs', 'potentialModerators', 'moderatedSections'));
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
        $section = Section::findOrFail($id);
        $formName = "section{$id}";

        // it not valid to move a section into a descendant
        $descendants = $section->allChildSections();
        $descendantsIDs = array_flatten($descendants->pluck('id')->toArray());
        
        // if parents exists then it must be a valid section id
        $allSectionIDs = Section::all('id')->pluck('id')->toArray();
        
        // updating the home section has different validation rules to other sections

        $messages = [
            "form.{$formName}.title.required" => 'Section Title is required'
        ];
        
        $rules = [
            "form.{$formName}.title" => 'required',
            "form.{$formName}.user_id" => 'required|numeric',
            "form.{$formName}.title" => 'required'
        ];

        if (!$section->is_home) {
            $rules["form.{$formName}.parent_id"] = [
                    'required',
                    'numeric',
                    Rule::notIn($descendantsIDs),
                    Rule::notIn([$id]),
                    Rule::In($allSectionIDs)
            ];
        }
        
        // manually create validator to dynamically name the errorBag
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, "sectionUpdate$id")
                ->withInput();
        }
        
        $input = $request->all();
        $updatedSectionDetails = [
            "id" => $id,
            "intro" => $input['form'][$formName]['intro'],
            "parent_id" => $input['form'][$formName]['parent_id'],
            "title" => $input['form'][$formName]['title'],
            "user_id" => $input['form'][$formName]['user_id'],
            "weight" => $input['form'][$formName]['weight']
        ];
        
        // do not set parent for home section
        if ($section->is_home) {
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
     * @param  int $id
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $section = Section::findOrFail($id);
        $parent_id = $section->parent_id;

        $this->authorize('delete', $section);

        // the deleting user takes the section into their archive
        $section->user_id = $request->user()->id;
        $section->save();

        $section->delete();
        $redirect = action('Nexus\SectionController@show', ['id' => $parent_id]);
        return redirect($redirect);
    }


    /**
     * Shows the Latest Posts screen
     *
     * @return \Illuminate\View\View
     */
    public function latest()
    {
        $heading = 'Latest Posts';
        $lead = "The most recent posts from across " . config('nexus.name');
        $topics = TopicHelper::recentTopics();
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility($heading);

        return view('topics.unread', compact('topics', 'heading', 'lead', 'breadcrumbs'));
    }

    /**
     * redirects a visitor to a section  with an updated topic
     *
     * @return RedirectResponse
     */
    public function leap(Request $request)
    {
         // should we be passing the user_id into this method?
        $views = View::with('topic')
            ->where('user_id', $request->user()->id)
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
            FlashHelper::showAlert($message, 'success');
            
            // redirect to the parent section of the unread topic
            return redirect()->action('Nexus\SectionController@show', [$destinationTopic->section->id]);
        } else {
            // set alert
            $message = 'No updated topics found. Why not start a new conversation or read more sections?';
            FlashHelper::showAlert($message, 'warning');
            
            // redirect to main menu
            return redirect('/');
        }
    }
}
