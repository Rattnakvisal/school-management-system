@extends('layout.admin.navbar')

@section('page')
    @php
        $classTotal = max(0, (int) ($stats['total'] ?? 0));

        $classStatCards = [
            [
                'label' => 'Classes',
                'activeLabel' => 'Total',
                'active' => $classTotal,
                'total' => $classTotal,
                'icon' => 'classes',
                'tone' =>
                    'from-indigo-100 to-white text-indigo-600 dark:from-indigo-500/20 dark:to-slate-900 dark:text-indigo-300',
            ],
            [
                'label' => 'Active',
                'activeLabel' => 'Active',
                'active' => (int) ($stats['active'] ?? 0),
                'total' => $classTotal,
                'icon' => 'active',
                'tone' =>
                    'from-emerald-100 to-white text-emerald-600 dark:from-emerald-500/20 dark:to-slate-900 dark:text-emerald-300',
            ],
            [
                'label' => 'Inactive',
                'activeLabel' => 'Inactive',
                'active' => (int) ($stats['inactive'] ?? 0),
                'total' => $classTotal,
                'icon' => 'inactive',
                'tone' =>
                    'from-rose-100 to-white text-rose-600 dark:from-rose-500/20 dark:to-slate-900 dark:text-rose-300',
            ],
            [
                'label' => 'With Schedule',
                'activeLabel' => 'Scheduled',
                'active' => (int) ($stats['withSchedule'] ?? 0),
                'total' => $classTotal,
                'icon' => 'time',
                'tone' => 'from-sky-100 to-white text-sky-600 dark:from-sky-500/20 dark:to-slate-900 dark:text-sky-300',
            ],
        ];

        $showCreateFormOnLoad = old('_form') === 'create_class';

        $dayLabels = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
            'all' => 'All Days',
        ];

        $panelClass =
            'class-reveal class-float rounded-[28px] border border-slate-200/80 bg-white/95 p-5 shadow-[0_24px_55px_-42px_rgba(15,23,42,0.75)] ring-1 ring-slate-200/70 dark:border-slate-700/80 dark:bg-slate-900/95 dark:ring-slate-700/80 dark:shadow-[0_24px_70px_-42px_rgba(0,0,0,0.9)]';

        $labelClass = 'mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300';

        $inputClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20';

        $softBoxClass =
            'rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-800/70 dark:text-slate-300';

        $modalPanelClass =
            'relative z-10 flex max-h-[calc(100vh-2rem)] w-full flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900';
    @endphp

    <div class="class-stage admin-management-page class-page mx-auto max-w-[1500px] space-y-6 pb-8 text-slate-900 dark:text-slate-100">
        <x-admin.page-header reveal-class="class-reveal" delay="1" icon="classes" title="Class Management"
            subtitle="Create, organize, and manage classroom groups." />

        <x-admin.stat-cards :cards="$classStatCards" reveal-class="class-reveal" float-class="class-float"
            grid-class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4" />

        @if (session('success'))
            <div class="class-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="class-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="class-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300"
                style="--sd: 2;">
                <div class="font-semibold">Please check the form fields and try again.</div>
            </div>
        @endif

        <div x-data="{ createOpen: @js($showCreateFormOnLoad) }"
            @open-class-create.window="
                createOpen = true;
                $nextTick(() => document.getElementById('name')?.focus());
            "
            class="grid gap-6 xl:grid-cols-12">
            {{-- CREATE CLASS --}}
            <section x-show="createOpen" x-cloak x-transition.opacity.duration.150ms
                @keydown.escape.window="createOpen = false" @click.self="createOpen = false" role="dialog"
                aria-modal="true" aria-labelledby="create-class-title"
                class="fixed inset-0 z-[90] flex items-start justify-center overflow-y-auto bg-slate-950/65 px-4 py-6 backdrop-blur-sm sm:py-10"
                style="--sd: 3;">
                <div
                    class="w-full max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl ring-1 ring-slate-950/5 dark:border-slate-700 dark:bg-slate-900 dark:ring-white/10">

                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-700">
                        <div>
                            <h2 id="create-class-title" class="text-2xl font-black text-slate-950 dark:text-white">
                                Create Class
                            </h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400">
                                Define a classroom for students and subject assignment.
                            </p>
                        </div>

                        <button type="button" @click="createOpen = false" aria-controls="create-class-form-panel"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-600 text-lg font-black leading-none text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus:ring-4 focus:ring-red-200 dark:focus:ring-red-500/25"
                            aria-label="Close create class form">
                            &times;
                        </button>
                    </div>

                <form id="create-class-form-panel" method="POST" action="{{ route('admin.classes.store') }}"
                    class="js-create-form">
                    @csrf

                    <input type="hidden" name="_form" value="create_class">

                    <div class="max-h-[calc(100vh-15rem)] overflow-y-auto px-6 py-5">
                        <div class="grid gap-5 lg:grid-cols-2">

                    <div>
                        <label for="name" class="{{ $labelClass }}">Class Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                            class="{{ $inputClass }}" placeholder="Grade 10">

                        @error('name')
                            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="room" class="{{ $labelClass }}">Room</label>
                        <input id="room" name="room" type="text" value="{{ old('room') }}"
                            class="{{ $inputClass }}" placeholder="B-201">

                        @error('room')
                            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Study Times</label>
                        <div class="{{ $softBoxClass }}">
                            Study times are now managed in Time Studies.
                            <a href="{{ route('admin.time-studies.index', ['tab' => 'class']) }}"
                                class="font-semibold text-indigo-700 transition hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-indigo-200">
                                Manage class times
                            </a>
                        </div>
                    </div>

                    <div>
                        <label for="capacity" class="{{ $labelClass }}">Capacity</label>
                        <input id="capacity" name="capacity" type="number" min="1" max="500"
                            value="{{ old('capacity') }}" class="{{ $inputClass }}" placeholder="40">

                        @error('capacity')
                            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <label
                        class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Initial Status</span>

                        <span
                            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            <input type="checkbox" name="is_active" value="1"
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 dark:border-slate-600 dark:bg-slate-900"
                                {{ old('is_active', '1') ? 'checked' : '' }}>
                            Active
                        </span>
                    </label>

                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end dark:border-slate-700 dark:bg-slate-950/40">
                        <button type="button" @click="createOpen = false"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            Cancel
                        </button>

                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-indigo-500/20 transition hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/25">
                            Create Class
                        </button>
                    </div>
                </form>
                </div>
            </section>

            {{-- CLASS LIST --}}
            <section class="{{ $panelClass }} xl:col-span-12" style="--sd: 4;">
                <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-lg font-black text-slate-950 dark:text-white">Class List</h2>

                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-class-create'))"
                                class="inline-flex min-w-[150px] items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-500/20 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/25">
                                <i class="fa-solid fa-plus text-xs"></i>
                                Create
                            </button>

                            <button type="button" @click="filterOpen = true"
                                class="inline-flex min-w-[150px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                <i class="fa-solid fa-filter text-xs"></i>
                                Filters
                            </button>
                        </div>
                    </div>

                    {{-- FILTER OVERLAY --}}
                    <div x-show="filterOpen" x-cloak x-transition.opacity
                        class="fixed inset-0 z-[80] bg-slate-900/50 backdrop-blur-sm" @click="filterOpen = false"></div>

                    {{-- FILTER DRAWER --}}
                    <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">

                        <div class="flex h-full flex-col">
                            <div
                                class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                                <h3 class="text-3xl font-black text-slate-950 dark:text-white">Filters</h3>

                                <div class="flex items-center gap-4">
                                    <a href="{{ route('admin.classes.index') }}"
                                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                        Clear All
                                    </a>

                                    <button type="button" @click="filterOpen = false"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                        aria-label="Close filters">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('admin.classes.index') }}"
                                class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                                <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Search</h4>
                                        <input id="q" name="q" type="text" value="{{ $search }}"
                                            placeholder="Search class, room, morning/night/custom time, or schedule"
                                            class="{{ $inputClass }}">
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Status</h4>
                                        <select name="status" class="{{ $inputClass }}">
                                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Period</h4>
                                        <select name="period" class="{{ $inputClass }}">
                                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Periods
                                            </option>
                                            @foreach ($periodOptions as $periodKey => $periodLabel)
                                                <option value="{{ $periodKey }}"
                                                    {{ $period === $periodKey ? 'selected' : '' }}>
                                                    {{ $periodLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Schedule</h4>
                                        <select name="schedule" class="{{ $inputClass }}">
                                            <option value="all" {{ $schedule === 'all' ? 'selected' : '' }}>All
                                                Schedules</option>
                                            <option value="with_schedule"
                                                {{ $schedule === 'with_schedule' ? 'selected' : '' }}>With Schedule
                                            </option>
                                            <option value="without_schedule"
                                                {{ $schedule === 'without_schedule' ? 'selected' : '' }}>Without Schedule
                                            </option>
                                        </select>
                                    </section>
                                </div>

                                <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500">
                                        Apply Filters
                                    </button>
                                </div>
                            </form>
                        </div>
                    </aside>

                    {{-- ACTIVE FILTERS --}}
                    @if ($search !== '' || $status !== 'all' || $period !== 'all' || $schedule !== 'all')
                        <div
                            class="flex flex-wrap items-center gap-2 rounded-2xl bg-indigo-50/70 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-indigo-500/10 dark:text-slate-300">
                            <span class="text-indigo-700 dark:text-indigo-300">Active filters:</span>

                            @if ($search !== '')
                                <span
                                    class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                    Search: {{ $search }}
                                </span>
                            @endif

                            @if ($status !== 'all')
                                <span
                                    class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                    Status: {{ ucfirst($status) }}
                                </span>
                            @endif

                            @if ($period !== 'all')
                                <span
                                    class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                    Period: {{ $periodOptions[$period] ?? ucfirst($period) }}
                                </span>
                            @endif

                            @if ($schedule !== 'all')
                                <span
                                    class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                    Schedule: {{ str_replace('_', ' ', ucfirst($schedule)) }}
                                </span>
                            @endif

                            <a href="{{ route('admin.classes.index') }}"
                                class="ml-auto rounded-full bg-white px-2.5 py-1 text-indigo-700 ring-1 ring-indigo-100 hover:bg-indigo-100 dark:bg-slate-800 dark:text-indigo-300 dark:ring-indigo-400/20 dark:hover:bg-slate-700">
                                Clear
                            </a>
                        </div>
                    @endif

                    <div class="min-w-0">
                        <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700">
                            <div class="class-table-scroller max-h-[700px] overflow-auto">
                                <table class="admin-table class-table w-full min-w-[1400px] text-left text-sm">
                                    <thead
                                        class="admin-table-head sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                        <tr>
                                            <th class="px-3 py-3 font-semibold">Class</th>
                                            <th class="px-3 py-3 font-semibold">Room</th>
                                            <th class="whitespace-nowrap px-3 py-3 font-semibold">Study Time</th>
                                            <th class="px-3 py-3 font-semibold">Capacity</th>
                                            <th class="px-3 py-3 font-semibold">Subjects</th>
                                            <th class="px-3 py-3 font-semibold">Students</th>
                                            <th class="px-3 py-3 font-semibold">Status</th>
                                            <th class="whitespace-nowrap px-3 py-3 font-semibold">Created</th>
                                            <th class="px-3 py-3 text-right font-semibold">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody
                                        class="divide-y divide-slate-100 bg-white dark:divide-slate-700 dark:bg-slate-900">
                                        @php
                                            /** @var iterable<int, \App\Models\SchoolClass> $classRows */
                                            $classRows =
                                                $classes instanceof \Illuminate\Pagination\AbstractPaginator
                                                    ? $classes->getCollection()->all()
                                                    : (is_iterable($classes)
                                                        ? $classes
                                                        : []);
                                        @endphp

                                        @forelse ($classRows as $schoolClass)
                                            @php
                                                if (!($schoolClass instanceof \App\Models\SchoolClass)) {
                                                    continue;
                                                }

                                                $studyScheduleRows = $schoolClass->studySchedules
                                                    ->sortBy(function ($scheduleRow) {
                                                        $daySortMap = [
                                                            'monday' => 1,
                                                            'tuesday' => 2,
                                                            'wednesday' => 3,
                                                            'thursday' => 4,
                                                            'friday' => 5,
                                                            'saturday' => 6,
                                                            'sunday' => 7,
                                                            'all' => 8,
                                                        ];

                                                        $dayKey = strtolower(
                                                            (string) ($scheduleRow->day_of_week ?? 'all'),
                                                        );

                                                        return sprintf(
                                                            '%02d-%s-%s',
                                                            $daySortMap[$dayKey] ?? 99,
                                                            (string) ($scheduleRow->period ?? ''),
                                                            (string) ($scheduleRow->start_time ?? ''),
                                                        );
                                                    })
                                                    ->values();

                                                $previewStudySchedules = $studyScheduleRows->take(3);
                                            @endphp

                                            <tr class="align-top transition hover:bg-slate-50/80 dark:hover:bg-slate-800/70"
                                                x-data="{ openEdit: false, detailOpen: false }">
                                                <td class="whitespace-nowrap px-3 py-3">
                                                    <div class="font-semibold text-slate-800 dark:text-slate-100">
                                                        {{ $schoolClass->name }}
                                                    </div>
                                                </td>

                                                <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                    {{ $schoolClass->room ?: '-' }}
                                                </td>

                                                <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                    @if ($studyScheduleRows->isNotEmpty())
                                                        <div class="space-y-2">
                                                            <div class="flex max-w-xl flex-wrap gap-1.5">
                                                                @foreach ($previewStudySchedules as $scheduleRow)
                                                                    @php
                                                                        $scheduleDayKey = strtolower(
                                                                            (string) ($scheduleRow->day_of_week ??
                                                                                'all'),
                                                                        );
                                                                        $scheduleDayLabel =
                                                                            $dayLabels[$scheduleDayKey] ??
                                                                            ucfirst($scheduleDayKey);
                                                                        $schedulePeriodLabel =
                                                                            $periodOptions[$scheduleRow->period] ??
                                                                            ucfirst($scheduleRow->period);
                                                                        $startTime = \Carbon\Carbon::parse(
                                                                            $scheduleRow->start_time,
                                                                        )->format('h:i A');
                                                                        $endTime = \Carbon\Carbon::parse(
                                                                            $scheduleRow->end_time,
                                                                        )->format('h:i A');
                                                                    @endphp

                                                                    <div
                                                                        class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300">
                                                                        <span
                                                                            class="uppercase tracking-wide">{{ $scheduleDayLabel }}</span>
                                                                        <span
                                                                            class="text-emerald-300 dark:text-emerald-400">|</span>
                                                                        <span
                                                                            class="font-bold">{{ $schedulePeriodLabel }}</span>
                                                                        <span
                                                                            class="text-emerald-300 dark:text-emerald-400">|</span>
                                                                        <span
                                                                            class="whitespace-nowrap text-slate-700 dark:text-slate-300">
                                                                            {{ $startTime }} -> {{ $endTime }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            @if ($studyScheduleRows->count() > $previewStudySchedules->count())
                                                                <div
                                                                    class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">
                                                                    +{{ $studyScheduleRows->count() - $previewStudySchedules->count() }}
                                                                    more slots
                                                                </div>
                                                            @endif

                                                            <div class="text-[11px] text-slate-400 dark:text-slate-500">
                                                                {{ $schoolClass->study_schedules_count }} slots
                                                            </div>
                                                        </div>
                                                    @else
                                                        @php
                                                            $classStartTime =
                                                                $schoolClass->study_start_time ?:
                                                                $schoolClass->study_time;
                                                            $classEndTime = $schoolClass->study_end_time;
                                                        @endphp

                                                        @if ($classStartTime && $classEndTime)
                                                            <span
                                                                class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                                {{ \Carbon\Carbon::parse($classStartTime)->format('h:i A') }}
                                                                ->
                                                                {{ \Carbon\Carbon::parse($classEndTime)->format('h:i A') }}
                                                            </span>
                                                        @elseif ($classStartTime)
                                                            {{ \Carbon\Carbon::parse($classStartTime)->format('h:i A') }}
                                                        @else
                                                            <span class="text-slate-400 dark:text-slate-500">-</span>
                                                        @endif
                                                    @endif
                                                </td>

                                                <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                    {{ $schoolClass->capacity ? number_format($schoolClass->capacity) : '-' }}
                                                </td>

                                                <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                    {{ $schoolClass->subjects_count }}
                                                </td>

                                                <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                    {{ $schoolClass->students_count ?? 0 }}
                                                </td>

                                                <td class="whitespace-nowrap px-3 py-3">
                                                    @if ($schoolClass->is_active)
                                                        <span
                                                            class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                                                            <span
                                                                class="status-dot h-2 w-2 rounded-full bg-emerald-500"></span>
                                                            Active
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/15 dark:text-rose-300">
                                                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                                            Inactive
                                                        </span>
                                                    @endif
                                                </td>

                                                <td class="whitespace-nowrap px-3 py-3 text-slate-500 dark:text-slate-400">
                                                    {{ $schoolClass->created_at->format('M d, Y') }}
                                                </td>

                                                <td class="whitespace-nowrap px-3 py-3">
                                                    <div
                                                        class="flex flex-nowrap items-center justify-end gap-2 whitespace-nowrap">
                                                        <button @click="detailOpen = true" type="button"
                                                            class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                            View Detail
                                                        </button>

                                                        <button @click="openEdit = true" type="button"
                                                            class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                            Edit
                                                        </button>

                                                        <form method="POST"
                                                            action="{{ route('admin.classes.status', $schoolClass) }}"
                                                            class="js-status-form"
                                                            data-class="{{ $schoolClass->display_name }}"
                                                            data-action="{{ $schoolClass->is_active ? 'set inactive' : 'set active' }}">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button type="submit"
                                                                class="whitespace-nowrap rounded-lg border px-3 py-1.5 text-xs font-semibold transition
                                                                {{ $schoolClass->is_active
                                                                    ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300 dark:hover:bg-amber-500/25'
                                                                    : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300 dark:hover:bg-emerald-500/25' }}">
                                                                {{ $schoolClass->is_active ? 'Set Inactive' : 'Set Active' }}
                                                            </button>
                                                        </form>

                                                        <form method="POST"
                                                            action="{{ route('admin.classes.destroy', $schoolClass) }}"
                                                            class="js-delete-form"
                                                            data-class="{{ $schoolClass->display_name }}">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300 dark:hover:bg-red-500/25">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>

                                                    {{-- DETAIL MODAL --}}
                                                    <div x-show="detailOpen" x-cloak
                                                        class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                        aria-modal="true" role="dialog">
                                                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                                                            @click="detailOpen = false"></div>

                                                        <div class="{{ $modalPanelClass }} max-w-3xl">
                                                            <div
                                                                class="flex items-start justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                                                                <div>
                                                                    <h3
                                                                        class="text-lg font-black text-slate-950 dark:text-white">
                                                                        Class Detail</h3>
                                                                    <p
                                                                        class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                                                        {{ $schoolClass->display_name }} &bull;
                                                                        {{ $schoolClass->room ?: 'No room assigned' }}
                                                                    </p>
                                                                </div>

                                                                <button type="button" @click="detailOpen = false"
                                                                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                                                                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                        fill="currentColor">
                                                                        <path
                                                                            d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <div class="overflow-y-auto p-5">
                                                                <div class="grid gap-4 md:grid-cols-2">
                                                                    <div
                                                                        class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/70">
                                                                        <div
                                                                            class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                                                            Overview
                                                                        </div>

                                                                        <div class="mt-3 space-y-3 text-sm">
                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span
                                                                                    class="text-slate-500 dark:text-slate-400">Class</span>
                                                                                <span
                                                                                    class="font-semibold text-slate-900 dark:text-white">{{ $schoolClass->display_name }}</span>
                                                                            </div>

                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span
                                                                                    class="text-slate-500 dark:text-slate-400">Room</span>
                                                                                <span
                                                                                    class="font-semibold text-slate-900 dark:text-white">{{ $schoolClass->room ?: '-' }}</span>
                                                                            </div>

                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span
                                                                                    class="text-slate-500 dark:text-slate-400">Capacity</span>
                                                                                <span
                                                                                    class="font-semibold text-slate-900 dark:text-white">{{ $schoolClass->capacity ? number_format($schoolClass->capacity) : '-' }}</span>
                                                                            </div>

                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span
                                                                                    class="text-slate-500 dark:text-slate-400">Subjects</span>
                                                                                <span
                                                                                    class="font-semibold text-slate-900 dark:text-white">{{ $schoolClass->subjects_count }}</span>
                                                                            </div>

                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span
                                                                                    class="text-slate-500 dark:text-slate-400">Students</span>
                                                                                <span
                                                                                    class="font-semibold text-slate-900 dark:text-white">{{ $schoolClass->students_count ?? 0 }}</span>
                                                                            </div>

                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span
                                                                                    class="text-slate-500 dark:text-slate-400">Status</span>
                                                                                <span
                                                                                    class="font-semibold {{ $schoolClass->is_active ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                                                                                    {{ $schoolClass->is_active ? 'Active' : 'Inactive' }}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                                                                        <div
                                                                            class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3 dark:border-slate-700">
                                                                            <div>
                                                                                <div
                                                                                    class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                                                                    Full Schedule
                                                                                </div>

                                                                                <div
                                                                                    class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                                                                    {{ $studyScheduleRows->count() }} slots
                                                                                    total
                                                                                </div>
                                                                            </div>

                                                                            <a href="{{ route('admin.time-studies.index', ['tab' => 'class', 'class_id' => $schoolClass->id]) }}"
                                                                                class="text-xs font-semibold text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                                                Manage times
                                                                            </a>
                                                                        </div>

                                                                        <div
                                                                            class="mt-4 max-h-[420px] space-y-2 overflow-y-auto pr-1">
                                                                            @forelse ($studyScheduleRows as $scheduleRow)
                                                                                @php
                                                                                    $scheduleDayKey = strtolower(
                                                                                        (string) ($scheduleRow->day_of_week ??
                                                                                            'all'),
                                                                                    );
                                                                                    $scheduleDayLabel =
                                                                                        $dayLabels[$scheduleDayKey] ??
                                                                                        ucfirst($scheduleDayKey);
                                                                                    $schedulePeriodLabel =
                                                                                        $periodOptions[
                                                                                            $scheduleRow->period
                                                                                        ] ??
                                                                                        ucfirst($scheduleRow->period);
                                                                                    $startTime = \Carbon\Carbon::parse(
                                                                                        $scheduleRow->start_time,
                                                                                    )->format('h:i A');
                                                                                    $endTime = \Carbon\Carbon::parse(
                                                                                        $scheduleRow->end_time,
                                                                                    )->format('h:i A');
                                                                                @endphp

                                                                                <div
                                                                                    class="flex flex-wrap items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300">
                                                                                    <span
                                                                                        class="uppercase tracking-wide">{{ $scheduleDayLabel }}</span>
                                                                                    <span
                                                                                        class="text-emerald-300 dark:text-emerald-400">|</span>
                                                                                    <span>{{ $schedulePeriodLabel }}</span>
                                                                                    <span
                                                                                        class="text-emerald-300 dark:text-emerald-400">|</span>
                                                                                    <span
                                                                                        class="whitespace-nowrap text-slate-700 dark:text-slate-300">
                                                                                        {{ $startTime }} ->
                                                                                        {{ $endTime }}
                                                                                    </span>
                                                                                </div>
                                                                            @empty
                                                                                <div
                                                                                    class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                                                                    No study schedule configured.
                                                                                </div>
                                                                            @endforelse
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- EDIT MODAL --}}
                                                    <div x-show="openEdit" x-cloak
                                                        class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                        aria-modal="true" role="dialog">
                                                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                                                            @click="openEdit = false"></div>

                                                        <div class="{{ $modalPanelClass }} max-w-xl">
                                                            <div
                                                                class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                                                                <h3
                                                                    class="text-lg font-black text-slate-950 dark:text-white">
                                                                    Edit Class</h3>

                                                                <button type="button" @click="openEdit = false"
                                                                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                                                                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                        fill="currentColor">
                                                                        <path
                                                                            d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <form method="POST"
                                                                action="{{ route('admin.classes.update', $schoolClass) }}"
                                                                class="js-edit-form flex min-h-0 flex-1 flex-col"
                                                                data-class="{{ $schoolClass->display_name }}">
                                                                @csrf
                                                                @method('PUT')

                                                                <div
                                                                    class="flex-1 space-y-4 overflow-y-auto overscroll-contain px-5 pb-4 pt-4">
                                                                    <div>
                                                                        <label for="edit_name_{{ $schoolClass->id }}"
                                                                            class="{{ $labelClass }}">Class Name</label>
                                                                        <input id="edit_name_{{ $schoolClass->id }}"
                                                                            name="name" type="text"
                                                                            value="{{ $schoolClass->name }}"
                                                                            class="{{ $inputClass }}">
                                                                    </div>

                                                                    <div>
                                                                        <label for="edit_room_{{ $schoolClass->id }}"
                                                                            class="{{ $labelClass }}">Room</label>
                                                                        <input id="edit_room_{{ $schoolClass->id }}"
                                                                            name="room" type="text"
                                                                            value="{{ $schoolClass->room }}"
                                                                            class="{{ $inputClass }}">
                                                                    </div>

                                                                    <div>
                                                                        <label class="{{ $labelClass }}">Study
                                                                            Times</label>
                                                                        <div class="{{ $softBoxClass }}">
                                                                            Manage this class schedule from Time Studies.
                                                                            <a href="{{ route('admin.time-studies.index', ['tab' => 'class', 'class_id' => $schoolClass->id]) }}"
                                                                                class="font-semibold text-indigo-700 transition hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                                                Open Time Studies
                                                                            </a>
                                                                        </div>
                                                                    </div>

                                                                    <div>
                                                                        <label for="edit_capacity_{{ $schoolClass->id }}"
                                                                            class="{{ $labelClass }}">Capacity</label>
                                                                        <input id="edit_capacity_{{ $schoolClass->id }}"
                                                                            name="capacity" type="number" min="1"
                                                                            max="500"
                                                                            value="{{ $schoolClass->capacity }}"
                                                                            class="{{ $inputClass }}">
                                                                    </div>

                                                                    <label
                                                                        class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                                                                        <span
                                                                            class="text-sm font-semibold text-slate-700 dark:text-slate-200">Status</span>

                                                                        <span
                                                                            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                                                            <input type="checkbox" name="is_active"
                                                                                value="1"
                                                                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 dark:border-slate-600 dark:bg-slate-900"
                                                                                {{ $schoolClass->is_active ? 'checked' : '' }}>
                                                                            Active
                                                                        </span>
                                                                    </label>
                                                                </div>

                                                                <div
                                                                    class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4 dark:border-slate-700">
                                                                    <button type="button" @click="openEdit = false"
                                                                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                                        Cancel
                                                                    </button>

                                                                    <button type="submit"
                                                                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                                                        Save Changes
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10"
                                                    class="px-3 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                                    No classes found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="class-pagination mt-5 text-slate-700 dark:text-slate-300">
                            {{ $classes->links() }}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @php
        $classPageData = [
            'periodOptions' => $periodOptions,
            'validationErrors' => $errors->all(),
            'flash' => [
                'success' => session('success'),
                'warning' => session('warning'),
                'error' => session('error'),
            ],
        ];
    @endphp

    <script id="admin-classes-data" type="application/json">{!! json_encode($classPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    @vite(['resources/js/admin/classes.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
