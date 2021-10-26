<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateOAuthProvider
{
    public function handle(Request $request, Closure $next)
    {
        abort_if(!$this->validateProvider($request->route('provider')), Response::HTTP_NOT_FOUND);

        return $next($request);
    }

    private function validateProvider(string $provider): bool
    {
        return in_array($provider, config('services.oauth_providers'));
    }
}
