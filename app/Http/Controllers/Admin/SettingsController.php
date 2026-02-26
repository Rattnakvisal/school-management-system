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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'avatar' => ['nullable', 'file', 'image', 'max:4096'],
        ]);

        $admin->name = (string) $validated['name'];
        $admin->email = (string) $validated['email'];

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars/admins', 'public');
            if ($avatarPath !== false) {
                $admin->avatar = $avatarPath;
            }
        }

        $admin->save();

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Profile updated successfully.');
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
