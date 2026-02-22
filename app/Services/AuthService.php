<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Support\Facades\Route as RouteFacade;

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
            'email_verified_at' => $data['email_verified_at'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? 'student',
            'is_active' => $data['is_active'] ?? true,
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
            $user = User::where('google_id', $gId)
                ->orWhere('email', $gEmail)
                ->lockForUpdate()
                ->first();

            // Determine role for new accounts. Do NOT overwrite an existing user's role.
            $defaultRole = config('auth.google_default_role', 'student');
            $role = match (true) {
                str_ends_with($gEmail, '@admin.school.com')   => 'admin',
                str_ends_with($gEmail, '@teacher.school.com') => 'teacher',
                default => $defaultRole,
            };

            if (!$user) {
                $user = User::create([
                    'name'      => $gName ?: $gEmail,
                    'email'     => $gEmail,
                    'password'  => Hash::make(Str::random(32)), // random unusable password
                    'role'      => $role,
                    'google_id' => $gId,
                    'provider'  => 'google',
                    'avatar'    => $gAvatar,
                ]);
            } else {
                // Preserve existing role - only update other profile fields
                $user->forceFill([
                    'google_id' => $user->google_id ?: $gId,
                    'provider'  => 'google',
                    'avatar'    => $gAvatar ?: $user->avatar,
                    'name'      => $user->name ?: ($gName ?: $gEmail),
                ])->save();
            }
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

    public function createWelcomeNotification(User $user)
    {
        try {
            $routeName = match ($user->role) {
                'admin' => 'admin.dashboard',
                'teacher' => 'teacher.dashboard',
                default => 'student.dashboard',
            };

            $url = RouteFacade::has($routeName) ? route($routeName) : null;
        } catch (\Throwable $e) {
            report($e);
            $url = null;
        }

        $data = [
            'type'    => 'info',
            'title'   => 'New User Registered',
            'message' => $user->name . ' (' . $user->role . ') has registered successfully.',
            'url'     => $url,
            'is_read' => false,
        ];

        try {
            $created = Notification::create($data);
            if (!$created || !$created->id || (empty($created->title) && empty($created->message))) {
                // ensure timestamps
                $now = now();
                $insert = array_merge($data, ['created_at' => $now, 'updated_at' => $now]);
                DB::table('notifications')->insert($insert);
                return DB::table('notifications')->where('created_at', $now)->first();
            }

            return $created;
        } catch (\Throwable $e) {
            report($e);
            try {
                $now = now();
                $insert = array_merge($data, ['created_at' => $now, 'updated_at' => $now]);
                DB::table('notifications')->insert($insert);
                return DB::table('notifications')->where('created_at', $now)->first();
            } catch (\Throwable $e) {
                report($e);
                return null;
            }
        }
    }
}
