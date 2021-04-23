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

Broadcast::channel('conversation.{id}', function ($user, $conversationId) {
    $t = floor((-1 + sqrt(1 + 8 * $conversationId)) / 2);
    $usersIds[] = $t * ($t + 3) / 2 - $conversationId;
    $usersIds[] = $conversationId - $t * ($t + 1) / 2;

    return in_array((int) $user->id, $usersIds);
});

Broadcast::channel('online', function ($user) {
   return $user;
});
