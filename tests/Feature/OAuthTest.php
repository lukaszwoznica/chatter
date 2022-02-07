<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OAuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $authUser;
    private array $providers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authUser = User::factory()->create();
        $this->providers = config('services.oauth_providers');
    }

    public function testUserCanGetProviderRedirectUrl()
    {
        collect($this->providers)->each(function ($provider) {
            $response = $this->getJson(route('oauth.redirect', $provider));

            $response->assertOk()
                ->assertJson(fn(AssertableJson $json) => $json->has('target_url'));
        });
    }

    public function testProviderNameMustBeValidToGetRedirectUrl()
    {
        $response = $this->getJson(route('oauth.redirect', Str::random()));

        $response->assertNotFound();
    }

    public function testUserCannotGetProviderRedirectUrlWhenAuthenticated()
    {
        $response = $this->actingAs($this->authUser)
            ->getJson(route('oauth.redirect', head($this->providers)));

        $response->assertOk()
            ->assertJson([
                'error' => 'Already authenticated.'
            ]);
    }

    public function testUserAccountIsCreatedIfFirstLogin()
    {
        $randomOAuthProvider = Arr::random($this->providers);
        $this->prepareSocialiteMock();

        $response = $this->getJson(route('oauth.callback', $randomOAuthProvider));

        $response->assertOk();
        $this->assertDatabaseCount('users', 2);
        $this->assertNotNull($newUser = User::whereNull('password')->first());
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $newUser->id,
            'provider_name' => $randomOAuthProvider
        ]);
        $this->assertAuthenticatedAs($newUser);
    }

    public function testRegisteredUserCanLinkHisProfileWithSocialAccount()
    {
        $randomOAuthProvider = Arr::random($this->providers);
        $this->prepareSocialiteMock(
            userEmail: $this->authUser->email,
            userName: $this->authUser->full_name
        );

        $response = $this->getJson(route('oauth.callback', $randomOAuthProvider));

        $response->assertOk();
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $this->authUser->id,
            'provider_name' => $randomOAuthProvider
        ]);
        $this->assertAuthenticatedAs($this->authUser);
    }

    public function testUserCanLoginWithLinkedSocialAccount()
    {
        $this->authUser->socialAccounts()->create([
            'provider_name' => $randomOAuthProvider = Arr::random($this->providers),
            'provider_id' => $providerUserId = rand()
        ]);
        $this->prepareSocialiteMock($providerUserId, $this->authUser->email, $this->authUser->full_name);

        $response = $this->getJson(route('oauth.callback', $randomOAuthProvider));

        $response->assertOk();
        $this->assertCount(1, $this->authUser->socialAccounts);
        $this->assertAuthenticatedAs($this->authUser);
    }

    public function testUserCanHaveMultipleSocialAccountsLinked()
    {
        if (count($this->providers) < 2) {
            $this->markTestSkipped();
        }

        $this->authUser->socialAccounts()->create([
            'provider_name' => head($this->providers),
            'provider_id' => rand()
        ]);
        $this->prepareSocialiteMock(
            userEmail: $this->authUser->email,
            userName: $this->authUser->full_name
        );

        $response = $this->getJson(route('oauth.callback', last($this->providers)));

        $response->assertOk();
        $this->assertCount(2, $this->authUser->socialAccounts);
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $this->authUser->id,
            'provider_name' => last($this->providers)
        ]);
        $this->assertAuthenticatedAs($this->authUser);
    }

    public function testValidAuthorizationCodeIsRequiredToLoginWithSocialAccount()
    {
        $response = $this->getJson(route('oauth.callback', [
            Arr::random($this->providers),
            'code' => Str::random()
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('message', 'Invalid OAuth callback data provided.');
        $this->assertGuest();
    }

    private function prepareSocialiteMock($userId = null, $userEmail = null, $userName = null)
    {
        $socialiteUserMock = $this->mock(
            SocialiteUser::class,
            function (MockInterface $mock) use ($userId, $userEmail, $userName) {
                $mock->shouldReceive([
                    'getId' => $userId ?? rand(),
                    'getEmail' => $userEmail ?? $this->faker->unique()->safeEmail(),
                    'getName' => $userName ?? $this->faker->name()
                ]);
            }
        );

        Socialite::shouldReceive('driver->stateless->user')->andReturn($socialiteUserMock);
    }
}
