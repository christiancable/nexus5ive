<?php

namespace nexus\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use nexus\Http\Requests;
use nexus\Http\Controllers\Controller;
// use Request;

class CommentController extends Controller
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
     * @param  CreateCommentRequest  $request
     * @return Response
     */
    public function store(Requests\CreateCommentRequest $request)
    {
        $input = $request->all();

        // dd($input);
        // @todo - this is the best way to get the current logged in user?
        $input['from_id'] = \Auth::user()->id;
        
        // if a user is posting on their own profile then assume that they have read the comment
        if ($input['from_id'] == $input['user_id']) {
            $input['readstatus'] = 'y';
        } else {
            $input['readstatus'] = 'n';
        }
   
        \nexus\Nexus\Comment::create($input);

        return redirect("/users/" . $input['redirect_user']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
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
