<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\ContactMessage;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user();

        $stats = [
            'students' => (int) User::query()->where('role', 'student')->count(),
            'teachers' => (int) User::query()->where('role', 'teacher')->count(),
            'classes' => (int) SchoolClass::query()->count(),
            'subjects' => (int) Subject::query()->count(),
            'classSlots' => (int) ClassStudyTime::query()->count(),
            'subjectSlots' => (int) SubjectStudyTime::query()->count(),
            'unreadMessages' => (int) ContactMessage::query()->where('is_read', false)->count(),
            'unreadNotifications' => (int) Notification::query()->where('is_read', false)->count(),
        ];

        return view('admin.settings', [
            'admin' => $admin,
            'stats' => $stats,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $admin = $request->user();

        $validated = $request->validateWithBag('profileUpdate', [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'file', 'image', 'max:4096'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $firstName = trim((string) ($validated['first_name'] ?? ''));
        $lastName = trim((string) ($validated['last_name'] ?? ''));

        $admin->name = trim($firstName . ' ' . $lastName);
        $admin->email = (string) $validated['email'];
        $admin->phone_number = trim((string) ($validated['phone_number'] ?? '')) ?: null;

        $currentAvatar = $admin->avatar;

        if ($request->boolean('remove_avatar')) {
            $this->deleteStoredAvatar($currentAvatar);
            $admin->avatar = null;
            $currentAvatar = null;
        }

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars/admins', 'public');
            if ($avatarPath !== false) {
                if ($currentAvatar && $currentAvatar !== $avatarPath) {
                    $this->deleteStoredAvatar($currentAvatar);
                }
                $admin->avatar = $avatarPath;
            }
        }

        $admin->save();

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Profile updated successfully.');
    }

    private function deleteStoredAvatar(?string $avatar): void
    {
        if (!$this->isLocalStorageAvatar($avatar)) {
            return;
        }

        $path = $this->normalizePublicPath((string) $avatar);
        if ($path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function isLocalStorageAvatar(?string $avatar): bool
    {
        if (!$avatar) {
            return false;
        }

        return !Str::startsWith($avatar, ['http://', 'https://', '//', 'data:image/']);
    }

    private function normalizePublicPath(string $path): string
    {
        $normalized = ltrim($path, '/');

        if (Str::startsWith($normalized, 'storage/')) {
            return Str::after($normalized, 'storage/');
        }

        if (Str::startsWith($normalized, 'public/')) {
            return Str::after($normalized, 'public/');
        }

        return $normalized;
    }

    public function updatePassword(Request $request)
    {
        $admin = $request->user();

        $validated = $request->validateWithBag('passwordUpdate', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin->password = (string) $validated['password'];
        $admin->save();

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Password updated successfully.');
    }
}
