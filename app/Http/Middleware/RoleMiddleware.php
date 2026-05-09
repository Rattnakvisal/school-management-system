<?php

namespace App\Http\Middleware;

use App\Support\StaffPermissions;
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

        if ($userRole === 'staff') {
            $routeName = $request->route()?->getName();
            $firstRouteName = StaffPermissions::firstRouteName($user);

            if ($routeName === 'staff.dashboard' && StaffPermissions::isRestricted($user) && $firstRouteName !== 'staff.dashboard') {
                return redirect()->route($firstRouteName);
            }

            if (str_starts_with((string) $routeName, 'staff.') && !StaffPermissions::canAccessRoute($user, $routeName)) {
                return redirect()
                    ->route($firstRouteName)
                    ->with('error', 'Your staff account is not assigned to that page.');
            }
        }

        return $next($request);
    }
}
