<?php

use App\Http\Controllers\Api\V1\AvatarController;
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
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('auth-user', [UserController::class, 'authUser'])->name('auth.show');
        Route::get('{user}/contacts', ContactController::class)->name('contacts.index');

        Route::prefix('avatar')->name('avatar.')->group(function () {
            Route::post('', [AvatarController::class, 'store'])->name('store');
            Route::delete('', [AvatarController::class, 'destroy'])->name('destroy');
        });
    });
    Route::apiResource('users', UserController::class)->only('index', 'show');

    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('{user}', [MessageController::class, 'conversation'])->name('conversation');
        Route::post('', [MessageController::class, 'send'])->name('send');
        Route::patch('{message}', [MessageController::class, 'markAsRead'])->name('read');
    });
});
