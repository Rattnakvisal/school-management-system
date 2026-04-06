@extends('layout.teacher.navbar')

@section('page')
    @php
        $selectedClass = collect($classes ?? [])->firstWhere('id', (int) ($classId ?? 0));
        $defaultClassId = $classId ?: (string) ($classes->first()?->id ?? '');
        $settingsDefaultTab = old(
            '_settings_tab',
            $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation')
                ? 'password'
                : 'profile',
        );

        $present = (int) ($selectedDateSummary['present'] ?? 0);
        $absent = (int) ($selectedDateSummary['absent'] ?? 0);
        $late = (int) ($selectedDateSummary['late'] ?? 0);
        $excused = (int) ($selectedDateSummary['excused'] ?? 0);
        $total = max(0, $present + $absent + $late + $excused);
        $presentPct = $total ? round(($present / $total) * 100) : 0;
    @endphp

    <div id="teacher-settings-page" class="teacher-stage teacher-settings-stage space-y-6"
        data-settings-default-tab="{{ $settingsDefaultTab }}">
        <section
            class="teacher-reveal teacher-settings-hero rounded-3xl border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-100 sm:p-6"
            style="--sd: 1;">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Teacher Settings</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        Manage your profile, password, and attendance shortcuts.
                    </p>
                </div>

                <div class="teacher-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">Classes: {{ number_format($stats['classes'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--indigo">Subjects:
                        {{ number_format($stats['subjects'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--sky">Students:
                        {{ number_format($stats['students'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Checked Today:
                        {{ number_format($stats['checkedToday'] ?? 0) }}</span>
                    <span class="admin-page-stat admin-page-stat--amber">Checked This Week:
                        {{ number_format($stats['checkedThisWeek'] ?? 0) }}</span>
                </div>
            </div>

            <div class="mt-5">
                @if ($defaultClassId !== '')
                    <a href="{{ route('teacher.attendance.index', ['class_id' => $defaultClassId, 'date' => $selectedDate]) }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                        Open Attendance
                    </a>
                @endif
            </div>
        </section>

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

        <section
            class="teacher-reveal rounded-3xl border border-slate-200 bg-white p-4 shadow-sm ring-1 ring-slate-100 sm:p-6"
            style="--sd: 3;">
            <div class="grid gap-6 xl:grid-cols-[220px_minmax(0,1fr)]">
                <aside class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                    <div class="space-y-2">
                        <button type="button" data-settings-nav="profile"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 2c-3.33 0-8 1.67-8 5v1h16v-1c0-3.33-4.67-5-8-5Z" />
                                </svg>
                                Profile Settings
                            </span>
                        </button>
                        <button type="button" data-settings-nav="password"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M17 9h-1V7a4 4 0 1 0-8 0v2H7a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2Zm-7-2a2 2 0 1 1 4 0v2h-4V7Zm2 10a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" />
                                </svg>
                                Password
                            </span>
                        </button>
                        <button type="button" data-settings-nav="attendance"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M7 2a1 1 0 0 0-1 1v1H5a3 3 0 0 0-3 3v11a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3h-1V3a1 1 0 1 0-2 0v1H8V3a1 1 0 0 0-1-1Zm12 7H5v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9ZM7 12h4v4H7v-4Z" />
                                </svg>
                                Attendance
                            </span>
                        </button>
                    </div>
                </aside>

                <div class="min-w-0">
                    <section data-settings-panel="profile" class="space-y-6">
                        <form method="POST" action="{{ route('teacher.settings.profile.update') }}"
                            enctype="multipart/form-data"
                            class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_settings_tab" value="profile">

                            <div class="flex flex-wrap items-center gap-4 border-b border-slate-200 pb-5">
                                <div class="relative">
                                    <img id="teacher_avatar_preview"
                                        class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-sm"
                                        src="{{ $teacher->avatar_url }}" data-original-src="{{ $teacher->avatar_url }}"
                                        data-fallback="{{ $teacher->fallback_avatar_url }}"
                                        onerror="this.onerror=null;this.src='{{ $teacher->fallback_avatar_url }}';"
                                        alt="teacher avatar">
                                    <button type="button" id="teacher_trigger_avatar_upload"
                                        class="absolute -bottom-1 -right-1 grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-indigo-600 text-white shadow transition hover:bg-indigo-500"
                                        aria-label="Upload avatar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z" />
                                            <path
                                                d="M20 4h-3.17L15.6 2.35A2 2 0 0 0 14.06 2H9.94A2 2 0 0 0 8.4 2.35L7.17 4H4a2 2 0 0 0-2 2v10a4 4 0 0 0 4 4h12a4 4 0 0 0 4-4V6a2 2 0 0 0-2-2Zm-8 13a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <input id="teacher_avatar_input" type="file" name="avatar" accept="image/*"
                                        class="hidden">
                                    <button type="button" id="teacher_upload_avatar_btn"
                                        class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-500">
                                        Upload New
                                    </button>
                                    <button type="button" id="teacher_reset_avatar_btn"
                                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                        Reset
                                    </button>
                                    <span class="text-xs text-slate-500">JPG, PNG, WEBP up to 4MB.</span>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="name" class="mb-1 block text-xs font-semibold text-slate-600">Full Name
                                        <span class="text-red-500">*</span></label>
                                    <input id="name" name="name" type="text"
                                        value="{{ old('name', $teacher->name) }}" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                </div>
                                <div>
                                    <label for="email" class="mb-1 block text-xs font-semibold text-slate-600">Email
                                        <span class="text-red-500">*</span></label>
                                    <input id="email" name="email" type="email"
                                        value="{{ old('email', $teacher->email) }}" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                                    <input type="text" readonly value="{{ ucfirst((string) $teacher->role) }}"
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">ID</label>
                                    <input type="text" readonly
                                        value="ID #{{ $teacher->formatted_id ?? $teacher->id }}"
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </section>

                    <section data-settings-panel="password" class="hidden space-y-6">
                        <form method="POST" action="{{ route('teacher.settings.password.update') }}"
                            class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_settings_tab" value="password">

                            <h2 class="text-lg font-semibold text-slate-900">Change Password</h2>
                            <p class="mt-1 text-sm text-slate-500">Use a strong password with at least 8 characters.</p>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="current_password"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Current Password</label>
                                    <input id="current_password" type="password" name="current_password" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="password" class="mb-1 block text-xs font-semibold text-slate-600">New
                                            Password</label>
                                        <input id="password" type="password" name="password" required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    </div>
                                    <div>
                                        <label for="password_confirmation"
                                            class="mb-1 block text-xs font-semibold text-slate-600">Confirm
                                            Password</label>
                                        <input id="password_confirmation" type="password" name="password_confirmation"
                                            required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </section>

                    <section data-settings-panel="attendance" class="hidden space-y-6">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">Check Attendance</h2>
                                    <p class="mt-1 text-sm text-slate-500">Pick a class and date to jump into attendance quickly.</p>
                                </div>

                                <a href="{{ route('teacher.attendance.index') }}"
                                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Open Full Attendance Page
                                </a>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-3">
                                <span class="text-xs font-semibold text-slate-600">Quick date:</span>
                                <button type="button" data-teacher-date-quick="today"
                                    class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                    Today
                                </button>
                                <button type="button" data-teacher-date-quick="yesterday"
                                    class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                    Yesterday
                                </button>

                                <div class="ml-auto flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-semibold text-slate-600">Selected:</span>
                                    <span class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700">
                                        {{ $selectedClass?->display_name ?? 'N/A' }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700">
                                        {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('teacher.attendance.index') }}"
                                class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="grid gap-3 md:grid-cols-12">
                                    <div class="md:col-span-4">
                                        <label for="teacher_class_query" class="mb-1 block text-xs font-semibold text-slate-600">
                                            Search Class
                                        </label>
                                        <input id="teacher_class_query" type="text" placeholder="Type to filter classes..."
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <p class="mt-1 text-[11px] text-slate-500">Tip: type 10A, Grade, or room name.</p>
                                    </div>

                                    <div class="md:col-span-4">
                                        <label for="teacher_class_id" class="mb-1 block text-xs font-semibold text-slate-600">Class</label>
                                        <select id="teacher_class_id" name="class_id"
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            @foreach ($classes as $classOption)
                                                <option value="{{ $classOption->id }}"
                                                    {{ ($classId ?? '') === (string) $classOption->id ? 'selected' : '' }}>
                                                    {{ $classOption->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-3">
                                        <label for="teacher_attendance_date" class="mb-1 block text-xs font-semibold text-slate-600">Date</label>
                                        <input id="teacher_attendance_date" type="date" name="date" value="{{ $selectedDate }}"
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    </div>

                                    <div class="md:col-span-1 flex items-end">
                                        <button type="submit"
                                            class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-3 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Go
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                                    <div class="text-xs font-semibold text-slate-500">Checked (Selected Date)</div>
                                    <div class="mt-1 text-sm font-black text-slate-900">{{ number_format($stats['selectedDateChecked'] ?? 0) }}</div>
                                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                        <div class="h-full rounded-full bg-indigo-500" style="width: {{ min(100, $presentPct) }}%"></div>
                                    </div>
                                    <div class="mt-1 text-[11px] font-semibold text-slate-500">Present rate: {{ $presentPct }}%</div>
                                </div>

                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-3">
                                    <div class="text-xs font-semibold text-emerald-700">Present</div>
                                    <div class="mt-1 text-sm font-black text-emerald-800">{{ number_format($present) }}</div>
                                </div>

                                <div class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-3">
                                    <div class="text-xs font-semibold text-rose-700">Absent</div>
                                    <div class="mt-1 text-sm font-black text-rose-800">{{ number_format($absent) }}</div>
                                </div>

                                <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-3">
                                    <div class="text-xs font-semibold text-amber-700">Late</div>
                                    <div class="mt-1 text-sm font-black text-amber-800">{{ number_format($late) }}</div>
                                </div>

                                <div class="rounded-xl border border-sky-200 bg-sky-50 px-3 py-3">
                                    <div class="text-xs font-semibold text-sky-700">Excused</div>
                                    <div class="mt-1 text-sm font-black text-sky-800">{{ number_format($excused) }}</div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <h3 class="text-sm font-black text-slate-900">Recent Attendance Checks</h3>
                                <div class="mt-2 overflow-hidden rounded-2xl border border-slate-200">
                                    <div class="max-h-[360px] overflow-auto">
                                        <table class="w-full min-w-[760px] text-left text-sm">
                                            <thead class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
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
                                                            <div class="font-semibold text-slate-900">{{ $item->student?->name ?? 'Student' }}</div>
                                                            <div class="text-xs text-slate-500">{{ $item->student?->email ?? '-' }}</div>
                                                        </td>
                                                        <td class="px-3 py-3 text-slate-700">{{ $item->schoolClass?->display_name ?: 'N/A' }}</td>
                                                        <td class="px-3 py-3 text-slate-700">{{ optional($item->attendance_date)->format('M d, Y') ?? '-' }}</td>
                                                        <td class="px-3 py-3">
                                                            <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-bold {{ $badge }}">
                                                                {{ $statusLabel }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-3 text-slate-700">{{ optional($item->checked_at)->diffForHumans() ?? '-' }}</td>
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
                                                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
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
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>

    @vite(['resources/js/Teacher/settings.js'])
@endsection
