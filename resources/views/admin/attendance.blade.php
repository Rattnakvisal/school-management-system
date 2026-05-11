@extends('layout.admin.navbar')

@section('page')
    @php
        $recordTotal = max(0, (int) ($stats['records'] ?? 0));

        $attendanceCards = [
            [
                'label' => 'Records',
                'activeLabel' => 'Total',
                'active' => $recordTotal,
                'total' => $recordTotal,
                'icon' => 'records',
                'tone' =>
                    'from-indigo-100 to-white text-indigo-600 dark:from-indigo-500/20 dark:to-slate-900 dark:text-indigo-300',
            ],
            [
                'label' => 'Students',
                'activeLabel' => 'Students',
                'active' => (int) ($stats['students'] ?? 0),
                'total' => max(0, (int) ($stats['students'] ?? 0)),
                'icon' => 'students',
                'tone' => 'from-sky-100 to-white text-sky-600 dark:from-sky-500/20 dark:to-slate-900 dark:text-sky-300',
            ],
            [
                'label' => 'Teachers',
                'activeLabel' => 'Teachers',
                'active' => (int) ($stats['teachers'] ?? 0),
                'total' => max(0, (int) ($stats['teachers'] ?? 0)),
                'icon' => 'teachers',
                'tone' =>
                    'from-emerald-100 to-white text-emerald-600 dark:from-emerald-500/20 dark:to-slate-900 dark:text-emerald-300',
            ],
            [
                'label' => 'Classes',
                'activeLabel' => 'Classes',
                'active' => (int) ($stats['classes'] ?? 0),
                'total' => max(0, (int) ($stats['classes'] ?? 0)),
                'icon' => 'classes',
                'tone' =>
                    'from-violet-100 to-white text-violet-600 dark:from-violet-500/20 dark:to-slate-900 dark:text-violet-300',
            ],
            [
                'label' => 'Present',
                'activeLabel' => 'Present',
                'active' => (int) ($stats['present'] ?? 0),
                'total' => $recordTotal,
                'icon' => 'present',
                'tone' =>
                    'from-emerald-100 to-white text-emerald-600 dark:from-emerald-500/20 dark:to-slate-900 dark:text-emerald-300',
            ],
            [
                'label' => 'Absent',
                'activeLabel' => 'Absent',
                'active' => (int) ($stats['absent'] ?? 0),
                'total' => $recordTotal,
                'icon' => 'absent',
                'tone' =>
                    'from-rose-100 to-white text-rose-600 dark:from-rose-500/20 dark:to-slate-900 dark:text-rose-300',
            ],
            [
                'label' => 'Late',
                'activeLabel' => 'Late',
                'active' => (int) ($stats['late'] ?? 0),
                'total' => $recordTotal,
                'icon' => 'late',
                'tone' =>
                    'from-amber-100 to-white text-amber-600 dark:from-amber-500/20 dark:to-slate-900 dark:text-amber-300',
            ],
        ];

        $panelClass =
            'attendence-reveal attendence-float rounded-[28px] border border-slate-200/80 bg-white/95 p-5 shadow-[0_24px_55px_-42px_rgba(15,23,42,0.75)] ring-1 ring-slate-200/70 dark:border-slate-700/80 dark:bg-slate-900/95 dark:ring-slate-700/80 dark:shadow-[0_24px_70px_-42px_rgba(0,0,0,0.9)]';

        $inputClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20';

        $filterTitleClass = 'text-xl font-bold text-slate-950 dark:text-white';

        $tableHeadClass =
            'admin-table-head sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400';

        $tableBodyClass = 'divide-y divide-slate-100 bg-white dark:divide-slate-700 dark:bg-slate-900';
    @endphp

    <div class="attendence-stage attendance-page mx-auto max-w-[1500px] space-y-6 pb-8 text-slate-900 dark:text-slate-100">
        <x-admin.page-header reveal-class="attendence-reveal" delay="1" icon="attendance" title="Attendance Monitoring"
            subtitle="Admin view to check attendance records connected with teachers.">
            <x-slot:actions>
                <a href="{{ route('admin.attendance.teachers.index') }}"
                    class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-300 dark:hover:bg-indigo-500/25">
                    Open Teacher Attendance Check
                </a>
            </x-slot:actions>
        </x-admin.page-header>

        {{-- STAT CARDS --}}
        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-7">
            @foreach ($attendanceCards as $index => $card)
                @php
                    $cardTotal = max(1, (int) ($card['total'] ?? 0));
                    $cardActive = max(0, (int) ($card['active'] ?? 0));
                    $progress = (int) round(($cardActive / $cardTotal) * 100);
                @endphp

                <div class="attendance-stat-card attendence-reveal attendence-float min-h-[132px] rounded-[26px] border border-slate-200/80 bg-white/95 p-5 shadow-[0_24px_55px_-42px_rgba(15,23,42,0.75)] ring-1 ring-slate-200/70 backdrop-blur dark:border-slate-700/80 dark:bg-slate-900/95 dark:ring-slate-700/80"
                    style="--sd: {{ $index + 2 }};">
                    <div class="flex items-start justify-between gap-4">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br {{ $card['tone'] }}">
                            @switch($card['icon'])
                                @case('records')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                        <path d="M14 2v6h6" />
                                        <path d="M8 13h8" />
                                        <path d="M8 17h5" />
                                    </svg>
                                @break

                                @case('students')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M16 21v-2a4 4 0 0 0-8 0v2" />
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                @break

                                @case('teachers')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M6 21v-2a6 6 0 0 1 12 0v2" />
                                        <path d="M9 11h6" />
                                    </svg>
                                @break

                                @case('classes')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="m12 3 8 4-8 4-8-4 8-4Z" />
                                        <path d="m4 12 8 4 8-4" />
                                        <path d="m4 17 8 4 8-4" />
                                    </svg>
                                @break

                                @case('present')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M20 6 9 17l-5-5" />
                                    </svg>
                                @break

                                @case('absent')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                @break

                                @default
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 7v5l3 2" />
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

        @if (!$hasAttendanceTable)
            <div class="attendence-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300"
                style="--sd: 2;">
                Attendance table is missing. Run <code>php artisan migrate</code> to enable this page.
            </div>
        @endif

        {{-- MAIN CARD --}}
        <section class="{{ $panelClass }}" style="--sd: 2;">
            <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-950 dark:text-white">Attendance List</h2>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Student attendance records connected with teachers
                        </p>
                    </div>

                    <button type="button" @click="filterOpen = true"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        <i class="fa-solid fa-filter text-xs"></i>
                        Filters
                    </button>
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
                                <a href="{{ route('admin.attendance.index') }}"
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

                        <form method="GET" action="{{ route('admin.attendance.index') }}"
                            class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">

                            <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                <section class="space-y-2">
                                    <h4 class="{{ $filterTitleClass }}">Date</h4>
                                    <input type="date" name="date" value="{{ $date }}"
                                        class="{{ $inputClass }}">
                                </section>

                                <section class="space-y-2">
                                    <h4 class="{{ $filterTitleClass }}">Class</h4>
                                    <select name="class_id" class="{{ $inputClass }}">
                                        <option value="all" {{ ($classId ?? 'all') === 'all' ? 'selected' : '' }}>
                                            All Classes
                                        </option>

                                        @foreach ($classes as $classOption)
                                            <option value="{{ $classOption->id }}"
                                                {{ ($classId ?? 'all') === (string) $classOption->id ? 'selected' : '' }}>
                                                {{ $classOption->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="{{ $filterTitleClass }}">Teacher</h4>
                                    <select name="teacher_id" class="{{ $inputClass }}">
                                        <option value="all" {{ ($teacherId ?? 'all') === 'all' ? 'selected' : '' }}>
                                            All Teachers
                                        </option>

                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->id }}"
                                                {{ ($teacherId ?? 'all') === (string) $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="{{ $filterTitleClass }}">Status</h4>
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
                                    <h4 class="{{ $filterTitleClass }}">Search</h4>
                                    <input type="text" name="q" value="{{ $search }}"
                                        placeholder="Search student, teacher, class, or remark"
                                        class="{{ $inputClass }}">
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

                    if (($classId ?? 'all') !== 'all') {
                        $selectedClass = collect($classes)->firstWhere('id', (int) $classId);
                        $selectedClassLabel = $selectedClass?->display_name;
                    }

                    $selectedTeacherLabel = null;

                    if (($teacherId ?? 'all') !== 'all') {
                        $selectedTeacher = collect($teachers)->firstWhere('id', (int) $teacherId);
                        $selectedTeacherLabel = $selectedTeacher?->name;
                    }
                @endphp

                {{-- ACTIVE FILTERS --}}
                <div class="mb-3 flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span
                        class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-indigo-700 dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-300">
                        Date: {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                    </span>

                    @if ($selectedClassLabel)
                        <span
                            class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-sky-700 dark:border-sky-400/20 dark:bg-sky-500/15 dark:text-sky-300">
                            Class: {{ $selectedClassLabel }}
                        </span>
                    @endif

                    @if ($selectedTeacherLabel)
                        <span
                            class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300">
                            Teacher: {{ $selectedTeacherLabel }}
                        </span>
                    @endif

                    @if (($status ?? 'all') !== 'all')
                        <span
                            class="inline-flex items-center rounded-full border border-violet-200 bg-violet-50 px-2.5 py-1 text-violet-700 dark:border-violet-400/20 dark:bg-violet-500/15 dark:text-violet-300">
                            Status: {{ $statusLabels[$status] ?? ucfirst((string) $status) }}
                        </span>
                    @endif

                    @if (($search ?? '') !== '')
                        <span
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-2.5 py-1 text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            Search: {{ $search }}
                        </span>
                    @endif
                </div>

                {{-- TABLE --}}
                <div
                    class="mt-1 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50/60 dark:border-slate-700 dark:bg-slate-800/60">
                    <div class="attendance-table-scroller max-h-[620px] overflow-auto">
                        <table class="admin-table attendance-table w-full min-w-[1180px] text-left text-sm">
                            <thead class="{{ $tableHeadClass }}">
                                <tr>
                                    <th class="w-[120px] px-3 py-3 font-semibold">Date</th>
                                    <th class="w-[230px] px-3 py-3 font-semibold">Student</th>
                                    <th class="w-[190px] px-3 py-3 font-semibold">Class</th>
                                    <th class="w-[120px] px-3 py-3 font-semibold">Status</th>
                                    <th class="w-[220px] px-3 py-3 font-semibold">Checked By Teacher</th>
                                    <th class="w-[160px] px-3 py-3 font-semibold">Checked At</th>
                                    <th class="w-[240px] px-3 py-3 font-semibold">Remark</th>
                                </tr>
                            </thead>

                            <tbody class="{{ $tableBodyClass }}">
                                @forelse ($records as $row)
                                    @php
                                        $statusKey = strtolower((string) ($row->status ?? ''));
                                        $statusText = $statusLabels[$statusKey] ?? ucfirst($statusKey);

                                        $statusClass = match ($statusKey) {
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

                                        $checkedAt = $row->checked_at ?: $row->created_at;
                                    @endphp

                                    <tr
                                        class="align-top transition odd:bg-slate-50/30 hover:bg-slate-50/80 dark:odd:bg-slate-800/30 dark:hover:bg-slate-800/70">
                                        <td class="px-3 py-3 text-slate-700 dark:text-slate-300">
                                            {{ \Carbon\Carbon::parse($row->attendance_date)->format('M d, Y') }}
                                        </td>

                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-800 dark:text-slate-100">
                                                {{ $row->student_name ?? '-' }}
                                            </div>

                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $row->student_email ?? '-' }}
                                            </div>

                                            <div class="text-xs text-slate-400 dark:text-slate-500">
                                                ID #{{ str_pad((string) ($row->student_id ?? 0), 7, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </td>

                                        <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                            <div class="font-medium text-slate-700 dark:text-slate-200">
                                                {{ $row->class_label ?? 'Unassigned' }}
                                            </div>

                                            <div class="text-xs text-slate-400 dark:text-slate-500">
                                                Room: {{ $row->class_room ?: '-' }}
                                            </div>
                                        </td>

                                        <td class="px-3 py-3">
                                            <span
                                                class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                            <div class="font-medium text-slate-700 dark:text-slate-200">
                                                {{ $row->teacher_name ?? 'Unknown' }}
                                            </div>

                                            <div class="text-xs text-slate-400 dark:text-slate-500">
                                                {{ $row->teacher_email ?? '-' }}
                                            </div>
                                        </td>

                                        <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
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

                                        <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                            @if (!empty($row->remark))
                                                <div class="max-w-[220px] break-words text-sm">
                                                    {{ $row->remark }}
                                                </div>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="px-3 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                            No attendance records found for selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (is_object($records) && method_exists($records, 'links'))
                    <div class="attendance-pagination mt-5 text-slate-700 dark:text-slate-300">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
