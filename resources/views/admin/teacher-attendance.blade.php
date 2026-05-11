@extends('layout.admin.navbar')

@section('page')
    @php
        $teacherTotal = max(0, (int) ($stats['teachers'] ?? 0));

        $teacherAttendanceCards = [
            [
                'label' => 'Teachers',
                'activeLabel' => 'Total',
                'active' => $teacherTotal,
                'total' => $teacherTotal,
                'icon' => 'teachers',
                'tone' =>
                    'from-indigo-100 to-white text-indigo-600 dark:from-indigo-500/20 dark:to-slate-900 dark:text-indigo-300',
            ],
            [
                'label' => 'Checked',
                'activeLabel' => 'Done',
                'active' => (int) ($stats['checked'] ?? 0),
                'total' => $teacherTotal,
                'icon' => 'checked',
                'tone' => 'from-sky-100 to-white text-sky-600 dark:from-sky-500/20 dark:to-slate-900 dark:text-sky-300',
            ],
            [
                'label' => 'Present',
                'activeLabel' => 'Present',
                'active' => (int) ($stats['present'] ?? 0),
                'total' => $teacherTotal,
                'icon' => 'present',
                'tone' =>
                    'from-emerald-100 to-white text-emerald-600 dark:from-emerald-500/20 dark:to-slate-900 dark:text-emerald-300',
            ],
            [
                'label' => 'Absent',
                'activeLabel' => 'Absent',
                'active' => (int) ($stats['absent'] ?? 0),
                'total' => $teacherTotal,
                'icon' => 'absent',
                'tone' =>
                    'from-rose-100 to-white text-rose-600 dark:from-rose-500/20 dark:to-slate-900 dark:text-rose-300',
            ],
            [
                'label' => 'Late',
                'activeLabel' => 'Late',
                'active' => (int) ($stats['late'] ?? 0),
                'total' => $teacherTotal,
                'icon' => 'late',
                'tone' =>
                    'from-amber-100 to-white text-amber-600 dark:from-amber-500/20 dark:to-slate-900 dark:text-amber-300',
            ],
            [
                'label' => 'Not Marked',
                'activeLabel' => 'Waiting',
                'active' => (int) ($stats['not_marked'] ?? 0),
                'total' => $teacherTotal,
                'icon' => 'not_marked',
                'tone' =>
                    'from-cyan-100 to-white text-cyan-600 dark:from-cyan-500/20 dark:to-slate-900 dark:text-cyan-300',
            ],
            [
                'label' => 'Law Requests',
                'activeLabel' => 'Requests',
                'active' => (int) ($stats['law_requests'] ?? 0),
                'total' => $teacherTotal,
                'icon' => 'law',
                'tone' =>
                    'from-violet-100 to-white text-violet-600 dark:from-violet-500/20 dark:to-slate-900 dark:text-violet-300',
            ],
        ];

        $panelClass =
            'attendence-reveal attendence-float rounded-[28px] border border-slate-200/80 bg-white/95 p-5 shadow-[0_24px_55px_-42px_rgba(15,23,42,0.75)] ring-1 ring-slate-200/70 dark:border-slate-700/80 dark:bg-slate-900/95 dark:ring-slate-700/80 dark:shadow-[0_24px_70px_-42px_rgba(0,0,0,0.9)]';

        $inputClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20 dark:disabled:bg-slate-800/60 dark:disabled:text-slate-500';

        $labelTitleClass = 'text-xl font-bold text-slate-950 dark:text-white';

        $tableHeadClass =
            'admin-table-head sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400';

        $tableBodyClass = 'divide-y divide-slate-100 bg-white dark:divide-slate-700 dark:bg-slate-900';
    @endphp

    <div
        class="attendence-stage teacher-attendance-page mx-auto max-w-[1500px] space-y-6 pb-8 text-slate-900 dark:text-slate-100">
        <x-admin.page-header reveal-class="attendence-reveal" delay="1" icon="attendance" title="Teacher Attendance Check"
            subtitle="Admin check and update teacher attendance records by date.">
            <x-slot:actions>
                <a href="{{ route('admin.attendance.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    Back To Student Attendance Monitor
                </a>
            </x-slot:actions>
        </x-admin.page-header>

        {{-- STAT CARDS --}}
        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-7">
            @foreach ($teacherAttendanceCards as $index => $card)
                @php
                    $cardTotal = max(1, (int) ($card['total'] ?? 0));
                    $cardActive = max(0, (int) ($card['active'] ?? 0));
                    $progress = (int) round(($cardActive / $cardTotal) * 100);
                @endphp

                <div class="teacher-attendance-stat-card attendence-reveal attendence-float min-h-[132px] rounded-[26px] border border-slate-200/80 bg-white/95 p-5 shadow-[0_24px_55px_-42px_rgba(15,23,42,0.75)] ring-1 ring-slate-200/70 backdrop-blur dark:border-slate-700/80 dark:bg-slate-900/95 dark:ring-slate-700/80"
                    style="--sd: {{ $index + 2 }};">
                    <div class="flex items-start justify-between gap-4">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br {{ $card['tone'] }}">
                            @switch($card['icon'])
                                @case('teachers')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M6 21v-2a6 6 0 0 1 12 0v2" />
                                        <path d="M9 11h6" />
                                    </svg>
                                @break

                                @case('checked')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5" />
                                    </svg>
                                @break

                                @case('present')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-8 0v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                @break

                                @case('absent')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                @break

                                @case('late')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 7v5l3 2" />
                                    </svg>
                                @break

                                @case('not_marked')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 17h.01" />
                                        <path d="M12 13a3 3 0 1 0-3-3" />
                                        <circle cx="12" cy="12" r="9" />
                                    </svg>
                                @break

                                @default
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                        <path d="M14 2v6h6" />
                                        <path d="M8 13h8" />
                                        <path d="M8 17h5" />
                                    </svg>
                            @endswitch
                        </span>

                        <span class="text-slate-300 dark:text-slate-600" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                            </svg>
                        </span>
                    </div>

                    <div
                        class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950 dark:text-white">
                        <span>{{ number_format($cardActive) }}</span>
                        <span class="pb-0.5 text-base font-extrabold text-slate-300 dark:text-slate-600">
                            / {{ number_format((int) ($card['total'] ?? 0)) }}
                        </span>
                    </div>

                    <div class="mt-1 text-sm font-bold text-slate-600 dark:text-slate-300">
                        {{ $card['label'] }}
                    </div>

                    <div class="mt-1 text-[11px] font-semibold text-slate-400 dark:text-slate-500">
                        {{ $card['activeLabel'] }}: {{ number_format($cardActive) }}
                    </div>

                    <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                        <span class="block h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400"
                            style="width: {{ min(100, max(0, $progress)) }}%"></span>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <div class="js-inline-flash attendence-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="js-inline-flash attendence-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300"
                style="--sd: 2;">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('error'))
            <div class="js-inline-flash attendence-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="js-inline-flash attendence-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300"
                style="--sd: 2;">
                {{ $errors->first() }}
            </div>
        @endif

        @if (!$hasTeacherAttendanceTable)
            <div class="attendence-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300"
                style="--sd: 2;">
                Teacher attendance table is missing. Run <code>php artisan migrate</code> to enable this page.
            </div>
        @endif

        @if (($lawRequestSummary['pending'] ?? 0) > 0)
            <div class="attendence-reveal rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700 dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-300"
                style="--sd: 2;">
                Law requests found for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}:
                {{ number_format($lawRequestSummary['total'] ?? 0) }} teacher(s)
                (Approved: {{ number_format($lawRequestSummary['approved'] ?? 0) }},
                Pending: {{ number_format($lawRequestSummary['pending'] ?? 0) }}).
                Attendance will auto-save as <strong>Excused</strong> only for <strong>Approved</strong> requests.
            </div>
        @endif

        {{-- MAIN TABLE CARD --}}
        <section class="{{ $panelClass }}" style="--sd: 3;" x-data="{ filterOpen: false }">
            <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-950 dark:text-white">Teacher Attendance</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Daily check status and teacher law requests
                    </p>
                </div>

                <button type="button" @click="filterOpen = true"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-filter text-xs"></i>
                    Filters
                </button>
            </div>

            {{-- ACTIVE FILTERS --}}
            <div class="mb-5 flex flex-wrap items-center gap-2 text-xs font-semibold">
                <span
                    class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-indigo-700 dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-300">
                    Date: {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                </span>

                @if (($status ?? 'all') !== 'all')
                    <span
                        class="rounded-full border border-violet-200 bg-violet-50 px-3 py-1.5 text-violet-700 dark:border-violet-400/20 dark:bg-violet-500/15 dark:text-violet-300">
                        Status: {{ $statusLabels[$status] ?? ucfirst((string) $status) }}
                    </span>
                @endif

                @if (($search ?? '') !== '')
                    <span
                        class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        Search: {{ $search }}
                    </span>
                @endif
            </div>

            {{-- FILTER OVERLAY --}}
            <div x-show="filterOpen" x-cloak x-transition.opacity
                class="fixed inset-0 z-[80] bg-slate-900/50 backdrop-blur-sm" @click="filterOpen = false"></div>

            {{-- FILTER DRAWER --}}
            <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">

                <div class="flex h-full flex-col">
                    <div
                        class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                        <h3 class="text-3xl font-black text-slate-950 dark:text-white">Filters</h3>

                        <div class="flex items-center gap-4">
                            <a href="{{ route('admin.attendance.teachers.index') }}"
                                class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                Clear All
                            </a>

                            <button type="button" @click="filterOpen = false"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                aria-label="Close filters">
                                &times;
                            </button>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.attendance.teachers.index') }}"
                        class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">

                        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                            <section class="space-y-2">
                                <h4 class="{{ $labelTitleClass }}">Date</h4>
                                <input type="date" name="date" value="{{ $date }}"
                                    class="{{ $inputClass }}">
                            </section>

                            <section class="space-y-2">
                                <h4 class="{{ $labelTitleClass }}">Status</h4>
                                <select name="status" class="{{ $inputClass }}">
                                    @foreach ($statusLabels as $statusKey => $statusLabel)
                                        <option value="{{ $statusKey }}"
                                            {{ ($status ?? 'all') === $statusKey ? 'selected' : '' }}>
                                            {{ $statusLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </section>

                            <section class="space-y-2">
                                <h4 class="{{ $labelTitleClass }}">Search</h4>
                                <input type="text" name="q" value="{{ $search }}"
                                    placeholder="Search teacher name or email" class="{{ $inputClass }}">
                            </section>
                        </div>

                        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500">
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </aside>

            <form method="POST" action="{{ route('admin.attendance.teachers.store') }}"
                class="js-admin-teacher-attendance-form mt-5 space-y-4">
                @csrf

                <input type="hidden" name="attendance_date" value="{{ $date }}">

                @if (!($hasUnlockedTeachers ?? true) && ($teachers ?? collect())->isNotEmpty())
                    <div
                        class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300">
                        Attendance saved successfully for this date. Status cannot be changed again.
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">
                        Teacher rows: {{ number_format(($teachers ?? collect())->count()) }}
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" data-set-all-status="present"
                            {{ !($hasUnlockedTeachers ?? true) ? 'disabled' : '' }}
                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300 dark:hover:bg-emerald-500/25">
                            Set All Present
                        </button>

                        <button type="button" data-set-all-status="absent"
                            {{ !($hasUnlockedTeachers ?? true) ? 'disabled' : '' }}
                            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-rose-500/30 dark:bg-rose-500/15 dark:text-rose-300 dark:hover:bg-rose-500/25">
                            Set All Absent
                        </button>

                        <button type="submit"
                            {{ !$hasTeacherAttendanceTable || !($hasUnlockedTeachers ?? true) ? 'disabled' : '' }}
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            Save Teacher Attendance
                        </button>
                    </div>
                </div>

                <div
                    class="admin-teacher-attendance-table-wrap overflow-hidden rounded-2xl border border-slate-200 bg-slate-50/60 dark:border-slate-700 dark:bg-slate-800/60">
                    <div class="admin-teacher-attendance-table-scroller max-h-[620px] overflow-auto">
                        <table class="admin-teacher-attendance-table admin-table w-full min-w-[1180px] text-left text-sm">
                            <thead class="{{ $tableHeadClass }}">
                                <tr>
                                    <th class="admin-teacher-col-teacher px-3 py-3 font-semibold">Teacher</th>
                                    <th class="admin-teacher-col-law px-3 py-3 font-semibold">Law Request</th>
                                    <th class="admin-teacher-col-current px-3 py-3 font-semibold">Current Status</th>
                                    <th class="admin-teacher-col-check px-3 py-3 font-semibold">Check Status</th>
                                    <th class="admin-teacher-col-remark px-3 py-3 font-semibold">Remark</th>
                                    <th class="admin-teacher-col-checked px-3 py-3 font-semibold">Checked At</th>
                                </tr>
                            </thead>

                            <tbody class="{{ $tableBodyClass }}">
                                @forelse ($teachers as $teacher)
                                    @php
                                        $record = $attendanceByTeacher->get((int) $teacher->id);
                                        $currentStatus = strtolower((string) ($record?->status ?? ''));
                                        $isLocked = $record !== null;

                                        $lawRequest = ($lawRequestsByTeacher ?? collect())->get((int) $teacher->id);
                                        $lawStatus = strtolower((string) ($lawRequest?->status ?? ''));

                                        $isLawApproved = $lawStatus === 'approved';
                                        $isLawPending = $lawStatus === 'pending';
                                        $isLawRejected = $lawStatus === 'rejected';

                                        $hasActiveLawRequest =
                                            $lawRequest && ($isLawPending || $isLawApproved || $isLawRejected);

                                        $lawTypeLabel = $lawRequest
                                            ? ucfirst(
                                                str_replace('_', ' ', (string) ($lawRequest->law_type ?? 'request')),
                                            )
                                            : '';

                                        $lawStatusClass = match ($lawStatus) {
                                            'approved'
                                                => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300',
                                            'pending'
                                                => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300',
                                            'rejected'
                                                => 'border-red-200 bg-red-50 text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300',
                                            default
                                                => 'border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                        };

                                        $lawRequestSubject = trim((string) ($lawRequest->subject ?? ''));
                                        $lawRequestSubjectTime = trim((string) ($lawRequest->subject_time ?? ''));

                                        if ($lawRequestSubjectTime === '' && str_contains($lawRequestSubject, '|')) {
                                            [$parsedSubject, $parsedTime] = array_pad(
                                                array_map('trim', explode('|', $lawRequestSubject, 2)),
                                                2,
                                                '',
                                            );

                                            $lawRequestSubject = $parsedSubject;
                                            $lawRequestSubjectTime = $parsedTime;
                                        }

                                        $defaultLawRemark = '';

                                        if ($hasActiveLawRequest) {
                                            $defaultLawRemark = 'Law Request (' . $lawTypeLabel . ')';

                                            if ($lawRequestSubject !== '') {
                                                $defaultLawRemark .= ': ' . $lawRequestSubject;
                                            }

                                            if ($lawRequestSubjectTime !== '') {
                                                $defaultLawRemark .= ' @ ' . $lawRequestSubjectTime;
                                            }

                                            $defaultLawRemark = mb_substr($defaultLawRemark, 0, 170);
                                        }

                                        $selectedStatus = old(
                                            'attendance.' . $teacher->id . '.status',
                                            $currentStatus !== ''
                                                ? $currentStatus
                                                : ($isLawApproved
                                                    ? 'excused'
                                                    : 'present'),
                                        );

                                        $remarkValue = old(
                                            'attendance.' . $teacher->id . '.remark',
                                            (string) ($record?->remark ?? $defaultLawRemark),
                                        );

                                        $statusClass = match ($currentStatus) {
                                            'present'
                                                => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300',
                                            'absent'
                                                => 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/15 dark:text-rose-300',
                                            'late'
                                                => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300',
                                            'excused'
                                                => 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/15 dark:text-sky-300',
                                            default
                                                => 'border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                        };

                                        $checkedAt = $record?->checked_at ?? $record?->updated_at;
                                    @endphp

                                    <tr class="js-admin-teacher-row align-top transition odd:bg-slate-50/30 hover:bg-slate-50/80 dark:odd:bg-slate-800/30 dark:hover:bg-slate-800/70"
                                        data-teacher-name="{{ $teacher->name }}" data-teacher-id="{{ $teacher->id }}"
                                        data-law-request-id="{{ (int) ($lawRequest->id ?? 0) }}">

                                        <td class="admin-teacher-cell admin-teacher-col-teacher px-3 py-4">
                                            <div class="font-semibold text-slate-800 dark:text-slate-100">
                                                {{ $teacher->name }}
                                            </div>

                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $teacher->email }}
                                            </div>

                                            <div class="text-xs text-slate-400 dark:text-slate-500">
                                                ID #{{ str_pad((string) ($teacher->id ?? 0), 7, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </td>

                                        <td class="admin-teacher-cell admin-teacher-col-law px-3 py-4">
                                            @if ($hasActiveLawRequest)
                                                <div class="space-y-1.5" data-law-request-content>
                                                    <div class="flex flex-wrap items-center gap-1.5">
                                                        <span data-law-request-badge
                                                            class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-semibold {{ $lawStatusClass }}">
                                                            {{ ucfirst($lawStatus) }}
                                                        </span>

                                                        <span
                                                            class="inline-flex items-center whitespace-nowrap rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-300">
                                                            {{ $lawTypeLabel }}
                                                        </span>
                                                    </div>

                                                    @if ($lawRequestSubject !== '')
                                                        <div
                                                            class="break-words text-sm font-semibold text-slate-700 dark:text-slate-200">
                                                            {{ $lawRequestSubject }}
                                                        </div>
                                                    @endif

                                                    @if ($lawRequestSubjectTime !== '')
                                                        <div
                                                            class="break-words text-xs font-semibold text-indigo-600 dark:text-indigo-300">
                                                            {{ $lawRequestSubjectTime }}
                                                        </div>
                                                    @endif

                                                    @if (!empty($lawRequest?->reason))
                                                        <div
                                                            class="line-clamp-3 max-w-sm text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                                                            {{ $lawRequest->reason }}
                                                        </div>
                                                    @endif

                                                    @if ($isLawPending)
                                                        <div class="flex flex-wrap gap-1.5 pt-1" data-law-request-actions>
                                                            <button type="submit"
                                                                formaction="{{ route('admin.attendance.teachers.law-requests.approve', $lawRequest) }}"
                                                                formmethod="POST" formnovalidate data-law-action="approve"
                                                                class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300 dark:hover:bg-emerald-500/25">
                                                                Approve
                                                            </button>

                                                            <button type="submit"
                                                                formaction="{{ route('admin.attendance.teachers.law-requests.reject', $lawRequest) }}"
                                                                formmethod="POST" formnovalidate data-law-action="reject"
                                                                class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 transition hover:bg-red-100 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300 dark:hover:bg-red-500/25">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    @elseif ($isLawRejected)
                                                        <div class="pt-1 text-[11px] font-semibold text-red-700 dark:text-red-300"
                                                            data-law-request-state>
                                                            Cancelled and marked absent
                                                        </div>
                                                    @elseif ($isLawApproved)
                                                        <div class="pt-1 text-[11px] font-semibold text-emerald-700 dark:text-emerald-300"
                                                            data-law-request-state>
                                                            Approved for attendance
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 dark:text-slate-500">No request</span>
                                            @endif
                                        </td>

                                        <td class="admin-teacher-cell admin-teacher-col-current px-3 py-4">
                                            @if ($currentStatus !== '' && isset($statusLabels[$currentStatus]))
                                                <span data-attendance-indicator
                                                    class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                    {{ $statusLabels[$currentStatus] }}
                                                </span>
                                            @else
                                                <span data-attendance-indicator
                                                    class="inline-flex items-center whitespace-nowrap rounded-full border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300">
                                                    Not Checked Yet
                                                </span>
                                            @endif
                                        </td>

                                        <td class="admin-teacher-cell admin-teacher-col-check px-3 py-4">
                                            <div class="space-y-1.5">
                                                <select name="attendance[{{ $teacher->id }}][status]"
                                                    {{ $isLocked ? 'disabled' : '' }}
                                                    class="js-admin-teacher-status sr-only" tabindex="-1"
                                                    aria-hidden="true">
                                                    @foreach ($statusLabels as $statusKey => $statusLabel)
                                                        @continue($statusKey === 'all')
                                                        <option value="{{ $statusKey }}"
                                                            {{ $selectedStatus === $statusKey ? 'selected' : '' }}>
                                                            {{ $statusLabel }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <div class="admin-teacher-status-grid flex flex-wrap gap-1.5">
                                                    @foreach ($statusLabels as $statusKey => $statusLabel)
                                                        @continue($statusKey === 'all')

                                                        @php
                                                            $isActiveStatus = $selectedStatus === $statusKey;
                                                        @endphp

                                                        <button type="button" data-status-option="{{ $statusKey }}"
                                                            {{ $isLocked ? 'disabled' : '' }}
                                                            aria-pressed="{{ $isActiveStatus ? 'true' : 'false' }}"
                                                            class="js-admin-status-box inline-flex min-w-[76px] items-center justify-center rounded-full border px-2.5 py-2 text-xs font-semibold transition disabled:cursor-not-allowed disabled:opacity-50
                                                            {{ $isActiveStatus
                                                                ? 'border-indigo-300 bg-indigo-50 text-indigo-700 shadow-sm dark:border-indigo-400/30 dark:bg-indigo-500/15 dark:text-indigo-300'
                                                                : 'border-slate-200 bg-white text-slate-600 hover:border-indigo-200 hover:bg-indigo-50/50 hover:text-indigo-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-indigo-400/30 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-300' }}">
                                                            {{ $statusLabel }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>

                                        <td class="admin-teacher-cell admin-teacher-col-remark px-3 py-4">
                                            <input type="text" name="attendance[{{ $teacher->id }}][remark]"
                                                value="{{ $remarkValue }}" maxlength="255"
                                                {{ $isLocked ? 'disabled' : '' }} class="{{ $inputClass }}"
                                                placeholder="Optional note">

                                            @if ($isLocked)
                                                <div
                                                    class="mt-1 text-[11px] font-semibold text-emerald-700 dark:text-emerald-300">
                                                    Saved and locked
                                                </div>
                                            @endif
                                        </td>

                                        <td
                                            class="admin-teacher-cell admin-teacher-col-checked px-3 py-4 text-slate-600 dark:text-slate-300">
                                            @if ($checkedAt)
                                                <div class="font-medium text-slate-700 dark:text-slate-200">
                                                    {{ \Carbon\Carbon::parse($checkedAt)->format('M d, Y h:i A') }}
                                                </div>

                                                <div class="text-xs text-slate-400 dark:text-slate-500">
                                                    {{ \Carbon\Carbon::parse($checkedAt)->diffForHumans() }}
                                                </div>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="px-3 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                            No teachers found for selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </section>
    </div>

    @php
        $teacherAttendancePageData = [
            'validationErrors' => $errors->all(),
            'flash' => [
                'success' => session('success'),
                'warning' => session('warning'),
                'error' => session('error'),
            ],
        ];
    @endphp

    <script id="admin-teacher-attendance-data" type="application/json">{!! json_encode($teacherAttendancePageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/admin/teacher-attendance.js'])
@endsection
