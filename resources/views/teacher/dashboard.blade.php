@extends('layout.teacher.navbar')

@section('page')
    @php
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst($todayKey);
        $chartData = $chartData ?? [
            'weekly' => ['labels' => [], 'classes' => [], 'subjects' => []],
            'todayMix' => ['labels' => [], 'values' => []],
            'todayPeriods' => ['labels' => [], 'values' => []],
        ];
        $dashboardData = [
            'todayKey' => $todayKey,
            'dayLabels' => $dayLabels ?? [],
            'chartData' => $chartData,
        ];
    @endphp

    <div class="dashboard-stage space-y-6">
        <section
            class="dash-reveal teacher-page-header teacher-dashboard-hero relative overflow-hidden rounded-3xl p-6 shadow-lg"
            style="--d: 1;">
            <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-16 right-16 h-48 w-48 rounded-full bg-cyan-200/18 blur-3xl">
            </div>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-center">
                <div class="space-y-6">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white/90 shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-emerald-300 shadow-[0_0_18px_rgba(110,231,183,0.9)]"></span>
                        Live teaching overview
                    </div>

                    <div class="space-y-3">
                        <h1 class="admin-page-title text-3xl font-black tracking-tight text-white sm:text-4xl lg:text-5xl">
                            Teacher Dashboard
                        </h1>
                        <p class="admin-page-subtitle max-w-2xl text-sm leading-6 text-white/88">
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

                    <div class="flex flex-wrap items-center gap-3">
                        <a id="dashboard-open-today-link" href="{{ route('teacher.schedule.index', ['day' => $todayKey]) }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white px-4 py-2.5 text-sm font-semibold text-indigo-700 shadow-lg shadow-indigo-950/10 transition hover:-translate-y-0.5 hover:bg-indigo-50">
                            Open Today Schedule
                        </a>
                    </div>
                </div>

                <div class="hidden lg:block">
                    <div class="dashboard-hero-art teacher-hero-art">
                        <div class="teacher-hero-art__panel">
                            <div class="teacher-hero-art__frame">
                                <svg viewBox="0 0 340 260" class="teacher-hero-art__svg h-auto w-full" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="teacherHeroSky" x1="0%" y1="0%" x2="100%"
                                            y2="100%">
                                            <stop offset="0%" stop-color="rgba(255,255,255,0.36)" />
                                            <stop offset="100%" stop-color="rgba(255,255,255,0.08)" />
                                        </linearGradient>
                                        <linearGradient id="teacherHeroBody" x1="0%" y1="0%" x2="100%"
                                            y2="100%">
                                            <stop offset="0%" stop-color="#f8fafc" />
                                            <stop offset="100%" stop-color="#dbeafe" />
                                        </linearGradient>
                                        <linearGradient id="teacherHeroAccent" x1="0%" y1="0%" x2="100%"
                                            y2="100%">
                                            <stop offset="0%" stop-color="#34d399" />
                                            <stop offset="100%" stop-color="#10b981" />
                                        </linearGradient>
                                        <filter id="teacherHeroShadow" x="-60%" y="-60%" width="220%" height="220%">
                                            <feGaussianBlur in="SourceAlpha" stdDeviation="7" />
                                            <feOffset dx="0" dy="10" result="offset" />
                                            <feFlood flood-color="rgba(15,23,42,0.28)" />
                                            <feComposite in2="offset" operator="in" />
                                            <feMerge>
                                                <feMergeNode />
                                                <feMergeNode in="SourceGraphic" />
                                            </feMerge>
                                        </filter>
                                    </defs>

                                    <ellipse cx="172" cy="228" rx="112" ry="14"
                                        fill="rgba(15,23,42,0.18)" />
                                    <circle cx="74" cy="66" r="24" fill="rgba(255,255,255,0.18)" />
                                    <circle cx="286" cy="54" r="34" fill="rgba(255,255,255,0.16)" />
                                    <circle cx="258" cy="184" r="18" fill="rgba(52,211,153,0.18)" />
                                    <circle cx="86" cy="192" r="14" fill="rgba(96,165,250,0.16)" />

                                    <g filter="url(#teacherHeroShadow)">
                                        <path
                                            d="M106 88c0-18 14-32 32-32h64c18 0 32 14 32 32v24c0 18-14 32-32 32h-64c-18 0-32-14-32-32V88Z"
                                            fill="url(#teacherHeroSky)" stroke="rgba(255,255,255,0.38)" />
                                        <path d="M136 82c0-11 9-20 20-20h28c11 0 20 9 20 20v7h-68v-7Z"
                                            fill="rgba(255,255,255,0.2)" />
                                        <circle cx="154" cy="96" r="5.5" fill="#fff" />
                                        <circle cx="186" cy="96" r="5.5" fill="#fff" />
                                        <path d="M159 109c6 4 16 4 22 0" stroke="rgba(255,255,255,0.9)" stroke-width="2.4"
                                            stroke-linecap="round" />
                                        <circle cx="138" cy="104" r="3" fill="#34d399" />
                                        <circle cx="202" cy="104" r="3" fill="#60a5fa" />
                                    </g>

                                    <g class="dashboard-person" filter="url(#teacherHeroShadow)">
                                        <ellipse cx="170" cy="210" rx="44" ry="10"
                                            fill="rgba(15,23,42,0.12)" />
                                        <rect x="122" y="120" width="96" height="70" rx="24"
                                            fill="url(#teacherHeroBody)" stroke="rgba(255,255,255,0.58)" />
                                        <rect x="139" y="136" width="62" height="34" rx="16"
                                            fill="rgba(255,255,255,0.86)" />
                                        <rect x="146" y="142" width="48" height="6" rx="3"
                                            fill="rgba(15,23,42,0.08)" />
                                        <rect x="146" y="153" width="30" height="6" rx="3"
                                            fill="rgba(15,23,42,0.08)" />
                                        <circle cx="152" cy="153" r="10" fill="url(#teacherHeroAccent)" />
                                        <circle cx="190" cy="153" r="10" fill="rgba(59,130,246,0.16)" />

                                        <path d="M122 138c-18 7-26 20-30 33" stroke="rgba(255,255,255,0.82)"
                                            stroke-width="10" stroke-linecap="round" />
                                        <path class="person-arm person-arm--right" d="M218 139c18 8 27 18 34 33"
                                            stroke="rgba(255,255,255,0.94)" stroke-width="10" stroke-linecap="round" />
                                        <path d="M92 186c-6 14-7 28-4 40" stroke="rgba(255,255,255,0.84)"
                                            stroke-width="10" stroke-linecap="round" />
                                        <path d="M247 186c8 14 10 29 6 40" stroke="rgba(255,255,255,0.84)"
                                            stroke-width="10" stroke-linecap="round" />

                                        <rect x="127" y="186" width="86" height="12" rx="6"
                                            fill="rgba(255,255,255,0.74)" />
                                        <rect x="122" y="194" width="96" height="18" rx="9"
                                            fill="rgba(255,255,255,0.9)" />
                                        <path d="M137 203h22" stroke="#2563eb" stroke-width="4" stroke-linecap="round" />
                                        <path d="M167 203h34" stroke="#10b981" stroke-width="4" stroke-linecap="round" />
                                        <circle cx="170" cy="76" r="16" fill="rgba(255,255,255,0.92)"
                                            stroke="rgba(203,213,225,0.8)" />
                                        <circle cx="170" cy="76" r="6" fill="#6366f1" />
                                        <path d="M170 58v-14" stroke="rgba(255,255,255,0.88)" stroke-width="4"
                                            stroke-linecap="round" />
                                        <circle cx="170" cy="38" r="4.5" fill="#34d399" />
                                    </g>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-12">
            <section
                class="dash-reveal dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7"
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

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-2">
                    @forelse ($todayTimeline as $item)
                        @php
                            $hasSubject = !empty($item['subject_names'] ?? []);
                            $slotToneClass = $hasSubject
                                ? 'border-emerald-200 bg-emerald-50/70'
                                : 'border-slate-200 bg-slate-50/70';
                            $labelToneClass = $hasSubject
                                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                : 'border-indigo-200 bg-indigo-50 text-indigo-700';
                            $dayCardClass = $hasSubject
                                ? 'border-emerald-200 bg-emerald-50/70'
                                : 'border-sky-200 bg-sky-50/70';
                            $dayTextClass = $hasSubject ? 'text-emerald-600' : 'text-sky-600';
                            $dayValueClass = $hasSubject ? 'text-emerald-900' : 'text-sky-900';
                            $dayHintClass = $hasSubject ? 'text-emerald-700' : 'text-sky-700';
                            $subjectCardClass = $hasSubject
                                ? 'border-emerald-200 bg-emerald-50/80'
                                : 'border-slate-200 bg-white';
                            $subjectTextClass = $hasSubject ? 'text-emerald-600' : 'text-slate-500';
                            $subjectValueClass = $hasSubject ? 'text-emerald-900' : 'text-slate-800';
                            $subjectHintClass = $hasSubject ? 'text-emerald-700' : 'text-slate-500';
                            $timeCardClass = $hasSubject
                                ? 'border-emerald-200 bg-emerald-50/80'
                                : 'border-emerald-200 bg-emerald-50/80';
                            $timeTextClass = 'text-emerald-600';
                            $timeValueClass = 'text-emerald-900';
                            $timeHintClass = 'text-emerald-700';
                        @endphp
                        <div class="js-dash-slot h-full rounded-2xl border px-4 py-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $slotToneClass }}"
                            data-start="{{ $item['start_24'] ?? '' }}" data-end="{{ $item['end_24'] ?? '' }}">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-sm font-black text-slate-900">{{ $item['class_name'] }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">Teaching day and subject time</div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $labelToneClass }}">
                                        {{ $item['day_label'] }}
                                    </span>
                                    <span
                                        class="js-dash-slot-status rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                        Scheduled
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-xl border px-3 py-2 {{ $dayCardClass }}">
                                    <div class="text-[11px] font-bold uppercase tracking-wide {{ $dayTextClass }}">Day
                                    </div>
                                    <div class="mt-1 text-xs font-semibold {{ $dayValueClass }}">{{ $item['day_label'] }}
                                    </div>
                                    <div class="mt-1 text-xs {{ $dayHintClass }}">Teaching day</div>
                                </div>
                                <div class="rounded-xl px-3 py-2 {{ $subjectCardClass }}">
                                    <div class="text-[11px] font-bold uppercase tracking-wide {{ $subjectTextClass }}">
                                        Subject</div>
                                    <div class="mt-1 truncate text-xs font-semibold {{ $subjectValueClass }}">
                                        {{ $item['subject_label'] }}</div>
                                    <div class="mt-1 text-xs {{ $subjectHintClass }}">{{ $item['class_name'] }}</div>
                                </div>
                                <div class="rounded-xl border px-3 py-2 {{ $timeCardClass }}">
                                    <div class="text-[11px] font-bold uppercase tracking-wide {{ $timeTextClass }}">Time
                                    </div>
                                    <div class="mt-1 text-xs font-semibold {{ $timeValueClass }}">{{ $item['period'] }}
                                    </div>
                                    <div class="mt-1 text-xs {{ $timeHintClass }}">{{ $item['start'] }} ->
                                        {{ $item['end'] }}</div>
                                </div>
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

            <section
                class="dash-reveal dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5"
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

                    <div class="border-t border-slate-100 pt-4">
                        <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Period Breakdown
                        </div>
                        <div class="h-52 sm:h-56">
                            <canvas id="teacherPeriodChart" class="h-full w-full"></canvas>
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
