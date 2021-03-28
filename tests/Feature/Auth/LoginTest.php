<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private string $loginRoute = '/api/v1/login';
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanLoginWithCorrectCredentials()
    {
        $response = $this->postJson($this->loginRoute, $this->userCredentials());

        $response->assertOk();
        $this->assertAuthenticatedAs($this->user);
    }

    public function testRememberCookieIsSetWhenUserLogsInWithRememberOption()
    {
        $response = $this->postJson($this->loginRoute, array_merge($this->userCredentials(), [
            'remember' => 'on'
        ]));

        $response->assertOk()
            ->assertCookie(auth()->guard()->getRecallerName());
        $this->assertAuthenticatedAs($this->user);
    }

    public function testUserCannotLoginWhenAuthenticated()
    {
        $response = $this->actingAs($this->user)->postJson($this->loginRoute, $this->userCredentials());

        $response->assertOk()
            ->assertJson([
                'error' => 'Already authenticated.'
            ]);
    }

    public function testUserCannotLoginWithoutProvidingEmail()
    {
        $response = $this->postJson($this->loginRoute, array_replace($this->userCredentials(), [
            'email' => ''
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email'));
        $this->assertGuest();
    }

    public function testUserCannotLoginWithIncorrectEmail()
    {
        $response = $this->postJson($this->loginRoute, array_replace($this->userCredentials(), [
            'email' => 'nonexist@email.com'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email'));
        $this->assertGuest();
    }

    public function testUserCannotLoginWithoutProvidingPassword()
    {
        $response = $this->postJson($this->loginRoute, array_replace($this->userCredentials(), [
            'password' => ''
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.password'));
        $this->assertGuest();
    }

    public function testUserCannotLoginWithIncorrectPassword()
    {
        $response = $this->postJson($this->loginRoute, array_replace($this->userCredentials(), [
            'password' => 'incorrect_password'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email'));
        $this->assertGuest();
    }

    public function testUserCanLogout()
    {
        $this->be($this->user);

        $response = $this->postJson(route('logout'));

        $response->assertNoContent();
        $this->assertGuest();
    }

    public function testUserCannotMakeMoreThanFiveLoginAttemptsDuringOneMinute()
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson($this->loginRoute, array_replace($this->userCredentials(), [
                'password' => 'incorrect_password'
            ]));
        }

        $response->assertStatus(Response::HTTP_TOO_MANY_REQUESTS)
            ->assertJson([
                'message' => 'Too Many Attempts.'
            ]);
        $this->assertGuest();
    }

    private function userCredentials(): array
    {
        return [
            'email' => $this->user->email,
            'password' => 'password',
        ];
    }
}
