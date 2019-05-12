<?php

namespace App\Http\Controllers\Nexus;

use App\Post;
use App\Topic;
use App\Section;
use App\Http\Requests;
use Illuminate\View\View;
use App\Helpers\ViewHelper;
use App\Helpers\FlashHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ActivityHelper;
use App\Http\Requests\StoreTopic;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
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
        $secondsToCache = 60 * 30; // 30 minutes

        return Cache::remember('topicIndex', $secondsToCache, function () {
            return Topic::where('title', '<>', '')->orderBy('title')->get(['id', 'title','intro']);
        });
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
     * @return RedirectResponse
     */
    public function store(StoreTopic $request)
    {
        $section = Section::findOrFail(request('section_id'));
        $this->authorize('create', [Topic::class, $section]);

        $topic = Topic::create([
            'section_id' => request('section_id'),
            'secret'     => request('secret'),
            'readonly'   => request('readonly'),
            'title'      => request('title'),
            'intro'      => request('intro'),
            'weight'     => request('weight')
        ]);
        
        return redirect(action('Nexus\SectionController@show', ['id' => $topic->section_id]));
    }
    
    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int  $topic_id
     * @return View
     */
    public function show(Request $request, $topic_id)
    {
        $posts = Post::with('author')->where('topic_id', $topic_id)->orderBy('id', 'dsc');
        $topic = Topic::findOrFail($topic_id);
        
        // is this topic readonly to the authenticated user?
        $readonly = true;
        
        if ($topic->readonly == false) {
            $readonly = false;
        }
        
        if ($topic->section->moderator->id === $request->user()->id) {
            $readonly = false;
        }

        if ($request->user()->administrator) {
            $readonly = false;
        }

        // is this topic secret to the authenticated user?
        $userCanSeeSecrets = false;

        if ($topic->section->moderator->id === $request->user()->id) {
            $userCanSeeSecrets = true;
        }

        if ($request->user()->administrator) {
            $userCanSeeSecrets = true;
        }

        // get the previously read progress so we can indicate this in the view
        $readProgress =  ViewHelper::getReadProgress($request->user(), $topic);
        
        // get the subscription status
        $topicStatus = ViewHelper::getTopicStatus($request->user(), $topic);
        $unsubscribed = $topicStatus['unsubscribed'];

        ViewHelper::updateReadProgress($request->user(), $topic);

        ActivityHelper::updateActivity(
            $request->user()->id,
            "Reading <em>{$topic->title}</em>",
            action('Nexus\TopicController@show', ['id' => $topic->id])
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
            'topics.index',
            compact(
                'topic',
                'posts',
                'readonly',
                'userCanSeeSecrets',
                'readProgress',
                'breadcrumbs',
                'unsubscribed',
                'replyingTo'
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
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $formName = "topicUpdate$id";

        // create validator here so we can name it based on the topic id
        $validator = Validator::make(
            $request->all(),
            [
                $formName . ".id"          => 'required|numeric',
                $formName . ".id"          => 'exists:topics,id',
                $formName . ".title"       => 'required',
                $formName . ".intro"       => 'required',
                $formName . ".section_id"  => 'required|numeric',
                $formName . ".section_id"  => 'exists:sections,id',
                $formName . ".weight"      => 'required|numeric',
            ],
            [
                $formName . ".title.required" => 'Title is required. Think of this as the subject to be discussed',
                $formName . ".intro.required" => 'Introduction is required. Give a brief introduction to your topic'
            ]
        );
        
        $topicDetails = request($formName);

        if ($validator->fails()) {
            return redirect(action('Nexus\SectionController@show', ['id' => $topicDetails['section_id']]))
            ->withErrors($validator, $formName)
            ->withInput();
        }
        
        $topic = Topic::findOrFail($id);
        
        $section = Section::findOrFail($topicDetails['section_id']);
        
        $this->authorize('update', $topic);
        
        if ($topic->section_id !== (int) $topicDetails['section_id']) {
            // is the user authorized to move the topic to a different section?
            $destinationSection = Section::findOrFail($topicDetails['section_id']);
            $this->authorize('move', [$topic, $destinationSection]);
        }
        
        $topic->update($topicDetails);

        return redirect(action('Nexus\SectionController@show', ['id' => $topic->section_id]));
    }

    /**
     * destroy the topic
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $topic = Topic::findOrFail($id);
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
    public function updateSubscription(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "command" => 'required'
            ]
        );
        
        if ($validator->fails()) {
            return redirect(action('Nexus\TopicController@show', ['id' => $id]));
        }


        $input = $request->all();
        $topic = Topic::findOrFail($id);

        if ($input['command'] === 'subscribe') {
            ViewHelper::subscribeToTopic($request->user(), $topic);
            $message = '**Subscribed!** _Catch-up_ will return you here when new comments are added.';
        } else {
            ViewHelper::unsubscribeFromTopic($request->user(), $topic);
            $message = '**Unsubscribed!** New comments here will be hidden from _Catch-up_.';
        }

        FlashHelper::showAlert($message, 'success');
        return  redirect()->route('topic.show', ['id' => $topic->id]);
    }
    
    /*
        update the latest read time for each subscribed topic
    */
    /**
     * markAllSubscribedTopicsAsRead
     *
     * @param Request $request
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
