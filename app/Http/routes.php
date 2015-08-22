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




// sections
Route::get('/', 'Nexus\SectionController@show');
Route::get('/home', 'Nexus\SectionController@show');
Route::get('/{section_id}', 'Nexus\SectionController@show');

// users
Route::get('users', 'Nexus\UserController@index');
Route::get('users/{user_name}', 'Nexus\UserController@show');

// topics
Route::get('/{section_id}/{topic_id}', 'Nexus\TopicController@show');

// comments
Route::post('comments', 'Nexus\CommentController@store');

/*

Future Routes

GET /who/ - list of users online
GET /inbox/ - a list of messages
*/
