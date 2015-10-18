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

// authentication
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

/* Route::post('password/email', function() {
	dd("hello");
}); */


// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');



// users
Route::get('users', 'Nexus\UserController@index');
Route::get('users/{user_name}', 'Nexus\UserController@show');

// DEBUG
Route::get('/section/unread', 'Nexus\SectionController@unread');
Route::get('leap', 'Nexus\SectionController@leap');

// sections
Route::get('/', 'Nexus\SectionController@show');
Route::get('/home', 'Nexus\SectionController@show');
Route::get('/section/{section_id}', 'Nexus\SectionController@show');


// topics
Route::get('/topic/{topic_id}', 'Nexus\TopicController@show');

// comments
Route::post('comments', 'Nexus\CommentController@store');


// posts
Route::post('posts', 'Nexus\PostController@store');

// upgrade users from old nexus
Route::get('upgrade', 'Nexus\UpgradeController@index');

/*

Future Routes

GET /who/ - list of users online
GET /inbox/ - a list of messages
*/
