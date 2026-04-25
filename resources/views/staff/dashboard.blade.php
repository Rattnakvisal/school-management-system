@extends('layout.staff.navbar')

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
                'label' => 'Total classes',
                'value' =>
                    number_format((int) ($stats['classesActive'] ?? 0)) .
                    '/' .
                    number_format((int) ($stats['classes'] ?? 0)),
                'active' => (int) ($stats['classesActive'] ?? 0),
                'total' => (int) ($stats['classes'] ?? 0),
                'icon' => 'clipboard',
                'tone' => 'from-violet-100 to-white text-violet-600',
            ],
            [
                'label' => 'Total Students',
                'value' =>
                    number_format((int) ($stats['studentsActive'] ?? 0)) .
                    '/' .
                    number_format((int) ($stats['students'] ?? 0)),
                'active' => (int) ($stats['studentsActive'] ?? 0),
                'total' => (int) ($stats['students'] ?? 0),
                'icon' => 'students',
                'tone' => 'from-indigo-100 to-white text-indigo-600',
            ],
            [
                'label' => 'Total Lessons',
                'value' =>
                    number_format((int) ($stats['subjectsActive'] ?? 0)) .
                    '/' .
                    number_format((int) ($stats['subjects'] ?? 0)),
                'active' => (int) ($stats['subjectsActive'] ?? 0),
                'total' => (int) ($stats['subjects'] ?? 0),
                'icon' => 'books',
                'tone' => 'from-sky-100 to-white text-sky-600',
            ],
            [
                'label' => 'Total Hours',
                'value' =>
                    number_format((int) ceil($scheduledMinutes / 60)) .
                    '/' .
                    number_format(max(1, $todaySchedule->count() * 2)),
                'active' => (int) ceil($scheduledMinutes / 60),
                'total' => max(1, $todaySchedule->count() * 2),
                'icon' => 'hours',
                'tone' => 'from-emerald-100 to-white text-emerald-600',
            ],
        ];

        $performanceItems = [
            [
                'name' => 'Student records',
                'meta' => number_format((int) ($stats['studentsActive'] ?? 0)) . ' active',
                'percent' => (int) round(
                    ((int) ($stats['studentsActive'] ?? 0) / max(1, (int) ($stats['students'] ?? 0))) * 100,
                ),
                'avatar' => null,
                'color' => 'bg-sky-100 text-sky-700',
            ],
            [
                'name' => 'Teacher records',
                'meta' => number_format((int) ($stats['teachersActive'] ?? 0)) . ' active',
                'percent' => (int) round(
                    ((int) ($stats['teachersActive'] ?? 0) / max(1, (int) ($stats['teachers'] ?? 0))) * 100,
                ),
                'avatar' => null,
                'color' => 'bg-amber-100 text-amber-700',
            ],
            [
                'name' => 'Class setup',
                'meta' => number_format((int) ($stats['classesActive'] ?? 0)) . ' active',
                'percent' => (int) round(
                    ((int) ($stats['classesActive'] ?? 0) / max(1, (int) ($stats['classes'] ?? 0))) * 100,
                ),
                'avatar' => null,
                'color' => 'bg-violet-100 text-violet-700',
            ],
            [
                'name' => 'Subject setup',
                'meta' => number_format((int) ($stats['subjectsActive'] ?? 0)) . ' active',
                'percent' => (int) round(
                    ((int) ($stats['subjectsActive'] ?? 0) / max(1, (int) ($stats['subjects'] ?? 0))) * 100,
                ),
                'avatar' => null,
                'color' => 'bg-rose-100 text-rose-700',
            ],
        ];

        $noteItems = [
            [
                'title' => 'Unread messages',
                'count' => (int) ($stats['messagesUnread'] ?? 0),
                'route' => route('admin.contacts.index'),
                'color' => 'bg-yellow-50 text-yellow-600 ring-yellow-100',
            ],
            [
                'title' => 'Study time setup',
                'count' => (int) ($stats['studentsNeedStudyTime'] ?? 0),
                'route' => route('admin.student-study.index'),
                'color' => 'bg-rose-50 text-rose-600 ring-rose-100',
            ],
            [
                'title' => 'Teacher requests',
                'count' => (int) ($stats['pendingTeacherRequests'] ?? 0),
                'route' => route('admin.attendance.teachers.index', ['date' => $todayDate->toDateString()]),
                'color' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
            ],
            [
                'title' => 'Attendance marking',
                'count' => (int) ($stats['teachersNotMarkedToday'] ?? 0),
                'route' => route('admin.attendance.teachers.index', ['date' => $todayDate->toDateString()]),
                'color' => 'bg-indigo-50 text-indigo-600 ring-indigo-100',
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
            'rounded-[26px] border border-white/80 bg-white/90 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.55)] backdrop-blur';
        $softPanelClass =
            'rounded-[26px] border border-[#ececff] bg-[#f7f6ff]/95 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.45)]';
        $titleClass = 'text-[15px] font-extrabold tracking-[-0.02em] text-slate-800';
    @endphp

    <div class="dashboard-stage staff-soft-dashboard -mx-2 rounded-[30px] bg-[#f5f6ff] p-3 sm:p-5 xl:p-7">
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px] 2xl:grid-cols-[minmax(0,1fr)_360px]">
            <main class="space-y-6">
                <section class="staff-hero dash-reveal" style="--d: 1;">
                    <div class="staff-hero__content">
                        <span class="staff-hero__badge">
                            {{ $todayLabel }} operations
                        </span>
                        <h1 class="staff-hero__title">
                            Welcome back, {{ $staffName !== '' ? $staffName : 'Staff' }}
                        </h1>
                        <p class="staff-hero__copy">
                            Keep attendance, class setup, and parent messages moving smoothly from one focused workspace.
                        </p>
                    </div>

                    <div class="staff-hero__visual" aria-hidden="true">
                        <div class="staff-hero__ring">
                            <span class="staff-hero__ring-core">{{ $workloadPercent }}%</span>
                            <span class="staff-hero__ring-label">ready</span>
                        </div>
                        <div class="staff-hero__mini-card staff-hero__mini-card--top">
                            <span>{{ number_format((int) ($stats['messagesUnread'] ?? 0)) }}</span>
                            unread
                        </div>
                        <div class="staff-hero__mini-card staff-hero__mini-card--bottom">
                            <span>{{ number_format((int) ($stats['todayStudentAttendance'] ?? 0) + (int) ($stats['todayTeacherAttendance'] ?? 0)) }}</span>
                            attendance
                        </div>
                    </div>
                </section>

                <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($statCards as $index => $card)
                        <a href="{{ $index === 0 ? route('admin.classes.index') : ($index === 1 ? route('admin.students.index') : ($index === 2 ? route('admin.subjects.index') : route('admin.time-studies.index'))) }}"
                            class="dash-reveal dash-hover {{ $panelClass }} min-h-[132px] p-5"
                            style="--d: {{ $index + 1 }};">
                            <div class="flex items-start justify-between gap-4">
                                <span
                                    class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br {{ $card['tone'] }}">
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

                                <span class="text-slate-300">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                                    </svg>
                                </span>
                            </div>

                            <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                                <span class="staff-animate-number"
                                    data-value="{{ $card['active'] }}">{{ number_format($card['active']) }}</span>
                                <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                                    {{ number_format($card['total']) }}</span>
                            </div>
                            <div class="mt-1 text-sm font-bold text-slate-600">{{ $card['label'] }}</div>
                            <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                <span
                                    class="staff-progress-fill block h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400"
                                    data-width="{{ (int) round(($card['active'] / max(1, $card['total'])) * 100) }}"></span>
                            </div>
                        </a>
                    @endforeach
                </section>

                <section class="grid gap-6 lg:grid-cols-[minmax(260px,0.7fr)_minmax(0,1.3fr)]">
                    <div class="dash-reveal {{ $panelClass }} p-5" style="--d: 3;">
                        <div class="mb-5 flex items-center justify-between">
                            <div>
                                <h2 class="{{ $titleClass }}">School Performance</h2>
                                <div class="mt-2 flex gap-3 text-[10px] font-bold text-slate-400">
                                    <span>Grade</span>
                                    <span>Class</span>
                                    <span>Mastery</span>
                                </div>
                            </div>
                            <span class="text-slate-300">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                                </svg>
                            </span>
                        </div>

                        <div class="space-y-4">
                            @foreach ($performanceItems as $item)
                                <a href="{{ route('admin.settings') }}"
                                    class="dash-hover flex items-center gap-3 rounded-2xl p-1.5 transition hover:bg-slate-50">
                                    <span
                                        class="grid h-9 w-9 shrink-0 place-items-center rounded-full {{ $item['color'] }}">
                                        {{ strtoupper(substr($item['name'], 0, 1)) }}
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span
                                            class="block truncate text-sm font-extrabold text-slate-800">{{ $item['name'] }}</span>
                                        <span
                                            class="block truncate text-[11px] font-semibold text-slate-400">{{ $item['meta'] }}</span>
                                    </span>
                                    <span class="text-xs font-black text-slate-700">{{ $item['percent'] }}%</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="dash-reveal {{ $panelClass }} p-5" style="--d: 4;">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <h2 class="{{ $titleClass }}">Operational Health</h2>
                            <span
                                class="rounded-full border border-slate-100 bg-white px-3 py-1 text-[10px] font-bold text-slate-400">Live</span>
                        </div>
                        <div class="teacher-chart-shell staff-workload-chart h-[230px]">
                            <canvas id="staffWorkloadChart"></canvas>
                        </div>
                    </div>
                </section>

                <section class="dash-reveal {{ $panelClass }} p-5 sm:p-6" style="--d: 5;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <h2 class="text-lg font-black tracking-[-0.03em] text-slate-800">Teaching Lessons</h2>
                        <a href="{{ route('admin.time-studies.index') }}"
                            class="rounded-full bg-[#f3f0ff] px-4 py-2 text-xs font-black text-indigo-500">View all</a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($todaySchedule as $slot)
                            <article
                                class="js-staff-slot grid gap-4 rounded-2xl bg-white px-4 py-4 shadow-[0_14px_34px_-30px_rgba(78,85,135,0.7)] sm:grid-cols-[auto_minmax(0,1fr)_minmax(120px,0.8fr)_minmax(110px,0.7fr)_auto] sm:items-center"
                                data-start="{{ $slot['start_24'] ?? '' }}" data-end="{{ $slot['end_24'] ?? '' }}">
                                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#f5f4ff] text-indigo-500">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19V5" />
                                        <path d="M8 19V5" />
                                        <path d="M4 7h4" />
                                        <path d="M4 17h4" />
                                        <path d="M14 6h6" />
                                        <path d="M14 12h6" />
                                        <path d="M14 18h6" />
                                    </svg>
                                </span>

                                <div class="min-w-0">
                                    <div class="text-sm font-black text-slate-800">Start from</div>
                                    <div class="mt-0.5 text-[11px] font-semibold text-slate-400">
                                        {{ $slot['day_label'] }}, {{ $slot['start'] }}
                                    </div>
                                </div>

                                <div class="min-w-0">
                                    <div class="truncate text-sm font-black text-slate-800">{{ $slot['class_name'] }}
                                    </div>
                                    <div class="mt-1 flex flex-wrap gap-3 text-[11px] font-semibold text-slate-400">
                                        <span>{{ $slot['period'] }}</span>
                                        <span>{{ $slot['start'] }} - {{ $slot['end'] }}</span>
                                    </div>
                                </div>

                                <div class="text-sm font-black text-slate-700">School Schedule</div>

                                <span class="js-staff-slot-status teacher-lesson__status teacher-lesson__status--default">
                                    Scheduled
                                </span>
                            </article>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center text-sm font-semibold text-slate-400">
                                No class schedule items are available for today.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="dash-reveal {{ $panelClass }} p-5" style="--d: 6;">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-base font-black text-slate-800">Team Pulse</h2>
                        <span class="text-xs font-black text-slate-400">Recent team</span>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        @forelse ($recentAccounts as $account)
                            <div
                                class="staff-team-card rounded-2xl border border-slate-100 bg-slate-50/80 p-3">
                                <span
                                    class="grid h-10 w-10 place-items-center rounded-2xl bg-white text-sm font-black text-indigo-500 shadow-sm">
                                    {{ strtoupper(substr($account->name ?: $account->role, 0, 1)) }}
                                </span>
                                <span
                                    class="mt-3 block truncate text-xs font-black text-slate-800">{{ $account->name ?: 'Team member' }}</span>
                                <span
                                    class="mt-1 block truncate text-[11px] font-semibold capitalize text-slate-400">{{ $account->role }}</span>
                            </div>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-5 text-center text-sm font-semibold text-slate-400 sm:col-span-2 xl:col-span-5">
                                No recent team activity yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>

            <aside class="space-y-6">
                <section class="dash-reveal {{ $panelClass }} p-5 sm:p-6" style="--d: 2;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Calendar</div>
                            <h2 id="staff-dashboard-calendar-label" class="{{ $titleClass }}">
                                {{ $todayDate->format('F Y') }}
                            </h2>
                        </div>
                        <span
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-sky-50 text-lg font-bold text-sky-500">
                            &rarr;
                        </span>
                    </div>
                    <div
                        class="mb-3 grid grid-cols-7 gap-1.5 text-center text-[11px] font-bold uppercase tracking-wide text-slate-400">
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

                <section class="dash-reveal space-y-4" style="--d: 3;">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-black text-slate-800">Upcoming Events</h2>
                        <span class="text-slate-300">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                            </svg>
                        </span>
                    </div>

                    <div class="relative space-y-5 border-l-2 border-[#e4e4f5] pl-5">
                        @forelse ($todaySchedule->take(3) as $slot)
                            <a href="{{ route('admin.time-studies.index') }}" class="group relative block">
                                <span
                                    class="absolute -left-[1.72rem] top-1 h-3 w-3 rounded-full bg-indigo-400 ring-4 ring-[#f5f6ff]"></span>
                                <div class="flex items-start justify-between gap-4">
                                    <div class="text-[11px] font-bold text-slate-400">{{ $slot['start'] }}</div>
                                    <div class="min-w-0 flex-1">
                                        <div
                                            class="truncate text-sm font-black text-slate-800 group-hover:text-indigo-600">
                                            {{ $slot['class_name'] }}
                                        </div>
                                        <div class="mt-1 line-clamp-2 text-[11px] font-medium leading-4 text-slate-400">
                                            {{ $slot['period'] }} class study time
                                        </div>
                                    </div>
                                    <div class="text-[11px] font-semibold text-slate-400">{{ $slot['end'] }}</div>
                                </div>
                            </a>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-200 bg-white/60 p-5 text-sm font-semibold text-slate-400">
                                No upcoming events today.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="dash-reveal {{ $panelClass }} p-5" style="--d: 4;">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-base font-black text-slate-800">My Notes</h2>
                        <span
                            class="rounded-full border border-indigo-100 bg-[#f7f5ff] px-3 py-1.5 text-[10px] font-black text-indigo-500">
                            {{ number_format($focusCount) }} open
                        </span>
                    </div>

                    <div class="space-y-4">
                        @foreach ($noteItems as $note)
                            <a href="{{ $note['route'] }}" class="group flex items-start gap-3">
                                <span
                                    class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full ring-1 {{ $note['color'] }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                    </svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span
                                        class="block truncate text-xs font-black text-slate-800 group-hover:text-indigo-600">
                                        {{ $note['title'] }}
                                    </span>
                                    <span class="mt-0.5 block truncate text-[11px] font-semibold text-slate-400">
                                        {{ number_format($note['count']) }} item{{ $note['count'] === 1 ? '' : 's' }}
                                        require attention
                                    </span>
                                </span>
                                <span class="text-slate-300">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                                    </svg>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="dash-reveal {{ $panelClass }} p-5" style="--d: 5;">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-base font-black text-slate-800">Messages</h2>
                        <a href="{{ route('admin.contacts.index') }}" class="text-xs font-black text-indigo-500">View
                            all</a>
                    </div>
                    <div class="space-y-3">
                        @forelse ($latestContactMessages as $message)
                            <a href="{{ route('admin.contacts.index') }}"
                                class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3">
                                <span
                                    class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white text-indigo-500 shadow-sm">
                                    {{ strtoupper(substr($message->name ?: 'M', 0, 1)) }}
                                </span>
                                <span class="min-w-0">
                                    <span
                                        class="block truncate text-xs font-black text-slate-800">{{ $message->name ?: 'Unknown Sender' }}</span>
                                    <span
                                        class="mt-0.5 block truncate text-[11px] font-semibold text-slate-400">{{ $message->subject ?: 'No subject' }}</span>
                                </span>
                            </a>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-5 text-center text-sm font-semibold text-slate-400">
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
