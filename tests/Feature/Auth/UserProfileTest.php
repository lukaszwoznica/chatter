<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $profileUpdateRoute = 'user-profile-information.update';
    private string $passwordUpdateRoute = 'user-password.update';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanUpdateProfileInformationWithCorrectData()
    {
        $response = $this->actingAs($this->user)
            ->putJson(route($this->profileUpdateRoute), $this->getUserProfileUpdateData());

        $response->assertOk();
        $this->assertDatabaseHas('users', array_merge($this->getUserProfileUpdateData(), [
            'id' => $this->user->id
        ]));
    }

    public function testUserCannotUpdateProfileInformationWhenUnauthenticated()
    {
        $response = $this->putJson(route($this->profileUpdateRoute), $this->getUserProfileUpdateData());

        $response->assertUnauthorized();
    }

    public function testUserCannotUpdateProfileInformationWithoutFirstName()
    {
        $this->testUserProfileInformationUpdateWithInvalidData('first_name', '');
    }

    public function testUserCannotUpdateProfileInformationWithoutLastName()
    {
        $this->testUserProfileInformationUpdateWithInvalidData('last_name', '');
    }

    public function testUserCannotUpdateProfileInformationWithoutEmail()
    {
        $this->testUserProfileInformationUpdateWithInvalidData('email', '');
    }

    public function testUserCannotUpdateProfileInformationWithInvalidEmail()
    {
        $this->testUserProfileInformationUpdateWithInvalidData('email', 'invalid_email');
    }

    public function testUserCannotUpdateProfileInformationWithTakenEmail()
    {
        $differentUser = User::factory()->create();

        $this->testUserProfileInformationUpdateWithInvalidData('email', $differentUser->email);
    }

    public function testUserCanUpdatePasswordWithCorrectData()
    {
        $response = $this->actingAs($this->user)
            ->putJson(route($this->passwordUpdateRoute), $this->getPasswordUpdateData());

        $response->assertOk();
        $this->assertTrue(Hash::check($this->getPasswordUpdateData()['password'], $this->user->password));
    }

    public function testUserCannotUpdatePasswordWhenUnauthenticated()
    {
        $response = $this->putJson(route($this->passwordUpdateRoute), $this->getPasswordUpdateData());

        $response->assertUnauthorized();
    }

    public function testUserCannotUpdatePasswordWithoutProvidingCurrentPassword()
    {
        $this->testPasswordUpdateWithInvalidData('current_password', '');
    }

    public function testUserCannotUpdatePasswordWithoutProvidingValidCurrentPassword()
    {
        $this->testPasswordUpdateWithInvalidData('current_password', 'invalid_password');
    }

    public function testUserCannotUpdatePasswordWithoutProvidingNewPassword()
    {
        $this->testPasswordUpdateWithInvalidData('password', '');
    }

    public function testUserCannotUpdatePasswordWhenNewPasswordIsShorterThanEightCharacters()
    {
        $this->testPasswordUpdateWithInvalidData('password', 'pass');
    }

    public function testUserCannotUpdatePasswordWithoutPasswordConfirmation()
    {
        $this->testPasswordUpdateWithInvalidData('password_confirmation', '', 'password');
    }

    public function testUserCannotUpdatePasswordWithoutValidPasswordConfirmation()
    {
        $this->testPasswordUpdateWithInvalidData('password_confirmation', 'newPass', 'password');
    }

    private function testUserProfileInformationUpdateWithInvalidData(string $invalidFieldName,
                                                                     string $invalidValue,
                                                                     string $fieldValidationError = null)
    {
        $response = $this->actingAs($this->user)
            ->putJson(route($this->profileUpdateRoute), array_replace($this->getUserProfileUpdateData(), [
                $invalidFieldName => $invalidValue
            ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors($fieldValidationError ?? $invalidFieldName);
    }

    private function getUserProfileUpdateData(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
        ];
    }

    private function testPasswordUpdateWithInvalidData(string $invalidFieldName,
                                                       string $invalidValue,
                                                       string $fieldValidationError = null)
    {
        $response = $this->actingAs($this->user)
            ->putJson(route($this->passwordUpdateRoute), array_replace($this->getPasswordUpdateData(), [
                $invalidFieldName => $invalidValue
            ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors($fieldValidationError ?? $invalidFieldName);
    }

    private function getPasswordUpdateData(): array
    {
        return [
            'current_password' => 'ChatterPass123',
            'password' => 'newPassword',
            'password_confirmation' => 'newPassword',
        ];
    }
}
