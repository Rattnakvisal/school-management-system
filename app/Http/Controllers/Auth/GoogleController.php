<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Services\AuthService;

class GoogleController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    private function googleDriver(Request $request)
    {
        $callbackUrl = $request->getSchemeAndHttpHost() . route('google.callback', [], false);

        return Socialite::driver('google')
            ->setHttpClient(new GuzzleClient([
                'verify' => (bool) config('services.google.verify_ssl', true),
            ]))
            ->redirectUrl($callbackUrl)
            ->stateless();
    }

    public function authProviderRedirect(Request $request)
    {
        return $this->googleDriver($request)->redirect();
    }

    public function socialAuthentication(Request $request)
    {
        try {
            $socialUser = $this->googleDriver($request)->user();

            $email = $socialUser->getEmail();
            if (!$email) {
                return redirect()->route('login')
                    ->with('error', 'Google account has no email.');
            }

            // Delegate creation/lookup to AuthService (keeps logic centralized)
            $user = $this->authService->handleGoogleUser($socialUser);

            // Ensure any Google-authenticated account is treated as a student
            if ($user->role !== 'student') {
                $user->update(['role' => 'student']);
            }

            // Make sure the provider fields are up-to-date
            $user->forceFill([
                'google_id' => $socialUser->getId(),
                'provider'  => 'google',
                'avatar'    => $socialUser->getAvatar(),
            ])->save();

            // Ensure user is logged in on the web guard before redirecting.
            Auth::guard('web')->login($user, true);
            $request->session()->regenerate();

            // Force post-Google destination to student dashboard.
            return redirect()->route('student.dashboard');
        } catch (\Throwable $e) {
            Log::error('Google login failed: ' . $e->getMessage(), ['exception' => $e]);

            $message = config('app.debug') ? 'Google login failed: ' . $e->getMessage() : 'Google login failed. Please try again.';

            return redirect()->route('login')
                ->with('error', $message);
        }
    }
}
