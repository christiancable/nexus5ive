<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Helpers\FlashHelper;
use App\Helpers\ViewHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Nexus\StoreTopic;
use App\Http\Requests\Nexus\SubscribeTopic;
use App\Http\Requests\Nexus\UpdateTopic;
use App\Models\Section;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopicController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(StoreTopic $request)
    {
        $section = Section::findOrFail($request->validated()['section_id']);
        if ($request->user()->cannot('create', $section)) {
            abort(403);
        }
        $topic = Topic::create($request->validated());

        return redirect(action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $topic->section_id]));
    }

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function show(Request $request, Topic $topic)
    {
        $posts = $topic->reversedPosts()->with('author');

        $readonly = ! ($request->user()->can('reply', $topic));

        // is this topic anonymous to the logged in user?
        $anonymous = ! ($request->user()->can('viewDetails', $topic));

        // get the previously read progress so we can indicate this in the view
        $readProgress = ViewHelper::getReadProgress($request->user(), $topic);

        // get the subscription status
        $topicStatus = ViewHelper::getTopicStatus($request->user(), $topic);
        $unsubscribed = $topicStatus['unsubscribed'];

        ViewHelper::updateReadProgress($request->user(), $topic);

        ActivityHelper::updateActivity(
            $request->user()->id,
            "Reading <em>{$topic->title}</em>",
            action('App\Http\Controllers\Nexus\TopicController@show', ['topic' => $topic->id])
        );

        // if replying then include a copy of what we are replying to
        $replyingTo = null;
        if ($request->reply && $topic->most_recent_post) {
            $replyingTo['text'] = $topic->most_recent_post->text;
            if ($topic->secret) {
                $replyingTo['username'] = null;
            } else {
                $replyingTo['username'] = $topic->most_recent_post->author->username;
            }
        }

        $breadcrumbs = BreadcrumbHelper::breadcrumbForTopic($topic);

        return view(
            'nexus.topics.index',
            compact(
                'topic',
                'posts',
                'readonly',
                'readProgress',
                'breadcrumbs',
                'unsubscribed',
                'replyingTo',
                'anonymous',
            )
        );
    }

    /**
     * Update the topic
     *
     * @return RedirectResponse
     */
    public function update(UpdateTopic $request, Topic $topic)
    {
        $formName = "topicUpdate{$topic->id}";
        $topicDetails = $request->validated()[$formName];
        if ($request->user()->cannot('update', $topic)) {
            abort(403);
        }

        // is the user authorized to move the topic to the destination section?
        if ($topic->section_id !== (int) $topicDetails['section_id']) {
            $destinationSection = Section::findOrFail($topicDetails['section_id']);
            if ($request->user()->cannot('move', [$topic, $destinationSection])) {
                abort(403);
            }
        }

        $topic->update($topicDetails);

        return redirect(action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $topic->section_id]));
    }

    /**
     * destroy the topic
     *
     * @return RedirectResponse
     */
    public function destroy(Request $request, Topic $topic)
    {
        $section_id = $topic->section->id;

        if ($request->user()->cannot('delete', $topic)) {
            abort(403);
        }
        $topic->delete();

        $redirect = action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $section_id]);

        return redirect($redirect);
    }

    /**
     * updateSubscription
     * toggles a users subscription to the topic
     *
     * @return RedirectResponse
     */
    public function updateSubscription(SubscribeTopic $request, Topic $topic)
    {
        $input = $request->validated();
        if ($input['command'] === 'subscribe') {
            ViewHelper::subscribeToTopic($request->user(), $topic);
            $message = '**Subscribed!** _Catch-up_ will return you here when new comments are added.';
        } else {
            ViewHelper::unsubscribeFromTopic($request->user(), $topic);
            $message = '**Unsubscribed!** New comments here will be hidden from _Catch-up_.';
        }

        FlashHelper::showAlert($message, 'success');

        return redirect()->route('topic.show', ['topic' => $topic->id]);
    }

    /**
     * markAllSubscribedTopicsAsRead
     *
     * update the latest read time for each subscribed topic
     *
     * @return RedirectResponse
     */
    public function markAllSubscribedTopicsAsRead(Request $request)
    {
        ViewHelper::catchUpCatchUp($request->user());

        $message = '**Success!** all subscribed topics are now marked as read';
        FlashHelper::showAlert($message, 'success');

        return redirect('/');
    }
}
