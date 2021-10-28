<?php


namespace App\Services;


use App\Models\Message;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Socialite\Two\User as SocialUser;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserService
{
    public function getAll(int $perPage, string $nameFilter = null, array $exceptKeys = null): LengthAwarePaginator
    {
        return User::with('media')
            ->when($exceptKeys, function ($query) use ($exceptKeys) {
                $query->whereKeyNot($exceptKeys);
            })->when($nameFilter, function ($query) use ($nameFilter) {
                $query->where(function ($query) use ($nameFilter) {
                    $pattern = "%$nameFilter%";
                    $query->whereRaw($this->rawQueryConcat('first_name', "' '", 'last_name') . ' like ?', [$pattern])
                        ->orWhereRaw($this->rawQueryConcat('last_name', "' '", 'first_name') . ' like ?', [$pattern]);
                });
            })->paginate($perPage);
    }

    public function getUserContacts(User $user): Collection
    {
        return User::with('media')
            ->whereHas('messagesSent', function ($query) use ($user) {
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
            ])->orderByDesc('last_message')->get();
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

    public function findSocialAccount(SocialUser $socialUser, string $providerName): ?SocialAccount
    {
        return SocialAccount::where([
            'provider_name' => $providerName,
            'provider_id' => $socialUser->getId()
        ])->first();
    }

    public function createSocialAccountForUser(User $user, SocialUser $socialUser, string $providerName): Model
    {
        return $user->socialAccounts()->create([
            'provider_name' => $providerName,
            'provider_id' => $socialUser->getId()
        ]);
    }

    public function getOrCreateUserFromOAuthProviderData(SocialUser $socialUser): User
    {
        $nameParts = explode(' ', $socialUser->getName());

        return User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'first_name' => head($nameParts),
                'last_name' => last($nameParts)
            ]
        );
    }

    private function rawQueryConcat(...$strings): string
    {
        return env('DB_CONNECTION') === 'sqlite'
            ? implode(' || ', $strings)
            : 'concat(' . implode(', ', $strings) . ')';
    }
}
