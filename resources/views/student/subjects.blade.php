@extends('layout.students.navbar')

@section('page')
    @php
        $selectedDayLabel = $dayOptions[$selectedDay] ?? 'All Days';
    @endphp

    <div class="student-stage space-y-6">
        <section class="student-reveal student-float admin-page-header" style="--sd: 1;">
            <div class="admin-page-header__main flex flex-wrap items-start gap-4">
                <div class="admin-page-header__intro space-y-2">
                    <div class="admin-page-header__title-row flex items-start gap-3">
                        <span class="admin-page-header__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 6.5 12 2l10 4.5L12 11 2 6.5Z"></path>
                                <path d="M6 9.5v6.2C6 17.6 8.7 19 12 19s6-1.4 6-3.3V9.5"></path>
                            </svg>
                        </span>
                        <div>
                            <div class="admin-page-header__eyebrow">Student Portal</div>
                            <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">My Subjects</h1>
                        </div>
                    </div>
                    <p class="admin-page-subtitle text-sm">
                        Track your assigned subjects, teacher list, and weekly study schedule in one page.
                    </p>
                    <p class="text-xs font-semibold text-slate-600">
                        Home Class: <span class="text-slate-900">{{ $classLabel }}</span>
                    </p>
                </div>

                <div class="admin-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">Total: {{ number_format($subjects->count()) }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Major:
                        {{ number_format($majorSubjectsCount) }}</span>
                    <span class="admin-page-stat admin-page-stat--sky">Class:
                        {{ number_format($classSubjectsCount) }}</span>
                    <span class="admin-page-stat admin-page-stat--amber">Schedule:
                        {{ number_format($scheduleCount) }}</span>
                </div>
            </div>

            <form method="GET" action="{{ route('student.subjects.index') }}"
                class="mt-5 rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-indigo-50/40 p-4 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-indigo-700 shadow-sm">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                        </svg>
                        Schedule Filter
                    </div>

                    <div
                        class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                        Current: <span class="ml-1 font-bold text-slate-800">{{ $selectedDayLabel }}</span>
                    </div>
                </div>

                <div class="mt-3 grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                    <div class="min-w-0">
                        <label for="day" class="mb-1.5 block text-xs font-semibold text-slate-600">Select Day</label>
                        <div class="relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-3 inline-flex items-center text-slate-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                            <select id="day" name="day"
                                class="h-11 w-full appearance-none rounded-xl border border-slate-300 bg-white pl-10 pr-10 text-sm font-semibold text-slate-800 outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                @foreach ($dayOptions as $dayKey => $dayLabel)
                                    <option value="{{ $dayKey }}" {{ $selectedDay === $dayKey ? 'selected' : '' }}>
                                        {{ $dayLabel }}
                                    </option>
                                @endforeach
                            </select>
                            <span
                                class="pointer-events-none absolute inset-y-0 right-3 inline-flex items-center text-slate-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-slate-900 px-5 text-sm font-bold text-white transition hover:from-indigo-500 hover:to-slate-800">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                            </svg>
                            Apply
                        </button>

                        @if ($selectedDay !== 'all')
                            <a href="{{ route('student.subjects.index') }}"
                                class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                Reset
                            </a>
                        @else
                            <a href="{{ route('student.subjects.index', ['day' => $todayKey]) }}"
                                class="inline-flex h-11 items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-4 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                Today
                            </a>
                        @endif
                    </div>
                </div>

                @php
                    $quickFilterKeys = ['all', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                @endphp
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Quick Days</span>
                    @foreach ($quickFilterKeys as $quickKey)
                        @php
                            $isActiveQuick = $selectedDay === $quickKey;
                            $quickLabel = $dayOptions[$quickKey] ?? ucfirst($quickKey);
                        @endphp
                        <a href="{{ route('student.subjects.index', ['day' => $quickKey]) }}"
                            class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-semibold transition {{ $isActiveQuick
                                ? 'border-indigo-200 bg-indigo-50 text-indigo-700 shadow-sm'
                                : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50' }}">
                            {{ $quickLabel }}
                        </a>
                    @endforeach
                </div>
            </form>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="student-reveal student-float rounded-3xl border border-indigo-100 bg-white p-5 shadow-sm"
                style="--sd: 2;">
                <div class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Subjects</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($subjects->count()) }}</div>
                <div class="mt-1 text-xs text-slate-500">Assigned to your account</div>
            </article>
            <article class="student-reveal student-float rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm"
                style="--sd: 3;">
                <div class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Major</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($majorSubjectsCount) }}</div>
                <div class="mt-1 text-xs text-slate-500">Focus subjects selected</div>
            </article>
            <article class="student-reveal student-float rounded-3xl border border-sky-100 bg-white p-5 shadow-sm"
                style="--sd: 4;">
                <div class="text-xs font-semibold uppercase tracking-wide text-sky-600">Class</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($classSubjectsCount) }}</div>
                <div class="mt-1 text-xs text-slate-500">Subjects from home class</div>
            </article>
            <article class="student-reveal student-float rounded-3xl border border-amber-100 bg-white p-5 shadow-sm"
                style="--sd: 5;">
                <div class="text-xs font-semibold uppercase tracking-wide text-amber-600">Today</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($todayScheduleCount) }}</div>
                <div class="mt-1 text-xs text-slate-500">Today schedule rows</div>
            </article>
        </section>

        <section class="student-reveal student-float rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
            style="--sd: 6;">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-slate-900">Assigned Subjects</h2>
                <span
                    class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                    {{ number_format($subjects->count()) }} total
                </span>
            </div>

            @if ($subjects->isNotEmpty())
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($subjects as $subject)
                        @php
                            $slotCount = (int) ($subjectSlotCounts[(int) $subject->id] ?? 0);
                        @endphp
                        <article
                            class="group rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">{{ $subject->name }}</h3>
                                    <div class="text-xs text-slate-500">{{ $subject->code ?: 'No code' }}</div>
                                </div>
                                <span
                                    class="rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700">
                                    {{ number_format($slotCount) }} slot{{ $slotCount === 1 ? '' : 's' }}
                                </span>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                                @if ($subject->is_major_subject)
                                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700">
                                        Major
                                    </span>
                                @endif
                                @if ($subject->is_class_subject)
                                    <span class="rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-sky-700">
                                        Class
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3 text-xs text-slate-600">
                                <span class="font-semibold text-slate-700">Teacher:</span>
                                {{ $subject->teacher?->name ?: 'Not assigned' }}
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-slate-500">
                                {{ \Illuminate\Support\Str::limit((string) ($subject->description ?: 'No description available.'), 110) }}
                            </p>
                        </article>
                    @endforeach
                </div>
            @else
                <div
                    class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                    No subjects assigned yet. Please contact admin to assign class or major subjects.
                </div>
            @endif
        </section>

        <section class="student-reveal student-float rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
            style="--sd: 7;">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Subject Schedule</h2>
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ $selectedDayLabel }}
                    </span>
                    <span
                        class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        Today rows highlighted
                    </span>
                </div>
            </div>

            @if ($scheduleRows->isNotEmpty())
                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="max-h-[560px] overflow-auto">
                        <table class="w-full min-w-[860px] text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    @if ($dayColumnEnabled)
                                        <th class="px-3 py-3 font-semibold">Day</th>
                                    @endif
                                    <th class="px-3 py-3 font-semibold">Start</th>
                                    <th class="px-3 py-3 font-semibold">End</th>
                                    <th class="px-3 py-3 font-semibold">Subject</th>
                                    <th class="px-3 py-3 font-semibold">Period</th>
                                    <th class="px-3 py-3 font-semibold">Teacher</th>
                                    <th class="px-3 py-3 font-semibold">Class</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white/95">
                                @foreach ($scheduleRows as $row)
                                    <tr class="{{ $row['is_today'] ? 'bg-emerald-50/45' : 'hover:bg-slate-50/80' }}">
                                        @if ($dayColumnEnabled)
                                            <td class="px-3 py-3">
                                                <span
                                                    class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                                    {{ $row['day_label'] }}
                                                </span>
                                            </td>
                                        @endif
                                        <td class="px-3 py-3 font-semibold text-slate-700">{{ $row['start_label'] }}</td>
                                        <td class="px-3 py-3 font-semibold text-slate-700">{{ $row['end_label'] }}</td>
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-900">
                                                @if ($row['is_today'])
                                                    <span
                                                        class="mr-2 inline-block h-2 w-2 rounded-full bg-emerald-500 align-middle"></span>
                                                @endif
                                                {{ $row['subject_name'] }}
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $row['subject_code'] ?: 'No code' }}</div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span
                                                class="inline-flex items-center rounded-full border border-sky-100 bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                                {{ $row['period_label'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">{{ $row['teacher_name'] }}</td>
                                        <td class="px-3 py-3 text-slate-700">{{ $row['class_label'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div
                    class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                    No schedule found for the selected day.
                </div>
            @endif
        </section>
    </div>
@endsection
