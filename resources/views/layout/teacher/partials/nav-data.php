<?php
$currentRole = strtolower(trim((string)(auth()->user()?->role ?? 'teacher')));
$isTeacherUser = $currentRole === 'teacher';
$teacherUser = auth()->user();

$item = fn($route, $label, $icon, $badge = 0) => [
    'route' => $route,
    'label' => $label,
    'icon' => $icon,
    'badge' => max((int) $badge, 0),
    'active' => request()->routeIs($route),
];

$teacherNavIconClasses = [
    'home' => 'fa-solid fa-table-columns',
    'grid' => 'fa-solid fa-layer-group',
    'clock' => 'fa-solid fa-clock',
    'check' => 'fa-solid fa-clipboard-check',
    'document' => 'fa-solid fa-file-lines',
    'clipboard' => 'fa-solid fa-clipboard-list',
    'flag' => 'fa-solid fa-flag',
    'book' => 'fa-solid fa-book-open',
    'bell' => 'fa-solid fa-bell',
    'cog' => 'fa-solid fa-gear',
];

$mainItems = [$item('teacher.dashboard', 'Dashboard', 'home')];

$learningItems = [
    $item('teacher.classes.index', 'My Classes', 'grid'),
    $item('teacher.schedule.index', 'Schedule', 'clock'),
    $item('teacher.attendance.index', 'Attendance', 'check'),
];

$requestItems = [
    $item('teacher.law-requests.index', 'Law Requests', 'document'),
    $item('teacher.assignments.index', 'Assignments', 'clipboard'),
    $item('teacher.grades.index', 'Grades', 'book'),
    $item('teacher.notices.index', 'Notifications', 'bell'),
];

$systemItems = [$item('teacher.settings', 'Settings', 'cog')];

$sections = [
    [
        'key' => 'main',
        'title' => 'Main',
        'items' => $mainItems,
    ],
    [
        'key' => 'learning',
        'title' => 'Learning',
        'items' => $learningItems,
    ],
    [
        'key' => 'requests',
        'title' => 'Requests',
        'items' => $requestItems,
    ],
    [
        'key' => 'system',
        'title' => 'System',
        'items' => $systemItems,
    ],
];

$teacherSearchTarget = function ($route, $label, $icon, $description, $keywords = [], $searchable = true) use ($teacherNavIconClasses) {
    return [
        'label' => $label,
        'description' => $description,
        'url' => route($route),
        'icon' => $teacherNavIconClasses[$icon] ?? 'fa-solid fa-magnifying-glass',
        'keywords' => implode(' ', array_merge([$label, $description], $keywords)),
        'searchable' => $searchable,
        'active' => request()->routeIs($route),
    ];
};

$teacherSearchTargets = [];
$teacherSearchTargets[] = $teacherSearchTarget('teacher.dashboard', 'Dashboard', 'home', 'Overview and quick links.', [], false);
$teacherSearchTargets[] = $teacherSearchTarget('teacher.classes.index', 'My Classes', 'grid', 'View and manage your classes.', ['class', 'room']);
$teacherSearchTargets[] = $teacherSearchTarget('teacher.schedule.index', 'Schedule', 'clock', 'View your schedule and periods.', ['timetable', 'time']);
$teacherSearchTargets[] = $teacherSearchTarget('teacher.attendance.index', 'Attendance', 'check', 'Manage attendance and records.', ['present', 'absent']);
$teacherSearchTargets[] = $teacherSearchTarget('teacher.assignments.index', 'Assignments', 'clipboard', 'Search assignments by title, student, or subject.', ['homework', 'task']);
$teacherSearchTargets[] = $teacherSearchTarget('teacher.grades.index', 'Grades', 'book', 'View and enter student grades.', ['marks', 'scores']);
$teacherSearchTargets[] = $teacherSearchTarget('teacher.law-requests.index', 'Law Requests', 'document', 'View and respond to law requests.', ['request', 'leave']);
$teacherSearchTargets[] = $teacherSearchTarget('teacher.notices.index', 'Notifications', 'bell', 'Open notices and announcements.', ['alerts', 'messages']);
$teacherSearchTargets[] = $teacherSearchTarget('teacher.settings', 'Settings', 'cog', 'Profile and account settings.', [], false);
