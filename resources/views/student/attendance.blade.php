@extends('layout.students.navbar')

@section('page')
    <div class="student-stage space-y-6">
        <section class="student-reveal student-float admin-page-header" style="--sd: 1;">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="admin-page-header__eyebrow">Student Portal</div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">Attendance</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        View each attendance record by subject, teacher, and date.
                    </p>
                </div>

                <div class="admin-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">Total: {{ number_format($summary['total'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Present:
                        {{ number_format($summary['present'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--rose">Absent:
                        {{ number_format($summary['absent'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--amber">Late:
                        {{ number_format($summary['late'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--sky">Excused:
                        {{ number_format($summary['excused'] ?? 0) }}</span>
                </div>
            </div>
        </section>

        <section class="student-reveal student-float rounded-3xl border border-slate-200 bg-white p-5 shadow-sm" style="--sd: 2;">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Filters</h2>
                    <p class="mt-1 text-xs font-semibold text-slate-500">
                        Narrow attendance by subject, date, or status.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('student.attendance.index') }}" class="mt-4 grid gap-4 md:grid-cols-4">
                <div class="space-y-2">
                    <label for="attendance_subject_id" class="text-sm font-semibold text-slate-700">Subject</label>
                    <select id="attendance_subject_id" name="subject_id"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <option value="all" {{ ($subjectId ?? 'all') === 'all' ? 'selected' : '' }}>All subjects</option>
                        @foreach ($subjectOptions ?? [] as $subjectOption)
                            <option value="{{ $subjectOption->id }}" {{ ($subjectId ?? 'all') === (string) $subjectOption->id ? 'selected' : '' }}>
                                {{ $subjectOption->name }}{{ !empty($subjectOption->code) ? ' (' . $subjectOption->code . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="attendance_date" class="text-sm font-semibold text-slate-700">Date</label>
                    <input id="attendance_date" type="date" name="date" value="{{ $selectedDate }}"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                </div>

                <div class="space-y-2">
                    <label for="attendance_status" class="text-sm font-semibold text-slate-700">Status</label>
                    <select id="attendance_status" name="status"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        @foreach ($statusLabels as $statusKey => $statusLabel)
                            <option value="{{ $statusKey }}" {{ ($statusFilter ?? 'all') === $statusKey ? 'selected' : '' }}>
                                {{ $statusLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="inline-flex flex-1 items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-500">
                        Apply
                    </button>
                    <a href="{{ route('student.attendance.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        <section class="student-reveal student-float rounded-3xl border border-slate-200 bg-white p-5 shadow-sm" style="--sd: 3;">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Attendance Records</h2>
                    <p class="mt-1 text-xs font-semibold text-slate-500">
                        Each row shows the teacher responsible for that subject attendance.
                    </p>
                </div>
            </div>

            @if (!$hasAttendanceTable)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
                    Attendance table is missing. Run `php artisan migrate` to enable attendance records.
                </div>
            @else
                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="max-h-[620px] overflow-auto">
                        <table class="w-full min-w-[980px] text-left text-sm">
                            <thead class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-3 py-3 font-semibold">Date</th>
                                    <th class="px-3 py-3 font-semibold">Subject</th>
                                    <th class="px-3 py-3 font-semibold">Teacher</th>
                                    <th class="px-3 py-3 font-semibold">Status</th>
                                    <th class="px-3 py-3 font-semibold">Remark</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($records as $record)
                                    @php
                                        $statusKey = strtolower((string) ($record->status ?? ''));
                                        $statusClass = match ($statusKey) {
                                            'present' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                            'absent' => 'border-red-200 bg-red-50 text-red-700',
                                            'late' => 'border-amber-200 bg-amber-50 text-amber-700',
                                            'excused' => 'border-sky-200 bg-sky-50 text-sky-700',
                                            default => 'border-slate-200 bg-slate-50 text-slate-700',
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50/80">
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-900">
                                                {{ $record->attendance_date?->format('M d, Y') ?? 'N/A' }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $record->checked_at?->format('h:i A') ?? 'Not checked time' }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-900">
                                                {{ $record->subject?->name ?: 'General attendance' }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $record->schoolClass?->display_name ?? 'Class not set' }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-900">
                                                {{ $record->teacher?->name ?: 'Teacher not assigned' }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $record->teacher?->email ?: 'No email' }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                {{ ucfirst($statusKey ?: 'unknown') }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            {{ $record->remark ?: 'No remark' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-10 text-center text-sm text-slate-500">
                                            No attendance records found for the selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (method_exists($records, 'links'))
                    <div class="mt-4">
                        {{ $records->links() }}
                    </div>
                @endif
            @endif
        </section>
    </div>
@endsection
