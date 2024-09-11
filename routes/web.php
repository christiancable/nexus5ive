<?php

use App\Http\Controllers\Nexus\SectionController;
use App\Http\Controllers\Nexus\ModeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


/*
Sections
*/

Route::get('/', [SectionController::class, 'index']);
Route::get('/home', [SectionController::class, 'index']);
Route::get('leap', [SectionController::class, 'leap']);
Route::get('/section/latest', [SectionController::class, 'latest']);
Route::resource('section', SectionController::class);

/* 
Admin
✅ auth
✅ verified
🔳 admin
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('admin', ModeController::class);
    Route::post('admin', [ModeController::class, 'handle'])
        ->name('mode.handle');
});