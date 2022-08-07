<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('chat')->name('chat.')->group(function () {

        Route::prefix('{user}/message')->name('message.')->group(function () {
            Route::post('', [MessageController::class, 'store']);
        });

        Route::get('', [ChatController::class, 'index']);
        Route::get('{user}', [ChatController::class, 'show'])
            ->name('show')
            ->where('user', '[0-9]+');
        Route::post('create', [ChatController::class, 'store']);
        Route::delete('{user}', [ChatController::class, 'destroy'])
            ->where('user', '[0-9]+');
    });
});
