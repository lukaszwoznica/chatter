<?php


namespace App\Services;


use App\Events\MessagesReadEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageService
{
    public function getAllBetweenUsers(User $firstUser, User $secondUser, int $perPage): LengthAwarePaginator
    {
        return Message::where(function ($query) use ($firstUser, $secondUser) {
            $query->where('sender_id', $firstUser->id)
                ->where('recipient_id', $secondUser->id);
        })->orWhere(function ($query) use ($firstUser, $secondUser) {
            $query->where('sender_id', $secondUser->id)
                ->where('recipient_id', $firstUser->id);
        })->latest()->paginate($perPage);
    }

    public function markAllReceivedMessagesFromUserAsRead(User $recipient, User $sender): void
    {
        $updatedMessagesCount = Message::where('sender_id', $sender->id)
            ->where('recipient_id', $recipient->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($updatedMessagesCount > 0) {
            $latestReadMessage = Message::where('sender_id', $sender->id)
                ->where('recipient_id', $recipient->id)
                ->orderByDesc('created_at')
                ->first();

            MessagesReadEvent::dispatch($latestReadMessage);
        }
    }

    public function markAsRead(Message $message): Message
    {
        if (!$message->read_at) {
            $message->read_at = now();
            $message->save();

            MessagesReadEvent::dispatch($message);
        }

        return $message;
    }
}
