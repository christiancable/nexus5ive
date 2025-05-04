<?php

use App\Http\Controllers\Nexus\ActivityController;
use App\Http\Controllers\Nexus\ChatController;
use App\Http\Controllers\Nexus\CommentController;
use App\Http\Controllers\Nexus\ModeController;
use App\Http\Controllers\Nexus\PostController;
use App\Http\Controllers\Nexus\ReportController;
use App\Http\Controllers\Nexus\RestoreController;
use App\Http\Controllers\Nexus\SearchController;
use App\Http\Controllers\Nexus\SectionController;
use App\Http\Controllers\Nexus\TopicController;
use App\Http\Controllers\Nexus\UserController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

// above default laravel stuff

Route::middleware(['auth', 'verified'])->group(function () {
    /* Admin */
    Route::middleware([EnsureUserIsAdmin::class])
        ->prefix('admin')
        ->group(function () {
            Route::resource('theme', ModeController::class);
            Route::get('reports', [ReportController::class, 'index']);
        });

    /* Sections */
    Route::get('/', [SectionController::class, 'index'])->name('dashboard');
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
    Route::get('posts/{post}/report', [PostController::class, 'report'])->name('report');
    Route::resource('posts', PostController::class);

    /* moderation and reporting - all users can report */
    Route::post('report/{type}/{id}', [ReportController::class, 'store']);

    /* messages */
    Route::get('chat/{user?}', [ChatController::class, 'index']);

    /* Search */
    Route::get('search', [SearchController::class, 'index']);
    Route::get('search/{text}', [SearchController::class, 'find']);
    Route::post('search', [SearchController::class, 'submitSearch']);

    /* misc */
    Route::get('updateSubscriptions', [TopicController::class, 'markAllSubscribedTopicsAsRead']);
    Route::resource('here', ActivityController::class);

    // restore
    Route::resource('archive', RestoreController::class);
    Route::post('archive/section/{section}', [RestoreController::class, 'section'])
        ->name('archive.section');
    Route::post('archive/topic/{topic}', [RestoreController::class, 'topic'])
        ->name('archive.topic');
});
