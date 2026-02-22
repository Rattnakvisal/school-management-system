<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $hasStatusColumn = $this->hasStatusColumn();

        $studentQuery = User::query()
            ->where('role', 'student')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            });

        if ($hasStatusColumn && in_array($status, ['active', 'inactive'], true)) {
            $studentQuery->where('is_active', $status === 'active');
        }

        $students = $studentQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $baseStatsQuery = User::query()->where('role', 'student');
        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'active' => $hasStatusColumn ? (clone $baseStatsQuery)->where('is_active', true)->count() : (clone $baseStatsQuery)->count(),
            'inactive' => $hasStatusColumn ? (clone $baseStatsQuery)->where('is_active', false)->count() : 0,
        ];

        return view('admin.students', [
            'students' => $students,
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
            'role' => 'student',
            'avatar' => $avatarResult['path'],
            'provider' => null,
            'google_id' => null,
        ];

        if ($this->hasStatusColumn()) {
            $payload['is_active'] = $request->boolean('is_active', true);
        }

        User::create($payload);

        $redirect = redirect()
            ->route('admin.students.index')
            ->with('success', 'Student account created successfully.');

        if ($avatarResult['warning']) {
            $redirect->with('warning', $avatarResult['warning']);
        }

        return $redirect;
    }

    public function update(Request $request, User $student)
    {
        $student = $this->studentOrFail($student);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $avatarResult = $this->resolveAvatarUpload($request, $student);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($avatarResult['path'] !== $student->avatar) {
            $payload['avatar'] = $avatarResult['path'];
        }

        if ($request->filled('password')) {
            $payload['password'] = $validated['password'];
        }

        if ($this->hasStatusColumn()) {
            $payload['is_active'] = $request->boolean('is_active');
        }

        $student->update($payload);

        $redirect = redirect()
            ->route('admin.students.index')
            ->with('success', 'Student updated successfully.');

        if ($avatarResult['warning']) {
            $redirect->with('warning', $avatarResult['warning']);
        }

        return $redirect;
    }

    public function toggleStatus(User $student)
    {
        $student = $this->studentOrFail($student);

        if (!$this->hasStatusColumn()) {
            return redirect()
                ->route('admin.students.index')
                ->with('error', 'Student status column is missing. Please run migrations.');
        }

        $student->is_active = !$student->is_active;
        $student->save();

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student status updated to ' . ($student->is_active ? 'active' : 'inactive') . '.');
    }

    public function destroy(User $student)
    {
        $student = $this->studentOrFail($student);

        $this->deleteStoredAvatar($student->avatar);
        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    private function studentOrFail(User $student): User
    {
        abort_unless($student->role === 'student', 404);
        return $student;
    }

    private function hasStatusColumn(): bool
    {
        return Schema::hasColumn('users', 'is_active');
    }

    private function uploadAvatarImage(Request $request, ?User $student = null): ?string
    {
        if (!$request->hasFile('avatar_image')) {
            return $student?->avatar;
        }

        $path = $request->file('avatar_image')->store('avatars/students', 'public');

        if ($student) {
            $this->deleteStoredAvatar($student->avatar);
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

    private function resolveAvatarUpload(Request $request, ?User $student = null): array
    {
        $file = $request->file('avatar_image');
        $current = $student?->avatar;

        if (!$file) {
            return ['path' => $current, 'warning' => null];
        }

        if (!$file->isValid()) {
            return [
                'path' => $current,
                'warning' => $student
                    ? 'Avatar upload failed: ' . $this->uploadErrorMessage($file->getError()) . ' Student was updated without changing avatar.'
                    : 'Avatar upload failed: ' . $this->uploadErrorMessage($file->getError()) . ' Student was created without avatar.',
            ];
        }

        $request->validate([
            'avatar_image' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB max
        ]);

        return [
            'path' => $this->uploadAvatarImage($request, $student),
            'warning' => null,
        ];
    }

    private function uploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE =>
            'Image is too large for current server upload limit (2MB max).',
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
