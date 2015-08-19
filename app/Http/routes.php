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


// users

Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');


Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');


Route::get('auth/logout', 'Auth\AuthController@getLogout');


// menus
// 
Route::get('users', function () {
    $users =  \App\Nexus\User::orderBy('user_name', 'asc')->get();
    return view('users.index')->with('users', $users);
});


Route::get('users/{user_name}', function($user_name) {
    $user = \App\Nexus\User::where('user_name', $user_name)->first();
    return view('users.show')->with('user', $user);
});

Route::get('/', function () {
    $section = \App\Nexus\Section::find(1)->first();
    return view('sections.index')->with('section', $section);
});

Route::get('/home', function () {
    $section = \App\Nexus\Section::find(1)->first();
    return view('sections.index')->with('section', $section);
});

Route::get('/{section_id}', function ($section_id) {
    $section = \App\Nexus\Section::where('section_id', $section_id)->first();
    return view('sections.index')->with('section', $section);
});

// Route::get('/{section_id}/{topic_id}', function ($section_id, $topic_id) {
//     $topic = \App\Nexus\Topic::where('topic_id', $topic_id)->where('section_id', $section_id)->first();
//     return view('topics.index')->with('topic', $topic);
// });

Route::get('/{section_id}/{topic_id}', 'Nexus\TopicController@show');

/*

Future Routes

GET / - main menu
GET /{section_id}/ - a section
GET /{section_id}/{topic_id}/ - a topic
GET /who/ - list of users online
GET /users/ - list of users
GET /users/{user_name}/ - info about {user_name}

*/
