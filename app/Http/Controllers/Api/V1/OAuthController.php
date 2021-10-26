<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;


class OAuthController extends Controller
{
    public function getProviderRedirectOrl(string $provider)
    {
        return response()->json([
            'target_url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl()
        ]);
    }

    public function handleCallback(string $provider, UserService $userService)
    {
        try {
            $providerUser = Socialite::driver($provider)->stateless()->user();
            $socialAccount = $userService->findSocialAccount($providerUser, $provider);

            if ($socialAccount) {
                $appUser = $socialAccount->user;
            } else {
                $appUser = $userService->getOrCreateUserFromOAuthProviderData($providerUser);
                $userService->createSocialAccountForUser($appUser, $providerUser, $provider);
            }

            auth()->login($appUser);

            return response()->json([
                'message' => 'OAuth authentication success.'
            ]);
        } catch (ClientException $exception) {
            return response()->json([
                'message' => 'Invalid OAuth callback data provided.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'OAuth authentication failed.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
