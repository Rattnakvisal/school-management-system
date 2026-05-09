@extends('layout.admin.navbar')

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
        $financeStats = $financeStats ?? [];
        $financeCollected = (float) ($financeStats['collected'] ?? 0);
        $financeOutstanding = (float) ($financeStats['outstanding'] ?? 0);
        $financeDiscounts = (float) ($financeStats['discounts'] ?? 0);
        $financeBillable = max((float) ($financeStats['billable'] ?? 0), $financeCollected + $financeOutstanding + $financeDiscounts);
        $financePayments = (int) ($financeStats['payments'] ?? 0);
        $adminStatCards = [
            [
                'label' => 'Students',
                'activeLabel' => 'Active',
                'active' => (int) ($studentsActive ?? 0),
                'total' => (int) ($studentsTotal ?? 0),
                'route' => route('admin.students.index'),
                'icon' => 'students',
                'tone' => 'from-indigo-100 to-white text-indigo-600',
            ],
            [
                'label' => 'Teachers',
                'activeLabel' => 'Active',
                'active' => (int) ($teachersActive ?? 0),
                'total' => (int) ($teachersTotal ?? 0),
                'route' => route('admin.teachers.index'),
                'icon' => 'teachers',
                'tone' => 'from-sky-100 to-white text-sky-600',
            ],
            [
                'label' => 'Classes',
                'activeLabel' => 'Active',
                'active' => (int) ($classesActive ?? 0),
                'total' => (int) ($classesTotal ?? 0),
                'route' => route('admin.classes.index'),
                'icon' => 'classes',
                'tone' => 'from-emerald-100 to-white text-emerald-600',
            ],
            [
                'label' => 'Subjects',
                'activeLabel' => 'Active',
                'active' => (int) ($subjectsActive ?? 0),
                'total' => (int) ($subjectsTotal ?? 0),
                'route' => route('admin.subjects.index'),
                'icon' => 'subjects',
                'tone' => 'from-amber-100 to-white text-amber-600',
            ],
            [
                'label' => 'Collected',
                'activeLabel' => 'Cash',
                'active' => (int) round($financeCollected),
                'total' => max(1, (int) round($financeBillable)),
                'route' => route('admin.finance.index'),
                'icon' => 'finance',
                'tone' => 'from-emerald-100 to-white text-emerald-600',
                'display' => '$' . number_format($financeCollected, 2),
                'totalDisplay' => '$' . number_format($financeBillable, 2) . '',
            ],
            [
                'label' => 'Outstanding',
                'activeLabel' => 'Due',
                'active' => (int) round($financeOutstanding),
                'total' => max(1, (int) round($financeBillable)),
                'route' => route('admin.finance.index'),
                'icon' => 'finance',
                'tone' => 'from-amber-100 to-white text-amber-600',
                'display' => '$' . number_format($financeOutstanding, 2),
                'totalDisplay' => '$' . number_format($financeBillable, 2) . '',
            ],
            [
                'label' => 'Discounts',
                'activeLabel' => 'Given',
                'active' => (int) round($financeDiscounts),
                'total' => max(1, (int) round($financeBillable)),
                'route' => route('admin.finance.index'),
                'icon' => 'finance',
                'tone' => 'from-sky-100 to-white text-sky-600',
                'display' => '$' . number_format($financeDiscounts, 2),
                'totalDisplay' => '$' . number_format($financeBillable, 2) . '',
            ],
            [
                'label' => 'Messages',
                'activeLabel' => 'Unread',
                'active' => (int) ($messagesUnread ?? 0),
                'total' => (int) ($messagesTotal ?? 0),
                'route' => route('admin.contacts.index'),
                'icon' => 'messages',
                'tone' => 'from-rose-100 to-white text-rose-600',
            ],
            [
                'label' => 'Study Time',
                'activeLabel' => 'Assigned',
                'active' => (int) ($studentsWithStudyTime ?? 0),
                'total' => (int) ($studentsTotal ?? 0),
                'route' => route('admin.student-study.index'),
                'icon' => 'study-time',
                'tone' => 'from-violet-100 to-white text-violet-600',
            ],
            [
                'label' => 'Major Subjects',
                'activeLabel' => 'Assigned',
                'active' => (int) ($studentsWithMajor ?? 0),
                'total' => (int) ($studentsTotal ?? 0),
                'route' => route('admin.student-study.index'),
                'icon' => 'major-subjects',
                'tone' => 'from-cyan-100 to-white text-cyan-600',
            ],
        ];
        $statPanelClass =
            'rounded-[26px] border border-white/80 bg-white/90 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.55)] backdrop-blur';
    @endphp

    <div class="dashboard-stage space-y-6">
        <section class="dash-reveal admin-page-header relative z-30 overflow-visible rounded-3xl p-6 shadow-lg"
            style="--d: 1; overflow: visible;">
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
                        Reports
                    </div>
                    <h1 class="mt-1 admin-page-title font-black tracking-tight">Reports Overview</h1>
                    <p class="mt-2 max-w-2xl text-sm admin-page-subtitle">
                        {{ $greeting }} {{ $fullName }}. Review school performance, attendance, enrollment,
                        study profile, and message activity from one reporting page.
                    </p>
                    <p class="mt-1 text-xs text-slate-500">{{ '$' . number_format($financeBillable, 2) }} billable</p>

                    <div class="relative z-50 mt-4 flex flex-wrap items-center gap-2 admin-page-header__actions">
                        <a href="{{ route('admin.reports.export.excel') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/35 bg-white px-4 py-2 text-xs font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-50 sm:w-auto">
                            <i class="fa-solid fa-file-excel" aria-hidden="true"></i>
                            Excel
                        </a>
                        <a href="{{ route('admin.reports.export.pdf') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/35 bg-white px-4 py-2 text-xs font-bold text-rose-700 shadow-sm transition hover:bg-rose-50 sm:w-auto">
                            <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>
                            PDF
                        </a>
                        <div class="relative z-[90] w-full sm:w-auto" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/35 bg-white/10 px-4 py-2 text-xs font-bold text-white transition hover:bg-white/20 sm:w-auto"
                                @click="open = !open">
                                <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                                More
                                <i class="fa-solid fa-chevron-down text-[10px]" aria-hidden="true"></i>
                            </button>

                            <div x-show="open" x-cloak x-transition.origin.top.right
                                class="absolute left-0 top-full z-[100] mt-3 max-h-96 w-full overflow-y-auto rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-2xl ring-1 ring-slate-900/5 sm:left-auto sm:right-0 sm:w-72">
                                <a href="{{ route('admin.students.export.excel') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50">
                                    <i class="fa-solid fa-file-excel text-emerald-600" aria-hidden="true"></i>
                                    Students Excel
                                </a>
                                <a href="{{ route('admin.students.export.pdf') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50">
                                    <i class="fa-solid fa-file-pdf text-rose-600" aria-hidden="true"></i>
                                    Students PDF
                                </a>
                                <a href="{{ route('admin.teachers.export.excel') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50">
                                    <i class="fa-solid fa-file-excel text-emerald-600" aria-hidden="true"></i>
                                    Teachers Excel
                                </a>
                                <a href="{{ route('admin.teachers.export.pdf') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50">
                                    <i class="fa-solid fa-file-pdf text-rose-600" aria-hidden="true"></i>
                                    Teachers PDF
                                </a>
                                <div class="border-t border-slate-100"></div>
                                <a href="{{ route('admin.contacts.index') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50">
                                    <i class="fa-solid fa-inbox text-indigo-600" aria-hidden="true"></i>
                                    Review Inbox
                                </a>
                                <a href="{{ route('admin.student-study.index') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50">
                                    <i class="fa-solid fa-graduation-cap text-indigo-600" aria-hidden="true"></i>
                                    Open Study List
                                </a>
                                <a href="{{ route('admin.finance.index') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50">
                                    <i class="fa-solid fa-wallet text-teal-600" aria-hidden="true"></i>
                                    Open Finance Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:flex md:items-center md:justify-end">
                    <div class="dashboard-hero-art">
                        <div class="flex flex-col items-center gap-3 text-center">
                            <img src="{{ $schoolBrandLogo ?? asset('images/techbridge-logo-mark.svg') }}"
                                alt="{{ $schoolBrandName ?? 'TechBridge Academy' }} logo"
                                class="h-32 w-auto dashboard-logo dashboard-logo--enhanced lg:h-44" />
                            <div
                                class="rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.26em] text-white/85">
                                {{ $schoolBrandName ?? 'TechBridge Academy' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($adminStatCards as $index => $card)
                @php
                    $progress = (int) round(($card['active'] / max(1, $card['total'])) * 100);
                @endphp
                <a href="{{ $card['route'] }}" class="dash-reveal dash-hover {{ $statPanelClass }} min-h-[132px] p-5"
                    style="--d: {{ $index + 2 }};">
                    <div class="flex items-start justify-between gap-4">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br {{ $card['tone'] }}">
                            @switch($card['icon'])
                                @case('students')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-8 0v2" />
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                @break

                                @case('teachers')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M6 21v-2a6 6 0 0 1 12 0v2" />
                                        <path d="M9 11h6" />
                                    </svg>
                                @break

                                @case('classes')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m12 3 8 4-8 4-8-4 8-4Z" />
                                        <path d="m4 12 8 4 8-4" />
                                        <path d="m4 17 8 4 8-4" />
                                    </svg>
                                @break

                                @case('subjects')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5V5a2 2 0 0 1 2-2h11a3 3 0 0 1 3 3v14a2 2 0 0 0-2-2H6a2 2 0 0 0-2 1.5Z" />
                                        <path d="M8 7h7" />
                                        <path d="M8 11h5" />
                                    </svg>
                                @break

                                @case('finance')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5H20v14H6.5A2.5 2.5 0 0 1 4 16.5v-9Z" />
                                        <path d="M20 9H6.5A2.5 2.5 0 0 1 4 6.5" />
                                        <path d="M16.5 13.5h.01" />
                                    </svg>
                                @break

                                @case('study-time')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 7v5l3 2" />
                                        <path d="M7 3 4 6" />
                                        <path d="m20 6-3-3" />
                                    </svg>
                                @break

                                @case('major-subjects')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3 3 8l9 5 9-5-9-5Z" />
                                        <path d="M6 10v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5" />
                                        <path d="M21 8v5" />
                                    </svg>
                                @break

                                @default
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a4 4 0 0 1-4 4H7l-4 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z" />
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
                        <span>{{ $card['display'] ?? number_format($card['active']) }}</span>
                        <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                            {{ $card['totalDisplay'] ?? number_format($card['total']) }}</span>
                    </div>
                    <div class="mt-1 text-sm font-bold text-slate-600">{{ $card['label'] }}</div>
                    <div class="mt-1 text-[11px] font-semibold text-slate-400">
                        {{ $card['activeLabel'] }}: {{ number_format($card['active']) }}
                    </div>
                    <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                        <span class="block h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400"
                            style="width: {{ min(100, max(0, $progress)) }}%"></span>
                    </div>
                </a>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-12">
            <article
                class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--d: 7;">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">School Finance Detail</h2>
                        <p class="mt-1 text-xs text-slate-500">Collected and outstanding student payments for the last 7 days.</p>
                    </div>
                    <a href="{{ route('admin.finance.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-teal-100 bg-teal-50 px-3 py-2 text-xs font-bold text-teal-700 transition hover:bg-teal-100">
                        Open detail
                        <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="dashboard-chart-box dashboard-chart-box--medium mt-5 h-72 sm:h-80 lg:h-[360px]">
                    <canvas id="schoolFinanceChart" class="h-full w-full"></canvas>
                </div>
            </article>

            <article
                class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--d: 8;">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Payment Status</h2>
                        <p class="mt-1 text-xs text-slate-500">Paid, pending, overdue, and waived amounts.</p>
                    </div>
                </div>
                <div class="dashboard-chart-box mt-4 h-64 sm:h-72">
                    <canvas id="financeStatusChart" class="h-full w-full"></canvas>
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-12">
            <article
                class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-12"
                style="--d: 9;">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Latest Student Payments</h2>
                        <p class="mt-1 text-xs text-slate-500">Newest payment amounts by student.</p>
                    </div>
                    <a href="{{ route('admin.finance.index') }}" class="text-xs font-bold text-indigo-700">Manage payments</a>
                </div>
                <div class="dashboard-chart-box dashboard-chart-box--medium mt-4 h-64 sm:h-72 lg:h-[300px]">
                    <canvas id="latestStudentPaymentsChart" class="h-full w-full"></canvas>
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-12">
            <article
                class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
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

            <article
                class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
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
                <article
                    class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
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
                    <article
                        class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 10;">
                        <div class="dashboard-card-header mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">System Composition</h2>
                            <span class="dashboard-card-meta text-xs text-slate-500">Population split</span>
                        </div>
                        <div class="dashboard-chart-box h-56 sm:h-64 md:h-72 lg:h-[280px]">
                            <canvas id="compositionChart" class="w-full h-full"></canvas>
                        </div>
                    </article>

                    <article
                        class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
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
                    <article
                        class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 12;">
                        <div class="dashboard-card-header mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">Top Class Load</h2>
                            <span class="dashboard-card-meta text-xs text-slate-500">Students per class</span>
                        </div>
                        <div
                            class="dashboard-chart-box dashboard-chart-box--medium h-56 sm:h-72 md:h-[300px] lg:h-[300px]">
                            <canvas id="classLoadChart" class="w-full h-full"></canvas>
                        </div>
                    </article>

                    <article
                        class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                        style="--d: 13;">
                        <div class="dashboard-card-header mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-bold text-slate-900">Subject Health</h2>
                            <span class="dashboard-card-meta text-xs text-slate-500">Status and assignment</span>
                        </div>
                        <div
                            class="dashboard-chart-box dashboard-chart-box--medium h-56 sm:h-72 md:h-[300px] lg:h-[300px]">
                            <canvas id="subjectHealthChart" class="w-full h-full"></canvas>
                        </div>
                    </article>
                </div>
            </div>

            <div class="space-y-6 min-w-0 xl:col-span-4">
                <article
                    class="dashboard-card dash-reveal dash-hover min-w-0 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
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

    <script id="admin-dashboard-data" type="application/json">@json($chartData)</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
