<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $last = (int) $request->session()->get('last_activity', 0);
        $lifetimeMinutes = (int) config('session.lifetime', 120);

        if ($last > 0) {
            $elapsed = time() - $last;
            $max = max(1, $lifetimeMinutes) * 60;
            if ($elapsed > $max) {
                try {
                    Auth::guard('web')->logout();
                } catch (\Throwable $e) {
                    report($e);
                }

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Your session expired. Please sign in again.'], 401);
                }

                return redirect()->route('login')->with('error', 'Your session expired. Please sign in again.');
            }
        }

        // Refresh last activity timestamp
        $request->session()->put('last_activity', time());

        return $next($request);
    }
}
