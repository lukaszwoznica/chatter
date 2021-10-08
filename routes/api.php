<?php

use App\Http\Controllers\Api\V1\AvatarUploadController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\UserController;
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


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'authUser'])->name('users.authenticated');
    Route::post('/user/avatar', AvatarUploadController::class)->name('users.avatar');
    Route::apiResource('users', UserController::class)->only('index', 'show');
    Route::get('/contacts/{user}', ContactController::class)->name('users.contacts');

    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('{user}', [MessageController::class, 'conversation'])->name('conversation');
        Route::post('', [MessageController::class, 'send'])->name('send');
        Route::patch('{message}', [MessageController::class, 'markAsRead'])->name('read');
    });
});
