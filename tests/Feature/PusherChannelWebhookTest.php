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
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PusherChannelWebhookTest extends TestCase
{
    use RefreshDatabase;

    private User $testUser;
    private Collection $testUserContacts;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->testUser = User::factory()->count(10)->create()->first();
        Message::factory()->count(100)->create();
        $this->testUserContacts = App::make('App\Services\UserService')->getUserContacts($this->testUser);
    }

    public function testUserIsOnlineAfterReceivingChannelOccupiedEvent()
    {
        $payload = $this->getPayload($this->testUser->id, 'channel_occupied');

        $response = $this->makeCorrectRequest($payload);

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

        $response = $this->makeCorrectRequest($payload);

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
        Notification::assertNotSentTo($this->testUserContacts, UserOnlineStatusChangedNotification::class);
    }

    public function testUserOnlineStatusDoesNotChangeWhenInvalidEventTypeIsReceived()
    {
        $payload = $this->getPayload($this->testUser->id, 'channel_invalid');

        $response = $this->makeCorrectRequest($payload);

        $response->assertOk();
        Notification::assertNotSentTo($this->testUserContacts, UserOnlineStatusChangedNotification::class);
    }

    public function testUserOnlineStatusDoesNotChangeWhenEventOccursOnWrongChannel()
    {
        $payload = $this->getPayload($this->testUser->id, 'channel_occupied', 'wrong-channel');

        $response = $this->makeCorrectRequest($payload);

        $response->assertOk();
        Notification::assertNotSentTo($this->testUserContacts, UserOnlineStatusChangedNotification::class);
    }

    public function testChannelEventIsIgnoredForInvalidUserId()
    {
        $payload = $this->getPayload(100, 'channel_occupied');

        $response = $this->makeCorrectRequest($payload);

        $response->assertOk();
        Notification::assertTimesSent(0, UserOnlineStatusChangedNotification::class);
    }

    private function getPayload(int $userId, string $eventType, string $channelPrefix = 'private-messages'): array
    {
        return [
            'time_ms' => now()->timestamp,
            'events' => [
                [
                    'channel' => "$channelPrefix.$userId",
                    'name' => $eventType
                ]
            ]
        ];
    }

    private function getSignature(array $payload): string
    {
        return hash_hmac('sha256', json_encode($payload), config('webhook-client.configs.0.signing_secret'));
    }

    private function makeCorrectRequest(array $payload): TestResponse
    {
        return $this->postJson(route('webhook-client-pusher'), $payload, [
            config('webhook-client.configs.0.signature_header_name') => $this->getSignature($payload)
        ]);
    }
}
