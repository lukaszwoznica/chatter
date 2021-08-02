<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    private User $authUser;
    private User $searchedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $users = User::factory()->count(10)->create();

        $this->authUser = $users->first();
        $this->searchedUser = $users->last();
    }

    public function testAllUsersAreReturnedWhenSearchFilterIsNotProvided()
    {
        $response = $this->actingAs($this->authUser)
            ->getJson(route('users.index'));

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getUserResourceStructure()
                ]
            ]);
    }

    public function testUsersListCannotBeAccessedWithoutAuthentication()
    {
        $response = $this->getJson(route('users.index'));

        $response->assertUnauthorized();
    }

    public function testUsersCanBeSearchedByFirstName()
    {
        $response = $this->actingAs($this->authUser)
            ->getJson(route('users.index', [
                'search' => $this->searchedUser->first_name
            ]));

        $firstNameValues = array_fill(0, count($response->json()), $this->searchedUser->first_name);
        $response->assertOk()
            ->assertJsonPath('data.*.first_name', $firstNameValues);
    }

    public function testUsersCanBeSearchedByLastName()
    {
        $response = $this->actingAs($this->authUser)
            ->getJson(route('users.index', [
                'search' => $this->searchedUser->last_name
            ]));

        $lastNameValues = array_fill(0, count($response->json()), $this->searchedUser->last_name);
        $response->assertOk()
            ->assertJsonPath('data.*.last_name', $lastNameValues);
    }

    public function testUserDetailsAreReturnedForCorrectUserId()
    {
        $response = $this->actingAs($this->authUser)
            ->getJson(route('users.show', $this->searchedUser->id));

        $response->assertOk()
            ->assertJsonPath('data.id', $this->searchedUser->id)
            ->assertJsonStructure([
                'data' => $this->getUserResourceStructure()
            ]);
    }

    public function testUserDetailsAreNotReturnedForInvalidUserId()
    {
        $response = $this->actingAs($this->authUser)
            ->getJson(route('users.show', 15));

        $response->assertNotFound();
    }

    public function testUserDetailsCannotBeAccessedWithoutAuthentication()
    {
        $response = $this->getJson(route('users.show', 1));

        $response->assertUnauthorized();
    }

    public function testCurrentUserObjectIsReturned()
    {
        $response = $this->actingAs($this->authUser)
            ->getJson(route('users.authenticated'));

        $response->assertOk()
            ->assertJsonPath('data.id', $this->authUser->id)
            ->assertJsonStructure([
                'data' => $this->getUserResourceStructure()
            ]);
    }

    public function testCurrentUserObjectIsNotReturnedIfUserIsUnauthenticated()
    {
        $response = $this->getJson(route('users.authenticated'));

        $response->assertUnauthorized();
    }

    private function getUserResourceStructure(): array
    {
        return [
            'id',
            'first_name',
            'last_name',
            'email',
            'is_online',
            'last_online_at',
            'created_at',
            'updated_at'
        ];
    }

}
