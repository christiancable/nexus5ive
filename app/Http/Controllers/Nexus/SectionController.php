<?php

namespace App\Http\Controllers\Nexus;

use App\User;
use App\View;
use App\Section;
use App\Http\Requests;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\FlashHelper;
use App\Helpers\TopicHelper;
use App\Helpers\ActivityHelper;
use Illuminate\Validation\Rule;
use App\Helpers\BreadcrumbHelper;
use App\Http\Requests\StoreSection;
use App\Http\Requests\UpdateSection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Log;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
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

        $redirect = action('Nexus\SectionController@show', ['section' => $section->id]);
        return redirect($redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Section $section optional
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Section $section = null)
    {
        Log::debug("Returning " . $section->id . '--' . $section->title);
        if (null == $section) {
            $section = Section::firstOrFail();
        }

        // lazy eager load relationships
        $section->load(
            'moderator:id,username',
            'sections.moderator:id,username',
            'sections.sections',
            'topics.most_recent_post.author:id,username'
        );

        // load some counts too
        $section->loadCount('sections');

        Log::debug('about to update activity');
        ActivityHelper::updateActivity(
            $request->user()->id,
            "Browsing <em>{$section->title}</em>",
            action('Nexus\SectionController@show', ['section' => $section->id])
        );
        Log::debug('can user moderate subsections');
        // if the user can moderate the section then they could potentially update subsections
        if ($section->moderator->id === $request->user()->id) {
            $potentialModerators = User::all(['id', 'username'])->pluck('username', 'id')->toArray();
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
        Log::debug('get breadcrumbs');
        $breadcrumbs = BreadcrumbHelper::breadcrumbForSection($section);



        Log::debug('data for the view');
        Log::debug('breadcrumbs' . print_r($breadcrumbs, true));
        Log::debug('potentialModerators' . print_r($potentialModerators, true));
        Log::debug('moderatedSections' . print_r($moderatedSections, true));
        Log::debug('about to return the view');
        return view('sections.index', compact('section', 'breadcrumbs', 'potentialModerators', 'moderatedSections'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSection $request
     * @param Section $section
     * @return RedirectResponse
     */
    public function update(UpdateSection $request, Section $section)
    {
        $formName = "section{$section->id}";
        $updatedSectionDetails = [
            "id"        => $section->id,
            "intro"     => $request->validated()['form'][$formName]['intro'],
            "parent_id" => $request->validated()['form'][$formName]['parent_id'],
            "title"     => $request->validated()['form'][$formName]['title'],
            "user_id"   => $request->validated()['form'][$formName]['user_id'],
            "weight"    => $request->validated()['form'][$formName]['weight']
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

        return redirect()->route('section.show', ['section' => $section->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Section $section
     * @return RedirectResponse
     */
    public function destroy(Request $request, Section $section)
    {
        $parent_id = $section->parent_id;

        $this->authorize('delete', $section);

        // the deleting user takes the section into their archive
        $section->user_id = $request->user()->id;
        $section->save();

        $section->delete();
        $redirect = action('Nexus\SectionController@show', ['section' => $parent_id]);
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
        $icon = 'pulse';
        $lead = "The freshest posts from across " . config('nexus.name');
        $topics = TopicHelper::recentTopics();
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility($heading);

        return view('topics.unread', compact('topics', 'heading', 'lead', 'icon', 'breadcrumbs'));
    }

    /**
     * redirects a visitor to a section with an updated topic
     *
     * @return RedirectResponse
     */
    public function leap(Request $request)
    {
        $views = View::subscribed()
            ->with('topic')
            ->where('user_id', $request->user()->id)
            ->get();

        $updatedView = $views->first(function ($view) {
            return ($view->latest_view_date->timestamp != $view->topic->most_recent_post_time->timestamp);
        });

        if ($updatedView != null) {
            $destinationTopic = $updatedView->topic;

            // set alert
            $topicURL = action('Nexus\TopicController@show', ['topic' => $destinationTopic->id]);
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
            return redirect()->action(
                'Nexus\SectionController@show',
                ['section' => $destinationTopic->section->id]
            );
        } else {
            // set alert
            $message = 'No updated topics found. Why not start a new conversation or read more sections?';
            FlashHelper::showAlert($message, 'warning');

            // redirect to main menu
            return redirect('/');
        }
    }
}
