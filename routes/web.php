<?php

use App\Http\Controllers\Nexus\CommentController;
use Illuminate\Http\Request;

use App\Http\Controllers\Nexus\SectionController;
use App\Http\Controllers\Nexus\TopicController;
use App\Http\Controllers\Nexus\UserController;

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
if (config('nexus.allow_registrations') === true) {
    Auth::routes(['verify' => true]);
} else {
    Auth::routes(
        ['verify' => true],
        ['register' => false]
    );

    // so redirect the register route
    Route::redirect('register', 'login');
}

// API
Route::get('api/notificationsCount', ['middleware' => 'auth',  function () {
    return Auth::user()->notificationCount();
}])->name('api.notificationCount');


Route::post('api/users', function (Request $request) {
    $input = $request->all();
    $username = $input['query'];
    $data = \App\User::select('username')->where('username', "LIKE", "%$username%")->orderBy('username', 'asc')->get()->toArray();
    return response()->json($data);
})->name('api.users');


// Interface partials
Route::get('interface/toolbar', ['middleware' => 'auth',  function () {
    return response()->view('_toolbar');
}])->name('interface.toolbar');


// uers
Route::resource('users', UserController::class);


// special sections
Route::get('/', [SectionController::class, 'show']);
Route::get('/home', [SectionController::class, 'show']);
Route::get('leap', [SectionController::class, 'leap']);
Route::get('/section/latest', [SectionController::class, 'latest']);

// sections
Route::resource('section', SectionController::class);

// topics
Route::delete('topic/{topic}', [TopicController::class, 'destroy']);
Route::post('/topic/{topic}/subscribe', [TopicController::class, 'updateSubscription'])
    ->name('topic.updateSubscription');
Route::resource('topic', TopicController::class);

// comments
Route::delete('comments', [CommentController::class, 'destroyAll']);
Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
Route::resource('comments', CommentController::class);

// posts
Route::delete('posts/{post}', 'Nexus\PostController@destroy');
Route::resource('posts', 'Nexus\PostController');

// messages
// Route::get('messages/{id}', 'Nexus\MessageController@index');
// Route::resource('messages', 'Nexus\MessageController');

// conversations
Route::get('chat/{username}', 'Nexus\ChatController@conversation');
Route::post('chat/{username}', 'Nexus\ChatController@store');
Route::resource('chat', 'Nexus\ChatController');

// chat refactor for vue
Route::get('chats/{username}', 'Nexus\ChatApiController@show');
Route::get('chats', 'Nexus\ChatApiController@index');
Route::get('chatsusers', 'Nexus\ChatApiController@chatPartnerIndex');

// activities
Route::resource('here', 'Nexus\ActivityController');

// search
Route::get('search', 'Nexus\SearchController@index');
Route::get('search/{text}', 'Nexus\SearchController@find');
Route::post('search', 'Nexus\SearchController@submitSearch');

// restore
Route::resource('archive', 'Nexus\RestoreController');
Route::post('archive/section/{section}', 'Nexus\RestoreController@section')
    ->name('archive.section');
Route::post('archive/topic/{topic}', 'Nexus\RestoreController@topic')
    ->name('archive.topic');

// admin
Route::resource('admin', 'Nexus\ModeController');
Route::post('admin', 'Nexus\ModeController@handle')
    ->name('mode.handle');

// utilities
Route::get('updateSubscriptions', 'Nexus\TopicController@markAllSubscribedTopicsAsRead');
Route::get('jump', 'Nexus\TreeController@show');

// @mentions
Route::delete('mentions', 'Nexus\MentionController@destroyAll');
