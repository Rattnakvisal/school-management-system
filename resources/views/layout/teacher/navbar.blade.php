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
    <div x-data="teacherShell()" x-init="init()" @keydown.escape.window="closeAll()" class="min-h-screen">

        {{-- MOBILE OVERLAY --}}
        <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 lg:hidden"
            @click="mobileOpen=false" aria-hidden="true"></div>

        {{-- SIDEBAR --}}
        <aside class="fixed inset-y-0 left-0 z-50 bg-white border-r border-slate-200 shadow-sm
                      transition-all duration-300 lg:translate-x-0 h-screen flex flex-col transform"
            :class="[
                mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                sidebarCollapsed ? 'w-72 lg:w-20' : 'w-72'
            ]"
            aria-label="Sidebar">

            {{-- Header --}}
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-200 shrink-0">
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-600/10 flex items-center justify-center shrink-0">
                        <svg class="h-6 w-6 text-indigo-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 4v9l10 5 10-5V4l-10 5Z" />
                        </svg>
                    </div>

                    {{-- Brand text (hide when collapsed) --}}
                    <div class="min-w-0" x-show="!sidebarCollapsed" x-transition>
                        <div class="text-lg font-extrabold tracking-tight text-slate-900 truncate">Schooli</div>
                        <div class="text-xs text-slate-500 -mt-0.5 truncate">Teacher Panel</div>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    {{-- Collapse/Expand button (desktop) --}}
                    <button type="button"
                        class="hidden lg:inline-flex items-center justify-center p-2 rounded-xl hover:bg-slate-100
                               focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        @click="toggleSidebar()"
                        :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">

                        {{-- Collapse icon --}}
                        <svg x-show="!sidebarCollapsed" class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="currentColor"
                            aria-hidden="true">
                            <path d="M4 6h16v2H4V6Zm0 5h10v2H4v-2Zm0 5h16v2H4v-2Z" />
                        </svg>

                        {{-- Expand icon --}}
                        <svg x-show="sidebarCollapsed" class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="currentColor"
                            aria-hidden="true">
                            <path d="M4 6h10v2H4V6Zm0 5h16v2H4v-2Zm0 5h10v2H4v-2Z" />
                        </svg>
                    </button>

                    {{-- Close button (mobile) --}}
                    <button class="lg:hidden p-2 rounded-xl hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        @click="mobileOpen=false" aria-label="Close" type="button">
                        ✕
                    </button>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="px-3 py-4 space-y-1 flex-1 overflow-y-auto">
                @php
                    $item = fn($route, $label, $icon) => [
                        'route' => $route,
                        'label' => $label,
                        'icon' => $icon,
                        'active' => request()->routeIs($route),
                    ];

                    $links = [
                        $item('teacher.dashboard', 'Dashboard', 'home'),
                        $item('teacher.classes.index', 'My Classes', 'grid'),
                        $item('teacher.schedule.index', 'Schedule', 'clock'),
                        $item('teacher.attendance.index', 'Attendance', 'check'),
                        $item('teacher.law-requests.index', 'Law Requests', 'document'),
                        $item('teacher.assignments.index', 'Assignments', 'clipboard'),
                        $item('teacher.grades.index', 'Grades', 'book'),
                        $item('teacher.notices.index', 'Notices', 'bell'),
                        $item('teacher.settings', 'Settings', 'cog'),
                    ];
                @endphp

                @foreach ($links as $l)
                    <a href="{{ route($l['route']) }}"
                        class="relative group flex items-center rounded-2xl py-3 text-sm font-semibold transition
                               focus:outline-none focus:ring-4 focus:ring-indigo-100
                               {{ $l['active'] ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}"
                        :class="sidebarCollapsed ? 'px-3 justify-center' : 'px-4 gap-3'">

                        {{-- Active indicator bar --}}
                        <span class="absolute left-1 top-1/2 -translate-y-1/2 h-8 w-1 rounded-full
                                     {{ $l['active'] ? 'bg-white/70' : 'bg-transparent group-hover:bg-slate-300' }}"></span>

                        {{-- Icon --}}
                        <span
                            class="h-9 w-9 rounded-xl flex items-center justify-center relative shrink-0
                                   {{ $l['active'] ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-white' }}">

                            @switch($l['icon'])
                                @case('home')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
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

                                @case('users')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M17 21v-2a4 4 0 00-3-3.87" />
                                        <path d="M7 21v-2a4 4 0 013-3.87" />
                                        <circle cx="12" cy="7" r="4" />
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
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <rect x="9" y="2" width="6" height="4" />
                                        <path d="M4 7h16v15H4z" />
                                    </svg>
                                @break

                                @case('book')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M2 7h20" />
                                        <path d="M2 7v13h20V7" />
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
                                        <path d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                    </svg>
                            @endswitch
                        </span>

                        {{-- Label (hide when collapsed) --}}
                        <span class="flex-1 truncate" x-show="!sidebarCollapsed">{{ $l['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- Footer card (hide when collapsed) --}}
            <div class="p-4 border-t border-slate-100 shrink-0" x-show="!sidebarCollapsed" x-transition>
                <div class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white shadow-lg">
                    <div class="text-sm font-semibold">Teacher Mode</div>
                    <div class="text-xs text-white/80 mt-1">Manage classes, attendance and student grades.</div>
                </div>
            </div>
        </aside>

        {{-- MAIN AREA --}}
        <div class="min-h-screen flex flex-col" :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'">

            {{-- TOPBAR --}}
            <header class="sticky top-0 z-30 bg-slate-100/80 backdrop-blur border-b border-slate-200">
                <div class="h-16 px-4 sm:px-6 flex items-center gap-3">

                    {{-- Mobile menu button --}}
                    <button class="lg:hidden p-2 rounded-xl hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        @click="mobileOpen=true" aria-label="Open menu" type="button">
                        <svg class="h-6 w-6 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z" />
                        </svg>
                    </button>

                    {{-- Search --}}
                    <div class="hidden min-w-0 flex-1 sm:block">
                        <div class="relative max-w-xl">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 2a8 8 0 1 0 5.29 14.06l4.33 4.33 1.41-1.41-4.33-4.33A8 8 0 0 0 10 2Zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12Z" />
                                </svg>
                            </span>
                            <input type="text" placeholder="Search students, classes..."
                                class="w-full rounded-full bg-white border border-slate-200 pl-11 pr-4 py-2.5 text-sm outline-none
                                       focus:ring-4 focus:ring-indigo-100 focus:border-indigo-200" />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">

                        {{-- Notifications --}}
                        <div class="relative" @click.outside="notifOpen=false">
                            <button class="relative p-2 rounded-xl bg-white border border-slate-200 hover:shadow-sm
                                           focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                @click="profileOpen=false; notifOpen=!notifOpen" aria-label="Notifications" type="button">
                                <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6V11a6 6 0 1 0-12 0v5L4 18v1h16v-1l-2-2Z" />
                                </svg>

                                @if (($navUnread ?? 0) > 0)
                                    <span class="absolute -top-1 -right-1 h-3 w-3 rounded-full bg-red-500 animate-pulse ring-2 ring-white"></span>
                                @endif
                            </button>

                            <div x-show="notifOpen" x-cloak x-transition.origin.top.right
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-xl
                                       sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-80">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                    <div class="text-sm font-bold text-slate-900">Notifications</div>
                                    <span class="text-xs text-slate-500">Latest</span>
                                </div>

                                <div class="max-h-80 overflow-auto">
                                    @forelse(($navNotifs ?? []) as $n)
                                        <a href="{{ $n->url ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50">
                                            <div class="flex items-start gap-3">
                                                <span class="mt-2 h-2 w-2 rounded-full {{ $n->is_read ? 'bg-slate-300' : 'bg-indigo-600' }}"></span>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-slate-800">{{ $n->title }}</div>
                                                    <div class="text-xs text-slate-500 truncate">{{ $n->message }}</div>
                                                    <div class="text-[11px] text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-sm text-slate-500">No notifications yet.</div>
                                    @endforelse
                                </div>

                                <div class="px-4 py-3 border-t border-slate-100">
                                    <form method="POST" action="{{ route('teacher.notifications.readAll') }}">
                                        @csrf
                                        <button class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                            Mark all as read
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Profile --}}
                        <div class="relative" @click.outside="profileOpen=false">
                            <button
                                class="flex items-center gap-3 rounded-full bg-white border border-slate-200 px-3 py-2 hover:shadow-sm sm:ml-2
                                       focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                @click="notifOpen=false; profileOpen=!profileOpen" aria-label="Open profile menu" type="button">
                                <img class="h-8 w-8 rounded-full object-cover"
                                    src="{{ auth()->user()->avatar_url }}"
                                    onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                                    alt="avatar">

                                <div class="leading-tight hidden sm:block text-left">
                                    <div class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</div>
                                </div>

                                <svg class="h-4 w-4 text-slate-400 transition-transform duration-200"
                                    :class="profileOpen ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </button>

                            <div x-show="profileOpen" x-cloak x-transition.origin.top.right
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-xl
                                       sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-56">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</div>
                                </div>

                                <a href="{{ route('teacher.settings') }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <span class="h-8 w-8 rounded-xl bg-indigo-50 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                        </svg>
                                    </span>
                                    Settings
                                </a>

                                <a href="{{ route('teacher.attendance.index') }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <span class="h-8 w-8 rounded-xl bg-emerald-50 flex items-center justify-center">
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
                                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8v2h8v14h-8v2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z" />
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
                    // close drawer when switching to desktop and keep collapse desktop-only
                    const mq = window.matchMedia('(min-width: 1024px)');
                    const handler = () => {
                        this.isDesktop = mq.matches;
                        if (mq.matches) this.mobileOpen = false;
                    };
                    handler();
                    mq.addEventListener?.('change', handler);

                    // remember sidebar state
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
