<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegisterOtpMail;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function showLogin(Request $request)
    {
        // Ensure fresh CSRF token whenever auth page is loaded.
        $request->session()->regenerateToken();
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = $this->authService->login(
            ['email' => $validated['email'], 'password' => $validated['password']],
            $request->boolean('remember')
        );

        if (!$user) {
            return back()
                ->withErrors(['email' => 'Invalid credentials'])
                ->withInput($request->only('email'));
        }

        if (isset($user->is_active) && !$user->is_active) {
            $this->authService->logout();

            $hasPendingRegisterOtp = DB::table('register_otps')
                ->where('email', $user->email)
                ->exists();

            if ($hasPendingRegisterOtp) {
                return redirect()
                    ->route('register.verify')
                    ->with('email', $user->email)
                    ->with('status', 'Your account is pending email verification. Please enter your OTP.');
            }

            return back()
                ->withErrors(['email' => 'Your account is inactive. Please contact administrator.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return $this->authService->redirectByRole($user->role);
    }

    public function showRegister(Request $request)
    {
        // Ensure fresh CSRF token whenever auth page is loaded.
        $request->session()->regenerateToken();
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $validated['role'] = 'student';
        $validated['email_verified_at'] = null;
        $validated['is_active'] = false;

        $user = $this->authService->register($validated);

        $otpResult = $this->dispatchRegisterOtp($user);

        $redirect = redirect()
            ->route('register.verify')
            ->with('email', $user->email)
            ->with('status', $otpResult['status']);

        if (!empty($otpResult['debug_otp'])) {
            $redirect = $redirect->with('debug_otp', $otpResult['debug_otp']);
        }

        return $redirect;
    }

    public function showRegisterOtpForm()
    {
        return view('auth.verify-register-otp');
    }

    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $row = DB::table('register_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$row) {
            $alreadyVerified = User::where('email', $request->email)
                ->whereNotNull('email_verified_at')
                ->where('is_active', true)
                ->exists();

            if ($alreadyVerified) {
                return redirect()
                    ->route('login')
                    ->with('success', 'Account already verified. Please login.');
            }

            return back()->withErrors(['otp' => 'Invalid OTP code.'])->withInput();
        }

        if (Carbon::parse($row->expires_at)->isPast()) {
            return back()->withErrors(['otp' => 'OTP expired. Please request a new code.'])->withInput();
        }

        $user = User::where('id', $row->user_id)
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User account not found.'])->withInput();
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'is_active' => true,
        ])->save();

        DB::table('register_otps')->where('email', $request->email)->delete();

        try {
            $this->authService->createWelcomeNotification($user);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('login')
            ->with('success', 'Registration successful. Your email is verified. Please log in.');
    }

    public function resendRegisterOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => 'No account found for that email.']);
        }

        if (!empty($user->email_verified_at) && (bool) $user->is_active) {
            return redirect()
                ->route('login')
                ->with('success', 'Account already verified. Please login.');
        }

        $otpResult = $this->dispatchRegisterOtp($user);

        $redirect = redirect()
            ->route('register.verify')
            ->with('email', $user->email)
            ->with('status', $otpResult['status']);

        if (!empty($otpResult['debug_otp'])) {
            $redirect = $redirect->with('debug_otp', $otpResult['debug_otp']);
        }

        return $redirect;
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function dispatchRegisterOtp(User $user): array
    {
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $expires = now()->addMinutes(10);

        DB::table('register_otps')->updateOrInsert(
            ['email' => $user->email],
            [
                'user_id' => $user->id,
                'otp' => (string) $otp,
                'expires_at' => $expires,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $exposeOtp = config('app.debug') || app()->environment(['local', 'testing']);
        $status = 'OTP sent to your email.';

        try {
            Mail::to($user->email)->send(new RegisterOtpMail($otp));
            Log::info('Registration OTP sent', ['email' => $user->email]);
        } catch (\Throwable $e) {
            try {
                Mail::mailer('log')->to($user->email)->send(new RegisterOtpMail($otp));
                Log::warning('Registration OTP mailer failed, logged to log-mailer', [
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
                $status = 'OTP generated. Email delivery failed, check app log.';
            } catch (\Throwable $_) {
                // ignore
            }

            Log::error('Registration OTP Mail Error', ['email' => $user->email, 'error' => $e->getMessage()]);
            $status = 'OTP generated but email delivery failed. Check logs.';
        }

        return [
            'status' => $status,
            'debug_otp' => $exposeOtp ? (string) $otp : null,
        ];
    }
}
