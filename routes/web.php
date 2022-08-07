<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::name('chat.')->group(function () {
        Route::get('', [HomeController::class, 'index'])->name('index');
        Route::get('create', [HomeController::class, 'create'])->name('create');
        Route::get('conversation/{user}', [HomeController::class, 'show'])->name('show')->where('user', '[0-9]+');
        Route::post('', [HomeController::class, 'store'])->name('store');

        Route::prefix('chat')->group(function () {
            Route::get('', [ChatController::class, 'index']);
            Route::get('{user}', [ChatController::class, 'show'])->where('user', '[0-9]+');
            Route::post('message/{user}', [HomeController::class, 'send'])->name('send')->where('user', '[0-9]+');
            Route::delete('{user}', [ChatController::class, 'destroy'])->name('delete')->where('user', '[0-9]+');
        });
    });
});

Auth::routes();
