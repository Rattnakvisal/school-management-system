<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Support\StaffPermissions;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        $role = strtolower(trim((string) ($user->role ?? '')));
        if (!in_array($role, ['admin', 'staff'], true)) {
            $hasAdmin = User::query()
                ->whereRaw('LOWER(TRIM(role)) = ?', ['admin'])
                ->exists();

            // Local bootstrap fallback: if no admin exists, promote current user.
            if (!$hasAdmin) {
                $user->forceFill(['role' => 'admin'])->save();
                $role = 'admin';
            }
        }

        if (!in_array($role, ['admin', 'staff'], true)) {
            $targetRoute = match ($role) {
                'teacher' => 'teacher.dashboard',
                'student' => 'student.dashboard',
                default => 'home',
            };

            return redirect()
                ->route($targetRoute)
                ->with('error', 'Admin or staff access is required to open that page.');
        }

        if ($role === 'staff' && !StaffPermissions::canAccessRoute($user, $request->route()?->getName())) {
            return redirect()
                ->route(StaffPermissions::firstRouteName($user))
                ->with('error', 'Your staff account is not assigned to that page.');
        }

        return $next($request);
    }
}
