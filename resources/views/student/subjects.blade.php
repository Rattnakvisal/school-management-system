@extends('layout.students.navbar')

@section('page')
    @php
        $selectedDayLabel = $dayOptions[$selectedDay] ?? 'All Days';
        $subjectStatPanelClass =
            'rounded-[26px] border border-white/80 bg-white/90 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.55)] ring-1 ring-slate-200 backdrop-blur';
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
            </div>
        </section>

        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <article class="student-reveal student-float {{ $subjectStatPanelClass }} min-h-[132px] p-5" style="--sd: 2;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-indigo-100 to-white text-indigo-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 19.5V5a2 2 0 0 1 2-2h11a3 3 0 0 1 3 3v14a2 2 0 0 0-2-2H6a2 2 0 0 0-2 1.5Z" />
                            <path d="M8 7h7" />
                            <path d="M8 11h5" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($subjects->count()) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format($subjects->count()) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Subjects</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Assigned:
                    {{ number_format($subjects->count()) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400"
                        style="width: {{ $subjects->count() > 0 ? 100 : 0 }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $subjectStatPanelClass }} min-h-[132px] p-5" style="--sd: 3;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-emerald-100 to-white text-emerald-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m12 3 8 4-8 4-8-4 8-4Z" />
                            <path d="M6 10.5V16c0 1.66 2.69 3 6 3s6-1.34 6-3v-5.5" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($majorSubjectsCount) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format(max(1, $subjects->count())) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Major</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Focus:
                    {{ number_format($majorSubjectsCount) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-emerald-500 to-cyan-400"
                        style="width: {{ min(100, max(0, (int) round(($majorSubjectsCount / max(1, $subjects->count())) * 100))) }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $subjectStatPanelClass }} min-h-[132px] p-5" style="--sd: 4;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-white text-sky-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 6h16" />
                            <path d="M4 12h16" />
                            <path d="M4 18h16" />
                            <path d="M8 6v12" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($classSubjectsCount) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format(max(1, $subjects->count())) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Class</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Home class:
                    {{ number_format($classSubjectsCount) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-sky-500 to-cyan-400"
                        style="width: {{ min(100, max(0, (int) round(($classSubjectsCount / max(1, $subjects->count())) * 100))) }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $subjectStatPanelClass }} min-h-[132px] p-5" style="--sd: 5;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-amber-100 to-white text-amber-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 3" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($todayScheduleCount) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format(max(1, $scheduleCount)) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Today</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Schedule rows:
                    {{ number_format($todayScheduleCount) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-amber-500 to-cyan-400"
                        style="width: {{ min(100, max(0, (int) round(($todayScheduleCount / max(1, $scheduleCount)) * 100))) }}%"></span>
                </div>
            </article>
        </section>

        <section x-data="{ subjectFilterOpen: false }"
            class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200"
            style="--sd: 7;">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Subject Schedule</h2>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="subjectFilterOpen = true"
                        class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-indigo-700 shadow-sm transition hover:bg-indigo-50">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                        </svg>
                        Filters
                    </button>
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

            <div x-show="subjectFilterOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[80] bg-slate-900/40"
                @click="subjectFilterOpen = false"></div>

            <aside x-show="subjectFilterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 z-[81] flex w-full max-w-xl transform flex-col border-l border-slate-200 bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <h3 class="text-3xl font-black text-slate-900">Filters</h3>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('student.subjects.index') }}"
                            class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-800">
                            Clear All
                        </a>
                        <button type="button" @click="subjectFilterOpen = false"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-3xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900"
                            aria-label="Close filters">
                            &times;
                        </button>
                    </div>
                </div>

                <form method="GET" action="{{ route('student.subjects.index') }}"
                    class="flex min-h-0 flex-1 flex-col" @submit="subjectFilterOpen = false">
                    <div class="nav-scrollbar flex-1 space-y-7 overflow-y-auto px-6 py-6">
                        <section class="space-y-3">
                            <h4 class="text-2xl font-black text-slate-900">Day</h4>
                            <select id="schedule-day" name="day"
                                class="h-12 w-full rounded-3xl border border-slate-300 bg-white px-5 text-base text-slate-800 outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                @foreach ($dayOptions as $dayKey => $dayLabel)
                                    <option value="{{ $dayKey }}" {{ $selectedDay === $dayKey ? 'selected' : '' }}>
                                        {{ $dayLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </section>

                        @php
                            $quickFilterKeys = [
                                'all',
                                'monday',
                                'tuesday',
                                'wednesday',
                                'thursday',
                                'friday',
                                'saturday',
                                'sunday',
                            ];
                        @endphp
                        <section class="space-y-3">
                            <h4 class="text-2xl font-black text-slate-900">Quick Days</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($quickFilterKeys as $quickKey)
                                    @php
                                        $isActiveQuick = $selectedDay === $quickKey;
                                        $quickLabel = $dayOptions[$quickKey] ?? ucfirst($quickKey);
                                    @endphp
                                    <a href="{{ route('student.subjects.index', ['day' => $quickKey]) }}"
                                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ $isActiveQuick
                                            ? 'border-indigo-200 bg-indigo-50 text-indigo-700 shadow-sm'
                                            : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50' }}">
                                        {{ $quickLabel }}
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    </div>

                    <div class="border-t border-slate-200 px-6 py-5">
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-950 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-slate-800">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </aside>

            @if ($scheduleRows->isNotEmpty())
                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="student-table-scroller max-h-[720px] overflow-auto">
                        <table class="student-table student-schedule-table w-full whitespace-nowrap text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    @if ($dayColumnEnabled)
                                        <th class="student-col-day px-3 py-3 font-semibold">Day</th>
                                    @endif
                                    <th class="student-col-time px-3 py-3 font-semibold">Start</th>
                                    <th class="student-col-time px-3 py-3 font-semibold">End</th>
                                    <th class="student-col-subject px-3 py-3 font-semibold">Subject</th>
                                    <th class="student-col-details px-3 py-3 font-semibold">Details</th>
                                    <th class="student-col-period px-3 py-3 font-semibold">Period</th>
                                    <th class="student-col-teacher px-3 py-3 font-semibold">Teacher</th>
                                    <th class="student-col-class px-3 py-3 font-semibold">Class</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($scheduleRows as $row)
                                    <tr class="{{ $row['is_today'] ? 'bg-emerald-50/45' : 'hover:bg-slate-50/80' }}">
                                        @if ($dayColumnEnabled)
                                            <td class="student-col-day px-3 py-3 align-top">
                                                <span
                                                    class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                                    {{ $row['day_label'] }}
                                                </span>
                                            </td>
                                        @endif
                                        <td class="student-col-time px-3 py-3 align-top font-semibold text-slate-700">
                                            {{ $row['start_label'] }}</td>
                                        <td class="student-col-time px-3 py-3 align-top font-semibold text-slate-700">
                                            {{ $row['end_label'] }}</td>
                                        <td class="student-col-subject px-3 py-3 align-top">
                                            <div class="student-name font-semibold text-slate-900">
                                                @if ($row['is_today'])
                                                    <span
                                                        class="mr-2 inline-block h-2 w-2 rounded-full bg-emerald-500 align-middle"></span>
                                                @endif
                                                {{ $row['subject_name'] }}
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $row['subject_code'] ?: 'No code' }}
                                            </div>
                                        </td>
                                        <td class="student-col-details px-3 py-3 align-top">
                                            <div class="space-y-1.5">
                                                <div class="student-schedule-details text-xs leading-relaxed text-slate-600">
                                                    {{ $row['subject_description'] ?: 'No description available.' }}
                                                </div>
                                                <div class="flex flex-wrap gap-1.5 text-[11px] font-semibold">
                                                    @if (!empty($row['subject_is_major']))
                                                        <span
                                                            class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700">Major</span>
                                                    @endif
                                                    @if (!empty($row['subject_is_class']))
                                                        <span
                                                            class="rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-sky-700">Class</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="student-col-period px-3 py-3 align-top">
                                            <span
                                                class="inline-flex items-center rounded-full border border-sky-100 bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                                {{ $row['period_label'] }}
                                            </span>
                                        </td>
                                        <td class="student-col-teacher px-3 py-3 align-top text-slate-700">
                                            {{ $row['teacher_name'] }}
                                            @if (!empty($row['teacher_email']))
                                                <div class="student-email text-xs text-slate-500">{{ $row['teacher_email'] }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="student-col-class px-3 py-3 align-top text-slate-700">
                                            {{ $row['class_label'] }}
                                            @if (!empty($row['class_code']))
                                                <div class="text-xs text-slate-500">{{ $row['class_code'] }}</div>
                                            @endif
                                        </td>
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
