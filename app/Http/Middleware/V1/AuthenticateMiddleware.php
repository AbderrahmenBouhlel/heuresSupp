<?php

namespace App\Http\Middleware\V1;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;


class AuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token ){
            return response()->json(['message' => 'Unauthorized - No token provided'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken){
            throw new AuthenticationException('Invalid token provided.');
        }

        // Check if token is expired
        if ($this->tokenExpired($accessToken)) {
            $accessToken->delete(); // Optional: delete the expired token from the database
            throw new AuthenticationException('Token has expired.');
        }

        // Authenticate the user
        $request->setUserResolver(function () use ($accessToken) {
            $user = $accessToken->tokenable;

            if (method_exists($user , 'withAccessToken')){
                $user->withAccessToken($accessToken);
            }
            return $user;
        });


        return $next($request);
    }


    protected function tokenExpired(PersonalAccessToken $token): bool
    {
        // Assuming the token has an 'expires_at' attribute
        return $token->expires_at && $token->expires_at->isPast();
    }
}
