<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('users', function () {
    // $users = DB::table('usertable')->where('user_id','=',1)->get();

    $users = \App\NexusUser::where('user_id', '=', 1)->get();
    // $users = DB::table('usertable')->find(1);
    dd($users);
});
