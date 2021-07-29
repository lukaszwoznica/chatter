<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanResetPasswordWithValidToken()
    {
        Event::fake();

        $response = $this->postJson(route('password.update'), $this->passwordResetData());

        $response->assertOk();
        $this->assertTrue(Hash::check('newPassword', $this->user->fresh()->password));
        Event::assertDispatched(PasswordReset::class, fn($event) => $event->user->id === $this->user->id);
    }

    public function testUserCannotResetPasswordWithInvalidToken()
    {
        $response = $this->postJson(route('password.update'), array_replace($this->passwordResetData(), [
            'token' => 'invalid-token'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email')->etc());
    }

    public function testUserCannotResetPasswordWithoutProvidingEmail()
    {
        $response = $this->postJson(route('password.update'), array_replace($this->passwordResetData(), [
            'email' => ''
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email')->etc());
    }

    public function testUserCannotResetPasswordWithInvalidEmail()
    {
        $response = $this->postJson(route('password.update'), array_replace($this->passwordResetData(), [
            'email' => 'invalid-email'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email')->etc());
    }

    public function testUserCannotResetPasswordWithEmailThatNotExists()
    {
        $response = $this->postJson(route('password.update'), array_replace($this->passwordResetData(), [
            'email' => 'nonexist@email.com'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email')->etc());
    }

    public function testUserCannotResetPasswordWithoutProvidingNewPassword()
    {
        $response = $this->postJson(route('password.update'), array_replace($this->passwordResetData(), [
            'password' => ''
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.password')->etc());
    }

    public function testUserCannotResetPasswordWhenPasswordConfirmationNotMatch()
    {
        $response = $this->postJson(route('password.update'), array_replace($this->passwordResetData(), [
            'password_confirmation' => 'invalid'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.password')->etc());
    }

    public function testUserCannotResetPasswordWhenNewPasswordIsShorterThanEightCharacters()
    {
        $response = $this->postJson(route('password.update'), array_replace($this->passwordResetData(), [
            'password' => 'pass'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.password')->etc());
    }

    private function passwordResetData(): array
    {
        return [
            'token' => Password::createToken($this->user),
            'email' => $this->user->email,
            'password' => 'newPassword',
            'password_confirmation' => 'newPassword',
        ];
    }
}
