<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @php
        $adminShellUser = auth()->user();
        $adminShellRole = strtolower(trim((string) (auth()->user()?->role ?? 'admin')));
        $adminShellLabel = $adminShellRole === 'staff' ? 'Staff' : 'Admin';
        $adminShellDashboardRoute =
            $adminShellRole === 'staff'
                ? \App\Support\StaffPermissions::firstRouteName($adminShellUser)
                : 'admin.dashboard';
        $adminShellNotificationReadRoute = $adminShellRole === 'staff'
            ? 'staff.notifications.readAll'
            : 'admin.notifications.readAll';
    @endphp
    <title>{{ $title ?? ($schoolBrandName ?? 'TechBridge Academy') . ' | ' . $adminShellLabel . ' Dashboard' }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        referrerpolicy="no-referrer" />
    <script>
        (() => {
            try {
                const savedTheme = localStorage.getItem('admin_theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const darkMode = savedTheme ? savedTheme === 'dark' : prefersDark;
                document.documentElement.classList.toggle('dark', darkMode);
                document.documentElement.style.colorScheme = darkMode ? 'dark' : 'light';
            } catch (error) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/navbar/admin-navbar.js'])
</head>

<body class="min-h-full overflow-x-hidden bg-slate-100 text-slate-800 transition-colors duration-200 dark:bg-slate-950 dark:text-slate-100"
    data-admin-shell data-loading-shell>
    <div id="admin-swirling-loader" class="admin-swirling-loader" role="status" aria-live="polite"
        aria-label="Loading {{ strtolower($adminShellLabel) }} panel">
        <svg class="admin-swirling-loader__icon" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true" focusable="false">
            <circle class="loading-ui-swirling-circle" cx="400" cy="400" r="200" fill="none"
                stroke="currentColor" stroke-linecap="round" stroke-width="50" />
        </svg>
    </div>

    <div x-data="adminShell()" x-init="init()" @keydown.escape.window="closeAll()"
        class="min-h-screen overflow-x-hidden">

        {{-- MOBILE OVERLAY --}}
        <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 lg:hidden"
            @click="mobileOpen=false" aria-hidden="true"></div>

        {{-- SIDEBAR --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 flex h-screen flex-col border-r border-slate-200 bg-white shadow-sm transition-all duration-300 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/30 lg:translate-x-0"
            :class="[
                mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                collapsed ? 'w-16' : 'w-56'
            ]"
            aria-label="Sidebar">
            {{-- Header --}}
            <div class="flex h-14 items-center border-b border-slate-200 shrink-0 dark:border-slate-800"
                :class="collapsed ? 'justify-center px-2' : 'justify-between px-3'">
                <a href="{{ route($adminShellDashboardRoute) }}" class="flex min-w-0 items-center gap-2"
                    x-show="!collapsed" x-transition>
                    <img src="{{ $schoolBrandLogo ?? asset('images/techbridge-logo-mark.svg') }}"
                        alt="{{ $schoolBrandName ?? 'TechBridge Academy' }} logo"
                        class="h-9 w-9 shrink-0 object-contain" />

                    <div class="min-w-0">
                        <div class="truncate text-sm font-extrabold tracking-tight text-slate-900 dark:text-white">
                            {{ $schoolBrandName ?? 'TechBridge Academy' }}
                        </div>
                        <div class="truncate text-xs text-slate-500 dark:text-slate-400">
                            {{ $schoolBrandTagline ?? $adminShellLabel . ' Dashboard' }}</div>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    {{-- desktop collapse --}}
                    <button type="button"
                        class="hidden h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white dark:focus:ring-indigo-500/20 lg:inline-flex"
                        @click="toggleSidebar()" :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                        <svg x-show="!collapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 6h16v2H4V6Zm0 5h10v2H4v-2Zm0 5h16v2H4v-2Z" />
                        </svg>
                        <svg x-show="collapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 6h10v2H4V6Zm0 5h16v2H4v-2Zm0 5h10v2H4v-2Z" />
                        </svg>
                    </button>

                    {{-- mobile close --}}
                    <button
                        class="inline-flex lg:hidden h-10 w-10 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:text-slate-300 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
                        @click="mobileOpen=false" type="button" aria-label="Close sidebar">
                        &times;
                    </button>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="nav-scrollbar flex-1 overflow-y-auto py-3" :class="collapsed ? 'px-2' : 'px-3'">
                @php
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

                    if (!$isStaffUser || $staffCan('homepage')) {
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

                    $adminSearchTarget = function ($route, $label, $icon, $description, $keywords = [], $searchable = true) use ($adminNavIconClasses) {
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

                    $adminSearchTargets[] = $adminSearchTarget($isStaffUser ? 'staff.dashboard' : 'admin.dashboard', 'Dashboard', 'layout-dashboard', 'Overview, stats, events, and school summary.', ['home', 'overview'], false);

                    if (!$isStaffUser) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.reports', 'Reports', 'chart-line', 'View admin reports and exports.', ['analytics', 'export'], false);
                        $adminSearchTargets[] = $adminSearchTarget('admin.admin-staff.index', 'Admin / Staff', 'shield', 'Search admins, staff accounts, emails, and roles.', ['users', 'account']);
                    }

                    if ($staffCan('students')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.students.index', 'Students', 'users', 'Search students, email, phone, class, and subject.', ['learner', 'class']);
                    }

                    if ($staffCan('teachers')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.teachers.index', 'Teachers', 'user-cog', 'Search teachers, email, phone, and subjects.', ['staff', 'instructor']);
                    }

                    if ($staffCan('classes')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.classes.index', 'Classes', 'layers', 'Search classes, sections, rooms, and study time.', ['room', 'section']);
                    }

                    if ($staffCan('subjects')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.subjects.index', 'Subjects', 'book-open', 'Search subjects, codes, schedules, and descriptions.', ['course', 'lesson']);
                    }

                    if ($staffCan('schedule')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.time-studies.index', 'Schedule', 'clock-3', 'Search class, subject, teacher, and study time records.', ['time study', 'period']);
                    }

                    if ($staffCan('student_progress')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.student-study.index', 'Student Progress', 'graduation-cap', 'Search study records by student, class, subject, or teacher.', ['study', 'progress']);
                    }

                    if ($staffCan('finance')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.finance.index', 'School Finance', 'wallet', 'Search payments, student balances, and finance notes.', ['payment', 'balance']);
                    }

                    if ($staffCan('teacher_attendance')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.attendance.teachers.index', 'Teacher Attendance', 'clipboard-check', 'Search teacher attendance and leave records.', ['present', 'absent', 'law request']);
                    }

                    if ($staffCan('student_attendance')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.attendance.index', 'Student Attendance', 'calendar-check', 'Search student attendance, classes, teachers, and remarks.', ['present', 'absent']);
                    }

                    if ($staffCan('messages')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.contacts.index', 'Messages', 'mail', 'Search contact messages by name, email, subject, or text.', ['inbox', 'contact']);
                    }

                    if (!$isStaffUser || $staffCan('homepage')) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.homepage.index', 'Homepage UI', 'palette', 'Open website content and homepage settings.', ['website', 'content'], false);
                    }

                    if (!$isStaffUser) {
                        $adminSearchTargets[] = $adminSearchTarget('admin.settings.index', 'Settings', 'settings', 'Open profile, password, and system settings.', ['profile', 'password'], false);
                    }
                @endphp

                <nav class="space-y-3">
                    @foreach ($sections as $section)
                        @php
                            if (empty($section['items'])) {
                                continue;
                            }

                            $sectionKey = $section['key'];
                            $sectionIsActive = collect($section['items'])->contains(fn($navItem) => $navItem['active']);
                            $sectionBadgeCount = collect($section['items'])->sum(fn($navItem) => $navItem['badge'] ?? 0);
                        @endphp
                        <div class="space-y-1" @if ($sectionIsActive) x-init="openSidebarSection('{{ $sectionKey }}')" @endif>
                            <button type="button" x-show="!collapsed" x-transition
                                @click="toggleSidebarSection('{{ $sectionKey }}')"
                                :aria-expanded="isSidebarSectionOpen('{{ $sectionKey }}')"
                                class="group flex w-full items-center gap-2 rounded-lg px-2 py-1.5 text-left text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400 transition hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:text-slate-500 dark:hover:bg-slate-800/80 dark:hover:text-slate-200 dark:focus:ring-indigo-500/20">
                                <span class="min-w-0 flex-1 truncate">{{ $section['title'] }}</span>
                                @if ($sectionBadgeCount > 0)
                                    <span
                                        class="grid h-4 min-w-4 place-items-center rounded-full bg-red-100 px-1 text-[9px] font-black tracking-normal text-red-700 dark:bg-red-500/15 dark:text-red-300">
                                        {{ $sectionBadgeCount > 99 ? '99+' : $sectionBadgeCount }}
                                    </span>
                                @endif
                                <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200 group-hover:text-slate-600 dark:group-hover:text-slate-300"
                                    :class="isSidebarSectionOpen('{{ $sectionKey }}') ? 'rotate-0' : '-rotate-90'"
                                    aria-hidden="true"></i>
                            </button>

                            <div class="space-y-1" x-show="collapsed || isSidebarSectionOpen('{{ $sectionKey }}')"
                                x-transition>
                                @foreach ($section['items'] as $l)
                                    <a href="{{ route($l['route']) }}"
                                        class="group relative flex min-h-10 items-center rounded-xl text-sm transition duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 {{ $l['active'] ? 'bg-indigo-600 text-white shadow-[0_12px_24px_-18px_rgba(79,70,229,0.8)]' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800/80 dark:hover:text-white' }}"
                                        :class="collapsed ? 'justify-center px-2 py-2.5' : 'gap-2 px-3 py-2.5'">

                                        @if ($l['active'])
                                            <span
                                                class="absolute left-0 top-2 bottom-2 w-0.5 rounded-r-full bg-white"></span>
                                        @endif

                                        <span
                                            class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm transition {{ $l['active'] ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-white group-hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-300 dark:group-hover:bg-slate-700 dark:group-hover:text-indigo-300' }}">
                                            <i class="{{ $adminNavIconClasses[$l['icon']] ?? 'fa-solid fa-circle' }}"
                                                aria-hidden="true"></i>

                                            @if (($l['badge'] ?? 0) > 0)
                                                <span
                                                    class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 {{ $l['active'] ? 'ring-indigo-600' : 'ring-white dark:ring-slate-900' }}"></span>
                                            @endif
                                        </span>

                                        <div x-show="!collapsed" x-transition class="min-w-0 flex-1">
                                            <div class="truncate text-sm font-semibold">{{ $l['label'] }}</div>
                                        </div>

                                        <div x-show="!collapsed">
                                            @if (($l['badge'] ?? 0) > 0)
                                                <span
                                                    class="inline-flex min-w-[24px] items-center justify-center rounded-full px-2 py-1 text-[10px] font-bold {{ $l['active'] ? 'bg-white text-indigo-700' : 'bg-red-100 text-red-700' }}">
                                                    {{ $l['badge'] > 99 ? '99+' : $l['badge'] }}
                                                </span>
                                            @elseif ($l['active'])
                                                <span class="h-2.5 w-2.5 rounded-full bg-white"></span>
                                            @endif
                                        </div>

                                        <div x-show="collapsed"
                                            class="pointer-events-none absolute left-full ml-2 hidden whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-lg ring-1 ring-white/10 group-hover:block dark:bg-slate-800 lg:group-hover:block">
                                            {{ $l['label'] }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>
        </aside>
        {{-- MAIN AREA --}}
        <div class="min-h-screen flex flex-col overflow-x-hidden" :class="collapsed ? 'lg:pl-16' : 'lg:pl-56'">

            {{-- TOPBAR --}}
            <header class="sticky top-0 z-30 bg-slate-100/80 backdrop-blur border-b border-slate-200 transition-colors dark:border-slate-800 dark:bg-slate-950/80">
                <div class="min-h-16 px-4 sm:px-4 lg:px-4 flex flex-wrap items-center gap-2 sm:gap-3 md:flex-nowrap">

                    {{-- Mobile menu button --}}
                    <button
                        class="lg:hidden p-2 rounded-xl hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
                        @click="mobileOpen=true" aria-label="Open menu" type="button">
                        <svg class="h-6 w-6 text-slate-700 dark:text-slate-200" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z" />
                        </svg>
                    </button>

                    {{-- Search --}}
                    <div class="flex-1 min-w-0">
                        <div class="relative max-w-xl" @click.outside="searchOpen=false">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 dark:text-slate-500">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M10 2a8 8 0 1 0 5.29 14.06l4.33 4.33 1.41-1.41-4.33-4.33A8 8 0 0 0 10 2Zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12Z" />
                                </svg>
                            </span>
                            <input type="text" placeholder="Search"
                                x-model="searchTerm"
                                @focus="searchOpen=true; closeMenus()"
                                @input="searchOpen=true"
                                @keydown.enter.prevent="goSearch()"
                                class="w-full rounded-full bg-white border border-slate-200 pl-11 pr-4 py-2.5 text-sm text-slate-900
                                    outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/20" />

                            <div x-show="searchOpen" x-cloak x-transition.origin.top.left
                                class="fixed left-3 right-3 top-20 z-[85] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/40
                                       sm:absolute sm:left-0 sm:right-auto sm:top-auto sm:mt-2 sm:w-[min(36rem,calc(100vw-2rem))]">
                                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 dark:border-slate-800">
                                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400 dark:text-slate-500"
                                        x-text="searchTerm.trim() ? 'Search in' : 'Quick places'"></div>
                                    <button type="button"
                                        class="rounded-lg px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                                        x-show="searchTerm.trim()"
                                        @click="searchTerm=''; $nextTick(() => $root.querySelector('input[placeholder=Search]')?.focus())">
                                        Clear
                                    </button>
                                </div>

                                <div class="nav-scrollbar max-h-96 overflow-y-auto p-2">
                                    <template x-for="item in filteredSearchItems()" :key="item.label">
                                        <button type="button"
                                            class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-left transition hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none dark:hover:bg-indigo-500/10 dark:focus:bg-indigo-500/10"
                                            @click="goSearch(item)">
                                            <span
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-indigo-600 dark:bg-slate-800 dark:text-indigo-300">
                                                <i :class="item.icon" aria-hidden="true"></i>
                                            </span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block truncate text-sm font-bold text-slate-900 dark:text-slate-100"
                                                    x-text="item.label"></span>
                                                <span class="block truncate text-xs text-slate-500 dark:text-slate-400"
                                                    x-text="item.searchable && searchTerm.trim() ? `Find '${searchTerm.trim()}' here` : item.description"></span>
                                            </span>
                                            <i class="fa-solid fa-arrow-right text-xs text-slate-300 dark:text-slate-600"
                                                aria-hidden="true"></i>
                                        </button>
                                    </template>

                                    <div class="px-4 py-8 text-center" x-show="filteredSearchItems().length === 0">
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100">No matching page</div>
                                        <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">Try students, teachers, classes, finance, or attendance.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="ml-auto flex shrink-0 items-center gap-1.5 sm:gap-2 md:ml-0">
                        {{-- Theme --}}
                        <button type="button"
                            class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
                            @click="toggleTheme()"
                            :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                            :title="darkMode ? 'Light mode' : 'Dark mode'">
                            <svg x-show="!darkMode" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"
                                aria-hidden="true">
                                <path
                                    d="M21.64 13.02A8.5 8.5 0 0 1 11 2.36 8.5 8.5 0 1 0 21.64 13.02Z" />
                            </svg>
                            <svg x-show="darkMode" x-cloak class="h-5 w-5" viewBox="0 0 24 24"
                                fill="currentColor" aria-hidden="true">
                                <path
                                    d="M12 18a6 6 0 1 1 0-12 6 6 0 0 1 0 12Zm0 4-1-3h2l-1 3Zm0-20 1 3h-2l1-3Zm10 10-3 1v-2l3 1ZM2 12l3-1v2l-3-1Zm16.95 6.95-2.83-1.41 1.42-1.42 1.41 2.83ZM5.05 5.05l2.83 1.41-1.42 1.42L5.05 5.05Zm13.9 0-1.41 2.83-1.42-1.42 2.83-1.41ZM5.05 18.95l1.41-2.83 1.42 1.42-2.83 1.41Z" />
                            </svg>
                        </button>

                        {{-- Notifications --}}
                        <div class="relative" @click.outside="notifOpen=false">
                            <button
                                class="relative p-2 rounded-xl bg-white border border-slate-200 hover:shadow-sm
                                           focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
                                @click="profileOpen=false; messageOpen=false; notifOpen=!notifOpen"
                                aria-label="Notifications" type="button">
                                <svg class="h-5 w-5 text-slate-700 dark:text-slate-200" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6V11a6 6 0 1 0-12 0v5L4 18v1h16v-1l-2-2Z" />
                                </svg>

                                @if (($navUnread ?? 0) > 0)
                                    <span
                                        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                                                 rounded-full bg-red-500 text-white text-[10px] font-bold
                                                 grid place-items-center ring-2 ring-white">
                                        {{ $navUnread }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="notifOpen" x-cloak x-transition.origin.top.right
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/40
                                       sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-80">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                                    <div class="text-sm font-bold text-slate-900 dark:text-slate-100">Notifications</div>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Latest</span>
                                </div>

                                <div class="nav-scrollbar max-h-80 overflow-auto">
                                    @forelse(($navNotifs ?? []) as $n)
                                        <a href="{{ $n->url ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                            <div class="flex items-start gap-3">
                                                <span
                                                    class="mt-2 h-2 w-2 rounded-full {{ $n->is_read ? 'bg-slate-300' : 'bg-indigo-600' }}"></span>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                                        {{ $n->title }}</div>
                                                    <div class="text-xs text-slate-500 truncate dark:text-slate-400">{{ $n->message }}
                                                    </div>
                                                    <div class="text-[11px] text-slate-400 mt-1 dark:text-slate-500">
                                                        {{ $n->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">No notifications yet.
                                        </div>
                                    @endforelse
                                </div>

                                <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800">
                                    <form method="POST" action="{{ route($adminShellNotificationReadRoute) }}">
                                        @csrf
                                        <button
                                            class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                            Mark all as read
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Messages --}}
                        <div class="relative" @click.outside="messageOpen=false">
                            <button
                                class="relative p-2 rounded-xl bg-white border border-slate-200 hover:shadow-sm
                                           focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
                                @click="notifOpen=false; profileOpen=false; messageOpen=!messageOpen"
                                aria-label="Messages" type="button">
                                <svg class="h-5 w-5 text-slate-700 dark:text-slate-200" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 4h16v12H6l-2 2V4Zm2 2v8h12V6H6Z" />
                                </svg>

                                @if (($contactUnread ?? 0) > 0)
                                    <span
                                        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                                                 rounded-full bg-red-500 text-white text-[10px] font-bold
                                                 grid place-items-center ring-2 ring-white">
                                        {{ $contactUnread > 99 ? '99+' : $contactUnread }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="messageOpen" x-cloak x-transition.origin.top.right
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/40
                                       sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-96">

                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                                    <div class="text-sm font-bold text-slate-900 dark:text-slate-100">Contact Messages</div>
                                    @if (($contactUnread ?? 0) > 0)
                                        <span class="text-xs font-semibold text-amber-600">{{ $contactUnread }}
                                            unread</span>
                                    @endif
                                </div>

                                <div class="nav-scrollbar max-h-80 overflow-auto">
                                    @forelse(($navContacts ?? []) as $contact)
                                        <a href="{{ route('admin.contacts.index') }}"
                                            class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                            <div class="flex items-start gap-3">
                                                <span
                                                    class="mt-2 h-2 w-2 rounded-full {{ $contact->is_read ? 'bg-slate-300' : 'bg-amber-500' }}"></span>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <div class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">
                                                            {{ $contact->name }}</div>
                                                        <div class="text-[11px] text-slate-400 dark:text-slate-500">
                                                            {{ $contact->created_at->diffForHumans() }}</div>
                                                    </div>
                                                    <div class="truncate text-xs font-semibold text-slate-600 dark:text-slate-300">
                                                        {{ $contact->subject }}</div>
                                                    <div class="truncate text-xs text-slate-500 dark:text-slate-400">
                                                        {{ $contact->message }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">No contact messages
                                            yet.</div>
                                    @endforelse
                                </div>

                                <div class="grid gap-2 px-4 py-3 border-t border-slate-100 dark:border-slate-800">
                                    <a href="{{ route('admin.contacts.index') }}"
                                        class="w-full rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800">
                                        Open Contact Inbox
                                    </a>
                                    @if (($contactUnread ?? 0) > 0)
                                        <form method="POST" action="{{ route('admin.contacts.readAll') }}">
                                            @csrf
                                            <button
                                                class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                Mark all contact messages as read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Profile --}}
                        <div class="relative" @click.outside="profileOpen=false">
                            <button
                                class="ml-1 flex max-w-[12rem] items-center gap-2 rounded-full bg-white border border-slate-200 px-2.5 py-2 hover:shadow-sm sm:ml-2 sm:max-w-none sm:gap-3 sm:px-3
                                       focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
                                @click="notifOpen=false; messageOpen=false; profileOpen=!profileOpen"
                                aria-label="Open profile menu" type="button">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}"
                                    onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                                    alt="avatar">

                                <div class="leading-tight hidden lg:block text-left">
                                    <div class="max-w-[10rem] truncate text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        {{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500 capitalize dark:text-slate-400">{{ auth()->user()->role }}</div>
                                </div>

                                <svg class="hidden h-4 w-4 text-slate-500 dark:text-slate-400 sm:block" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path d="M7 10l5 5 5-5H7z" />
                                </svg>
                            </button>

                            <div x-show="profileOpen" x-cloak x-transition.origin.top.right
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/40
                                       sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-56">
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                                    <div class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</div>
                                </div>

                                @if ($adminShellRole !== 'staff')
                                    <a href="{{ route('admin.settings.index') }}"
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800/80">
                                        <span class="h-8 w-8 rounded-xl bg-slate-100 flex items-center justify-center dark:bg-slate-800">
                                            <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                            </svg>
                                        </span>
                                        Settings
                                    </a>
                                @endif

                                <div class="border-t border-slate-100 dark:border-slate-800"></div>

                                <div class="p-2">
                                    <a href="{{ route('logout') }}"
                                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8v2h8v14h-8v2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z" />
                                        </svg>
                                        Logout
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-x-hidden p-4 sm:p-6" data-page-animate>
                @yield('page')
            </main>

        </div>
    </div>

    <script>
        window.adminNavbarSearchItems = @json($adminSearchTargets, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
