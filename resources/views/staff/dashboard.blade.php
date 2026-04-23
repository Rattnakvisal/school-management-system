@extends('layout.staff.navbar')

@section('page')
    @php
        $todayDate = now();
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst($todayKey);
        $staffName = trim((string) auth()->user()->name);
        $staffFirstName = explode(' ', $staffName)[0] ?? $staffName;
        $todaySchedule = collect($todaySchedule ?? [])->take(4)->values();
        $latestContactMessages = collect($latestContactMessages ?? [])->take(4)->values();
        $recentAccounts = collect($recentAccounts ?? [])->take(5)->values();
        $workloadPercent = (int) ($workloadPercent ?? 0);
        $focusCount =
            (int) ($stats['messagesUnread'] ?? 0) +
            (int) ($stats['studentsNeedStudyTime'] ?? 0) +
            (int) ($stats['pendingTeacherRequests'] ?? 0) +
            (int) ($stats['teachersNotMarkedToday'] ?? 0);

        $heroLine =
            $focusCount > 0
                ? "There are {$focusCount} operational item" . ($focusCount === 1 ? '' : 's') . " waiting for review."
                : "The staff workspace is clear for {$todayLabel}.";

        $healthLine =
            $workloadPercent >= 80
                ? 'School operations look healthy today.'
                : ($workloadPercent >= 50
                    ? 'A few records still need attention.'
                    : 'Prioritize records, attendance, and messages today.');

        $dashboardData = [
            'workloadPercent' => $workloadPercent,
            'calendar' => [
                'year' => (int) $todayDate->year,
                'month' => (int) $todayDate->month,
                'day' => (int) $todayDate->day,
            ],
        ];

        $panelClass =
            'rounded-[28px] border border-slate-200/80 bg-white/90 shadow-[0_18px_50px_-30px_rgba(15,23,42,0.22)] backdrop-blur-sm';
        $panelPadding = 'p-5 sm:p-6';
        $titleClass = 'text-lg font-extrabold tracking-[-0.03em] text-slate-900';
        $mutedLinkClass = 'text-sm font-semibold text-slate-400 transition hover:text-slate-600';
    @endphp

    <div class="dashboard-stage space-y-6">
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px] xl:items-start">
            <div class="space-y-6">
                <section
                    class="dash-reveal relative overflow-hidden rounded-[32px] border border-slate-200/80 bg-gradient-to-br from-white via-slate-50 to-indigo-50/60 px-6 py-7 shadow-[0_20px_60px_-30px_rgba(15,23,42,0.24)] sm:px-8 sm:py-8"
                    style="--d: 1;">
                    <div
                        class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.12),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(251,191,36,0.10),transparent_28%)]">
                    </div>

                    <div class="relative grid items-center gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(280px,360px)]">
                        <div class="space-y-5">
                            <div class="space-y-3">
                                <span
                                    class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-indigo-700">
                                    Staff dashboard
                                </span>

                                <h1
                                    class="text-[clamp(2rem,4vw,3.25rem)] font-black leading-[0.95] tracking-[-0.06em] text-slate-900">
                                    Welcome back,
                                    <span
                                        class="bg-gradient-to-r from-indigo-700 to-blue-500 bg-clip-text text-transparent">
                                        {{ $staffFirstName }}
                                    </span>
                                </h1>

                                <p class="max-w-2xl text-[1rem] leading-7 text-slate-600 sm:text-[1.05rem]">
                                    {{ $heroLine }}
                                </p>

                                <p class="max-w-2xl text-[1rem] leading-7 text-slate-500 sm:text-[1.05rem]">
                                    Operational health is
                                    <strong class="staff-animate-number font-extrabold text-rose-500"
                                        data-value="{{ $workloadPercent }}" data-suffix="%">0%</strong>.
                                    {{ $healthLine }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <span
                                    class="teacher-stat-pill inline-flex items-center rounded-full bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700 ring-1 ring-blue-100">
                                    Students <span class="staff-animate-number ml-1"
                                        data-value="{{ (int) ($stats['students'] ?? 0) }}">0</span>
                                </span>
                                <span
                                    class="teacher-stat-pill inline-flex items-center rounded-full bg-amber-50 px-4 py-2 text-sm font-bold text-amber-700 ring-1 ring-amber-100">
                                    Teachers <span class="staff-animate-number ml-1"
                                        data-value="{{ (int) ($stats['teachers'] ?? 0) }}">0</span>
                                </span>
                                <span
                                    class="teacher-stat-pill inline-flex items-center rounded-full bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 ring-1 ring-rose-100">
                                    Messages <span class="staff-animate-number ml-1"
                                        data-value="{{ (int) ($stats['messagesUnread'] ?? 0) }}">0</span>
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <a href="{{ route('admin.contacts.index') }}"
                                    class="dash-hover inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 px-5 py-3 text-sm font-bold text-white shadow-[0_18px_30px_-16px_rgba(59,130,246,0.5)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_34px_-16px_rgba(59,130,246,0.55)]">
                                    Open message inbox
                                </a>

                                <span class="text-sm font-semibold text-slate-400">
                                    {{ $todayLabel }} | {{ $todayDate->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="relative flex items-center justify-center">
                            <div class="absolute inset-x-8 inset-y-8 rounded-full bg-indigo-100/60 blur-3xl"></div>
                            <img src="{{ asset('images/9865735.png') }}" alt="Staff dashboard illustration"
                                class="teacher-hero-art relative block h-auto w-full max-w-[28rem] rounded-[30px] object-cover drop-shadow-[0_18px_40px_rgba(59,130,246,0.20)]">
                        </div>
                    </div>
                </section>

                <div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
                    <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 2;">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <div>
                                <h2 class="{{ $titleClass }}">Work Queue</h2>
                                <p class="mt-1 text-sm text-slate-400">Actionable items for today</p>
                            </div>
                            <span
                                class="rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                {{ number_format($focusCount) }} open
                            </span>
                        </div>

                        @php
                            $queueItems = [
                                [
                                    'label' => 'Unread messages',
                                    'value' => (int) ($stats['messagesUnread'] ?? 0),
                                    'route' => route('admin.contacts.index'),
                                    'color' => 'from-blue-600 to-indigo-600',
                                    'note' => 'Contact inbox',
                                    'action' => 'Open inbox',
                                ],
                                [
                                    'label' => 'Students without study time',
                                    'value' => (int) ($stats['studentsNeedStudyTime'] ?? 0),
                                    'route' => route('admin.student-study.index'),
                                    'color' => 'from-amber-400 to-orange-500',
                                    'note' => 'Study mapping',
                                    'action' => 'Assign times',
                                ],
                                [
                                    'label' => 'Teacher law requests today',
                                    'value' => (int) ($stats['pendingTeacherRequests'] ?? 0),
                                    'route' => route('admin.attendance.teachers.index', ['date' => $todayDate->toDateString()]),
                                    'color' => 'from-rose-400 to-pink-500',
                                    'note' => 'Teacher attendance',
                                    'action' => 'Review',
                                ],
                                [
                                    'label' => 'Teachers not marked today',
                                    'value' => (int) ($stats['teachersNotMarkedToday'] ?? 0),
                                    'route' => route('admin.attendance.teachers.index', ['date' => $todayDate->toDateString()]),
                                    'color' => 'from-cyan-400 to-sky-500',
                                    'note' => 'Daily staff check',
                                    'action' => 'Mark attendance',
                                ],
                            ];
                            $maxQueueValue = max(1, collect($queueItems)->max('value'));
                        @endphp

                        <div class="space-y-4">
                            @foreach ($queueItems as $item)
                                @php
                                    $itemValue = max(0, (int) $item['value']);
                                    $width = $itemValue > 0 ? min(100, max(12, round(($itemValue / $maxQueueValue) * 100))) : 0;
                                @endphp
                                <a href="{{ $item['route'] }}"
                                    class="dash-hover grid items-center gap-4 rounded-2xl border border-slate-100 bg-slate-50/70 p-4 transition hover:border-slate-200 hover:bg-white sm:grid-cols-[minmax(0,1.2fr)_minmax(120px,0.9fr)_auto]">
                                    <div class="min-w-0">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <div class="truncate text-[15px] font-bold text-slate-800">
                                                {{ $item['label'] }}
                                            </div>
                                            @if ($itemValue === 0)
                                                <span
                                                    class="shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-emerald-700 ring-1 ring-emerald-100">
                                                    Clear
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-0.5 truncate text-xs text-slate-400">{{ $item['note'] }}</div>
                                    </div>

                                    <div class="min-w-0">
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                            <span
                                                class="staff-progress-fill block h-full rounded-full bg-gradient-to-r {{ $item['color'] }}"
                                                data-width="{{ $width }}" style="width: {{ $width }}%"></span>
                                        </div>
                                        <div class="mt-2 text-xs font-semibold text-slate-400">
                                            {{ $item['action'] }}
                                        </div>
                                    </div>

                                    <div class="staff-animate-number min-w-8 text-right text-sm font-extrabold text-slate-800"
                                        data-value="{{ $itemValue }}">
                                        0
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>

                    <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 3;">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <div>
                                <h2 class="{{ $titleClass }}">Operational Health</h2>
                                <p class="mt-1 text-sm text-slate-400">Active records across the school</p>
                            </div>

                            <span
                                class="rounded-full bg-amber-50 px-4 py-1.5 text-xs font-bold text-amber-700 ring-1 ring-amber-100">
                                Today
                            </span>
                        </div>

                        <div class="teacher-chart-shell relative h-[230px]">
                            <canvas id="staffWorkloadChart"></canvas>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-4 border-t border-slate-100 pt-5">
                            <div class="dash-hover rounded-2xl bg-slate-50 p-4">
                                <strong class="staff-animate-number block text-lg font-extrabold text-slate-900"
                                    data-value="{{ (int) ($stats['todayStudentAttendance'] ?? 0) }}">0</strong>
                                <span class="text-xs font-medium text-slate-400">student checks today</span>
                            </div>

                            <div class="dash-hover rounded-2xl bg-slate-50 p-4">
                                <strong class="staff-animate-number block text-lg font-extrabold text-slate-900"
                                    data-value="{{ (int) ($stats['todayTeacherAttendance'] ?? 0) }}">0</strong>
                                <span class="text-xs font-medium text-slate-400">teacher checks today</span>
                            </div>
                        </div>
                    </section>
                </div>

                <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 4;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="{{ $titleClass }}">Recent Accounts</h2>
                            <p class="mt-1 text-sm text-slate-400">Latest staff, admin, and teacher records</p>
                        </div>
                        <a href="{{ route('admin.admin-staff.index') }}" class="{{ $mutedLinkClass }}">Manage</a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($recentAccounts as $account)
                            <article
                                class="dash-hover grid items-center gap-4 rounded-3xl border border-slate-100 bg-slate-50/70 p-4 transition hover:border-slate-200 hover:bg-white lg:grid-cols-[minmax(0,1.6fr)_minmax(120px,0.7fr)_auto]">
                                <div class="flex min-w-0 items-center gap-4">
                                    <img src="{{ $account->avatar_url }}" alt="{{ $account->name }}"
                                        class="h-11 w-11 shrink-0 rounded-full object-cover ring-2 ring-white shadow-sm">

                                    <div class="min-w-0">
                                        <div class="truncate text-[15px] font-extrabold text-slate-800">
                                            {{ $account->name }}
                                        </div>
                                        <div class="truncate text-sm text-slate-400">
                                            {{ $account->email }}
                                        </div>
                                    </div>
                                </div>

                                <div class="text-sm font-semibold capitalize text-slate-600">
                                    {{ $account->role }}
                                </div>

                                <div class="text-sm text-slate-400">
                                    {{ $account->created_at?->diffForHumans() }}
                                </div>
                            </article>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-400">
                                No recent accounts are available yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-5">
                <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 2;">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-slate-400">
                                Staff panel
                            </div>
                            <div class="mt-1 text-base font-extrabold text-slate-900">{{ $staffName }}</div>
                            <div class="mt-1 text-sm text-slate-400">Ready to coordinate school operations</div>
                        </div>

                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ $staffName }}"
                            onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                            class="h-12 w-12 rounded-full object-cover ring-2 ring-white shadow-md">
                    </div>
                </section>

                <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 3;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Calendar</div>
                            <h2 id="staff-dashboard-calendar-label" class="{{ $titleClass }}">
                                {{ $todayDate->format('F Y') }}
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

                    <div id="staff-dashboard-calendar" class="grid grid-cols-7 gap-1.5"></div>
                </section>

                <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 4;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="{{ $titleClass }}">Today Schedule</h2>
                            <p class="mt-1 text-sm text-slate-400">Class study time overview</p>
                        </div>

                        <a href="{{ route('admin.time-studies.index') }}" class="{{ $mutedLinkClass }}">View all</a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($todaySchedule as $index => $slot)
                            @php
                                $accentClass =
                                    $index % 2 === 0 ? 'from-orange-500 to-amber-400' : 'from-indigo-600 to-blue-500';
                            @endphp

                            <article
                                class="js-staff-slot dash-hover flex gap-4 rounded-2xl border border-slate-100 bg-slate-50/70 p-4 transition hover:border-slate-200 hover:bg-white"
                                data-start="{{ $slot['start_24'] ?? '' }}" data-end="{{ $slot['end_24'] ?? '' }}">
                                <span class="w-1 shrink-0 rounded-full bg-gradient-to-b {{ $accentClass }}"></span>

                                <div class="min-w-0 flex-1">
                                    <div class="text-[15px] font-extrabold text-slate-900">
                                        {{ $slot['class_name'] }}
                                    </div>
                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ $slot['day_label'] }} | {{ $slot['start'] }} - {{ $slot['end'] }}
                                    </div>
                                    <div class="mt-1 text-xs text-slate-400">{{ $slot['period'] }}</div>
                                </div>

                                <span
                                    class="js-staff-slot-status self-center whitespace-nowrap rounded-full bg-slate-100 px-3 py-1.5 text-[11px] font-extrabold text-slate-500">
                                    Scheduled
                                </span>
                            </article>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-400">
                                No class schedule items are available for today.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="dash-reveal {{ $panelClass }} {{ $panelPadding }}" style="--d: 5;">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="{{ $titleClass }}">Latest Messages</h2>
                            <p class="mt-1 text-sm text-slate-400">Recent contact inbox activity</p>
                        </div>

                        <a href="{{ route('admin.contacts.index') }}" class="{{ $mutedLinkClass }}">View all</a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($latestContactMessages as $message)
                            <a href="{{ route('admin.contacts.index') }}"
                                class="dash-hover flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-4 transition hover:border-slate-200 hover:bg-white">
                                <span
                                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        class="h-4 w-4">
                                        <path d="M4 4h16v12H6l-2 2V4Z" />
                                    </svg>
                                </span>

                                <div class="min-w-0">
                                    <div class="truncate text-sm font-bold text-slate-900">
                                        {{ $message->name ?: 'Unknown Sender' }}
                                    </div>
                                    <div class="mt-0.5 truncate text-xs text-slate-400">
                                        {{ $message->subject ?: 'No subject' }}
                                    </div>
                                </div>

                                <span class="ml-auto text-xl text-slate-300">&rsaquo;</span>
                            </a>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-400">
                                No contact messages yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script id="staff-dashboard-data" type="application/json">@json($dashboardData)</script>
    @vite(['resources/js/Staff/dashboard.js'])
@endsection
