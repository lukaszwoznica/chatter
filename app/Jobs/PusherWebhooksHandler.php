<?php

namespace App\Jobs;


use App\Models\User;
use Spatie\WebhookClient\ProcessWebhookJob;

class PusherWebhooksHandler extends ProcessWebhookJob
{
    public function handle()
    {
        $payload = json_decode($this->webhookCall, true)['payload'];

        collect($payload['events'])->each(function ($event) {
            $channelSegments = explode('.', $event['channel'], 2);

            if ($channelSegments[0] === 'private-messages') {
                $data['is_online'] = $event['name'] === 'channel_occupied';
                if (!$data['is_online']) {
                    $data['last_online_at'] = now();
                }

                User::where('id', $channelSegments[1])
                    ->update($data);

                // notify contacts that user status has changed
            }
        });
    }
}
