<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Helpers\FlashHelper;
use App\Helpers\TopicHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSection;
use App\Http\Requests\UpdateSection;
use App\Section;
use App\User;
use App\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
     * @return RedirectResponse
     */
    public function store(StoreSection $request)
    {
        $parentSection = Section::findOrFail(request('parent_id'));
        $this->authorize('create', [Section::class, $parentSection]);

        $section = Section::create([
            'user_id' => $request->user()->id,
            'parent_id' => request('parent_id'),
            'title' => request('title'),
            'intro' => request('intro'),
        ]);

        $redirect = action('Nexus\SectionController@show', ['section' => $section->id]);

        return redirect($redirect);
    }

    /**
     * Display the index of sections which in this case is our BBS home
     */
    public function index(Request $request)
    {
        $section = Section::firstOrFail();
        return redirect(action('Nexus\SectionController@show', ['section' => $section->id]));
    }
    /**
     * Display the specified resource.
     *
     * @param  Section  $section  optional
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Section $section)
    {
         // lazy eager load relationships
        $section->load(
            'moderator:id,username',
            'sections.moderator:id,username',
            'sections.sections',
            'topics.most_recent_post.author:id,username'
        );

        // load some counts too
        $section->loadCount('sections');

        ActivityHelper::updateActivity(
            $request->user()->id,
            "Browsing <em>{$section->title}</em>",
            action('Nexus\SectionController@show', ['section' => $section->id])
        );

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
        $breadcrumbs = BreadcrumbHelper::breadcrumbForSection($section);

        return view('sections.index', compact('section', 'breadcrumbs', 'potentialModerators', 'moderatedSections'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return RedirectResponse
     */
    public function update(UpdateSection $request, Section $section)
    {
        $formName = "section{$section->id}";
        $updatedSectionDetails = [
            'id' => $section->id,
            'intro' => $request->validated()['form'][$formName]['intro'],
            'parent_id' => $request->validated()['form'][$formName]['parent_id'],
            'title' => $request->validated()['form'][$formName]['title'],
            'user_id' => $request->validated()['form'][$formName]['user_id'],
            'weight' => $request->validated()['form'][$formName]['weight'],
        ];

        // do not set parent for home section
        if ($section->is_home) {
            $updatedSectionDetails['parent_id'] = null;
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
        $lead = 'The freshest posts from across '.config('nexus.name');
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
            return $view->latest_view_date->timestamp != $view->topic->most_recent_post_time->timestamp;
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
