<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? ($schoolBrandName ?? 'TechBridge Academy') . ' | Student Dashboard' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/navbar/student-navbar.js'])
</head>

<body class="min-h-full overflow-x-hidden bg-slate-100 text-slate-800" data-loading-shell>
    <div id="admin-swirling-loader" class="admin-swirling-loader" role="status" aria-live="polite"
        aria-label="Loading student panel">
        <svg class="admin-swirling-loader__icon" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true" focusable="false">
            <circle class="loading-ui-swirling-circle" cx="400" cy="400" r="200" fill="none"
                stroke="currentColor" stroke-linecap="round" stroke-width="50" />
        </svg>
    </div>

    <div x-data="studentShell()" x-init="init()" @keydown.escape.window="closeAll()"
        class="min-h-screen overflow-x-hidden" data-student-notification-root
        data-notification-poll-url="{{ route('student.notifications.poll') }}"
        data-notification-readall-url="{{ route('student.notifications.readAll') }}"
        data-notification-latest-id="{{ (int) ($navNotifs->first()->id ?? 0) }}"
        data-notification-unread-count="{{ (int) ($navUnread ?? 0) }}">

        {{-- MOBILE OVERLAY --}}
        <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 lg:hidden"
            @click="mobileOpen = false" aria-hidden="true"></div>

        {{-- SIDEBAR --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 flex h-screen flex-col border-r border-slate-200 bg-white shadow-sm transition-all duration-300 lg:translate-x-0"
            :class="[
                mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                sidebarCollapsed ? 'w-16' : 'w-56'
            ]"
            aria-label="Sidebar">

            {{-- Header --}}
            <div class="flex h-14 items-center border-b border-slate-200 shrink-0"
                :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                <a href="{{ route('student.dashboard') }}" class="flex min-w-0 items-center gap-2"
                    x-show="!sidebarCollapsed" x-transition>
                    <img src="{{ $schoolBrandLogo ?? asset('images/techbridge-logo-mark.svg') }}"
                        alt="{{ $schoolBrandName ?? 'TechBridge Academy' }} logo"
                        class="h-9 w-9 shrink-0 object-contain" />
                    <div class="min-w-0">
                        <div class="truncate text-sm font-extrabold tracking-tight text-slate-900">
                            {{ $schoolBrandName ?? 'TechBridge Academy' }}</div>
                        <div class="truncate text-xs text-slate-500">{{ $schoolBrandTagline ?? 'Student Panel' }}</div>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    <button type="button"
                        class="hidden h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 lg:inline-flex"
                        @click="toggleSidebar()" :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                        <svg x-show="!sidebarCollapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 6h16v2H4V6Zm0 5h10v2H4v-2Zm0 5h16v2H4v-2Z" />
                        </svg>
                        <svg x-show="sidebarCollapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 6h10v2H4V6Zm0 5h16v2H4v-2Zm0 5h10v2H4v-2Z" />
                        </svg>
                    </button>

                    <button
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 lg:hidden"
                        @click="mobileOpen = false" aria-label="Close sidebar" type="button">
                        &times;
                    </button>
                </div>
            </div>

            {{-- NAVIGATION --}}
            <nav class="nav-scrollbar flex-1 space-y-3 overflow-y-auto py-3" :class="sidebarCollapsed ? 'px-2' : 'px-3'">
                @php
                    $item = fn($route, $label, $icon) => [
                        'route' => $route,
                        'label' => $label,
                        'icon' => $icon,
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

                    $sections = [
                        [
                            'key' => 'main',
                            'title' => 'Main',
                            'items' => [$item('student.dashboard', 'Dashboard', 'home')],
                        ],
                        [
                            'key' => 'management',
                            'title' => 'Management',
                            'items' => [
                                $item('student.subjects.index', 'My Subjects', 'book'),
                                $item('student.grades.index', 'My Grades', 'clipboard'),
                            ],
                        ],
                        [
                            'key' => 'requests',
                            'title' => 'Requests',
                            'items' => [
                                $item('student.law-requests.index', 'Law Requests', 'document'),
                                $item('student.assignment.index', 'Assignment', 'clipboard'),
                                $item('student.notices.index', 'Notifications', 'bell'),
                            ],
                        ],
                        [
                            'key' => 'system',
                            'title' => 'System',
                            'items' => [$item('student.settings', 'Settings', 'cog')],
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
                                        <i class="{{ $studentNavIconClasses[$l['icon']] ?? 'fa-solid fa-circle' }}"
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

        {{-- MAIN --}}
        <div class="flex min-h-screen flex-col overflow-x-hidden transition-[padding] duration-300"
            :class="isDesktop ? (sidebarCollapsed ? 'pl-16' : 'pl-56') : 'pl-0'">

            {{-- TOPBAR --}}
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-slate-100/80 backdrop-blur">
                <div class="flex min-h-16 flex-wrap items-center gap-2 px-4 sm:gap-3 md:flex-nowrap">

                    <button
                        class="rounded-xl p-2 text-slate-600 transition hover:bg-white hover:text-slate-900 lg:hidden"
                        @click="mobileOpen = true" aria-label="Open sidebar" type="button">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 6h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="min-w-0 flex-1">
                        <div class="relative max-w-xl">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M10 2a8 8 0 1 0 5.29 14.06l4.33 4.33 1.41-1.41-4.33-4.33A8 8 0 0 0 10 2Zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12Z" />
                                </svg>
                            </span>
                            <input type="text" placeholder="Search"
                                class="w-full rounded-full border border-slate-200 bg-white py-2.5 pl-11 pr-4 text-sm outline-none focus:border-indigo-200 focus:ring-4 focus:ring-indigo-100" />
                        </div>
                    </div>

                    <div class="ml-auto flex shrink-0 items-center gap-1.5 sm:gap-2 md:ml-0">
                        <div class="relative" @click.outside="notifOpen=false">
                            <button
                                class="relative inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 transition hover:shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                @click="profileOpen = false; notifOpen = !notifOpen" aria-label="Notifications"
                                type="button">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6V11a6 6 0 1 0-12 0v5L4 18v1h16v-1l-2-2Z" />
                                </svg>

                                <span id="student-notif-badge"
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

                                <div id="student-notif-list" class="nav-scrollbar max-h-80 overflow-auto">
                                    @forelse (($navNotifs ?? []) as $n)
                                        <a href="{{ trim((string) ($n->url ?? '')) !== '' ? $n->url : route('student.notices.index') }}"
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
                                        <div id="student-notif-empty"
                                            class="px-4 py-8 text-center text-sm text-slate-500">No notifications yet.
                                        </div>
                                    @endforelse
                                </div>

                                <div class="border-t border-slate-100 px-4 py-3">
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <a href="{{ route('student.notices.index') }}"
                                            class="rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800">
                                            View all
                                        </a>
                                        <form method="POST" action="{{ route('student.notifications.readAll') }}">
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

                        {{-- PROFILE --}}
                        <div class="relative" @click.outside="profileOpen=false">
                            <button
                                class="group flex max-w-[13rem] items-center gap-2 rounded-full border border-slate-200 bg-white px-2.5 py-2 transition hover:shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 sm:max-w-none sm:gap-3 sm:px-3"
                                @click="notifOpen = false; profileOpen=!profileOpen" aria-label="Open profile menu"
                                type="button">
                                <img class="h-8 w-8 rounded-full object-cover ring-2 ring-indigo-100 transition group-hover:ring-indigo-300"
                                    src="{{ auth()->user()->avatar_url }}"
                                    onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                                    alt="avatar">

                                <div class="hidden min-w-0 text-left leading-tight sm:block">
                                    <div class="max-w-[10rem] truncate text-sm font-semibold text-slate-900">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <div class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</div>
                                </div>

                                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200"
                                    :class="profileOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </button>

                            <div x-show="profileOpen" x-cloak x-transition.origin.top.right
                                class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-3 sm:w-64">

                                <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3">
                                    <img class="h-10 w-10 rounded-full object-cover ring-2 ring-indigo-100"
                                        src="{{ auth()->user()->avatar_url }}"
                                        onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                                        alt="avatar">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-bold text-slate-900">
                                            {{ auth()->user()->name }}
                                        </div>
                                        <div class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</div>
                                    </div>
                                </div>

                                <a href="{{ route('student.settings') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-50">
                                        <svg class="h-4 w-4 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                        </svg>
                                    </span>

                                    Settings
                                </a>

                                <div class="border-t border-slate-100"></div>

                                <div class="p-2">
                                    <a href="{{ route('logout') }}"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100">

                                        <svg class="h-4 w-4 text-red-600" viewBox="0 0 24 24" fill="currentColor">
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

            <main class="p-4 sm:p-6" data-page-animate>
                @yield('page')
            </main>
        </div>
    </div>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
