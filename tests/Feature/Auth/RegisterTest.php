<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private string $registerRoute = '/api/v1/register';

    public function testUserCanRegisterWithCorrectData()
    {
        Event::fake();

        $response = $this->postJson($this->registerRoute, $this->getUserData());

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

        $response = $this->actingAs($user)->postJson($this->registerRoute, $this->getUserData());

        $response->assertOk()
            ->assertJson([
                'error' => 'Already authenticated.'
            ]);
    }

    public function testUserCannotRegisterWithoutFirstName()
    {
        $this->testRegisterWithInvalidData('first_name', '');
    }

    public function testUserCannotRegisterWithoutLastName()
    {
        $this->testRegisterWithInvalidData('last_name', '');
    }

    public function testUserCannotRegisterWithoutEmail()
    {
        $this->testRegisterWithInvalidData('email', '');
    }

    public function testUserCannotRegisterWithInvalidEmail()
    {
        $this->testRegisterWithInvalidData('email', 'invalid_email');
    }

    public function testUserCannotRegisterWithTakenEmail()
    {
        User::factory()->create([
            'email' => 'takenemail@example.com'
        ]);

        $this->testRegisterWithInvalidData('email', 'takenemail@example.com');
    }

    public function testUserCannotRegisterWithoutPassword()
    {
        $this->testRegisterWithInvalidData('password', '');
    }

    public function testUserCannotRegisterWithoutPasswordConfirmation()
    {
        $this->testRegisterWithInvalidData('password_confirmation', '', 'password');
    }

    public function testUserCannotRegisterWhenPasswordConfirmationNotMatch()
    {
        $this->testRegisterWithInvalidData('password_confirmation', 'Password', 'password');
    }

    public function testUserCannotRegisterWhenPasswordIsShorterThanEightCharacters()
    {
        $this->testRegisterWithInvalidData('password', 'pass');
    }

    private function testRegisterWithInvalidData(string $invalidFieldName,
                                                 string $invalidValue,
                                                 string $fieldValidationError = null)
    {
        $response = $this->postJson($this->registerRoute, array_replace($this->getUserData(), [
            $invalidFieldName => $invalidValue
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors($fieldValidationError ?? $invalidFieldName);
    }

    private function getUserData(): array
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
