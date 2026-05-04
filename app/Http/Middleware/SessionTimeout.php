<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    /** Default timeout in seconds (30 minutes) */
    protected int $defaultTimeout = 1800;

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Determine timeout per role if needed in future (currently 30 minutes for students/teachers)
            $timeout = $this->defaultTimeout;

            $last = (int) $request->session()->get('last_activity', 0);
            $now = time();

            if ($last > 0 && ($now - $last) > $timeout) {
                // Expired: logout and redirect to login with message
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'You have been logged out due to inactivity.');
            }

            // Update last activity timestamp
            $request->session()->put('last_activity', $now);
        }

        return $next($request);
    }
}
