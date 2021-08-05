<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ContactsTest extends TestCase
{
    use RefreshDatabase;

    private User $currentUser;
    private Collection $userContacts;

    protected function setUp(): void
    {
        parent::setUp();

        $users = User::factory()->count(4)->create();
        $this->currentUser = $users->first();
        $this->userContacts = $users->except([$this->currentUser->id]);
    }

    public function testUserCannotGetContactsListWhenUnauthorized()
    {
        $response = $this->getJson(route('users.contacts', $this->currentUser->id));

        $response->assertUnauthorized();
    }

    public function testContactsListIsEmptyWhenUserHasNoConversations()
    {
        $response = $this->actingAs($this->currentUser)
            ->getJson(route('users.contacts', $this->currentUser->id));

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testContactsListContainsOnlyUsersWithWhomConversationWasConducted()
    {
        Message::factory()->count(2)->state(new Sequence(
            ['sender_id' => $this->currentUser->id, 'recipient_id' => $this->userContacts->first()->id],
            ['sender_id' => $this->userContacts->last()->id, 'recipient_id' => $this->currentUser->id],
        ))->create();

        $response = $this->actingAs($this->currentUser)
            ->getJson(route('users.contacts', $this->currentUser));

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $this->userContacts->first()->id])
            ->assertJsonFragment(['id' => $this->userContacts->last()->id])
            ->assertJsonMissing(['id' => $this->userContacts->skip(1)->first()]);
    }

    public function testContactResourceHasCorrectDataStructure()
    {
        Message::factory()->count(5)->create([
            'sender_id' => $this->currentUser->id
        ]);

        $response = $this->actingAs($this->currentUser)
            ->getJson(route('users.contacts', $this->currentUser->id));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'unread_messages',
                        'last_message',
                        'is_online',
                        'last_online_at'
                    ]
                ]
            ]);
    }
}
