<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\Schema;

class AuthService
{
    // ==========================
    // REGISTER
    // ==========================
    public function register(array $data): User
    {
        $user = new User([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'email_verified_at' => $data['email_verified_at'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? 'student',
            'is_active' => $data['is_active'] ?? true,
        ]);
        $user->save();

        return $user;
    }

    // ==========================
    // LOGIN
    // ==========================
    public function loginWithIdentifier(string $identifier, string $password, bool $remember = false): User|false
    {
        $identifier = trim($identifier);
        if ($identifier === '' || $password === '') {
            return false;
        }

        $userId = null;

        $user = User::query()
            ->where(function ($query) use ($identifier) {
                $query->where('email', $identifier)
                    ->orWhere('phone_number', $identifier);
            })
            ->first(['id', 'password']);

        if ($user) {
            $userId = (int) $user->id;
        }

        if ($userId === null) {
            $targetTokens = $this->phoneLookupTokens($identifier);
            if ($targetTokens !== []) {
                $users = User::query()
                    ->whereNotNull('phone_number')
                    ->get(['id', 'phone_number']);

                foreach ($users as $candidate) {
                    if ($this->phoneNumbersMatch($targetTokens, (string) ($candidate->phone_number ?? ''))) {
                        $userId = (int) $candidate->id;
                        break;
                    }
                }
            }
        }

        if ($userId === null || $userId <= 0) {
            return false;
        }

        $user = User::query()->find($userId);
        if (!$user || !Hash::check($password, (string) $user->password)) {
            return false;
        }

        Auth::guard('web')->login($user, $remember);

        // Security best practice after login
        request()->session()->regenerate();

        return $user;
    }

    // ==========================
    // GOOGLE LOGIN
    // ==========================
    public function handleGoogleUser($googleUser): User
    {
        $gIdRaw  = method_exists($googleUser, 'getId') ? $googleUser->getId() : ($googleUser->id ?? null);
        $gEmailRaw  = method_exists($googleUser, 'getEmail') ? $googleUser->getEmail() : ($googleUser->email ?? null);
        $gName   = method_exists($googleUser, 'getName') ? $googleUser->getName() : ($googleUser->name ?? null);
        $gAvatar = method_exists($googleUser, 'getAvatar') ? $googleUser->getAvatar() : ($googleUser->avatar ?? null);
        $avatarForStorage = $this->normalizeGoogleAvatar($gAvatar);
        $gId = trim((string) ($gIdRaw ?? ''));
        $gId = $gId !== '' ? $gId : null;
        $gEmail = trim((string) ($gEmailRaw ?? ''));
        $normalizedEmail = mb_strtolower($gEmail);

        if ($normalizedEmail === '') {
            throw new \RuntimeException('Google account did not provide an email address.');
        }

        return DB::transaction(function () use ($gId, $gEmail, $normalizedEmail, $gName, $avatarForStorage) {
            $userByGoogle = null;
            if ($gId !== null) {
                $userByGoogle = User::query()
                    ->where('google_id', $gId)
                    ->lockForUpdate()
                    ->first();
            }

            $userByEmail = User::query()
                ->whereRaw('LOWER(TRIM(email)) = ?', [$normalizedEmail])
                ->lockForUpdate()
                ->first();

            if (
                $userByGoogle !== null
                && $userByEmail !== null
                && (int) $userByGoogle->id !== (int) $userByEmail->id
            ) {
                throw new \RuntimeException(
                    'This Google account is linked to another user. Please contact administrator.'
                );
            }

            $user = $userByGoogle ?? $userByEmail;

            // Determine role for new accounts. Do NOT overwrite an existing user's role.
            $defaultRole = (string) config('services.google.default_role', config('auth.google_default_role', 'student'));
            $role = match (true) {
                str_ends_with($normalizedEmail, '@admin.school.com')   => 'admin',
                str_ends_with($normalizedEmail, '@teacher.school.com') => 'teacher',
                default => $defaultRole,
            };

            if (!$user) {
                $allowAutoCreate = (bool) config('services.google.allow_auto_create', false);
                if (!$allowAutoCreate) {
                    throw new \RuntimeException(
                        'No account found for this Google email. Please contact administrator.'
                    );
                }

                $user = new User([
                    'name'      => $gName ?: $normalizedEmail,
                    'email'     => $normalizedEmail,
                    'password'  => Hash::make(Str::random(32)), // random unusable password
                    'role'      => $role,
                    'google_id' => $gId,
                    'provider'  => 'google',
                    'avatar'    => $avatarForStorage,
                    'email_verified_at' => now(),
                ]);
                $user->save();
            } else {
                // Preserve existing role - only update other profile fields
                $updatedEmail = trim((string) ($user->email ?? ''));
                if ($updatedEmail === '') {
                    $updatedEmail = $normalizedEmail;
                }

                $user->forceFill([
                    'google_id' => $user->google_id ?: $gId,
                    'provider'  => 'google',
                    'avatar'    => $avatarForStorage ?: $user->avatar,
                    'name'      => $user->name ?: ($gName ?: $normalizedEmail),
                    'email'     => $updatedEmail,
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
        $role = strtolower(trim($role));

        return match ($role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'staff'   => redirect()->route('admin.dashboard'),
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
            $role = strtolower(trim((string) $user->role));

            $routeName = match ($role) {
                'admin' => 'admin.dashboard',
                'staff' => 'admin.dashboard',
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
            $created = new Notification($data);
            $created->save();
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

    private function normalizeGoogleAvatar(mixed $avatar): ?string
    {
        $value = trim((string) ($avatar ?? ''));
        if ($value === '') {
            return null;
        }

        $maxLength = $this->resolveAvatarColumnMaxLength();
        if ($maxLength !== null && mb_strlen($value) > $maxLength) {
            return null;
        }

        return $value;
    }

    private function resolveAvatarColumnMaxLength(): ?int
    {
        try {
            if (!Schema::hasColumn('users', 'avatar')) {
                return null;
            }

            $columnType = strtolower((string) DB::connection()
                ->getSchemaBuilder()
                ->getColumnType('users', 'avatar'));

            return match ($columnType) {
                'char', 'string', 'varchar', 'tinytext' => 255,
                'text' => 65535,
                'mediumtext' => 16777215,
                'longtext' => PHP_INT_MAX,
                default => 255, // Safe default for unknown schema types.
            };
        } catch (\Throwable $e) {
            report($e);
            return 255; // Conservative fallback to prevent SQLSTATE[22001].
        }
    }

    private function normalizePhoneDigits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    private function phoneNumbersMatch(array $targetTokens, string $candidatePhone): bool
    {
        $candidateTokens = $this->phoneLookupTokens($candidatePhone);
        if ($candidateTokens === []) {
            return false;
        }

        return array_intersect($targetTokens, $candidateTokens) !== [];
    }

    private function phoneLookupTokens(string $value): array
    {
        $digits = $this->normalizePhoneDigits($value);
        if ($digits === '') {
            return [];
        }

        $tokens = [$digits];
        $khCore = $this->cambodiaCoreDigits($digits);
        if ($khCore !== null) {
            $tokens[] = '0' . $khCore;
            $tokens[] = '855' . $khCore;
        }

        return array_values(array_unique(array_filter($tokens, fn(string $token): bool => $token !== '')));
    }

    private function cambodiaCoreDigits(string $digits): ?string
    {
        if (str_starts_with($digits, '855')) {
            $core = ltrim(substr($digits, 3), '0');
            return $core !== '' ? $core : null;
        }

        if (str_starts_with($digits, '0')) {
            $core = ltrim(substr($digits, 1), '0');
            return $core !== '' ? $core : null;
        }

        return null;
    }
}
