<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminStaffController extends Controller
{
    private const ROLES = ['admin', 'staff'];
    private const EDIT_ROLES = ['admin', 'staff', 'teacher', 'student'];

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $accounts = $this->filteredQuery($filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $baseStatsQuery = User::query()->whereIn('role', self::ROLES);
        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'admins' => (clone $baseStatsQuery)->where('role', 'admin')->count(),
            'staff' => (clone $baseStatsQuery)->where('role', 'staff')->count(),
            'active' => $filters['hasStatusColumn']
                ? (clone $baseStatsQuery)->where('is_active', true)->count()
                : (clone $baseStatsQuery)->count(),
            'inactive' => $filters['hasStatusColumn']
                ? (clone $baseStatsQuery)->where('is_active', false)->count()
                : 0,
        ];

        return view('admin.admin-staff', [
            'accounts' => $accounts,
            'search' => $filters['search'],
            'role' => $filters['role'],
            'status' => $filters['status'],
            'stats' => $stats,
            'roles' => self::ROLES,
            'editRoles' => self::EDIT_ROLES,
            'hasStatusColumn' => $filters['hasStatusColumn'],
            'hasPhoneColumn' => $filters['hasPhoneColumn'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules($request));
        $avatarResult = $this->resolveAvatarUpload($request);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'avatar' => $avatarResult['path'],
            'provider' => null,
            'google_id' => null,
        ];

        if ($this->hasPhoneColumn()) {
            $payload['phone_number'] = $validated['phone_number'] ?? null;
        }

        if ($this->hasStatusColumn()) {
            $payload['is_active'] = $request->boolean('is_active', true);
        }

        User::query()->create($payload);

        $redirect = redirect()
            ->route('admin.admin-staff.index')
            ->with('success', ucfirst($validated['role']) . ' account created successfully.');

        if ($avatarResult['warning']) {
            $redirect->with('warning', $avatarResult['warning']);
        }

        return $redirect;
    }

    public function update(Request $request, User $account)
    {
        $account = $this->adminStaffOrFail($account);
        $validated = $request->validate($this->rules($request, $account));
        $avatarResult = $this->resolveAvatarUpload($request, $account);

        if ($this->isSelf($account) && !in_array((string) $validated['role'], self::ROLES, true)) {
            return redirect()
                ->route('admin.admin-staff.index')
                ->with('error', 'You cannot change your own account to student or teacher while signed in.');
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($this->hasPhoneColumn()) {
            $payload['phone_number'] = $validated['phone_number'] ?? null;
        }

        if ($avatarResult['path'] !== $account->avatar) {
            $payload['avatar'] = $avatarResult['path'];
        }

        if ($request->filled('password')) {
            $payload['password'] = $validated['password'];
        }

        if ($this->hasStatusColumn()) {
            $payload['is_active'] = $this->isSelf($account)
                ? true
                : $request->boolean('is_active');
        }

        $account->update($payload);

        $redirect = redirect()
            ->route('admin.admin-staff.index')
            ->with('success', 'Account updated successfully.');

        if ($avatarResult['warning']) {
            $redirect->with('warning', $avatarResult['warning']);
        }

        return $redirect;
    }

    public function toggleStatus(User $account)
    {
        $account = $this->adminStaffOrFail($account);

        if ($this->isSelf($account)) {
            return redirect()
                ->route('admin.admin-staff.index')
                ->with('error', 'You cannot change your own account status.');
        }

        if (!$this->hasStatusColumn()) {
            return redirect()
                ->route('admin.admin-staff.index')
                ->with('error', 'Account status column is missing. Please run migrations.');
        }

        $account->is_active = !$account->is_active;
        $account->save();

        return redirect()
            ->route('admin.admin-staff.index')
            ->with('success', 'Account status updated to ' . ($account->is_active ? 'active' : 'inactive') . '.');
    }

    public function destroy(User $account)
    {
        $account = $this->adminStaffOrFail($account);

        if ($this->isSelf($account)) {
            return redirect()
                ->route('admin.admin-staff.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $this->deleteStoredAvatar($account->avatar);
        $account->delete();

        return redirect()
            ->route('admin.admin-staff.index')
            ->with('success', 'Account deleted successfully.');
    }

    private function rules(Request $request, ?User $account = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email' . ($account ? ',' . $account->id : '')],
            'password' => [$account ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in($account ? self::EDIT_ROLES : self::ROLES)],
            'is_active' => ['nullable', 'boolean'],
        ];

        if ($this->hasPhoneColumn()) {
            $rules['phone_number'] = ['nullable', 'string', 'max:30'];
        }

        return $rules;
    }

    private function filters(Request $request): array
    {
        $role = strtolower((string) $request->query('role', 'all'));
        $status = strtolower((string) $request->query('status', 'all'));

        return [
            'search' => trim((string) $request->query('q', '')),
            'role' => in_array($role, ['all', ...self::ROLES], true) ? $role : 'all',
            'status' => in_array($status, ['all', 'active', 'inactive'], true) ? $status : 'all',
            'hasStatusColumn' => $this->hasStatusColumn(),
            'hasPhoneColumn' => $this->hasPhoneColumn(),
        ];
    }

    private function filteredQuery(array $filters): Builder
    {
        $query = User::query()
            ->whereIn('role', self::ROLES)
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $search = $filters['search'];

                $query->where(function ($inner) use ($search, $filters) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');

                    if ($filters['hasPhoneColumn']) {
                        $inner->orWhere('phone_number', 'like', '%' . $search . '%');
                    }
                });
            });

        if (in_array($filters['role'], self::ROLES, true)) {
            $query->where('role', $filters['role']);
        }

        if ($filters['hasStatusColumn'] && in_array($filters['status'], ['active', 'inactive'], true)) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        return $query;
    }

    private function adminStaffOrFail(User $account): User
    {
        abort_unless(in_array((string) $account->role, self::ROLES, true), 404);

        return $account;
    }

    private function isSelf(User $account): bool
    {
        return (int) auth()->id() === (int) $account->id;
    }

    private function hasStatusColumn(): bool
    {
        return Schema::hasColumn('users', 'is_active');
    }

    private function hasPhoneColumn(): bool
    {
        return Schema::hasColumn('users', 'phone_number');
    }

    private function uploadAvatarImage(Request $request, ?User $account = null): ?string
    {
        if (!$request->hasFile('avatar_image')) {
            return $account?->avatar;
        }

        $path = $request->file('avatar_image')->store('avatars/admin-staff', 'public');

        if ($account) {
            $this->deleteStoredAvatar($account->avatar);
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

    private function resolveAvatarUpload(Request $request, ?User $account = null): array
    {
        $file = $request->file('avatar_image');
        $current = $account?->avatar;

        if (!$file) {
            return ['path' => $current, 'warning' => null];
        }

        if (!$file->isValid()) {
            return [
                'path' => $current,
                'warning' => $account
                    ? 'Avatar upload failed: ' . $this->uploadErrorMessage($file->getError()) . ' Account was updated without changing avatar.'
                    : 'Avatar upload failed: ' . $this->uploadErrorMessage($file->getError()) . ' Account was created without avatar.',
            ];
        }

        $request->validate([
            'avatar_image' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        return [
            'path' => $this->uploadAvatarImage($request, $account),
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
