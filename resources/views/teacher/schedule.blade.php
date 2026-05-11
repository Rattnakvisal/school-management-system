@extends('layout.teacher.navbar')

@section('page')
    @php
        $selectedClassEntity = collect($classes ?? [])->firstWhere('id', (int) ($classId ?? 0));
        $selectedClassLabel = $selectedClassEntity?->display_name ?? 'All Classes';
        $selectedDayLabel = $dayOptions[$selectedDay ?? 'all'] ?? 'All Days';
        $schedulePageData = [
            'dayLabels' => $dayOptions ?? [],
            'selectedDay' => $selectedDay ?? 'all',
        ];
    @endphp

    <div class="teacher-time-stage space-y-6">
        <section class="teacher-time-reveal admin-page-header teacher-page-header" style="--sd: 1;">
            <div class="admin-page-header__main flex flex-wrap items-start justify-between gap-4">
                <div class="admin-page-header__intro min-w-0">
                    <div class="admin-page-header__title-row flex items-start gap-3">
                        <span class="admin-page-header__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="8.5" />
                                <path d="M12 7.5v5l3.4 1.9" />
                                <path d="M12 2.5v2" />
                            </svg>
                        </span>

                        <div>
                            <div class="admin-page-header__eyebrow">Teacher Panel</div>
                            <h1 class="admin-page-title text-3xl font-black tracking-tight">My Schedule</h1>
                            <p class="admin-page-subtitle mt-1 text-sm">View your teaching list by day, time, subject, and
                                class.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="teacher-time-reveal teacher-time-float rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
            style="--sd: 2;" x-data="{ filterOpen: false, tab: 'teach' }">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 p-1">
                        <button type="button" @click="tab = 'class'"
                            :class="tab === 'class' ? 'bg-white text-slate-900 shadow-sm' :
                                'text-slate-600 hover:text-slate-800'"
                            class="rounded-lg px-4 py-2 text-sm font-semibold transition">
                            Class Times ({{ number_format($stats['classSlots'] ?? 0) }})
                        </button>
                        <button type="button" @click="tab = 'teach'"
                            :class="tab === 'teach' ? 'bg-white text-slate-900 shadow-sm' :
                                'text-slate-600 hover:text-slate-800'"
                            class="rounded-lg px-4 py-2 text-sm font-semibold transition">
                            Teach List ({{ number_format($stats['subjectSlots'] ?? 0) }})
                        </button>
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

                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-slate-700">
                        Class: {{ $selectedClassLabel }}
                    </span>
                    <span id="schedule-selected-day-pill"
                        class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-indigo-700">
                        Day: {{ $selectedDayLabel }}
                    </span>
                    @if (($search ?? '') !== '')
                        <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-sky-700">
                            Search: {{ $search }}
                        </span>
                    @endif
                </div>
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
                            <a href="{{ route('teacher.schedule.index') }}"
                                class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                                Clear All
                            </a>
                            <button type="button" @click="filterOpen = false"
                                class="text-2xl font-bold leading-none text-slate-700 hover:text-slate-900"
                                aria-label="Close filters">
                                &times;
                            </button>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('teacher.schedule.index') }}" class="flex min-h-0 flex-1 flex-col"
                        @submit="filterOpen = false">
                        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                            <section class="space-y-2">
                                <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                <input id="q" name="q" type="text" value="{{ $search }}"
                                    placeholder="Search class, subject, period, or time"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            </section>

                            <section class="space-y-2">
                                <h4 class="text-xl font-bold text-slate-900">Class</h4>
                                <select id="class_id" name="class_id"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <option value="all" {{ ($classId ?? 'all') === 'all' ? 'selected' : '' }}>All
                                        Classes
                                    </option>
                                    @foreach ($classes as $classOption)
                                        <option value="{{ $classOption->id }}"
                                            {{ ($classId ?? 'all') === (string) $classOption->id ? 'selected' : '' }}>
                                            {{ $classOption->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </section>

                            <section class="space-y-2">
                                <h4 class="text-xl font-bold text-slate-900">Day</h4>
                                <select id="day" name="day"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    @foreach ($dayOptions as $dayKey => $dayLabel)
                                        <option value="{{ $dayKey }}"
                                            {{ ($selectedDay ?? 'all') === $dayKey ? 'selected' : '' }}>
                                            {{ $dayLabel }}
                                        </option>
                                    @endforeach
                                </select>
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

            <div class="mt-5 min-w-0">
                <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200">
                    <div class="student-table-scroller max-h-[700px] overflow-auto">
                        <table x-show="tab === 'class'" x-cloak
                            class="student-table w-full whitespace-nowrap text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-3 py-3 font-semibold">Class</th>
                                    @if ($dayColumnEnabled)
                                        <th class="px-3 py-3 font-semibold">Day</th>
                                    @endif
                                    <th class="px-3 py-3 font-semibold">Period</th>
                                    <th class="px-3 py-3 font-semibold">Start</th>
                                    <th class="px-3 py-3 font-semibold">End</th>
                                    <th class="px-3 py-3 font-semibold">Status</th>
                                    <th class="px-3 py-3 font-semibold">Room</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($classSlots as $slot)
                                    @php
                                        $periodKey = strtolower((string) $slot->period);
                                        $periodLabel = $periodLabels[$periodKey] ?? ucfirst($periodKey);
                                        $slotDayKey = strtolower((string) ($slot->day_of_week ?? 'all'));
                                        $slotDayLabel = $dayOptions[$slotDayKey] ?? ucfirst($slotDayKey);
                                    @endphp
                                    <tr class="js-schedule-row align-top hover:bg-slate-50/80" data-type="class"
                                        data-label="{{ $slot->schoolClass?->display_name ?? 'Class Schedule' }}"
                                        data-day="{{ $slotDayKey }}"
                                        data-start="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i:s') }}"
                                        data-end="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i:s') }}">
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-900">
                                                {{ $slot->schoolClass?->display_name ?? 'Unknown Class' }}
                                            </div>
                                        </td>
                                        @if ($dayColumnEnabled)
                                            <td class="px-3 py-3">
                                                <span
                                                    class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                                    {{ $slotDayLabel }}
                                                </span>
                                            </td>
                                        @endif
                                        <td class="px-3 py-3">
                                            <span
                                                class="inline-flex items-center rounded-full border border-sky-100 bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                                {{ $periodLabel }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 font-semibold text-slate-700">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                        </td>
                                        <td class="px-3 py-3 font-semibold text-slate-700">
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <span
                                                class="js-live-status inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                                Scheduled
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">{{ $slot->schoolClass?->room ?: 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $dayColumnEnabled ? 7 : 6 }}"
                                            class="px-3 py-10 text-center text-sm text-slate-500">
                                            No class study times found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <table x-show="tab === 'teach'" x-cloak
                            class="student-table w-full whitespace-nowrap text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    @if ($dayColumnEnabled)
                                        <th class="px-3 py-3 font-semibold">Day</th>
                                    @endif
                                    <th class="px-3 py-3 font-semibold">Start</th>
                                    <th class="px-3 py-3 font-semibold">End</th>
                                    <th class="px-3 py-3 font-semibold">Subject</th>
                                    <th class="px-3 py-3 font-semibold">Class</th>
                                    <th class="px-3 py-3 font-semibold">Period</th>
                                    <th class="px-3 py-3 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($subjectSlots as $slot)
                                    @php
                                        $periodKey = strtolower((string) $slot->period);
                                        $periodLabel = $periodLabels[$periodKey] ?? ucfirst($periodKey);
                                        $slotDayKey = strtolower((string) ($slot->day_of_week ?? 'all'));
                                        $slotDayLabel = $dayOptions[$slotDayKey] ?? ucfirst($slotDayKey);
                                    @endphp
                                    <tr class="js-schedule-row align-top hover:bg-slate-50/80" data-type="subject"
                                        data-label="{{ ($slot->subject?->name ?? 'Subject Schedule') . ' | ' . ($slot->schoolClass?->display_name ?? ($slot->subject?->schoolClass?->display_name ?? 'Class')) }}"
                                        data-day="{{ $slotDayKey }}"
                                        data-start="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i:s') }}"
                                        data-end="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i:s') }}">
                                        @if ($dayColumnEnabled)
                                            <td class="px-3 py-3">
                                                <span
                                                    class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                                    {{ $slotDayLabel }}
                                                </span>
                                            </td>
                                        @endif
                                        <td class="px-3 py-3 font-semibold text-slate-700">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                        </td>
                                        <td class="px-3 py-3 font-semibold text-slate-700">
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-900">
                                                {{ $slot->subject?->name ?? 'Unknown Subject' }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">
                                            {{ $slot->schoolClass?->display_name ?: ($slot->subject?->schoolClass?->display_name ?: 'N/A') }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <span
                                                class="inline-flex items-center rounded-full border border-sky-100 bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                                {{ $periodLabel }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span
                                                class="js-live-status inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                                Scheduled
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $dayColumnEnabled ? 7 : 6 }}"
                                            class="px-3 py-10 text-center text-sm text-slate-500">
                                            No subject study times found.
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

    <script id="teacher-schedule-data" type="application/json">@json($schedulePageData)</script>
    @vite(['resources/js/Teacher/schedule.js'])
@endsection
