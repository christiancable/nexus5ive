<?php

namespace App\Http\Controllers\Nexus;

use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\FlashHelper;
use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Requests\UpdateUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
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
                $request->user()->id,
                "Viewing list of Users",
                action('Nexus\UserController@index')
            );
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Users');

        return view('users.index', compact('users', 'breadcrumbs'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $user_name - the name of the user
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $user_name)
    {

        try {
            $user = User::with('comments', 'comments.author')->where('username', $user_name)->firstOrFail();
        } catch (ModelNotFoundException $ex) {
            $message = "$user_name not found. Maybe you're thinking of someone else";
            FlashHelper::showAlert($message, 'warning');
            return redirect('/users/');
        }

        if ($user->id === $request->user()->id) {
            $request->user()->markCommentsAsRead();
            $request->user()->save();
        }

            ActivityHelper::updateActivity(
                $request->user()->id,
                "Examining <em>{$user->username}</em>",
                action('Nexus\UserController@show', ['user_name' => $user_name])
            );

        $themes = \App\Theme::all()->pluck('name', 'id');
        $breadcrumbs = BreadcrumbHelper::breadcrumbForUser($user);
        $comments = $user->comments()->paginate(config('nexus.comment_pagination'));
        return view('users.show', compact('user', 'comments', 'breadcrumbs', 'themes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  String  $user_name
     * @return \Illuminate\View\View
     */
    public function edit($user_name)
    {
        $user = User::with('comments', 'comments.author')->where('username', $user_name)->firstOrFail();
        return view('users.show')->with('user', $user);
    }

    /**
     * Update the user
     *
     * @param  String $user_name
     * @param  UpdateUser  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($user_name, UpdateUser $request)
    {
        $input = $request->all();
        
        // to prevent setting password to an empty string https://trello.com/c/y1WAxwfb
        if ($input['password'] <> '') {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        
        $user = User::findOrFail(request('id'));
        $this->authorize('update', $user);
        $user->update($input);
        
        FlashHelper::showAlert('Profile Updated!', 'success');
        return redirect(action('Nexus\UserController@show', ['user_name' => $user_name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
