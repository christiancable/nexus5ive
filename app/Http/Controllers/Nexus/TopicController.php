<?php

namespace Nexus\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use Nexus\Http\Requests;
use Nexus\Http\Controllers\Controller;
use Nexus\Topic;

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
    public function store(Requests\Topic\CreateRequest $request)
    {
        $formName = "topicCreate";
        $input = $request->all();
        $input['section_id'] = $input['form'][$formName]['section_id'];
        $input['secret'] = $input['form'][$formName]['secret'];
        $input['readonly'] = $input['form'][$formName]['readonly'];
        $input['title'] = $input['form'][$formName]['title'];
        $input['intro'] = $input['form'][$formName]['intro'];
        $input['weight'] = $input['form'][$formName]['weight'];

        $topic = \Nexus\Topic::create($input);
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

        $posts = \Nexus\Post::with('author')->where('topic_id', $topic_id)->orderBy('id', 'dsc');
        $topic = \Nexus\Topic::findOrFail($topic_id);

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
        $readProgress =  \Nexus\Helpers\ViewHelper::getReadProgress(\Auth::user(), $topic);
        
        // get the subscription status
        $topicStatus = \Nexus\Helpers\ViewHelper::getTopicStatus(\Auth::user(), $topic);
        $unsubscribed = $topicStatus['unsubscribed'];

        \Nexus\Helpers\ViewHelper::updateReadProgress(\Auth::user(), $topic);

        \Nexus\Helpers\ActivityHelper::updateActivity(
            \Auth::user()->id,
            "Reading <em>{$topic->title}</em>",
            action('Nexus\TopicController@show', ['id' => $topic->id])
        );

        $breadcrumbs = \Nexus\Helpers\BreadcrumbHelper::breadcrumbForTopic($topic);
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
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Requests\Topic\UpdateRequest $request, $id)
    {
        $topic = \Nexus\Topic::findOrFail($id);

        $formName = "topic{$id}";

        $input = $request->all();
        $input['section_id'] = $input['form'][$formName]['section_id'];
        $input['secret'] = $input['form'][$formName]['secret'];
        $input['readonly'] = $input['form'][$formName]['readonly'];
        $input['title'] = $input['form'][$formName]['title'];
        $input['intro'] = $input['form'][$formName]['intro'];
        $input['weight'] = $input['form'][$formName]['weight'];

        $topic->update($input);
        return  redirect()->route('section.show', ['id' => $topic->section_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Requests\Topic\DestroyRequest $request, $id)
    {
        $topic = \Nexus\Topic::findOrFail($id);
        $section_id = $topic->section->id;
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
        $topic = \Nexus\Topic::findOrFail($id);

        if ($input['command'] === 'subscribe') {
            \Nexus\Helpers\ViewHelper::subscribeToTopic(\Auth::user(), $topic);
            $message = '**Subscribed!** _Catch-up_ will return you here when new comments are added.';
        } else {
            \Nexus\Helpers\ViewHelper::unsubscribeFromTopic(\Auth::user(), $topic);
            $message = '**Unsubscribed!** New comments here will be hidden from _Catch-up_.';
        }

        $request->session()->flash('headerAlert', $message);
        return  redirect()->route('topic.show', ['id' => $topic->id]);
    }
    
    /*
        update the latest read time for each subscribed topic
    */
    public function markAllSubscribedTopicsAsRead()
    {
        \Nexus\Helpers\ViewHelper::catchUpCatchUp(\Auth::user());
        
        $message = '**Success!** all subscribed topics are now marked as read';
        \Session::flash('headerAlert', $message);
        
        return redirect('/');
    }
}
