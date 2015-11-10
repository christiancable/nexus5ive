<?php

namespace Nexus\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use Nexus\Http\Requests;
use Nexus\Http\Controllers\Controller;

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
        return view('users.index')->with('users', $users);
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
        $user = \Nexus\User::with('comments', 'comments.author')->where('username', $user_name)->first();
        return view('users.show')->with('user', $user);
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
    public function update(Request $request, $id)
    {
        //
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
