@extends('layout.admin.navbar.navbar')

@section('page')
    @php
        $majorRate = ($studentsTotal ?? 0) > 0 ? round((($studentsWithMajor ?? 0) / $studentsTotal) * 100, 1) : 0;
        $studyTimeRate =
            ($studentsTotal ?? 0) > 0 ? round((($studentsWithStudyTime ?? 0) / $studentsTotal) * 100, 1) : 0;
        $dashboardNow = $dashboardNow ?? now();
        $dashboardAgenda = collect($dashboardAgenda ?? []);
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
        $attendanceChartData = $chartData['attendance'] ?? [
            'labels' => [],
            'present' => [],
            'absent' => [],
            'hasData' => false,
        ];
    @endphp

    <div class="dashboard-stage space-y-6">
        <section class="dash-reveal admin-page-header relative overflow-hidden rounded-3xl p-6 shadow-lg" style="--d: 1;">
            <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-white/15 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-14 right-20 h-44 w-44 rounded-full bg-cyan-200/25 blur-2xl">
            </div>

            <div
                class="grid grid-cols-1 relative items-center gap-6 md:grid-cols-[minmax(0,1fr)_220px] lg:grid-cols-[minmax(0,1fr)_280px]">
                <div>
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-sm font-semibold text-indigo-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3 3 8v8l9 5 9-5V8l-9-5Z" />
                            <path d="M12 12V3" />
                            <path d="m3 8 9 5 9-5" />
                        </svg>
                        Dashboard
                    </div>
                    <h1 class="mt-1 admin-page-title font-black tracking-tight">{{ $greeting }} {{ $firstName }}</h1>
                    <p class="mt-2 max-w-2xl text-sm admin-page-subtitle">
                        You have {{ number_format($focusCount) }} item{{ $focusCount === 1 ? '' : 's' }} to review today.
                        Unread messages: {{ number_format($messagesUnread ?? 0) }}, students without study time:
                        {{ number_format($studentsNeedStudyTime) }}.
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-2 admin-page-header__actions">
                        <a href="{{ route('admin.contacts.index') }}"
                            class="w-full rounded-xl border border-white/35 bg-white px-4 py-2 text-center text-xs font-semibold text-indigo-700 transition hover:bg-indigo-50 sm:w-auto">
                            Review Inbox
                        </a>
                        <a href="{{ route('admin.student-study.index') }}"
                            class="w-full rounded-xl border border-white/35 bg-white/10 px-4 py-2 text-center text-xs font-semibold text-white transition hover:bg-white/20 sm:w-auto">
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

                <div class="hidden md:flex md:items-center md:justify-end">
                    <div class="dashboard-hero-art">
                        <svg viewBox="0 0 340 230" class="h-32 w-auto dashboard-logo dashboard-logo--enhanced lg:h-44"
                            fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <defs>
                                <radialGradient id="adminHdrBlobA" cx="50%" cy="50%" r="50%">
                                    <stop offset="0%" stop-color="rgba(125,211,252,0.45)" />
                                    <stop offset="100%" stop-color="rgba(125,211,252,0)" />
                                </radialGradient>
                                <radialGradient id="adminHdrBlobB" cx="50%" cy="50%" r="50%">
                                    <stop offset="0%" stop-color="rgba(199,210,254,0.4)" />
                                    <stop offset="100%" stop-color="rgba(199,210,254,0)" />
                                </radialGradient>
                                <linearGradient id="adminHdrPanelA" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="rgba(255,255,255,0.35)" />
                                    <stop offset="100%" stop-color="rgba(255,255,255,0.12)" />
                                </linearGradient>
                                <linearGradient id="adminHdrPanelB" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="rgba(255,255,255,0.3)" />
                                    <stop offset="100%" stop-color="rgba(255,255,255,0.08)" />
                                </linearGradient>
                                <linearGradient id="adminHdrCap" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#111827" />
                                    <stop offset="100%" stop-color="#0f172a" />
                                </linearGradient>
                                <filter id="adminHdrShadow" x="-60%" y="-60%" width="220%" height="220%">
                                    <feGaussianBlur in="SourceAlpha" stdDeviation="7" />
                                    <feOffset dx="0" dy="10" result="offset" />
                                    <feFlood flood-color="rgba(15,23,42,0.32)" />
                                    <feComposite in2="offset" operator="in" />
                                    <feMerge>
                                        <feMergeNode />
                                        <feMergeNode in="SourceGraphic" />
                                    </feMerge>
                                </filter>
                            </defs>

                            <ellipse cx="172" cy="210" rx="120" ry="13" fill="rgba(15,23,42,0.2)" />

                            <circle class="dashboard-logo__orb dashboard-logo__orb--a" cx="85" cy="88" r="56"
                                fill="url(#adminHdrBlobA)" />
                            <circle class="dashboard-logo__orb dashboard-logo__orb--b" cx="250" cy="72" r="72"
                                fill="url(#adminHdrBlobB)" />

                            <g class="dashboard-logo__panel dashboard-logo__panel--left" filter="url(#adminHdrShadow)">
                                <rect x="44" y="70" width="132" height="86" rx="22" fill="url(#adminHdrPanelA)"
                                    stroke="rgba(255,255,255,0.35)" />
                                <path d="M63 125c20-24 31 10 46-12 15-20 24-26 35-8 13 20 25 6 38-12"
                                    stroke="rgba(255,255,255,0.92)" stroke-width="3.4" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <circle cx="76" cy="112" r="3.2" fill="#34d399" />
                                <circle cx="120" cy="118" r="3.2" fill="#60a5fa" />
                                <circle cx="157" cy="102" r="3.2" fill="#a78bfa" />
                            </g>

                            <g class="dashboard-logo__panel dashboard-logo__panel--right" filter="url(#adminHdrShadow)">
                                <rect x="174" y="50" width="122" height="102" rx="22" fill="url(#adminHdrPanelB)"
                                    stroke="rgba(255,255,255,0.3)" />
                                <rect class="dashboard-logo__bar dashboard-logo__bar--a" x="194" y="84" width="8"
                                    height="44" rx="4" fill="rgba(255,255,255,0.86)" />
                                <rect class="dashboard-logo__bar dashboard-logo__bar--b" x="220" y="94" width="8"
                                    height="34" rx="4" fill="rgba(255,255,255,0.76)" />
                                <rect class="dashboard-logo__bar dashboard-logo__bar--c" x="246" y="76" width="8"
                                    height="52" rx="4" fill="rgba(255,255,255,0.9)" />
                                <rect class="dashboard-logo__bar dashboard-logo__bar--d" x="272" y="98" width="8"
                                    height="30" rx="4" fill="rgba(255,255,255,0.72)" />
                                <circle cx="246" cy="66" r="10" stroke="rgba(255,255,255,0.62)" stroke-width="3" />
                                <path d="M252 61 L257 66 L252 71" stroke="rgba(255,255,255,0.82)" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </g>

                            <path d="M170 144 L199 132" stroke="rgba(255,255,255,0.32)" stroke-width="1.2"
                                stroke-dasharray="4 4" />

                            <g class="dashboard-logo__cap" filter="url(#adminHdrShadow)">
                                <path d="M170 90 L100 116 L170 142 L240 116 Z" fill="url(#adminHdrCap)" />
                                <path d="M170 90 L100 116 L109 118 L170 96 L231 118 L240 116 Z"
                                    fill="rgba(71,85,105,0.6)" />
                                <path d="M126 124v21c0 14 20 25 44 25s44-11 44-25v-21l-44 17-44-17Z"
                                    fill="rgba(241,245,249,0.95)" stroke="rgba(203,213,225,0.56)" />
                                <path d="M240 116v30" stroke="rgba(255,255,255,0.86)" stroke-width="3"
                                    stroke-linecap="round" />
                                <circle class="dashboard-logo__tassel" cx="240" cy="148" r="7" fill="#22c55e" />
                                <circle cx="240" cy="148" r="4" fill="#4ade80" />
                            </g>

                            <g class="dashboard-logo__sparkles">
                                <path d="M74 56 L76 61 L81 63 L76 65 L74 70 L72 65 L67 63 L72 61 Z"
                                    fill="rgba(255,255,255,0.72)" />
                                <path d="M258 34 L259 37 L262 38 L259 39 L258 42 L257 39 L254 38 L257 37 Z"
                                    fill="rgba(255,255,255,0.64)" />
                                <path d="M47 164 L48 166 L50 167 L48 168 L47 170 L46 168 L44 167 L46 166 Z"
                                    fill="rgba(255,255,255,0.56)" />
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5">
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
            <article class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--d: 7;">
                <div class="dashboard-card-header mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-slate-900">Students</h2>
                    <span class="dashboard-card-meta text-xs text-slate-500">Active vs Inactive</span>
                </div>
                <div class="dashboard-chart-box h-56 sm:h-64 md:h-72 lg:h-[280px]">
                    <canvas id="studentsSnapshotChart" class="w-full h-full"></canvas>
                </div>
                <div class="dashboard-summary-grid mt-3 grid grid-cols-2 gap-3 text-center">
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

            <article class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--d: 8;">
                <div class="dashboard-card-header mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-slate-900">Attendance</h2>
                    <span class="dashboard-card-meta text-xs text-slate-500">Last 5 Days</span>
                </div>
                <div class="dashboard-chart-box dashboard-chart-box--tall h-64 sm:h-72 md:h-80 lg:h-[320px]">
                    <canvas id="attendanceOverviewChart" class="w-full h-full"></canvas>
                </div>
                @if (!($chartData['attendance']['hasData'] ?? false))
                    <p class="mt-3 text-xs text-slate-500">
                        No attendance records found yet. Chart will update automatically when attendance data is available.
                    </p>
                @endif
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-12">
            <div class="space-y-6 min-w-0 xl:col-span-8">
                <article class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                    style="--d: 9;">
                    <div class="dashboard-card-header mb-4 flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-base font-bold text-slate-900">Enrollment Trend (Last 6 Months)</h2>
                        <span class="dashboard-card-meta text-xs text-slate-500">Students vs Teachers</span>
                    </div>
                    <div class="dashboard-chart-box dashboard-chart-box--tall h-64 sm:h-72 md:h-80 lg:h-[320px]">
                        <canvas id="enrollmentTrendChart" class="w-full h-full"></canvas>
                    </div>
                </article>

                <div class="grid gap-6 lg:grid-cols-2">
                    <article class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 10;">
                        <div class="dashboard-card-header mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">System Composition</h2>
                            <span class="dashboard-card-meta text-xs text-slate-500">Population split</span>
                        </div>
                        <div class="dashboard-chart-box h-56 sm:h-64 md:h-72 lg:h-[280px]">
                            <canvas id="compositionChart" class="w-full h-full"></canvas>
                        </div>
                    </article>

                    <article class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 11;">
                        <div class="dashboard-card-header mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">Study Period Distribution</h2>
                            <span class="dashboard-card-meta text-xs text-slate-500">Morning, night, and more</span>
                        </div>
                        <div class="dashboard-chart-box h-56 sm:h-64 md:h-72 lg:h-[280px]">
                            <canvas id="periodChart" class="w-full h-full"></canvas>
                        </div>
                    </article>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <article class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 12;">
                        <div class="dashboard-card-header mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">Top Class Load</h2>
                            <span class="dashboard-card-meta text-xs text-slate-500">Students per class</span>
                        </div>
                        <div class="dashboard-chart-box dashboard-chart-box--medium h-56 sm:h-72 md:h-[300px] lg:h-[300px]">
                            <canvas id="classLoadChart" class="w-full h-full"></canvas>
                        </div>
                    </article>

                    <article class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 13;">
                        <div class="dashboard-card-header mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">Subject Health</h2>
                            <span class="dashboard-card-meta text-xs text-slate-500">Status and assignment</span>
                        </div>
                        <div class="dashboard-chart-box dashboard-chart-box--medium h-56 sm:h-72 md:h-[300px] lg:h-[300px]">
                            <canvas id="subjectHealthChart" class="w-full h-full"></canvas>
                        </div>
                    </article>
                </div>
            </div>

            <div class="space-y-6 min-w-0 xl:col-span-4">
                <article class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                    style="--d: 14;">
                    <h2 class="text-base font-bold text-slate-900">Study Profile</h2>
                    <p class="mt-1 text-xs text-slate-500">How well students are mapped to major subjects and study times.
                    </p>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">With Major Subject
                            </div>
                            <div class="mt-2 text-2xl font-black text-slate-900">
                                {{ number_format($studentsWithMajor ?? 0) }}
                            </div>
                            <div class="mt-1 text-xs text-slate-500">{{ $majorRate }}% of students</div>
                            <div class="mt-3 h-2 rounded-full bg-slate-200">
                                <div class="h-2 rounded-full bg-indigo-500"
                                    style="width: {{ min(100, max(0, $majorRate)) }}%"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">With Study Time</div>
                            <div class="mt-2 text-2xl font-black text-slate-900">
                                {{ number_format($studentsWithStudyTime ?? 0) }}
                            </div>
                            <div class="mt-1 text-xs text-slate-500">{{ $studyTimeRate }}% of students</div>
                            <div class="mt-3 h-2 rounded-full bg-slate-200">
                                <div class="h-2 rounded-full bg-cyan-500"
                                    style="width: {{ min(100, max(0, $studyTimeRate)) }}%"></div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                    style="--d: 15;">
                    <div class="flex flex-wrap items-center justify-between gap-2">
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
                                            <div class="truncate text-sm font-semibold text-slate-900">{{ $name }}
                                            </div>
                                            <div class="text-[11px] text-slate-400">
                                                {{ $contactMessage->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div class="truncate text-xs font-semibold text-slate-600">
                                            {{ $contactMessage->subject ?: 'No subject' }}
                                        </div>
                                        <div class="truncate text-xs text-slate-500">
                                            {{ \Illuminate\Support\Str::limit($contactMessage->message, 70) }}
                                        </div>
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

                <article
                    class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl border border-indigo-100 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 shadow-sm ring-1 ring-indigo-100/60"
                    style="--d: 16;">
                    <div class="mb-4 flex items-start justify-between gap-2">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Agenda</h2>
                            <p class="mt-1 text-xs text-slate-500">{{ $dashboardNow->format('F Y') }}</p>
                        </div>
                        <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-2.5 py-1.5 text-right">
                            <div class="text-[10px] font-bold uppercase tracking-wide text-indigo-600">Now</div>
                            <div id="dash_agenda_clock" class="text-xs font-semibold text-indigo-800">
                                {{ $dashboardNow->format('h:i:s A') }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        @forelse ($dashboardAgenda as $agendaItem)
                            <div class="rounded-2xl border border-slate-200 bg-white/90 px-3 py-2.5">
                                <div class="flex flex-wrap items-start gap-3 sm:flex-nowrap">
                                    <div
                                        class="w-20 shrink-0 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-center sm:min-w-[92px]">
                                        <div class="text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                            {{ $agendaItem['weekday_label'] ?? '-' }}
                                        </div>
                                        <div class="text-[11px] font-semibold text-slate-700">
                                            {{ $agendaItem['date_label'] ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="text-xs font-semibold text-slate-800">
                                                {{ $agendaItem['class_label'] ?? 'Unassigned Class' }}
                                            </div>
                                            <span
                                                class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700">
                                                {{ $agendaItem['relative_label'] ?? 'Upcoming' }}
                                            </span>
                                        </div>
                                        <div class="mt-0.5 text-[11px] text-slate-500">
                                            {{ $agendaItem['day_label'] ?? 'All Days' }} |
                                            {{ $agendaItem['period_label'] ?? 'Custom' }}
                                        </div>
                                        <div class="mt-0.5 text-[11px] text-slate-500">
                                            {{ $agendaItem['date_full_label'] ?? ($agendaItem['date_label'] ?? '-') }}
                                        </div>
                                        <div class="mt-1 text-xs font-bold text-indigo-700">
                                            {{ $agendaItem['time_label'] ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-200 bg-white/60 px-3 py-4 text-center text-xs text-slate-500">
                                No schedule slots yet. Add class study times to see agenda.
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('admin.time-studies.index') }}"
                        class="mt-3 inline-flex items-center rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                        Open Time Studies
                    </a>
                </article>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const agendaClockElement = document.getElementById('dash_agenda_clock');
            if (agendaClockElement) {
                const updateAgendaClock = () => {
                    const now = new Date();
                    const hh = String(((now.getHours() + 11) % 12) + 1).padStart(2, '0');
                    const mm = String(now.getMinutes()).padStart(2, '0');
                    const ss = String(now.getSeconds()).padStart(2, '0');
                    const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
                    agendaClockElement.textContent = `${hh}:${mm}:${ss} ${ampm}`;
                };
                updateAgendaClock();
                window.setInterval(updateAgendaClock, 1000);
            }

            if (typeof Chart === 'undefined') {
                return;
            }

            // Make Chart.js responsive so canvases size to their parent containers
            Chart.defaults.responsive = true;
            Chart.defaults.maintainAspectRatio = false;

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
            const isCompactViewport = window.matchMedia('(max-width: 640px)').matches;
            const dashboardLegend = (desktop = 'top', mobile = 'bottom') => ({
                position: isCompactViewport ? mobile : desktop,
                labels: {
                    boxWidth: isCompactViewport ? 10 : 12,
                    usePointStyle: true,
                    padding: isCompactViewport ? 10 : 14,
                    font: {
                        size: isCompactViewport ? 11 : 12,
                    },
                },
            });
            const dashboardLayoutPadding = isCompactViewport
                ? { left: 4, right: 4, top: 2, bottom: 0 }
                : { left: 0, right: 0, top: 0, bottom: 0 };
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
                            backgroundColor: createGradient(trendCtx, 'rgba(79,70,229,0.35)',
                                'rgba(79,70,229,0.02)'),
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3,
                            pointBackgroundColor: '#4f46e5',
                        }, {
                            label: 'Teachers',
                            data: trendData.teachers,
                            borderWidth: 3,
                            borderColor: '#0ea5e9',
                            backgroundColor: createGradient(trendCtx, 'rgba(14,165,233,0.28)',
                                'rgba(14,165,233,0.02)'),
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
                        layout: {
                            padding: dashboardLayoutPadding,
                        },
                        plugins: {
                            legend: dashboardLegend('top', 'bottom'),
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
                        layout: {
                            padding: dashboardLayoutPadding,
                        },
                        plugins: {
                            legend: dashboardLegend('top', 'bottom'),
                        }
                    }
                });
            }

            const attendanceOverviewCanvas = document.getElementById('attendanceOverviewChart');
            if (attendanceOverviewCanvas) {
                new Chart(attendanceOverviewCanvas, {
                    type: 'bar',
                    data: {
                        labels: attendanceData.labels.length ? attendanceData.labels : ['Mon', 'Tue', 'Wed',
                            'Thu', 'Fri'
                        ],
                        datasets: [{
                            label: 'Total Present',
                            data: attendanceData.present.length ? attendanceData.present : [0, 0, 0,
                                0, 0
                            ],
                            backgroundColor: '#facc15',
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 28,
                        }, {
                            label: 'Total Absent',
                            data: attendanceData.absent.length ? attendanceData.absent : [0, 0, 0,
                                0, 0
                            ],
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
                        layout: {
                            padding: dashboardLayoutPadding,
                        },
                        plugins: {
                            legend: dashboardLegend('top', 'bottom'),
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
                        layout: {
                            padding: dashboardLayoutPadding,
                        },
                        plugins: {
                            legend: dashboardLegend('bottom', 'bottom'),
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
                        layout: {
                            padding: dashboardLayoutPadding,
                        },
                        plugins: {
                            legend: dashboardLegend('bottom', 'bottom'),
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
