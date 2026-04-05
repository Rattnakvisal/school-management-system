@extends('layout.teacher.navbar')

@section('page')
    @php
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst($todayKey);
        $chartData = $chartData ?? [
            'weekly' => ['labels' => [], 'classes' => [], 'subjects' => []],
            'todayMix' => ['labels' => [], 'values' => []],
        ];
        $dashboardData = [
            'todayKey' => $todayKey,
            'dayLabels' => $dayLabels ?? [],
            'chartData' => $chartData,
        ];
    @endphp

    <div class="dashboard-stage space-y-6">
        <section class="dash-reveal admin-page-header teacher-page-header" style="--d: 1;">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Teacher Dashboard</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        Dynamic schedule overview for <span id="dashboard-live-day">{{ $todayLabel }}</span>
                        (<span id="dashboard-live-date">{{ now()->format('M d, Y') }}</span>).
                    </p>
                </div>

                <div class="teacher-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">Classes: {{ number_format($stats['classes']) }}</span>
                    <span class="admin-page-stat admin-page-stat--sky">Students:
                        {{ number_format($stats['students']) }}</span>
                    <span class="admin-page-stat admin-page-stat--indigo">Subjects:
                        {{ number_format($stats['subjects']) }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Today Slots:
                        {{ number_format($stats['todaySchedules']) }}</span>
                </div>
            </div>

            <div class="mt-5">
                <a id="dashboard-open-today-link" href="{{ route('teacher.schedule.index', ['day' => $todayKey]) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                    Open Today Schedule
                </a>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-12">
            <section class="dash-reveal dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7"
                style="--d: 2;">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-black text-slate-900">Today's Schedule</h2>
                    <div class="flex items-center gap-2">
                        <span id="dashboard-live-time"
                            class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            --:--:--
                        </span>
                        <span
                            class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $todayLabel }}
                        </span>
                    </div>
                </div>
                <p id="dashboard-live-hint" class="mb-4 text-xs font-semibold text-slate-500">
                    Checking live schedule...
                </p>

                <div class="space-y-3">
                    @forelse ($todayTimeline as $item)
                        <div class="js-dash-slot rounded-2xl border border-slate-200 bg-slate-50/70 px-4 py-3"
                            data-start="{{ $item['start_24'] ?? '' }}" data-end="{{ $item['end_24'] ?? '' }}">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <div class="text-sm font-bold text-slate-900">{{ $item['title'] }}</div>
                                    <div class="text-xs text-slate-500">{{ $item['subtitle'] }}</div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $item['type'] === 'subject' ? 'border border-indigo-200 bg-indigo-50 text-indigo-700' : 'border border-sky-200 bg-sky-50 text-sky-700' }}">
                                        {{ ucfirst($item['type']) }}
                                    </span>
                                    <span
                                        class="js-dash-slot-status rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                        Scheduled
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-700">
                                <span class="rounded-lg border border-slate-200 bg-white px-2 py-1">{{ $item['period'] }}</span>
                                <span class="rounded-lg border border-slate-200 bg-white px-2 py-1">{{ $item['start'] }} ->
                                    {{ $item['end'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                            No schedule configured for today.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="dash-reveal dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5"
                style="--d: 3;">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-black text-slate-900">Weekly Schedule</h2>
                    <a href="{{ route('teacher.schedule.index') }}"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">
                        Manage
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="h-52 sm:h-56">
                        <canvas id="teacherWeeklyChart" class="h-full w-full"></canvas>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Today Slot Mix</div>
                        <div class="h-44 sm:h-48">
                            <canvas id="teacherTodayMixChart" class="h-full w-full"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script id="teacher-dashboard-data" type="application/json">@json($dashboardData)</script>
    @vite(['resources/js/Teacher/dashboard.js'])
@endsection
