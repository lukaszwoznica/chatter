<?php

namespace App\Jobs;


use App\Models\User;
use App\Notifications\UserOnlineStatusChangedNotification;
use App\Services\UserService;
use Illuminate\Support\Facades\Notification;
use Spatie\WebhookClient\ProcessWebhookJob;

class PusherChannelWebhookHandler extends ProcessWebhookJob
{
    public function handle(UserService $userService)
    {
        $payload = json_decode($this->webhookCall, true)['payload'];

        collect($payload['events'])->each(function ($event) use ($userService) {
            $channelSegments = explode('.', $event['channel'], 2);
            if ($channelSegments[0] !== 'private-messages' ||
                !in_array($event['name'], ['channel_occupied', 'channel_vacated'])) {
                return;
            }

            $user = $this->updateUserOnlineStatus(intval($channelSegments[1]), $event['name']);
            if ($user) {
                $this->notifyUserContacts($user, $userService);
            }
        });
    }

    private function updateUserOnlineStatus(int $userId, string $eventType): ?User
    {
        $data['is_online'] = $eventType === 'channel_occupied';
        if ($eventType === 'channel_vacated') {
            $data['last_online_at'] = now();
        }

        $user = User::find($userId);
        $user?->update($data);

        return $user;
    }

    private function notifyUserContacts(User $user, UserService $userService): void
    {
        $userContacts = $userService->getUserContacts($user);
        Notification::send($userContacts, new UserOnlineStatusChangedNotification($user));
    }
}
