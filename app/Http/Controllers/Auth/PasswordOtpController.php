<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordOtpController extends Controller
{
    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'No account found for that email.']);
        }

        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $token = hash('sha256', Str::random(60));
        $expires = now()->addMinutes(15);

        DB::table('password_otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp'        => (string) $otp,
                'token'      => $token,
                'expires_at' => $expires,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $exposeOtp = config('app.debug') || app()->environment(['local', 'testing']);

        try {
            Mail::to($email)->send(new PasswordOtpMail($otp));
            Log::info('Password OTP sent', ['email' => $email]);

            $redirect = redirect()->route('password.verify')
                ->with('email', $email)
                ->with('status', 'OTP sent to your email.');

            if ($exposeOtp) $redirect = $redirect->with('debug_otp', (string) $otp);
            return $redirect;
        } catch (\Throwable $e) {

            try {
                Mail::mailer('log')->to($email)->send(new PasswordOtpMail($otp));
                Log::warning('Password OTP mailer failed, logged to log-mailer', [
                    'email' => $email,
                    'error' => $e->getMessage()
                ]);

                $redirect = redirect()->route('password.verify')
                    ->with('email', $email)
                    ->with('status', 'OTP generated. Email delivery failed, check app log.');

                if ($exposeOtp) $redirect = $redirect->with('debug_otp', (string) $otp);
                return $redirect;
            } catch (\Throwable $_) {
                // ignore
            }

            Log::error('Password OTP Mail Error', ['email' => $email, 'error' => $e->getMessage()]);

            $redirect = redirect()->route('password.verify')
                ->with('email', $email)
                ->with('status', 'OTP generated but email delivery failed. Check logs.');

            if ($exposeOtp) $redirect = $redirect->with('debug_otp', (string) $otp);
            return $redirect;
        }
    }

    public function verifyForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $row = DB::table('password_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$row) {
            return back()->withErrors(['otp' => 'Invalid OTP code.'])->withInput();
        }

        if (Carbon::parse($row->expires_at)->isPast()) {
            return back()->withErrors(['otp' => 'OTP expired. Please request again.']);
        }

        return redirect()->route('password.reset', ['token' => $row->token])
            ->with('email', $request->email);
    }

    public function resetForm(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $row = DB::table('password_otps')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$row) {
            return back()->withErrors(['email' => 'Invalid reset session.']);
        }

        if (Carbon::parse($row->expires_at)->isPast()) {
            return back()->withErrors(['email' => 'Reset session expired.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_otps')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Password updated successfully. Please login.');
    }
}
