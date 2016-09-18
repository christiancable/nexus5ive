<?php

namespace Nexus\Http\Controllers\Nexus;

use Illuminate\Http\Request;
use Log;
use Nexus\Http\Requests;
use Nexus\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
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
        $users =  \Nexus\User::select('username')->orderBy('username', 'asc')->get();

        \Nexus\Helpers\ActivityHelper::updateActivity(
            \Auth::user()->id,
            "Viewing list of Users",
            action('Nexus\UserController@index')
        );
        $breadcrumbs = \Nexus\Helpers\BreadcrumbHelper::breadcumbForUtility('Users');

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
     * @return Response
     */
    public function show($user_name)
    {

        try {
            $user = \Nexus\User::with('comments', 'comments.author')->where('username', $user_name)->firstOrFail();
        } catch (ModelNotFoundException $ex) {
            $message = "$user_name not found. Maybe you're thinking of someone else";
            \Nexus\Helpers\FlashHelper::showAlert($message, 'warning');
            return redirect('/users/');
        }

        if ($user->id === \Auth::user()->id) {
            \Auth::user()->markCommentsAsRead();
            \Auth::user()->save();
        }

        \Nexus\Helpers\ActivityHelper::updateActivity(
            \Auth::user()->id,
            "Examining <em>{$user->username}</em>",
            action('Nexus\UserController@show', ['user_name' => $user_name])
        );

        $breadcrumbs = \Nexus\Helpers\BreadcrumbHelper::breadcrumbForUser($user);

        return view('users.show', compact('user', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($user_name)
    {
        $user = \Nexus\User::with('comments', 'comments.author')->where('username', $user_name)->firstOrFail();
        return view('users.show')->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($user_name, Requests\User\UpdateRequest $request)
    {
        $user = \Nexus\User::where('username', $user_name)->firstOrFail();
        $input = $request->all();
        if ($input['password'] <> '') {
            // to prevent setting password to an empty string https://trello.com/c/y1WAxwfb
            $input['password'] = \Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        $user->update($input);
        
        \Nexus\Helpers\FlashHelper::showAlert('Profile Updated!', 'success');
        return redirect('/users/'. $user_name);
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
