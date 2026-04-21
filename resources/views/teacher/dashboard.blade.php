@extends('layout.teacher.navbar')

@section('page')
    @php
        $todayDate = now();
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst($todayKey);
        $teacherName = trim((string) auth()->user()->name);
        $teacherFirstName = explode(' ', $teacherName)[0] ?? $teacherName;
        $studentBarTones = [
            'teacher-progress__bar--blue',
            'teacher-progress__bar--orange',
            'teacher-progress__bar--coral',
            'teacher-progress__bar--sky',
        ];
        $classBadgeTones = [
            'teacher-resource__badge--amber',
            'teacher-resource__badge--rose',
            'teacher-resource__badge--sky',
        ];
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
    @endphp

    <div class="teacher-overview dashboard-stage space-y-6">
        <div class="teacher-overview__grid">
            <div class="space-y-6">
                <section class="dash-reveal teacher-surface teacher-hero" style="--d: 1;">
                    <div class="teacher-hero__content">
                        <div class="space-y-4">
                            <div class="space-y-3">
                                <h1 class="teacher-hero__title">
                                    Welcome Back, <span>{{ $teacherFirstName }}!</span>
                                </h1>
                                <p class="teacher-hero__lead">
                                    {{ $heroLine }}
                                </p>
                                <p class="teacher-hero__sublead">
                                    Today's subject coverage is <strong>{{ $coveragePercent }}%</strong>.
                                    {{ $progressLine }}
                                </p>
                            </div>

                            <div class="teacher-hero__stats">
                                <span class="teacher-hero__pill">Classes {{ number_format($stats['classes']) }}</span>
                                <span class="teacher-hero__pill teacher-hero__pill--soft">Students
                                    {{ number_format($stats['students']) }}</span>
                                <span class="teacher-hero__pill teacher-hero__pill--warm">Subjects
                                    {{ number_format($stats['subjects']) }}</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <a href="{{ route('teacher.schedule.index', ['day' => $todayKey]) }}"
                                    class="teacher-hero__button">
                                    Open today schedule
                                </a>
                                <span class="teacher-hero__date">
                                    {{ $todayLabel }} - {{ $todayDate->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="teacher-hero__art" aria-hidden="true">
                            <img src="{{ asset('images/9865735.png') }}" alt="Teacher dashboard study illustration"
                                class="teacher-hero__image">
                        </div>
                    </div>
                </section>

                <div class="grid gap-6 xl:grid-cols-[minmax(0,1.05fr)_minmax(320px,0.95fr)]">
                    <section class="dash-reveal teacher-surface teacher-students" style="--d: 2;">
                        <div class="teacher-card__head">
                            <h2>My Students</h2>
                            <a href="{{ route('teacher.classes.index') }}">View all</a>
                        </div>

                        <div class="space-y-4">
                            @forelse ($featuredStudents as $index => $student)
                                @php
                                    $progressPercent = $student['attendance_percent'] ?? 0;
                                    $progressLabel =
                                        $student['attendance_percent'] !== null
                                            ? $student['attendance_percent'] . '%'
                                            : 'No record';
                                @endphp
                                <div class="teacher-student">
                                    <div class="teacher-student__identity">
                                        <img src="{{ $student['avatar_url'] }}" alt="{{ $student['name'] }}"
                                            class="teacher-student__avatar">
                                        <div class="min-w-0">
                                            <div class="teacher-student__name">{{ $student['name'] }}</div>
                                            <div class="teacher-student__meta">{{ $student['class_label'] }}</div>
                                        </div>
                                    </div>

                                    <div class="teacher-progress">
                                        <div class="teacher-progress__track">
                                            <span
                                                class="teacher-progress__bar {{ $studentBarTones[$index % count($studentBarTones)] }}"
                                                style="width: {{ $progressPercent }}%"></span>
                                        </div>
                                    </div>

                                    <div class="teacher-student__score">{{ $progressLabel }}</div>
                                </div>
                            @empty
                                <div class="teacher-empty">
                                    No students are assigned to your classes yet.
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <section class="dash-reveal teacher-surface teacher-workload" style="--d: 3;">
                        <div class="teacher-card__head">
                            <h2>Working hours</h2>
                            <span class="teacher-card__filter">Today</span>
                        </div>

                        <div class="teacher-workload__chart">
                            <canvas id="teacherWorkloadChart"></canvas>
                        </div>

                        <div class="teacher-workload__legend">
                            <div class="teacher-workload__item">
                                <span class="teacher-workload__dot teacher-workload__dot--amber"></span>
                                <span>Progress</span>
                            </div>
                            <div class="teacher-workload__item">
                                <span class="teacher-workload__dot teacher-workload__dot--blue"></span>
                                <span>Done</span>
                            </div>
                        </div>

                        <div class="teacher-workload__meta">
                            <div>
                                <strong>{{ $stats['todaySchedules'] }}</strong>
                                <span>lesson slots today</span>
                            </div>
                            <div>
                                <strong>{{ $coveragePercent }}%</strong>
                                <span>subject coverage</span>
                            </div>
                        </div>
                    </section>
                </div>

                <section class="dash-reveal teacher-surface teacher-resources" style="--d: 4;">
                    <div class="space-y-3">
                        @forelse ($featuredClasses as $index => $classItem)
                            <div class="teacher-resource">
                                <div class="teacher-resource__main">
                                    <span
                                        class="teacher-resource__badge {{ $classBadgeTones[$index % count($classBadgeTones)] }}">
                                        {{ $classItem['badge'] }}
                                    </span>
                                    <div class="teacher-resource__copy">
                                        <div class="teacher-resource__title">{{ $classItem['title'] }}</div>
                                        <div class="teacher-resource__meta">{{ $classItem['meta'] }}</div>
                                    </div>
                                </div>

                                <div class="teacher-resource__status">{{ $classItem['action'] }}</div>
                                <div class="teacher-resource__count">{{ $classItem['members'] }} members</div>
                                <div class="teacher-resource__size">{{ $classItem['capacity_label'] }}</div>
                            </div>
                        @empty
                            <div class="teacher-empty">
                                No classes are available for your dashboard yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-5">
                <section class="dash-reveal teacher-surface teacher-rail-profile" style="--d: 2;">
                    <div class="teacher-rail-profile__top">
                        <div>
                            <div class="teacher-rail-profile__label">Teacher panel</div>
                            <div class="teacher-rail-profile__name">{{ $teacherName }}</div>
                        </div>
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ $teacherName }}"
                            onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                            class="teacher-rail-profile__avatar">
                    </div>
                </section>

                <section class="dash-reveal teacher-surface teacher-calendar" style="--d: 3;">
                    <div class="teacher-card__head">
                        <div>
                            <div class="teacher-calendar__label">Calendar</div>
                            <h2 id="teacher-dashboard-calendar-label">{{ $todayDate->format('F Y') }}</h2>
                        </div>
                        <span class="teacher-calendar__arrow">-></span>
                    </div>

                    <div class="teacher-calendar__weekdays">
                        <span>Mo</span>
                        <span>Tu</span>
                        <span>We</span>
                        <span>Th</span>
                        <span>Fr</span>
                        <span>Sa</span>
                        <span>Su</span>
                    </div>

                    <div id="teacher-dashboard-calendar" class="teacher-calendar__grid"></div>
                </section>

                <section class="dash-reveal teacher-surface teacher-lessons" style="--d: 4;">
                    <div class="teacher-card__head">
                        <h2>Lessons</h2>
                        <a href="{{ route('teacher.schedule.index') }}">View all</a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($lessonCards as $index => $lesson)
                            <article class="teacher-lesson js-dash-slot" data-start="{{ $lesson['start_24'] ?? '' }}"
                                data-end="{{ $lesson['end_24'] ?? '' }}">
                                <span
                                    class="teacher-lesson__accent {{ $index % 2 === 0 ? 'teacher-lesson__accent--orange' : 'teacher-lesson__accent--blue' }}"></span>
                                <div class="teacher-lesson__content">
                                    <div class="teacher-lesson__title">{{ $lesson['subject_label'] }}</div>
                                    <div class="teacher-lesson__time">
                                        {{ $lesson['day_label'] }} - {{ $lesson['start'] }} - {{ $lesson['end'] }}
                                    </div>
                                    <div class="teacher-lesson__class">{{ $lesson['class_name'] }}</div>
                                </div>
                                <span class="js-dash-slot-status teacher-lesson__status">Scheduled</span>
                            </article>
                        @empty
                            <div class="teacher-empty">
                                No lessons are scheduled for today.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="dash-reveal teacher-surface teacher-updates" style="--d: 5;">
                    <div class="teacher-card__head">
                        <h2>Completed tasks</h2>
                        <a href="{{ route('teacher.notices.index') }}">View all</a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($recentUpdates as $notice)
                            <article class="teacher-update">
                                <span class="teacher-update__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14" stroke-linecap="round" />
                                        <path d="M12 5v14" stroke-linecap="round" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <div class="teacher-update__title">{{ $notice->title }}</div>
                                    <div class="teacher-update__meta">{{ $notice->created_at?->diffForHumans() }}</div>
                                </div>
                                <span class="teacher-update__arrow">></span>
                            </article>
                        @empty
                            <div class="teacher-empty">
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
