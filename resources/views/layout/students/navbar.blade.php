<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Student Dashboard' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100">

    <div x-data="{
        mobile: false,
        notifOpen: false,
        profileOpen: false,
        closeAll() {
            this.notifOpen = false;
            this.profileOpen = false
        }
    }" @keydown.escape.window="closeAll()" class="min-h-screen">

        {{-- MOBILE OVERLAY --}}
        <div x-show="mobile" class="fixed inset-0 bg-black/40 z-40 lg:hidden" @click="mobile=false"></div>

        {{-- SIDEBAR --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 shadow-sm
                  transition-all duration-300 lg:translate-x-0 h-screen flex flex-col"
            :class="mobile ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            {{-- Header --}}
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-200">
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-600/10 flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2 2 7l10 5 10-5-10-5Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-slate-900">Schooli</div>
                        <div class="text-xs text-slate-500">Student Panel</div>
                    </div>
                </a>

                <button class="lg:hidden p-2 rounded-xl hover:bg-slate-100" @click="mobile=false">✕</button>
            </div>

            {{-- NAVIGATION --}}
            <nav class="px-3 py-4 space-y-1 flex-1 overflow-y-auto">

                @php
                    $item = fn($route, $label, $icon) => [
                        'route' => $route,
                        'label' => $label,
                        'icon' => $icon,
                        'active' => request()->routeIs($route),
                    ];

                    $links = [
                        $item('student.dashboard', 'Dashboard', 'home'),
                        $item('student.subjects.index', 'My Subjects', 'book'),
                        $item('student.attendance.index', 'Attendance', 'check'),
                        $item('student.grades.index', 'My Grades', 'clipboard'),
                        $item('student.schedule.index', 'Schedule', 'calendar'),
                        $item('student.notices.index', 'Notices', 'bell'),
                        $item('student.settings', 'Settings', 'cog'),
                    ];
                @endphp

                @foreach ($links as $l)
                    <a href="{{ route($l['route']) }}"
                        class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition
                          {{ $l['active'] ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">

                        <span
                            class="h-9 w-9 rounded-xl flex items-center justify-center
                                 {{ $l['active'] ? 'bg-white/15' : 'bg-slate-100' }}">
                            {{-- ICONS --}}
                            @switch($l['icon'])
                                @case('home')
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 3 2 12h3v9h6v-6h2v6h6v-9h3L12 3Z" />
                                    </svg>
                                @break

                                @case('book')
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12v-2H6V4h12v16h2Z" />
                                    </svg>
                                @break

                                @case('check')
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17 4.83 12 3.41 13.41 9 19l12-12-1.41-1.41Z" />
                                    </svg>
                                @break

                                @case('clipboard')
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M16 2H8v2H5v18h14V4h-3V2Z" />
                                    </svg>
                                @break

                                @case('calendar')
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7 2v2H5a2 2 0 0 0-2 2v14h18V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7Z" />
                                    </svg>
                                @break

                                @case('bell')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M18 8a6 6 0 00-12 0v5l-2 2h16l-2-2z" />
                                        <path d="M13.73 21a2 2 0 01-3.46 0" />
                                    </svg>
                                @break

                                @case('cog')
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58-1.92-3.32-2.39.96a7.28 7.28 0 0 0-1.63-.94L13.9 1h-3.8l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96-1.92 3.32L4.83 10.5c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5l1.92 3.32 2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54h3.8l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96 1.92-3.32-2.03-1.56Z" />
                                        <path d="M12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                    </svg>
                                @break
                            @endswitch
                        </span>

                        <span class="flex-1">{{ $l['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- FOOTER --}}
            <div class="p-4 border-t border-slate-100">
                <div class="rounded-2xl bg-indigo-600 text-white p-4 text-sm">
                    Stay focused and keep learning 🎓
                </div>
            </div>
        </aside>

        {{-- MAIN --}}
        <div class="lg:pl-72">

            {{-- TOPBAR --}}
            <header class="sticky top-0 bg-slate-100/80 backdrop-blur border-b border-slate-200 z-30">
                <div class="h-16 flex items-center px-4 gap-3">

                    <button class="lg:hidden p-2 rounded-xl hover:bg-white" @click="mobile=true">
                        ☰
                    </button>

                    <div class="flex-1 text-sm font-semibold text-slate-700">
                        Welcome back, {{ auth()->user()->name }} 👋
                    </div>

                    {{-- PROFILE --}}
                    <div class="relative" @click.outside="profileOpen=false">
                        <button class="flex items-center gap-2 group" @click="profileOpen=!profileOpen">
                            <img class="h-9 w-9 rounded-full border-2 border-indigo-100 group-hover:border-indigo-400 transition"
                                src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}">

                            {{-- Arrow --}}
                            <svg class="h-4 w-4 text-slate-500 transition-transform duration-200"
                                :class="profileOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 9l6 6 6-6" />
                            </svg>
                        </button>

                        <div x-show="profileOpen" x-transition.origin.top.right
                            class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">

                            {{-- User Info --}}
                            <div class="px-4 py-3 border-b border-slate-100">
                                <div class="text-sm font-semibold text-slate-800">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-xs text-slate-500 capitalize">
                                    {{ auth()->user()->role }}
                                </div>
                            </div>

                            {{-- Settings --}}
                            <a href="{{ route('student.settings') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">

                                <span class="h-8 w-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                    </svg>
                                </span>

                                Settings
                            </a>

                            {{-- Divider --}}
                            <div class="border-t border-slate-100"></div>

                            {{-- Logout --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">

                                    <span class="h-8 w-8 rounded-lg bg-red-50 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-red-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8v2h8v14h-8v2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z" />
                                        </svg>
                                    </span>

                                    Logout
                                </button>
                            </form>

                        </div>
                    </div>


                </div>
            </header>

            <main class="p-6">
                @yield('page')
            </main>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
