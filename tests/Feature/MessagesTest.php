<?php

namespace Tests\Feature;

use App\Events\NewMessageSentEvent;
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
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentUser = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    public function testUserCanGetAllMessagesFromConversationWithOtherUser()
    {
        Message::factory()->count(10)->create();

        $response = $this->actingAs($this->currentUser)
            ->getJson(route('messages.conversation', $this->otherUser));

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'text',
                        'sender',
                        'recipient',
                        'read_at',
                        'created_at'
                    ]
                ]
            ]);
    }

    public function testUserCannotGetMessagesFromConversationWithOtherUserWhenUnauthenticated()
    {
        $response = $this->getJson(route('messages.conversation', $this->otherUser));

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

        // currentUser has 10 messages with otherUser and 8 messages with thirdUser
        Message::factory()->count(18)->state(new Sequence(
            ['sender_id' => $this->currentUser->id, 'recipient_id' => $this->otherUser->id],
            ['sender_id' => $this->otherUser->id, 'recipient_id' => $this->currentUser->id],
            ['sender_id' => $this->currentUser->id, 'recipient_id' => $thirdUser->id],
            ['sender_id' => $thirdUser->id, 'recipient_id' => $this->currentUser->id],
        ))->create();

        $response = $this->actingAs($this->currentUser)
            ->getJson(route('messages.conversation', $this->otherUser));

        $response->assertOk()->assertJsonCount(10, 'data');
    }

    public function testUserCanSendMessageToOtherUser()
    {
        Event::fake();

        $response = $this->actingAs($this->currentUser)->postJson(route('messages.send'), $this->messageData());

        $response->assertCreated()
            ->assertJsonFragment(['text' => 'Test message'])
            ->assertJsonPath('data.sender.id', $this->currentUser->id)
            ->assertJsonPath('data.recipient.id', $this->otherUser->id);
        $this->assertCount(1, $messages = Message::all());
        Event::assertDispatched(fn(NewMessageSentEvent $event) => $event->message->id === $messages->first()->id);
    }

    public function testUserCannotSendMessageToOtherUserWhenUnauthenticated()
    {
        $response = $this->postJson(route('messages.send'), $this->messageData());

        $response->assertUnauthorized();
        $this->assertEquals(0, Message::count());
    }

    public function testUserCannotSendMessageWithoutText()
    {
        $response = $this->actingAs($this->currentUser)
            ->postJson(route('messages.send'), array_replace($this->messageData(), [
                'text' => ''
            ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['text']);
    }

    public function testUserCannotSendMessageWithoutRecipientId()
    {
        $response = $this->actingAs($this->currentUser)
            ->postJson(route('messages.send'), array_replace($this->messageData(), [
                'recipient_id' => ''
            ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['recipient_id']);
    }

    public function testUserCannotSendMessageWithInvalidRecipientId()
    {
        $response = $this->actingAs($this->currentUser)
            ->postJson(route('messages.send'), array_replace($this->messageData(), [
                'recipient_id' => 'not-integer'
            ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['recipient_id']);
    }

    public function testUserCannotSendMessageToUserThatNotExists()
    {
        $response = $this->actingAs($this->currentUser)
            ->postJson(route('messages.send'), array_replace($this->messageData(), [
                'recipient_id' => 10
            ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['recipient_id']);
    }

    private function messageData(): array
    {
        return [
            'recipient_id' => $this->otherUser->id,
            'text' => 'Test message'
        ];
    }
}
