<?php

namespace App\Http\Controllers\Nexus;

use App\Topic;
use App\Section;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
     * @param  Requests\Topic\TopicRequest  $request
     * @return Response
     */
    public function store(Requests\Topic\TopicRequest $request)
    {
        $formName = "topicCreate";
        $input = $request->all();
        $input['section_id'] = $input['form'][$formName]['section_id'];
        $input['secret'] = $input['form'][$formName]['secret'];
        $input['readonly'] = $input['form'][$formName]['readonly'];
        $input['title'] = $input['form'][$formName]['title'];
        $input['intro'] = $input['form'][$formName]['intro'];
        $input['weight'] = $input['form'][$formName]['weight'];
        $section = Section::findOrFail($input['section_id']);

        $this->authorize('create', [Topic::class, $section]);
        $topic = \App\Topic::create($input);
        $redirect = action('Nexus\SectionController@show', ['id' => $topic->section_id]);
        
        return redirect($redirect);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($topic_id)
    {

        $posts = \App\Post::with('author')->where('topic_id', $topic_id)->orderBy('id', 'dsc');
        $topic = \App\Topic::findOrFail($topic_id);

        // is this topic readonly to the authenticated user?
        $readonly = true;

        if ($topic->readonly == false) {
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

        // get the previously read progress so we can indicate this in the view
        $readProgress =  \App\Helpers\ViewHelper::getReadProgress(\Auth::user(), $topic);
        
        // get the subscription status
        $topicStatus = \App\Helpers\ViewHelper::getTopicStatus(\Auth::user(), $topic);
        $unsubscribed = $topicStatus['unsubscribed'];

        \App\Helpers\ViewHelper::updateReadProgress(\Auth::user(), $topic);

        \App\Helpers\ActivityHelper::updateActivity(
            \Auth::user()->id,
            "Reading <em>{$topic->title}</em>",
            action('Nexus\TopicController@show', ['id' => $topic->id])
        );

        $breadcrumbs = \App\Helpers\BreadcrumbHelper::breadcrumbForTopic($topic);
        return view(
            'topics.index',
            compact(
                'topic',
                'posts',
                'readonly',
                'userCanSeeSecrets',
                'readProgress',
                'breadcrumbs',
                'unsubscribed'
            )
        );
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
     * Update the topic
     *
     * @param Requests\Topic\TopicRequest $request
     * @param [type] $id
     * @return void
     */
    public function update(Requests\Topic\TopicRequest $request, $id)
    {
        $topic = Topic::findOrFail($id);

        $formName = "topic{$id}";

        $input = $request->all();
        $input['section_id'] = $input['form'][$formName]['section_id'];
        $input['secret'] = $input['form'][$formName]['secret'];
        $input['readonly'] = $input['form'][$formName]['readonly'];
        $input['title'] = $input['form'][$formName]['title'];
        $input['intro'] = $input['form'][$formName]['intro'];
        $input['weight'] = $input['form'][$formName]['weight'];

        // can we update this section?
        $this->authorize('update', $topic);

        if ($topic->section_id !== (int) $input['section_id']) {
            // is the user authorized to move the topic to a different section?
            $destinationSection = Section::findOrFail($input['section_id']);
            $this->authorize('move', [$topic, $destinationSection]);
        }

        $topic->update($input);
        return  redirect()->route('section.show', ['id' => $topic->section_id]);
    }

    /**
     * destroy the topic
     *
     * @param Request $request
     * @param int $id
     * @return response
     */
    public function destroy(Request $request, $id)
    {
        $topic = \App\Topic::findOrFail($id);
        $section_id = $topic->section->id;

        $this->authorize('delete', $topic);
        $topic->delete();

        $redirect = action('Nexus\SectionController@show', ['id' => $section_id]);
        return redirect($redirect);
    }

    /**
     *
     * toggles a users subscription to the topic
     */
    public function updateSubscription(Requests\Topic\SubscriptionRequest $request, $id)
    {
        $input = $request->all();
        $topic = \App\Topic::findOrFail($id);

        if ($input['command'] === 'subscribe') {
            \App\Helpers\ViewHelper::subscribeToTopic(\Auth::user(), $topic);
            $message = '**Subscribed!** _Catch-up_ will return you here when new comments are added.';
        } else {
            \App\Helpers\ViewHelper::unsubscribeFromTopic(\Auth::user(), $topic);
            $message = '**Unsubscribed!** New comments here will be hidden from _Catch-up_.';
        }

        \App\Helpers\FlashHelper::showAlert($message, 'success');
        return  redirect()->route('topic.show', ['id' => $topic->id]);
    }
    
    /*
        update the latest read time for each subscribed topic
    */
    public function markAllSubscribedTopicsAsRead()
    {
        \App\Helpers\ViewHelper::catchUpCatchUp(\Auth::user());
        
        $message = '**Success!** all subscribed topics are now marked as read';
        \App\Helpers\FlashHelper::showAlert($message, 'success');
        
        return redirect('/');
    }
}
