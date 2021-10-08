<?php


namespace App\Services;


use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserService
{
    public function getAll(int $perPage, string $nameFilter = null): LengthAwarePaginator
    {
        return User::when($nameFilter, function ($query) use ($nameFilter) {
            $pattern = "%$nameFilter%";
            $query->whereRaw($this->rawQueryConcat('first_name', "' '", 'last_name') . ' like ?', [$pattern])
                ->orWhereRaw($this->rawQueryConcat('last_name', "' '", 'first_name') . ' like ?', [$pattern]);
        })->paginate($perPage);
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

    /**
     * @throws \Exception
     */
    public function uploadUserAvatar(User $user, string $avatarPath): ?Media
    {
        $user->addMedia($avatarPath)
            ->addCustomHeaders([
                'ACL' => 'public-read'
            ])
            ->usingFileName('avatar.' . pathinfo($avatarPath, PATHINFO_EXTENSION))
            ->toMediaCollection('avatar');

        return $user->getFirstMedia('avatar');
    }

    private function rawQueryConcat(...$strings): string
    {
        return env('DB_CONNECTION') === 'sqlite'
            ? implode(' || ', $strings)
            : 'concat(' . implode(', ', $strings) . ')';
    }
}
