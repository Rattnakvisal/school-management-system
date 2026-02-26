@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="attendence-stage space-y-6">
        <x-admin.page-header reveal-class="attendence-reveal" delay="1" icon="attendance" title="Attendance Monitoring"
            subtitle="Admin view to check attendance records connected with teachers.">
            <x-slot:stats>
                <span class="admin-page-stat">Records: {{ number_format($stats['records'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--sky">Students:
                    {{ number_format($stats['students'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--emerald">Teachers:
                    {{ number_format($stats['teachers'] ?? 0) }}</span>
                <span class="admin-page-stat">Classes: {{ number_format($stats['classes'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--emerald">Present:
                    {{ number_format($stats['present'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--rose">Absent:
                    {{ number_format($stats['absent'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--amber">Late:
                    {{ number_format($stats['late'] ?? 0) }}</span>
            </x-slot:stats>
        </x-admin.page-header>

        @if (!$hasAttendanceTable)
            <div class="attendence-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                Attendance table is missing. Run <code>php artisan migrate</code> to enable this page.
            </div>
        @endif

        <section class="attendence-reveal attendence-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200"
            style="--sd: 2;">
            <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-black text-slate-900">Attendance List</h2>
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

                <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200">
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
