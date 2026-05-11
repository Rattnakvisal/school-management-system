<?php
$currentRole = strtolower(trim((string) (auth()->user()?->role ?? 'student')));
$isStudentUser = $currentRole === 'student';
$studentUser = auth()->user();

$item = fn($route, $label, $icon, $badge = 0) => [
    'route' => $route,
    'label' => $label,
    'icon' => $icon,
    'badge' => max((int) $badge, 0),
    'active' => request()->routeIs($route),
];

$studentNavIconClasses = [
    'home' => 'fa-solid fa-table-columns',
    'book' => 'fa-solid fa-book-open',
    'clipboard' => 'fa-solid fa-clipboard-list',
    'document' => 'fa-solid fa-file-lines',
    'bell' => 'fa-solid fa-bell',
    'cog' => 'fa-solid fa-gear',
];

$mainItems = [$item('student.dashboard', 'Dashboard', 'home')];

$managementItems = [
    $item('student.subjects.index', 'My Subjects', 'book'),
    $item('student.grades.index', 'My Grades', 'clipboard'),
];

$requestItems = [
    $item('student.law-requests.index', 'Law Requests', 'document'),
    $item('student.assignment.index', 'Assignment', 'clipboard'),
    $item('student.notices.index', 'Notifications', 'bell'),
];

$systemItems = [$item('student.settings', 'Settings', 'cog')];

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
