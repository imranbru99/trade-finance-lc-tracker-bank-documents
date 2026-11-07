<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->bearerToken();

        if (! $header) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $hash = hash('sha256', $header);

        $user = User::query()->where('api_token', $hash)->first();

        if (! $user) {
            return response()->json(['message' => 'Invalid API token.'], 401);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
