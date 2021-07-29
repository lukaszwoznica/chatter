<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private string $registerRoute = '/api/v1/register';

    public function testUserCanRegisterWithCorrectData()
    {
        Event::fake();

        $response = $this->postJson($this->registerRoute, $this->userData());

        $response->assertCreated();
        $this->assertCount(1, $users = User::all());
        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
        ]);
        Event::assertDispatched(Registered::class, fn($event) => $event->user->id === $users->first()->id);
    }

    public function testUserCannotRegisterWhenAuthenticated()
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->postJson($this->registerRoute, $this->userData());

        $response->assertOk()
            ->assertJson([
                'error' => 'Already authenticated.'
            ]);
    }

    public function testUserCannotRegisterWithoutFirstName()
    {
        $response = $this->postJson($this->registerRoute, array_replace($this->userData(), [
            'first_name' => ''
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.first_name')->etc());
    }

    public function testUserCannotRegisterWithoutLastName()
    {
        $response = $this->postJson($this->registerRoute, array_replace($this->userData(), [
            'last_name' => ''
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.last_name')->etc());
    }

    public function testUserCannotRegisterWithoutEmail()
    {
        $response = $this->postJson($this->registerRoute, array_replace($this->userData(), [
            'email' => ''
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email')->etc());
    }

    public function testUserCannotRegisterWithInvalidEmail()
    {
        $response = $this->postJson($this->registerRoute, array_replace($this->userData(), [
            'email' => 'invalid_email'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email')->etc());
    }

    public function testUserCannotRegisterWithTakenEmail()
    {
        User::factory()->create([
            'email' => 'johndoe@example.com'
        ]);

        $response = $this->postJson($this->registerRoute, $this->userData());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email')->etc());
    }

    public function testUserCannotRegisterWithoutPassword()
    {
        $response = $this->postJson($this->registerRoute, array_replace($this->userData(), [
            'password' => ''
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.password')->etc());
    }

    public function testUserCannotRegisterWhenPasswordConfirmationNotMatch()
    {
        $response = $this->postJson($this->registerRoute, array_replace($this->userData(), [
            'password_confirmation' => 'Password'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.password')->etc());
    }

    public function testUserCannotRegisterWhenPasswordIsShorterThanEightCharacters()
    {
        $response = $this->postJson($this->registerRoute, array_replace($this->userData(), [
            'password' => 'pass'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.password')->etc());
    }

    private function userData(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123'
        ];
    }
}
