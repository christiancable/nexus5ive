<?php

use Illuminate\Http\Request;

use App\Http\Controllers\Nexus\ActivityController;
use App\Http\Controllers\Nexus\ChatApiController;
use App\Http\Controllers\Nexus\MentionController;
use App\Http\Controllers\Nexus\CommentController;
use App\Http\Controllers\Nexus\RestoreController;
use App\Http\Controllers\Nexus\SectionController;
use App\Http\Controllers\Nexus\NotificationsController;
use App\Http\Controllers\Nexus\SearchController;
use App\Http\Controllers\Nexus\TopicController;
use App\Http\Controllers\Nexus\ModeController;
use App\Http\Controllers\Nexus\PostController;
use App\Http\Controllers\Nexus\ChatController;
use App\Http\Controllers\Nexus\TreeController;
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


// Notifications
Route::get('api/notificationsCount', [NotificationsController::class, 'notificationCount'])->middleware('auth')->name('api.notificationCount');
Route::get('interface/toolbar', [NotificationsController::class, 'toolbar'])->middleware('auth')->name('interface.toolbar');



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
Route::delete('posts/{post}', [PostController::class, 'destroy']);
Route::resource('posts', PostController::class);

// messages
// Route::get('messages/{id}', 'Nexus\MessageController@index');
// Route::resource('messages', 'Nexus\MessageController');

// conversations
Route::get('chat/{username}', [ChatController::class, 'conversation']);
Route::post('chat/{username}', [ChatController::class, 'store']);
Route::resource('chat', ChatController::class);

// chat refactor for vue
Route::get('chats/{username}', [ChatApiController::class, 'show']);
Route::get('chats', [ChatApiController::class, 'index']);
Route::get('chatsusers', [ChatApiController::class, 'chatPartnerIndex']);

// activities
Route::resource('here', ActivityController::class);

// search
Route::get('search', [SearchController::class, 'index']);
Route::get('search/{text}', [SearchController::class, 'find']);
Route::post('search', [SearchController::class, 'submitSearch']);

// restore
Route::resource('archive', RestoreController::class);
Route::post('archive/section/{section}', [RestoreController::class, 'section'])
    ->name('archive.section');
Route::post('archive/topic/{topic}', [RestoreController::class, 'topic'])
    ->name('archive.topic');

// admin
Route::resource('admin', ModeController::class);
Route::post('admin', [ModeController::class, 'handle'])
    ->name('mode.handle');

// utilities
Route::get('updateSubscriptions', [TopicController::class, 'markAllSubscribedTopicsAsRead']);
Route::get('jump', [TreeController::class, 'show']);

// @mentions
Route::delete('mentions', [MentionController::class, 'destroyAll']);
