@extends('layout.students.navbar')

@section('page')
    @php
        $fullName = trim((string) ($student->name ?? (auth()->user()->name ?? 'Student')));
        $firstName = explode(' ', $fullName)[0] ?? 'Student';
        $studentClass = $student->schoolClass?->display_name ?? 'No class assigned';
        $classRoom = trim((string) ($student->schoolClass?->room ?? ''));
        $studentPhone = trim((string) ($student->phone_number ?? ''));
        $studentEmail = trim((string) ($student->email ?? ''));
        $studentAddress = $classRoom !== '' ? 'Room ' . $classRoom : $studentClass;
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst((string) $todayKey);
        $majorSubjects = collect($majorSubjects ?? []);
        $classSubjects = collect($classSubjects ?? []);
        $latestAttendance = collect($latestAttendance ?? []);
        $monthlyAttendanceTrend = collect($monthlyAttendanceTrend ?? []);
        $displayStudentId =
            'ID: ' . ($student->formatted_id ?? str_pad((string) ($student->id ?? 0), 7, '0', STR_PAD_LEFT));
        $studentSubtitle = $majorSubjects->first()?->name ?? ($classSubjects->first()?->name ?? 'Student overview');

        $statusCards = collect([
            [
                'value' => (int) ($attendanceByStatus['present'] ?? 0),
                'label' => 'Total Attendance',
                'short_label' => 'Attendance',
                'tone' => 'blue',
                'icon' => 'attendance',
            ],
            [
                'value' => (int) ($attendanceByStatus['late'] ?? 0),
                'label' => 'Late Attendance',
                'short_label' => 'Late',
                'tone' => 'lime',
                'icon' => 'late',
            ],
            [
                'value' => (int) ($attendanceByStatus['excused'] ?? 0),
                'label' => 'Excused Attendance',
                'short_label' => 'Excused',
                'tone' => 'amber',
                'icon' => 'excused',
            ],
            [
                'value' => (int) ($attendanceByStatus['absent'] ?? 0),
                'label' => 'Total Absent',
                'short_label' => 'Absent',
                'tone' => 'rose',
                'icon' => 'absent',
            ],
        ]);

        $statusMax = max(1, (int) $statusCards->max('value'));

        $summaryBars = $statusCards->map(function (array $card) use ($statusMax): array {
            $value = (int) ($card['value'] ?? 0);

            return $card + [
                'height' => max(26, (int) round(($value / $statusMax) * 100)),
                'display_value' => str_pad((string) $value, 2, '0', STR_PAD_LEFT),
            ];
        });

        $panelClass =
            'rounded-[28px] border border-white/70 bg-white/85 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.18)] backdrop-blur-sm';
        $panelPadding = 'p-5 sm:p-6';
        $eyebrowClass = 'text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-400';
        $titleClass = 'text-lg font-black tracking-[-0.04em] text-slate-900';
        $softText = 'text-sm text-slate-500';
    @endphp

    <div class="dashboard-stage space-y-6 bg-[linear-gradient(180deg,#f8fbff_0%,#f4f7fb_100%)] font-[Plus Jakarta Sans] tracking-[-0.01em]">
        {{-- Hero / Profile --}}
        <section
            class="dash-reveal relative overflow-hidden rounded-[32px] border border-white/70 bg-[radial-gradient(circle_at_top_left,rgba(96,165,250,0.14),transparent_28%),radial-gradient(circle_at_bottom_right,rgba(167,139,250,0.10),transparent_24%),linear-gradient(180deg,rgba(255,255,255,0.98),rgba(248,250,252,0.98))] px-6 py-6 shadow-[0_24px_70px_-30px_rgba(15,23,42,0.22)] sm:px-8 sm:py-8"
            style="--d: 1;">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute left-0 top-0 h-44 w-44 rounded-full bg-blue-200/20 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 h-44 w-44 rounded-full bg-violet-200/20 blur-3xl"></div>
            </div>

            <div class="relative">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-2">
                        <span
                            class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.16em] text-blue-700">
                            Student dashboard
                        </span>
                        <h1
                            class="font-[Sora] text-[clamp(1.8rem,4vw,2.8rem)] font-black leading-[0.95] tracking-[-0.06em] text-slate-900">
                            Welcome back,
                            <span class="bg-gradient-to-r from-blue-700 to-indigo-600 bg-clip-text text-transparent">
                                {{ $firstName }}
                            </span>
                        </h1>
                        <p class="max-w-2xl text-sm leading-7 text-slate-500 sm:text-[0.98rem]">
                            Here is your academic overview, attendance summary, and current study focus.
                        </p>
                    </div>

                    <span
                        class="inline-flex min-h-[2.75rem] items-center justify-center rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-bold text-slate-600 shadow-sm">
                        {{ $todayLabel }}
                    </span>
                </div>

                <div class="mt-8 grid gap-6 xl:grid-cols-[minmax(300px,1.05fr)_minmax(0,1.4fr)]">
                    <div
                        class="student-hover-card dash-hover flex min-w-0 items-center gap-4 rounded-[26px] border border-white/70 bg-white/80 p-4 shadow-[0_14px_35px_-28px_rgba(15,23,42,0.18)] sm:p-5">
                        <img src="{{ $student->avatar_url }}" alt="{{ $fullName }}"
                            onerror="this.onerror=null;this.src='{{ $student->fallback_avatar_url }}';"
                            class="h-20 w-20 shrink-0 rounded-full border-[5px] border-[#eef5ff] object-cover shadow-md">

                        <div class="min-w-0">
                            <h2
                                class="truncate font-[Sora] text-[clamp(1.25rem,3vw,1.75rem)] font-extrabold tracking-[-0.04em] text-slate-900">
                                {{ $fullName }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-400">
                                {{ $studentClass }}
                                @if ($classRoom !== '')
                                    • Room {{ $classRoom }}
                                @endif
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ $studentSubtitle }}
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
                        <article class="student-hover-card dash-hover rounded-[22px] bg-white/80 p-4 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.16)]">
                            <span class="block text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Student
                                ID</span>
                            <strong
                                class="mt-2 block break-words font-[Sora] text-[0.98rem] font-extrabold text-slate-800">
                                {{ $displayStudentId }}
                            </strong>
                        </article>

                        <article class="student-hover-card dash-hover rounded-[22px] bg-white/80 p-4 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.16)]">
                            <span
                                class="block text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Phone</span>
                            <strong
                                class="mt-2 block break-words font-[Sora] text-[0.98rem] font-extrabold text-slate-800">
                                {{ $studentPhone !== '' ? $studentPhone : 'Not set' }}
                            </strong>
                        </article>

                        <article class="student-hover-card dash-hover rounded-[22px] bg-white/80 p-4 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.16)]">
                            <span
                                class="block text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Email</span>
                            <strong
                                class="mt-2 block break-words font-[Sora] text-[0.98rem] font-extrabold text-slate-800">
                                {{ $studentEmail !== '' ? $studentEmail : 'Not set' }}
                            </strong>
                        </article>

                        <article class="student-hover-card dash-hover rounded-[22px] bg-white/80 p-4 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.16)]">
                            <span
                                class="block text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Address</span>
                            <strong
                                class="mt-2 block break-words font-[Sora] text-[0.98rem] font-extrabold text-slate-800">
                                {{ $studentAddress }}
                            </strong>
                        </article>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($statusCards as $card)
                        @php
                            $cardTone = match ($card['tone']) {
                                'blue' => [
                                    'wrap' => 'bg-[linear-gradient(180deg,#f8fbff,#ffffff)]',
                                    'icon' => 'bg-[#e0f1ff] text-[#2090f5]',
                                ],
                                'lime' => [
                                    'wrap' => 'bg-[linear-gradient(180deg,#fbfff2,#ffffff)]',
                                    'icon' => 'bg-[#eef9d7] text-lime-500',
                                ],
                                'amber' => [
                                    'wrap' => 'bg-[linear-gradient(180deg,#fffaf2,#ffffff)]',
                                    'icon' => 'bg-[#fff2d7] text-amber-500',
                                ],
                                default => [
                                    'wrap' => 'bg-[linear-gradient(180deg,#fff8f8,#ffffff)]',
                                    'icon' => 'bg-[#ffe1e4] text-rose-500',
                                ],
                            };
                        @endphp

                        <article
                            class="student-hover-card dash-hover flex items-center gap-4 rounded-[24px] border border-white/70 {{ $cardTone['wrap'] }} px-4 py-4 shadow-[0_14px_30px_-24px_rgba(15,23,42,0.14)]">
                            <span
                                class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $cardTone['icon'] }}">
                                @switch($card['icon'])
                                    @case('attendance')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            class="h-[1.15rem] w-[1.15rem]">
                                            <path d="M12 12a3 3 0 1 0-3-3 3 3 0 0 0 3 3Z" />
                                            <path d="M6.5 18.5a5.5 5.5 0 0 1 11 0" stroke-linecap="round" />
                                        </svg>
                                    @break

                                    @case('late')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            class="h-[1.15rem] w-[1.15rem]">
                                            <circle cx="12" cy="12" r="7" />
                                            <path d="M12 8v4l2.5 1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @break

                                    @case('excused')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            class="h-[1.15rem] w-[1.15rem]">
                                            <circle cx="12" cy="12" r="7" />
                                            <path d="M12 9.5v3.5" stroke-linecap="round" />
                                            <path d="M12 15.5h.01" stroke-linecap="round" />
                                        </svg>
                                    @break

                                    @default
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            class="h-[1.15rem] w-[1.15rem]">
                                            <path d="M8 8l8 8" stroke-linecap="round" />
                                            <path d="M16 8l-8 8" stroke-linecap="round" />
                                            <circle cx="12" cy="12" r="8" />
                                        </svg>
                                @endswitch
                            </span>

                            <div>
                                <strong
                                    class="student-animate-number block font-[Sora] text-[1.15rem] font-extrabold tracking-[-0.04em] text-slate-800"
                                    data-value="{{ (int) $card['value'] }}" data-suffix=" Days">
                                    0 Days
                                </strong>
                                <span class="mt-1 block text-[0.8rem] text-slate-400">{{ $card['label'] }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Main Grid --}}
        <section class="dash-reveal grid gap-6 xl:grid-cols-[minmax(320px,0.95fr)_minmax(0,1.35fr)]" style="--d: 2;">
            <div class="grid gap-6">
                <article class="student-hover-card dash-hover {{ $panelClass }} {{ $panelPadding }}">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="{{ $eyebrowClass }}">Monthly</p>
                            <h2 class="{{ $titleClass }}">Class Days</h2>
                            <p class="mt-1 {{ $softText }}">Total class days recorded this month</p>
                        </div>

                        <div
                            class="student-animate-number font-[Sora] text-[clamp(2rem,4vw,2.6rem)] font-normal tracking-[-0.05em] text-slate-800 max-sm:text-left"
                            data-value="{{ (int) ($attendanceThisMonth ?? 0) }}" data-suffix=" Days">
                            0 Days
                        </div>
                    </div>
                </article>

                <article class="student-hover-card dash-hover {{ $panelClass }} {{ $panelPadding }}">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="{{ $eyebrowClass }}">Performance</p>
                            <h2 class="{{ $titleClass }}">Attendance Rate</h2>
                            <p class="mt-1 {{ $softText }}">Yearly attendance overview</p>
                        </div>

                        <div
                            class="student-animate-number font-[Sora] text-[clamp(2.3rem,4vw,3.25rem)] font-normal leading-none tracking-[-0.06em] text-slate-900"
                            data-value="{{ (float) ($attendanceRate ?? 0) }}" data-decimals="1" data-suffix="%">
                            0.0%
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3">
                        @forelse ($monthlyAttendanceTrend as $trend)
                            <article
                                class="student-hover-card dash-hover grid gap-3 border-t border-slate-100 pt-3 first:border-t-0 first:pt-0 md:grid-cols-[76px_minmax(72px,auto)_minmax(0,1fr)_auto] md:items-center">
                                <span
                                    class="inline-flex min-h-[3rem] items-center justify-center rounded-2xl bg-blue-50 px-3 py-2 text-[0.82rem] font-bold text-blue-600 shadow-sm">
                                    {{ $trend['label'] }}
                                </span>

                                <strong
                                    class="student-animate-number font-[Sora] text-[1.08rem] font-extrabold tracking-[-0.04em] text-slate-800"
                                    data-value="{{ (float) ($trend['value'] ?? 0) }}" data-decimals="1"
                                    data-suffix="%">
                                    0.0%
                                </strong>

                                <span class="relative block h-3 w-full overflow-hidden rounded-full bg-slate-100">
                                    <span
                                        class="student-trend-fill absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-sky-400 to-blue-600"
                                        data-width="{{ max(0, min(100, (float) ($trend['value'] ?? 0))) }}"
                                        style="width: {{ max(0, min(100, (float) ($trend['value'] ?? 0))) }}%"></span>
                                </span>

                                <small class="text-[0.82rem] text-slate-400">
                                    {{ number_format((int) ($trend['total'] ?? 0)) }} checks
                                </small>
                            </article>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center text-sm text-slate-400">
                                No monthly attendance data yet.
                            </div>
                        @endforelse
                    </div>
                </article>
            </div>

            <article class="student-hover-card dash-hover {{ $panelClass }} {{ $panelPadding }}">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="{{ $eyebrowClass }}">Summary</p>
                        <h2 class="{{ $titleClass }}">Summary - {{ $firstName }}</h2>
                    </div>

                    <span
                        class="inline-flex min-h-[2.5rem] items-center justify-center rounded-full bg-slate-50 px-4 py-2 text-sm font-bold text-slate-600">
                        {{ $todayLabel }}
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($summaryBars as $card)
                        @php
                            $trackClass = match ($card['tone']) {
                                'blue' => 'bg-[#dbeeff]',
                                'lime' => 'bg-[#eaf7d5]',
                                'amber' => 'bg-[#fff2d8]',
                                default => 'bg-[#ffdfe3]',
                            };

                            $fillClass = match ($card['tone']) {
                                'blue' => 'bg-[linear-gradient(180deg,#4aa8f1,#2f8ddd)]',
                                'lime' => 'bg-[linear-gradient(180deg,#8cda1a,#74c500)]',
                                'amber' => 'bg-[linear-gradient(180deg,#ffc250,#ffab2f)]',
                                default => 'bg-[linear-gradient(180deg,#ff676f,#ff3942)]',
                            };
                        @endphp

                        <article class="student-hover-card dash-hover flex flex-col gap-3 rounded-[24px] bg-slate-50/70 p-4">
                            <div
                                class="student-animate-number text-center font-[Sora] text-[1.75rem] font-extrabold tracking-[-0.05em] text-slate-800"
                                data-value="{{ (int) $card['value'] }}">
                                0
                            </div>

                            <div
                                class="relative flex min-h-[8rem] flex-1 items-end overflow-hidden rounded-[1.5rem] {{ $trackClass }} md:min-h-[10rem] xl:min-h-[14rem]">
                                <div class="student-summary-fill w-full rounded-[1.25rem] {{ $fillClass }}"
                                    data-height="{{ $card['height'] }}"
                                    style="height: {{ $card['height'] }}%"></div>
                            </div>

                            <div class="text-center text-[0.82rem] font-bold text-slate-500">
                                {{ $card['short_label'] }}
                            </div>
                        </article>
                    @endforeach
                </div>
            </article>
        </section>

        {{-- Lower Grid --}}
        <section class="dash-reveal grid gap-6 md:grid-cols-2" style="--d: 3;">
            <article class="student-hover-card dash-hover {{ $panelClass }} {{ $panelPadding }}">
                <div>
                    <p class="{{ $eyebrowClass }}">Recent</p>
                    <h2 class="{{ $titleClass }}">Attendance Checks</h2>
                    <p class="mt-1 {{ $softText }}">Latest attendance updates</p>
                </div>

                @if ($latestAttendance->isNotEmpty())
                    <div class="mt-6 grid gap-4">
                        @foreach ($latestAttendance as $record)
                            @php
                                $badgeClass = match (strtolower((string) $record->status)) {
                                    'present' => 'bg-green-100 text-green-700',
                                    'late' => 'bg-amber-100 text-amber-700',
                                    'excused' => 'bg-sky-100 text-sky-700',
                                    default => 'bg-rose-100 text-rose-700',
                                };
                            @endphp

                            <article
                                class="student-hover-card dash-hover flex flex-col gap-3 rounded-[22px] border border-slate-100 bg-slate-50/70 p-4 transition hover:bg-white sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <strong class="block text-[0.95rem] font-extrabold text-slate-800">
                                        {{ optional($record->attendance_date)->format('d M Y') ?? '-' }}
                                    </strong>
                                    <span class="mt-1 block text-[0.8rem] text-slate-400">
                                        {{ $record->teacher?->name ?: 'Teacher update' }}
                                    </span>
                                </div>

                                <span
                                    class="inline-flex items-center justify-center rounded-full px-3 py-2 text-[0.74rem] font-extrabold sm:min-w-[6.5rem] {{ $badgeClass }}">
                                    {{ ucfirst((string) $record->status) }}
                                </span>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div
                        class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center text-sm text-slate-400">
                        No attendance records yet.
                    </div>
                @endif
            </article>

            <article class="student-hover-card dash-hover {{ $panelClass }} {{ $panelPadding }}">
                <div>
                    <p class="{{ $eyebrowClass }}">Subjects</p>
                    <h2 class="{{ $titleClass }}">Study Focus</h2>
                    <p class="mt-1 {{ $softText }}">Your current major or class subjects</p>
                </div>

                <div class="mt-6 grid gap-4">
                    @forelse ($majorSubjects->take(3) as $subject)
                        <article
                            class="student-hover-card dash-hover rounded-[22px] border border-emerald-100 bg-[linear-gradient(180deg,#ecfdf5,#ffffff)] p-4 shadow-[0_10px_24px_-20px_rgba(16,185,129,0.22)]">
                            <strong class="block text-[0.95rem] font-extrabold text-slate-800">
                                {{ $subject->name }}
                            </strong>
                            <span class="mt-1 block text-[0.8rem] text-slate-400">
                                {{ $subject->code ?: 'Major subject' }}
                            </span>
                        </article>
                    @empty
                        @forelse ($classSubjects->take(3) as $subject)
                            <article
                                class="student-hover-card dash-hover rounded-[22px] border border-sky-100 bg-[linear-gradient(180deg,#f0f9ff,#ffffff)] p-4 shadow-[0_10px_24px_-20px_rgba(14,165,233,0.22)]">
                                <strong class="block text-[0.95rem] font-extrabold text-slate-800">
                                    {{ $subject->name }}
                                </strong>
                                <span class="mt-1 block text-[0.8rem] text-slate-400">
                                    {{ $subject->code ?: 'Class subject' }}
                                    @if ($subject->teacher?->name)
                                        • {{ $subject->teacher->name }}
                                    @endif
                                </span>
                            </article>
                        @empty
                            <div
                                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center text-sm text-slate-400">
                                No subjects assigned yet.
                            </div>
                        @endforelse
                    @endforelse
                </div>
            </article>
        </section>
    </div>
    @vite(['resources/js/Student/dashboard.js'])
@endsection
