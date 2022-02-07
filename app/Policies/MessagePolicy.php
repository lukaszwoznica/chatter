<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Allow the user to mark the message as read only if he is the recipient
     */
    public function markAsRead(User $user, Message $message): bool
    {
        return (int) $user->id === (int) $message->recipient_id;
    }
}
