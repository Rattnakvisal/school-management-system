<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Student Dashboard' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full overflow-x-hidden bg-slate-100 text-slate-800">
    <div x-data="{
        mobile: false,
        notifOpen: false,
        profileOpen: false,
        closeAll() {
            this.notifOpen = false;
            this.profileOpen = false;
        }
    }" @keydown.escape.window="closeAll()" class="min-h-screen">

        {{-- MOBILE OVERLAY --}}
        <div x-show="mobile" class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="mobile = false"></div>

        {{-- SIDEBAR --}}
        <aside
            class="layout-rail fixed inset-y-0 left-0 z-50 flex h-screen w-72 flex-col overflow-hidden border-r border-slate-200 bg-white shadow-sm transition-all duration-300 lg:translate-x-0"
            :class="mobile ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            {{-- Header --}}
            <div class="flex h-16 items-center justify-between border-b border-slate-200 px-4 shrink-0">
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm">
                        <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2 2 7l10 5 10-5-10-5Z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="truncate text-base font-extrabold tracking-tight text-slate-900">Schooli</div>
                        <div class="truncate text-xs text-slate-500">Student Panel</div>
                    </div>
                </a>

                <button
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 lg:hidden"
                    @click="mobile = false" aria-label="Close sidebar">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- NAVIGATION --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4">
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
                            'items' => [$item('student.dashboard', 'Dashboard', 'home')],
                        ],
                        [
                            'title' => 'Management',
                            'items' => [
                                $item('student.subjects.index', 'My Subjects', 'book'),
                                $item('student.grades.index', 'My Grades', 'clipboard'),
                            ],
                        ],
                        [
                            'title' => 'Attendance',
                            'items' => [
                                $item('student.attendance.index', 'Attendance', 'check'),
                                $item('student.schedule.index', 'Schedule', 'calendar'),
                                $item('student.notices.index', 'Notices', 'bell'),
                            ],
                        ],
                        [
                            'title' => 'System',
                            'items' => [$item('student.settings', 'Settings', 'cog')],
                        ],
                    ];
                @endphp

                @foreach ($sections as $section)
                    <div class="space-y-1.5">
                        <div class="px-3 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">
                            {{ $section['title'] }}
                        </div>

                        <div class="space-y-1.5">
                            @foreach ($section['items'] as $l)
                                <a href="{{ route($l['route']) }}"
                                    class="group relative flex items-center rounded-2xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-100 {{ $l['active'] ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200/60' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}"
                                    :class="'gap-3 px-3 py-3'">

                                    @if ($l['active'])
                                        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-white"></span>
                                    @endif

                                    <span
                                        class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl transition {{ $l['active'] ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}">
                                        @switch($l['icon'])
                                            @case('home')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3 10.5 12 3l9 7.5" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M5.5 9.5V21h5.5v-6h2v6h5.5V9.5" />
                                                </svg>
                                            @break

                                            @case('book')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 4.5A2.5 2.5 0 0 1 8.5 2h10v16h-10A2.5 2.5 0 0 0 6 20.5V4.5Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 4.5c0-1.38 1.12-2.5 2.5-2.5H18v16H8.5A2.5 2.5 0 0 0 6 20.5" />
                                                </svg>
                                            @break

                                            @case('check')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 12v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h7" />
                                                </svg>
                                            @break

                                            @case('clipboard')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 4.5h6A1.5 1.5 0 0 1 16.5 6v1H19v13H5V7h2.5V6A1.5 1.5 0 0 1 9 4.5Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5h6v3H9v-3Z" />
                                                </svg>
                                            @break

                                            @case('calendar')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M7 3.5v3M17 3.5v3M4.5 8.5h15" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6.5 5.5h11A2 2 0 0 1 19.5 7.5v11A2 2 0 0 1 17.5 20.5h-11A2 2 0 0 1 4.5 18.5v-11A2 2 0 0 1 6.5 5.5Z" />
                                                </svg>
                                            @break

                                            @case('bell')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 17.5H9m6-9A3 3 0 0 0 9 8.5c0 3.5-1 4.5-2 5.5h10c-1-1-2-2-2-6Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13.5 17.5a1.5 1.5 0 0 1-3 0" />
                                                </svg>
                                            @break

                                            @case('cog')
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="3" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06-2 3.46-.08-.03a1.65 1.65 0 0 0-2.11.73l-.03.08h-4l-.03-.08a1.65 1.65 0 0 0-2.11-.73l-.08.03-2-3.46.06-.06A1.65 1.65 0 0 0 4.6 15l-.03-.08V9l.03-.08A1.65 1.65 0 0 0 4.27 7.1l-.06-.06 2-3.46.08.03a1.65 1.65 0 0 0 2.11-.73l.03-.08h4l.03.08a1.65 1.65 0 0 0 2.11.73l.08-.03 2 3.46-.06.06A1.65 1.65 0 0 0 19.4 9l.03.08v5.84Z" />
                                                </svg>
                                            @break
                                        @endswitch
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-semibold">{{ $l['label'] }}</div>
                                    </div>

                                    @if ($l['active'])
                                        <span class="h-2.5 w-2.5 rounded-full bg-white"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            {{-- FOOTER --}}
            <div class="border-t border-slate-100 p-4">
                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-600 p-4 text-white shadow-lg">
                    <div class="text-sm font-bold uppercase tracking-[0.18em] text-white/70">Student Focus</div>
                    <div class="mt-2 text-sm font-semibold leading-6 text-white/95">
                        Stay focused and keep learning.
                    </div>
                    <div class="mt-4 inline-flex items-center rounded-xl bg-white/15 px-3 py-2 text-xs font-semibold">
                        Open your schedule, check notices, and keep moving forward.
                    </div>
                </div>
            </div>
        </aside>

        {{-- MAIN --}}
        <div class="lg:pl-72">

            {{-- TOPBAR --}}
            <header class="layout-topbar sticky top-0 z-30 border-b border-slate-200 bg-slate-100/80 backdrop-blur">
                <div class="flex h-16 items-center gap-3 px-4">

                    <button
                        class="rounded-xl p-2 text-slate-600 transition hover:bg-white hover:text-slate-900 lg:hidden"
                        @click="mobile = true" aria-label="Open sidebar">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 6h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="flex-1 text-sm font-semibold text-slate-700">
                        Welcome back, {{ auth()->user()->name }}
                    </div>

                    {{-- PROFILE --}}
                    <div class="relative" @click.outside="profileOpen=false">
                        <button class="group flex items-center gap-2" @click="profileOpen=!profileOpen">
                            <img class="h-9 w-9 rounded-full border-2 border-indigo-100 transition group-hover:border-indigo-400"
                                src="{{ auth()->user()->avatar_url }}"
                                onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                                alt="avatar">

                            <svg class="h-4 w-4 text-slate-500 transition-transform duration-200"
                                :class="profileOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 9l6 6 6-6" />
                            </svg>
                        </button>

                        <div x-show="profileOpen" x-transition.origin.top.right
                            class="absolute right-0 mt-3 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

                            <div class="border-b border-slate-100 px-4 py-3">
                                <div class="text-sm font-semibold text-slate-800">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-xs text-slate-500 capitalize">
                                    {{ auth()->user()->role }}
                                </div>
                            </div>

                            <a href="{{ route('student.settings') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50">

                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                                    <svg class="h-4 w-4 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                    </svg>
                                </span>

                                Settings
                            </a>

                            <div class="border-t border-slate-100"></div>

                            <a href="{{ route('logout') }}"
                                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 transition hover:bg-red-50">

                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50">
                                    <svg class="h-4 w-4 text-red-600" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8v2h8v14h-8v2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z" />
                                    </svg>
                                </span>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-6" data-page-animate>
                @yield('page')
            </main>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
