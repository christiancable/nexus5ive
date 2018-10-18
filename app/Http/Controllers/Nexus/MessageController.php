<?php

namespace App\Http\Controllers\Nexus;

use Auth;
use Validator;
use App\Message;
use App\Http\Requests;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Displays a list of messages sent to the logged in user
     * @todo - generate $activeUsers array from a list of active users
     * @return View
     */
    public function index($selected = null)
    {
        $allMessages = Message::with('user')
            ->with('author')
            ->where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get()
            ->all();
        $messages = array_slice($allMessages, 5);
        $recentMessages = array_reverse(array_slice($allMessages, 0, 5));
        $recentActivities = ActivityHelper::recentActivities();

        $activeUsers = [];
        foreach ($recentActivities as $activity) {
            if (Auth::user()->id != $activity['user_id']) {
                $activeUsers[$activity['user_id']] = $activity->user->username;
            }
        }

        // mark all messages as read
        Message::where('user_id', Auth::user()->id)->update(['read' => true]);

        ActivityHelper::updateActivity(
            Auth::user()->id,
            "Viewing <em>Inbox</em>",
            action('Nexus\MessageController@index')
        );

        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Inbox');

        return view('messages.index')
            ->with(compact('messages', 'recentMessages', 'activeUsers', 'selected', 'breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'text' => 'required',
                'user_id' => 'required|numeric|exists:users,id',
                'author_id' => 'required|numeric|exists:users,id'
            ],
            [
                "text.required" => 'Sending empty messages is a little creepy!'
            ]
        );

        if ($validator->fails()) {
            return redirect(action('Nexus\MessageController@index'))
                ->withErrors($validator, 'messageStore')
                ->withInput();
        }

        $input = $request->all();
        $input['read'] = false;
        $input['user_id'] = $input['user_id'];
        $input['time'] = time();
        $input['author_id'] = Auth::user()->id;

        Message::create($input);

        return redirect(action('Nexus\MessageController@index'));
    }
}
