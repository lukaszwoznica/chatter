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

    private User $user;
    private string $passwordConfirmRoute = '/api/v1/user/confirm-password';

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
            'password' => 'ChatterPass123'
        ]);

        $response->assertCreated();
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

    public function testUserCannotConfirmPasswordWithoutProvidingPassword()
    {
        $this->testPasswordConfirmWithInvalidData('password', '');
    }

    public function testUserCannotConfirmPasswordWithInvalidPassword()
    {
        $this->testPasswordConfirmWithInvalidData('password', 'invalid_password');
    }

    private function testPasswordConfirmWithInvalidData(string $invalidFieldName, string $invalidValue)
    {
        $response = $this->actingAs($this->user)->postJson($this->passwordConfirmRoute, [
            $invalidFieldName => $invalidValue
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors($invalidFieldName);
    }
}
