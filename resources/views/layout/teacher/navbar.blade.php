<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title ?? 'Teacher Dashboard' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full overflow-x-hidden bg-slate-100 text-slate-800">
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
            class="fixed inset-y-0 left-0 z-50 flex h-screen flex-col overflow-hidden border-r border-slate-200/80 bg-[radial-gradient(circle_at_top,rgba(79,70,229,0.10),transparent_30%),linear-gradient(180deg,#ffffff_0%,#f8fbff_100%)] shadow-[0_24px_50px_-34px_rgba(15,23,42,0.35)] transition-all duration-300"
            :class="[
                isDesktop
                    ? (sidebarCollapsed ? 'translate-x-0 w-24' : 'translate-x-0 w-72')
                    : (mobileOpen ? 'translate-x-0 w-[84vw] max-w-[19rem]' : '-translate-x-full w-[84vw] max-w-[19rem]')
            ]"
            aria-label="Sidebar">

            {{-- Header --}}
            <div class="flex h-20 items-center justify-between border-b border-slate-200/80 px-5 shrink-0">
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <img src="{{ asset('images/techbridge-logo-mark.svg') }}" alt="TechBridge Academy logo"
                        class="h-12 w-12 shrink-0 object-contain drop-shadow-[0_12px_24px_-14px_rgba(24,80,200,0.45)]" />

                    <div class="min-w-0" x-show="!sidebarCollapsed" x-transition>
                        <div class="truncate text-lg font-black tracking-tight text-slate-900">TechBridge Academy</div>
                        <div class="truncate text-xs font-medium text-slate-500">Teacher Panel</div>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    <button type="button"
                        class="hidden lg:inline-flex h-10 w-10 items-center justify-center rounded-2xl text-slate-600 transition hover:bg-white hover:text-slate-900 hover:shadow-[0_10px_20px_-16px_rgba(15,23,42,0.35)] focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        @click="toggleSidebar()" :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                        <svg x-show="!sidebarCollapsed" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"
                            aria-hidden="true">
                            <path d="M4 6h16v2H4V6Zm0 5h10v2H4v-2Zm0 5h16v2H4v-2Z" />
                        </svg>
                        <svg x-show="sidebarCollapsed" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"
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
            <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
                @php
                    $item = fn($route, $label, $icon) => [
                        'route' => $route,
                        'label' => $label,
                        'icon' => $icon,
                        'active' => request()->routeIs($route),
                    ];

                    $sections = [
                        [
                            'title' => 'Main',
                            'items' => [$item('teacher.dashboard', 'Dashboard', 'home')],
                        ],
                        [
                            'title' => 'Learning',
                            'items' => [
                                $item('teacher.classes.index', 'My Classes', 'grid'),
                                $item('teacher.schedule.index', 'Schedule', 'clock'),
                                $item('teacher.attendance.index', 'Attendance', 'check'),
                            ],
                        ],
                        [
                            'title' => 'Requests',
                            'items' => [
                                $item('teacher.law-requests.index', 'Law Requests', 'document'),
                                $item('teacher.assignments.index', 'Assignments', 'clipboard'),
                                $item('teacher.grades.index', 'Grades', 'book'),
                                $item('teacher.notices.index', 'Notifications', 'bell'),
                            ],
                        ],
                        [
                            'title' => 'System',
                            'items' => [$item('teacher.settings', 'Settings', 'cog')],
                        ],
                    ];
                @endphp

                @foreach ($sections as $section)
                    <div class="space-y-1.5">
                        <div x-show="!sidebarCollapsed" x-transition
                            class="px-3 text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">
                            {{ $section['title'] }}
                        </div>

                        <div class="space-y-1.5">
                            @foreach ($section['items'] as $l)
                                <a href="{{ route($l['route']) }}"
                                    class="group relative flex items-center rounded-2xl transition duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-100 {{ $l['active'] ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-[0_14px_30px_-18px_rgba(79,70,229,0.8)]' : 'text-slate-700 hover:-translate-y-px hover:bg-white hover:text-slate-900 hover:shadow-[0_12px_24px_-18px_rgba(15,23,42,0.35)]' }}"
                                    :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'gap-3 px-4 py-3'">

                                    @if ($l['active'])
                                        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-white"></span>
                                    @endif

                                    <span
                                        class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl transition
                                        {{ $l['active'] ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-indigo-50 group-hover:text-indigo-600' }}">
                                        @switch($l['icon'])
                                            @case('home')
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 3 2 12h3v9h6v-6h2v6h6v-9h3L12 3Z" />
                                                </svg>
                                            @break

                                            @case('grid')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <rect x="3" y="3" width="7" height="7" />
                                                    <rect x="14" y="3" width="7" height="7" />
                                                    <rect x="14" y="14" width="7" height="7" />
                                                    <rect x="3" y="14" width="7" height="7" />
                                                </svg>
                                            @break

                                            @case('clock')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" />
                                                    <path d="M12 7v5l3 3" />
                                                </svg>
                                            @break

                                            @case('check')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                            @break

                                            @case('clipboard')
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M16 2H8v2H5v18h14V4h-3V2Zm1 18H7V6h10v14Z" />
                                                </svg>
                                            @break

                                            @case('book')
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M18 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12v-2H6V4h12v16h2V4a2 2 0 0 0-2-2Z" />
                                                </svg>
                                            @break

                                            @case('bell')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path d="M18 8a6 6 0 00-12 0v5l-2 2h16l-2-2z" />
                                                    <path d="M13.73 21a2 2 0 01-3.46 0" />
                                                </svg>
                                            @break

                                            @case('document')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                    <path d="M14 2v6h6" />
                                                    <path d="M8 13h8" />
                                                    <path d="M8 17h8" />
                                                </svg>
                                            @break

                                            @default
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                                </svg>
                                        @endswitch
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
                                        class="pointer-events-none absolute left-full ml-3 hidden whitespace-nowrap rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-lg group-hover:block lg:group-hover:block">
                                        {{ $l['label'] }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            {{-- Footer card --}}
            <div class="hidden shrink-0 border-t border-slate-200/80 p-4 sm:block" x-show="!sidebarCollapsed" x-transition>
                <div
                    class="rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-600 p-4 text-white shadow-[0_18px_40px_-24px_rgba(79,70,229,0.9)]">
                    <div class="text-[11px] font-black uppercase tracking-[0.22em] text-white/70">Teacher Focus</div>
                    <div class="mt-2 text-sm font-semibold leading-6 text-white/95">
                        Manage classes, attendance, and student grades with a clean workflow.
                    </div>
                    <a href="{{ route('teacher.settings') }}"
                        class="mt-4 inline-flex items-center rounded-full bg-white/15 px-3 py-2 text-xs font-semibold hover:bg-white/20">
                        Open Settings
                    </a>
                </div>
            </div>
        </aside>

        {{-- MAIN AREA --}}
        <div class="min-h-screen flex flex-col transition-[padding] duration-300"
            :class="isDesktop ? (sidebarCollapsed ? 'pl-24' : 'pl-72') : 'pl-0'">

            {{-- TOPBAR --}}
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-slate-100/80 backdrop-blur">
                <div class="flex h-16 items-center gap-3 px-4 sm:px-6">
                    <button
                        class="rounded-xl p-2 text-slate-600 transition hover:bg-white hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 lg:hidden"
                        @click="mobileOpen = true" aria-label="Open menu" type="button">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z" />
                        </svg>
                    </button>

                    <div class="hidden min-w-0 flex-1 sm:block">
                        <div class="relative max-w-xl">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M10 2a8 8 0 1 0 5.29 14.06l4.33 4.33 1.41-1.41-4.33-4.33A8 8 0 0 0 10 2Zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12Z" />
                                </svg>
                            </span>
                            <input type="text" placeholder="Search students, classes..."
                                class="w-full rounded-full border border-slate-200 bg-white py-2.5 pl-11 pr-4 text-sm outline-none focus:border-indigo-200 focus:ring-4 focus:ring-indigo-100" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
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

                                <div id="teacher-notif-list" class="max-h-80 overflow-auto">
                                    @forelse (($navNotifs ?? []) as $n)
                                        <a href="{{ trim((string) ($n->url ?? '')) !== '' ? $n->url : route('teacher.notices.index') }}"
                                            class="block px-4 py-3 hover:bg-slate-50">
                                            <div class="flex items-start gap-3">
                                                <span
                                                    class="mt-2 h-2 w-2 rounded-full {{ $n->is_read ? 'bg-slate-300' : 'bg-indigo-600' }}"></span>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-slate-800">{{ $n->title }}
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
        function teacherShell() {
            return {
                mobileOpen: false,
                collapsed: false,
                isDesktop: false,

                notifOpen: false,
                profileOpen: false,

                get sidebarCollapsed() {
                    return this.isDesktop && this.collapsed;
                },

                init() {
                    const mq = window.matchMedia('(min-width: 1024px)');
                    const handler = () => {
                        this.isDesktop = mq.matches;
                        if (mq.matches) this.mobileOpen = false;
                    };
                    handler();
                    mq.addEventListener?.('change', handler);

                    const saved = localStorage.getItem('teacher_sidebar_collapsed');
                    if (saved !== null) this.collapsed = saved === '1';
                },

                closeAll() {
                    this.notifOpen = false;
                    this.profileOpen = false;
                },

                toggleSidebar() {
                    if (!this.isDesktop) return;
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('teacher_sidebar_collapsed', this.collapsed ? '1' : '0');
                }
            }
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
