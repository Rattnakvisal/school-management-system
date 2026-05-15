@extends('layout.admin.navbar')

@section('page')
    @php
        $studyStudentTotal = max(0, (int) ($stats['students'] ?? 0));

        $panelClass =
            'study-reveal study-float rounded-[28px] border border-slate-200/80 bg-white/95 p-5 shadow-[0_24px_55px_-42px_rgba(15,23,42,0.75)] ring-1 ring-slate-200/70 dark:border-slate-700/80 dark:bg-slate-900/95 dark:ring-slate-700/80 dark:shadow-[0_24px_70px_-42px_rgba(0,0,0,0.9)]';

        $inputClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20';

        $tableHeadClass =
            'admin-table-head sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400';

        $tableBodyClass = 'divide-y divide-slate-100 bg-white dark:divide-slate-700 dark:bg-slate-900';
    @endphp

    <div class="study-stage admin-management-page student-study-page mx-auto max-w-[1500px] space-y-6 pb-8 text-slate-900 dark:text-slate-100">
        <x-admin.page-header
            reveal-class="study-reveal"
            delay="1"
            icon="study"
            title="Student Study"
            subtitle="Students with selected class time, major subject, teacher, and created dates."
        />

        <section class="{{ $panelClass }}" style="--sd: 2;">
            <div x-data="{ filterOpen: false }"
                @open-filter-panel.window="filterOpen = true"
                class="space-y-4">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-black text-slate-950 dark:text-white">Study List</h2>

                    <button type="button"
                        @click="filterOpen = true"
                        class="inline-flex min-w-[150px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        <i class="fa-solid fa-filter text-xs"></i>
                        Filters
                    </button>
                </div>

                {{-- FILTER OVERLAY --}}
                <div x-show="filterOpen"
                    x-cloak
                    x-transition.opacity
                    class="fixed inset-0 z-[80] bg-slate-900/50 backdrop-blur-sm"
                    @click="filterOpen = false"></div>

                {{-- FILTER DRAWER --}}
                <aside x-show="filterOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">

                    <div class="flex h-full flex-col">
                        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                            <h3 class="text-3xl font-black text-slate-950 dark:text-white">Filters</h3>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.student-study.index') }}"
                                    class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                    Clear All
                                </a>

                                <button type="button"
                                    @click="filterOpen = false"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                    aria-label="Close filters">
                                    &times;
                                </button>
                            </div>
                        </div>

                        <form method="GET"
                            action="{{ route('admin.student-study.index') }}"
                            class="flex min-h-0 flex-1 flex-col"
                            @submit="filterOpen = false">

                            <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-950 dark:text-white">Search</h4>
                                    <input id="q"
                                        name="q"
                                        type="text"
                                        value="{{ $search }}"
                                        placeholder="Search student, email, class, room, subject, time, or teacher"
                                        class="{{ $inputClass }}">
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-950 dark:text-white">Class</h4>
                                    <select name="class_id" class="{{ $inputClass }}">
                                        <option value="all" {{ $classId === 'all' ? 'selected' : '' }}>All Classes</option>

                                        @foreach ($classes as $classOption)
                                            <option value="{{ $classOption->id }}" {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                                {{ $classOption->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-950 dark:text-white">Subject</h4>
                                    <select name="subject_id" class="{{ $inputClass }}">
                                        <option value="all" {{ $subjectId === 'all' ? 'selected' : '' }}>All Subjects</option>

                                        @foreach ($subjects as $subjectOption)
                                            <option value="{{ $subjectOption->id }}" {{ $subjectId === (string) $subjectOption->id ? 'selected' : '' }}>
                                                {{ $subjectOption->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-950 dark:text-white">Period</h4>
                                    <select name="period" class="{{ $inputClass }}">
                                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Periods</option>

                                        @foreach ($periodOptions as $periodKey => $periodLabel)
                                            <option value="{{ $periodKey }}" {{ $period === $periodKey ? 'selected' : '' }}>
                                                {{ $periodLabel }}
                                            </option>
                                        @endforeach
                                    </select>
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

                @php
                    $selectedClassLabel = null;

                    if ($classId !== 'all') {
                        $selectedClass = $classes->firstWhere('id', (int) $classId);
                        $selectedClassLabel = $selectedClass?->display_name;
                    }

                    $selectedSubjectLabel = null;

                    if ($subjectId !== 'all') {
                        $selectedSubject = $subjects->firstWhere('id', (int) $subjectId);
                        $selectedSubjectLabel = $selectedSubject?->name;
                    }
                @endphp

                {{-- ACTIVE FILTERS --}}
                <div class="flex flex-wrap items-center gap-2 rounded-2xl bg-indigo-50/70 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-indigo-500/10 dark:text-slate-300">
                    @if ($search !== '' || $selectedClassLabel || $selectedSubjectLabel || $period !== 'all')
                        <span class="text-indigo-700 dark:text-indigo-300">Active filters:</span>

                        @if ($search !== '')
                            <span class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                Search: {{ $search }}
                            </span>
                        @endif

                        @if ($selectedClassLabel)
                            <span class="rounded-full bg-white px-2.5 py-1 text-sky-700 ring-1 ring-sky-100 dark:bg-slate-800 dark:text-sky-300 dark:ring-sky-400/20">
                                Class: {{ $selectedClassLabel }}
                            </span>
                        @endif

                        @if ($selectedSubjectLabel)
                            <span class="rounded-full bg-white px-2.5 py-1 text-indigo-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-indigo-300 dark:ring-indigo-400/20">
                                Subject: {{ $selectedSubjectLabel }}
                            </span>
                        @endif

                        @if ($period !== 'all')
                            <span class="rounded-full bg-white px-2.5 py-1 text-violet-700 ring-1 ring-violet-100 dark:bg-slate-800 dark:text-violet-300 dark:ring-violet-400/20">
                                Period: {{ $periodOptions[$period] ?? ucfirst($period) }}
                            </span>
                        @endif

                        <a href="{{ route('admin.student-study.index') }}"
                            class="ml-auto rounded-full bg-white px-2.5 py-1 text-indigo-700 ring-1 ring-indigo-100 hover:bg-indigo-100 dark:bg-slate-800 dark:text-indigo-300 dark:ring-indigo-400/20 dark:hover:bg-slate-700">
                            Clear
                        </a>
                    @else
                        <span class="text-slate-400 dark:text-slate-500">Showing all records</span>
                    @endif
                </div>

                {{-- TABLE --}}
                <div class="min-w-0">
                    <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700">
                        <div class="student-study-table-scroller max-h-[700px] overflow-auto">
                            <table class="admin-table student-study-table w-full min-w-[1240px] text-left text-sm">
                                <thead class="{{ $tableHeadClass }}">
                                    <tr>
                                        <th class="w-[260px] whitespace-nowrap px-3 py-3 font-semibold">Student</th>
                                        <th class="w-[180px] whitespace-nowrap px-3 py-3 font-semibold">Class</th>
                                        <th class="w-[220px] whitespace-nowrap px-3 py-3 font-semibold">Selected Time</th>
                                        <th class="w-[220px] whitespace-nowrap px-3 py-3 font-semibold">Major Subject</th>
                                        <th class="w-[180px] whitespace-nowrap px-3 py-3 font-semibold">Subject Time</th>
                                        <th class="w-[220px] whitespace-nowrap px-3 py-3 font-semibold">Schedule</th>
                                        <th class="w-[220px] whitespace-nowrap px-3 py-3 font-semibold">Teacher</th>
                                        <th class="w-[180px] whitespace-nowrap px-3 py-3 font-semibold">Created</th>
                                    </tr>
                                </thead>

                                <tbody class="{{ $tableBodyClass }}">
                                    @forelse ($studies as $row)
                                        @php
                                            $classLabel = trim((string) ($row->class_name ?? ''));
                                            $section = trim((string) ($row->class_section ?? ''));

                                            if ($classLabel !== '' && $section !== '') {
                                                $classLabel .= ' - ' . $section;
                                            } elseif ($classLabel === '') {
                                                $classLabel = 'Unassigned';
                                            }

                                            $classRoom = trim((string) ($row->class_room ?? ''));

                                            $studentCreatedAt = $row->student_created_at
                                                ? \Carbon\Carbon::parse($row->student_created_at)
                                                : null;

                                            $teacherCreatedAt = $row->teacher_created_at
                                                ? \Carbon\Carbon::parse($row->teacher_created_at)
                                                : null;

                                            $dayLabels = [
                                                'all' => 'All Days',
                                                'monday' => 'Monday',
                                                'tuesday' => 'Tuesday',
                                                'wednesday' => 'Wednesday',
                                                'thursday' => 'Thursday',
                                                'friday' => 'Friday',
                                                'saturday' => 'Saturday',
                                                'sunday' => 'Sunday',
                                            ];

                                            $classPeriodKey = strtolower(trim((string) ($row->class_study_period ?? '')));
                                            $subjectPeriodKey = strtolower(trim((string) ($row->subject_study_period ?? '')));

                                            $classPeriodLabel =
                                                $classPeriodKey !== ''
                                                    ? $periodOptions[$classPeriodKey] ?? ucfirst($classPeriodKey)
                                                    : null;

                                            $subjectPeriodLabel =
                                                $subjectPeriodKey !== ''
                                                    ? $periodOptions[$subjectPeriodKey] ?? ucfirst($subjectPeriodKey)
                                                    : null;

                                            $classDayKey = strtolower(trim((string) ($row->class_study_day ?? '')));
                                            $subjectDayKey = strtolower(trim((string) ($row->subject_study_day ?? '')));

                                            $classDayLabel =
                                                $classDayKey !== ''
                                                    ? $dayLabels[$classDayKey] ?? ucfirst($classDayKey)
                                                    : null;

                                            $subjectDayLabel =
                                                $subjectDayKey !== ''
                                                    ? $dayLabels[$subjectDayKey] ?? ucfirst($subjectDayKey)
                                                    : null;

                                            $classStart = substr((string) ($row->class_study_start_time ?? ''), 0, 5);
                                            $classEnd = substr((string) ($row->class_study_end_time ?? ''), 0, 5);
                                            $subjectStart = substr((string) ($row->subject_study_start_time ?? ''), 0, 5);
                                            $subjectEnd = substr((string) ($row->subject_study_end_time ?? ''), 0, 5);

                                            $classHasTime = $classStart !== '' && $classEnd !== '';
                                            $subjectHasTime = $subjectStart !== '' && $subjectEnd !== '';

                                            $samePeriod =
                                                $classPeriodKey === '' ||
                                                $subjectPeriodKey === '' ||
                                                $classPeriodKey === $subjectPeriodKey;

                                            $sameStart = $classHasTime && $subjectHasTime && $classStart === $subjectStart;
                                            $sameEnd = $classHasTime && $subjectHasTime && $classEnd === $subjectEnd;

                                            $dayCompatible = true;

                                            if ($classDayKey !== '' && $subjectDayKey !== '') {
                                                $dayCompatible =
                                                    $classDayKey === $subjectDayKey ||
                                                    $classDayKey === 'all' ||
                                                    $subjectDayKey === 'all';
                                            }

                                            if (!$classHasTime || !$subjectHasTime) {
                                                $scheduleStatus = 'missing';
                                                $scheduleLabel = 'Missing';
                                                $scheduleClass =
                                                    'border-slate-200 bg-slate-50 text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300';
                                            } elseif ($samePeriod && $sameStart && $sameEnd && $dayCompatible) {
                                                $scheduleStatus = 'matched';
                                                $scheduleLabel = 'Matched';
                                                $scheduleClass =
                                                    'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300';
                                            } else {
                                                $scheduleStatus = 'mismatch';
                                                $scheduleLabel = 'Mismatch';
                                                $scheduleClass =
                                                    'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300';
                                            }

                                            $studentId = (int) ($row->student_id ?? 0);

                                            $selectedMajorSubjects = collect($majorSubjectsByStudent[$studentId] ?? []);

                                            if ($selectedMajorSubjects->isEmpty() && !empty($row->subject_name)) {
                                                $selectedMajorSubjects = collect([
                                                    [
                                                        'id' => (int) ($row->subject_id ?? 0),
                                                        'name' => (string) $row->subject_name,
                                                        'code' => (string) ($row->subject_code ?? ''),
                                                    ],
                                                ]);
                                            }

                                            $selectedStudyTimes = collect($studyTimesByStudent[$studentId] ?? []);

                                            if ($selectedStudyTimes->isEmpty() && $classHasTime) {
                                                $selectedStudyTimes = collect([
                                                    [
                                                        'id' => (int) ($row->class_study_time_id ?? 0),
                                                        'school_class_id' => null,
                                                        'class_label' => $classLabel,
                                                        'day_of_week' => $classDayKey !== '' ? $classDayKey : 'all',
                                                        'period' => $classPeriodKey,
                                                        'start_time' => $classStart,
                                                        'end_time' => $classEnd,
                                                    ],
                                                ]);
                                            }
                                        @endphp

                                        <tr class="align-top transition odd:bg-slate-50/30 hover:bg-slate-50/80 dark:odd:bg-slate-800/30 dark:hover:bg-slate-800/70">
                                            <td class="whitespace-nowrap px-3 py-3">
                                                @php
                                                    $studentProfile = new \App\Models\User([
                                                        'name' => (string) ($row->student_name ?? 'Student'),
                                                        'email' => (string) ($row->student_email ?? ''),
                                                        'avatar' => (string) ($row->student_avatar ?? ''),
                                                    ]);
                                                @endphp

                                                <div class="flex items-start gap-3">
                                                    <img src="{{ $studentProfile->avatar_url }}"
                                                        alt="{{ $row->student_name ?: 'Student' }}"
                                                        class="h-11 w-11 rounded-2xl border border-slate-200 bg-slate-50 object-cover shadow-sm dark:border-slate-700 dark:bg-slate-800">

                                                    <div class="min-w-0">
                                                        <div class="truncate font-semibold text-slate-800 dark:text-slate-100">
                                                            {{ $row->student_name }}
                                                        </div>

                                                        <div class="truncate text-xs text-slate-500 dark:text-slate-400">
                                                            {{ $row->student_email ?: '-' }}
                                                        </div>

                                                        <div class="mt-1 inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-medium text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                                            ID #{{ str_pad((string) $row->student_id, 7, '0', STR_PAD_LEFT) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                <div class="whitespace-nowrap font-medium text-slate-700 dark:text-slate-200">
                                                    {{ $classLabel }}
                                                </div>

                                                <div class="text-xs text-slate-400 dark:text-slate-500">
                                                    {{ $classRoom !== '' ? 'Room: ' . $classRoom : 'Room: -' }}
                                                </div>
                                            </td>

                                            <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                @if ($selectedStudyTimes->isNotEmpty())
                                                    @php
                                                        $firstSlot = $selectedStudyTimes->first();
                                                        $slotDayKey = strtolower(trim((string) ($firstSlot['day_of_week'] ?? 'all')));
                                                        $slotPeriodKey = strtolower(trim((string) ($firstSlot['period'] ?? '')));

                                                        $slotDayLabel = $dayLabels[$slotDayKey] ?? ucfirst($slotDayKey ?: 'all');

                                                        $slotPeriodLabel =
                                                            $slotPeriodKey !== ''
                                                                ? $periodOptions[$slotPeriodKey] ?? ucfirst($slotPeriodKey)
                                                                : null;

                                                        $slotStart = substr((string) ($firstSlot['start_time'] ?? ''), 0, 5);
                                                        $slotEnd = substr((string) ($firstSlot['end_time'] ?? ''), 0, 5);
                                                    @endphp

                                                    <div class="rounded-xl border border-slate-200 bg-white p-2.5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                                        <div class="flex flex-wrap gap-1.5">
                                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                                                {{ $slotDayLabel }}
                                                            </span>

                                                            @if ($slotPeriodLabel)
                                                                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                                                                    {{ $slotPeriodLabel }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                                            @if ($slotStart !== '' && $slotEnd !== '')
                                                                {{ \Carbon\Carbon::parse($slotStart)->format('h:i A') }}
                                                                ->
                                                                {{ \Carbon\Carbon::parse($slotEnd)->format('h:i A') }}
                                                            @elseif ($slotStart !== '')
                                                                {{ \Carbon\Carbon::parse($slotStart)->format('h:i A') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if ($selectedStudyTimes->count() > 1)
                                                        <details class="mt-2 group">
                                                            <summary class="cursor-pointer text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                                +{{ $selectedStudyTimes->count() - 1 }} more study time(s)
                                                            </summary>

                                                            <div class="mt-2 space-y-1.5">
                                                                @foreach ($selectedStudyTimes->slice(1, 4) as $extraSlot)
                                                                    @php
                                                                        $extraDayKey = strtolower(trim((string) ($extraSlot['day_of_week'] ?? 'all')));
                                                                        $extraPeriodKey = strtolower(trim((string) ($extraSlot['period'] ?? '')));

                                                                        $extraDayLabel =
                                                                            $dayLabels[$extraDayKey] ?? ucfirst($extraDayKey ?: 'all');

                                                                        $extraPeriodLabel =
                                                                            $extraPeriodKey !== ''
                                                                                ? $periodOptions[$extraPeriodKey] ?? ucfirst($extraPeriodKey)
                                                                                : null;

                                                                        $extraStart = substr((string) ($extraSlot['start_time'] ?? ''), 0, 5);
                                                                        $extraEnd = substr((string) ($extraSlot['end_time'] ?? ''), 0, 5);
                                                                    @endphp

                                                                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                                        {{ $extraDayLabel }}{{ $extraPeriodLabel ? ' | ' . $extraPeriodLabel : '' }}

                                                                        @if ($extraStart !== '' && $extraEnd !== '')
                                                                            |
                                                                            {{ \Carbon\Carbon::parse($extraStart)->format('h:i A') }}
                                                                            ->
                                                                            {{ \Carbon\Carbon::parse($extraEnd)->format('h:i A') }}
                                                                        @elseif ($extraStart !== '')
                                                                            |
                                                                            {{ \Carbon\Carbon::parse($extraStart)->format('h:i A') }}
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </details>
                                                    @endif
                                                @else
                                                    <div class="text-slate-400 dark:text-slate-500">-</div>
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                @if ($selectedMajorSubjects->isNotEmpty())
                                                    @php
                                                        $firstSubject = $selectedMajorSubjects->first();
                                                    @endphp

                                                    <div class="rounded-xl border border-indigo-100 bg-indigo-50/70 px-2.5 py-2 shadow-sm dark:border-indigo-400/20 dark:bg-indigo-500/15">
                                                        <div class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                                                            {{ $firstSubject['name'] ?? '-' }}
                                                        </div>

                                                        <div class="text-xs text-indigo-500 dark:text-indigo-300/80">
                                                            {{ $firstSubject['code'] ?? '-' }}
                                                        </div>
                                                    </div>

                                                    @if ($selectedMajorSubjects->count() > 1)
                                                        <details class="mt-2 group">
                                                            <summary class="cursor-pointer text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                                +{{ $selectedMajorSubjects->count() - 1 }} more major subject(s)
                                                            </summary>

                                                            <div class="mt-2 space-y-1.5">
                                                                @foreach ($selectedMajorSubjects->slice(1, 5) as $subjectItem)
                                                                    <div class="rounded-lg border border-indigo-100 bg-indigo-50/40 px-2 py-1 text-[11px] text-indigo-700 dark:border-indigo-400/20 dark:bg-indigo-500/10 dark:text-indigo-300">
                                                                        <span class="font-semibold">{{ $subjectItem['name'] ?? '-' }}</span>

                                                                        @if (!empty($subjectItem['code']))
                                                                            <span class="text-indigo-500 dark:text-indigo-300/80">
                                                                                ({{ $subjectItem['code'] }})
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </details>
                                                    @endif
                                                @else
                                                    <span class="text-slate-400 dark:text-slate-500">Unassigned</span>
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                <div class="flex flex-wrap gap-1.5">
                                                    @if ($subjectDayLabel)
                                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                                            {{ $subjectDayLabel }}
                                                        </span>
                                                    @endif

                                                    @if ($subjectPeriodLabel)
                                                        <span class="inline-flex items-center rounded-full bg-violet-50 px-2 py-0.5 text-[11px] font-semibold text-violet-700 dark:bg-violet-500/15 dark:text-violet-300">
                                                            {{ $subjectPeriodLabel }}
                                                        </span>
                                                    @endif
                                                </div>

                                                @if ($row->subject_study_start_time && $row->subject_study_end_time)
                                                    <div class="mt-1 whitespace-nowrap font-semibold text-slate-700 dark:text-slate-200">
                                                        {{ \Carbon\Carbon::parse($row->subject_study_start_time)->format('h:i A') }}
                                                        ->
                                                        {{ \Carbon\Carbon::parse($row->subject_study_end_time)->format('h:i A') }}
                                                    </div>
                                                @elseif ($row->subject_study_start_time)
                                                    <div class="mt-1 whitespace-nowrap font-semibold text-slate-700 dark:text-slate-200">
                                                        {{ \Carbon\Carbon::parse($row->subject_study_start_time)->format('h:i A') }}
                                                    </div>
                                                @else
                                                    <div class="mt-1 text-slate-400 dark:text-slate-500">-</div>
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-3 py-3">
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $scheduleClass }}">
                                                        {{ $scheduleLabel }}
                                                    </span>

                                                    @if ($selectedStudyTimes->count() > 1)
                                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                            {{ $selectedStudyTimes->count() }} slots
                                                        </span>
                                                    @endif
                                                </div>

                                                @if ($scheduleStatus === 'mismatch')
                                                    <div class="mt-1 text-[11px] text-amber-700 dark:text-amber-300">
                                                        Class time and subject time are different.
                                                    </div>
                                                @endif

                                                @if ($selectedStudyTimes->isNotEmpty())
                                                    @php
                                                        $previewSlot = $selectedStudyTimes->first();
                                                        $previewDayKey = strtolower(trim((string) ($previewSlot['day_of_week'] ?? 'all')));
                                                        $previewDayLabel = $dayLabels[$previewDayKey] ?? ucfirst($previewDayKey ?: 'all');
                                                        $previewStart = substr((string) ($previewSlot['start_time'] ?? ''), 0, 5);
                                                        $previewEnd = substr((string) ($previewSlot['end_time'] ?? ''), 0, 5);

                                                        $dayToWeekIndex = [
                                                            'sunday' => 0,
                                                            'monday' => 1,
                                                            'tuesday' => 2,
                                                            'wednesday' => 3,
                                                            'thursday' => 4,
                                                            'friday' => 5,
                                                            'saturday' => 6,
                                                        ];

                                                        $previewBaseDate = \Carbon\Carbon::today();

                                                        if ($previewDayKey === 'all' || !isset($dayToWeekIndex[$previewDayKey])) {
                                                            $previewDate = $previewBaseDate->copy();
                                                        } else {
                                                            $previewTargetWeekday = (int) $dayToWeekIndex[$previewDayKey];
                                                            $previewDaysUntil = ($previewTargetWeekday - $previewBaseDate->dayOfWeek + 7) % 7;
                                                            $previewDate = $previewBaseDate->copy()->addDays($previewDaysUntil);
                                                        }
                                                    @endphp

                                                    <div class="mt-2 rounded-xl border border-slate-200 bg-white px-2.5 py-2 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                                        <div class="text-[10px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                                            Next Schedule
                                                        </div>

                                                        <div class="text-xs font-semibold text-slate-800 dark:text-slate-100">
                                                            {{ $previewDate->format('D, M d, Y') }}
                                                            ({{ $previewDayLabel }})
                                                        </div>

                                                        <div class="mt-1 text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                                                            @if ($previewStart !== '' && $previewEnd !== '')
                                                                {{ \Carbon\Carbon::parse($previewStart)->format('h:i A') }}
                                                                ->
                                                                {{ \Carbon\Carbon::parse($previewEnd)->format('h:i A') }}
                                                            @elseif ($previewStart !== '')
                                                                {{ \Carbon\Carbon::parse($previewStart)->format('h:i A') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if ($selectedStudyTimes->count() > 1)
                                                        <details class="mt-2 group">
                                                            <summary class="cursor-pointer text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                                View all schedules
                                                            </summary>

                                                            <div class="mt-2 space-y-1.5">
                                                                @foreach ($selectedStudyTimes->slice(1, 4) as $slot)
                                                                    @php
                                                                        $slotDayKey = strtolower(trim((string) ($slot['day_of_week'] ?? 'all')));
                                                                        $slotDayLabel = $dayLabels[$slotDayKey] ?? ucfirst($slotDayKey ?: 'all');
                                                                        $slotStart = substr((string) ($slot['start_time'] ?? ''), 0, 5);
                                                                        $slotEnd = substr((string) ($slot['end_time'] ?? ''), 0, 5);

                                                                        $scheduleBaseDate = \Carbon\Carbon::today();

                                                                        if ($slotDayKey === 'all' || !isset($dayToWeekIndex[$slotDayKey])) {
                                                                            $nextScheduleDate = $scheduleBaseDate->copy();
                                                                        } else {
                                                                            $targetWeekday = (int) $dayToWeekIndex[$slotDayKey];
                                                                            $daysUntil = ($targetWeekday - $scheduleBaseDate->dayOfWeek + 7) % 7;
                                                                            $nextScheduleDate = $scheduleBaseDate->copy()->addDays($daysUntil);
                                                                        }
                                                                    @endphp

                                                                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                                        {{ $nextScheduleDate->format('D, M d, Y') }} |
                                                                        {{ $slotDayLabel }}

                                                                        @if ($slotStart !== '' && $slotEnd !== '')
                                                                            |
                                                                            {{ \Carbon\Carbon::parse($slotStart)->format('h:i A') }}
                                                                            ->
                                                                            {{ \Carbon\Carbon::parse($slotEnd)->format('h:i A') }}
                                                                        @elseif ($slotStart !== '')
                                                                            |
                                                                            {{ \Carbon\Carbon::parse($slotStart)->format('h:i A') }}
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </details>
                                                    @endif
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                @php
                                                    $teacherProfile = new \App\Models\User([
                                                        'name' => (string) ($row->teacher_name ?? 'Unassigned'),
                                                        'email' => (string) ($row->teacher_email ?? ''),
                                                        'avatar' => (string) ($row->teacher_avatar ?? ''),
                                                    ]);
                                                @endphp

                                                <div class="flex items-start gap-3">
                                                    <img src="{{ $teacherProfile->avatar_url }}"
                                                        alt="{{ $row->teacher_name ?: 'Teacher' }}"
                                                        class="h-11 w-11 rounded-2xl border border-slate-200 bg-slate-50 object-cover shadow-sm dark:border-slate-700 dark:bg-slate-800">

                                                    <div class="min-w-0">
                                                        <div class="truncate font-semibold text-slate-700 dark:text-slate-100">
                                                            {{ $row->teacher_name ?: 'Unassigned' }}
                                                        </div>

                                                        <div class="truncate text-xs text-slate-400 dark:text-slate-500">
                                                            {{ $row->teacher_email ?: '-' }}
                                                        </div>

                                                        <div class="mt-1 inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-medium text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                                            Teacher Profile
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="whitespace-nowrap px-3 py-3 text-slate-500 dark:text-slate-400">
                                                <div>
                                                    <div class="text-[11px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                                        Student
                                                    </div>

                                                    @if ($studentCreatedAt)
                                                        <div>{{ $studentCreatedAt->format('M d, Y h:i A') }}</div>
                                                        <div class="text-xs text-slate-400 dark:text-slate-500">
                                                            {{ $studentCreatedAt->diffForHumans() }}
                                                        </div>
                                                    @else
                                                        <div>-</div>
                                                    @endif
                                                </div>

                                                <div class="mt-2 border-t border-dashed border-slate-200 pt-2 dark:border-slate-700">
                                                    <div class="text-[11px] font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                                        Teacher
                                                    </div>

                                                    @if ($teacherCreatedAt)
                                                        <div>{{ $teacherCreatedAt->format('M d, Y h:i A') }}</div>
                                                        <div class="text-xs text-slate-400 dark:text-slate-500">
                                                            {{ $teacherCreatedAt->diffForHumans() }}
                                                        </div>
                                                    @else
                                                        <div>-</div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-3 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                                No student study records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="student-study-pagination mt-5 text-slate-700 dark:text-slate-300">
                        {{ $studies->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
