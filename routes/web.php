<?php

use App\Http\Controllers\Nexus\ActivityController;
use App\Http\Controllers\Nexus\ChatController;
use App\Http\Controllers\Nexus\CommentController;
use App\Http\Controllers\Nexus\ModeController;
use App\Http\Controllers\Nexus\PostController;
use App\Http\Controllers\Nexus\RestoreController;
use App\Http\Controllers\Nexus\SearchController;
use App\Http\Controllers\Nexus\SectionController;
use App\Http\Controllers\Nexus\TopicController;
use App\Http\Controllers\Nexus\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';

// above default laravel stuff

Route::middleware(['auth', 'verified'])->group(function () {
    /* Admin */
    Route::middleware([EnsureUserIsAdmin::class])->group(function () {
        Route::resource('admin', ModeController::class);
        Route::post('admin', [ModeController::class, 'handle'])
            ->name('mode.handle');
    });

    /* Sections */
    Route::get('/', [SectionController::class, 'index']);
    Route::get('/home', [SectionController::class, 'index']);
    Route::get('leap', [SectionController::class, 'leap']);
    Route::get('/section/latest', [SectionController::class, 'latest'])->name('latest');
    Route::resource('section', SectionController::class);

    /* Topics */
    Route::delete('topic/{topic}', [TopicController::class, 'destroy']);
    Route::post('/topic/{topic}/subscribe', [TopicController::class, 'updateSubscription'])
        ->name('topic.updateSubscription');
    Route::resource('topic', TopicController::class);

    /* Users */
    Route::resource('users', UserController::class);

    /* Comments */
    Route::delete('comments', [CommentController::class, 'destroyAll']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
    Route::resource('comments', CommentController::class);

    /* Posts */
    Route::delete('posts/{post}', [PostController::class, 'destroy']);
    Route::resource('posts', PostController::class);
});

// special sections

// users

// activities
Route::resource('here', ActivityController::class);

// search
Route::get('search', [SearchController::class, 'index']);
Route::get('search/{text}', [SearchController::class, 'find']);
Route::post('search', [SearchController::class, 'submitSearch']);

// conversations
Route::get('chat/{username}', [ChatController::class, 'conversation']);
Route::post('chat/{username}', [ChatController::class, 'store']);
Route::resource('chat', ChatController::class);

// chat refactor for vue
Route::get('chats/{username}', [ChatApiController::class, 'show']);
Route::get('chats', [ChatApiController::class, 'index']);
Route::get('chatsusers', [ChatApiController::class, 'chatPartnerIndex']);

// restore
Route::resource('archive', RestoreController::class);
Route::post('archive/section/{section}', [RestoreController::class, 'section'])
    ->name('archive.section');
Route::post('archive/topic/{topic}', [RestoreController::class, 'topic'])
    ->name('archive.topic');
