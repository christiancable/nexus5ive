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
    return view('sections.index')->with('sections', $sections);
});

Route::get('/{section_title}', function ($section_title) {

    // $currentSection = \App\Nexus\Section::where('section_title', '=', $section_title)->take(1)->get();
    

    $section = \App\Nexus\Section::where('section_title', '=', $section_title)->take(1)->get();
    // dd($section);
    
    $childSections = $section->children;

    dd($childSections);

    dd($currentSection->moderator->user_name);
    $parentSeection = $currentSection->children()->get();

    dd($parentSeection);
    // $sections = $currentSection->children;

    // dd($currentSection);
    // $sections = \App\Nexus\Section::where('parent', '=', $currentSection)->orderBy('section_weight', 'asc')->get();
    
    // $sections = \App\Nexus\Section::where()
    dd($sections);
    // return view('sections.index')->with('sections', $sections);
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
