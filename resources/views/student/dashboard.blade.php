@extends('layout.students.navbar')

@section('page')
    @php
        $fullName = trim((string) ($student->name ?? auth()->user()->name ?? 'Student'));
        $firstName = explode(' ', $fullName)[0] ?? 'Student';
        $hour = (int) now()->hour;
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
        $studentClass = $student->schoolClass?->display_name ?? 'No class assigned';
        $classRoom = trim((string) ($student->schoolClass?->room ?? ''));
        $majorSubjects = collect($majorSubjects ?? []);
        $classSubjects = collect($classSubjects ?? []);
        $weeklySummary = collect($weeklySummary ?? []);
        $latestAttendance = collect($latestAttendance ?? []);
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst((string) $todayKey);
        $attendanceByStatus = $attendanceByStatus ?? ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
        $chartData = $chartData ?? [
            'attendance' => ['labels' => [], 'values' => []],
            'weekly' => ['labels' => [], 'classes' => [], 'subjects' => []],
            'subjects' => ['labels' => [], 'values' => []],
        ];
    @endphp

    <div class="dashboard-stage space-y-6">
        <section class="student-hero relative overflow-hidden rounded-3xl p-6 text-white shadow-lg sm:p-7"
            style="--d: 1; background: linear-gradient(120deg, #312e81 0%, #4f46e5 42%, #0ea5e9 100%);">
            <div
                class="student-hero__orb student-hero__orb--a pointer-events-none absolute -right-10 -top-14 h-44 w-44 rounded-full bg-white/20 blur-2xl">
            </div>
            <div
                class="student-hero__orb student-hero__orb--b pointer-events-none absolute -bottom-14 left-[46%] h-32 w-32 rounded-full bg-white/15 blur-2xl">
            </div>
            <div
                class="student-hero__orb student-hero__orb--c pointer-events-none absolute left-[21%] top-[14%] h-20 w-20 rounded-full bg-white/10 blur-xl">
            </div>

            <div class="student-hero__grid relative z-10 grid items-center gap-4 lg:grid-cols-[minmax(0,1fr)_150px_220px]">
                <div class="student-hero__content min-w-0">
                    <div
                        class="student-hero__badge inline-flex items-center rounded-full border border-white/35 bg-white/15 px-3 py-1 text-xs font-extrabold tracking-wide">
                        Student Dashboard
                    </div>
                    <h1 class="student-hero__title mt-3 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">
                        {{ $greeting }} {{ $firstName }}
                    </h1>
                    <p class="student-hero__subtitle mt-2 max-w-2xl text-sm text-indigo-100">
                        Class: <span>{{ $studentClass }}</span>
                        @if ($classRoom !== '')
                            | Room {{ $classRoom }}
                        @endif
                    </p>
                    <div class="student-hero__chips mt-4 flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span class="student-hero__chip rounded-full border border-white/30 bg-white/15 px-3 py-1.5">
                            Today: {{ $todayLabel }}
                        </span>
                        <span class="student-hero__chip rounded-full border border-white/30 bg-white/15 px-3 py-1.5">
                            Sessions: {{ number_format($todayTimelineCount ?? 0) }}
                        </span>
                        <span class="student-hero__chip rounded-full border border-white/30 bg-white/15 px-3 py-1.5">
                            Attendance: {{ number_format((float) ($attendanceRate ?? 0), 1) }}%
                        </span>
                    </div>
                </div>

                <div class="student-hero__robot-panel mx-auto w-full max-w-[10rem] rounded-2xl border border-white/30 bg-white/10 p-2 backdrop-blur"
                    aria-hidden="true">
                    <svg class="student-hero__robot" viewBox="0 0 180 220" xmlns="http://www.w3.org/2000/svg">
                        <ellipse class="robot-shadow" cx="90" cy="206" rx="44" ry="10" />
                        <g class="robot-float">
                            <line class="robot-antenna-line" x1="90" y1="27" x2="90" y2="42" />
                            <circle class="robot-antenna-dot" cx="90" cy="22" r="6" />

                            <rect class="robot-head" x="50" y="42" width="80" height="58" rx="18" />
                            <rect class="robot-face" x="63" y="54" width="54" height="26" rx="10" />
                            <circle class="robot-eye robot-eye--left" cx="79" cy="67" r="5.4" />
                            <circle class="robot-eye robot-eye--right" cx="101" cy="67" r="5.4" />
                            <rect class="robot-mouth" x="76" y="83" width="28" height="5" rx="2.5" />

                            <rect class="robot-arm robot-arm--left" x="36" y="112" width="14" height="45" rx="7" />
                            <rect class="robot-arm robot-arm--right" x="130" y="110" width="14" height="45" rx="7" />
                            <rect class="robot-body" x="58" y="101" width="64" height="73" rx="18" />
                            <rect class="robot-body-screen" x="73" y="118" width="34" height="24" rx="8" />
                            <circle class="robot-core" cx="90" cy="151" r="8" />

                            <rect class="robot-leg" x="73" y="172" width="14" height="28" rx="6" />
                            <rect class="robot-leg" x="93" y="172" width="14" height="28" rx="6" />
                            <ellipse class="robot-foot" cx="80" cy="201" rx="12" ry="5.5" />
                            <ellipse class="robot-foot" cx="100" cy="201" rx="12" ry="5.5" />
                        </g>
                        <g class="robot-signal">
                            <circle cx="129" cy="38" r="3" />
                            <circle cx="137" cy="32" r="2" />
                            <circle cx="145" cy="26" r="1.5" />
                        </g>
                    </svg>
                    <div class="student-hero__robot-label mt-1 text-center text-[11px] font-bold uppercase tracking-wider text-indigo-100">
                        Study Bot Online
                    </div>
                </div>

                <div class="student-hero__progress rounded-2xl border border-white/25 bg-white/10 p-4 backdrop-blur">
                    <div class="student-hero__progress-label text-xs font-semibold uppercase tracking-wide text-indigo-100">
                        Progress Snapshot
                    </div>
                    <div class="student-hero__progress-value mt-3 text-3xl font-black text-white">
                        {{ number_format((float) ($attendanceRate ?? 0), 1) }}%
                    </div>
                    <div class="student-hero__progress-hint mt-1 text-xs text-indigo-100">Attendance score</div>
                    <div class="student-hero__progress-track mt-4 h-2 overflow-hidden rounded-full bg-white/20">
                        <div class="student-hero__progress-fill"
                            style="width: {{ max(0, min(100, (float) ($attendanceRate ?? 0))) }}%"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dash-reveal grid gap-4 sm:grid-cols-2 xl:grid-cols-4" style="--d: 2;">
            <article class="dash-hover rounded-3xl border border-indigo-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Class Subjects</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($subjectsTotal ?? 0) }}</div>
                <div class="mt-1 text-xs text-slate-500">Available in your class</div>
            </article>

            <article class="dash-hover rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Major Subjects</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($majorSubjectsCount ?? 0) }}</div>
                <div class="mt-1 text-xs text-slate-500">Selected for focus</div>
            </article>

            <article class="dash-hover rounded-3xl border border-sky-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-sky-600">Attendance</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format((float) ($attendanceRate ?? 0), 1) }}%</div>
                <div class="mt-1 text-xs text-slate-500">{{ number_format($attendanceThisMonth ?? 0) }} records this month</div>
            </article>

            <article class="dash-hover rounded-3xl border border-amber-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-amber-600">Study Slots</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($studySlotCount ?? 0) }}</div>
                <div class="mt-1 text-xs text-slate-500">Assigned schedule blocks</div>
            </article>
        </section>

        <section class="dash-reveal grid gap-6 xl:grid-cols-12" style="--d: 3;">
            <article class="dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7">
                <div class="mb-4 flex items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-slate-900">Today Schedule</h2>
                    <span class="text-xs text-slate-500">{{ $todayLabel }}</span>
                </div>

                @if (!empty($todayTimeline))
                    <div class="space-y-3">
                        @foreach ($todayTimeline as $slot)
                            <div class="flex items-start gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-3 py-3">
                                <span
                                    class="mt-1 h-2.5 w-2.5 rounded-full {{ ($slot['type'] ?? '') === 'subject' ? 'bg-sky-500' : 'bg-indigo-500' }}"></span>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-semibold text-slate-900">{{ $slot['title'] ?? 'Session' }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">{{ $slot['subtitle'] ?? '' }}</div>
                                </div>
                                <div class="text-right text-xs font-semibold text-slate-600">
                                    {{ $slot['start'] ?? '--:--' }} - {{ $slot['end'] ?? '--:--' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                        No sessions scheduled for today.
                    </div>
                @endif
            </article>

            <article class="dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5">
                <h2 class="text-base font-bold text-slate-900">Attendance Overview</h2>
                <div class="mt-4 h-56 sm:h-60">
                    <canvas id="studentAttendanceChart" class="h-full w-full"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2">
                        <div class="font-semibold text-emerald-700">Present</div>
                        <div class="mt-0.5 text-slate-700">{{ number_format($attendanceByStatus['present'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-xl border border-amber-100 bg-amber-50 px-3 py-2">
                        <div class="font-semibold text-amber-700">Late</div>
                        <div class="mt-0.5 text-slate-700">{{ number_format($attendanceByStatus['late'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-xl border border-sky-100 bg-sky-50 px-3 py-2">
                        <div class="font-semibold text-sky-700">Excused</div>
                        <div class="mt-0.5 text-slate-700">{{ number_format($attendanceByStatus['excused'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-xl border border-rose-100 bg-rose-50 px-3 py-2">
                        <div class="font-semibold text-rose-700">Absent</div>
                        <div class="mt-0.5 text-slate-700">{{ number_format($attendanceByStatus['absent'] ?? 0) }}</div>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-slate-100 bg-slate-50 p-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recent Checks</div>
                    @if ($latestAttendance->isNotEmpty())
                        <div class="mt-2 space-y-2">
                            @foreach ($latestAttendance as $record)
                                <div class="flex items-center justify-between gap-2 text-xs">
                                    <span class="font-semibold text-slate-700">
                                        {{ optional($record->attendance_date)->format('d M Y') ?? '-' }}
                                    </span>
                                    <span class="capitalize text-slate-500">{{ $record->status }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-2 text-xs text-slate-500">No attendance records yet.</div>
                    @endif
                </div>
            </article>
        </section>

        <section class="dash-reveal grid gap-6 xl:grid-cols-12" style="--d: 4;">
            <article class="dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5">
                <h2 class="text-base font-bold text-slate-900">Weekly Load</h2>
                <div class="mt-4 h-56 sm:h-60">
                    <canvas id="studentWeeklyChart" class="h-full w-full"></canvas>
                </div>
            </article>

            <article class="dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7">
                <div class="mb-5 rounded-2xl border border-slate-100 bg-slate-50 p-3">
                    <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Subject Mix</div>
                    <div class="h-40 sm:h-44">
                        <canvas id="studentSubjectsMixChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Major Subjects</h2>
                        @if ($majorSubjects->isNotEmpty())
                            <div class="mt-3 space-y-2">
                                @foreach ($majorSubjects as $subject)
                                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2">
                                        <div class="text-sm font-semibold text-slate-800">{{ $subject->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $subject->code ?: 'No code' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-5 text-xs text-slate-500">
                                No major subjects selected.
                            </div>
                        @endif
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-900">Class Subjects</h2>
                        @if ($classSubjects->isNotEmpty())
                            <div class="mt-3 space-y-2">
                                @foreach ($classSubjects as $subject)
                                    <div class="rounded-xl border border-sky-100 bg-sky-50 px-3 py-2">
                                        <div class="text-sm font-semibold text-slate-800">{{ $subject->name }}</div>
                                        <div class="text-xs text-slate-500">
                                            {{ $subject->code ?: 'No code' }}
                                            @if ($subject->teacher?->name)
                                                | {{ $subject->teacher->name }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-5 text-xs text-slate-500">
                                No subjects assigned to your class.
                            </div>
                        @endif
                    </div>
                </div>
            </article>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dashboardCharts = @json($chartData);

            if (!window.Chart) {
                return;
            }

            Chart.defaults.responsive = true;
            Chart.defaults.maintainAspectRatio = false;

            const attendanceCanvas = document.getElementById('studentAttendanceChart');
            if (attendanceCanvas) {
                new Chart(attendanceCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: dashboardCharts?.attendance?.labels ?? [],
                        datasets: [{
                            data: dashboardCharts?.attendance?.values ?? [],
                            borderWidth: 0,
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.9)',
                                'rgba(245, 158, 11, 0.9)',
                                'rgba(14, 165, 233, 0.9)',
                                'rgba(244, 63, 94, 0.9)',
                            ],
                            hoverOffset: 6,
                        }, ],
                    },
                    options: {
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                },
                            },
                        },
                    },
                });
            }

            const weeklyCanvas = document.getElementById('studentWeeklyChart');
            if (weeklyCanvas) {
                new Chart(weeklyCanvas, {
                    type: 'bar',
                    data: {
                        labels: dashboardCharts?.weekly?.labels ?? [],
                        datasets: [{
                                label: 'Class Sessions',
                                data: dashboardCharts?.weekly?.classes ?? [],
                                borderRadius: 8,
                                backgroundColor: 'rgba(56, 189, 248, 0.8)',
                            },
                            {
                                label: 'Subject Sessions',
                                data: dashboardCharts?.weekly?.subjects ?? [],
                                borderRadius: 8,
                                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                            },
                        ],
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                },
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    stepSize: 1,
                                },
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.2)',
                                },
                            },
                            x: {
                                grid: {
                                    display: false,
                                },
                            },
                        },
                    },
                });
            }

            const subjectsMixCanvas = document.getElementById('studentSubjectsMixChart');
            if (subjectsMixCanvas) {
                new Chart(subjectsMixCanvas, {
                    type: 'pie',
                    data: {
                        labels: dashboardCharts?.subjects?.labels ?? [],
                        datasets: [{
                            data: dashboardCharts?.subjects?.values ?? [],
                            borderWidth: 0,
                            backgroundColor: ['rgba(99, 102, 241, 0.85)', 'rgba(16, 185, 129, 0.85)'],
                        }, ],
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                },
                            },
                        },
                    },
                });
            }
        });
    </script>
@endsection
