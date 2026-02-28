@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="attendence-stage space-y-6">
        <x-admin.page-header reveal-class="attendence-reveal" delay="1" icon="attendance" title="Teacher Attendance Check"
            subtitle="Admin check and update teacher attendance records by date.">
            <x-slot:stats>
                <span class="admin-page-stat">Teachers: {{ number_format($stats['teachers'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--sky">Checked:
                    {{ number_format($stats['checked'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--emerald">Present:
                    {{ number_format($stats['present'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--rose">Absent:
                    {{ number_format($stats['absent'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--amber">Late:
                    {{ number_format($stats['late'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--cyan">Not Marked:
                    {{ number_format($stats['not_marked'] ?? 0) }}</span>
            </x-slot:stats>
            <x-slot:actions>
                <a href="{{ route('admin.attendance.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                    Back To Student Attendance Monitor
                </a>
            </x-slot:actions>
        </x-admin.page-header>

        @if (session('success'))
            <div class="attendence-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="attendence-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('error'))
            <div class="attendence-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="attendence-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                {{ $errors->first() }}
            </div>
        @endif

        @if (!$hasTeacherAttendanceTable)
            <div class="attendence-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                Teacher attendance table is missing. Run <code>php artisan migrate</code> to enable this page.
            </div>
        @endif

        <section
            class="attendence-reveal attendence-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200"
            style="--sd: 3;" x-data="{ filterOpen: false }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-indigo-700">
                        Date: {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                    </span>
                    @if (($status ?? 'all') !== 'all')
                        <span class="rounded-full border border-violet-200 bg-violet-50 px-3 py-1.5 text-violet-700">
                            Status: {{ $statusLabels[$status] ?? ucfirst((string) $status) }}
                        </span>
                    @endif
                    @if (($search ?? '') !== '')
                        <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-slate-600">
                            Search: {{ $search }}
                        </span>
                    @endif
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
                            <a href="{{ route('admin.attendance.teachers.index') }}"
                                class="text-sm font-semibold text-slate-500 hover:text-slate-700">Clear All</a>
                            <button type="button" @click="filterOpen = false"
                                class="text-2xl font-bold leading-none text-slate-700 hover:text-slate-900"
                                aria-label="Close filters">&times;</button>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.attendance.teachers.index') }}"
                        class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                            <section class="space-y-2">
                                <h4 class="text-xl font-bold text-slate-900">Date</h4>
                                <input type="date" name="date" value="{{ $date }}"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
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
                                    placeholder="Search teacher name or email"
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

            <form method="POST" action="{{ route('admin.attendance.teachers.store') }}" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $date }}">

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-slate-600">
                        Teacher rows: {{ number_format(($teachers ?? collect())->count()) }}
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" data-set-all-status="present"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                            Set All Present
                        </button>
                        <button type="button" data-set-all-status="absent"
                            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                            Set All Absent
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-500">
                            Save Teacher Attendance
                        </button>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="max-h-[620px] overflow-auto">
                        <table class="w-full min-w-[980px] text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="w-[300px] px-3 py-3 font-semibold">Teacher</th>
                                    <th class="w-[150px] px-3 py-3 font-semibold">Current Status</th>
                                    <th class="w-[210px] px-3 py-3 font-semibold">Check Status</th>
                                    <th class="w-[300px] px-3 py-3 font-semibold">Remark</th>
                                    <th class="w-[220px] px-3 py-3 font-semibold">Checked At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($teachers as $teacher)
                                    @php
                                        $record = $attendanceByTeacher->get((int) $teacher->id);
                                        $currentStatus = strtolower((string) ($record?->status ?? ''));
                                        $selectedStatus = old(
                                            'attendance.' . $teacher->id . '.status',
                                            $currentStatus !== '' ? $currentStatus : 'present',
                                        );
                                        $remarkValue = old(
                                            'attendance.' . $teacher->id . '.remark',
                                            (string) ($record?->remark ?? ''),
                                        );
                                        $statusClass = match ($currentStatus) {
                                            'present' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                            'absent' => 'border-rose-200 bg-rose-50 text-rose-700',
                                            'late' => 'border-amber-200 bg-amber-50 text-amber-700',
                                            'excused' => 'border-sky-200 bg-sky-50 text-sky-700',
                                            default => 'border-slate-200 bg-slate-50 text-slate-700',
                                        };
                                        $checkedAt = $record?->checked_at ?? $record?->updated_at;
                                    @endphp
                                    <tr class="align-top transition hover:bg-slate-50/80 odd:bg-slate-50/30">
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-800">{{ $teacher->name }}</div>
                                            <div class="text-xs text-slate-500">{{ $teacher->email }}</div>
                                            <div class="text-xs text-slate-400">ID #{{ str_pad((string) ($teacher->id ?? 0), 7, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td class="px-3 py-3">
                                            @if ($currentStatus !== '' && isset($statusLabels[$currentStatus]))
                                                <span
                                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
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
                                            <select name="attendance[{{ $teacher->id }}][status]"
                                                class="js-teacher-status w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                @foreach ($statusLabels as $statusKey => $statusLabel)
                                                    @continue($statusKey === 'all')
                                                    <option value="{{ $statusKey }}"
                                                        {{ $selectedStatus === $statusKey ? 'selected' : '' }}>
                                                        {{ $statusLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" name="attendance[{{ $teacher->id }}][remark]"
                                                value="{{ $remarkValue }}" maxlength="255"
                                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                placeholder="Optional note">
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            @if ($checkedAt)
                                                <div class="font-medium text-slate-700">
                                                    {{ \Carbon\Carbon::parse($checkedAt)->format('M d, Y h:i A') }}</div>
                                                <div class="text-xs text-slate-400">
                                                    {{ \Carbon\Carbon::parse($checkedAt)->diffForHumans() }}</div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-10 text-center text-sm text-slate-500">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusFields = Array.from(document.querySelectorAll('.js-teacher-status'));
            document.querySelectorAll('[data-set-all-status]').forEach((button) => {
                button.addEventListener('click', () => {
                    const status = String(button.getAttribute('data-set-all-status') || '');
                    if (!status) return;

                    statusFields.forEach((field) => {
                        const option = field.querySelector(`option[value="${status}"]`);
                        if (option) {
                            field.value = status;
                        }
                    });
                });
            });
        });
    </script>
@endsection
