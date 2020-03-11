<?php

namespace App\Http\Controllers\Nexus;

use App\User;
use App\Theme;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\FlashHelper;
use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Requests\UpdateUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $users =  User::select('username', 'name', 'popname', 'latestLogin', 'totalPosts', 'totalVisits')
            ->verified()->orderBy('username', 'asc')->get();
        ActivityHelper::updateActivity(
            Auth::user()->id,
            "Viewing list of Users",
            action('Nexus\UserController@index')
        );
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Users');

        return view('users.index', compact('users', 'breadcrumbs'));
    }

    /**
     * show a user
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function show(Request $request, User $user)
    {
        // lazy eager load the users comments
        $user->load('comments', 'comments.author');

        // if the user is looking at their own page then mark comments as read
        if ($user->id === Auth::user()->id) {
            $user->markCommentsAsRead();
            $user->save();
        }

        ActivityHelper::updateActivity(
            Auth::user()->id,
            "Examining <em>{$user->username}</em>",
            action('Nexus\UserController@show', ['user' => $user->username])
        );

        $themes = Theme::all()->pluck('name', 'id');
        $breadcrumbs = BreadcrumbHelper::breadcrumbForUser($user);
        $comments = $user->comments()->paginate(config('nexus.comment_pagination'));

        return view('users.show', compact('user', 'comments', 'breadcrumbs', 'themes'));
    }

    /**
     * edit
     * this just forwards to the $this->show because edit and show are combined
     * @param Request $request
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, User $user)
    {
        return $this->show($request, $user);
    }

    /**
     * Update the user
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $input = $request->all();
        
        // to prevent setting password to an empty string https://trello.com/c/y1WAxwfb
        if ($input['password'] <> '') {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        
        $this->authorize('update', $user);
        $user->update($input);
        
        FlashHelper::showAlert('Profile Updated!', 'success');
        
        return redirect(action('Nexus\UserController@show', ['user' => $user]));
    }
}
