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

            if ($channelSegments[0] === 'private-messages') {
                $data['is_online'] = $event['name'] === 'channel_occupied';
                if (!$data['is_online']) {
                    $data['last_online_at'] = now();
                }

                $user = User::find($channelSegments[1]);
                $user->update($data);

                $userContacts = $userService->getUserContacts($user);
                Notification::send($userContacts, new UserOnlineStatusChangedNotification($user));
            }
        });
    }
}
