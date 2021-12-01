<?php

namespace App\Jobs;


use App\Models\User;
use App\Notifications\UserOnlineStatusChangedNotification;
use App\Services\UserService;
use Illuminate\Support\Facades\Notification;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\ProcessWebhookJob;

class PusherChannelWebhookHandler extends ProcessWebhookJob
{
    private UserService $userService;

    public function __construct(WebhookCall $webhookCall, UserService $userService)
    {
        parent::__construct($webhookCall);

        $this->userService = $userService;
    }

    public function handle()
    {
        $payload = json_decode($this->webhookCall, true)['payload'];

        collect($payload['events'])->each(function ($event) {
            $channelSegments = explode('.', $event['channel'], 2);
            if ($channelSegments[0] !== 'private-messages') {
                return;
            }

            $user = $this->updateUserOnlineStatus(intval($channelSegments[1]), $event['name']);
            $this->notifyUserContacts($user);
        });
    }

    private function updateUserOnlineStatus(int $userId, string $eventType): User
    {
        $data['is_online'] = $eventType === 'channel_occupied';
        if (!$data['is_online']) {
            $data['last_online_at'] = now();
        }

        $user = User::find($userId);
        $user->update($data);

        return $user;
    }

    private function notifyUserContacts(User $user): void
    {
        $userContacts = $this->userService->getUserContacts($user);
        Notification::send($userContacts, new UserOnlineStatusChangedNotification($user));
    }
}
