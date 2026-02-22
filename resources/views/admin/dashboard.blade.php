@extends('layout.admin.navbar.navbar')

@section('page')
    @php
        $majorRate = ($studentsTotal ?? 0) > 0 ? round((($studentsWithMajor ?? 0) / $studentsTotal) * 100, 1) : 0;
        $studyTimeRate = ($studentsTotal ?? 0) > 0 ? round((($studentsWithStudyTime ?? 0) / $studentsTotal) * 100, 1) : 0;
    @endphp

    <div class="dashboard-stage space-y-6">
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
            <div class="space-y-6 xl:col-span-8">
                <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                    style="--d: 7;">
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
                        style="--d: 8;">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">System Composition</h2>
                            <span class="text-xs text-slate-500">Population split</span>
                        </div>
                        <div class="h-[280px]">
                            <canvas id="compositionChart"></canvas>
                        </div>
                    </article>

                    <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 9;">
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
                        style="--d: 10;">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">Top Class Load</h2>
                            <span class="text-xs text-slate-500">Students per class</span>
                        </div>
                        <div class="h-[300px]">
                            <canvas id="classLoadChart"></canvas>
                        </div>
                    </article>

                    <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 11;">
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
                    style="--d: 12;">
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
                    style="--d: 13;">
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

            const trendData = @json($chartData['trend'] ?? ['labels' => [], 'students' => [], 'teachers' => []]);
            const compositionData = @json($chartData['composition'] ?? ['labels' => [], 'values' => []]);
            const classLoadData = @json($chartData['classLoad'] ?? ['labels' => [], 'values' => []]);
            const subjectHealthData = @json($chartData['subjectHealth'] ?? ['labels' => [], 'values' => []]);
            const periodData = @json($chartData['periods'] ?? ['labels' => [], 'values' => []]);

            const createGradient = (ctx, colorStart, colorEnd) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 320);
                gradient.addColorStop(0, colorStart);
                gradient.addColorStop(1, colorEnd);
                return gradient;
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