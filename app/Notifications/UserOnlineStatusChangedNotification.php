<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class UserOnlineStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'user' => [
                'id' => $this->user->id,
                'is_online' => $this->user->is_online,
                'last_online_at' => optional($this->user->last_online_at)->format('Y-m-d H:i:s')
            ]
        ]);
    }

    public function broadcastType()
    {
        return 'UserOnlineStatusChangedNotification';
    }
}
