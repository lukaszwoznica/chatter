<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $loginRoute = '/api/v1/login';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanLoginWithCorrectCredentials()
    {
        $response = $this->postJson($this->loginRoute, $this->getUserCredentials());

        $response->assertOk();
        $this->assertAuthenticatedAs($this->user);
    }

    public function testRememberCookieIsSetWhenUserLogsInWithRememberOption()
    {
        $response = $this->postJson($this->loginRoute, array_merge($this->getUserCredentials(), [
            'remember' => 'on'
        ]));

        $response->assertOk()
            ->assertCookie(auth()->guard()->getRecallerName());
        $this->assertAuthenticatedAs($this->user);
    }

    public function testUserCannotLoginWhenAuthenticated()
    {
        $response = $this->actingAs($this->user)->postJson($this->loginRoute, $this->getUserCredentials());

        $response->assertOk()
            ->assertJson([
                'error' => 'Already authenticated.'
            ]);
    }

    public function testUserCannotLoginWithoutProvidingEmail()
    {
        $this->testLoginWithInvalidData('email', '');
    }

    public function testUserCannotLoginWithIncorrectEmail()
    {
        $this->testLoginWithInvalidData('email', 'nonexist@email.com');
    }

    public function testUserCannotLoginWithoutProvidingPassword()
    {
        $this->testLoginWithInvalidData('password', '');
    }

    public function testUserCannotLoginWithIncorrectPassword()
    {
        $this->testLoginWithInvalidData('password', 'incorrect_password', 'email');
    }

    public function testUserCanLogout()
    {
        $response = $this->actingAs($this->user)->postJson(route('logout'));

        $response->assertNoContent();
        $this->assertGuest();
    }

    public function testUserCannotMakeMoreThanFiveLoginAttemptsDuringOneMinute()
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson($this->loginRoute, array_replace($this->getUserCredentials(), [
                'password' => 'incorrect_password'
            ]));
        }

        $response->assertStatus(Response::HTTP_TOO_MANY_REQUESTS)
            ->assertJson([
                'message' => 'Too Many Attempts.'
            ]);
        $this->assertGuest();
    }

    private function testLoginWithInvalidData(string $invalidFieldName,
                                              string $invalidValue,
                                              string $fieldValidationError = null)
    {
        $response = $this->postJson($this->loginRoute, array_replace($this->getUserCredentials(), [
            $invalidFieldName => $invalidValue
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors($fieldValidationError ?? $invalidFieldName);
        $this->assertGuest();
    }

    private function getUserCredentials(): array
    {
        return [
            'email' => $this->user->email,
            'password' => 'password',
        ];
    }
}
