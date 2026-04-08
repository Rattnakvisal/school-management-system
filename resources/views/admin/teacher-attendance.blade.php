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
                <span class="admin-page-stat admin-page-stat--indigo">Law Requests:
                    {{ number_format($stats['law_requests'] ?? 0) }}</span>
            </x-slot:stats>
            <x-slot:actions>
                <a href="{{ route('admin.attendance.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                    Back To Student Attendance Monitor
                </a>
            </x-slot:actions>
        </x-admin.page-header>

        @if (session('success'))
            <div class="js-inline-flash attendence-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="js-inline-flash attendence-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('error'))
            <div class="js-inline-flash attendence-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="js-inline-flash attendence-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
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

        @if (($lawRequestSummary['pending'] ?? 0) > 0)
            <div class="attendence-reveal rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700"
                style="--sd: 2;">
                Law requests found for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}:
                {{ number_format($lawRequestSummary['total'] ?? 0) }} teacher(s)
                (Approved: {{ number_format($lawRequestSummary['approved'] ?? 0) }},
                Pending: {{ number_format($lawRequestSummary['pending'] ?? 0) }}).
                Attendance will auto-save as <strong>Excused</strong> only for <strong>Approved</strong> requests.
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

            <form method="POST" action="{{ route('admin.attendance.teachers.store') }}" class="js-admin-teacher-attendance-form mt-5 space-y-4">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $date }}">

                @if (!($hasUnlockedTeachers ?? true) && ($teachers ?? collect())->isNotEmpty())
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                        Attendance saved successfully for this date. Status cannot be changed again.
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-slate-600">
                        Teacher rows: {{ number_format(($teachers ?? collect())->count()) }}
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" data-set-all-status="present"
                            {{ !($hasUnlockedTeachers ?? true) ? 'disabled' : '' }}
                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50">
                            Set All Present
                        </button>
                        <button type="button" data-set-all-status="absent"
                            {{ !($hasUnlockedTeachers ?? true) ? 'disabled' : '' }}
                            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-50">
                            Set All Absent
                        </button>
                        <button type="submit" {{ !$hasTeacherAttendanceTable || !($hasUnlockedTeachers ?? true) ? 'disabled' : '' }}
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50">
                            Save Teacher Attendance
                        </button>
                    </div>
                </div>

                <div class="admin-teacher-attendance-table-wrap overflow-hidden rounded-2xl border border-slate-200">
                    <div class="admin-teacher-attendance-table-scroller max-h-[620px] overflow-auto">
                        <table class="admin-teacher-attendance-table w-full min-w-[1180px] text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="admin-teacher-col-teacher px-3 py-3 font-semibold">Teacher</th>
                                    <th class="admin-teacher-col-law px-3 py-3 font-semibold">Law Request</th>
                                    <th class="admin-teacher-col-current px-3 py-3 font-semibold">Current Status</th>
                                    <th class="admin-teacher-col-check px-3 py-3 font-semibold">Check Status</th>
                                    <th class="admin-teacher-col-remark px-3 py-3 font-semibold">Remark</th>
                                    <th class="admin-teacher-col-checked px-3 py-3 font-semibold">Checked At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
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
                                        $hasActiveLawRequest = $lawRequest && ($isLawPending || $isLawApproved || $isLawRejected);
                                        $lawTypeLabel = $lawRequest
                                            ? ucfirst(str_replace('_', ' ', (string) ($lawRequest->law_type ?? 'request')))
                                            : '';
                                        $lawStatusClass = match ($lawStatus) {
                                            'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                            'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
                                            'rejected' => 'border-red-200 bg-red-50 text-red-700',
                                            default => 'border-slate-200 bg-slate-50 text-slate-700',
                                        };
                                        $lawRequestSubject = trim((string) ($lawRequest->subject ?? ''));
                                        $lawRequestSubjectTime = trim((string) ($lawRequest->subject_time ?? ''));
                                        if ($lawRequestSubjectTime === '' && str_contains($lawRequestSubject, '|')) {
                                            [$parsedSubject, $parsedTime] = array_pad(array_map('trim', explode('|', $lawRequestSubject, 2)), 2, '');
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
                                            $currentStatus !== '' ? $currentStatus : ($isLawApproved ? 'excused' : 'present'),
                                        );
                                        $remarkValue = old(
                                            'attendance.' . $teacher->id . '.remark',
                                            (string) ($record?->remark ?? $defaultLawRemark),
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
                                    <tr class="js-admin-teacher-row align-top transition hover:bg-slate-50/80 odd:bg-slate-50/30"
                                        data-teacher-name="{{ $teacher->name }}"
                                        data-teacher-id="{{ $teacher->id }}"
                                        data-law-request-id="{{ (int) ($lawRequest->id ?? 0) }}">
                                        <td class="admin-teacher-cell admin-teacher-col-teacher px-3 py-4">
                                            <div class="font-semibold text-slate-800">{{ $teacher->name }}</div>
                                            <div class="text-xs text-slate-500">{{ $teacher->email }}</div>
                                            <div class="text-xs text-slate-400">ID #{{ str_pad((string) ($teacher->id ?? 0), 7, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td class="admin-teacher-cell admin-teacher-col-law px-3 py-4">
                                            @if ($hasActiveLawRequest)
                                                <div class="space-y-1.5">
                                                    <div class="flex flex-wrap items-center gap-1.5">
                                                        <span data-law-request-badge
                                                            class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold whitespace-nowrap {{ $lawStatusClass }}">
                                                            {{ ucfirst($lawStatus) }}
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 whitespace-nowrap">
                                                            {{ $lawTypeLabel }}
                                                        </span>
                                                    </div>
                                                    @if ($lawRequestSubject !== '')
                                                        <div class="text-sm font-semibold text-slate-700 break-words">{{ $lawRequestSubject }}</div>
                                                    @endif
                                                    @if ($lawRequestSubjectTime !== '')
                                                        <div class="text-xs font-semibold text-indigo-600 break-words">{{ $lawRequestSubjectTime }}</div>
                                                    @endif
                                                    @if (!empty($lawRequest?->reason))
                                                        <div class="max-w-sm text-xs leading-relaxed text-slate-500 line-clamp-3">{{ $lawRequest->reason }}</div>
                                                    @endif
                                                    @if ($isLawPending)
                                                        <div class="flex flex-wrap gap-1.5 pt-1" data-law-request-actions>
                                                            <button type="submit"
                                                                formaction="{{ route('admin.attendance.teachers.law-requests.approve', $lawRequest) }}"
                                                                formmethod="POST" formnovalidate
                                                                data-law-action="approve"
                                                                class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 hover:bg-emerald-100">
                                                                Approve
                                                            </button>
                                                            <button type="submit"
                                                                formaction="{{ route('admin.attendance.teachers.law-requests.reject', $lawRequest) }}"
                                                                formmethod="POST" formnovalidate
                                                                data-law-action="reject"
                                                                class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 hover:bg-red-100">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    @elseif ($isLawRejected)
                                                        <div class="pt-1 text-[11px] font-semibold text-red-700" data-law-request-state>Cancelled and marked absent</div>
                                                    @elseif ($isLawApproved)
                                                        <div class="pt-1 text-[11px] font-semibold text-emerald-700" data-law-request-state>Approved for attendance</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">No request</span>
                                            @endif
                                        </td>
                                        <td class="admin-teacher-cell admin-teacher-col-current px-3 py-4">
                                            @if ($currentStatus !== '' && isset($statusLabels[$currentStatus]))
                                                <span data-attendance-indicator
                                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold whitespace-nowrap {{ $statusClass }}">
                                                    {{ $statusLabels[$currentStatus] }}
                                                </span>
                                            @else
                                                <span data-attendance-indicator
                                                    class="inline-flex items-center rounded-full border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 whitespace-nowrap">
                                                    Not Checked Yet
                                                </span>
                                            @endif
                                        </td>
                                        <td class="admin-teacher-cell admin-teacher-col-check px-3 py-4">
                                            <div class="space-y-1.5">
                                            <select name="attendance[{{ $teacher->id }}][status]"
                                                {{ $isLocked ? 'disabled' : '' }}
                                                class="js-admin-teacher-status sr-only" tabindex="-1" aria-hidden="true">
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
                                                        <button type="button"
                                                            data-status-option="{{ $statusKey }}"
                                                            {{ $isLocked ? 'disabled' : '' }}
                                                            aria-pressed="{{ $isActiveStatus ? 'true' : 'false' }}"
                                                            class="js-admin-status-box inline-flex min-w-[76px] items-center justify-center rounded-full border px-2.5 py-2 text-xs font-semibold transition disabled:cursor-not-allowed disabled:opacity-50 {{ $isActiveStatus ? 'border-indigo-300 bg-indigo-50 text-indigo-700 shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-indigo-200 hover:bg-indigo-50/50 hover:text-indigo-700' }}">
                                                            {{ $statusLabel }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td class="admin-teacher-cell admin-teacher-col-remark px-3 py-4">
                                            <input type="text" name="attendance[{{ $teacher->id }}][remark]"
                                                value="{{ $remarkValue }}" maxlength="255" {{ $isLocked ? 'disabled' : '' }}
                                                class="w-full rounded-2xl border border-slate-200 px-3.5 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                                                placeholder="Optional note">
                                            @if ($isLocked)
                                                <div class="mt-1 text-[11px] font-semibold text-emerald-700">Saved and locked</div>
                                            @endif
                                        </td>
                                        <td class="admin-teacher-cell admin-teacher-col-checked px-3 py-4 text-slate-600">
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
                                        <td colspan="6" class="px-3 py-10 text-center text-sm text-slate-500">
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
