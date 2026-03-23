<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Services\AuthService;
use App\Services\LoginOtpService;

class GoogleController extends Controller
{
    private AuthService $authService;
    private LoginOtpService $loginOtpService;

    public function __construct(AuthService $authService, LoginOtpService $loginOtpService)
    {
        $this->authService = $authService;
        $this->loginOtpService = $loginOtpService;
    }

    private function googleDriver(Request $request)
    {
        $callbackUrl = $request->getSchemeAndHttpHost() . route('google.callback', [], false);
        $verifyOption = (bool) config('services.google.verify_ssl', true);
        $caBundle = trim((string) config('services.google.ca_bundle', ''));

        if ($caBundle !== '' && is_file($caBundle)) {
            $verifyOption = $caBundle;
        }

        return Socialite::driver('google')
            ->setHttpClient(new GuzzleClient([
                'verify' => $verifyOption,
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

            // Ensure user is logged in on the web guard before redirecting.
            Auth::guard('web')->login($user, true);
            $request->session()->regenerate();

            if (isset($user->is_active) && !$user->is_active) {
                Auth::guard('web')->logout();

                return redirect()->route('login')
                    ->with('error', 'Your account is inactive. Please contact administrator.');
            }

            if ($this->loginOtpService->requiresOtp($user)) {
                $challenge = $this->loginOtpService->issueAndSend($user);
                if (!$challenge['ok']) {
                    Auth::guard('web')->logout();

                    return redirect()->route('login')
                        ->with('error', (string) ($challenge['message'] ?? 'Unable to send OTP.'));
                }

                Auth::guard('web')->logout();
                $request->session()->put([
                    'auth.login_otp.user_id' => (int) $user->id,
                    'auth.login_otp.remember' => true,
                    'auth.login_otp.last_sent_at' => now()->toIso8601String(),
                ]);
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login.otp.form')
                    ->with('success', 'OTP sent to your Telegram.');
            }

            // Redirect based on stored role (admin, teacher, student)
            return $this->authService->redirectByRole($user->role);
        } catch (\Throwable $e) {
            Log::error('Google login failed: ' . $e->getMessage(), ['exception' => $e]);

            $message = config('app.debug') ? 'Google login failed: ' . $e->getMessage() : 'Google login failed. Please try again.';

            return redirect()->route('login')
                ->with('error', $message);
        }
    }
}
