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
        $financeBillable = max(
            (float) ($financeStats['billable'] ?? 0),
            $financeCollected + $financeOutstanding + $financeDiscounts,
        );

        $cardClass =
            'dashboard-card dash-reveal dash-hover min-w-0 rounded-[28px] border border-slate-200/80 bg-white p-5 shadow-[0_24px_55px_-42px_rgba(15,23,42,0.75)] dark:border-slate-700/80 dark:bg-slate-900 dark:shadow-[0_24px_70px_-42px_rgba(0,0,0,0.9)]';

        $titleClass = 'text-base font-bold text-slate-950 dark:text-white';
        $metaClass = 'text-xs font-semibold text-slate-500 dark:text-slate-400';
    @endphp

    <div class="dashboard-stage mx-auto max-w-[1500px] space-y-6 pb-8 text-slate-900 dark:text-slate-100">

        {{-- HEADER --}}
        <section
            class="dash-reveal relative z-30 overflow-visible rounded-[32px] border border-white/20 bg-gradient-to-br from-indigo-600 via-violet-600 to-sky-500 p-6 shadow-[0_24px_70px_-35px_rgba(79,70,229,0.65)] dark:border-slate-700/70 dark:from-slate-950 dark:via-slate-900 dark:to-indigo-950"
            style="--d: 1; overflow: visible;">

            <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-white/15 blur-2xl"></div>
            <div
                class="pointer-events-none absolute -bottom-14 right-20 h-44 w-44 rounded-full bg-cyan-200/25 blur-2xl dark:bg-indigo-400/20">
            </div>

            <div
                class="relative grid grid-cols-1 items-center gap-6 md:grid-cols-[minmax(0,1fr)_220px] lg:grid-cols-[minmax(0,1fr)_280px]">
                <div>
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-sm font-semibold text-indigo-100 shadow-sm backdrop-blur">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3 3 8v8l9 5 9-5V8l-9-5Z" />
                            <path d="M12 12V3" />
                            <path d="m3 8 9 5 9-5" />
                        </svg>
                        Reports
                    </div>

                    <h1 class="mt-3 text-3xl font-black tracking-[-0.05em] text-white sm:text-4xl">
                        Reports Overview
                    </h1>

                    <p class="mt-2 max-w-xl text-sm font-semibold leading-6 text-white/75">
                        Key insights and data visualizations about your school.
                    </p>

                    <div class="relative z-50 mt-5 flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.reports.export.excel') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/35 bg-white px-4 py-2.5 text-xs font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-50 sm:w-auto dark:border-emerald-400/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:hover:bg-emerald-500/25">
                            <i class="fa-solid fa-file-excel" aria-hidden="true"></i>
                            Excel
                        </a>

                        <a href="{{ route('admin.reports.export.pdf') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/35 bg-white px-4 py-2.5 text-xs font-bold text-rose-700 shadow-sm transition hover:bg-rose-50 sm:w-auto dark:border-rose-400/20 dark:bg-rose-500/15 dark:text-rose-300 dark:hover:bg-rose-500/25">
                            <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>
                            PDF
                        </a>

                        <div class="relative z-[90] w-full sm:w-auto" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/35 bg-white/10 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-white/20 sm:w-auto"
                                @click="open = !open">
                                <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                                More
                                <i class="fa-solid fa-chevron-down text-[10px]" aria-hidden="true"></i>
                            </button>

                            <div x-show="open" x-cloak x-transition.origin.top.right
                                class="absolute left-0 top-full z-[100] mt-3 max-h-96 w-full overflow-y-auto rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-2xl ring-1 ring-slate-900/5 sm:left-auto sm:right-0 sm:w-72 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:ring-white/10">

                                <a href="{{ route('admin.students.export.excel') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-300"
                                        aria-hidden="true"></i>
                                    Students Excel
                                </a>

                                <a href="{{ route('admin.students.export.pdf') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <i class="fa-solid fa-file-pdf text-rose-600 dark:text-rose-300" aria-hidden="true"></i>
                                    Students PDF
                                </a>

                                <a href="{{ route('admin.teachers.export.excel') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-300"
                                        aria-hidden="true"></i>
                                    Teachers Excel
                                </a>

                                <a href="{{ route('admin.teachers.export.pdf') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <i class="fa-solid fa-file-pdf text-rose-600 dark:text-rose-300" aria-hidden="true"></i>
                                    Teachers PDF
                                </a>

                                <a href="{{ route('admin.finance.export.excel') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-300"
                                        aria-hidden="true"></i>
                                    Finance Excel
                                </a>

                                <a href="{{ route('admin.finance.export.pdf') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <i class="fa-solid fa-file-pdf text-rose-600 dark:text-rose-300" aria-hidden="true"></i>
                                    Finance PDF
                                </a>

                                <div class="border-t border-slate-100 dark:border-slate-700"></div>

                                <a href="{{ route('admin.contacts.index') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <i class="fa-solid fa-inbox text-indigo-600 dark:text-indigo-300"
                                        aria-hidden="true"></i>
                                    Review Inbox
                                </a>

                                <a href="{{ route('admin.student-study.index') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <i class="fa-solid fa-graduation-cap text-indigo-600 dark:text-indigo-300"
                                        aria-hidden="true"></i>
                                    Open Study List
                                </a>

                                <a href="{{ route('admin.finance.index') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <i class="fa-solid fa-wallet text-teal-600 dark:text-teal-300" aria-hidden="true"></i>
                                    Open Finance Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:flex md:items-center md:justify-end">
                    <div class="flex flex-col items-center gap-3 text-center">
                        <img src="{{ $schoolBrandLogo ?? asset('images/techbridge-logo-mark.svg') }}"
                            alt="{{ $schoolBrandName ?? 'TechBridge Academy' }} logo"
                            class="h-32 w-auto drop-shadow-2xl lg:h-44" />

                        <div
                            class="rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.26em] text-white/85 backdrop-blur">
                            {{ $schoolBrandName ?? 'TechBridge Academy' }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- FINANCE CHARTS --}}
        <section class="grid gap-6 xl:grid-cols-12">
            <article class="{{ $cardClass }} xl:col-span-8" style="--d: 7;">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="{{ $titleClass }}">School Finance Detail</h2>
                        <p class="mt-1 {{ $metaClass }}">
                            Collected and outstanding student payments for the last 7 days.
                        </p>
                    </div>

                    <a href="{{ route('admin.finance.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-teal-100 bg-teal-50 px-3 py-2 text-xs font-bold text-teal-700 transition hover:bg-teal-100 dark:border-teal-400/20 dark:bg-teal-500/15 dark:text-teal-300 dark:hover:bg-teal-500/25">
                        Open detail
                        <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="dashboard-chart-box dashboard-chart-box--medium mt-5 h-72 sm:h-80 lg:h-[360px]">
                    <canvas id="schoolFinanceChart" class="h-full w-full"></canvas>
                </div>
            </article>

            <article class="{{ $cardClass }} xl:col-span-4" style="--d: 8;">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="{{ $titleClass }}">Payment Status</h2>
                        <p class="mt-1 {{ $metaClass }}">Paid, pending, overdue, and waived amounts.</p>
                    </div>
                </div>

                <div class="dashboard-chart-box mt-4 h-64 sm:h-72">
                    <canvas id="financeStatusChart" class="h-full w-full"></canvas>
                </div>
            </article>
        </section>

        {{-- STUDENTS + ATTENDANCE --}}
        <section class="grid gap-6 xl:grid-cols-12">
            <article class="{{ $cardClass }} xl:col-span-4" style="--d: 7;">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="{{ $titleClass }}">Students</h2>
                    <span class="{{ $metaClass }}">Active vs Inactive</span>
                </div>

                <div class="dashboard-chart-box h-56 sm:h-64 md:h-72 lg:h-[280px]">
                    <canvas id="studentsSnapshotChart" class="h-full w-full"></canvas>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-3 text-center">
                    <div
                        class="rounded-xl border border-sky-100 bg-sky-50 px-3 py-2 dark:border-sky-400/20 dark:bg-sky-500/15">
                        <div class="{{ $metaClass }}">Active</div>
                        <div class="text-lg font-black text-slate-950 dark:text-white">
                            {{ number_format($studentsActive ?? 0) }}
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-amber-100 bg-amber-50 px-3 py-2 dark:border-amber-400/20 dark:bg-amber-500/15">
                        <div class="{{ $metaClass }}">Inactive</div>
                        <div class="text-lg font-black text-slate-950 dark:text-white">
                            {{ number_format(max(0, ($studentsTotal ?? 0) - ($studentsActive ?? 0))) }}
                        </div>
                    </div>
                </div>
            </article>

            <article class="{{ $cardClass }} xl:col-span-8" style="--d: 8;">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="{{ $titleClass }}">Attendance</h2>
                    <span class="{{ $metaClass }}">Last 5 Days</span>
                </div>

                <div class="dashboard-chart-box dashboard-chart-box--tall h-64 sm:h-72 md:h-80 lg:h-[320px]">
                    <canvas id="attendanceOverviewChart" class="h-full w-full"></canvas>
                </div>

                @if (!($chartData['attendance']['hasData'] ?? false))
                    <p class="mt-3 {{ $metaClass }}">
                        No attendance records found yet. Chart will update automatically when attendance data is available.
                    </p>
                @endif
            </article>
        </section>

        {{-- MAIN REPORTS --}}
        <section class="grid gap-6 xl:grid-cols-12">
            <div class="min-w-0 space-y-6 xl:col-span-8">
                <article class="{{ $cardClass }}" style="--d: 9;">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <h2 class="{{ $titleClass }}">Enrollment Trend (Last 6 Months)</h2>
                        <span class="{{ $metaClass }}">Students vs Teachers</span>
                    </div>

                    <div class="dashboard-chart-box dashboard-chart-box--tall h-64 sm:h-72 md:h-80 lg:h-[320px]">
                        <canvas id="enrollmentTrendChart" class="h-full w-full"></canvas>
                    </div>
                </article>

                <div class="grid gap-6 lg:grid-cols-2">
                    <article class="{{ $cardClass }}" style="--d: 10;">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="{{ $titleClass }}">System Composition</h2>
                            <span class="{{ $metaClass }}">Population split</span>
                        </div>

                        <div class="dashboard-chart-box h-56 sm:h-64 md:h-72 lg:h-[280px]">
                            <canvas id="compositionChart" class="h-full w-full"></canvas>
                        </div>
                    </article>

                    <article class="{{ $cardClass }}" style="--d: 11;">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="{{ $titleClass }}">Study Period Distribution</h2>
                            <span class="{{ $metaClass }}">Morning, night, and more</span>
                        </div>

                        <div class="dashboard-chart-box h-56 sm:h-64 md:h-72 lg:h-[280px]">
                            <canvas id="periodChart" class="h-full w-full"></canvas>
                        </div>
                    </article>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <article class="{{ $cardClass }}" style="--d: 12;">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="{{ $titleClass }}">Top Class Load</h2>
                            <span class="{{ $metaClass }}">Students per class</span>
                        </div>

                        <div
                            class="dashboard-chart-box dashboard-chart-box--medium h-56 sm:h-72 md:h-[300px] lg:h-[300px]">
                            <canvas id="classLoadChart" class="h-full w-full"></canvas>
                        </div>
                    </article>

                    <article class="{{ $cardClass }}" style="--d: 13;">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="{{ $titleClass }}">Subject Health</h2>
                            <span class="{{ $metaClass }}">Status and assignment</span>
                        </div>

                        <div
                            class="dashboard-chart-box dashboard-chart-box--medium h-56 sm:h-72 md:h-[300px] lg:h-[300px]">
                            <canvas id="subjectHealthChart" class="h-full w-full"></canvas>
                        </div>
                    </article>
                </div>
            </div>

            {{-- RIGHT SIDEBAR --}}
            <div class="min-w-0 space-y-6 xl:col-span-4">

                {{-- LATEST MESSAGES --}}
                <article class="{{ $cardClass }}" style="--d: 15;">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="{{ $titleClass }}">Latest Messages</h2>

                        <a href="{{ route('admin.contacts.index') }}"
                            class="text-xs font-semibold text-slate-400 transition hover:text-indigo-600 dark:text-slate-500 dark:hover:text-indigo-300">
                            View all
                        </a>
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
                                class="block rounded-2xl border border-slate-100 p-3 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">

                                <div class="flex items-start gap-3">
                                    <div
                                        class="grid h-10 w-10 place-items-center rounded-full font-bold
                                        {{ $contactMessage->is_read
                                            ? 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'
                                            : 'bg-violet-100 text-violet-600 dark:bg-violet-500/15 dark:text-violet-300' }}">
                                        {{ $initials }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="truncate text-sm font-semibold text-slate-950 dark:text-white">
                                                {{ $name }}
                                            </div>

                                            <div class="text-[11px] text-slate-400 dark:text-slate-500">
                                                {{ $contactMessage->created_at->diffForHumans() }}
                                            </div>
                                        </div>

                                        <div class="truncate text-xs font-semibold text-slate-600 dark:text-slate-300">
                                            {{ $contactMessage->subject ?: 'No subject' }}
                                        </div>

                                        <div class="truncate text-xs text-slate-500 dark:text-slate-400">
                                            {{ \Illuminate\Support\Str::limit($contactMessage->message, 70) }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div
                                class="rounded-2xl bg-slate-50 px-3 py-4 text-center text-xs text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                No contact messages yet.
                            </div>
                        @endforelse
                    </div>
                </article>

                {{-- AGENDA --}}
                <article
                    class="dashboard-card dash-reveal dash-hover min-w-0 rounded-[28px] border border-indigo-100 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 shadow-[0_24px_55px_-42px_rgba(79,70,229,0.45)] ring-1 ring-indigo-100/60 dark:border-indigo-400/20 dark:bg-[linear-gradient(135deg,rgba(79,70,229,0.18),rgba(14,165,233,0.08),rgba(15,23,42,0.94))] dark:ring-indigo-400/10"
                    style="--d: 16;">

                    <div class="mb-4 flex items-start justify-between gap-2">
                        <div>
                            <h2 class="{{ $titleClass }}">Agenda</h2>
                            <p class="mt-1 {{ $metaClass }}">{{ $dashboardNow->format('F Y') }}</p>
                        </div>

                        <div
                            class="rounded-xl border border-indigo-200 bg-indigo-50 px-2.5 py-1.5 text-right dark:border-indigo-400/20 dark:bg-indigo-500/15">
                            <div
                                class="text-[10px] font-bold uppercase tracking-wide text-indigo-600 dark:text-indigo-300">
                                Now
                            </div>

                            <div id="dash_agenda_clock"
                                class="text-xs font-semibold text-indigo-800 dark:text-indigo-200">
                                {{ $dashboardNow->format('h:i:s A') }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        @forelse ($dashboardAgenda as $agendaItem)
                            <div
                                class="rounded-2xl border border-slate-200 bg-white/90 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-900/80">
                                <div class="flex flex-wrap items-start gap-3 sm:flex-nowrap">
                                    <div
                                        class="w-20 shrink-0 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-center sm:min-w-[92px] dark:border-slate-700 dark:bg-slate-800">
                                        <div
                                            class="text-[10px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                            {{ $agendaItem['weekday_label'] ?? '-' }}
                                        </div>

                                        <div class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $agendaItem['date_label'] ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="text-xs font-semibold text-slate-800 dark:text-white">
                                                {{ $agendaItem['class_label'] ?? 'Unassigned Class' }}
                                            </div>

                                            <span
                                                class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-300">
                                                {{ $agendaItem['relative_label'] ?? 'Upcoming' }}
                                            </span>
                                        </div>

                                        <div class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                            {{ $agendaItem['day_label'] ?? 'All Days' }} |
                                            {{ $agendaItem['period_label'] ?? 'Custom' }}
                                        </div>

                                        <div class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                            {{ $agendaItem['date_full_label'] ?? ($agendaItem['date_label'] ?? '-') }}
                                        </div>

                                        <div class="mt-1 text-xs font-bold text-indigo-700 dark:text-indigo-300">
                                            {{ $agendaItem['time_label'] ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-200 bg-white/60 px-3 py-4 text-center text-xs text-slate-500 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-400">
                                No schedule slots yet. Add class study times to see agenda.
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('admin.time-studies.index') }}"
                        class="mt-3 inline-flex items-center rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-300 dark:hover:bg-indigo-500/25">
                        Open Time Studies
                    </a>
                </article>
            </div>
        </section>
    </div>

    <script id="admin-dashboard-data" type="application/json">@json($chartData)</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
