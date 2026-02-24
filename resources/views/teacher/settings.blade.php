@extends('layout.teacher.navbar')

@section('page')
    @php
        $selectedClass = collect($classes ?? [])->firstWhere('id', (int) ($classId ?? 0));
        $defaultClassId = $classId ?: (string) ($classes->first()?->id ?? '');

        // For improved summary cards
        $present = (int) ($selectedDateSummary['present'] ?? 0);
        $absent = (int) ($selectedDateSummary['absent'] ?? 0);
        $late = (int) ($selectedDateSummary['late'] ?? 0);
        $excused = (int) ($selectedDateSummary['excused'] ?? 0);
        $total = max(0, $present + $absent + $late + $excused);
        $presentPct = $total ? round(($present / $total) * 100) : 0;
    @endphp

    <div class="teacher-stage teacher-settings-stage space-y-6">
        {{-- HEADER --}}
        <section class="teacher-reveal admin-page-header teacher-page-header" style="--sd: 1;">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">My Profile</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        Manage your account and jump into attendance workflow in one click.
                    </p>
                </div>

                <div class="teacher-page-header__actions flex flex-wrap items-center gap-2">
                    <div class="teacher-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span class="admin-page-stat">Classes: {{ number_format($stats['classes'] ?? 0) }}</span>
                        <span class="admin-page-stat admin-page-stat--indigo">
                            Subjects: {{ number_format($stats['subjects'] ?? 0) }}
                        </span>
                        <span class="admin-page-stat admin-page-stat--sky">
                            Students: {{ number_format($stats['students'] ?? 0) }}
                        </span>
                        <span class="admin-page-stat admin-page-stat--emerald">
                            Checked Today: {{ number_format($stats['checkedToday'] ?? 0) }}
                        </span>
                        <span class="admin-page-stat admin-page-stat--amber">
                            Checked This Week: {{ number_format($stats['checkedThisWeek'] ?? 0) }}
                        </span>
                    </div>

                    @if ($defaultClassId !== '')
                        <a href="{{ route('teacher.attendance.index', ['class_id' => $defaultClassId, 'date' => $selectedDate]) }}"
                            class="teacher-settings-cta inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            Check Attendance
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- FLASH / ERRORS --}}
        @if (session('success'))
            <div class="teacher-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="teacher-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="teacher-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            {{-- LEFT: PROFILE --}}
            <section
                class="teacher-reveal teacher-float teacher-settings-panel rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5"
                style="--sd: 3;">
                <div class="teacher-settings-profile-head mb-5">
                    <div class="flex items-center gap-3">
                        <img class="h-20 w-20 rounded-2xl border border-white/60 object-cover shadow-sm"
                            src="{{ $teacher->avatar_url }}"
                            onerror="this.onerror=null;this.src='{{ $teacher->fallback_avatar_url }}';" alt="avatar">
                        <div>
                            <h2 class="text-xl font-black text-slate-900">{{ $teacher->name }}</h2>
                            <p class="text-sm text-slate-600">{{ $teacher->email }}</p>
                            <p
                                class="mt-1 inline-flex rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[11px] font-bold uppercase text-indigo-700">
                                {{ $teacher->role }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-5 grid gap-2 sm:grid-cols-3">
                    <div class="teacher-settings-inline-kpi rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                        <div class="text-[11px] font-semibold text-slate-500">Classes</div>
                        <div class="mt-0.5 text-base font-black text-slate-900">{{ number_format($stats['classes'] ?? 0) }}</div>
                    </div>
                    <div class="teacher-settings-inline-kpi rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2.5">
                        <div class="text-[11px] font-semibold text-indigo-600">Today Checks</div>
                        <div class="mt-0.5 text-base font-black text-indigo-800">{{ number_format($stats['checkedToday'] ?? 0) }}</div>
                    </div>
                    <div class="teacher-settings-inline-kpi rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5">
                        <div class="text-[11px] font-semibold text-amber-600">Week Checks</div>
                        <div class="mt-0.5 text-base font-black text-amber-800">{{ number_format($stats['checkedThisWeek'] ?? 0) }}</div>
                    </div>
                </div>

                <div class="space-y-5">
                    {{-- UPDATE PROFILE --}}
                    <form method="POST" action="{{ route('teacher.settings.profile.update') }}"
                        enctype="multipart/form-data"
                        class="teacher-settings-form-card space-y-3 rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                        @csrf
                        @method('PUT')
                        <h3 class="teacher-settings-section-title text-sm font-black text-slate-900">
                            <span class="teacher-settings-section-dot"></span>
                            Profile Details
                        </h3>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                            <input type="email" name="email" value="{{ old('email', $teacher->email) }}" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Avatar</label>
                            <input type="file" name="avatar" accept="image/*"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <p class="mt-1 text-[11px] text-slate-500">JPG, PNG, WEBP up to 4MB.</p>
                        </div>

                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-500">
                            Save Profile
                        </button>
                    </form>

                    {{-- UPDATE PASSWORD --}}
                    <form method="POST" action="{{ route('teacher.settings.password.update') }}"
                        class="teacher-settings-form-card space-y-3 rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                        @csrf
                        @method('PUT')
                        <h3 class="teacher-settings-section-title text-sm font-black text-slate-900">
                            <span class="teacher-settings-section-dot"></span>
                            Change Password
                        </h3>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Current Password</label>
                            <input type="password" name="current_password" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">New Password</label>
                                <input type="password" name="password" required
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Confirm</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            </div>
                        </div>

                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800">
                            Update Password
                        </button>
                    </form>
                </div>
            </section>

            {{-- RIGHT: CHECK ATTENDANCE (IMPROVED FULL) --}}
            <section
                class="teacher-reveal teacher-float teacher-settings-panel rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7"
                style="--sd: 4;" x-data="{
                            classQuery: '',
                            selectedClassId: '{{ $classId ?? '' }}',
                            setDate(d){ this.$refs.date.value = d; },
                            today(){ return '{{ \Carbon\Carbon::now()->toDateString() }}'; },
                            yesterday(){ return '{{ \Carbon\Carbon::now()->subDay()->toDateString() }}'; }
                        }">

                {{-- Header --}}
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Check Attendance</h2>
                        <p class="mt-1 text-xs text-slate-500">
                            Pick a class + date to jump into attendance quickly.
                        </p>
                    </div>

                    <a href="{{ route('teacher.attendance.index') }}"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Open Full Attendance Page
                    </a>
                </div>

                {{-- Quick Date + Selected Info --}}
                <div
                    class="mb-3 flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                    <span class="text-xs font-semibold text-slate-600">Quick date:</span>

                    <button type="button" @click="setDate(today())"
                        class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                        Today
                    </button>

                    <button type="button" @click="setDate(yesterday())"
                        class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                        Yesterday
                    </button>

                    <div class="ml-auto flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-slate-600">Selected:</span>
                        <span
                            class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700">
                            {{ $selectedClass?->display_name ?? 'N/A' }}
                        </span>
                        <span
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                        </span>
                    </div>
                </div>

                {{-- Form --}}
                <form method="GET" action="{{ route('teacher.attendance.index') }}"
                    class="teacher-settings-check-form rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="grid gap-3 md:grid-cols-12">
                        {{-- Search Class --}}
                        <div class="md:col-span-4">
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Search Class</label>
                            <input type="text" x-model="classQuery" placeholder="Type to filter classes..." oninput="
                                            const q = this.value.trim().toLowerCase();
                                            const sel = this.closest('form').querySelector('select[name=class_id]');
                                            [...sel.options].forEach(o => {
                                                o.hidden = q ? !o.text.toLowerCase().includes(q) : false;
                                            });
                                        "
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <p class="mt-1 text-[11px] text-slate-500">Tip: type “10A”, “Grade”, etc.</p>
                        </div>

                        {{-- Select Class --}}
                        <div class="md:col-span-4">
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Class</label>
                            <select name="class_id" x-model="selectedClassId"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                @foreach ($classes as $classOption)
                                    <option value="{{ $classOption->id }}" {{ ($classId ?? '') === (string) $classOption->id ? 'selected' : '' }}>
                                        {{ $classOption->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date --}}
                        <div class="md:col-span-3">
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Date</label>
                            <input type="date" name="date" x-ref="date" value="{{ $selectedDate }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>

                        {{-- Submit --}}
                        <div class="md:col-span-1 flex items-end">
                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-3 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-500">
                                Go
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Summary Cards --}}
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <div class="teacher-settings-kpi rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                        <div class="text-xs font-semibold text-slate-500">Checked (Selected Date)</div>
                        <div class="mt-1 text-sm font-black text-slate-900">
                            {{ number_format($stats['selectedDateChecked'] ?? 0) }}
                        </div>
                        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-indigo-500" style="width: {{ min(100, $presentPct) }}%">
                            </div>
                        </div>
                        <div class="mt-1 text-[11px] font-semibold text-slate-500">Present rate: {{ $presentPct }}%</div>
                    </div>

                    <div class="teacher-settings-kpi rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-3">
                        <div class="text-xs font-semibold text-emerald-700">Present</div>
                        <div class="mt-1 text-sm font-black text-emerald-800">{{ number_format($present) }}</div>
                    </div>

                    <div class="teacher-settings-kpi rounded-xl border border-rose-200 bg-rose-50 px-3 py-3">
                        <div class="text-xs font-semibold text-rose-700">Absent</div>
                        <div class="mt-1 text-sm font-black text-rose-800">{{ number_format($absent) }}</div>
                    </div>

                    <div class="teacher-settings-kpi rounded-xl border border-amber-200 bg-amber-50 px-3 py-3">
                        <div class="text-xs font-semibold text-amber-700">Late</div>
                        <div class="mt-1 text-sm font-black text-amber-800">{{ number_format($late) }}</div>
                    </div>

                    <div class="teacher-settings-kpi rounded-xl border border-sky-200 bg-sky-50 px-3 py-3">
                        <div class="text-xs font-semibold text-sky-700">Excused</div>
                        <div class="mt-1 text-sm font-black text-sky-800">{{ number_format($excused) }}</div>
                    </div>
                </div>

                {{-- Recent Attendance --}}
                <div class="mt-5">
                    <h3 class="text-sm font-black text-slate-900">Recent Attendance Checks</h3>

                    <div class="teacher-settings-table-wrap mt-2 overflow-hidden rounded-2xl border border-slate-200">
                        <div class="max-h-[360px] overflow-auto">
                            <table class="w-full min-w-[760px] text-left text-sm">
                                <thead
                                    class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-3 py-3 font-semibold">Student</th>
                                        <th class="px-3 py-3 font-semibold">Class</th>
                                        <th class="px-3 py-3 font-semibold">Date</th>
                                        <th class="px-3 py-3 font-semibold">Status</th>
                                        <th class="px-3 py-3 font-semibold">Checked</th>
                                        <th class="px-3 py-3 font-semibold text-right">Action</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse ($recentAttendances as $item)
                                        @php
                                            $statusKey = strtolower((string) ($item->status ?? 'unknown'));
                                            $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);

                                            $badge = match ($statusKey) {
                                                'present' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                                'absent' => 'border-rose-200 bg-rose-50 text-rose-700',
                                                'late' => 'border-amber-200 bg-amber-50 text-amber-700',
                                                'excused' => 'border-sky-200 bg-sky-50 text-sky-700',
                                                default => 'border-slate-200 bg-slate-50 text-slate-700',
                                            };
                                        @endphp

                                        <tr class="align-top hover:bg-slate-50/80">
                                            <td class="px-3 py-3">
                                                <div class="font-semibold text-slate-900">
                                                    {{ $item->student?->name ?? 'Student' }}
                                                </div>
                                                <div class="text-xs text-slate-500">{{ $item->student?->email ?? '-' }}</div>
                                            </td>

                                            <td class="px-3 py-3 text-slate-700">
                                                {{ $item->schoolClass?->display_name ?: 'N/A' }}
                                            </td>

                                            <td class="px-3 py-3 text-slate-700">
                                                {{ optional($item->attendance_date)->format('M d, Y') ?? '-' }}
                                            </td>

                                            <td class="px-3 py-3">
                                                <span
                                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-bold {{ $badge }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>

                                            <td class="px-3 py-3 text-slate-700">
                                                {{ optional($item->checked_at)->diffForHumans() ?? '-' }}
                                            </td>

                                            <td class="px-3 py-3">
                                                <div class="flex justify-end">
                                                    <a href="{{ route('teacher.attendance.index', ['class_id' => $item->school_class_id, 'date' => optional($item->attendance_date)->toDateString()]) }}"
                                                        class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                        Open
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-3 py-10">
                                                <div
                                                    class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                                                    <div class="text-sm font-bold text-slate-900">No attendance checks yet</div>
                                                    <div class="mt-1 text-xs text-slate-500">
                                                        Use the form above to start checking attendance quickly.
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
