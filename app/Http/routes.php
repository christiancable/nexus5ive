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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    $sections = \App\Nexus\Section::where('parent_id', '=', 1)->orderBy('section_weight', 'asc')->get();
    // $sections = \App\Nexus\Section::find(1)->children()->get();
    return view('sections.index')->with('sections', $sections);
});

Route::get('/{section_title}', function ($section_title) {
    
    $result = \App\Nexus\Section::find(1)->take(1)->moderator()->get();     

    dd($result);
    
});

Route::get('users', function () {
    $users =  \App\Nexus\User::orderBy('user_name', 'asc')->get();
    return view('users.index')->with('users', $users);
});


Route::get('users/{user_name}', function($user_name) {
    $user = \App\Nexus\User::where('user_name', $user_name)->first();
    return view('users.show')->with('user', $user);
});

/*

Future Routes

GET / - main menu
GET /{section_name}/ - a section
GET /{section_name}/{topic_name}/ - a topic
GET /who/ - list of users online
GET /users/ - list of users
GET /users/{user_name}/ - info about {user_name}

*/
