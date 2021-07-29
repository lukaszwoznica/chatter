<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ConfirmPasswordTest extends TestCase
{
    use RefreshDatabase;

    private string $passwordConfirmRoute = '/api/v1/user/confirm-password';
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanCheckPasswordConfirmationStatus()
    {
        $response = $this->actingAs($this->user)->getJson(route('password.confirmation'));

        $response->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->has('confirmed'));
    }

    public function testUserCanConfirmPassword()
    {
        $response = $this->actingAs($this->user)->postJson($this->passwordConfirmRoute, [
            'password' => 'password'
        ]);

        $response->assertCreated();
    }

    public function testUserCannotConfirmPasswordWithoutProvidingPassword()
    {
        $response = $this->actingAs($this->user)->postJson($this->passwordConfirmRoute, [
            'password' => ''
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.password')->etc());
    }

    public function testUserCannotConfirmPasswordWithInvalidPassword()
    {
        $response = $this->actingAs($this->user)->postJson($this->passwordConfirmRoute, [
            'password' => 'invalid'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.password')->etc());
    }

    public function testUserCannotCheckPasswordConfirmationStatusWhenUnauthenticated()
    {
        $response = $this->getJson(route('password.confirmation'));

        $response->assertUnauthorized();
    }

    public function testUserCannotConfirmPasswordWhenUnauthenticated()
    {
        $response = $this->postJson($this->passwordConfirmRoute, [
            'password' => 'password'
        ]);

        $response->assertUnauthorized();
    }
}
