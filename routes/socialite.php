<?php

use App\Http\Controllers\Api\V1\OAuthController;
use Illuminate\Support\Facades\Route;

Route::controller(OAuthController::class)->group(function () {
    Route::get('', 'getProviderRedirectOrl')->name('redirect');
    Route::get('callback', 'handleCallback')->name('callback');
});

