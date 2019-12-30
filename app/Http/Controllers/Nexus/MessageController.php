<?php

namespace App\Http\Controllers\Nexus;

use App\User;
use App\Message;
use App\Http\Requests;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Requests\StoreMessage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }
    
    /**
     * Displays a list of messages sent to the logged in user
     * @todo - generate $activeUsers array from a list of active users
     * @return View
     */
    public function index(Request $request, $selected = null)
    {
        $allMessages = Message::with('user')
            ->with('author')
            ->where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get()
            ->all();
        $messages = array_slice($allMessages, 5);
        $recentMessages = array_reverse(array_slice($allMessages, 0, 5));
        $recentActivities = ActivityHelper::recentActivities();

        $activeUsers = [];
        foreach ($recentActivities as $activity) {
            if ($request->user()->id != $activity['user_id']) {
                $activeUsers[$activity['user_id']] = $activity->user->username;
            }
        }

        // mark all messages as read
        Message::where('user_id', $request->user()->id)->update(['read' => true]);

        ActivityHelper::updateActivity(
            $request->user()->id,
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
     * @param  StoreMessage $request
     * @return RedirectResponse
     */
    public function store(StoreMessage $request)
    {
        $input = $request->all();
        $input['read'] = false;
        $input['user_id'] = $input['user_id'];
        $input['time'] = time();
        $input['author_id'] = $request->user()->id;

        Message::create($input);

        return redirect(action('Nexus\MessageController@index'));
    }
}
