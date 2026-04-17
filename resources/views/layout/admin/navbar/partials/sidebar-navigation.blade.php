@php
    $item = fn($route, $label, $icon, $badge = 0) => [
        'route' => $route,
        'label' => $label,
        'icon' => $icon,
        'badge' => max((int) $badge, 0),
        'active' => request()->routeIs($route),
    ];

    $sections = [
        [
            'title' => 'Main',
            'items' => [$item('admin.dashboard', 'Dashboard', 'layout-dashboard')],
        ],

        [
            'title' => 'Management',
            'items' => [
                $item('admin.students.index', 'Students', 'users'),
                $item('admin.teachers.index', 'Teachers', 'user-cog'),
                $item('admin.classes.index', 'Classes', 'layers'),
                $item('admin.subjects.index', 'Subjects', 'book-open'),
                $item('admin.time-studies.index', 'Schedule', 'clock-3'),
                $item('admin.student-study.index', 'Student Progress', 'graduation-cap'),
                $item('admin.mission.index', 'Mission', 'flag'),
            ],
        ],

        [
            'title' => 'Attendance',
            'items' => [
                $item('admin.attendance.teachers.index', 'Teacher Attendance', 'clipboard-check'),
                $item('admin.attendance.index', 'Student Attendance', 'calendar-check'),
            ],
        ],

        [
            'title' => 'Communication',
            'items' => [$item('admin.contacts.index', 'Messages', 'mail', $contactUnread ?? 0)],
        ],

        [
            'title' => 'System',
            'items' => [$item('admin.settings', 'Settings', 'settings')],
        ],
    ];
@endphp

<nav class="space-y-4">
    @foreach ($sections as $section)
        <div class="space-y-1.5">
            <div x-show="!collapsed" class="px-3 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">
                {{ $section['title'] }}
            </div>

            <div class="space-y-1.5">
                @foreach ($section['items'] as $l)
                    <a href="{{ route($l['route']) }}"
                        class="group relative flex items-center rounded-2xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-100 {{ $l['active'] ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200/60' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}"
                        :class="collapsed ? 'justify-center px-2 py-3' : 'gap-3 px-3 py-3'">

                        @if ($l['active'])
                            <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-white"></span>
                        @endif

                        <span
                            class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $l['active'] ? 'bg-white/15' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}">
                            @include('layout.admin.navbar.partials.sidebar-icon', ['icon' => $l['icon']])

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
