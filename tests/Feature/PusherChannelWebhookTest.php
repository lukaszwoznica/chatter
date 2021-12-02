<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use App\Notifications\UserOnlineStatusChangedNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PusherChannelWebhookTest extends TestCase
{
    use RefreshDatabase;

    private User $testUser;
    private Collection $testUserContacts;
    private string $pusherSigningSecret;
    private string $pusherSignatureHeaderName;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->testUser = User::factory()->count(10)->create()->first();
        Message::factory()->count(100)->create();
        $this->testUserContacts = App::make('App\Services\UserService')->getUserContacts($this->testUser);

        $this->pusherSigningSecret = config('webhook-client.configs.0.signing_secret');
        $this->pusherSignatureHeaderName = config('webhook-client.configs.0.signature_header_name');
    }

    public function testUserIsOnlineAfterReceivingChannelOccupiedEvent()
    {
        $payload = $this->getPayload($this->testUser->id, 'channel_occupied');

        $response = $this->withHeader($this->pusherSignatureHeaderName, $this->getSignature($payload))
            ->postJson(route('webhook-client-pusher'), $payload);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $this->testUser->id,
            'is_online' => true
        ]);
        $this->assertDatabaseCount('webhook_calls', 1);
        Notification::assertSentTo($this->testUserContacts, UserOnlineStatusChangedNotification::class);
    }

    public function testUserIsOfflineAfterReceivingChannelVacatedEvent()
    {
        $this->testUser->update([
            'is_online' => true
        ]);
        $payload = $this->getPayload($this->testUser->id, 'channel_vacated');

        $response = $this->withHeader($this->pusherSignatureHeaderName, $this->getSignature($payload))
            ->postJson(route('webhook-client-pusher'), $payload);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $this->testUser->id,
            'is_online' => false
        ]);
        $this->assertDatabaseCount('webhook_calls', 1);
        Notification::assertSentTo($this->testUserContacts, UserOnlineStatusChangedNotification::class);
    }

    public function testWebhookRequestMustContainValidSignature()
    {
        $payload = $this->getPayload($this->testUser->id, 'channel_occupied');

        $response = $this->postJson(route('webhook-client-pusher'), $payload);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJsonPath('message', 'The signature is invalid.');
        $this->assertDatabaseCount('webhook_calls', 0);
        $this->assertDatabaseHas('users', [
            'id' => $this->testUser->id,
            'is_online' => false
        ]);
        Notification::assertNotSentTo($this->testUserContacts, UserOnlineStatusChangedNotification::class);
    }

    private function getPayload(int $userId, string $eventType): array
    {
        return [
            'time_ms' => now()->timestamp,
            'events' => [
                [
                    'channel' => "private-messages.$userId",
                    'name' => $eventType
                ]
            ]
        ];
    }

    private function getSignature(array $payload)
    {
        return hash_hmac('sha256', json_encode($payload), $this->pusherSigningSecret);
    }
}
