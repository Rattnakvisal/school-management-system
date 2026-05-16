<?php

namespace App\Support;

use App\Models\User;

class StaffPermissions
{
    public const OPTIONS = [
        'students' => [
            'label' => 'Students',
            'description' => 'Manage student accounts and records.',
            'route' => 'admin.students.index',
        ],
        'teachers' => [
            'label' => 'Teachers',
            'description' => 'Manage teacher accounts.',
            'route' => 'admin.teachers.index',
        ],
        'classes' => [
            'label' => 'Classes',
            'description' => 'Manage school classes.',
            'route' => 'admin.classes.index',
        ],
        'subjects' => [
            'label' => 'Subjects',
            'description' => 'Manage subjects and assignments.',
            'route' => 'admin.subjects.index',
        ],
        'schedule' => [
            'label' => 'Schedule',
            'description' => 'Manage class and subject study times.',
            'route' => 'admin.time-studies.index',
        ],
        'student_progress' => [
            'label' => 'Student Progress',
            'description' => 'Review student study progress.',
            'route' => 'admin.student-study.index',
        ],
        'finance' => [
            'label' => 'School Finance',
            'description' => 'Manage student payments and balances.',
            'route' => 'admin.finance.index',
        ],
        'messages' => [
            'label' => 'Messages',
            'description' => 'Manage contact messages.',
            'route' => 'admin.contacts.index',
        ],
        'student_attendance' => [
            'label' => 'Student Attendance',
            'description' => 'View student attendance records.',
            'route' => 'admin.attendance.index',
        ],
        'teacher_attendance' => [
            'label' => 'Teacher Attendance',
            'description' => 'Manage teacher attendance.',
            'route' => 'admin.attendance.teachers.index',
        ],
        'homepage' => [
            'label' => 'Homepage UI',
            'description' => 'Manage public homepage content.',
            'route' => 'admin.homepage.index',
        ],
        'settings' => [
            'label' => 'Settings',
            'description' => 'Manage system settings and configurations.',
            'route' => 'admin.settings.index',
        ],
    ];

    public static function keys(): array
    {
        return array_keys(self::OPTIONS);
    }

    public static function normalize(mixed $permissions): array
    {
        if (!is_array($permissions)) {
            return [];
        }

        return collect($permissions)
            ->map(fn($permission) => strtolower(trim((string) $permission)))
            ->filter(fn($permission) => array_key_exists($permission, self::OPTIONS))
            ->unique()
            ->values()
            ->all();
    }

    public static function isRestricted(?User $user): bool
    {
        return strtolower(trim((string) ($user?->role ?? ''))) === 'staff';
    }

    public static function permissionsFor(?User $user): array
    {
        if (strtolower(trim((string) ($user?->role ?? ''))) !== 'staff') {
            return self::keys();
        }

        $stored = $user?->getAttribute('staff_permissions');

        return $stored === null
            ? self::keys()
            : self::normalize($stored);
    }

    public static function can(?User $user, string $permission): bool
    {
        $permission = strtolower(trim($permission));

        if (!array_key_exists($permission, self::OPTIONS)) {
            return false;
        }

        return in_array($permission, self::permissionsFor($user), true);
    }

    public static function firstRouteName(?User $user): string
    {
        if (strtolower(trim((string) ($user?->role ?? ''))) === 'staff') {
            return 'staff.dashboard';
        }

        foreach (self::permissionsFor($user) as $permission) {
            $route = self::OPTIONS[$permission]['route'] ?? null;
            if ($route) {
                return $route;
            }
        }

        return 'staff.dashboard';
    }

    public static function permissionForRouteName(?string $routeName): ?string
    {
        $routeName = (string) $routeName;

        $prefixMap = [
            'admin.students.' => 'students',
            'admin.teachers.' => 'teachers',
            'admin.classes.' => 'classes',
            'admin.subjects.' => 'subjects',
            'admin.time-studies.' => 'schedule',
            'admin.student-study.' => 'student_progress',
            'admin.finance.' => 'finance',
            'admin.contacts.' => 'messages',
            'admin.attendance.teachers.' => 'teacher_attendance',
            'admin.homepage.' => 'homepage',
            'admin.settings.navbar-page.' => 'homepage',
            'admin.settings.home-page.' => 'homepage',
            'admin.settings.about-page.' => 'homepage',
            'admin.settings.program-page.' => 'homepage',
            'admin.settings.course-page.' => 'homepage',
            'admin.settings.faq-page.' => 'homepage',
            'admin.settings.contact-page.' => 'homepage',
            'admin.settings.footer-page.' => 'homepage',
            'admin.settings.index' => 'settings',
        ];

        foreach ($prefixMap as $prefix => $permission) {
            if (str_starts_with($routeName, $prefix)) {
                return $permission;
            }
        }

        return match ($routeName) {
            'admin.attendance.index' => 'student_attendance',
            default => null,
        };
    }

    public static function canAccessRoute(?User $user, ?string $routeName): bool
    {
        if (strtolower(trim((string) ($user?->role ?? ''))) !== 'staff') {
            return true;
        }

        $routeName = (string) $routeName;

        if (in_array($routeName, ['staff.notifications.readAll', 'admin.notifications.readAll'], true)) {
            return true;
        }

        if ($routeName === 'staff.dashboard') {
            return true;
        }

        $permission = self::permissionForRouteName($routeName);

        return $permission !== null && self::can($user, $permission);
    }
}
