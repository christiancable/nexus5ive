<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Helpers\FlashHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Nexus\UpdateUser;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // this logic instead becomes showing the view for the userlist
        ActivityHelper::updateActivity(
            $request->user()->id,
            'Viewing list of Users',
            action('App\Http\Controllers\Nexus\UserController@index')
        );
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Users');

        return view('nexus.users.index', compact('breadcrumbs'));
    }

    /**
     * show a user
     *
     * @return \Illuminate\View\View
     */
    public function show(Request $request, User $user)
    {
        // lazy eager load the users comments
        $user->load('comments', 'comments.author');

        // if the user is looking at their own page then mark comments as read
        if ($user->id === $request->user()->id) {
            $user->markCommentsAsRead();
            $user->save();
        }

        ActivityHelper::updateActivity(
            $request->user()->id,
            "Examining <em>{$user->username}</em>",
            action('App\Http\Controllers\Nexus\UserController@show', ['user' => $user->username])
        );

        // get default theme and then the others sorted by name
        $defaultTheme = Theme::firstOrFail();
        $otherThemes = Theme::orderBy('name')->where('id', '!=', $defaultTheme->id)->get();

        $themes = collect([$defaultTheme])
            ->concat($otherThemes)
            ->pluck('ucname', 'id');

        $breadcrumbs = BreadcrumbHelper::breadcrumbForUser($user);
        $comments = $user->comments()->paginate(config('nexus.comment_pagination'));

        return view('nexus.users.show', compact('user', 'comments', 'breadcrumbs', 'themes'));
    }

    /**
     * edit
     * this just forwards to the $this->show because edit and show are combined
     *
     * @return \Illuminate\View\View
     */
    public function edit(UpdateUser $request, User $user)
    {
        return $this->show($request, $user);
    }

    /**
     * Update the user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUser $request, User $user)
    {
        $input = $request->all();
        if ($request->user()->cannot('update', $user)) {
            abort(403);
        }

        // to prevent setting password to an empty string https://trello.com/c/y1WAxwfb
        if ($input['password'] != '') {
            // password must match confirm
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $user->update($input);

        FlashHelper::showAlert('Profile Updated!', 'success');

        return redirect(action('App\Http\Controllers\Nexus\UserController@show', ['user' => $user]));
    }
}
