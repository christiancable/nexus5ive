<?php

namespace App\Http\Controllers\Nexus;

use Log;
use Auth;
use Hash;
use App\User;
use Validator;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Helpers\FlashHelper;
use App\Http\Controllers\Controller;
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
        $users =  \App\User::select('username', 'name', 'popname', 'latestLogin')->orderBy('username', 'asc')->get();
        \App\Helpers\ActivityHelper::updateActivity(
            Auth::user()->id,
            "Viewing list of Users",
            action('Nexus\UserController@index')
        );
        $breadcrumbs = \App\Helpers\BreadcrumbHelper::breadcumbForUtility('Users');

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
            $user = \App\User::with('comments', 'comments.author')->where('username', $user_name)->firstOrFail();
        } catch (ModelNotFoundException $ex) {
            $message = "$user_name not found. Maybe you're thinking of someone else";
            FlashHelper::showAlert($message, 'warning');
            return redirect('/users/');
        }

        if ($user->id === Auth::user()->id) {
            Auth::user()->markCommentsAsRead();
            Auth::user()->save();
        }

        \App\Helpers\ActivityHelper::updateActivity(
            Auth::user()->id,
            "Examining <em>{$user->username}</em>",
            action('Nexus\UserController@show', ['user_name' => $user_name])
        );

        $themes = \App\Theme::all()->pluck('name', 'id');
        $breadcrumbs = \App\Helpers\BreadcrumbHelper::breadcrumbForUser($user);
        $comments = $user->comments()->paginate(config('nexus.comment_pagination'));
        return view('users.show', compact('user', 'comments', 'breadcrumbs', 'themes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($user_name)
    {
        $user = \App\User::with('comments', 'comments.author')->where('username', $user_name)->firstOrFail();
        return view('users.show')->with('user', $user);
    }

    /**
     * Update the user
     *
     * @param  String $username
     * @param  Request  $request
     * @return Response
     */
    public function update($user_name, Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id'    => 'required|exists:users,id',
                'email' => 'required|unique:users,email,' . request('id'),
                'password' => 'confirmed',
            ]
        );

        if ($validator->fails()) {
            return redirect(action('Nexus\UserController@show', ['user_name' => $user_name]))
                ->withErrors($validator, 'userUpdate')
                ->withInput();
        }

        
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
