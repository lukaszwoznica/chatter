<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function getAll(string $nameFilter = null): Collection
    {
        return User::when($nameFilter, function ($query) use ($nameFilter) {
            $query->whereRaw("concat(first_name, ' ', last_name) like '%$nameFilter%'")
                ->orWhereRaw("concat(last_name, ' ', first_name) like '%$nameFilter%'");
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
