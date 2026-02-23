@extends('layout.teacher.navbar')

@section('page')
    @php
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst($todayKey);
    @endphp

    <div class="dashboard-stage space-y-6">
        <section class="dash-reveal admin-page-header" style="--d: 1;">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Teacher Dashboard</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        Dynamic schedule overview for {{ $todayLabel }} ({{ now()->format('M d, Y') }}).
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">Classes: {{ number_format($stats['classes']) }}</span>
                    <span class="admin-page-stat admin-page-stat--sky">Students: {{ number_format($stats['students']) }}</span>
                    <span class="admin-page-stat admin-page-stat--indigo">Subjects: {{ number_format($stats['subjects']) }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Today Slots: {{ number_format($stats['todaySchedules']) }}</span>
                </div>
            </div>

            <div class="mt-5">
                <a href="{{ route('teacher.time-studies.index', ['day' => $todayKey]) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                    Open Today Schedule
                </a>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-12">
            <section class="dash-reveal dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7"
                style="--d: 2;">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900">Today's Schedule</h2>
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
                        {{ $todayLabel }}
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse ($todayTimeline as $item)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 px-4 py-3">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <div class="text-sm font-bold text-slate-900">{{ $item['title'] }}</div>
                                    <div class="text-xs text-slate-500">{{ $item['subtitle'] }}</div>
                                </div>
                                <span
                                    class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $item['type'] === 'subject' ? 'border border-indigo-200 bg-indigo-50 text-indigo-700' : 'border border-sky-200 bg-sky-50 text-sky-700' }}">
                                    {{ ucfirst($item['type']) }}
                                </span>
                            </div>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-700">
                                <span class="rounded-lg border border-slate-200 bg-white px-2 py-1">{{ $item['period'] }}</span>
                                <span class="rounded-lg border border-slate-200 bg-white px-2 py-1">{{ $item['start'] }} -> {{ $item['end'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                            No schedule configured for today.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="dash-reveal dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5"
                style="--d: 3;">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900">Weekly Schedule</h2>
                    <a href="{{ route('teacher.time-studies.index') }}"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">
                        Manage
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach ($weeklySummary as $dayItem)
                        @php
                            $barWidth = (int) round(($dayItem['total'] / $maxWeeklyTotal) * 100);
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-xs font-semibold">
                                <span class="text-slate-700">{{ $dayItem['label'] }}</span>
                                <span class="text-slate-500">{{ $dayItem['total'] }} slots</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $barWidth }}%"></div>
                            </div>
                            <div class="mt-1 text-[11px] text-slate-500">
                                Class: {{ $dayItem['class_count'] }} | Subject: {{ $dayItem['subject_count'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
