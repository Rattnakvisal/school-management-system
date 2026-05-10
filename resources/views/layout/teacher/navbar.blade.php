<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title ?? ($schoolBrandName ?? 'TechBridge Academy') . ' | Teacher Dashboard' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/navbar/teacher-navbar.js'])
</head>

<body class="min-h-full overflow-x-hidden bg-slate-100 text-slate-800" data-loading-shell>
    <div id="admin-swirling-loader" class="admin-swirling-loader" role="status" aria-live="polite"
        aria-label="Loading teacher panel">
        <svg class="admin-swirling-loader__icon" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true" focusable="false">
            <circle class="loading-ui-swirling-circle" cx="400" cy="400" r="200" fill="none"
                stroke="currentColor" stroke-linecap="round" stroke-width="50" />
        </svg>
    </div>

    <div x-data="teacherShell()" x-init="init()" @keydown.escape.window="closeAll()" class="min-h-screen"
        data-teacher-notification-root data-notification-poll-url="{{ route('teacher.notifications.poll') }}"
        data-notification-readall-url="{{ route('teacher.notifications.readAll') }}"
        data-notification-latest-id="{{ (int) ($navNotifs->first()->id ?? 0) }}"
        data-notification-unread-count="{{ (int) ($navUnread ?? 0) }}">

        {{-- MOBILE OVERLAY --}}
        <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 lg:hidden"
            @click="mobileOpen = false" aria-hidden="true"></div>

        {{-- SIDEBAR --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 flex h-screen flex-col overflow-hidden border-r border-slate-200 bg-white shadow-sm transition-all duration-300"
            :class="[
                isDesktop ?
                (sidebarCollapsed ? 'translate-x-0 w-16' : 'translate-x-0 w-56') :
                (mobileOpen ? 'translate-x-0 w-[84vw] max-w-[19rem]' : '-translate-x-full w-[84vw] max-w-[19rem]')
            ]"
            aria-label="Sidebar">

            {{-- Header --}}
            <div class="flex h-14 items-center border-b border-slate-200 shrink-0"
                :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                <a href="{{ route('teacher.dashboard') }}" class="flex min-w-0 items-center gap-2"
                    x-show="!sidebarCollapsed" x-transition>
                    <img src="{{ $schoolBrandLogo ?? asset('images/techbridge-logo-mark.svg') }}"
                        alt="{{ $schoolBrandName ?? 'TechBridge Academy' }} logo"
                        class="h-9 w-9 shrink-0 object-contain" />

                    <div class="min-w-0">
                        <div class="truncate text-sm font-extrabold tracking-tight text-slate-900">
                            {{ $schoolBrandName ?? 'TechBridge Academy' }}</div>
                        <div class="truncate text-xs text-slate-500">
                            {{ $schoolBrandTagline ?? 'Teacher Panel' }}</div>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    <button type="button"
                        class="hidden h-9 w-9 items-center justify-center rounded-xl text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 lg:inline-flex"
                        @click="toggleSidebar()" :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                        <svg x-show="!sidebarCollapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"
                            aria-hidden="true">
                            <path d="M4 6h16v2H4V6Zm0 5h10v2H4v-2Zm0 5h16v2H4v-2Z" />
                        </svg>
                        <svg x-show="sidebarCollapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"
                            aria-hidden="true">
                            <path d="M4 6h10v2H4V6Zm0 5h16v2H4v-2Zm0 5h10v2H4v-2Z" />
                        </svg>
                    </button>

                    <button
                        class="rounded-2xl p-2.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 lg:hidden"
                        @click="mobileOpen = false" aria-label="Close sidebar" type="button">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 6 6 18"></path>
                            <path d="M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="nav-scrollbar flex-1 space-y-3 overflow-y-auto py-3"
                :class="sidebarCollapsed ? 'px-2' : 'px-3'">
                @php
                    $item = fn($route, $label, $icon) => [
                        'route' => $route,
                        'label' => $label,
                        'icon' => $icon,
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

                    $sections = [
                        [
                            'key' => 'main',
                            'title' => 'Main',
                            'items' => [$item('teacher.dashboard', 'Dashboard', 'home')],
                        ],
                        [
                            'key' => 'learning',
                            'title' => 'Learning',
                            'items' => [
                                $item('teacher.classes.index', 'My Classes', 'grid'),
                                $item('teacher.schedule.index', 'Schedule', 'clock'),
                                $item('teacher.attendance.index', 'Attendance', 'check'),
                            ],
                        ],
                        [
                            'key' => 'requests',
                            'title' => 'Requests',
                            'items' => [
                                $item('teacher.law-requests.index', 'Law Requests', 'document'),
                                $item('teacher.assignments.index', 'Assignments', 'clipboard'),
                                $item('teacher.grades.index', 'Grades', 'book'),
                                $item('teacher.notices.index', 'Notifications', 'bell'),
                            ],
                        ],
                        [
                            'key' => 'system',
                            'title' => 'System',
                            'items' => [$item('teacher.settings', 'Settings', 'cog')],
                        ],
                    ];
                @endphp

                @foreach ($sections as $section)
                    @php
                        $sectionKey = $section['key'];
                        $sectionIsActive = collect($section['items'])->contains(fn($navItem) => $navItem['active']);
                    @endphp
                    <div class="space-y-1" @if ($sectionIsActive) x-init="openSidebarSection('{{ $sectionKey }}')" @endif>
                        <button type="button" x-show="!sidebarCollapsed" x-transition
                            @click="toggleSidebarSection('{{ $sectionKey }}')"
                            :aria-expanded="isSidebarSectionOpen('{{ $sectionKey }}')"
                            class="group flex w-full items-center gap-2 rounded-lg px-2 py-1.5 text-left text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400 transition hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                            <span class="min-w-0 flex-1 truncate">{{ $section['title'] }}</span>
                            <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200 group-hover:text-slate-600"
                                :class="isSidebarSectionOpen('{{ $sectionKey }}') ? 'rotate-0' : '-rotate-90'"
                                aria-hidden="true"></i>
                        </button>

                        <div class="space-y-1" x-show="sidebarCollapsed || isSidebarSectionOpen('{{ $sectionKey }}')"
                            x-transition>
                            @foreach ($section['items'] as $l)
                                <a href="{{ route($l['route']) }}"
                                    class="group relative flex min-h-10 items-center rounded-xl text-sm transition duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-100 {{ $l['active'] ? 'bg-indigo-600 text-white shadow-[0_12px_24px_-18px_rgba(79,70,229,0.8)]' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-950' }}"
                                    :class="sidebarCollapsed ? 'justify-center px-2 py-2.5' : 'gap-2 px-3 py-2.5'">

                                    @if ($l['active'])
                                        <span class="absolute left-0 top-2 bottom-2 w-0.5 rounded-r-full bg-white"></span>
                                    @endif

                                    <span
                                        class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm transition {{ $l['active'] ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-white group-hover:text-indigo-600' }}">
                                        <i class="{{ $teacherNavIconClasses[$l['icon']] ?? 'fa-solid fa-circle' }}"
                                            aria-hidden="true"></i>
                                    </span>

                                    <div x-show="!sidebarCollapsed" x-transition class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-semibold">{{ $l['label'] }}</div>
                                    </div>

                                    <div x-show="!sidebarCollapsed">
                                        @if ($l['active'])
                                            <span class="h-2.5 w-2.5 rounded-full bg-white"></span>
                                        @endif
                                    </div>

                                    <div x-show="sidebarCollapsed"
                                        class="pointer-events-none absolute left-full ml-2 hidden whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-lg group-hover:block lg:group-hover:block">
                                        {{ $l['label'] }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>
        </aside>

        {{-- MAIN AREA --}}
        <div class="min-h-screen flex flex-col transition-[padding] duration-300"
            :class="isDesktop ? (sidebarCollapsed ? 'pl-16' : 'pl-56') : 'pl-0'">

            {{-- TOPBAR --}}
            <header class="top-0 z-30">
                <div class="flex h-16 items-center gap-3 px-4 sm:px-6">
                    <button
                        class="rounded-xl p-2 text-slate-600 transition hover:bg-white hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 lg:hidden"
                        @click="mobileOpen = true" aria-label="Open menu" type="button">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z" />
                        </svg>
                    </button>
                <div class="flex-1 min-w-0">
                    <div class="relative max-w-xl" @click.outside="searchOpen=false">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <i class="fa-solid fa-magnifying-glass"></i>
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
                                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400"
                                        x-text="searchTerm.trim() ? 'Search in' : 'Quick places'"></div>
                                    <button type="button"
                                        class="rounded-lg px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                        x-show="searchTerm.trim()"
                                        @click="searchTerm=''; $nextTick(() => $root.querySelector('input[placeholder=Search]')?.focus())">
                                        Clear
                                    </button>
                                </div>

                                <div class="nav-scrollbar max-h-96 overflow-y-auto p-2">
                                    <template x-for="item in filteredSearchItems()" :key="item.label">
                                        <button type="button"
                                            class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-left transition hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none"
                                            @click="goSearch(item)">
                                            <span
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-indigo-600">
                                                <i :class="item.icon" aria-hidden="true"></i>
                                            </span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block truncate text-sm font-bold text-slate-900"
                                                    x-text="item.label"></span>
                                                <span class="block truncate text-xs text-slate-500"
                                                    x-text="item.searchable && searchTerm.trim() ? `Find '${searchTerm.trim()}' here` : item.description"></span>
                                            </span>
                                            <i class="fa-solid fa-arrow-right text-xs text-slate-300" aria-hidden="true"></i>
                                        </button>
                                    </template>

                                    <div class="px-4 py-8 text-center" x-show="filteredSearchItems().length === 0">
                                        <div class="text-sm font-bold text-slate-800">No matching page</div>
                                        <div class="mt-1 text-xs text-slate-500">Try classes, schedule, assignments, or attendance.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        {{-- Search (teacher) --}}
                        @php
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
                        @endphp

                        <div class="relative" @click.outside="notifOpen = false">
                            <button
                                class="relative rounded-xl border border-slate-200 bg-white p-2 hover:shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                @click="profileOpen = false; notifOpen = !notifOpen" aria-label="Notifications"
                                type="button">
                                <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6V11a6 6 0 1 0-12 0v5L4 18v1h16v-1l-2-2Z" />
                                </svg>

                                <span id="teacher-notif-badge"
                                    class="absolute -top-1 -right-1 grid h-[18px] min-w-[18px] place-items-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-white {{ ($navUnread ?? 0) > 0 ? '' : 'hidden' }}">
                                    {{ $navUnread ?? 0 }}
                                </span>
                            </button>

                            <div x-show="notifOpen" x-cloak x-transition.origin.top.right
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-80">
                                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                                    <div class="text-sm font-bold text-slate-900">Notifications</div>
                                    <span class="text-xs text-slate-500">Latest</span>
                                </div>

                                <div id="teacher-notif-list" class="nav-scrollbar max-h-80 overflow-auto">
                                    @forelse (($navNotifs ?? []) as $n)
                                        <a href="{{ trim((string) ($n->url ?? '')) !== '' ? $n->url : route('teacher.notices.index') }}"
                                            class="block px-4 py-3 hover:bg-slate-50">
                                            <div class="flex items-start gap-3">
                                                <span
                                                    class="mt-2 h-2 w-2 rounded-full {{ $n->is_read ? 'bg-slate-300' : 'bg-indigo-600' }}"></span>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-slate-800">
                                                        {{ $n->title }}
                                                    </div>
                                                    <div class="truncate text-xs text-slate-500">{{ $n->message }}
                                                    </div>
                                                    <div class="mt-1 text-[11px] text-slate-400">
                                                        {{ $n->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div id="teacher-notif-empty"
                                            class="px-4 py-8 text-center text-sm text-slate-500">No notifications yet.
                                        </div>
                                    @endforelse
                                </div>

                                <div class="border-t border-slate-100 px-4 py-3">
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <a href="{{ route('teacher.notices.index') }}"
                                            class="rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800">
                                            View all
                                        </a>
                                        <form method="POST" action="{{ route('teacher.notifications.readAll') }}">
                                            @csrf
                                            <button
                                                class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                                Mark all read
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative" @click.outside="profileOpen = false">
                            <button
                                class="flex items-center gap-3 rounded-full border border-slate-200 bg-white px-3 py-2 hover:shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                @click="notifOpen = false; profileOpen = !profileOpen" aria-label="Open profile menu"
                                type="button">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}"
                                    onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                                    alt="avatar">

                                <div class="hidden text-left leading-tight sm:block">
                                    <div class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</div>
                                </div>

                                <svg class="h-4 w-4 text-slate-400 transition-transform duration-200"
                                    :class="profileOpen ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </button>

                            <div x-show="profileOpen" x-cloak x-transition.origin.top.right
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-56">
                                <div class="border-b border-slate-100 px-4 py-3">
                                    <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                                    <div class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</div>
                                </div>

                                <a href="{{ route('teacher.settings') }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-50">
                                        <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                        </svg>
                                    </span>
                                    Settings
                                </a>

                                <a href="{{ route('teacher.attendance.index') }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50">
                                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    </span>
                                    Check Attendance
                                </a>

                                <div class="border-t border-slate-100"></div>

                                <div class="p-2">
                                    <a href="{{ route('logout') }}"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
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

            <main class="flex-1 p-4 sm:p-6" data-page-animate>
                @yield('page')
            </main>
        </div>
    </div>

    <script>
        window.teacherNavbarSearchItems = @json($teacherSearchTargets ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
