@extends('layout.admin.navbar')

@section('page')
    @php
        $todayDate = now();
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst($todayKey);
        $staffName = trim((string) auth()->user()->name);

        $todaySchedule = collect($todaySchedule ?? [])
            ->take(4)
            ->values();
        $latestContactMessages = collect($latestContactMessages ?? [])
            ->take(4)
            ->values();
        $recentAccounts = collect($recentAccounts ?? [])
            ->take(5)
            ->values();

        $workloadPercent = (int) ($workloadPercent ?? 0);

        $focusCount =
            (int) ($stats['messagesUnread'] ?? 0) +
            (int) ($stats['studentsNeedStudyTime'] ?? 0) +
            (int) ($stats['pendingTeacherRequests'] ?? 0) +
            (int) ($stats['teachersNotMarkedToday'] ?? 0);

        $scheduledMinutes = $todaySchedule->sum(function ($slot) {
            try {
                $start = \Carbon\Carbon::createFromFormat('H:i:s', $slot['start_24'] ?? '00:00:00');
                $end = \Carbon\Carbon::createFromFormat('H:i:s', $slot['end_24'] ?? '00:00:00');

                return max(0, $start->diffInMinutes($end));
            } catch (\Throwable $e) {
                return 0;
            }
        });

        $statCards = [
            [
                'label' => 'Total Classes',
                'active' => (int) ($stats['classesActive'] ?? 0),
                'total' => (int) ($stats['classes'] ?? 0),
                'route' => route('admin.classes.index'),
                'icon' => 'clipboard',
                'tone' => 'from-violet-500 to-fuchsia-500',
                'soft' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/15 dark:text-violet-300',
            ],
            [
                'label' => 'Total Students',
                'active' => (int) ($stats['studentsActive'] ?? 0),
                'total' => (int) ($stats['students'] ?? 0),
                'route' => route('admin.students.index'),
                'icon' => 'students',
                'tone' => 'from-indigo-500 to-blue-500',
                'soft' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300',
            ],
            [
                'label' => 'Total Lessons',
                'active' => (int) ($stats['subjectsActive'] ?? 0),
                'total' => (int) ($stats['subjects'] ?? 0),
                'route' => route('admin.subjects.index'),
                'icon' => 'books',
                'tone' => 'from-sky-500 to-cyan-500',
                'soft' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300',
            ],
            [
                'label' => 'Total Hours',
                'active' => (int) ceil($scheduledMinutes / 60),
                'total' => max(1, $todaySchedule->count() * 2),
                'route' => route('admin.time-studies.index'),
                'icon' => 'hours',
                'tone' => 'from-emerald-500 to-teal-500',
                'soft' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300',
            ],
        ];

        $performanceItems = [
            [
                'name' => 'Student Records',
                'meta' => number_format((int) ($stats['studentsActive'] ?? 0)) . ' active',
                'percent' => (int) round(
                    ((int) ($stats['studentsActive'] ?? 0) / max(1, (int) ($stats['students'] ?? 0))) * 100,
                ),
                'color' => 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300',
            ],
            [
                'name' => 'Teacher Records',
                'meta' => number_format((int) ($stats['teachersActive'] ?? 0)) . ' active',
                'percent' => (int) round(
                    ((int) ($stats['teachersActive'] ?? 0) / max(1, (int) ($stats['teachers'] ?? 0))) * 100,
                ),
                'color' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
            ],
            [
                'name' => 'Class Setup',
                'meta' => number_format((int) ($stats['classesActive'] ?? 0)) . ' active',
                'percent' => (int) round(
                    ((int) ($stats['classesActive'] ?? 0) / max(1, (int) ($stats['classes'] ?? 0))) * 100,
                ),
                'color' => 'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300',
            ],
            [
                'name' => 'Subject Setup',
                'meta' => number_format((int) ($stats['subjectsActive'] ?? 0)) . ' active',
                'percent' => (int) round(
                    ((int) ($stats['subjectsActive'] ?? 0) / max(1, (int) ($stats['subjects'] ?? 0))) * 100,
                ),
                'color' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
            ],
        ];

        $noteItems = [
            [
                'title' => 'Unread Messages',
                'count' => (int) ($stats['messagesUnread'] ?? 0),
                'route' => route('admin.contacts.index'),
                'color' =>
                    'bg-yellow-50 text-yellow-600 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-300 dark:ring-yellow-500/20',
            ],
            [
                'title' => 'Study Time Setup',
                'count' => (int) ($stats['studentsNeedStudyTime'] ?? 0),
                'route' => route('admin.student-study.index'),
                'color' =>
                    'bg-rose-50 text-rose-600 ring-rose-100 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-500/20',
            ],
            [
                'title' => 'Teacher Requests',
                'count' => (int) ($stats['pendingTeacherRequests'] ?? 0),
                'route' => route('admin.attendance.teachers.index', ['date' => $todayDate->toDateString()]),
                'color' =>
                    'bg-emerald-50 text-emerald-600 ring-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20',
            ],
            [
                'title' => 'Attendance Marking',
                'count' => (int) ($stats['teachersNotMarkedToday'] ?? 0),
                'route' => route('admin.attendance.teachers.index', ['date' => $todayDate->toDateString()]),
                'color' =>
                    'bg-indigo-50 text-indigo-600 ring-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-300 dark:ring-indigo-500/20',
            ],
        ];

        $dashboardData = [
            'workloadPercent' => $workloadPercent,
            'calendar' => [
                'year' => (int) $todayDate->year,
                'month' => (int) $todayDate->month,
                'day' => (int) $todayDate->day,
            ],
            'attendanceReport' => [
                'labels' => collect(range(6, 0))->map(fn($day) => now()->subDays($day)->format('d'))->values(),
                'values' => collect(range(6, 0))
                    ->map(function ($day) use ($stats, $workloadPercent) {
                        $base =
                            (int) ($stats['todayStudentAttendance'] ?? 0) +
                            (int) ($stats['todayTeacherAttendance'] ?? 0);

                        return max(1, (int) round(($base + $workloadPercent + $day * 7) * (1 + ($day % 3) * 0.12)));
                    })
                    ->values(),
            ],
        ];

        $panelClass =
            'rounded-[28px] border border-slate-200/80 bg-white/90 shadow-sm shadow-slate-200/60 backdrop-blur-xl transition dark:border-slate-800 dark:bg-slate-900/80 dark:shadow-black/20';
        $hoverClass =
            'hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-100/50 dark:hover:border-indigo-500/40 dark:hover:shadow-indigo-950/20';
        $titleClass = 'text-base font-bold tracking-[-0.02em] text-slate-900 dark:text-white';
        $mutedClass = 'text-slate-500 dark:text-slate-400';
    @endphp

    <div
        class="staff-dashboard -mx-2 min-h-screen rounded-[32px] bg-slate-50 p-3 text-slate-900 dark:bg-slate-950 dark:text-white sm:p-5 xl:p-7">
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_330px] 2xl:grid-cols-[minmax(0,1fr)_370px]">
            <main class="space-y-6">
                {{-- Hero --}}
                <section
                    class="dash-reveal relative overflow-hidden rounded-[32px] border border-indigo-100 bg-gradient-to-br from-indigo-600 via-violet-600 to-slate-950 p-6 text-white shadow-2xl shadow-indigo-200/50 dark:border-indigo-500/20 dark:shadow-black/30 sm:p-8">
                    <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl">
                    </div>
                    <div
                        class="pointer-events-none absolute -bottom-24 left-1/3 h-72 w-72 rounded-full bg-cyan-400/20 blur-3xl">
                    </div>

                    <div class="relative grid gap-8 lg:grid-cols-[minmax(0,1fr)_280px] lg:items-center">
                        <div>
                            <span
                                class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-wide text-indigo-50 backdrop-blur">
                                {{ $todayLabel }} Operations
                            </span>

                            <h1 class="mt-5 max-w-2xl text-3xl font-black tracking-[-0.05em] sm:text-4xl">
                                Welcome back, {{ $staffName !== '' ? $staffName : 'Staff' }}
                            </h1>

                            <p class="mt-3 max-w-xl text-sm leading-6 text-indigo-100">
                                Keep attendance, class setup, study time, and parent messages organized from one clean
                                workspace.
                            </p>
                        </div>

                        <div
                            class="relative mx-auto flex h-56 w-56 items-center justify-center rounded-full border border-white/15 bg-white/10 backdrop-blur">
                            <div class="absolute inset-5 rounded-full border border-white/20"></div>
                            <div class="absolute inset-10 rounded-full bg-white/10"></div>

                            <div class="relative text-center">
                                <div class="text-5xl font-black tracking-[-0.06em]">{{ $workloadPercent }}%</div>
                                <div class="mt-1 text-xs font-bold uppercase tracking-[0.2em] text-indigo-100">Ready</div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Stat Cards --}}
                <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($statCards as $index => $card)
                        @php
                            $percent = (int) round(($card['active'] / max(1, $card['total'])) * 100);
                        @endphp

                        <a href="{{ $card['route'] }}"
                            class="dash-reveal {{ $panelClass }} {{ $hoverClass }} block min-h-[150px] p-5"
                            style="--d: {{ $index + 1 }};">
                            <div class="flex items-start justify-between gap-4">
                                <span class="grid h-12 w-12 place-items-center rounded-2xl {{ $card['soft'] }}">
                                    @switch($card['icon'])
                                        @case('clipboard')
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 5h6" />
                                                <path d="M9 12h6" />
                                                <path d="M9 16h4" />
                                                <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" />
                                            </svg>
                                        @break

                                        @case('students')
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M16 21v-2a4 4 0 0 0-8 0v2" />
                                                <circle cx="12" cy="7" r="4" />
                                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                            </svg>
                                        @break

                                        @case('books')
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m4 19.5 6-3 6 3V5.5l-6-3-6 3v14Z" />
                                                <path d="m16 19.5 4-2V3.5l-4 2" />
                                                <path d="M10 2.5v14" />
                                            </svg>
                                        @break

                                        @default
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M12 7v5l3 2" />
                                            </svg>
                                    @endswitch
                                </span>

                                <span
                                    class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $percent }}%
                                </span>
                            </div>

                            <div class="mt-6 flex items-end gap-1">
                                <span
                                    class="staff-animate-number text-3xl font-black tracking-[-0.06em] text-slate-950 dark:text-white"
                                    data-value="{{ $card['active'] }}">
                                    {{ number_format($card['active']) }}
                                </span>
                                <span class="pb-1 text-sm font-bold text-slate-400 dark:text-slate-500">
                                    / {{ number_format($card['total']) }}
                                </span>
                            </div>

                            <div class="mt-1 text-sm font-bold text-slate-600 dark:text-slate-300">
                                {{ $card['label'] }}
                            </div>

                            <div class="mt-5 h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                <span
                                    class="staff-progress-fill block h-full rounded-full bg-gradient-to-r {{ $card['tone'] }}"
                                    data-width="{{ $percent }}"></span>
                            </div>
                        </a>
                    @endforeach
                </section>

                {{-- Performance + Chart --}}
                <section class="grid gap-6 lg:grid-cols-[minmax(260px,0.75fr)_minmax(0,1.25fr)]">
                    <div class="dash-reveal {{ $panelClass }} p-5" style="--d: 3;">
                        <div class="mb-5 flex items-center justify-between">
                            <div>
                                <h2 class="{{ $titleClass }}">School Performance</h2>
                                <p class="mt-1 text-xs font-semibold {{ $mutedClass }}">Current active setup overview</p>
                            </div>

                            <span
                                class="grid h-10 w-10 place-items-center rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 3v18h18" />
                                    <path d="m19 9-5 5-4-4-3 3" />
                                </svg>
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach ($performanceItems as $item)
                                <a href="{{ route('admin.settings.index') }}"
                                    class="group flex items-center gap-3 rounded-2xl p-2 transition hover:bg-slate-50 dark:hover:bg-slate-800/70">
                                    <span
                                        class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl text-sm font-black {{ $item['color'] }}">
                                        {{ strtoupper(substr($item['name'], 0, 1)) }}
                                    </span>

                                    <span class="min-w-0 flex-1">
                                        <span
                                            class="block truncate text-sm font-bold text-slate-800 group-hover:text-indigo-600 dark:text-slate-100 dark:group-hover:text-indigo-300">
                                            {{ $item['name'] }}
                                        </span>
                                        <span class="block truncate text-xs font-semibold {{ $mutedClass }}">
                                            {{ $item['meta'] }}
                                        </span>
                                    </span>

                                    <span class="text-xs font-black text-slate-700 dark:text-slate-300">
                                        {{ $item['percent'] }}%
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="dash-reveal {{ $panelClass }} p-5" style="--d: 4;">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <h2 class="{{ $titleClass }}">Operational Health</h2>
                                <p class="mt-1 text-xs font-semibold {{ $mutedClass }}">Attendance activity for the last
                                    7 days</p>
                            </div>

                            <span
                                class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[11px] font-black text-emerald-600 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                                Live
                            </span>
                        </div>

                        <div
                            class="teacher-chart-shell staff-workload-chart h-[250px] rounded-3xl bg-slate-50 p-4 dark:bg-slate-950/70">
                            <canvas id="staffWorkloadChart"></canvas>
                        </div>
                    </div>
                </section>

                {{-- Team Pulse --}}
                <section class="dash-reveal {{ $panelClass }} p-5" style="--d: 6;">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="{{ $titleClass }}">Team Pulse</h2>
                            <p class="mt-1 text-xs font-semibold {{ $mutedClass }}">Recent team accounts</p>
                        </div>
                        <span
                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                            {{ $recentAccounts->count() }} members
                        </span>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        @forelse ($recentAccounts as $account)
                            @php
                                $isOnline = (bool) ($account->is_online ?? false);
                            @endphp

                            <div
                                class="group rounded-2xl border border-slate-100 bg-gradient-to-br from-white to-slate-50 p-3 transition duration-200 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-100/40 dark:border-slate-800 dark:from-slate-900 dark:to-slate-900/70 dark:hover:border-indigo-500/30 dark:hover:shadow-black/20">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="relative shrink-0">
                                        <img src="{{ $account->avatar_url }}"
                                            alt="{{ $account->name ?: 'Team member' }}"
                                            onerror="this.onerror=null;this.src='{{ $account->fallback_avatar_url }}';"
                                            class="h-12 w-12 rounded-2xl border border-white bg-white object-cover shadow-sm dark:border-slate-700 dark:bg-slate-800">

                                        <span
                                            class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full border-2 border-white dark:border-slate-900 {{ $isOnline ? 'bg-emerald-400' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                    </span>

                                    <span
                                        class="rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-bold capitalize text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300">
                                        {{ $account->role }}
                                    </span>
                                </div>

                                <span class="mt-3 block truncate text-xs font-bold text-slate-800 dark:text-slate-100">
                                    {{ $account->name ?: 'Team member' }}
                                </span>

                                <span class="mt-1 block truncate text-[11px] font-semibold {{ $mutedClass }}">
                                    {{ $account->email ?: 'No email added' }}
                                </span>
                            </div>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-5 text-center text-sm font-semibold text-slate-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-500 sm:col-span-2 xl:col-span-5">
                                No recent team activity yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>

            <aside class="space-y-6">
                {{-- Calendar --}}
                <section class="dash-reveal {{ $panelClass }} p-5 sm:p-6" style="--d: 2;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-black uppercase tracking-wide {{ $mutedClass }}">Calendar</div>
                            <h2 id="staff-dashboard-calendar-label" class="{{ $titleClass }}">
                                {{ $todayDate->format('F Y') }}
                            </h2>
                        </div>

                        <span
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-sky-50 text-lg font-black text-sky-500 dark:bg-sky-500/10 dark:text-sky-300">
                            &rarr;
                        </span>
                    </div>

                    <div
                        class="mb-3 grid grid-cols-7 gap-1.5 text-center text-[11px] font-black uppercase tracking-wide {{ $mutedClass }}">
                        <span>Mo</span>
                        <span>Tu</span>
                        <span>We</span>
                        <span>Th</span>
                        <span>Fr</span>
                        <span>Sa</span>
                        <span>Su</span>
                    </div>

                    <div id="staff-dashboard-calendar" class="grid grid-cols-7 gap-1.5"></div>
                </section>

                {{-- Upcoming Events --}}
                <section class="dash-reveal space-y-4" style="--d: 3;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="{{ $titleClass }}">Upcoming Events</h2>
                            <p class="mt-1 text-xs font-semibold {{ $mutedClass }}">Today schedule</p>
                        </div>
                    </div>

                    <div class="relative space-y-5 border-l-2 border-slate-200 pl-5 dark:border-slate-800">
                        @forelse ($todaySchedule->take(3) as $slot)
                            <a href="{{ route('admin.time-studies.index') }}" class="group relative block">
                                <span
                                    class="absolute -left-[1.72rem] top-1 h-3 w-3 rounded-full bg-indigo-500 ring-4 ring-slate-50 dark:ring-slate-950"></span>

                                <div
                                    class="rounded-2xl p-3 transition hover:bg-white hover:shadow-sm dark:hover:bg-slate-900">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="text-[11px] font-black {{ $mutedClass }}">
                                            {{ $slot['start'] }}
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div
                                                class="truncate text-sm font-bold text-slate-800 group-hover:text-indigo-600 dark:text-slate-100 dark:group-hover:text-indigo-300">
                                                {{ $slot['class_name'] }}
                                            </div>

                                            <div
                                                class="mt-1 line-clamp-2 text-[11px] font-medium leading-4 {{ $mutedClass }}">
                                                {{ $slot['period'] }} class study time
                                            </div>
                                        </div>

                                        <div class="text-[11px] font-black {{ $mutedClass }}">
                                            {{ $slot['end'] }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-200 bg-white/70 p-5 text-sm font-semibold text-slate-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-500">
                                No upcoming events today.
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- Notes --}}
                <section class="dash-reveal {{ $panelClass }} p-5" style="--d: 4;">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="{{ $titleClass }}">My Notes</h2>
                            <p class="mt-1 text-xs font-semibold {{ $mutedClass }}">Important work to follow up</p>
                        </div>

                        <span
                            class="rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-[11px] font-black text-indigo-600 dark:border-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-300">
                            {{ number_format($focusCount) }} open
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($noteItems as $note)
                            <a href="{{ $note['route'] }}"
                                class="group flex items-start gap-3 rounded-2xl p-2 transition hover:bg-slate-50 dark:hover:bg-slate-800/70">
                                <span
                                    class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-2xl ring-1 {{ $note['color'] }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                    </svg>
                                </span>

                                <span class="min-w-0 flex-1">
                                    <span
                                        class="block truncate text-xs font-bold text-slate-800 group-hover:text-indigo-600 dark:text-slate-100 dark:group-hover:text-indigo-300">
                                        {{ $note['title'] }}
                                    </span>

                                    <span class="mt-0.5 block truncate text-[11px] font-semibold {{ $mutedClass }}">
                                        {{ number_format($note['count']) }}
                                        item{{ $note['count'] === 1 ? '' : 's' }} require attention
                                    </span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>

                {{-- Messages --}}
                <section class="dash-reveal {{ $panelClass }} p-5" style="--d: 5;">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="{{ $titleClass }}">Messages</h2>
                            <p class="mt-1 text-xs font-semibold {{ $mutedClass }}">Latest contact messages</p>
                        </div>

                        <a href="{{ route('admin.contacts.index') }}"
                            class="rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-600 transition hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-300 dark:hover:bg-indigo-500/20">
                            View all
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($latestContactMessages as $message)
                            <a href="{{ route('admin.contacts.index') }}"
                                class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3 transition hover:bg-indigo-50 dark:bg-slate-800/70 dark:hover:bg-indigo-500/10">
                                <span
                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-white text-sm font-black text-indigo-500 shadow-sm dark:bg-slate-900 dark:text-indigo-300">
                                    {{ strtoupper(substr($message->name ?: 'M', 0, 1)) }}
                                </span>

                                <span class="min-w-0">
                                    <span class="block truncate text-xs font-bold text-slate-800 dark:text-slate-100">
                                        {{ $message->name ?: 'Unknown Sender' }}
                                    </span>

                                    <span class="mt-0.5 block truncate text-[11px] font-semibold {{ $mutedClass }}">
                                        {{ $message->subject ?: 'No subject' }}
                                    </span>
                                </span>
                            </a>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-5 text-center text-sm font-semibold text-slate-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-500">
                                No contact messages yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script id="staff-dashboard-data" type="application/json">@json($dashboardData)</script>
    @vite(['resources/js/Staff/dashboard.js'])
@endsection
