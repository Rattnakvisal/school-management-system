@extends('layout.admin.navbar.navbar')

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
                'tone' => 'from-indigo-100 to-white text-indigo-600',
            ],
            [
                'label' => 'Students',
                'activeLabel' => 'Students',
                'active' => (int) ($stats['students'] ?? 0),
                'total' => max(0, (int) ($stats['students'] ?? 0)),
                'icon' => 'students',
                'tone' => 'from-sky-100 to-white text-sky-600',
            ],
            [
                'label' => 'Teachers',
                'activeLabel' => 'Teachers',
                'active' => (int) ($stats['teachers'] ?? 0),
                'total' => max(0, (int) ($stats['teachers'] ?? 0)),
                'icon' => 'teachers',
                'tone' => 'from-emerald-100 to-white text-emerald-600',
            ],
            [
                'label' => 'Classes',
                'activeLabel' => 'Classes',
                'active' => (int) ($stats['classes'] ?? 0),
                'total' => max(0, (int) ($stats['classes'] ?? 0)),
                'icon' => 'classes',
                'tone' => 'from-violet-100 to-white text-violet-600',
            ],
            [
                'label' => 'Present',
                'activeLabel' => 'Present',
                'active' => (int) ($stats['present'] ?? 0),
                'total' => $recordTotal,
                'icon' => 'present',
                'tone' => 'from-emerald-100 to-white text-emerald-600',
            ],
            [
                'label' => 'Absent',
                'activeLabel' => 'Absent',
                'active' => (int) ($stats['absent'] ?? 0),
                'total' => $recordTotal,
                'icon' => 'absent',
                'tone' => 'from-rose-100 to-white text-rose-600',
            ],
            [
                'label' => 'Late',
                'activeLabel' => 'Late',
                'active' => (int) ($stats['late'] ?? 0),
                'total' => $recordTotal,
                'icon' => 'late',
                'tone' => 'from-amber-100 to-white text-amber-600',
            ],
        ];
    @endphp

    <div class="attendence-stage space-y-6">
        <x-admin.page-header reveal-class="attendence-reveal" delay="1" icon="attendance" title="Attendance Monitoring"
            subtitle="Admin view to check attendance records connected with teachers.">
            <x-slot:actions>
                <a href="{{ route('admin.attendance.teachers.index') }}"
                    class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                    Open Teacher Attendance Check
                </a>
            </x-slot:actions>
        </x-admin.page-header>

        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-7">
            @foreach ($attendanceCards as $index => $card)
                @php
                    $cardTotal = max(1, (int) ($card['total'] ?? 0));
                    $cardActive = max(0, (int) ($card['active'] ?? 0));
                    $progress = (int) round(($cardActive / $cardTotal) * 100);
                @endphp
                <div class="attendance-stat-card attendence-reveal attendence-float min-h-[132px] rounded-[26px] border border-white/80 bg-white/90 p-5 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.55)] backdrop-blur"
                    style="--sd: {{ $index + 2 }};">
                    <div class="flex items-start justify-between gap-4">
                        <span
                            class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br {{ $card['tone'] }}">
                            @switch($card['icon'])
                                @case('records')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                        <path d="M14 2v6h6" />
                                        <path d="M8 13h8" />
                                        <path d="M8 17h5" />
                                    </svg>
                                @break

                                @case('students')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M16 21v-2a4 4 0 0 0-8 0v2" />
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                @break

                                @case('teachers')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M6 21v-2a6 6 0 0 1 12 0v2" />
                                        <path d="M9 11h6" />
                                    </svg>
                                @break

                                @case('classes')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="m12 3 8 4-8 4-8-4 8-4Z" />
                                        <path d="m4 12 8 4 8-4" />
                                        <path d="m4 17 8 4 8-4" />
                                    </svg>
                                @break

                                @case('present')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M20 6 9 17l-5-5" />
                                    </svg>
                                @break

                                @case('absent')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                @break

                                @default
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 7v5l3 2" />
                                    </svg>
                            @endswitch
                        </span>

                        <span class="text-slate-300" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                            </svg>
                        </span>
                    </div>

                    <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                        <span>{{ number_format($cardActive) }}</span>
                        <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                            {{ number_format((int) ($card['total'] ?? 0)) }}</span>
                    </div>
                    <div class="mt-1 text-sm font-bold text-slate-600">{{ $card['label'] }}</div>
                    <div class="mt-1 text-[11px] font-semibold text-slate-400">
                        {{ $card['activeLabel'] }}: {{ number_format($cardActive) }}
                    </div>
                    <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                        <span class="block h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400"
                            style="width: {{ min(100, max(0, $progress)) }}%"></span>
                    </div>
                </div>
            @endforeach
        </section>

        @if (!$hasAttendanceTable)
            <div class="attendence-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                Attendance table is missing. Run <code>php artisan migrate</code> to enable this page.
            </div>
        @endif

        <section class="dashboard-card dash-hover attendence-reveal attendence-float rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
            style="--sd: 2;">
            <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                <div class="dashboard-card-header flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Attendance List</h2>
                        <p class="dashboard-card-meta mt-1 text-xs text-slate-500">
                            Student attendance records connected with teachers
                        </p>
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
                                <a href="{{ route('admin.attendance.index') }}"
                                    class="text-sm font-semibold text-slate-500 hover:text-slate-700">Clear All</a>
                                <button type="button" @click="filterOpen = false"
                                    class="text-2xl font-bold leading-none text-slate-700 hover:text-slate-900"
                                    aria-label="Close filters">&times;</button>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex min-h-0 flex-1 flex-col"
                            @submit="filterOpen = false">
                            <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Date</h4>
                                    <input type="date" name="date" value="{{ $date }}"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Class</h4>
                                    <select name="class_id"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="all" {{ ($classId ?? 'all') === 'all' ? 'selected' : '' }}>All
                                            Classes</option>
                                        @foreach ($classes as $classOption)
                                            <option value="{{ $classOption->id }}"
                                                {{ ($classId ?? 'all') === (string) $classOption->id ? 'selected' : '' }}>
                                                {{ $classOption->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Teacher</h4>
                                    <select name="teacher_id"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="all" {{ ($teacherId ?? 'all') === 'all' ? 'selected' : '' }}>All
                                            Teachers</option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->id }}"
                                                {{ ($teacherId ?? 'all') === (string) $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                    <select name="status"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        @foreach ($statusLabels as $statusKey => $statusLabel)
                                            <option value="{{ $statusKey }}"
                                                {{ ($status ?? 'all') === $statusKey ? 'selected' : '' }}>
                                                {{ $statusLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                    <input type="text" name="q" value="{{ $search }}"
                                        placeholder="Search student, teacher, class, or remark"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
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
                <div class="mb-3 flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-indigo-700">
                        Date: {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                    </span>
                    @if ($selectedClassLabel)
                        <span class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-sky-700">
                            Class: {{ $selectedClassLabel }}
                        </span>
                    @endif
                    @if ($selectedTeacherLabel)
                        <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700">
                            Teacher: {{ $selectedTeacherLabel }}
                        </span>
                    @endif
                    @if (($status ?? 'all') !== 'all')
                        <span class="inline-flex items-center rounded-full border border-violet-200 bg-violet-50 px-2.5 py-1 text-violet-700">
                            Status: {{ $statusLabels[$status] ?? ucfirst((string) $status) }}
                        </span>
                    @endif
                    @if (($search ?? '') !== '')
                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-2.5 py-1 text-slate-600">
                            Search: {{ $search }}
                        </span>
                    @endif
                </div>

                <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50/60">
                    <div class="max-h-[620px] overflow-auto">
                        <table class="w-full min-w-[1180px] text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
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
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($records as $row)
                                    @php
                                        $statusKey = strtolower((string) ($row->status ?? ''));
                                        $statusText = $statusLabels[$statusKey] ?? ucfirst($statusKey);
                                        $statusClass = match ($statusKey) {
                                            'present' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                            'absent' => 'border-rose-200 bg-rose-50 text-rose-700',
                                            'late' => 'border-amber-200 bg-amber-50 text-amber-700',
                                            'excused' => 'border-sky-200 bg-sky-50 text-sky-700',
                                            default => 'border-slate-200 bg-slate-50 text-slate-700',
                                        };
                                        $checkedAt = $row->checked_at ?: $row->created_at;
                                    @endphp
                                    <tr class="align-top transition hover:bg-slate-50/80 odd:bg-slate-50/30">
                                        <td class="px-3 py-3 text-slate-700">
                                            {{ \Carbon\Carbon::parse($row->attendance_date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-800">{{ $row->student_name ?? '-' }}</div>
                                            <div class="text-xs text-slate-500">{{ $row->student_email ?? '-' }}</div>
                                            <div class="text-xs text-slate-400">ID #{{ str_pad((string) ($row->student_id ?? 0), 7, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            <div class="font-medium text-slate-700">{{ $row->class_label ?? 'Unassigned' }}</div>
                                            <div class="text-xs text-slate-400">Room: {{ $row->class_room ?: '-' }}</div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            <div class="font-medium text-slate-700">{{ $row->teacher_name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-slate-400">{{ $row->teacher_email ?? '-' }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            @if ($checkedAt)
                                                <div class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($checkedAt)->format('M d, Y h:i A') }}</div>
                                                <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($checkedAt)->diffForHumans() }}</div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            @if (!empty($row->remark))
                                                <div class="max-w-[220px] break-words text-sm">{{ $row->remark }}</div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-10 text-center text-sm text-slate-500">
                                            No attendance records found for selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (is_object($records) && method_exists($records, 'links'))
                    <div class="mt-5">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
