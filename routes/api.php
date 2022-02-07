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
            Route::controller(AvatarController::class)->group(function () {
                Route::post('', 'store')->name('store');
                Route::delete('', 'destroy')->name('destroy');
            });
        });
    });
    Route::apiResource('users', UserController::class)->only('index', 'show');

    Route::controller(MessageController::class)->prefix('messages')->name('messages.')->group(function () {
        Route::get('{user}', 'conversation')->name('conversation');
        Route::post('', 'send')->name('send');
        Route::patch('{message}', 'markAsRead')->name('read');
    });
});
