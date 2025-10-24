<?php

namespace App\Http\Middleware\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {
        $user = $request->user();
        if (!$user || !in_array($user->role->value, $roles)) {
            // not allowed
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}

//That last parameter (...$roles) is PHP’s spread operator.
//It means “accept any number of extra arguments as an array.”
