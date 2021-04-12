<?php


namespace App\Services;


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
        })->get();
    }
}
