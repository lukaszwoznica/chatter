<?php

namespace App\Jobs;


use App\Models\User;
use App\Notifications\UserOnlineStatusChangedNotification;
use App\Services\UserService;
use Illuminate\Support\Facades\Notification;
use Spatie\WebhookClient\ProcessWebhookJob;

class PusherWebhooksHandler extends ProcessWebhookJob
{
    public function handle(UserService $userService)
    {
        $payload = json_decode($this->webhookCall, true)['payload'];

        collect($payload['events'])->each(function ($event) use ($userService) {
            $channelSegments = explode('.', $event['channel'], 2);
            if ($channelSegments[0] !== 'private-messages') {
                return;
            }

            $data = $this->prepareUserOnlineStatusDataForUpdate($event['name']);
            $user = $this->updateUserOnlineStatus(intval($channelSegments[1]), $data);
            $this->notifyUserContacts($user, $userService);
        });
    }

    private function prepareUserOnlineStatusDataForUpdate(string $eventType): array
    {
        $data['is_online'] = $eventType === 'channel_occupied';
        if (!$data['is_online']) {
            $data['last_online_at'] = now();
        }

        return $data;
    }

    private function updateUserOnlineStatus(int $userId, array $data): User
    {
        $user = User::find($userId);
        $user->update($data);

        return $user;
    }

    private function notifyUserContacts(User $user, UserService $userService): void
    {
        $userContacts = $userService->getUserContacts($user);
        Notification::send($userContacts, new UserOnlineStatusChangedNotification($user));
    }
}
