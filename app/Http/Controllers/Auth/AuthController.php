<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\LoginOtpService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private const OTP_SESSION_USER_KEY = 'auth.login_otp.user_id';
    private const OTP_SESSION_REMEMBER_KEY = 'auth.login_otp.remember';
    private const OTP_SESSION_LAST_SENT_AT_KEY = 'auth.login_otp.last_sent_at';

    public function __construct(
        protected AuthService $authService,
        protected LoginOtpService $loginOtpService
    ) {}

    public function showLogin(Request $request)
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (!$request->filled('login') && $request->filled('email')) {
            $request->merge([
                'login' => (string) $request->input('email'),
            ]);
        }

        $validated = $request->validate([
            'login'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = $this->authService->loginWithIdentifier(
            (string) $validated['login'],
            (string) $validated['password'],
            $request->boolean('remember')
        );

        if (!$user) {
            return back()
                ->withErrors(['login' => 'Invalid email/phone or password'])
                ->withInput($request->only('login'));
        }

        if (isset($user->is_active) && !$user->is_active) {
            $this->authService->logout();

            return back()
                ->withErrors(['login' => 'Your account is inactive. Please contact administrator.'])
                ->withInput($request->only('login'));
        }

        if ($this->loginOtpService->requiresOtp($user)) {
            $challenge = $this->loginOtpService->issueAndSend($user);
            if (!$challenge['ok']) {
                Auth::guard('web')->logout();

                return back()
                    ->withErrors(['login' => (string) ($challenge['message'] ?? 'Unable to send OTP.')])
                    ->withInput($request->only('login'));
            }

            Auth::guard('web')->logout();
            $request->session()->put([
                self::OTP_SESSION_USER_KEY => (int) $user->id,
                self::OTP_SESSION_REMEMBER_KEY => $request->boolean('remember'),
                self::OTP_SESSION_LAST_SENT_AT_KEY => now()->toIso8601String(),
            ]);
            $request->session()->regenerateToken();

            return redirect()
                ->route('login.otp.form')
                ->with('success', 'OTP sent to your Telegram.');
        }

        return $this->authService->redirectByRole($user->role);
    }

    public function showLoginOtpForm(Request $request)
    {
        $user = $this->pendingOtpUser($request);
        if (!$user) {
            return redirect()->route('login');
        }

        return view('auth.login-otp', [
            'user' => $user,
            'maskedPhone' => $this->maskPhone((string) ($user->phone_number ?? '')),
        ]);
    }

    public function verifyLoginOtp(Request $request)
    {
        $user = $this->pendingOtpUser($request);
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $valid = $this->loginOtpService->verify($user, (string) $validated['otp']);
        if (!$valid) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP. Please try again.',
            ]);
        }

        $remember = (bool) $request->session()->get(self::OTP_SESSION_REMEMBER_KEY, false);
        Auth::guard('web')->login($user, $remember);

        $request->session()->forget([
            self::OTP_SESSION_USER_KEY,
            self::OTP_SESSION_REMEMBER_KEY,
            self::OTP_SESSION_LAST_SENT_AT_KEY,
        ]);
        $request->session()->regenerate();

        return $this->authService->redirectByRole((string) $user->role);
    }

    public function resendLoginOtp(Request $request)
    {
        $user = $this->pendingOtpUser($request);
        if (!$user) {
            return redirect()->route('login');
        }

        $lastSentAt = (string) $request->session()->get(self::OTP_SESSION_LAST_SENT_AT_KEY, '');
        $throttleSeconds = $this->loginOtpService->resendThrottleSeconds();
        if ($lastSentAt !== '') {
            $lastSent = Carbon::parse($lastSentAt);
            if ($lastSent->addSeconds($throttleSeconds)->isFuture()) {
                return back()->with('warning', 'Please wait ' . $throttleSeconds . ' seconds before resending OTP.');
            }
        }

        $challenge = $this->loginOtpService->issueAndSend($user);
        if (!$challenge['ok']) {
            return back()->withErrors([
                'otp' => (string) ($challenge['message'] ?? 'Unable to resend OTP.'),
            ]);
        }

        $request->session()->put(self::OTP_SESSION_LAST_SENT_AT_KEY, now()->toIso8601String());

        return back()->with('success', 'A new OTP was sent to your Telegram.');
    }

    public function logout(Request $request)
    {
        $role = (string) ($request->user()?->role ?? '');

        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $role === 'teacher'
            ? redirect()->to('/')
            : redirect()->route('home');
    }

    private function pendingOtpUser(Request $request): ?User
    {
        $userId = (int) $request->session()->get(self::OTP_SESSION_USER_KEY, 0);
        if ($userId <= 0) {
            return null;
        }

        return User::query()->find($userId);
    }

    private function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return 'Not available';
        }

        $last4 = substr($digits, -4);
        return '******' . $last4;
    }
}
