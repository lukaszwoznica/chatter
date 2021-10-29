<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesReadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $latestReadMessage;

    /**
     * Create a new event instance.
     *
     * @param Message $latestReadMessage
     */
    public function __construct(Message $latestReadMessage)
    {
        $this->latestReadMessage = $latestReadMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . $this->latestReadMessage->sender_id);
    }

    public function broadcastWith()
    {
        return [
            'latest_read_message' => (new MessageResource($this->latestReadMessage))->resolve()
        ];
    }
}
