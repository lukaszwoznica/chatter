<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function getAuthUserContacts(): Collection
    {
        return User::whereHas('messagesSent', function ($query) {
            $query->where('recipient_id', auth()->id());
        })->orWhereHas('messagesReceived', function ($query) {
            $query->where('sender_id', auth()->id());
        })->get();
    }
}
