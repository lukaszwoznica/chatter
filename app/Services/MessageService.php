<?php


namespace App\Services;


use App\Events\MessagesReadEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageService
{
    public function getAllForUser(User $user, int $perPage): LengthAwarePaginator
    {
        return Message::where(function ($query) use ($user) {
            $query->where('sender_id', auth()->id())
                ->where('recipient_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->where('recipient_id', auth()->id());
        })->latest()->paginate($perPage);
    }

    public function markAllAsReadFromUser(User $user): void
    {
        $updatedMessagesCount = Message::where('sender_id', $user->id)
            ->where('recipient_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($updatedMessagesCount > 0) {
            $latestReadMessage = Message::where('sender_id', $user->id)
                ->where('recipient_id', auth()->id())
                ->orderByDesc('created_at')
                ->first();

            MessagesReadEvent::dispatch($latestReadMessage);
        }
    }

    public function markAsRead(Message $message): Message
    {
        $message->read_at = now();
        $message->save();

        return $message;
    }

    public function create(array $data): Message
    {
        $message = Message::create(array_merge($data, [
            'sender_id' => auth()->id()
        ]));

        return $message;
    }
}
