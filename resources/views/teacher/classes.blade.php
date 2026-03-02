@extends('layout.teacher.navbar')

@section('page')
    <div class="teacher-classes-stage space-y-6">
        <section class="teacher-classes-reveal admin-page-header teacher-page-header" style="--sd: 1;">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">My Classes</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">View classes you teach and open class details.</p>
                </div>

                <div class="teacher-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">
                        Classes: {{ number_format($stats['classes'] ?? 0) }}
                    </span>
                    <span class="admin-page-stat admin-page-stat--sky">
                        Students: {{ number_format($stats['students'] ?? 0) }}
                    </span>
                    <span class="admin-page-stat admin-page-stat--indigo">
                        Subjects: {{ number_format($stats['subjects'] ?? 0) }}
                    </span>
                </div>
            </div>
        </section>

        <section
            class="teacher-classes-reveal teacher-classes-float rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
            style="--sd: 2;">
            <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-black text-slate-900">Class List</h2>
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

                <div class="grid gap-4">
                    <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl">
                        <div class="flex h-full flex-col">
                            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                                <h3 class="text-3xl font-black text-slate-900">Filters</h3>
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('teacher.classes.index') }}"
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

                            <form method="GET" action="{{ route('teacher.classes.index') }}"
                                class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                                <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                        <input id="q" name="q" type="text" value="{{ $search }}"
                                            placeholder="Search class, section, room, or subject"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    </section>
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Room</h4>
                                        <select id="room" name="room"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all" {{ ($room ?? 'all') === 'all' ? 'selected' : '' }}>All
                                                Rooms
                                            </option>
                                            @foreach ($roomOptions ?? collect() as $roomOption)
                                                <option value="{{ $roomOption }}"
                                                    {{ ($room ?? 'all') === (string) $roomOption ? 'selected' : '' }}>
                                                    {{ $roomOption }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Period</h4>
                                        <select id="period" name="period"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all" {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}>All
                                                Periods</option>
                                            <option value="morning"
                                                {{ ($period ?? 'all') === 'morning' ? 'selected' : '' }}>
                                                Morning</option>
                                            <option value="afternoon"
                                                {{ ($period ?? 'all') === 'afternoon' ? 'selected' : '' }}>Afternoon
                                            </option>
                                            <option value="evening"
                                                {{ ($period ?? 'all') === 'evening' ? 'selected' : '' }}>
                                                Evening</option>
                                            <option value="night" {{ ($period ?? 'all') === 'night' ? 'selected' : '' }}>
                                                Night
                                            </option>
                                            <option value="custom" {{ ($period ?? 'all') === 'custom' ? 'selected' : '' }}>
                                                Custom</option>
                                        </select>
                                    </section>
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Schedule</h4>
                                        <select id="schedule" name="schedule"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all" {{ ($schedule ?? 'all') === 'all' ? 'selected' : '' }}>
                                                All
                                                Schedules</option>
                                            <option value="with_schedule"
                                                {{ ($schedule ?? 'all') === 'with_schedule' ? 'selected' : '' }}>With
                                                Schedule</option>
                                            <option value="without_schedule"
                                                {{ ($schedule ?? 'all') === 'without_schedule' ? 'selected' : '' }}>Without
                                                Schedule</option>
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

                    <div class="min-w-0">
                        <div class="overflow-hidden rounded-2xl border border-slate-200">
                            <div class="max-h-[560px] overflow-auto">
                                <table class="w-full min-w-[980px] text-left text-sm">
                                    <thead
                                        class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-3 py-3 font-semibold">Class</th>
                                            <th class="px-3 py-3 font-semibold">Room</th>
                                            <th class="px-3 py-3 font-semibold">Students</th>
                                            <th class="px-3 py-3 font-semibold">Subjects</th>
                                            <th class="px-3 py-3 font-semibold">Study Times</th>
                                            <th class="px-3 py-3 font-semibold text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @forelse ($classes as $schoolClass)
                                            <tr class="align-top hover:bg-slate-50/80" x-data="{ open: false }">
                                                <td class="px-3 py-3">
                                                    <div class="font-semibold text-slate-900">
                                                        {{ $schoolClass->display_name }}
                                                    </div>
                                                    <div class="text-xs text-slate-500">
                                                        Capacity: {{ $schoolClass->capacity ?: 'N/A' }}
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3 text-slate-700">{{ $schoolClass->room ?: 'N/A' }}
                                                </td>
                                                <td class="px-3 py-3 text-slate-700">
                                                    {{ number_format($schoolClass->students_count ?? 0) }}</td>
                                                <td class="px-3 py-3">
                                                    <div class="flex max-w-sm flex-wrap gap-1.5">
                                                        @forelse ($schoolClass->subjects as $subject)
                                                            <span
                                                                class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700">
                                                                {{ $subject->name }}
                                                            </span>
                                                        @empty
                                                            <span class="text-xs text-slate-400">No subjects</span>
                                                        @endforelse
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3">
                                                    <div class="flex max-w-sm flex-wrap gap-1.5">
                                                        @forelse ($schoolClass->studySchedules as $slot)
                                                            @php
                                                                $periodKey = strtolower((string) $slot->period);
                                                                $periodLabel =
                                                                    $periodLabels[$periodKey] ?? ucfirst($periodKey);
                                                            @endphp
                                                            <span
                                                                class="inline-flex items-center gap-1.5 rounded-lg border border-sky-100 bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700">
                                                                {{ $periodLabel }}
                                                                <span class="text-sky-300">|</span>
                                                                {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                                                ->
                                                                {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                            </span>
                                                        @empty
                                                            <span class="text-xs text-slate-400">No times</span>
                                                        @endforelse
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3">
                                                    <div class="flex justify-end">
                                                        <button type="button" @click="open = true"
                                                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                            View Details
                                                        </button>
                                                    </div>

                                                    <div x-show="open" x-cloak
                                                        class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                        aria-modal="true" role="dialog">
                                                        <div class="absolute inset-0 bg-slate-900/50"
                                                            @click="open = false">
                                                        </div>

                                                        <div
                                                            class="relative z-10 w-full max-w-2xl max-h-[calc(100vh-2rem)] overflow-y-auto rounded-3xl bg-white p-5 shadow-2xl sm:max-h-[85vh]">
                                                            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                                                                <h3 class="text-lg font-black text-slate-900">
                                                                    Class Detail: {{ $schoolClass->display_name }}
                                                                </h3>
                                                                <button type="button" @click="open = false"
                                                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                        fill="currentColor">
                                                                        <path
                                                                            d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <div class="grid gap-4 sm:grid-cols-3">
                                                                <div
                                                                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                                                    <div class="text-xs font-semibold text-slate-500">Room
                                                                    </div>
                                                                    <div class="mt-1 text-sm font-semibold text-slate-900">
                                                                        {{ $schoolClass->room ?: 'N/A' }}</div>
                                                                </div>
                                                                <div
                                                                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                                                    <div class="text-xs font-semibold text-slate-500">
                                                                        Capacity
                                                                    </div>
                                                                    <div class="mt-1 text-sm font-semibold text-slate-900">
                                                                        {{ $schoolClass->capacity ?: 'N/A' }}</div>
                                                                </div>
                                                                <div
                                                                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                                                    <div class="text-xs font-semibold text-slate-500">
                                                                        Students
                                                                    </div>
                                                                    <div class="mt-1 text-sm font-semibold text-slate-900">
                                                                        {{ number_format($schoolClass->students_count ?? 0) }}
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="mt-4">
                                                                <h4 class="text-sm font-black text-slate-900">Subjects You
                                                                    Teach
                                                                </h4>
                                                                <div class="mt-2 flex flex-wrap gap-2">
                                                                    @forelse ($schoolClass->subjects as $subject)
                                                                        <span
                                                                            class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                                                            {{ $subject->name }}
                                                                        </span>
                                                                    @empty
                                                                        <span class="text-xs text-slate-400">No subjects
                                                                            assigned.</span>
                                                                    @endforelse
                                                                </div>
                                                            </div>

                                                            <div class="mt-4">
                                                                <h4 class="text-sm font-black text-slate-900">Class Study
                                                                    Schedule</h4>
                                                                <div class="mt-2 space-y-2">
                                                                    @forelse ($schoolClass->studySchedules as $slot)
                                                                        @php
                                                                            $periodKey = strtolower(
                                                                                (string) $slot->period,
                                                                            );
                                                                            $periodLabel =
                                                                                $periodLabels[$periodKey] ??
                                                                                ucfirst($periodKey);
                                                                        @endphp
                                                                        <div
                                                                            class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                                                            <span
                                                                                class="font-semibold text-slate-800">{{ $periodLabel }}</span>
                                                                            <span class="font-semibold text-slate-600">
                                                                                {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                                                                ->
                                                                                {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                                            </span>
                                                                        </div>
                                                                    @empty
                                                                        <div
                                                                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-500">
                                                                            No class study schedule configured.
                                                                        </div>
                                                                    @endforelse
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-3 py-10 text-center text-sm text-slate-500">
                                                    No classes found for your account.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-5">
                            {{ $classes->links() }}
                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection
