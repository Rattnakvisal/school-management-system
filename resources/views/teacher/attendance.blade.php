@extends('layout.teacher.navbar')

@section('page')
    @php
        $selectedClass = collect($classes ?? [])->firstWhere('id', (int) ($classId ?? 0));
    @endphp

    <div class="teacher-time-stage space-y-6">
        <section class="teacher-time-reveal admin-page-header teacher-page-header" style="--sd: 1;">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Attendance Check</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        Mark daily attendance for students in your classes.
                    </p>
                </div>

                <div class="teacher-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">Students: {{ number_format($summary['students'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Present:
                        {{ number_format($summary['present'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--rose">Absent:
                        {{ number_format($summary['absent'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--amber">Late:
                        {{ number_format($summary['late'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--rose">Not Marked:
                        {{ number_format($summary['not_marked'] ?? 0) }}</span>
                </div>
            </div>
        </section>

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
            style="--sd: 3;" x-data="{ filterOpen: false }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-slate-700">
                        Class: {{ $selectedClass?->display_name ?? 'Not selected' }}
                    </span>
                    <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-indigo-700">
                        Date: {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
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
                        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                            <section class="space-y-2">
                                <h4 class="text-xl font-bold text-slate-900">Class</h4>
                                <select name="class_id"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    @foreach ($classes as $classOption)
                                        <option value="{{ $classOption->id }}"
                                            {{ ($classId ?? '') === (string) $classOption->id ? 'selected' : '' }}>
                                            {{ $classOption->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </section>

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
                <div class="mt-5">
                    <h3 class="text-sm font-black text-slate-900">Classes Grid</h3>
                    <p class="mt-1 text-xs text-slate-500">Click a class to load users table with class times and subjects.
                    </p>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($classes as $classOption)
                            @php
                                $isActiveClass = ($classId ?? '') === (string) $classOption->id;
                                $classStatus = $classAttendanceStatus[(int) ($classOption->id ?? 0)] ?? null;
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
                                $statusLabel = $isSaved ? 'Saved' : ($isPending ? 'Check Attendance' : 'No Students');
                                $slotPreview = collect($classOption->studySchedules ?? [])->take(2);
                                $subjectPreview = collect($classOption->subjects ?? [])->take(2);
                                $moreSlots = max(
                                    0,
                                    (int) (($classOption->class_slots_count ?? 0) - $slotPreview->count()),
                                );
                                $moreSubjects = max(
                                    0,
                                    (int) (($classOption->taught_subjects_count ?? 0) - $subjectPreview->count()),
                                );
                            @endphp
                            <a href="{{ route('teacher.attendance.index', ['class_id' => $classOption->id, 'date' => $selectedDate]) }}"
                                class="rounded-2xl border px-4 py-3 transition {{ $cardColorClass }}{{ $activeRingClass }}">
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
                                    <span class="rounded-full border border-sky-200 bg-sky-50 px-2 py-0.5 text-sky-700">
                                        Users: {{ number_format($classOption->students_count ?? 0) }}
                                    </span>
                                    <span
                                        class="rounded-full border px-2 py-0.5 {{ $isSaved ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($isPending ? 'border-red-200 bg-red-50 text-red-700' : 'border-slate-200 bg-white text-slate-600') }}">
                                        Checked: {{ number_format($checkedCount) }}/{{ number_format($studentsCount) }}
                                    </span>
                                    <span
                                        class="rounded-full border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-indigo-700">
                                        Subjects: {{ number_format($classOption->taught_subjects_count ?? 0) }}
                                    </span>
                                    <span
                                        class="rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-amber-700">
                                        Times: {{ number_format($classOption->class_slots_count ?? 0) }}
                                    </span>
                                </div>

                                <div class="mt-3 space-y-1.5">
                                    <div class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Class Times
                                    </div>
                                    @if ($slotPreview->isEmpty())
                                        <div class="text-[11px] font-semibold text-slate-400">No class times</div>
                                    @else
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($slotPreview as $slot)
                                                @php
                                                    $periodKey = strtolower((string) ($slot->period ?? 'custom'));
                                                    $periodLabel = $periodLabels[$periodKey] ?? ucfirst($periodKey);
                                                    $startText = $slot->start_time
                                                        ? \Carbon\Carbon::parse($slot->start_time)->format('h:i A')
                                                        : '--';
                                                    $endText = $slot->end_time
                                                        ? \Carbon\Carbon::parse($slot->end_time)->format('h:i A')
                                                        : '--';
                                                    $slotText = $periodLabel . ' ' . $startText . '->' . $endText;
                                                    if (($hasClassDayColumn ?? false) && isset($slot->day_of_week)) {
                                                        $slotDayKey = strtolower(
                                                            (string) ($slot->day_of_week ?? 'all'),
                                                        );
                                                        $slotDayLabel = $dayLabels[$slotDayKey] ?? ucfirst($slotDayKey);
                                                        $slotText = $slotDayLabel . ' | ' . $slotText;
                                                    }
                                                @endphp
                                                <span
                                                    class="rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700">
                                                    {{ $slotText }}
                                                </span>
                                            @endforeach
                                            @if ($moreSlots > 0)
                                                <span
                                                    class="rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-500">
                                                    +{{ $moreSlots }} more
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-3 space-y-1.5">
                                    <div class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Subjects
                                    </div>
                                    @if ($subjectPreview->isEmpty())
                                        <div class="text-[11px] font-semibold text-slate-400">No subjects</div>
                                    @else
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($subjectPreview as $subject)
                                                <span
                                                    class="rounded-lg border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">
                                                    {{ $subject->name }}
                                                </span>
                                            @endforeach
                                            @if ($moreSubjects > 0)
                                                <span
                                                    class="rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-500">
                                                    +{{ $moreSubjects }} more
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (($classId ?? '') === '')
                <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center">
                    <div class="text-sm font-black text-slate-800">No class selected yet</div>
                    <div class="mt-1 text-sm text-slate-500">
                        Click a class card above to show users table and check attendance.
                    </div>
                </div>
            @else
                @php
                    $selectedClassStatus = $classAttendanceStatus[(int) ($classId ?? 0)] ?? null;
                    $isSelectedClassSaved = ($selectedClassStatus['state'] ?? '') === 'saved';
                @endphp
                <form method="POST" action="{{ route('teacher.attendance.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="school_class_id" value="{{ $classId }}">
                    <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="text-sm font-semibold text-slate-600">
                            User rows: {{ number_format($students->count()) }}
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" data-set-all-status="present"
                                {{ $isSelectedClassSaved ? 'disabled' : '' }}
                                class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50">
                                Set All Present
                            </button>
                            <button type="button" data-set-all-status="absent"
                                {{ $isSelectedClassSaved ? 'disabled' : '' }}
                                class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-50">
                                Set All Absent
                            </button>
                            <button type="submit" {{ !$hasAttendanceTable || $isSelectedClassSaved ? 'disabled' : '' }}
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50">
                                Save Attendance
                            </button>
                        </div>
                    </div>
                    @if ($isSelectedClassSaved)
                        <p class="text-xs font-semibold text-emerald-700">
                            Attendance saved successfully. Check status cannot be changed.
                        </p>
                    @endif
                    <p class="text-xs font-semibold text-slate-500">
                        Saving attendance for <span
                            class="text-slate-700">{{ $selectedClass?->display_name ?? 'N/A' }}</span>
                        on <span
                            class="text-slate-700">{{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</span>.
                    </p>

                    <div class="overflow-hidden rounded-2xl border border-slate-200">
                        <div class="max-h-[560px] overflow-auto">
                            <table class="w-full min-w-[1080px] text-left text-sm">
                                <thead
                                    class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-3 py-3 font-semibold">Users</th>
                                        <th class="px-3 py-3 font-semibold">Email</th>
                                        <th class="px-3 py-3 font-semibold">Current
                                            ({{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }})</th>
                                        <th class="px-3 py-3 font-semibold">Check Status</th>
                                        <th class="px-3 py-3 font-semibold">Remark</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse ($students as $student)
                                        @php
                                            $record = $attendanceByStudent->get($student->id);
                                            $currentStatus = strtolower((string) ($record?->status ?? ''));
                                            $selectedStatus = old(
                                                'attendance.' . $student->id . '.status',
                                                $currentStatus !== '' ? $currentStatus : 'present',
                                            );
                                            $remarkValue = old(
                                                'attendance.' . $student->id . '.remark',
                                                (string) ($record?->remark ?? ''),
                                            );
                                        @endphp
                                        <tr class="js-attendance-row align-top hover:bg-slate-50/80"
                                            data-student-name="{{ $student->name }}">
                                            <td class="px-3 py-3">
                                                <div class="font-semibold text-slate-900">{{ $student->name }}</div>
                                                <div class="text-xs text-slate-500">ID #{{ $student->formatted_id }}</div>
                                            </td>
                                            <td class="px-3 py-3 text-slate-700">{{ $student->email }}</td>
                                            <td class="px-3 py-3">
                                                @if ($currentStatus !== '' && isset($statusLabels[$currentStatus]))
                                                    <span
                                                        class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                        {{ $statusLabels[$currentStatus] }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center rounded-full border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                        Not Checked Yet
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="space-y-2">
                                                    <select name="attendance[{{ $student->id }}][status]"
                                                        {{ $isSelectedClassSaved ? 'disabled' : '' }}
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
                                                                {{ $isSelectedClassSaved ? 'disabled' : '' }}
                                                                aria-pressed="{{ $isActiveStatus ? 'true' : 'false' }}"
                                                                class="js-status-box rounded-lg border px-2 py-1.5 text-xs font-semibold transition disabled:cursor-not-allowed disabled:opacity-50 {{ $isActiveStatus ? 'border-indigo-300 bg-indigo-50 text-indigo-700 shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-indigo-200 hover:bg-indigo-50/50 hover:text-indigo-700' }}">
                                                                {{ $statusLabel }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3">
                                                <input type="text" name="attendance[{{ $student->id }}][remark]"
                                                    value="{{ $remarkValue }}" maxlength="255"
                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                    placeholder="Optional note">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-3 py-10 text-center text-sm text-slate-500">
                                                No users found for this class and filter.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
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
