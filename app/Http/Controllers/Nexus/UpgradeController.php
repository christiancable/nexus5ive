<?php

namespace App\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UpgradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        // show all users in usertable
        
        $classicUsers = \DB::table('usertable')->get();
        
        foreach ($classicUsers as $classicUser) {
        	$newUser = new \App\User;
        	
        	$newUser->id = $classicUser->user_id;
			$newUser->username = $classicUser->user_name;
			$newUser->popname = $classicUser->user_popname;
			$newUser->about = $classicUser->user_comment;
			$newUser->location = $classicUser->user_town;
			
			if($classicUser->user_sysop === 'y'){
				$newUser->administrator = true;	
			} else {
				$newUser->administrator = false;
			}
			
			$newUser->totalVisits = $classicUser->user_totalvisits;
			$newUser->totalPosts = $classicUser->user_totaledits;
			$newUser->favouriteMovie = $classicUser->user_film;
			$newUser->favouriteMusic = $classicUser->user_band;
			
			if($classicUser->user_hideemail === 'no'){
				$newUser->private = false;
			} else {
				$newUser->private = true;
			}
			
			$newUser->ipaddress = $classicUser->user_ipaddress;
			$lastLogin = \DB::table('whoison')->select('timeon')->where('user_id', $classicUser->user_id)->first();
			if($lastLogin){
				$newUser->latestLogin = $lastLogin->timeon;
			} 
			
			if($classicUser->user_status === 'Invalid'){
				$newUser->banned = true;
			}
			
			// avoid reusing email addresses
			$emailUses = \DB::table('usertable')->select('user_id')->where('user_email',$classicUser->user_email)->get();
			$count = count($emailUses);
			if($count > 1) {
				$newUser->email = $classicUser->user_name . '@fakeemail.com';
			} else {
				$newUser->email = $classicUser->user_email;
			}
			/* 			
			note sure about these....		
			$table->boolean('banned')->default(false);
		
			*/
			
			if($classicUser->user_realname != "") {
				$newUser->name = $classicUser->user_realname;
			} else {
				$newUser->name = "Unknown";
			}
			
			$newUser->save();
        }
        
        return view('upgrade.index', ['classicUsers' => $classicUsers]);
        /*
        foreach classicUser in usertable 
        	$user = new User;
        	$user.id = classicUser.user_id;
        	get whoison result and populate field
        	etc
        	
        */
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
