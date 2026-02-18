<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    // ==========================
    // REGISTER
    // ==========================
    public function register(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? 'student', // keep controlled in controller (recommended)
        ]);
    }

    // ==========================
    // LOGIN
    // ==========================
    public function login(array $credentials, bool $remember = false): User|false
    {
        if (!Auth::attempt($credentials, $remember)) {
            return false;
        }

        // Security best practice after login
        request()->session()->regenerate();

        return Auth::user();
    }

    // ==========================
    // GOOGLE LOGIN
    // ==========================
    public function handleGoogleUser($googleUser): User
    {
        $gId     = method_exists($googleUser, 'getId') ? $googleUser->getId() : ($googleUser->id ?? null);
        $gEmail  = method_exists($googleUser, 'getEmail') ? $googleUser->getEmail() : ($googleUser->email ?? null);
        $gName   = method_exists($googleUser, 'getName') ? $googleUser->getName() : ($googleUser->name ?? null);
        $gAvatar = method_exists($googleUser, 'getAvatar') ? $googleUser->getAvatar() : ($googleUser->avatar ?? null);

        if (empty($gEmail)) {
            throw new \RuntimeException('Google account did not provide an email address.');
        }

        return DB::transaction(function () use ($gId, $gEmail, $gName, $gAvatar) {
            // Find by google_id OR email
            $user = User::where('google_id', $gId)
                ->orWhere('email', $gEmail)
                ->lockForUpdate()
                ->first();

            if (!$user) {
                $user = User::create([
                    'name'      => $gName ?: $gEmail,
                    'email'     => $gEmail,
                    'password'  => Hash::make(Str::random(32)), // random unusable password
                    'role'      => 'student',                   // IMPORTANT: default student
                    'google_id' => $gId,
                    'provider'  => 'google',
                    'avatar'    => $gAvatar,
                ]);
            } else {
                $user->forceFill([
                    'google_id' => $user->google_id ?: $gId,
                    'provider'  => 'google',
                    'avatar'    => $gAvatar ?: $user->avatar,
                    'name'      => $user->name ?: ($gName ?: $gEmail),
                ])->save();
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            return $user;
        });
    }

    // ==========================
    // ROLE REDIRECT
    // ==========================
    public function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            default   => redirect()->route('student.dashboard'),
        };
    }

    // ==========================
    // LOGOUT
    // ==========================
    public function logout(): void
    {
        Auth::logout();

        // Best practice on logout
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
