<?php


namespace App\Services;


use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class MessageService
{
    public function getAllForUser(User $user): Collection
    {
        return Message::where(function ($query) use ($user) {
            $query->where('sender_id', auth()->id())
                ->where('recipient_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->where('recipient_id', auth()->id());
        })->with('sender', 'recipient')->get();
    }

    public function markAsReadFromUser(User $user): void
    {
        Message::where('sender_id', $user->id)
            ->where('recipient_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function create(array $data): Message
    {
        return Message::create(array_merge($data, [
            'sender_id' => auth()->id()
        ]));
    }
}
