<?php

use App\Http\Controllers\Api\V1\OAuthController;
use Illuminate\Support\Facades\Route;

Route::get('', [OAuthController::class, 'getProviderRedirectOrl'])->name('redirect');
Route::get('callback', [OAuthController::class, 'handleCallback'])->name('callback');
