<?php

use App\Http\Controllers\Nexus\ModeController;
use App\Http\Controllers\Nexus\SectionController;
use App\Http\Controllers\Nexus\SearchController;
use App\Http\Controllers\Nexus\RestoreController;
use App\Http\Controllers\Nexus\ChatController;
use App\Http\Controllers\Nexus\UserController;
use App\Http\Controllers\Nexus\ActivityController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\EnsureUserIsAdmin;

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

// admin

Route::middleware(['auth', EnsureUserIsAdmin::class])->group(function() {
    Route::resource('admin', ModeController::class);
    Route::post('admin', [ModeController::class, 'handle'])
        ->name('mode.handle');
});


// special sections
Route::get('/', [SectionController::class, 'index']);
Route::get('/home', [SectionController::class, 'index']);

Route::get('leap', [SectionController::class, 'leap']);
Route::get('/section/latest', [SectionController::class, 'latest'])->name('latest');

// sections
Route::resource('section', SectionController::class);


// users
Route::resource('users', UserController::class);

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