<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveApiOrganization
{
    public function handle(Request $request, Closure $next): Response
    {
        $organizationId = $request->header('X-Organization-Id');

        if (! $organizationId || ! is_numeric($organizationId)) {
            return response()->json(['message' => 'X-Organization-Id header is required.'], 422);
        }

        $user = $request->user();

        if ($user->is_platform_admin || $user->organizations()->where('organizations.id', $organizationId)->exists()) {
            $request->attributes->set('api_organization_id', (int) $organizationId);

            return $next($request);
        }

        return response()->json(['message' => 'You do not have access to this organization.'], 403);
    }
}
