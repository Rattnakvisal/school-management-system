<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $hasStatusColumn = $this->hasStatusColumn();

        $teacherQuery = User::query()
            ->where('role', 'teacher')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            });

        if ($hasStatusColumn && in_array($status, ['active', 'inactive'], true)) {
            $teacherQuery->where('is_active', $status === 'active');
        }

        $teachers = $teacherQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $baseStatsQuery = User::query()->where('role', 'teacher');
        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'active' => $hasStatusColumn ? (clone $baseStatsQuery)->where('is_active', true)->count() : (clone $baseStatsQuery)->count(),
            'inactive' => $hasStatusColumn ? (clone $baseStatsQuery)->where('is_active', false)->count() : 0,
        ];

        return view('admin.teachers', [
            'teachers' => $teachers,
            'search' => $search,
            'status' => in_array($status, ['all', 'active', 'inactive'], true) ? $status : 'all',
            'stats' => $stats,
            'hasStatusColumn' => $hasStatusColumn,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $avatarResult = $this->resolveAvatarUpload($request);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'teacher',
            'avatar' => $avatarResult['path'],
            'provider' => null,
            'google_id' => null,
        ];

        if ($this->hasStatusColumn()) {
            $payload['is_active'] = $request->boolean('is_active', true);
        }

        User::create($payload);

        $redirect = redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher account created successfully.');

        if ($avatarResult['warning']) {
            $redirect->with('warning', $avatarResult['warning']);
        }

        return $redirect;
    }

    public function update(Request $request, User $teacher)
    {
        $teacher = $this->teacherOrFail($teacher);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $teacher->id],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $avatarResult = $this->resolveAvatarUpload($request, $teacher);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($avatarResult['path'] !== $teacher->avatar) {
            $payload['avatar'] = $avatarResult['path'];
        }

        if ($request->filled('password')) {
            $payload['password'] = $validated['password'];
        }

        if ($this->hasStatusColumn()) {
            $payload['is_active'] = $request->boolean('is_active');
        }

        $teacher->update($payload);

        $redirect = redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully.');

        if ($avatarResult['warning']) {
            $redirect->with('warning', $avatarResult['warning']);
        }

        return $redirect;
    }

    public function toggleStatus(User $teacher)
    {
        $teacher = $this->teacherOrFail($teacher);

        if (!$this->hasStatusColumn()) {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', 'Teacher status column is missing. Please run migrations.');
        }

        $teacher->is_active = !$teacher->is_active;
        $teacher->save();

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher status updated to ' . ($teacher->is_active ? 'active' : 'inactive') . '.');
    }

    public function destroy(User $teacher)
    {
        $teacher = $this->teacherOrFail($teacher);

        $this->deleteStoredAvatar($teacher->avatar);
        $teacher->delete();

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }

    private function teacherOrFail(User $teacher): User
    {
        abort_unless($teacher->role === 'teacher', 404);
        return $teacher;
    }

    private function hasStatusColumn(): bool
    {
        return Schema::hasColumn('users', 'is_active');
    }

    private function uploadAvatarImage(Request $request, ?User $teacher = null): ?string
    {
        if (!$request->hasFile('avatar_image')) {
            return $teacher?->avatar;
        }

        $path = $request->file('avatar_image')->store('avatars/teachers', 'public');

        if ($teacher) {
            $this->deleteStoredAvatar($teacher->avatar);
        }

        return $path;
    }

    private function deleteStoredAvatar(?string $avatar): void
    {
        if (!$this->isLocalStorageAvatar($avatar)) {
            return;
        }

        $path = $this->normalizePublicPath($avatar);
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

    private function resolveAvatarUpload(Request $request, ?User $teacher = null): array
    {
        $file = $request->file('avatar_image');
        $current = $teacher?->avatar;

        if (!$file) {
            return ['path' => $current, 'warning' => null];
        }

        if (!$file->isValid()) {
            return [
                'path' => $current,
                'warning' => $teacher
                    ? 'Avatar upload failed: ' . $this->uploadErrorMessage($file->getError()) . ' Teacher was updated without changing avatar.'
                    : 'Avatar upload failed: ' . $this->uploadErrorMessage($file->getError()) . ' Teacher was created without avatar.',
            ];
        }

        $request->validate([
            'avatar_image' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB max
        ]);

        return [
            'path' => $this->uploadAvatarImage($request, $teacher),
            'warning' => null,
        ];
    }

    private function uploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE =>
            'Image is too large for current server upload limit (5MB max).',
            UPLOAD_ERR_PARTIAL =>
            'Upload was interrupted. Please try again.',
            UPLOAD_ERR_NO_TMP_DIR =>
            'Server temporary folder is missing.',
            UPLOAD_ERR_CANT_WRITE =>
            'Server failed to write uploaded file.',
            UPLOAD_ERR_EXTENSION =>
            'A PHP extension blocked the upload.',
            default =>
            'Unknown upload error.',
        };
    }
}
