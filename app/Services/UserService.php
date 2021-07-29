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
            $query->whereRaw($this->concatenateStrings('first_name', "' '", 'last_name') . ' like ?', [$pattern])
                ->orWhereRaw($this->concatenateStrings('last_name', "' '", 'first_name') . ' like ?', [$pattern]);
        })->get();
    }

    public function getUserContacts(User $user): Collection
    {
        return User::whereHas('messagesSent', function ($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orWhereHas('messagesReceived', function ($query) use ($user) {
            $query->where('sender_id', $user->id);
        })->withCount(['messagesSent as unread_messages' => function ($query) use ($user) {
            $query->where('recipient_id', $user->id)
                ->whereNull('read_at');
        }])->addSelect([
            'last_message' => Message::select('created_at')
                ->where(function ($query) use ($user) {
                    $query->whereColumn('sender_id', 'users.id')
                        ->where('recipient_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->whereColumn('recipient_id', 'users.id')
                        ->where('sender_id', $user->id);
                })->orderByDesc('created_at')
                ->limit(1)
        ])->orderByDesc('last_message')
            ->get();
    }

    private function concatenateStrings(...$columnNames): string
    {
        return env('DB_CONNECTION') === 'sqlite'
            ? implode(' || ', $columnNames)
            : 'concat(' . implode(', ', $columnNames) . ')';
    }
}
