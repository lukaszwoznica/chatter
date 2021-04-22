<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('messages.{recipient}', function ($user, User $recipient) {
    return (int) $user->id === (int) $recipient->id;
});

Broadcast::channel('conversation.{id}', function ($user) {
    return auth()->check();
});
