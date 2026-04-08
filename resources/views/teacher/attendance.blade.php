@extends('layout.teacher.navbar')

@section('page')
    @php
        $selectedClass = collect($classes ?? [])->firstWhere('id', (int) ($classId ?? 0));
        $selectedSubject = collect($subjectsForSelectedClass ?? [])->firstWhere('id', (int) ($subjectId ?? 0));
    @endphp

    <div class="teacher-time-stage space-y-6">
        @if (session('success'))
            <div class="js-inline-flash teacher-time-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="js-inline-flash teacher-time-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('error'))
            <div class="js-inline-flash teacher-time-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="js-inline-flash teacher-time-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="teacher-time-reveal teacher-time-float rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
            style="--sd: 3;" x-data="{ filterOpen: false, showClassGrid: {{ ($classId ?? '') === '' ? 'true' : 'false' }} }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-slate-700">
                        Class: {{ $selectedClass?->display_name ?? 'Not selected' }}
                    </span>
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-emerald-700">
                        Subject: {{ $selectedSubject?->name ?? 'Choose class first' }}
                    </span>
                    <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-indigo-700">
                        Date: {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                    </span>
                    <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-sky-700">
                        Day: {{ $selectedDayLabel ?? 'All Days' }}
                    </span>
                </div>

                <button type="button" @click="filterOpen = true"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                    </svg>
                    Filters
                </button>
            </div>

            <div x-show="filterOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[80] bg-slate-900/40"
                @click="filterOpen = false"></div>

            <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl">
                <div class="flex h-full flex-col">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <h3 class="text-3xl font-black text-slate-900">Filters</h3>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('teacher.attendance.index') }}"
                                class="text-sm font-semibold text-slate-500 hover:text-slate-700">Clear All</a>
                            <button type="button" @click="filterOpen = false"
                                class="text-2xl font-bold leading-none text-slate-700 hover:text-slate-900"
                                aria-label="Close filters">&times;</button>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('teacher.attendance.index') }}"
                        class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                        @if (($classId ?? '') !== '')
                            <input type="hidden" name="class_id" value="{{ $classId }}">
                        @endif
                        @if (($subjectId ?? '') !== '')
                            <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                        @endif
                        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                            <section class="space-y-2">
                                <h4 class="text-xl font-bold text-slate-900">Date</h4>
                                <input type="date" name="date" value="{{ $selectedDate }}"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            </section>

                            <section class="space-y-2">
                                <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                <input type="text" name="q" value="{{ $search }}"
                                    placeholder="Search student name or email"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            </section>

                            <section class="space-y-2">
                                <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                <select name="status"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>All
                                    </option>
                                    @foreach ($statusLabels as $statusKey => $statusLabel)
                                        <option value="{{ $statusKey }}"
                                            {{ ($statusFilter ?? 'all') === $statusKey ? 'selected' : '' }}>
                                            {{ $statusLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </section>
                        </div>

                        <div class="border-t border-slate-200 px-5 py-4">
                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-4 py-3 text-lg font-bold text-white transition hover:bg-slate-800">
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </aside>

            @if (!$hasAttendanceTable)
                <div
                    class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
                    Attendance table is missing. Run `php artisan migrate` to enable attendance check.
                </div>
            @endif

            @if (($classes ?? collect())->count() > 0)
                <div class="mt-5" x-show="showClassGrid" x-cloak x-transition.opacity>
                    <h3 class="text-sm font-black text-slate-900">Classes Grid</h3>
                    <p class="mt-1 text-xs text-slate-500">Showing only classes taught on
                        {{ $selectedDayLabel ?? 'this day' }}.
                    </p>
                    @php
                        $periodOrder = [
                            'morning' => 1,
                            'afternoon' => 2,
                            'evening' => 3,
                            'night' => 4,
                            'custom' => 5,
                        ];
                        $periodBuckets = collect([
                            'morning' => collect(),
                            'afternoon' => collect(),
                            'evening' => collect(),
                            'night' => collect(),
                            'custom' => collect(),
                        ]);

                        foreach ($classes as $classOption) {
                            $teacherSchedules = collect(
                                $classOption->teacherStudySchedules ?? ($classOption->studySchedules ?? []),
                            );
                            $periodGroups = $teacherSchedules
                                ->groupBy(function ($slot) {
                                    return strtolower((string) ($slot->period ?? 'custom'));
                                })
                                ->sortBy(function ($group, $periodKey) use ($periodOrder) {
                                    return $periodOrder[$periodKey] ?? 99;
                                });

                            foreach ($periodGroups as $periodKey => $periodSlots) {
                                if (!$periodBuckets->has($periodKey)) {
                                    $periodBuckets->put($periodKey, collect());
                                }

                                $periodBuckets->put(
                                    $periodKey,
                                    $periodBuckets->get($periodKey)->push([
                                        'class' => $classOption,
                                        'period_key' => $periodKey,
                                        'period_slots' => $periodSlots->values(),
                                    ]),
                                );
                            }
                        }
                    @endphp

                    <div class="mt-3 grid gap-5 xl:grid-cols-3">
                        @foreach ($periodOrder as $periodKey => $periodRank)
                            @php
                                $periodCards = collect($periodBuckets->get($periodKey, collect()))->values();
                                $periodLabel = $periodLabels[$periodKey] ?? ucfirst($periodKey);
                            @endphp
                            @if ($periodCards->isNotEmpty())
                                <section
                                    class="flex h-full flex-col rounded-3xl border border-slate-200 bg-white/80 p-4 shadow-sm">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <h4 class="text-sm font-black text-slate-900">{{ $periodLabel }}</h4>
                                            <p class="mt-0.5 text-xs text-slate-500">
                                                Classes taught in the {{ strtolower($periodLabel) }} period on
                                                {{ $selectedDayLabel ?? 'this day' }}.
                                            </p>
                                        </div>
                                        <span
                                            class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-500">
                                            {{ number_format($periodCards->count()) }} class(es)
                                        </span>
                                    </div>

                                    <div class="mt-3 grid grid-cols-1 gap-4">
                                        @foreach ($periodCards as $periodCard)
                                            @php
                                                $classOption = $periodCard['class'];
                                                $periodSlots = collect($periodCard['period_slots'] ?? []);
                                                $isActiveClass = ($classId ?? '') === (string) $classOption->id;
                                                $classStatus =
                                                    $classAttendanceStatus[(int) ($classOption->id ?? 0)] ?? null;
                                                $attendanceState = (string) ($classStatus['state'] ?? 'pending');
                                                $checkedCount = (int) ($classStatus['checked_count'] ?? 0);
                                                $studentsCount = (int) ($classStatus['students_count'] ?? 0);
                                                $isSaved = $attendanceState === 'saved';
                                                $isPending = $attendanceState === 'pending';
                                                $cardColorClass = $isSaved
                                                    ? 'border-emerald-300 bg-emerald-50 hover:bg-emerald-100/70'
                                                    : ($isPending
                                                        ? 'border-red-300 bg-red-50 hover:bg-red-100/70'
                                                        : 'border-slate-200 bg-slate-50 hover:bg-slate-100');
                                                $activeRingClass = $isActiveClass
                                                    ? ($isSaved
                                                        ? ' ring-2 ring-emerald-300'
                                                        : ($isPending
                                                            ? ' ring-2 ring-red-300'
                                                            : ' ring-2 ring-slate-300'))
                                                    : '';
                                                $titleClass = $isSaved
                                                    ? 'text-emerald-900'
                                                    : ($isPending
                                                        ? 'text-red-900'
                                                        : 'text-slate-900');
                                                $roomClass = $isSaved
                                                    ? 'text-emerald-700'
                                                    : ($isPending
                                                        ? 'text-red-700'
                                                        : 'text-slate-500');
                                                $badgeClass = $isSaved
                                                    ? 'border-emerald-200 bg-emerald-100 text-emerald-700'
                                                    : ($isPending
                                                        ? 'border-red-200 bg-red-100 text-red-700'
                                                        : 'border-slate-200 bg-white text-slate-600');
                                                $statusLabel = $isSaved
                                                    ? 'Saved'
                                                    : ($isPending
                                                        ? 'Check Attendance'
                                                        : 'No Students');
                                                $todaySubjects = $periodSlots
                                                    ->pluck('subject')
                                                    ->filter()
                                                    ->unique('id')
                                                    ->values();
                                                $slotPreview = $periodSlots->take(3);
                                            @endphp
                                            <a href="{{ route('teacher.attendance.index', ['class_id' => $classOption->id, 'date' => $selectedDate]) }}"
                                                class="block h-full w-full rounded-2xl border px-4 py-3 transition hover:-translate-y-0.5 hover:shadow-md {{ $cardColorClass }}{{ $activeRingClass }}">
                                                <div class="flex h-full min-h-[18rem] w-full flex-col">
                                                    <div class="flex items-start justify-between gap-2">
                                                        <div class="text-sm font-black {{ $titleClass }}">
                                                            {{ $classOption->display_name }}
                                                        </div>
                                                        <span
                                                            class="rounded-full border px-2 py-0.5 text-[11px] font-bold {{ $badgeClass }}">
                                                            {{ $statusLabel }}
                                                        </span>
                                                    </div>

                                                    <div class="mt-1 text-[11px] font-semibold {{ $roomClass }}">
                                                        Room: {{ $classOption->room ?: 'N/A' }}
                                                    </div>

                                                    <div class="mt-2 flex flex-wrap gap-1.5 text-[11px] font-semibold">
                                                        <span
                                                            class="rounded-full border border-sky-200 bg-sky-50 px-2 py-0.5 text-sky-700">
                                                            Users: {{ number_format($classOption->students_count ?? 0) }}
                                                        </span>
                                                        <span
                                                            class="rounded-full border px-2 py-0.5 {{ $isSaved ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($isPending ? 'border-red-200 bg-red-50 text-red-700' : 'border-slate-200 bg-white text-slate-600') }}">
                                                            Checked:
                                                            {{ number_format($checkedCount) }}/{{ number_format($studentsCount) }}
                                                        </span>
                                                        <span
                                                            class="rounded-full border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-indigo-700">
                                                            Subjects: {{ number_format($todaySubjects->count()) }}
                                                        </span>
                                                        <span
                                                            class="rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-amber-700">
                                                            {{ $periodLabel }}:
                                                            {{ number_format($periodSlots->count()) }}
                                                        </span>
                                                    </div>

                                                    <div class="mt-3 space-y-2">
                                                        <div
                                                            class="text-[11px] font-bold uppercase tracking-wide text-slate-500">
                                                            {{ $periodLabel }} Teaching
                                                        </div>
                                                        @if ($periodSlots->isEmpty())
                                                            <div class="text-[11px] font-semibold text-slate-400">No slots
                                                                for this period</div>
                                                        @else
                                                            <div class="space-y-2">
                                                                @foreach ($slotPreview as $slot)
                                                                    @php
                                                                        $subjectName =
                                                                            (string) ($slot->subject?->name ??
                                                                                'Assigned Subject');
                                                                        $startText = $slot->start_time
                                                                            ? \Carbon\Carbon::parse(
                                                                                $slot->start_time,
                                                                            )->format('h:i A')
                                                                            : '--';
                                                                        $endText = $slot->end_time
                                                                            ? \Carbon\Carbon::parse(
                                                                                $slot->end_time,
                                                                            )->format('h:i A')
                                                                            : '--';
                                                                    @endphp
                                                                    <div
                                                                        class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs">
                                                                        <span
                                                                            class="min-w-0 truncate font-semibold text-slate-800">
                                                                            {{ $subjectName }}
                                                                        </span>
                                                                        <span
                                                                            class="shrink-0 font-semibold text-slate-600">
                                                                            {{ $startText }} -> {{ $endText }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                                @if ($periodSlots->count() > $slotPreview->count())
                                                                    <div class="text-[10px] font-semibold text-slate-500">
                                                                        +{{ $periodSlots->count() - $slotPreview->count() }}
                                                                        more slot(s)
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if (($classId ?? '') === '')
            @else
                @php
                    $isSelectedSubjectSaved = ($selectedSubjectAttendanceStatus['state'] ?? '') === 'saved';
                @endphp
                <div class="mt-5 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 bg-slate-50/80 px-4 py-4 sm:px-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-black text-slate-900">Attendance Table</div>
                                <div class="mt-1 text-xs text-slate-500">
                                    Mark attendance for {{ $selectedClass?->display_name ?? 'this class' }}
                                    @if ($selectedSubject)
                                        in {{ $selectedSubject->name }}
                                    @endif
                                    on {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}.
                                </div>
                            </div>

                            <a href="{{ route(
                                'teacher.attendance.index',
                                array_filter(
                                    [
                                        'date' => $selectedDate,
                                        'q' => $search ?: null,
                                        'status' => ($statusFilter ?? 'all') !== 'all' ? $statusFilter : null,
                                    ],
                                    fn($value) => !is_null($value) && $value !== '',
                                ),
                            ) }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                Back Class
                            </a>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-semibold">
                            <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-slate-600">
                                User rows: {{ number_format($students->count()) }}
                            </span>
                            <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-indigo-700">
                                Day: {{ $selectedDayLabel ?? 'All Days' }}
                            </span>
                            <span
                                class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-emerald-700">
                                Selected class: {{ $selectedClass?->display_name ?? 'N/A' }}
                            </span>
                            <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-sky-700">
                                Subject: {{ $selectedSubject?->name ?? 'Not selected' }}
                            </span>
                        </div>

                        @if (collect($subjectsForSelectedClass ?? [])->isNotEmpty())
                            <div class="mt-4">
                                <div class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Subject for this
                                    class</div>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($subjectsForSelectedClass as $subjectOption)
                                        @php
                                            $subjectStatus =
                                                $subjectAttendanceStatus[(int) ($subjectOption->id ?? 0)] ?? null;
                                            $isActiveSubject =
                                                (string) ($subjectId ?? '') === (string) ($subjectOption->id ?? '');
                                            $isSavedSubject = ($subjectStatus['state'] ?? '') === 'saved';
                                        @endphp
                                        <a href="{{ route(
                                            'teacher.attendance.index',
                                            array_filter(
                                                [
                                                    'class_id' => $classId,
                                                    'subject_id' => $subjectOption->id,
                                                    'date' => $selectedDate,
                                                    'q' => $search ?: null,
                                                    'status' => ($statusFilter ?? 'all') !== 'all' ? $statusFilter : null,
                                                ],
                                                fn($value) => !is_null($value) && $value !== '',
                                            ),
                                        ) }}"
                                            class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ $isActiveSubject ? 'border-indigo-300 bg-indigo-50 text-indigo-700' : ($isSavedSubject ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50') }}">
                                            <span>{{ $subjectOption->name }}</span>
                                            <span
                                                class="rounded-full px-2 py-0.5 text-[10px] {{ $isSavedSubject ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                                {{ $isSavedSubject ? 'Saved' : 'Pending' }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (!$selectedSubject)
                            <div
                                class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
                                Choose a subject for this class before saving attendance.
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('teacher.attendance.store') }}"
                        class="js-attendance-submit-form space-y-4 p-4 sm:p-5">
                        @csrf
                        <input type="hidden" name="school_class_id" value="{{ $classId }}">
                        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                            @if ($isSelectedSubjectSaved)
                                <p
                                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                                    Attendance for this subject is already saved. Check status cannot be changed.
                                </p>
                            @else
                                <p class="text-xs font-semibold text-slate-500">
                                    Choose a status for each student in this subject, then save the table below.
                                </p>
                            @endif

                            <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                                <button type="button" data-set-all-status="present"
                                    {{ !$selectedSubject || $isSelectedSubjectSaved ? 'disabled' : '' }}
                                    class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50 sm:py-1.5">
                                    Set All Present
                                </button>
                                <button type="button" data-set-all-status="absent"
                                    {{ !$selectedSubject || $isSelectedSubjectSaved ? 'disabled' : '' }}
                                    class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-50 sm:py-1.5">
                                    Set All Absent
                                </button>
                                <button type="submit"
                                    {{ !$hasAttendanceTable || !$selectedSubject || $isSelectedSubjectSaved ? 'disabled' : '' }}
                                    class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50 sm:py-2">
                                    Save Attendance
                                </button>
                            </div>
                        </div>

                        <div class="teacher-attendance-table-wrap overflow-hidden rounded-3xl border border-slate-200 bg-slate-50">
                            <div class="teacher-attendance-table-scroller max-h-[620px] overflow-auto">
                                <table class="teacher-attendance-table w-full min-w-[1220px] text-left text-sm">
                                    <thead
                                        class="sticky top-0 z-10 border-b border-slate-200 bg-white/95 text-xs uppercase tracking-wide text-slate-500 backdrop-blur">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold">Student Profile</th>
                                            <th class="px-4 py-3 font-semibold">Law Request</th>
                                            <th class="px-4 py-3 font-semibold">Attendance</th>
                                            <th class="px-4 py-3 font-semibold">Check Status</th>
                                            <th class="px-4 py-3 font-semibold">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @forelse ($students as $student)
                                            @php
                                                $record = $attendanceByStudent->get($student->id);
                                                $lawRequest = ($lawRequestsByStudent ?? collect())->get(
                                                    (int) $student->id,
                                                );
                                                $currentStatus = strtolower((string) ($record?->status ?? ''));
                                                $lawStatus = strtolower((string) ($lawRequest?->status ?? ''));
                                                $isLawApproved = $lawStatus === 'approved';
                                                $isLawPending = $lawStatus === 'pending';
                                                $isLawRejected = $lawStatus === 'rejected';
                                                $hasActiveLawRequest =
                                                    $lawRequest && ($isLawPending || $isLawApproved || $isLawRejected);
                                                $lawTypeLabel = $lawRequest
                                                    ? ucfirst(
                                                        str_replace(
                                                            '_',
                                                            ' ',
                                                            (string) ($lawRequest->law_type ?? 'request'),
                                                        ),
                                                    )
                                                    : '';
                                                $lawRequestSubject = trim((string) ($lawRequest->subject ?? ''));
                                                $lawRequestSubjectTime = trim(
                                                    (string) ($lawRequest->subject_time ?? ''),
                                                );
                                                $defaultLawRemark = '';
                                                if ($hasActiveLawRequest) {
                                                    $defaultLawRemark = 'Law Request (' . $lawTypeLabel . ')';
                                                    if ($lawRequestSubject !== '') {
                                                        $defaultLawRemark .= ': ' . $lawRequestSubject;
                                                    }
                                                    if ($lawRequestSubjectTime !== '') {
                                                        $defaultLawRemark .= ' @ ' . $lawRequestSubjectTime;
                                                    }
                                                    if (!empty($lawRequest?->reason)) {
                                                        $defaultLawRemark .=
                                                            ($lawRequestSubject !== '' ? ' | ' : ': ') .
                                                            trim((string) $lawRequest->reason);
                                                    }
                                                    $defaultLawRemark = mb_substr($defaultLawRemark, 0, 170);
                                                }
                                                $selectedStatus = old(
                                                    'attendance.' . $student->id . '.status',
                                                    $currentStatus !== ''
                                                        ? $currentStatus
                                                        : ($isLawApproved
                                                            ? 'excused'
                                                            : ($isLawRejected
                                                                ? 'absent'
                                                                : 'present')),
                                                );
                                                $remarkValue = old(
                                                    'attendance.' . $student->id . '.remark',
                                                    (string) ($record?->remark ?? $defaultLawRemark),
                                                );
                                            @endphp
                                            <tr class="teacher-attendance-row js-attendance-row align-top hover:bg-slate-50/80"
                                                data-student-name="{{ $student->name }}"
                                                data-student-id="{{ $student->id }}"
                                                data-law-request-id="{{ (int) ($lawRequest->id ?? 0) }}">
                                                <td class="teacher-attendance-cell px-4 py-4" data-cell-label="Student Profile">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="h-12 w-12 shrink-0 overflow-hidden rounded-2xl bg-indigo-100 ring-2 ring-white shadow-sm">
                                                            <img src="{{ $student->avatar_url }}"
                                                                onerror="this.onerror=null;this.src='{{ $student->fallback_avatar_url }}';"
                                                                alt="{{ $student->name }}"
                                                                class="h-full w-full object-cover">
                                                        </div>
                                                        <div class="min-w-0">
                                                            <div class="font-semibold text-slate-900">{{ $student->name }}
                                                            </div>
                                                            <div class="mt-0.5 text-xs text-slate-500">ID
                                                                #{{ $student->formatted_id }}</div>
                                                            <div class="mt-0.5 truncate text-xs text-slate-500">
                                                                {{ $student->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="teacher-attendance-cell px-4 py-4" data-cell-label="Law Request">
                                                    @if ($hasActiveLawRequest)
                                                        <div class="space-y-2">
                                                            <div class="flex flex-wrap items-center gap-1.5">
                                                                <span data-law-request-badge
                                                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $isLawApproved ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($isLawRejected ? 'border-red-200 bg-red-50 text-red-700' : 'border-amber-200 bg-amber-50 text-amber-700') }}">
                                                                    {{ ucfirst($lawStatus) }}
                                                                </span>
                                                                <span
                                                                    class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700">
                                                                    {{ $lawTypeLabel }}
                                                                </span>
                                                            </div>
                                                            <div class="text-xs font-semibold text-slate-700">
                                                                {{ $lawRequestSubject !== '' ? $lawRequestSubject : 'Subject not set' }}
                                                            </div>
                                                            <div class="text-xs text-indigo-600">
                                                                {{ $lawRequest->requested_for?->format('M d, Y') ?? 'Selected date' }}
                                                                @if ($lawRequestSubjectTime !== '')
                                                                    | {{ $lawRequestSubjectTime }}
                                                                @endif
                                                            </div>
                                                            @if (!empty($lawRequest?->reason))
                                                                <div
                                                                    class="max-w-xs text-xs leading-relaxed text-slate-500 line-clamp-3">
                                                                    {{ $lawRequest->reason }}
                                                                </div>
                                                            @endif
                                                            @if ($isLawPending)
                                                                <div class="flex flex-wrap gap-2 pt-1"
                                                                    data-law-request-actions>
                                                                    <button type="submit"
                                                                        formaction="{{ route('teacher.attendance.law-requests.approve', $lawRequest) }}"
                                                                        formmethod="POST" formnovalidate
                                                                        data-law-action="approve"
                                                                        {{ !$selectedSubject || $isSelectedSubjectSaved ? 'disabled' : '' }}
                                                                        class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50">
                                                                        Approve
                                                                    </button>
                                                                    <button type="submit"
                                                                        formaction="{{ route('teacher.attendance.law-requests.reject', $lawRequest) }}"
                                                                        formmethod="POST" formnovalidate
                                                                        data-law-action="reject"
                                                                        {{ !$selectedSubject || $isSelectedSubjectSaved ? 'disabled' : '' }}
                                                                        class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50">
                                                                        Cancel
                                                                    </button>
                                                                </div>
                                                            @elseif ($isLawRejected)
                                                                <div class="text-[11px] font-semibold text-red-700"
                                                                    data-law-request-state>
                                                                    Cancelled and marked absent
                                                                </div>
                                                            @else
                                                                <div class="text-[11px] font-semibold text-emerald-700"
                                                                    data-law-request-state>
                                                                    Approved for attendance
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-slate-400">No request</span>
                                                    @endif
                                                </td>
                                                <td class="teacher-attendance-cell px-4 py-4" data-cell-label="Attendance">
                                                    @if ($currentStatus !== '' && isset($statusLabels[$currentStatus]))
                                                        <span data-attendance-indicator
                                                            class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                            {{ $statusLabels[$currentStatus] }}
                                                        </span>
                                                    @else
                                                        <span data-attendance-indicator
                                                            class="inline-flex items-center rounded-full border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                            Not Checked Yet
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="teacher-attendance-cell px-4 py-4" data-cell-label="Check Status">
                                                    <div class="space-y-2">
                                                        <select name="attendance[{{ $student->id }}][status]"
                                                            {{ !$selectedSubject || $isSelectedSubjectSaved ? 'disabled' : '' }}
                                                            class="js-student-status sr-only" tabindex="-1"
                                                            aria-hidden="true">
                                                            @foreach ($statusLabels as $statusKey => $statusLabel)
                                                                <option value="{{ $statusKey }}"
                                                                    {{ $selectedStatus === $statusKey ? 'selected' : '' }}>
                                                                    {{ $statusLabel }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-4">
                                                            @foreach ($statusLabels as $statusKey => $statusLabel)
                                                                @php
                                                                    $isActiveStatus = $selectedStatus === $statusKey;
                                                                @endphp
                                                                <button type="button"
                                                                    data-status-option="{{ $statusKey }}"
                                                                    {{ !$selectedSubject || $isSelectedSubjectSaved ? 'disabled' : '' }}
                                                                    aria-pressed="{{ $isActiveStatus ? 'true' : 'false' }}"
                                                                    class="js-status-box rounded-lg border px-2 py-1.5 text-xs font-semibold transition disabled:cursor-not-allowed disabled:opacity-50 {{ $isActiveStatus ? 'border-indigo-300 bg-indigo-50 text-indigo-700 shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-indigo-200 hover:bg-indigo-50/50 hover:text-indigo-700' }}">
                                                                    {{ $statusLabel }}
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="teacher-attendance-cell px-4 py-4" data-cell-label="Remark">
                                                    <input type="text" name="attendance[{{ $student->id }}][remark]"
                                                        value="{{ $remarkValue }}" maxlength="255"
                                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                        placeholder="Optional note">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                                                    No users found for this class and filter.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </section>
    </div>

    @php
        $attendancePageData = [
            'attendanceAlerts' => $attendanceAlerts ?? [],
            'validationErrors' => $errors->all(),
            'flash' => [
                'success' => session('success'),
                'warning' => session('warning'),
                'error' => session('error'),
            ],
        ];
    @endphp
    <script id="teacher-attendance-data" type="application/json">{!! json_encode($attendancePageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/Teacher/Attendence.js'])
@endsection
