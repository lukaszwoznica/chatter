<?php

namespace Tests\Feature;

use App\Events\MessagesReadEvent;
use App\Events\NewMessageEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MessagesTest extends TestCase
{
    use RefreshDatabase;

    private User $currentUser;
    private User $differentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentUser = User::factory()->create();
        $this->differentUser = User::factory()->create();
    }

    public function testUserCanGetAllMessagesFromConversationWithOtherUser()
    {
        Message::factory()->count(10)->create();

        $response = $this->actingAs($this->currentUser)
            ->getJson(route('messages.conversation', $this->differentUser));

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'text',
                        'sender_id',
                        'recipient_id',
                        'read_at',
                        'created_at'
                    ]
                ]
            ]);
    }

    public function testUserCannotGetMessagesFromConversationWithOtherUserWhenUnauthenticated()
    {
        $response = $this->getJson(route('messages.conversation', $this->differentUser));

        $response->assertUnauthorized();
    }

    public function testUserCannotGetMessagesFromConversationWithUserThatNotExists()
    {
        $response = $this->actingAs($this->currentUser)
            ->getJson(route('messages.conversation', 5));

        $response->assertNotFound();
    }

    public function testUserReceivesMessagesOnlyFromConversationWithSpecifiedUser()
    {
        $thirdUser = User::factory()->create();

        // currentUser has 10 messages with differentUser and 8 messages with thirdUser
        Message::factory()->count(18)->state(new Sequence(
            ['sender_id' => $this->currentUser->id, 'recipient_id' => $this->differentUser->id],
            ['sender_id' => $this->differentUser->id, 'recipient_id' => $this->currentUser->id],
            ['sender_id' => $this->currentUser->id, 'recipient_id' => $thirdUser->id],
            ['sender_id' => $thirdUser->id, 'recipient_id' => $this->currentUser->id],
        ))->create();

        $response = $this->actingAs($this->currentUser)
            ->getJson(route('messages.conversation', $this->differentUser));

        $response->assertOk()->assertJsonCount(10, 'data');
    }

    public function testUserCanSendMessageToOtherUser()
    {
        Event::fake();

        $response = $this->actingAs($this->currentUser)->postJson(route('messages.send'), $this->getMessageData());

        $response->assertCreated()
            ->assertJsonFragment(['text' => 'Test message'])
            ->assertJsonPath('data.sender_id', $this->currentUser->id)
            ->assertJsonPath('data.recipient_id', $this->differentUser->id);
        $this->assertCount(1, $messages = Message::all());
        Event::assertDispatched(fn(NewMessageEvent $event) => $event->message->id === $messages->first()->id);
    }

    public function testUserCannotSendMessageToOtherUserWhenUnauthenticated()
    {
        $response = $this->postJson(route('messages.send'), $this->getMessageData());

        $response->assertUnauthorized();
        $this->assertEquals(0, Message::count());
    }

    public function testUserCannotSendMessageWithoutText()
    {
        $this->testSendMessageWithInvalidData('text', '');
    }

    public function testUserCannotSendMessageWithoutRecipientId()
    {
        $this->testSendMessageWithInvalidData('recipient_id', '');
    }

    public function testUserCannotSendMessageWithInvalidRecipientId()
    {
        $this->testSendMessageWithInvalidData('recipient_id', 'not-integer');
    }

    public function testUserCannotSendMessageToUserThatNotExists()
    {
        $this->testSendMessageWithInvalidData('recipient_id', 10);
    }

    public function testUserCanMarkMessageAsRead()
    {
        Event::fake();

        $message = Message::factory()->create(array_replace($this->getMessageData(), [
            'recipient_id' => $this->currentUser->id,
            'sender_id' => $this->differentUser->id
        ]));

        $response = $this->actingAs($this->currentUser)
            ->patchJson(route('messages.read', $message));

        $response->assertOk();
        $this->assertNotNull($response->json('data.read_at'));
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'read_at' => $response->json('data.read_at')
        ]);
        Event::assertDispatched(fn(MessagesReadEvent $event) => $event->latestReadMessage->id === $message->id);
    }

    public function testUserCannotMarkMessageAsReadWhenUnauthenticated()
    {
        $message = Message::factory()->create(array_replace($this->getMessageData(), [
            'recipient_id' => $this->currentUser->id,
            'sender_id' => $this->differentUser->id
        ]));

        $response = $this->patchJson(route('messages.read', $message));

        $response->assertUnauthorized();
    }

    public function testMessageCannotBeMarkedAsReadSecondTime()
    {
        Event::fake();

        $message = Message::factory()->create(array_replace($this->getMessageData(), [
            'recipient_id' => $this->currentUser->id,
            'sender_id' => $this->differentUser->id,
            'read_at' => now()
        ]));

        $response = $this->actingAs($this->currentUser)
            ->patchJson(route('messages.read', $message));

        $response->assertOk()
            ->assertJsonPath('data.read_at', $message->read_at->format('Y-m-d H:i:s'));
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'read_at' => $message->read_at
        ]);
        Event::assertNotDispatched(MessagesReadEvent::class);
    }

    public function testUserCannotMarkMessageAsReadIfHeIsNotTheRecipient()
    {
        $message = Message::factory()->create($this->getMessageData());

        $response = $this->actingAs($this->currentUser)
            ->patchJson(route('messages.read', $message));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'read_At' => null
        ]);
    }

    private function testSendMessageWithInvalidData(string $invalidFieldName, mixed $invalidValue)
    {
        $response = $this->actingAs($this->currentUser)
            ->postJson(route('messages.send'), array_replace($this->getMessageData(), [
                $invalidFieldName => $invalidValue
            ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors($invalidFieldName);
    }

    private function getMessageData(): array
    {
        return [
            'recipient_id' => $this->differentUser->id,
            'text' => 'Test message'
        ];
    }
}
