<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testEmailAddressIsRequiredToSendEmailWithPasswordResetLink()
    {
        $response = $this->postJson(route('password.email'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email'));
    }

    public function testEmailAddressMustBeValidToSendEmailWithPasswordResetLink()
    {
        $response = $this->postJson(route('password.email'), [
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email'));
    }

    public function testEmailWithPasswordResetLinkIsSentAfterCorrectUserRequest()
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->postJson(route('password.email'), [
            'email' => $user->email
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('password_resets', [
            'email' => $user->email
        ]);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function testEmailWithPasswordResetLinkIsNotSentWhenUserNotExists()
    {
        Notification::fake();

        $response = $this->postJson(route('password.email'), [
            'email' => 'nonexist@email.com'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn(AssertableJson $json) => $json->has('errors.email'));
        $this->assertDatabaseMissing('password_resets', [
            'email' => 'nonexist@email.com'
        ]);
        Notification::assertNotSentTo(User::factory()->make(['email' => 'nonexist@email.com']), ResetPassword::class);
    }

    public function testUserCannotRequestEmailWithPasswordResetLinkWhenAuthenticated()
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->postJson(route('password.email'), [
            'email' => $user->email
        ]);

        $response->assertOk()
            ->assertJson([
                'error' => 'Already authenticated.'
            ]);
    }
}
