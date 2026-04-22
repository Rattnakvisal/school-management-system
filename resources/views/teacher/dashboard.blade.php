@extends('layout.teacher.navbar')

@section('page')
    @php
        $todayDate = now();
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst($todayKey);
        $teacherName = trim((string) auth()->user()->name);
        $teacherFirstName = explode(' ', $teacherName)[0] ?? $teacherName;

        $lessonCards = collect($todayTimeline ?? [])
            ->take(3)
            ->values();
        $recentUpdates = collect($navNotifs ?? [])
            ->take(3)
            ->values();

        $workloadPercent = (int) ($workloadPercent ?? 0);
        $coveragePercent = (int) ($coveragePercent ?? 0);

        $heroLine =
            $stats['todaySchedules'] > 0
                ? "You've planned {$stats['todaySchedules']} lesson slot" .
                    ($stats['todaySchedules'] === 1 ? '' : 's') .
                    " for {$todayLabel}."
                : "You don't have lesson slots scheduled for {$todayLabel} yet.";

        $progressLine =
            $coveragePercent >= 80
                ? 'Subject coverage looks very good.'
                : ($coveragePercent >= 50
                    ? 'Your day is nicely balanced.'
                    : 'You can still add more subject coverage.');

        $dashboardData = [
            'todayKey' => $todayKey,
            'dayLabels' => $dayLabels ?? [],
            'workloadPercent' => $workloadPercent,
            'calendar' => [
                'year' => (int) $todayDate->year,
                'month' => (int) $todayDate->month,
                'day' => (int) $todayDate->day,
            ],
        ];

        $panelClass =
            'rounded-[28px] border border-slate-200/80 bg-white/90 shadow-[0_18px_50px_-30px_rgba(15,23,42,0.22)] backdrop-blur-sm';
        $panelPadding = 'p-5 sm:p-6';
        $titleClass = 'text-lg font-extrabold tracking-[-0.03em] text-slate-900';
        $mutedLinkClass = 'text-sm font-semibold text-slate-400 transition hover:text-slate-600';
    @endphp

    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px] xl:items-start">
            <div class="space-y-6">
                {{-- Hero --}}
                <section
                    class="dash-reveal relative overflow-hidden rounded-[32px] border border-slate-200/80 bg-gradient-to-br from-white via-slate-50 to-indigo-50/60 px-6 py-7 shadow-[0_20px_60px_-30px_rgba(15,23,42,0.24)] sm:px-8 sm:py-8"
                    style="--d: 1;">
                    <div
                        class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.12),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(251,191,36,0.10),transparent_28%)]">
                    </div>

                    <div class="relative grid items-center gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(280px,360px)]">
                        <div class="space-y-5">
                            <div class="space-y-3">
                                <span
                                    class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-indigo-700">
                                    Teacher dashboard
                                </span>

                                <h1
                                    class="text-[clamp(2rem,4vw,3.25rem)] font-black leading-[0.95] tracking-[-0.06em] text-slate-900">
                                    Welcome back,
                                    <span
                                        class="bg-gradient-to-r from-indigo-700 to-blue-500 bg-clip-text text-transparent">
                                        {{ $teacherFirstName }}
                                    </span>
                                </h1>

                                <p class="max-w-2xl text-[1rem] leading-7 text-slate-600 sm:text-[1.05rem]">
                                    {{ $heroLine }}
                                </p>

                                <p class="max-w-2xl text-[1rem] leading-7 text-slate-500 sm:text-[1.05rem]">
                                    Today's subject coverage is
                                    <strong class="font-extrabold text-rose-500">{{ $coveragePercent }}%</strong>.
                                    {{ $progressLine }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700 ring-1 ring-blue-100">
                                    Classes {{ number_format($stats['classes']) }}
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-amber-50 px-4 py-2 text-sm font-bold text-amber-700 ring-1 ring-amber-100">
                                    Students {{ number_format($stats['students']) }}
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 ring-1 ring-rose-100">
                                    Subjects {{ number_format($stats['subjects']) }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <a href="{{ route('teacher.schedule.index', ['day' => $todayKey]) }}"
                                    class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 px-5 py-3 text-sm font-bold text-white shadow-[0_18px_30px_-16px_rgba(59,130,246,0.5)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_34px_-16px_rgba(59,130,246,0.55)]">
                                    Open today schedule
                                </a>

                                <span class="text-sm font-semibold text-slate-400">
                                    {{ $todayLabel }} • {{ $todayDate->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="relative flex items-center justify-center">
                            <div class="absolute inset-x-8 inset-y-8 rounded-full bg-indigo-100/60 blur-3xl"></div>
                            <img src="{{ asset('images/9865735.png') }}" alt="Teacher dashboard study illustration"
                                class="relative block h-auto w-full max-w-[28rem] rounded-[30px] object-cover drop-shadow-[0_18px_40px_rgba(59,130,246,0.20)]">
                        </div>
                    </div>
                </section>

                <div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
                    {{-- My Students --}}
                    <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 2;">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <div>
                                <h2 class="{{ $titleClass }}">My Students</h2>
                                <p class="mt-1 text-sm text-slate-400">Attendance snapshot and class overview</p>
                            </div>
                            <a href="{{ route('teacher.classes.index') }}" class="{{ $mutedLinkClass }}">
                                View all
                            </a>
                        </div>

                        <div class="space-y-4">
                            @forelse ($featuredStudents as $index => $student)
                                @php
                                    $progressPercent = $student['attendance_percent'] ?? 0;
                                    $progressLabel =
                                        $student['attendance_percent'] !== null
                                            ? $student['attendance_percent'] . '%'
                                            : 'No record';

                                    $barClass = match ($index % 4) {
                                        0 => 'from-blue-600 to-indigo-600',
                                        1 => 'from-amber-400 to-orange-500',
                                        2 => 'from-rose-400 to-pink-500',
                                        default => 'from-cyan-400 to-sky-500',
                                    };
                                @endphp

                                <div
                                    class="grid items-center gap-4 rounded-2xl border border-slate-100 bg-slate-50/70 p-4 transition hover:border-slate-200 hover:bg-white sm:grid-cols-[minmax(0,1.25fr)_minmax(110px,1fr)_auto]">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <img src="{{ $student['avatar_url'] }}" alt="{{ $student['name'] }}"
                                            class="h-11 w-11 shrink-0 rounded-full object-cover ring-2 ring-white shadow-sm">

                                        <div class="min-w-0">
                                            <div class="truncate text-[15px] font-bold text-slate-800">
                                                {{ $student['name'] }}
                                            </div>
                                            <div class="mt-0.5 truncate text-xs text-slate-400">
                                                {{ $student['class_label'] }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                        <span class="block h-full rounded-full bg-gradient-to-r {{ $barClass }}"
                                            style="width: {{ $progressPercent }}%"></span>
                                    </div>

                                    <div class="text-sm font-extrabold text-slate-800">
                                        {{ $progressLabel }}
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-400">
                                    No students are assigned to your classes yet.
                                </div>
                            @endforelse
                        </div>
                    </section>

                    {{-- Working Hours --}}
                    <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 3;">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <div>
                                <h2 class="{{ $titleClass }}">Working Hours</h2>
                                <p class="mt-1 text-sm text-slate-400">Your progress for today</p>
                            </div>

                            <span
                                class="rounded-full bg-amber-50 px-4 py-1.5 text-xs font-bold text-amber-700 ring-1 ring-amber-100">
                                Today
                            </span>
                        </div>

                        <div class="relative h-[230px]">
                            <canvas id="teacherWorkloadChart"></canvas>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-4 max-sm:flex-col max-sm:items-start">
                            <div class="inline-flex items-center gap-2 text-sm font-medium text-slate-600">
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                                <span>Progress</span>
                            </div>
                            <div class="inline-flex items-center gap-2 text-sm font-medium text-slate-600">
                                <span class="h-2.5 w-2.5 rounded-full bg-indigo-600"></span>
                                <span>Done</span>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-4 border-t border-slate-100 pt-5">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <strong class="block text-lg font-extrabold text-slate-900">
                                    {{ $stats['todaySchedules'] }}
                                </strong>
                                <span class="text-xs font-medium text-slate-400">lesson slots today</span>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4">
                                <strong class="block text-lg font-extrabold text-slate-900">
                                    {{ $coveragePercent }}%
                                </strong>
                                <span class="text-xs font-medium text-slate-400">subject coverage</span>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- Classes --}}
                <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 4;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="{{ $titleClass }}">Featured Classes</h2>
                            <p class="mt-1 text-sm text-slate-400">Quick access to your active classes</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse ($featuredClasses as $index => $classItem)
                            @php
                                $badgeClass = match ($index % 3) {
                                    0 => 'bg-amber-100 text-amber-700',
                                    1 => 'bg-rose-100 text-rose-700',
                                    default => 'bg-sky-100 text-sky-700',
                                };
                            @endphp

                            <div
                                class="grid items-center gap-4 rounded-3xl border border-slate-100 bg-slate-50/70 p-4 transition hover:border-slate-200 hover:bg-white lg:grid-cols-[minmax(0,1.6fr)_minmax(140px,1fr)_auto_auto]">
                                <div class="flex min-w-0 items-center gap-4">
                                    <span
                                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl text-sm font-extrabold {{ $badgeClass }}">
                                        {{ $classItem['badge'] }}
                                    </span>

                                    <div class="min-w-0">
                                        <div class="truncate text-[15px] font-extrabold text-slate-800">
                                            {{ $classItem['title'] }}
                                        </div>
                                        <div class="truncate text-sm text-slate-400">
                                            {{ $classItem['meta'] }}
                                        </div>
                                    </div>
                                </div>

                                <div class="text-sm font-semibold text-slate-600">
                                    {{ $classItem['action'] }}
                                </div>

                                <div class="text-sm text-slate-400">
                                    {{ $classItem['members'] }} members
                                </div>

                                <div class="text-sm font-extrabold text-slate-700">
                                    {{ $classItem['capacity_label'] }}
                                </div>
                            </div>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-400">
                                No classes are available for your dashboard yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-5">
                {{-- Profile --}}
                <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 2;">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-slate-400">
                                Teacher panel
                            </div>
                            <div class="mt-1 text-base font-extrabold text-slate-900">
                                {{ $teacherName }}
                            </div>
                            <div class="mt-1 text-sm text-slate-400">
                                Ready to manage today’s lessons
                            </div>
                        </div>

                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ $teacherName }}"
                            onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                            class="h-12 w-12 rounded-full object-cover ring-2 ring-white shadow-md">
                    </div>
                </section>

                {{-- Calendar --}}
                <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 3;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Calendar</div>
                            <h2 id="teacher-dashboard-calendar-label" class="{{ $titleClass }}">
                                {{ $todayDate->format('F Y') }}
                            </h2>
                        </div>
                        <span
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-sky-50 text-lg font-bold text-sky-500">
                            →
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

                    <div id="teacher-dashboard-calendar" class="grid grid-cols-7 gap-1.5"></div>
                </section>

                {{-- Lessons --}}
                <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 4;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="{{ $titleClass }}">Lessons</h2>
                            <p class="mt-1 text-sm text-slate-400">Upcoming and live schedule items</p>
                        </div>

                        <a href="{{ route('teacher.schedule.index') }}" class="{{ $mutedLinkClass }}">
                            View all
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($lessonCards as $index => $lesson)
                            @php
                                $accentClass =
                                    $index % 2 === 0 ? 'from-orange-500 to-amber-400' : 'from-indigo-600 to-blue-500';
                            @endphp

                            <article
                                class="js-dash-slot flex gap-4 rounded-2xl border border-slate-100 bg-slate-50/70 p-4 transition hover:border-slate-200 hover:bg-white {{ $lesson['is_live'] ?? false ? 'ring-1 ring-indigo-100 shadow-[0_16px_30px_-20px_rgba(79,70,229,0.30)]' : '' }}"
                                data-start="{{ $lesson['start_24'] ?? '' }}" data-end="{{ $lesson['end_24'] ?? '' }}">
                                <span class="w-1 shrink-0 rounded-full bg-gradient-to-b {{ $accentClass }}"></span>

                                <div class="min-w-0 flex-1">
                                    <div class="text-[15px] font-extrabold text-slate-900">
                                        {{ $lesson['subject_label'] }}
                                    </div>
                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ $lesson['day_label'] }} • {{ $lesson['start'] }} - {{ $lesson['end'] }}
                                    </div>
                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ $lesson['class_name'] }}
                                    </div>
                                </div>

                                <span
                                    class="js-dash-slot-status self-center whitespace-nowrap rounded-full bg-slate-100 px-3 py-1.5 text-[11px] font-extrabold text-slate-500">
                                    Scheduled
                                </span>
                            </article>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-400">
                                No lessons are scheduled for today.
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- Updates --}}
                <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 5;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="{{ $titleClass }}">Completed Tasks</h2>
                            <p class="mt-1 text-sm text-slate-400">Recent notices and completed items</p>
                        </div>

                        <a href="{{ route('teacher.notices.index') }}" class="{{ $mutedLinkClass }}">
                            View all
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($recentUpdates as $notice)
                            <article
                                class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-4 transition hover:border-slate-200 hover:bg-white">
                                <span
                                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        class="h-4 w-4">
                                        <path d="M5 12h14" stroke-linecap="round" />
                                        <path d="M12 5v14" stroke-linecap="round" />
                                    </svg>
                                </span>

                                <div class="min-w-0">
                                    <div class="truncate text-sm font-bold text-slate-900">
                                        {{ $notice->title }}
                                    </div>
                                    <div class="mt-0.5 text-xs text-slate-400">
                                        {{ $notice->created_at?->diffForHumans() }}
                                    </div>
                                </div>

                                <span class="ml-auto text-xl text-slate-300">›</span>
                            </article>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-400">
                                No recent tasks are available yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script id="teacher-dashboard-data" type="application/json">@json($dashboardData)</script>
    @vite(['resources/js/Teacher/dashboard.js'])
@endsection
