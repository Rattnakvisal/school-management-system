@extends('layout.admin.navbar.navbar')

@section('page')
    @php
        $dashboardNow = $dashboardNow ?? now();
        $dashboardAgenda = collect($dashboardAgenda ?? []);
        $studentsTotal = (int) ($studentsTotal ?? 0);
        $teachersTotal = (int) ($teachersTotal ?? 0);
        $classesTotal = (int) ($classesTotal ?? 0);
        $subjectsTotal = (int) ($subjectsTotal ?? 0);
        $messagesUnread = (int) ($messagesUnread ?? 0);
        $studentsActive = (int) ($studentsActive ?? 0);
        $teachersActive = (int) ($teachersActive ?? 0);
        $financeStats = $financeStats ?? [];
        $incomeTotal = (float) ($financeStats['collected'] ?? 0);
        $expenseTotal = (float) ($financeStats['outstanding'] ?? 0);
        $discountTotal = (float) ($financeStats['discounts'] ?? 0);
        $billableTotal = max((float) ($financeStats['billable'] ?? 0), $incomeTotal + $expenseTotal + $discountTotal);
        $outstandingDisplay = '$' . number_format($expenseTotal);
        $financeRecords = (int) ($financeStats['payments'] ?? 0);

        $dashboardChartData = $chartData ?? [];

        $calendarStart = $dashboardNow->copy()->startOfMonth();
        $calendarDays = [];
        for ($i = 0; $i < ($calendarStart->dayOfWeek + 6) % 7; $i++) {
            $calendarDays[] = null;
        }
        for ($day = 1; $day <= $dashboardNow->daysInMonth; $day++) {
            $calendarDays[] = $calendarStart->copy()->day($day);
        }

        $teacherPanelClass =
            'rounded-[28px] border border-slate-200/80 bg-white/90 shadow-[0_18px_50px_-30px_rgba(15,23,42,0.22)] backdrop-blur-sm';
        $teacherTitleClass = 'text-lg font-extrabold tracking-[-0.03em] text-slate-900';
        $adminName = trim((string) (auth()->user()->name ?? 'Admin'));
        $hour = (int) $dashboardNow->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
        $summaryCards = [
            [
                'label' => 'Active Students',
                'value' => $studentsActive,
                'note' => '+2 from last month',
                'route' => route('admin.students.index'),
                'icon' => 'fa-users',
                'tile' => 'bg-indigo-50 text-indigo-600',
                'trend' => 'bg-indigo-50 text-indigo-600',
            ],
            [
                'label' => 'Active Teachers',
                'value' => $teachersActive,
                'note' => 'No change',
                'route' => route('admin.teachers.index'),
                'icon' => 'fa-graduation-cap',
                'tile' => 'bg-emerald-50 text-emerald-600',
                'trend' => 'bg-emerald-50 text-emerald-600',
            ],
            [
                'label' => 'Outstanding Balance',
                'value' => $outstandingDisplay,
                'note' => '$' . number_format($incomeTotal) . ' collected',
                'route' => route('admin.finance.index'),
                'icon' => 'fa-wallet',
                'tile' => 'bg-orange-50 text-orange-500',
                'trend' => 'bg-orange-50 text-orange-500',
            ],
            [
                'label' => 'Upcoming Events',
                'value' => $dashboardAgenda->count(),
                'note' => 'This week',
                'route' => route('admin.time-studies.index'),
                'icon' => 'fa-clipboard-list',
                'tile' => 'bg-sky-50 text-sky-600',
                'trend' => 'bg-sky-50 text-sky-600',
            ],
        ];
    @endphp

    <div class="dashboard-stage mx-auto max-w-[1500px] space-y-5 pb-8 text-slate-900">
        <section class="dash-reveal space-y-5" style="--d: 1;">
            <div class="grid gap-5 xl:grid-cols-[minmax(0,1.65fr)_minmax(330px,0.8fr)]">
                <div class="space-y-5">
                    <article
                        class="overflow-hidden rounded-[28px] border border-white/80 bg-white shadow-[0_24px_60px_-42px_rgba(15,23,42,0.55)]">
                        <div
                            class="grid min-h-[380px] gap-6 bg-[linear-gradient(135deg,#ffffff_0%,#fbfdff_48%,#f4f7ff_100%)] p-7 sm:p-8 lg:grid-cols-[minmax(0,0.88fr)_minmax(300px,0.78fr)]">
                            <div class="flex min-w-0 flex-col justify-center">
                                <div
                                    class="inline-flex w-fit items-center gap-2 rounded-full font-bold border border-indigo-50 bg-white px-4 py-2 text-sm text-indigo-700 shadow-sm">
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                                    Live admin overview
                                </div>
                                <h2
                                    class="mt-8 max-w-xl text-[clamp(1.3rem,3.4vw,3rem)] font-bold leading-[1.05] tracking-[-0.06em] text-slate-950">
                                    Make faster decisions from school data.
                                </h2>
                                <p class="mt-5 max-w-xl text-base font-semibold leading-8 text-slate-500">
                                    Track students, teachers, schedules, messages, and finance health from a single clean
                                    workspace.
                                </p>
                                <a href="{{ route('admin.reports') }}"
                                    class="mt-8 inline-flex w-fit items-center gap-4 rounded-2xl bg-indigo-600 px-7 py-4 text-sm text-white font-bold shadow-[0_18px_35px_-20px_rgba(79,70,229,0.8)] transition hover:-translate-y-0.5 hover:bg-indigo-700">
                                    Explore Dashboard
                                    <i class="fa-solid fa-arrow-right text-base" aria-hidden="true"></i>
                                </a>
                            </div>

                            <div class="relative flex items-center justify-center">
                                <div class="absolute inset-x-8 inset-y-8 rounded-full bg-indigo-100/60 blur-3xl"></div>
                                <img src="{{ asset('images/3D.png') }}" alt="Student learning illustration"
                                    class="teacher-hero-art relative block h-auto w-full max-w-[30rem] max-h-[40rem] rounded-[30px] object-cover drop-shadow-[0_18px_40px_rgba(59,130,246,0.20)]
                                    transition group-hover:rotate-1 group-hover:scale-105" />
                            </div>
                        </div>
                    </article>

                    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
                        @foreach ($summaryCards as $index => $card)
                            <a href="{{ $card['route'] }}"
                                class="dash-hover group flex min-h-[168px] flex-col justify-between rounded-[24px] border border-white/80 bg-white p-5 shadow-[0_22px_48px_-36px_rgba(15,23,42,0.58)] transition hover:-translate-y-1 hover:border-indigo-100 hover:shadow-[0_28px_54px_-34px_rgba(79,70,229,0.35)]"
                                style="--d: {{ $index + 2 }};">
                                <div class="flex items-start justify-between gap-3">
                                    <span
                                        class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl {{ $card['tile'] }}">
                                        <i class="fa-solid {{ $card['icon'] }} text-xl" aria-hidden="true"></i>
                                    </span>
                                    <span
                                        class="grid h-9 w-9 shrink-0 place-items-center rounded-full {{ $card['trend'] }} transition group-hover:scale-110">
                                        <i class="fa-solid fa-arrow-trend-up text-xs" aria-hidden="true"></i>
                                    </span>
                                </div>

                                <div class="mt-5 min-w-0">
                                    <div class="truncate text-3xl leading-none tracking-[-0.04em] text-slate-950">
                                        {{ $card['value'] }}
                                    </div>
                                    <div class="mt-2 text-sm leading-tight text-slate-700">
                                        {{ $card['label'] }}
                                    </div>
                                    <div class="mt-2 max-w-[9.5rem] text-xs font-bold leading-5 text-slate-400">
                                        {{ $card['note'] }}
                                    </div>
                                </div>

                                <span class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                    <span
                                        class="block h-full w-2/3 rounded-full bg-gradient-to-r from-indigo-500 to-sky-400 transition group-hover:w-5/6"></span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <article
                    class="rounded-[28px] border border-white/80 bg-white p-6 shadow-[0_24px_60px_-42px_rgba(15,23,42,0.55)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg tracking-[-0.03em] font-bold text-slate-950">Upcoming Events</h2>
                            <p class="mt-2 text-sm font-bold text-slate-400">{{ $dashboardNow->format('F Y') }}</p>
                        </div>
                        <a href="{{ route('admin.time-studies.index') }}" class="text-sm text-indigo-600">View
                            all</a>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($dashboardAgenda->take(5) as $agendaItem)
                            @php
                                $agendaDateLabel = (string) ($agendaItem['date_label'] ?? 'Jan 01, 2026');
                                $agendaDay = \Illuminate\Support\Str::between($agendaDateLabel, ' ', ',') ?: '01';
                            @endphp
                            <a href="{{ route('admin.time-studies.index') }}"
                                class="group flex items-center gap-4 rounded-[22px] border border-slate-100 bg-slate-50/80 p-4 transition hover:-translate-y-0.5 hover:border-indigo-100 hover:bg-white hover:shadow-md">
                                <div
                                    class="grid h-16 w-14 shrink-0 place-items-center rounded-2xl bg-white text-center shadow-sm">
                                    <div>
                                        <div class="text-xl leading-none text-indigo-600">{{ $agendaDay }}
                                        </div>
                                        <div class="mt-2 text-[11px] uppercase text-slate-400">
                                            {{ $agendaItem['weekday_label'] ?? 'Day' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm text-slate-900">
                                        {{ $agendaItem['class_label'] ?? 'Class Schedule' }}
                                    </div>
                                    <div class="mt-2 truncate text-xs font-semibold text-slate-500">
                                        {{ $agendaItem['period_label'] ?? 'Study' }} |
                                        {{ $agendaItem['time_label'] ?? '-' }}
                                    </div>
                                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-200">
                                        <span
                                            class="block h-full w-2/3 rounded-full bg-emerald-400 transition group-hover:w-4/5"></span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div
                                class="rounded-[22px] border border-dashed border-slate-200 p-6 text-center text-sm font-semibold text-slate-500">
                                No upcoming study times yet.
                            </div>
                        @endforelse
                    </div>
                </article>
            </div>
        </section>

        <section class="grid gap-5 xl:grid-cols-[minmax(0,1.65fr)_minmax(300px,0.75fr)]">
            <article
                class="dashboard-card dash-reveal rounded-lg border border-slate-200 bg-white p-5 shadow-[0_24px_55px_-42px_rgba(15,23,42,0.75)]"
                style="--d: 8;">
                <div class="dashboard-card-header flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl text-slate-950 font-bold">School Performance</h2>
                        <div class="mt-1 text-[11px] font-bold text-slate-400">Six-month student and teacher trend</div>
                    </div>
                    <div class="flex items-center gap-4 text-[11px] font-bold">
                        <span class="inline-flex items-center gap-1.5 text-indigo-600">
                            <span class="h-2 w-2 rounded-full bg-indigo-600"></span> Students
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-emerald-500">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span> Teachers
                        </span>
                    </div>
                </div>
                <div class="dashboard-chart-box dashboard-chart-box--tall mt-4 h-[310px]">
                    <canvas id="enrollmentTrendChart" class="h-full w-full"></canvas>
                </div>
            </article>

            <article class="admin-dashboard-calendar dash-reveal {{ $teacherPanelClass }} p-5 sm:p-6" style="--d: 9;">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Calendar</div>
                        <h2 class="{{ $teacherTitleClass }} font-bold">
                            {{ $dashboardNow->format('F Y') }}
                        </h2>
                    </div>
                    <span
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-sky-50 text-lg font-bold text-sky-500">
                        &rarr;
                    </span>
                </div>

                <div
                    class="mb-3 grid grid-cols-7 gap-1.5 text-center text-[11px] font-bold uppercase tracking-wide text-slate-400">
                    <span>Mo</span>
                    <span>Tu</span>
                    <span>We</span>
                    <span>Th</span>
                    <span>Fr</span>
                    <span>Sa</span>
                    <span>Su</span>
                </div>

                <div class="grid grid-cols-7 gap-1.5">
                    @foreach ($calendarDays as $calendarDay)
                        @if ($calendarDay)
                            <span
                                class="teacher-calendar__day {{ $calendarDay->isSameDay($dashboardNow) ? 'teacher-calendar__day--active' : '' }}">
                                {{ $calendarDay->day }}
                            </span>
                        @else
                            <span class="teacher-calendar__day teacher-calendar__day--muted"></span>
                        @endif
                    @endforeach
                </div>
            </article>
        </section>

        <section class="grid gap-5 xl:grid-cols-[minmax(0,1.22fr)_minmax(360px,0.78fr)]">
            <article
                class="dashboard-card dash-reveal rounded-[28px] border border-slate-200/80 bg-white p-6 shadow-[0_24px_60px_-42px_rgba(15,23,42,0.45)]"
                style="--d: 10;">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-extrabold tracking-[-0.03em] text-slate-950">School Finance</h2>
                        <div class="mt-2 text-sm font-semibold text-slate-400">Weekly collected and outstanding payments</div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-emerald-50 px-4 py-2 text-xs text-emerald-600">
                            Collected ${{ number_format($incomeTotal) }}
                        </span>
                        <span class="rounded-full bg-orange-50 px-4 py-2 text-xs text-orange-500">
                            Outstanding ${{ number_format($expenseTotal) }}
                        </span>
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-7 text-sm font-semibold text-slate-500">
                    <span class="inline-flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                        Collected
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full border-4 border-orange-500 bg-white"></span>
                        Outstanding
                    </span>
                </div>

                <div class="dashboard-chart-box mt-4 h-[300px]">
                    <canvas id="schoolFinanceChart" class="h-full w-full"></canvas>
                </div>

                <div
                    class="mt-6 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-slate-50/80 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-indigo-50 text-indigo-600">
                            <i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i>
                        </span>
                        <p class="text-sm font-semibold text-slate-600">
                            {{ number_format($financeRecords) }} student payment records are available for finance reports.
                            Billable total: ${{ number_format($billableTotal) }}.
                        </p>
                    </div>
                    <a href="{{ route('admin.finance.index') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-50 px-4 py-2 text-xs text-indigo-700 transition hover:bg-indigo-100">
                        View full report
                    </a>
                </div>
            </article>

            <article
                class="dash-reveal rounded-[28px] border border-slate-200/80 bg-white p-6 shadow-[0_24px_60px_-42px_rgba(15,23,42,0.45)]"
                style="--d: 11;">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-extrabold tracking-[-0.03em] text-slate-950">Quick Reports</h2>
                        <p class="mt-2 text-sm font-semibold text-slate-400">Fast paths to key admin work</p>
                    </div>
                    <a href="{{ route('admin.reports') }}"
                        class="inline-flex shrink-0 items-center gap-2 text-xs text-indigo-600 transition hover:text-indigo-800">
                        Open all reports
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <div class="mt-6 grid gap-4">
                    <a href="{{ route('admin.students.index') }}"
                        class="group flex items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-100 hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-indigo-50 text-indigo-600">
                                <i class="fa-solid fa-users" aria-hidden="true"></i>
                            </span>
                            <div>
                                <div class="text-sm text-slate-950">Active Students</div>
                                <div class="mt-1 text-xs font-semibold text-slate-500">Enrollment and profile list</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-lg text-slate-950">{{ number_format($studentsActive) }}</div>
                            <span
                                class="grid h-8 w-8 place-items-center rounded-full bg-indigo-50 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white">
                                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                    <a href="{{ route('admin.teachers.index') }}"
                        class="group flex items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-100 hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-emerald-600">
                                <i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i>
                            </span>
                            <div>
                                <div class="text-sm text-slate-950">Active Teachers</div>
                                <div class="mt-1 text-xs font-semibold text-slate-500">Teacher accounts and status</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-lg text-slate-950">{{ number_format($teachersActive) }}</div>
                            <span
                                class="grid h-8 w-8 place-items-center rounded-full bg-indigo-50 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white">
                                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                    <a href="{{ route('admin.finance.index') }}"
                        class="group flex items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-100 hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-orange-50 text-orange-500">
                                <i class="fa-solid fa-wallet" aria-hidden="true"></i>
                            </span>
                            <div>
                                <div class="text-sm text-slate-950">School Finance</div>
                                <div class="mt-1 text-xs font-semibold text-slate-500">Student payment detail</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-lg text-slate-950">{{ $outstandingDisplay }}</div>
                            <span
                                class="grid h-8 w-8 place-items-center rounded-full bg-indigo-50 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white">
                                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                </div>

                <div
                    class="mt-8 flex flex-col gap-4 rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50 via-violet-50 to-white p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="grid h-12 w-12 place-items-center rounded-full bg-white text-indigo-600 shadow-sm">
                            <i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i>
                        </span>
                        <div>
                            <div class="text-sm text-slate-950">smarter decisions</div>
                            <div class="mt-1 text-xs font-semibold text-slate-500">Explore detailed analytics
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-xs text-white shadow-sm transition hover:bg-indigo-700">
                        View full analytics
                    </a>
                </div>
            </article>
        </section>
    </div>

    <script id="admin-dashboard-data" type="application/json">@json($dashboardChartData)</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
