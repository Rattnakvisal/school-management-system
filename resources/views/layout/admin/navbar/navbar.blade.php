<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @php
        $adminShellRole = strtolower(trim((string) (auth()->user()?->role ?? 'admin')));
        $adminShellLabel = $adminShellRole === 'staff' ? 'Staff' : 'Admin';
    @endphp
    <title>{{ $title ?? ($schoolBrandName ?? 'TechBridge Academy') . ' | ' . $adminShellLabel . ' Dashboard' }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        referrerpolicy="no-referrer" />

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full overflow-x-hidden bg-slate-100 text-slate-800" data-admin-shell data-loading-shell>
    <div id="admin-swirling-loader" class="admin-swirling-loader" role="status" aria-live="polite"
        aria-label="Loading admin panel">
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
            class="fixed inset-y-0 left-0 z-50 flex h-screen flex-col border-r border-slate-200 bg-white shadow-sm transition-all duration-300 lg:translate-x-0"
            :class="[
                mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                collapsed ? 'w-24' : 'w-72'
            ]"
            aria-label="Sidebar">
            {{-- Header --}}
            <div class="flex h-16 items-center justify-between border-b border-slate-200 px-4 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <img src="{{ $schoolBrandLogo ?? asset('images/techbridge-logo-mark.svg') }}"
                        alt="{{ $schoolBrandName ?? 'TechBridge Academy' }} logo"
                        class="h-12 w-12 shrink-0 object-contain" />

                    <div x-show="!collapsed" x-transition class="min-w-0">
                        <div class="truncate text-base font-extrabold tracking-tight text-slate-900">
                            {{ $schoolBrandName ?? 'TechBridge Academy' }}
                        </div>
                        <div class="truncate text-xs text-slate-500">
                            {{ $schoolBrandTagline ?? $adminShellLabel . ' Dashboard' }}</div>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    {{-- desktop collapse --}}
                    <button type="button"
                        class="hidden lg:inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        @click="toggleSidebar()" :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                        <svg x-show="!collapsed" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 6h16v2H4V6Zm0 5h10v2H4v-2Zm0 5h16v2H4v-2Z" />
                        </svg>
                        <svg x-show="collapsed" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 6h10v2H4V6Zm0 5h16v2H4v-2Zm0 5h10v2H4v-2Z" />
                        </svg>
                    </button>

                    {{-- mobile close --}}
                    <button
                        class="inline-flex lg:hidden h-10 w-10 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        @click="mobileOpen=false" type="button" aria-label="Close sidebar">
                        &times;
                    </button>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="nav-scrollbar flex-1 overflow-y-auto px-3 py-4">
                @php
                    $currentRole = strtolower(trim((string) (auth()->user()?->role ?? 'admin')));
                    $isStaffUser = $currentRole === 'staff';

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

                    $managementItems = [
                        $item('admin.students.index', 'Students', 'users'),
                        $item('admin.teachers.index', 'Teachers', 'user-cog'),
                        $item('admin.classes.index', 'Classes', 'layers'),
                        $item('admin.subjects.index', 'Subjects', 'book-open'),
                        $item('admin.time-studies.index', 'Schedule', 'clock-3'),
                        $item('admin.student-study.index', 'Student Progress', 'graduation-cap'),
                        $item('admin.finance.index', 'School Finance', 'wallet'),
                        $item('admin.contacts.index', 'Messages', 'mail', $contactUnread ?? 0),
                    ];

                    if (!$isStaffUser) {
                        array_unshift($managementItems, $item('admin.admin-staff.index', 'Admin / Staff', 'shield'));
                        $managementItems[] = $item('admin.mission.index', 'Mission', 'flag', $missionUnread ?? 0);
                    } else {
                        $managementItems[] = $item('staff.missions.index', 'Mission Events', 'flag');
                    }

                    $mainItems = [$item('admin.dashboard', 'Dashboard', 'layout-dashboard')];

                    if (!$isStaffUser) {
                        $mainItems[] = $item('admin.reports', 'Reports', 'chart-line');
                    }

                    $sections = [
                        [
                            'title' => 'Main',
                            'items' => $mainItems,
                        ],
                        [
                            'title' => 'Management',
                            'items' => $managementItems,
                        ],
                        [
                            'title' => 'Attendance',
                            'items' => [
                                $item(
                                    'admin.attendance.teachers.index',
                                    'Teacher Attendance',
                                    'clipboard-check',
                                    $teacherAttendanceUnread ?? 0,
                                ),
                                $item(
                                    'admin.attendance.index',
                                    'Student Attendance',
                                    'calendar-check',
                                    $studentAttendanceUnread ?? 0,
                                ),
                            ],
                        ],
                        [
                            'title' => 'System',
                            'items' => [
                                $item('admin.homepage.index', 'Homepage UI', 'palette'),
                                $item('admin.settings', 'Settings', 'settings'),
                            ],
                        ],
                    ];
                @endphp

                <nav class="space-y-4">
                    @foreach ($sections as $section)
                        <div class="space-y-1.5">
                            <div x-show="!collapsed"
                                class="px-3 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">
                                {{ $section['title'] }}
                            </div>

                            <div class="space-y-1.5">
                                @foreach ($section['items'] as $l)
                                    <a href="{{ route($l['route']) }}"
                                        class="group relative flex items-center rounded-2xl transition duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-100 {{ $l['active'] ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-[0_14px_30px_-18px_rgba(79,70,229,0.8)]' : 'text-slate-700 hover:-translate-y-px hover:bg-white hover:text-slate-900 hover:shadow-[0_12px_24px_-18px_rgba(15,23,42,0.35)]' }}"
                                        :class="collapsed ? 'justify-center px-2 py-3' : 'gap-3 px-4 py-3'">

                                        @if ($l['active'])
                                            <span
                                                class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-white"></span>
                                        @endif

                                        <span
                                            class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl transition {{ $l['active'] ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-indigo-50 group-hover:text-indigo-600' }}">
                                            <i class="{{ $adminNavIconClasses[$l['icon']] ?? 'fa-solid fa-gear' }} text-base"
                                                aria-hidden="true"></i>

                                            @if ($l['icon'] === 'mail' && ($l['badge'] ?? 0) > 0)
                                                <span
                                                    class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 {{ $l['active'] ? 'ring-indigo-600' : 'ring-white' }}"></span>
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
                                            class="pointer-events-none absolute left-full ml-3 hidden whitespace-nowrap rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-lg group-hover:block lg:group-hover:block">
                                            {{ $l['label'] }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>
            {{-- Footer --}}
            <div class="border-t border-slate-100 p-4 shrink-0" x-show="!collapsed" x-transition>
                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-600 p-4 text-white shadow-lg">
                    <div class="text-sm font-bold">{{ $adminShellLabel }} Panel</div>
                    <div class="mt-1 text-xs text-white/80">
                        Manage students, teachers, classes, and attendance easily.
                    </div>
                    <a href="#"
                        class="mt-3 inline-flex items-center rounded-xl bg-white/15 px-3 py-2 text-xs font-semibold hover:bg-white/20">
                        Open Guide
                    </a>
                </div>
            </div>
        </aside>
        {{-- MAIN AREA --}}
        <div class="min-h-screen flex flex-col overflow-x-hidden" :class="collapsed ? 'lg:pl-20' : 'lg:pl-72'">

            {{-- TOPBAR --}}
            <header class="sticky top-0 z-30 bg-slate-100/80 backdrop-blur border-b border-slate-200">
                <div class="min-h-16 px-4 sm:px-4 lg:px-4 flex flex-wrap items-center gap-2 sm:gap-3 md:flex-nowrap">

                    {{-- Mobile menu button --}}
                    <button
                        class="lg:hidden p-2 rounded-xl hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        @click="mobileOpen=true" aria-label="Open menu" type="button">
                        <svg class="h-6 w-6 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z" />
                        </svg>
                    </button>

                    {{-- Search --}}
                    <div class="flex-1 min-w-0">
                        <div class="relative max-w-xl">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M10 2a8 8 0 1 0 5.29 14.06l4.33 4.33 1.41-1.41-4.33-4.33A8 8 0 0 0 10 2Zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12Z" />
                                </svg>
                            </span>
                            <input type="text" placeholder="Search"
                                class="w-full rounded-full bg-white border border-slate-200 pl-11 pr-4 py-2.5 text-sm
                                    outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-200" />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="ml-auto flex shrink-0 items-center gap-1.5 sm:gap-2 md:ml-0">
                        {{-- Notifications --}}
                        <div class="relative" @click.outside="notifOpen=false">
                            <button
                                class="relative p-2 rounded-xl bg-white border border-slate-200 hover:shadow-sm
                                           focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                @click="profileOpen=false; messageOpen=false; notifOpen=!notifOpen"
                                aria-label="Notifications" type="button">
                                <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
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
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl
                                       sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-80">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                    <div class="text-sm font-bold text-slate-900">Notifications</div>
                                    <span class="text-xs text-slate-500">Latest</span>
                                </div>

                                <div class="nav-scrollbar max-h-80 overflow-auto">
                                    @forelse(($navNotifs ?? []) as $n)
                                        <a href="{{ $n->url ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50">
                                            <div class="flex items-start gap-3">
                                                <span
                                                    class="mt-2 h-2 w-2 rounded-full {{ $n->is_read ? 'bg-slate-300' : 'bg-indigo-600' }}"></span>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-slate-800">
                                                        {{ $n->title }}</div>
                                                    <div class="text-xs text-slate-500 truncate">{{ $n->message }}
                                                    </div>
                                                    <div class="text-[11px] text-slate-400 mt-1">
                                                        {{ $n->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-sm text-slate-500">No notifications yet.
                                        </div>
                                    @endforelse
                                </div>

                                <div class="px-4 py-3 border-t border-slate-100">
                                    <form method="POST" action="{{ route('admin.notifications.readAll') }}">
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
                                           focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                @click="notifOpen=false; profileOpen=false; messageOpen=!messageOpen"
                                aria-label="Messages" type="button">
                                <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
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
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl
                                       sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-96">

                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                    <div class="text-sm font-bold text-slate-900">Contact Messages</div>
                                    @if (($contactUnread ?? 0) > 0)
                                        <span class="text-xs font-semibold text-amber-600">{{ $contactUnread }}
                                            unread</span>
                                    @endif
                                </div>

                                <div class="nav-scrollbar max-h-80 overflow-auto">
                                    @forelse(($navContacts ?? []) as $contact)
                                        <a href="{{ route('admin.contacts.index') }}"
                                            class="block px-4 py-3 hover:bg-slate-50">
                                            <div class="flex items-start gap-3">
                                                <span
                                                    class="mt-2 h-2 w-2 rounded-full {{ $contact->is_read ? 'bg-slate-300' : 'bg-amber-500' }}"></span>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <div class="truncate text-sm font-semibold text-slate-800">
                                                            {{ $contact->name }}</div>
                                                        <div class="text-[11px] text-slate-400">
                                                            {{ $contact->created_at->diffForHumans() }}</div>
                                                    </div>
                                                    <div class="truncate text-xs font-semibold text-slate-600">
                                                        {{ $contact->subject }}</div>
                                                    <div class="truncate text-xs text-slate-500">
                                                        {{ $contact->message }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-sm text-slate-500">No contact messages
                                            yet.</div>
                                    @endforelse
                                </div>

                                <div class="grid gap-2 px-4 py-3 border-t border-slate-100">
                                    <a href="{{ route('admin.contacts.index') }}"
                                        class="w-full rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800">
                                        Open Contact Inbox
                                    </a>
                                    @if (($contactUnread ?? 0) > 0)
                                        <form method="POST" action="{{ route('admin.contacts.readAll') }}">
                                            @csrf
                                            <button
                                                class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
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
                                       focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                @click="notifOpen=false; messageOpen=false; profileOpen=!profileOpen"
                                aria-label="Open profile menu" type="button">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}"
                                    onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                                    alt="avatar">

                                <div class="leading-tight hidden lg:block text-left">
                                    <div class="max-w-[10rem] truncate text-sm font-semibold text-slate-900">
                                        {{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</div>
                                </div>

                                <svg class="hidden h-4 w-4 text-slate-500 sm:block" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path d="M7 10l5 5 5-5H7z" />
                                </svg>
                            </button>

                            <div x-show="profileOpen" x-cloak x-transition.origin.top.right
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl
                                       sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-56">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
                                </div>

                                <a href="{{ route('admin.settings') }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <span class="h-8 w-8 rounded-xl bg-slate-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                        </svg>
                                    </span>
                                    Settings
                                </a>

                                <div class="border-t border-slate-100"></div>

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

    {{-- Alpine logic (works even if Alpine is bundled in app.js) --}}
    <script>
        function adminShell() {
            return {
                mobileOpen: false,
                collapsed: false,

                notifOpen: false,
                messageOpen: false,
                profileOpen: false,

                init() {
                    // close sidebar drawer when switch to desktop
                    const mq = window.matchMedia('(min-width: 1024px)');
                    const handler = () => {
                        if (mq.matches) this.mobileOpen = false;
                    };
                    handler();
                    mq.addEventListener?.('change', handler);

                    // optional: remember collapsed state
                    const saved = localStorage.getItem('admin_sidebar_collapsed');
                    if (saved !== null) this.collapsed = saved === '1';
                },

                closeAll() {
                    this.notifOpen = false;
                    this.messageOpen = false;
                    this.profileOpen = false;
                },

                toggleSidebar() {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('admin_sidebar_collapsed', this.collapsed ? '1' : '0');
                },
            }
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
