@extends('layout.admin.navbar.navbar')

@section('page')
        <div class="study-time-stage space-y-6">
            <!-- Header -->
            <section class="study-time-reveal admin-page-header overflow-hidden" style="--sd: 1;">
                <div class="flex flex-wrap items-center justify-between gap-5">
                    <div>
                        <h1 class="admin-page-title text-3xl font-black tracking-tight">Time Studies</h1>
                        <p class="admin-page-subtitle mt-1 text-sm">Manage class and subject study schedules in one place.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                        <span class="admin-page-stat">Class Slots: {{ $stats['classSlots'] }}</span>
                        <span class="admin-page-stat admin-page-stat--sky">Subject Slots: {{ $stats['subjectSlots'] }}</span>
                        <span class="admin-page-stat admin-page-stat--emerald">
                            Classes With Slots: {{ $stats['classesWithSlots'] }}
                        </span>
                        <span class="admin-page-stat admin-page-stat--amber">
                            Subjects With Slots: {{ $stats['subjectsWithSlots'] }}
                        </span>
                    </div>
                </div>
            </section>

            <!-- Alerts -->
            @if (session('success'))
                <div
                    class="study-time-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                    style="--sd: 2;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="study-time-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                    style="--sd: 2;">
                    <div class="font-semibold">{{ $errors->first() }}</div>
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-12">

                <!-- LEFT: Add Time Slot -->
                <section
                    class="study-time-reveal rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4">
                    <h2 class="text-lg font-black text-slate-900">Add Time Slot</h2>
                    <p class="mt-1 text-xs text-slate-500">Create study schedules for classes and subjects.</p>

                    <div class="mt-5 space-y-6">
                        @php
                            $classFormClassId = old('school_class_id', (string) ($classes->first()?->id ?? ''));
                        @endphp

                        <!-- Class Study Time -->
                        <form method="POST" action="{{ route('admin.time-studies.classes.store') }}" class="js-create-form space-y-3">
                            @csrf

                            <h3 class="text-sm font-black text-slate-900">Class Study Time</h3>

                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Class</label>
                                <select name="school_class_id"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    @foreach ($classes as $classOption)
                                    <option value="{{ $classOption->id }}" @if ($classFormClassId === (string) $classOption->id) selected @endif>
                                            {{ $classOption->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <select name="period"
                                    class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    @foreach ($periodOptions as $periodKey => $periodLabel)
                                        <option value="{{ $periodKey }}" @if (old('period', 'morning') === $periodKey) selected @endif>
                                            {{ $periodLabel }}
                                        </option>
                                    @endforeach
                                </select>

                                <input type="time" name="start_time"
                                    class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                    value="{{ old('start_time') }}" required>

                                <input type="time" name="end_time"
                                    class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                    value="{{ old('end_time') }}" required>
                            </div>

                            <button type="submit"
                                class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                Add Class Time
                            </button>
                        </form>

                        @php
                            $subjectFormClassId = old('subject_class_id', (string) ($classes->first()?->id ?? ''));
                        @endphp

                        <!-- Subject Study Time -->
                        <form method="POST" action="{{ route('admin.time-studies.subjects.store') }}"
                            class="js-create-form space-y-3 border-t border-slate-200 pt-5">
                            @csrf

                            <h3 class="text-sm font-black text-slate-900">Subject Study Time</h3>

                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Class</label>
                                <select id="subject_form_class_id" name="subject_class_id"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    @foreach ($classes as $classOption)
                                    <option value="{{ $classOption->id }}" @if ($subjectFormClassId === (string) $classOption->id) selected @endif>
                                            {{ $classOption->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Subject</label>
                                <select id="subject_form_subject_id" name="subject_id" data-selected="{{ old('subject_id', '') }}"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">Select a subject</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Class Time</label>
                                <select id="subject_form_class_time_id" name="class_time_id"
                                    data-selected="{{ old('class_time_id', '') }}"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">Select class time</option>
                                </select>
                            </div>

                            <button type="submit"
                                class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Add Subject Time From Class
                            </button>
                        </form>
                    </div>
                </section>

                <!-- RIGHT: Schedule List -->
                <section
                    class="study-time-reveal study-time-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8">

                    <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">

                        <!-- Header Row -->
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-lg font-black text-slate-900">
                                {{ $tab === 'subject' ? 'Subject List' : 'Class List' }}
                            </h2>

                            <div class="flex items-center gap-2">

                                <!-- Tabs -->
                                <div class="inline-flex rounded-xl border border-slate-200 bg-slate-50 p-1">
                                    <a href="{{ route('admin.time-studies.index', ['tab' => 'class', 'q' => $search, 'period' => $period, 'class_id' => $classId, 'subject_id' => $subjectId]) }}"
                                        class="rounded-lg px-3 py-1.5 text-sm font-semibold {{ $tab === 'class' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                        Class Times
                                    </a>
                                    <a href="{{ route('admin.time-studies.index', ['tab' => 'subject', 'q' => $search, 'period' => $period, 'class_id' => $classId, 'subject_id' => $subjectId]) }}"
                                        class="rounded-lg px-3 py-1.5 text-sm font-semibold {{ $tab === 'subject' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                        Subject Times
                                    </a>
                                </div>

                                <!-- Filter Button -->
                                <button type="button" @click="filterOpen = true"
                                    class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-white">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                                    </svg>
                                    Filters
                                </button>

                            </div>
                        </div>

                        <!-- Overlay -->
                        <div x-show="filterOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[80] bg-slate-900/40"
                            @click="filterOpen = false"></div>

                        <div class="grid gap-4">
                            <!-- Filter Panel -->
                            <aside x-show="filterOpen" x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="translate-x-full"
                                x-transition:enter-end="translate-x-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="translate-x-0"
                                x-transition:leave-end="translate-x-full"
                                class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl">

                                <div class="flex h-full flex-col">
                                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                                        <h3 class="text-3xl font-black text-slate-900">Filters</h3>

                                        <div class="flex items-center gap-4">
                                            <a href="{{ route('admin.time-studies.index', ['tab' => $tab]) }}"
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

                                    <form method="GET" action="{{ route('admin.time-studies.index') }}"
                                        class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                                        <input type="hidden" name="tab" value="{{ $tab }}">

                                        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">

                                            <section class="space-y-2">
                                                <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                                <input id="q" name="q" type="text" value="{{ $search }}"
                                                    placeholder="Search class, subject, period, or time"
                                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            </section>

                                            <section class="space-y-2">
                                                <h4 class="text-xl font-bold text-slate-900">Period</h4>
                                                <select name="period"
                                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    <option value="all" @if ($period === 'all') selected @endif>All Periods</option>
                                                    @foreach ($periodOptions as $periodKey => $periodLabel)
                                                        <option value="{{ $periodKey }}" @if ($period === $periodKey) selected @endif>
                                                            {{ $periodLabel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </section>

                                            <section class="space-y-2">
                                                <h4 class="text-xl font-bold text-slate-900">Class</h4>
                                                <select id="filter_class_id" name="class_id"
                                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    <option value="all" @if ($classId === 'all') selected @endif>All Classes</option>
                                                    @foreach ($classes as $classOption)
                                                        <option value="{{ $classOption->id }}" @if ($classId === (string) $classOption->id) selected @endif>
                                                            {{ $classOption->display_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </section>

                                            <section class="space-y-2">
                                                <h4 class="text-xl font-bold text-slate-900">Subject</h4>
                                                <select id="filter_subject_id" name="subject_id" data-selected="{{ $subjectId }}"
                                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    <option value="all" @if ($subjectId === 'all') selected @endif>All Subjects</option>
                                                    @foreach ($subjects as $subjectOption)
                                                        <option value="{{ $subjectOption->id }}" @if ($subjectId === (string) $subjectOption->id) selected @endif>
                                                            {{ $subjectOption->name }} ({{ $subjectOption->code }})
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

                            <div class="min-w-0">
                                @if ($tab === 'class')
                        <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200">
                                <!-- TABLES -->
                            <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200">
                                <div class="max-h-[560px] overflow-auto">
                                    <table class="w-full min-w-[1120px] text-left text-sm">
                                        <thead
                                            class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                            <tr>
                                                <th class="px-3 py-3 font-semibold">Class</th>
                                                <th class="px-3 py-3 font-semibold">Study Time</th>
                                                <th class="px-3 py-3 font-semibold">Order</th>
                                                <th class="px-3 py-3 font-semibold text-right">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-slate-100 bg-white">
                                            @forelse ($classTimes as $slot)
                                                <tr class="align-top hover:bg-slate-50/80" x-data="{ openClassEdit: false }">
                                                    <td class="px-3 py-3 align-top">
                                                        <div class="whitespace-nowrap font-semibold text-slate-800">
                                                            {{ $slot->schoolClass?->display_name ?? '-' }}
                                                        </div>
                                                        <div class="text-xs text-slate-400">
                                                            {{ \Illuminate\Support\Str::limit($slot->schoolClass?->description ?: 'No description', 60) }}
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-3 align-top text-slate-700">
                                                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px]">
                                                            <span class="font-bold uppercase tracking-wide text-indigo-700">
                                                                {{ $periodOptions[$slot->period] ?? ucfirst($slot->period) }}
                                                            </span>
                                                            <span class="text-indigo-300">|</span>
                                                            <span class="whitespace-nowrap font-semibold text-slate-700">
                                                                {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} ->
                                                                {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-3 align-top text-slate-700">{{ $slot->sort_order }}</td>

                                                    <td class="px-3 py-3 align-top">
                                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                                            <button type="button" @click="openClassEdit = true"
                                                                class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                                Edit
                                                            </button>

                                                            <form method="POST"
                                                                action="{{ route('admin.time-studies.classes.destroy', $slot) }}"
                                                                class="js-delete-time-form"
                                                                data-label="{{ $slot->schoolClass?->display_name ?? 'class slot' }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>

                                                        <!-- Edit Class Modal -->
                                                        <div x-show="openClassEdit" x-cloak
                                                            class="fixed inset-0 z-[70] grid place-items-center p-4" aria-modal="true"
                                                            role="dialog">
                                                            <div class="absolute inset-0 bg-slate-900/50"
                                                                @click="openClassEdit = false"></div>

                                                            <div class="relative z-10 w-full max-w-xl max-h-[85vh] overflow-y-auto rounded-3xl bg-white p-5 shadow-2xl">
                                                                <div class="mb-4 flex items-center justify-between">
                                                                    <h3 class="text-lg font-black text-slate-900">Edit Class Time</h3>
                                                                    <button type="button" @click="openClassEdit = false"
                                                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                                                            <path
                                                                                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>

                                                                <form method="POST"
                                                                    action="{{ route('admin.time-studies.classes.update', $slot) }}"
                                                                    class="js-edit-form space-y-4"
                                                                    data-subject="{{ $slot->schoolClass?->display_name ?? 'Class Time' }}">
                                                                    @csrf
                                                                    @method('PUT')

                                                                    <div>
                                                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Class</label>
                                                                        <select name="school_class_id"
                                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                            @foreach ($classes as $classOption)
                                                                                <option value="{{ $classOption->id }}" @if ((int) $slot->school_class_id === (int) $classOption->id) selected @endif>
                                                                                    {{ $classOption->display_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="grid gap-4 sm:grid-cols-3">
                                                                        <div>
                                                                            <label class="mb-1 block text-xs font-semibold text-slate-600">Period</label>
                                                                            <select name="period"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                @foreach ($periodOptions as $periodKey => $periodLabel)
                                                                                    <option value="{{ $periodKey }}" @if (strtolower((string) $slot->period) === $periodKey) selected @endif>
                                                                                        {{ $periodLabel }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div>
                                                                            <label class="mb-1 block text-xs font-semibold text-slate-600">Start</label>
                                                                            <input type="time" name="start_time"
                                                                                value="{{ \Illuminate\Support\Str::substr((string) $slot->start_time, 0, 5) }}"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                                required>
                                                                        </div>

                                                                        <div>
                                                                            <label class="mb-1 block text-xs font-semibold text-slate-600">End</label>
                                                                            <input type="time" name="end_time"
                                                                                value="{{ \Illuminate\Support\Str::substr((string) $slot->end_time, 0, 5) }}"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                                required>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex justify-end gap-2 pt-2">
                                                                        <button type="button" @click="openClassEdit = false"
                                                                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                                                            Cancel
                                                                        </button>
                                                                        <button type="submit"
                                                                            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
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
                                                    <td colspan="4" class="px-3 py-10 text-center text-sm text-slate-500">
                                                        No class study times found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <div class="mt-5">{{ $classTimes->links() }}</div>
                    @else
                        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200">
                            <div class="max-h-[560px] overflow-auto">
                                <table class="w-full min-w-[1120px] text-left text-sm">
                                    <thead
                                        class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-3 py-3 font-semibold">Subject</th>
                                            <th class="px-3 py-3 font-semibold">Code</th>
                                            <th class="px-3 py-3 font-semibold">Study Time</th>
                                            <th class="px-3 py-3 font-semibold">Class</th>
                                            <th class="px-3 py-3 font-semibold text-right">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @forelse ($subjectTimes as $slot)
                                            @php
                                                $slotClassId = (string) ($slot->subject?->school_class_id ?? '');
                                                $slotClassTimeId = '';
                                                $slotPeriod = strtolower((string) $slot->period);
                                                $slotStart = \Illuminate\Support\Str::substr((string) $slot->start_time, 0, 5);
                                                $slotEnd = \Illuminate\Support\Str::substr((string) $slot->end_time, 0, 5);

                                                if ($slotClassId !== '' && isset($classTimeOptionsByClass[$slotClassId])) {
                                                    foreach ($classTimeOptionsByClass[$slotClassId] as $classTimeOption) {
                                                        if (
                                                            ($classTimeOption['period'] ?? '') === $slotPeriod &&
                                                            ($classTimeOption['start_time'] ?? '') === $slotStart &&
                                                            ($classTimeOption['end_time'] ?? '') === $slotEnd
                                                        ) {
                                                            $slotClassTimeId = (string) ($classTimeOption['id'] ?? '');
                                                            break;
                                                        }
                                                    }
                                                }
                                            @endphp

                                            <tr class="align-top hover:bg-slate-50/80" x-data="{ openSubjectEdit: false }">
                                                <td class="px-3 py-3 align-top">
                                                    <div class="whitespace-nowrap font-semibold text-slate-800">
                                                        {{ $slot->subject?->name ?? '-' }}
                                                    </div>
                                                    <div class="text-xs text-slate-400">
                                                        {{ \Illuminate\Support\Str::limit($slot->subject?->description ?: 'No description', 60) }}
                                                    </div>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-3 align-top text-slate-600">
                                                    {{ $slot->subject?->code ?? '-' }}
                                                </td>
                                                <td class="px-3 py-3 align-top text-slate-700">
                                                    <div class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px]">
                                                        <span class="font-bold uppercase tracking-wide text-indigo-700">
                                                            {{ $periodOptions[$slot->period] ?? ucfirst($slot->period) }}
                                                        </span>
                                                        <span class="text-indigo-300">|</span>
                                                        <span class="whitespace-nowrap font-semibold text-slate-700">
                                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} ->
                                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-3 align-top text-slate-700">
                                                    {{ $slot->subject?->schoolClass?->display_name ?? 'Unassigned' }}
                                                </td>

                                                <td class="px-3 py-3 align-top">
                                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                                        <button type="button" @click="openSubjectEdit = true"
                                                            class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                            Edit
                                                        </button>

                                                        <form method="POST"
                                                            action="{{ route('admin.time-studies.subjects.destroy', $slot) }}"
                                                            class="js-delete-time-form"
                                                            data-label="{{ $slot->subject?->name ?? 'subject slot' }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>

                                                    <div x-show="openSubjectEdit" x-cloak
                                                        class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                        aria-modal="true" role="dialog">
                                                        <div class="absolute inset-0 bg-slate-900/50"
                                                            @click="openSubjectEdit = false"></div>

                                                        <div class="relative z-10 w-full max-w-xl max-h-[85vh] overflow-y-auto rounded-3xl bg-white p-5 shadow-2xl">
                                                            <div class="mb-4 flex items-center justify-between">
                                                                <h3 class="text-lg font-black text-slate-900">Edit Subject Time</h3>
                                                                <button type="button" @click="openSubjectEdit = false"
                                                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                                                        <path
                                                                            d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <form method="POST"
                                                                action="{{ route('admin.time-studies.subjects.update', $slot) }}"
                                                                class="js-edit-form js-subject-edit-form space-y-4"
                                                                data-subject="{{ $slot->subject?->name ?? 'Subject Time' }}"
                                                                data-subject-study-time-id="{{ $slot->id }}">
                                                                @csrf
                                                                @method('PUT')

                                                                <div>
                                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Class</label>
                                                                    <select name="subject_class_id"
                                                                        class="js-subject-edit-class w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                        @foreach ($classes as $classOption)
                                                                            <option value="{{ $classOption->id }}" @if ($slotClassId === (string) $classOption->id) selected @endif>
                                                                                {{ $classOption->display_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Subject</label>
                                                                    <select name="subject_id"
                                                                        data-selected="{{ $slot->subject_id }}"
                                                                        class="js-subject-edit-subject w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                        <option value="">Select a subject</option>
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Class Time</label>
                                                                    <select name="class_time_id"
                                                                        data-selected="{{ $slotClassTimeId }}"
                                                                        class="js-subject-edit-class-time w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                        <option value="">Select class time</option>
                                                                    </select>
                                                                </div>

                                                                <div class="flex justify-end gap-2 pt-2">
                                                                    <button type="button" @click="openSubjectEdit = false"
                                                                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                                                        Cancel
                                                                    </button>
                                                                    <button type="submit"
                                                                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
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
                                                <td colspan="5" class="px-3 py-10 text-center text-sm text-slate-500">
                                                    No subject study times found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-5">{{ $subjectTimes->links() }}</div>
                    @endif
                            </div>
                        </div>

                </div>
            </section>

            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hasSwal = typeof Swal !== 'undefined';

            const subjectOptionsByClass = @json($subjectOptionsByClass);
            const subjectOptionsAll = @json($subjectOptionsAll);
            const classTimeOptionsByClass = @json($classTimeOptionsByClass);
            const occupiedClassSlotsByClass = @json($occupiedClassSlotsByClass);

            const getSubjectsForClass = (classId) => {
                if (!classId || classId === 'all') return subjectOptionsAll;
                return subjectOptionsByClass[classId] || [];
            };

            const renderSubjectOptions = (subjectSelect, classId, selectedValue, includeAllOption = false) => {
                if (!subjectSelect) return;

                const subjects = getSubjectsForClass(classId);
                const options = [];

                if (includeAllOption) {
                    const selectedAll = String(selectedValue) === 'all' ? ' selected' : '';
                    options.push(`<option value="all"${selectedAll}>All Subjects</option>`);
                }

                if (subjects.length === 0) {
                    options.push('<option value="">No subjects in selected class</option>');
                    subjectSelect.innerHTML = options.join('');
                    return;
                }

                for (const subject of subjects) {
                    const selected = String(selectedValue) === String(subject.id) ? ' selected' : '';
                    options.push(`<option value="${subject.id}"${selected}>${subject.label}</option>`);
                }

                subjectSelect.innerHTML = options.join('');
            };

            const renderClassTimeOptions = (timeSelect, classId, selectedValue, currentSubjectStudyTimeId = null) => {
                if (!timeSelect) return;

                const slots = classTimeOptionsByClass[classId] || [];
                const occupiedSlots = occupiedClassSlotsByClass[classId] || {};
                const options = [];

                const availableSlots = slots.filter((slot) => {
                    const ownerId = occupiedSlots[slot.key];
                    if (!ownerId) return true;

                    if (currentSubjectStudyTimeId === null || currentSubjectStudyTimeId === '') return false;
                    return String(ownerId) === String(currentSubjectStudyTimeId);
                });

                if (availableSlots.length === 0) {
                    options.push('<option value="">No class times in selected class</option>');
                    timeSelect.innerHTML = options.join('');
                    return;
                }

                for (const slot of availableSlots) {
                    const selected = String(selectedValue) === String(slot.id) ? ' selected' : '';
                    options.push(`<option value="${slot.id}"${selected}>${slot.label}</option>`);
                }

                timeSelect.innerHTML = options.join('');
            };

            // Add Subject Time form
            const subjectFormClass = document.getElementById('subject_form_class_id');
            const subjectFormSubject = document.getElementById('subject_form_subject_id');
            const subjectFormClassTime = document.getElementById('subject_form_class_time_id');

            if (subjectFormClass && subjectFormSubject && subjectFormClassTime) {
                const selectedSubject = subjectFormSubject.dataset.selected || '';
                const selectedClassTime = subjectFormClassTime.dataset.selected || '';

                renderSubjectOptions(subjectFormSubject, subjectFormClass.value, selectedSubject, false);
                renderClassTimeOptions(subjectFormClassTime, subjectFormClass.value, selectedClassTime, null);

                subjectFormClass.addEventListener('change', () => {
                    renderSubjectOptions(subjectFormSubject, subjectFormClass.value, '', false);
                    renderClassTimeOptions(subjectFormClassTime, subjectFormClass.value, '', null);
                });
            }

            // Edit Subject forms
            document.querySelectorAll('.js-subject-edit-form').forEach((form) => {
                const classSelect = form.querySelector('.js-subject-edit-class');
                const subjectSelect = form.querySelector('.js-subject-edit-subject');
                const classTimeSelect = form.querySelector('.js-subject-edit-class-time');
                const currentSubjectStudyTimeId = form.dataset.subjectStudyTimeId || '';

                if (!classSelect || !subjectSelect || !classTimeSelect) return;

                const selectedSubject = subjectSelect.dataset.selected || '';
                const selectedClassTime = classTimeSelect.dataset.selected || '';

                renderSubjectOptions(subjectSelect, classSelect.value, selectedSubject, false);
                renderClassTimeOptions(classTimeSelect, classSelect.value, selectedClassTime, currentSubjectStudyTimeId);

                classSelect.addEventListener('change', () => {
                    renderSubjectOptions(subjectSelect, classSelect.value, '', false);
                    renderClassTimeOptions(classTimeSelect, classSelect.value, '', currentSubjectStudyTimeId);
                });
            });

            // Filter subjects by class
            const filterClass = document.getElementById('filter_class_id');
            const filterSubject = document.getElementById('filter_subject_id');

            if (filterClass && filterSubject) {
                const selectedFilterSubject = filterSubject.dataset.selected || 'all';
                renderSubjectOptions(filterSubject, filterClass.value, selectedFilterSubject, true);

                filterClass.addEventListener('change', () => {
                    renderSubjectOptions(filterSubject, filterClass.value, 'all', true);
                });
            }

            // SweetAlert confirmations
            if (!hasSwal) return;

            const confirmSubmit = (selector, buildConfig) => {
                document.querySelectorAll(selector).forEach((form) => {
                    form.addEventListener('submit', function(event) {
                        if (form.dataset.confirmed === '1') return;

                        event.preventDefault();

                        Swal.fire(buildConfig(form)).then((result) => {
                            if (result.isConfirmed) {
                                form.dataset.confirmed = '1';
                                form.submit();
                            }
                        });
                    });
                });
            };

            confirmSubmit('.js-create-form', () => ({
                title: 'Create time slot?',
                text: 'A new time slot will be saved.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, create',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-edit-form', (form) => ({
                title: 'Save changes?',
                text: `Update details for ${form.dataset.subject || 'this schedule'}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, save',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-delete-time-form', (form) => ({
                title: 'Delete time slot?',
                text: `Remove schedule for ${form.dataset.label || 'this item'}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626'
            }));

            // Show validation and flash messages using SweetAlert like teacher view
            const validationErrors = @json($errors->all());
            if (validationErrors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: validationErrors[0]
                });
                return;
            }

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: @json(session('error'))
                });
            @elseif (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: @json(session('warning'))
                });
            @elseif (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: @json(session('success')),
                    timer: 2200,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endsection
