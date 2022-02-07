<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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

    public function testUserCanResetPasswordWithCorrectData()
    {
        Event::fake();

        $response = $this->postJson(route('password.update'), $this->getPasswordResetData());

        $response->assertOk();
        $this->assertTrue(Hash::check('newPassword', $this->user->fresh()->password));
        Event::assertDispatched(PasswordReset::class, fn($event) => $event->user->id === $this->user->id);
    }

    public function testUserCannotResetPasswordWithInvalidToken()
    {
        $this->testPasswordResetWithInvalidData('token', 'invalid-token', 'email');
    }

    public function testUserCannotResetPasswordWithoutProvidingEmail()
    {
        $this->testPasswordResetWithInvalidData('email', '');
    }

    public function testUserCannotResetPasswordWithInvalidEmail()
    {
        $this->testPasswordResetWithInvalidData('email', 'invalid-email');
    }

    public function testUserCannotResetPasswordWithEmailThatNotExists()
    {
        $this->testPasswordResetWithInvalidData('email', 'nonexist@email.com');
    }

    public function testUserCannotResetPasswordWithoutProvidingNewPassword()
    {
        $this->testPasswordResetWithInvalidData('password', '');
    }

    public function testUserCannotResetPasswordWithoutPasswordConfirmation()
    {
        $this->testPasswordResetWithInvalidData('password_confirmation', '', 'password');
    }

    public function testUserCannotResetPasswordWhenPasswordConfirmationNotMatch()
    {
        $this->testPasswordResetWithInvalidData('password_confirmation', 'invalid', 'password');
    }

    public function testUserCannotResetPasswordWhenNewPasswordIsShorterThanEightCharacters()
    {
        $this->testPasswordResetWithInvalidData('password', 'pass');
    }

    private function testPasswordResetWithInvalidData(string $invalidFieldName,
                                                      string $invalidValue,
                                                      string $fieldValidationError = null)
    {
        $response = $this->postJson(route('password.update'), array_replace($this->getPasswordResetData(), [
            $invalidFieldName => $invalidValue
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors($fieldValidationError ?? $invalidFieldName);
    }

    private function getPasswordResetData(): array
    {
        return [
            'token' => Password::createToken($this->user),
            'email' => $this->user->email,
            'password' => 'newPassword',
            'password_confirmation' => 'newPassword',
        ];
    }
}
