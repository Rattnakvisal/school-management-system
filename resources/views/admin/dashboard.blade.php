@extends('layout.admin.navbar.navbar')

@section('page')
    @php
        $majorRate = ($studentsTotal ?? 0) > 0 ? round((($studentsWithMajor ?? 0) / $studentsTotal) * 100, 1) : 0;
        $studyTimeRate = ($studentsTotal ?? 0) > 0 ? round((($studentsWithStudyTime ?? 0) / $studentsTotal) * 100, 1) : 0;
        $fullName = trim((string) (auth()->user()->name ?? 'Admin'));
        $firstName = explode(' ', $fullName)[0] ?? 'Admin';
        $hour = (int) now()->hour;
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
        $studentsNeedStudyTime = max(0, (int) ($studentsTotal ?? 0) - (int) ($studentsWithStudyTime ?? 0));
        $focusCount = (int) ($messagesUnread ?? 0) + $studentsNeedStudyTime;
        $trendChartData = $chartData['trend'] ?? ['labels' => [], 'students' => [], 'teachers' => []];
        $studentProfileChartData = $chartData['studentProfile'] ?? ['labels' => [], 'values' => []];
        $compositionChartData = $chartData['composition'] ?? ['labels' => [], 'values' => []];
        $classLoadChartData = $chartData['classLoad'] ?? ['labels' => [], 'values' => []];
        $subjectHealthChartData = $chartData['subjectHealth'] ?? ['labels' => [], 'values' => []];
        $periodChartData = $chartData['periods'] ?? ['labels' => [], 'values' => []];
        $attendanceChartData = $chartData['attendance'] ?? ['labels' => [], 'present' => [], 'absent' => [], 'hasData' => false];
    @endphp

    <div class="dashboard-stage space-y-6">
        <section class="dash-reveal relative overflow-hidden rounded-3xl border border-indigo-300/30 bg-gradient-to-r from-indigo-600 via-indigo-500 to-blue-500 p-6 text-white shadow-lg shadow-indigo-500/20"
            style="--d: 1;">
            <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-white/15 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-14 right-20 h-44 w-44 rounded-full bg-cyan-200/25 blur-2xl"></div>

            <div class="relative grid items-center gap-6 lg:grid-cols-[1fr_280px]">
                <div>
                    <div class="text-sm font-semibold text-indigo-100">Dashboard</div>
                    <h1 class="mt-1 text-3xl font-black tracking-tight">{{ $greeting }} {{ $firstName }}</h1>
                    <p class="mt-2 max-w-2xl text-sm text-indigo-100">
                        You have {{ number_format($focusCount) }} item{{ $focusCount === 1 ? '' : 's' }} to review today.
                        Unread messages: {{ number_format($messagesUnread ?? 0) }}, students without study time:
                        {{ number_format($studentsNeedStudyTime) }}.
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.contacts.index') }}"
                            class="rounded-xl border border-white/35 bg-white px-4 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-50">
                            Review Inbox
                        </a>
                        <a href="{{ route('admin.student-study.index') }}"
                            class="rounded-xl border border-white/35 bg-white/10 px-4 py-2 text-xs font-semibold text-white transition hover:bg-white/20">
                            Open Study List
                        </a>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span class="rounded-full border border-white/25 bg-white/15 px-3 py-1.5">Students:
                            {{ number_format($studentsTotal ?? 0) }}</span>
                        <span class="rounded-full border border-white/25 bg-white/15 px-3 py-1.5">Teachers:
                            {{ number_format($teachersTotal ?? 0) }}</span>
                        <span class="rounded-full border border-white/25 bg-white/15 px-3 py-1.5">Classes:
                            {{ number_format($classesTotal ?? 0) }}</span>
                    </div>
                </div>

                <div class="hidden lg:flex lg:justify-end">
                    <svg viewBox="0 0 240 170" class="h-40 w-auto" fill="none" xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true">
                        <ellipse cx="123" cy="145" rx="85" ry="16" fill="rgba(15,23,42,0.25)" />
                        <circle cx="132" cy="73" r="58" fill="rgba(191,219,254,0.3)" />
                        <path d="M120 57c0-12 9-22 21-22s21 10 21 22v23h-42V57Z" fill="#0F172A" />
                        <circle cx="141" cy="45" r="13" fill="#F8D1B2" />
                        <path
                            d="M109 92c0-12 10-22 22-22h7c13 0 24 10 24 23v30h-53V92Z"
                            fill="#E2E8F0" />
                        <path d="M104 122h64a8 8 0 0 1 8 8v8H96v-8a8 8 0 0 1 8-8Z" fill="#94A3B8" />
                        <path d="M104 102h40a7 7 0 0 1 7 7v13h-47v-20Z" fill="#1E293B" />
                        <path d="M154 91h17l9 25h-19l-7-25Z" fill="#3B82F6" />
                        <path d="M116 71c7 4 13 5 19 3l4 10c-9 3-18 2-27-4l4-9Z" fill="#F8D1B2" />
                    </svg>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <article class="dash-reveal dash-hover rounded-3xl border border-indigo-100 bg-indigo-50/80 p-5"
                style="--d: 2;">
                <div class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Students</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($studentsTotal ?? 0) }}</div>
                <div class="mt-1 text-xs text-slate-500">Active: {{ number_format($studentsActive ?? 0) }}</div>
            </article>

            <article class="dash-reveal dash-hover rounded-3xl border border-sky-100 bg-sky-50/80 p-5" style="--d: 3;">
                <div class="text-xs font-semibold uppercase tracking-wide text-sky-600">Teachers</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($teachersTotal ?? 0) }}</div>
                <div class="mt-1 text-xs text-slate-500">Active: {{ number_format($teachersActive ?? 0) }}</div>
            </article>

            <article class="dash-reveal dash-hover rounded-3xl border border-emerald-100 bg-emerald-50/80 p-5"
                style="--d: 4;">
                <div class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Classes</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($classesTotal ?? 0) }}</div>
                <div class="mt-1 text-xs text-slate-500">Active: {{ number_format($classesActive ?? 0) }}</div>
            </article>

            <article class="dash-reveal dash-hover rounded-3xl border border-amber-100 bg-amber-50/80 p-5" style="--d: 5;">
                <div class="text-xs font-semibold uppercase tracking-wide text-amber-600">Subjects</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($subjectsTotal ?? 0) }}</div>
                <div class="mt-1 text-xs text-slate-500">Active: {{ number_format($subjectsActive ?? 0) }}</div>
            </article>

            <article class="dash-reveal dash-hover rounded-3xl border border-rose-100 bg-rose-50/80 p-5" style="--d: 6;">
                <div class="text-xs font-semibold uppercase tracking-wide text-rose-600">Messages</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($messagesTotal ?? 0) }}</div>
                <div class="mt-1 text-xs text-slate-500">Unread: {{ number_format($messagesUnread ?? 0) }}</div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-12">
            <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--d: 7;">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-slate-900">Students</h2>
                    <span class="text-xs text-slate-500">Active vs Inactive</span>
                </div>
                <div class="h-[280px]">
                    <canvas id="studentsSnapshotChart"></canvas>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-3 text-center">
                    <div class="rounded-xl border border-sky-100 bg-sky-50 px-3 py-2">
                        <div class="text-xs font-semibold text-slate-500">Active</div>
                        <div class="text-lg font-black text-slate-900">{{ number_format($studentsActive ?? 0) }}</div>
                    </div>
                    <div class="rounded-xl border border-amber-100 bg-amber-50 px-3 py-2">
                        <div class="text-xs font-semibold text-slate-500">Inactive</div>
                        <div class="text-lg font-black text-slate-900">
                            {{ number_format(max(0, ($studentsTotal ?? 0) - ($studentsActive ?? 0))) }}
                        </div>
                    </div>
                </div>
            </article>

            <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--d: 8;">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-slate-900">Attendance</h2>
                    <span class="text-xs text-slate-500">Last 5 Days</span>
                </div>
                <div class="h-[320px]">
                    <canvas id="attendanceOverviewChart"></canvas>
                </div>
                @if (!($chartData['attendance']['hasData'] ?? false))
                    <p class="mt-3 text-xs text-slate-500">
                        No attendance records found yet. Chart will update automatically when attendance data is available.
                    </p>
                @endif
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-12">
            <div class="space-y-6 xl:col-span-8">
                <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                    style="--d: 9;">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-base font-bold text-slate-900">Enrollment Trend (Last 6 Months)</h2>
                        <span class="text-xs text-slate-500">Students vs Teachers</span>
                    </div>
                    <div class="h-[320px]">
                        <canvas id="enrollmentTrendChart"></canvas>
                    </div>
                </article>

                <div class="grid gap-6 lg:grid-cols-2">
                    <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 10;">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">System Composition</h2>
                            <span class="text-xs text-slate-500">Population split</span>
                        </div>
                        <div class="h-[280px]">
                            <canvas id="compositionChart"></canvas>
                        </div>
                    </article>

                    <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 11;">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">Study Period Distribution</h2>
                            <span class="text-xs text-slate-500">Morning, night, and more</span>
                        </div>
                        <div class="h-[280px]">
                            <canvas id="periodChart"></canvas>
                        </div>
                    </article>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 12;">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">Top Class Load</h2>
                            <span class="text-xs text-slate-500">Students per class</span>
                        </div>
                        <div class="h-[300px]">
                            <canvas id="classLoadChart"></canvas>
                        </div>
                    </article>

                    <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 13;">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">Subject Health</h2>
                            <span class="text-xs text-slate-500">Status and assignment</span>
                        </div>
                        <div class="h-[300px]">
                            <canvas id="subjectHealthChart"></canvas>
                        </div>
                    </article>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-4">
                <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                    style="--d: 14;">
                    <h2 class="text-base font-bold text-slate-900">Study Profile</h2>
                    <p class="mt-1 text-xs text-slate-500">How well students are mapped to major subjects and study times.
                    </p>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">With Major Subject
                            </div>
                            <div class="mt-2 text-2xl font-black text-slate-900">
                                {{ number_format($studentsWithMajor ?? 0) }}</div>
                            <div class="mt-1 text-xs text-slate-500">{{ $majorRate }}% of students</div>
                            <div class="mt-3 h-2 rounded-full bg-slate-200">
                                <div class="h-2 rounded-full bg-indigo-500"
                                    style="width: {{ min(100, max(0, $majorRate)) }}%"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">With Study Time</div>
                            <div class="mt-2 text-2xl font-black text-slate-900">
                                {{ number_format($studentsWithStudyTime ?? 0) }}</div>
                            <div class="mt-1 text-xs text-slate-500">{{ $studyTimeRate }}% of students</div>
                            <div class="mt-3 h-2 rounded-full bg-slate-200">
                                <div class="h-2 rounded-full bg-cyan-500"
                                    style="width: {{ min(100, max(0, $studyTimeRate)) }}%"></div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                    style="--d: 15;">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900">Latest Messages</h2>
                        <a href="{{ route('admin.contacts.index') }}" class="text-xs font-semibold text-slate-400">View
                            all</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse (($latestContactMessages ?? []) as $contactMessage)
                            @php
                                $name = trim((string) ($contactMessage->name ?? 'Unknown Sender'));
                                $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                                $initials = '';
                                foreach (array_slice($parts, 0, 2) as $part) {
                                    $initials .= strtoupper(substr($part, 0, 1));
                                }
                                if ($initials === '') {
                                    $initials = 'UN';
                                }
                            @endphp
                            <a href="{{ route('admin.contacts.index') }}"
                                class="block rounded-2xl border border-slate-100 p-3 transition hover:bg-slate-50">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="grid h-10 w-10 place-items-center rounded-full font-bold {{ $contactMessage->is_read ? 'bg-slate-100 text-slate-500' : 'bg-violet-100 text-violet-600' }}">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="truncate text-sm font-semibold text-slate-900">{{ $name }}</div>
                                            <div class="text-[11px] text-slate-400">
                                                {{ $contactMessage->created_at->diffForHumans() }}</div>
                                        </div>
                                        <div class="truncate text-xs font-semibold text-slate-600">
                                            {{ $contactMessage->subject ?: 'No subject' }}</div>
                                        <div class="truncate text-xs text-slate-500">
                                            {{ \Illuminate\Support\Str::limit($contactMessage->message, 70) }}</div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl bg-slate-50 px-3 py-4 text-center text-xs text-slate-500">
                                No contact messages yet.
                            </div>
                        @endforelse
                    </div>
                </article>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') {
                return;
            }

            const trendData = @json($trendChartData);
            const studentProfileData = @json($studentProfileChartData);
            const compositionData = @json($compositionChartData);
            const classLoadData = @json($classLoadChartData);
            const subjectHealthData = @json($subjectHealthChartData);
            const periodData = @json($periodChartData);
            const attendanceData = @json($attendanceChartData);

            const createGradient = (ctx, colorStart, colorEnd) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 320);
                gradient.addColorStop(0, colorStart);
                gradient.addColorStop(1, colorEnd);
                return gradient;
            };

            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const chartDelay = (ctx, baseDelay = 0, step = 75) => {
                if (ctx.type !== 'data' || ctx.mode !== 'default') {
                    return 0;
                }
                return baseDelay + (ctx.dataIndex * step) + (ctx.datasetIndex * 90);
            };

            const buildStaggeredAnimation = (baseDelay = 0, step = 75, duration = 1050) => {
                if (reduceMotion) {
                    return false;
                }
                return {
                    duration,
                    easing: 'easeOutCubic',
                    delay: (ctx) => chartDelay(ctx, baseDelay, step),
                };
            };

            const buildAxisAnimations = (baseDelay = 0) => {
                if (reduceMotion) {
                    return {};
                }
                return {
                    x: {
                        duration: 760,
                        easing: 'easeOutQuart',
                        delay: (ctx) => chartDelay(ctx, baseDelay, 55),
                    },
                    y: {
                        from: 0,
                        duration: 920,
                        easing: 'easeOutQuart',
                        delay: (ctx) => chartDelay(ctx, baseDelay, 70),
                    },
                };
            };

            const trendCanvas = document.getElementById('enrollmentTrendChart');
            if (trendCanvas) {
                const trendCtx = trendCanvas.getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendData.labels,
                        datasets: [{
                            label: 'Students',
                            data: trendData.students,
                            borderWidth: 3,
                            borderColor: '#4f46e5',
                            backgroundColor: createGradient(trendCtx, 'rgba(79,70,229,0.35)', 'rgba(79,70,229,0.02)'),
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3,
                            pointBackgroundColor: '#4f46e5',
                        }, {
                            label: 'Teachers',
                            data: trendData.teachers,
                            borderWidth: 3,
                            borderColor: '#0ea5e9',
                            backgroundColor: createGradient(trendCtx, 'rgba(14,165,233,0.28)', 'rgba(14,165,233,0.02)'),
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3,
                            pointBackgroundColor: '#0ea5e9',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: buildStaggeredAnimation(120, 60, 1100),
                        animations: buildAxisAnimations(120),
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(148,163,184,0.2)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            const studentsSnapshotCanvas = document.getElementById('studentsSnapshotChart');
            if (studentsSnapshotCanvas) {
                new Chart(studentsSnapshotCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: studentProfileData.labels,
                        datasets: [{
                            data: studentProfileData.values,
                            backgroundColor: ['#38bdf8', '#facc15'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: buildStaggeredAnimation(150, 70, 980),
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                }
                            }
                        }
                    }
                });
            }

            const attendanceOverviewCanvas = document.getElementById('attendanceOverviewChart');
            if (attendanceOverviewCanvas) {
                new Chart(attendanceOverviewCanvas, {
                    type: 'bar',
                    data: {
                        labels: attendanceData.labels.length ? attendanceData.labels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                        datasets: [{
                            label: 'Total Present',
                            data: attendanceData.present.length ? attendanceData.present : [0, 0, 0, 0, 0],
                            backgroundColor: '#facc15',
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 28,
                        }, {
                            label: 'Total Absent',
                            data: attendanceData.absent.length ? attendanceData.absent : [0, 0, 0, 0, 0],
                            backgroundColor: '#7dd3fc',
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 28,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: buildStaggeredAnimation(170, 65, 980),
                        animations: buildAxisAnimations(170),
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(148,163,184,0.2)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            const compositionCanvas = document.getElementById('compositionChart');
            if (compositionCanvas) {
                new Chart(compositionCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: compositionData.labels,
                        datasets: [{
                            data: compositionData.values,
                            backgroundColor: ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: buildStaggeredAnimation(180, 80, 1020),
                        cutout: '62%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                }
                            }
                        }
                    }
                });
            }

            const periodCanvas = document.getElementById('periodChart');
            if (periodCanvas) {
                new Chart(periodCanvas, {
                    type: 'polarArea',
                    data: {
                        labels: periodData.labels,
                        datasets: [{
                            data: periodData.values,
                            backgroundColor: [
                                'rgba(99,102,241,0.8)',
                                'rgba(14,165,233,0.8)',
                                'rgba(16,185,129,0.8)',
                                'rgba(245,158,11,0.8)',
                                'rgba(148,163,184,0.8)'
                            ],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: buildStaggeredAnimation(220, 70, 1000),
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                }
                            }
                        },
                        scales: {
                            r: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            const classLoadCanvas = document.getElementById('classLoadChart');
            if (classLoadCanvas) {
                new Chart(classLoadCanvas, {
                    type: 'bar',
                    data: {
                        labels: classLoadData.labels.length ? classLoadData.labels : ['No Data'],
                        datasets: [{
                            label: 'Students',
                            data: classLoadData.values.length ? classLoadData.values : [0],
                            backgroundColor: '#6366f1',
                            borderRadius: 10,
                            borderSkipped: false,
                            maxBarThickness: 34,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: buildStaggeredAnimation(250, 85, 940),
                        animations: buildAxisAnimations(250),
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(148,163,184,0.2)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            const subjectHealthCanvas = document.getElementById('subjectHealthChart');
            if (subjectHealthCanvas) {
                new Chart(subjectHealthCanvas, {
                    type: 'bar',
                    data: {
                        labels: subjectHealthData.labels,
                        datasets: [{
                            label: 'Subjects',
                            data: subjectHealthData.values,
                            backgroundColor: ['#10b981', '#f43f5e', '#2563eb', '#94a3b8'],
                            borderRadius: 10,
                            borderSkipped: false,
                            maxBarThickness: 36,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: buildStaggeredAnimation(290, 85, 940),
                        animations: buildAxisAnimations(290),
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(148,163,184,0.2)'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
