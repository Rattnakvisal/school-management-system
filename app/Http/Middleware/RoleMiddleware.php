<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $normalizedAllowedRoles = array_values(array_filter(array_map(
            fn(string $role): string => strtolower(trim($role)),
            $roles
        )));
        $userRole = strtolower(trim((string) ($user?->role ?? '')));

        if (!$user || !in_array($userRole, $normalizedAllowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
