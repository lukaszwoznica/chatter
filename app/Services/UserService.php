<?php


namespace App\Services;


use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function getAll(string $nameFilter = null): Collection
    {
        return User::when($nameFilter, function ($query) use ($nameFilter) {
            $pattern = "%$nameFilter%";
            $query->whereRaw("concat(first_name, ' ', last_name) like ?", [$pattern])
                ->orWhereRaw("concat(last_name, ' ', first_name) like ?", [$pattern]);
        })->get();
    }

    public function getAuthUserContacts(): Collection
    {
        return User::whereHas('messagesSent', function ($query) {
            $query->where('recipient_id', auth()->id());
        })->orWhereHas('messagesReceived', function ($query) {
            $query->where('sender_id', auth()->id());
        })->withCount(['messagesSent as unread_messages' => function ($query) {
            $query->where('recipient_id', auth()->id())
                ->whereNull('read_at');
        }])->addSelect([
            'last_message' => Message::select('created_at')
                ->where(function ($query) {
                    $query->whereColumn('sender_id', 'users.id')
                        ->where('recipient_id', auth()->id());
                })->orWhere(function ($query) {
                    $query->whereColumn('recipient_id', 'users.id')
                        ->where('sender_id', auth()->id());
                })->orderByDesc('created_at')
                ->limit(1)
        ])->orderByDesc('last_message')
            ->get();
    }
}
