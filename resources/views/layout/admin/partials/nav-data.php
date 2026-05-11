<?php
    $currentRole = strtolower(trim((string) (auth()->user()?->role ?? 'admin')));
    $isStaffUser = $currentRole === 'staff';
    $staffUser = auth()->user();
    $staffIsRestricted = $isStaffUser && \App\Support\StaffPermissions::isRestricted($staffUser);
    $staffCan = fn($permission) => !$isStaffUser || \App\Support\StaffPermissions::can($staffUser, $permission);

    $item = fn($route, $label, $icon, $badge = 0) => [
        'route' => $route,
        'label' => $label,
        'icon' => $icon,
        'badge' => max((int) $badge, 0),
        'active' => request()->routeIs($route),
    ];

    $adminNavIconClasses = [
        'layout-dashboard' => 'fa-solid fa-table-columns',
        'users' => 'fa-solid fa-users',
        'user-cog' => 'fa-solid fa-user-gear',
        'shield' => 'fa-solid fa-shield-halved',
        'layers' => 'fa-solid fa-layer-group',
        'book-open' => 'fa-solid fa-book-open',
        'clock-3' => 'fa-solid fa-clock',
        'graduation-cap' => 'fa-solid fa-graduation-cap',
        'clipboard-check' => 'fa-solid fa-clipboard-check',
        'calendar-check' => 'fa-solid fa-calendar-check',
        'mail' => 'fa-solid fa-envelope',
        'flag' => 'fa-solid fa-flag',
        'chart-line' => 'fa-solid fa-chart-line',
        'wallet' => 'fa-solid fa-wallet',
        'palette' => 'fa-solid fa-palette',
        'settings' => 'fa-solid fa-gear',
    ];

    $managementItems = [];

    if ($staffCan('students')) {
        $managementItems[] = $item('admin.students.index', 'Students', 'users');
    }

    if ($staffCan('teachers')) {
        $managementItems[] = $item('admin.teachers.index', 'Teachers', 'user-cog');
    }

    if ($staffCan('classes')) {
        $managementItems[] = $item('admin.classes.index', 'Classes', 'layers');
    }

    if ($staffCan('subjects')) {
        $managementItems[] = $item('admin.subjects.index', 'Subjects', 'book-open');
    }

    if ($staffCan('schedule')) {
        $managementItems[] = $item('admin.time-studies.index', 'Schedule', 'clock-3');
    }

    if ($staffCan('student_progress')) {
        $managementItems[] = $item('admin.student-study.index', 'Student Progress', 'graduation-cap');
    }

    if ($staffCan('finance')) {
        $managementItems[] = $item('admin.finance.index', 'School Finance', 'wallet');
    }

    if ($staffCan('messages')) {
        $managementItems[] = $item('admin.contacts.index', 'Messages', 'mail', $contactUnread ?? 0);
    }

    if ($staffCan('homepage')) {
        $managementItems[] = $item('admin.homepage.index', 'Home UI', 'palette');
    }

    if (!$isStaffUser) {
        array_unshift($managementItems, $item('admin.admin-staff.index', 'Admin / Staff', 'shield'));
    }

    $mainItems = [$item($isStaffUser ? 'staff.dashboard' : 'admin.dashboard', 'Dashboard', 'layout-dashboard')];

    if (!$isStaffUser) {
        $mainItems[] = $item('admin.reports', 'Reports', 'chart-line');
    }

    $attendanceItems = [];

    if ($isStaffUser && $staffCan('teacher_attendance')) {
        $attendanceItems[] = $item(
            'admin.attendance.teachers.index',
            'Teacher Attendance',
            'clipboard-check',
            $teacherAttendanceUnread ?? 0,
        );
    }

    if ($isStaffUser && $staffCan('student_attendance')) {
        $attendanceItems[] = $item(
            'admin.attendance.index',
            'Student Attendance',
            'calendar-check',
            $studentAttendanceUnread ?? 0,
        );
    }

    $systemItems = [];

    if ($staffCan('settings')) {
        $systemItems[] = $item('admin.settings.index', 'Settings', 'settings');
    }

    $sections = [
        [
            'key' => 'main',
            'title' => 'Main',
            'items' => $mainItems,
        ],
        [
            'key' => 'management',
            'title' => 'Management',
            'items' => $managementItems,
        ],
        [
            'key' => 'attendance',
            'title' => 'Attendance',
            'items' => $attendanceItems,
        ],
        [
            'key' => 'system',
            'title' => 'System',
            'items' => $systemItems,
        ],
    ];

    $adminSearchTarget = function ($route, $label, $icon, $description, $keywords = [], $searchable = true) use (
        $adminNavIconClasses
    ) {
        return [
            'label' => $label,
            'description' => $description,
            'url' => route($route),
            'icon' => $adminNavIconClasses[$icon] ?? 'fa-solid fa-magnifying-glass',
            'keywords' => implode(' ', array_merge([$label, $description], $keywords)),
            'searchable' => $searchable,
            'active' => request()->routeIs($route),
        ];
    };

    $adminSearchTargets = [];

    $adminSearchTargets[] = $adminSearchTarget(
        $isStaffUser ? 'staff.dashboard' : 'admin.dashboard',
        'Dashboard',
        'layout-dashboard',
        'Overview, stats, events, and school summary.',
        ['home', 'overview'],
        false,
    );

    if (!$isStaffUser) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.reports',
            'Reports',
            'chart-line',
            'View admin reports and exports.',
            ['analytics', 'export'],
            false,
        );
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.admin-staff.index',
            'Admin / Staff',
            'shield',
            'Search admins, staff accounts, emails, and roles.',
            ['users', 'account'],
        );
    }

    if ($staffCan('students')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.students.index',
            'Students',
            'users',
            'Search students, email, phone, class, and subject.',
            ['learner', 'class'],
        );
    }

    if ($staffCan('teachers')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.teachers.index',
            'Teachers',
            'user-cog',
            'Search teachers, email, phone, and subjects.',
            ['staff', 'instructor'],
        );
    }

    if ($staffCan('classes')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.classes.index',
            'Classes',
            'layers',
            'Search classes, sections, rooms, and study time.',
            ['room', 'section'],
        );
    }

    if ($staffCan('subjects')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.subjects.index',
            'Subjects',
            'book-open',
            'Search subjects, codes, schedules, and descriptions.',
            ['course', 'lesson'],
        );
    }

    if ($staffCan('schedule')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.time-studies.index',
            'Schedule',
            'clock-3',
            'Search class, subject, teacher, and study time records.',
            ['time study', 'period'],
        );
    }

    if ($staffCan('student_progress')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.student-study.index',
            'Student Progress',
            'graduation-cap',
            'Search study records by student, class, subject, or teacher.',
            ['study', 'progress'],
        );
    }

    if ($staffCan('finance')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.finance.index',
            'School Finance',
            'wallet',
            'Search payments, student balances, and finance notes.',
            ['payment', 'balance'],
        );
    }

    if ($staffCan('teacher_attendance')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.attendance.teachers.index',
            'Teacher Attendance',
            'clipboard-check',
            'Search teacher attendance and leave records.',
            ['present', 'absent', 'law request'],
        );
    }

    if ($staffCan('student_attendance')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.attendance.index',
            'Student Attendance',
            'calendar-check',
            'Search student attendance, classes, teachers, and remarks.',
            ['present', 'absent'],
        );
    }

    if ($staffCan('messages')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.contacts.index',
            'Messages',
            'mail',
            'Search contact messages by name, email, subject, or text.',
            ['inbox', 'contact'],
        );
    }

    if (!$isStaffUser || $staffCan('homepage')) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.homepage.index',
            'Homepage UI',
            'palette',
            'Open website content and homepage settings.',
            ['website', 'content'],
            false,
        );
    }

    if (!$isStaffUser) {
        $adminSearchTargets[] = $adminSearchTarget(
            'admin.settings.index',
            'Settings',
            'settings',
            'Open profile, password, and system settings.',
            ['profile', 'password'],
            false,
        );
    }
?>
