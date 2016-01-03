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
if (env('NEXUS_ALLOW_REGISTRATIONS') == true) {
    Route::get('auth/register', 'Auth\AuthController@getRegister');
    Route::post('auth/register', 'Auth\AuthController@postRegister');
}

Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// API 
Route::get('api/comments/count', ['middleware' => 'auth',  function() {
    return Auth::user()->newCommentCount();
}]);


// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');



// users
Route::resource('users', 'Nexus\UserController');


// special sections
Route::get('/', 'Nexus\SectionController@show');
Route::get('/home', 'Nexus\SectionController@show');
Route::get('leap', 'Nexus\SectionController@leap');
Route::get('/section/latest', 'Nexus\SectionController@latest');

// sections
Route::resource('section', 'Nexus\SectionController');

// topics
Route::resource('topic', 'Nexus\TopicController');

// comments
Route::post('comments', 'Nexus\CommentController@store');

// posts
Route::post('posts', 'Nexus\PostController@store');

// messages
Route::get('messages/{id}', 'Nexus\MessageController@index');
Route::resource('messages', 'Nexus\MessageController');

// activities 
Route::resource('active', 'Nexus\ActivityController');
/*

Future Routes

GET /who/ - list of users online
GET /inbox/ - a list of messages
*/
