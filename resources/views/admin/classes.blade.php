@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="class-stage space-y-6">
        <section
            class="class-reveal overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-900 to-blue-800 p-6 text-white shadow-lg"
            style="--sd: 1;">
            <div class="flex flex-wrap items-center justify-between gap-5">
                <div>
                    <h1 class="text-3xl font-black tracking-tight">Class Management</h1>
                    <p class="mt-1 text-sm text-indigo-100">Create, organize, and manage classroom groups.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                    <span class="rounded-full bg-white/15 px-3 py-1.5">Total: {{ $stats['total'] }}</span>
                    <span class="rounded-full bg-emerald-400/20 px-3 py-1.5 text-emerald-100">Active:
                        {{ $stats['active'] }}</span>
                    <span class="rounded-full bg-rose-400/20 px-3 py-1.5 text-rose-100">Inactive:
                        {{ $stats['inactive'] }}</span>
                    <span class="rounded-full bg-sky-400/20 px-3 py-1.5 text-sky-100">With Schedule:
                        {{ $stats['withSchedule'] ?? 0 }}</span>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="class-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="class-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="class-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                <div class="font-semibold">Please check the form fields and try again.</div>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            <section
                class="class-reveal class-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-5"
                style="--sd: 3;">
                <h2 class="text-lg font-black text-slate-900">Create Class</h2>
                <p class="mt-1 text-xs text-slate-500">Define a classroom for students and subject assignment.</p>

                <form method="POST" action="{{ route('admin.classes.store') }}" class="js-create-form mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold text-slate-600">Class Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Grade 10">
                        @error('name')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="section" class="mb-1 block text-xs font-semibold text-slate-600">Section</label>
                            <input id="section" name="section" type="text" value="{{ old('section') }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                placeholder="A">
                            @error('section')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="room" class="mb-1 block text-xs font-semibold text-slate-600">Room</label>
                            <input id="room" name="room" type="text" value="{{ old('room') }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                placeholder="B-201">
                            @error('room')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @php
                        $createStudySlots = old('study_slots');
                        if (!is_array($createStudySlots) || $createStudySlots === []) {
                            $createStudySlots = [['period' => 'morning', 'start_time' => '', 'end_time' => '']];
                        }
                        $createSlotError =
                            $errors->first('study_slots') ?:
                            $errors->first('study_slots.*.period') ?:
                            $errors->first('study_slots.*.start_time') ?:
                            $errors->first('study_slots.*.end_time');
                    @endphp

                    <div>
                        <div class="mb-1 flex items-center justify-between gap-2">
                            <label class="block text-xs font-semibold text-slate-600">Study Times (Morning / Night /
                                More)</label>
                            <button type="button"
                                class="js-add-study-slot rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700 hover:bg-indigo-100"
                                data-target="create-study-slots">
                                + Add Time
                            </button>
                        </div>
                        <div id="create-study-slots" class="js-study-slots space-y-2" data-name-prefix="study_slots"
                            data-next-index="{{ count($createStudySlots) }}">
                            @foreach ($createStudySlots as $index => $slot)
                                @php
                                    $slotPeriod = strtolower(trim((string) ($slot['period'] ?? 'morning')));
                                    $slotStart = trim((string) ($slot['start_time'] ?? ''));
                                    $slotEnd = trim((string) ($slot['end_time'] ?? ''));
                                @endphp
                                <div
                                    class="js-slot-row grid gap-2 rounded-xl border border-slate-200 bg-slate-50/80 p-2 sm:grid-cols-[1fr_1fr_1fr_auto]">
                                    <select name="study_slots[{{ $index }}][period]"
                                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        @foreach ($periodOptions as $periodKey => $periodLabel)
                                            <option value="{{ $periodKey }}"
                                                {{ $slotPeriod === $periodKey ? 'selected' : '' }}>
                                                {{ $periodLabel }}</option>
                                        @endforeach
                                    </select>
                                    <input type="time" name="study_slots[{{ $index }}][start_time]"
                                        value="{{ $slotStart }}"
                                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <input type="time" name="study_slots[{{ $index }}][end_time]"
                                        value="{{ $slotEnd }}"
                                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <button type="button"
                                        class="js-remove-study-slot rounded-xl border border-rose-200 bg-rose-50 px-2.5 py-2 text-[11px] font-semibold text-rose-700 hover:bg-rose-100">
                                        Remove
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Example: Morning 08:00 -> 11:00, Night 06:00 ->
                            08:00 PM.</p>
                        @if ($createSlotError)
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $createSlotError }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="capacity" class="mb-1 block text-xs font-semibold text-slate-600">Capacity</label>
                        <input id="capacity" name="capacity" type="number" min="1" max="500"
                            value="{{ old('capacity') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="40">
                        @error('capacity')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="mb-1 block text-xs font-semibold text-slate-600">Description
                            (Optional)</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Notes about this class...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5">
                        <span class="text-sm font-semibold text-slate-700">Initial Status</span>
                        <span class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300"
                                {{ old('is_active', '1') ? 'checked' : '' }}>
                            Active
                        </span>
                    </label>

                    <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        Create Class
                    </button>
                </form>
            </section>

            <section
                class="class-reveal class-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-7"
                style="--sd: 4;">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-black text-slate-900">Class List</h2>

                    <form method="GET" action="{{ route('admin.classes.index') }}"
                        class="grid w-full max-w-5xl gap-2 rounded-2xl border border-slate-200 bg-slate-50/80 p-2 sm:grid-cols-2 xl:grid-cols-[1fr_auto_auto_auto_auto_auto]">
                        <input id="q" name="q" type="text" value="{{ $search }}"
                            placeholder="Search class, room, morning/night/custom time, or schedule"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <select name="status"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <select name="period"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Periods</option>
                            @foreach ($periodOptions as $periodKey => $periodLabel)
                                <option value="{{ $periodKey }}" {{ $period === $periodKey ? 'selected' : '' }}>
                                    {{ $periodLabel }}
                                </option>
                            @endforeach
                        </select>
                        <select name="schedule"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="all" {{ $schedule === 'all' ? 'selected' : '' }}>All Schedules</option>
                            <option value="with_schedule" {{ $schedule === 'with_schedule' ? 'selected' : '' }}>With
                                Schedule</option>
                            <option value="without_schedule" {{ $schedule === 'without_schedule' ? 'selected' : '' }}>
                                Without Schedule</option>
                        </select>
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white">Filter</button>
                        <a href="{{ route('admin.classes.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                            Reset
                        </a>
                    </form>
                </div>

                <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200">
                    <div class="max-h-[560px] overflow-auto">
                        <table class="w-full min-w-[1050px] text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-3 font-semibold">Class</th>
                                <th class="px-3 py-3 font-semibold">Room</th>
                                <th class="px-3 py-3 font-semibold">Study Time</th>
                                <th class="px-3 py-3 font-semibold">Capacity</th>
                                <th class="px-3 py-3 font-semibold">Subjects</th>
                                <th class="px-3 py-3 font-semibold">Students</th>
                                <th class="px-3 py-3 font-semibold">Status</th>
                                <th class="px-3 py-3 font-semibold">Created</th>
                                <th class="px-3 py-3 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($classes as $schoolClass)
                                <tr class="align-top hover:bg-slate-50/80" x-data="{ open: false }">
                                    <td class="px-3 py-3">
                                        <div class="font-semibold text-slate-800">{{ $schoolClass->display_name }}</div>
                                        <div class="mt-0.5 text-xs leading-relaxed text-slate-400">
                                            {{ \Illuminate\Support\Str::limit($schoolClass->description ?: 'No description', 60) }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">{{ $schoolClass->room ?: '-' }}</td>
                                    <td class="px-3 py-3 text-slate-600">
                                        @if ($schoolClass->studySchedules->isNotEmpty())
                                            <div class="flex max-w-sm flex-wrap gap-1.5">
                                                @foreach ($schoolClass->studySchedules as $scheduleRow)
                                                    <div
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px]">
                                                        <span class="font-bold uppercase tracking-wide text-indigo-700">
                                                            {{ $periodOptions[$scheduleRow->period] ?? ucfirst($scheduleRow->period) }}
                                                        </span>
                                                        <span class="text-indigo-300">|</span>
                                                        <span class="whitespace-nowrap font-semibold text-slate-700">
                                                            {{ \Carbon\Carbon::parse($scheduleRow->start_time)->format('h:i A') }}
                                                            ->
                                                            {{ \Carbon\Carbon::parse($scheduleRow->end_time)->format('h:i A') }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="mt-1 text-[11px] text-slate-400">{{ $schoolClass->study_schedules_count }}
                                                slots</div>
                                        @else
                                            @php
                                                $classStartTime = $schoolClass->study_start_time ?: $schoolClass->study_time;
                                                $classEndTime = $schoolClass->study_end_time;
                                            @endphp
                                            @if ($classStartTime && $classEndTime)
                                                <span
                                                    class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700">
                                                    {{ \Carbon\Carbon::parse($classStartTime)->format('h:i A') }} ->
                                                    {{ \Carbon\Carbon::parse($classEndTime)->format('h:i A') }}
                                                </span>
                                            @elseif ($classStartTime)
                                                {{ \Carbon\Carbon::parse($classStartTime)->format('h:i A') }}
                                            @else
                                                -
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">
                                        {{ $schoolClass->capacity ? number_format($schoolClass->capacity) : '-' }}
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">{{ $schoolClass->subjects_count }}</td>
                                    <td class="px-3 py-3 text-slate-600">{{ $schoolClass->students_count ?? 0 }}</td>
                                    <td class="px-3 py-3">
                                        @if ($schoolClass->is_active)
                                            <span
                                                class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                <span class="status-dot h-2 w-2 rounded-full bg-emerald-500"></span>Active
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                                <span class="h-2 w-2 rounded-full bg-rose-500"></span>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-slate-500">{{ $schoolClass->created_at->format('M d, Y') }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <button @click="open = true" type="button"
                                                class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('admin.classes.status', $schoolClass) }}"
                                                class="js-status-form" data-class="{{ $schoolClass->display_name }}"
                                                data-action="{{ $schoolClass->is_active ? 'set inactive' : 'set active' }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="whitespace-nowrap rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $schoolClass->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                    {{ $schoolClass->is_active ? 'Set Inactive' : 'Set Active' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.classes.destroy', $schoolClass) }}"
                                                class="js-delete-form" data-class="{{ $schoolClass->display_name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>

                                        <div x-show="open" x-cloak class="fixed inset-0 z-[70] grid place-items-center p-4"
                                            aria-modal="true" role="dialog">
                                            <div class="absolute inset-0 bg-slate-900/50" @click="open = false"></div>

                                            <div class="relative z-10 w-full max-w-xl rounded-3xl bg-white p-5 shadow-2xl">
                                                <div class="mb-4 flex items-center justify-between">
                                                    <h3 class="text-lg font-black text-slate-900">Edit Class</h3>
                                                    <button type="button" @click="open = false"
                                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                                            <path
                                                                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <form method="POST"
                                                    action="{{ route('admin.classes.update', $schoolClass) }}"
                                                    class="js-edit-form space-y-4" data-class="{{ $schoolClass->display_name }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <div>
                                                        <label for="edit_name_{{ $schoolClass->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Class
                                                            Name</label>
                                                        <input id="edit_name_{{ $schoolClass->id }}" name="name"
                                                            type="text" value="{{ $schoolClass->name }}"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    </div>

                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label for="edit_section_{{ $schoolClass->id }}"
                                                                class="mb-1 block text-xs font-semibold text-slate-600">Section</label>
                                                            <input id="edit_section_{{ $schoolClass->id }}" name="section"
                                                                type="text" value="{{ $schoolClass->section }}"
                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                        </div>
                                                        <div>
                                                            <label for="edit_room_{{ $schoolClass->id }}"
                                                                class="mb-1 block text-xs font-semibold text-slate-600">Room</label>
                                                            <input id="edit_room_{{ $schoolClass->id }}" name="room"
                                                                type="text" value="{{ $schoolClass->room }}"
                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                        </div>
                                                    </div>

                                                    @php
                                                        $editStudySlots = $schoolClass->studySchedules
                                                            ->map(function ($slot) {
                                                                return [
                                                                    'period' => $slot->period,
                                                                    'start_time' => $slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('H:i') : '',
                                                                    'end_time' => $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('H:i') : '',
                                                                ];
                                                            })
                                                            ->values()
                                                            ->all();

                                                        if ($editStudySlots === []) {
                                                            $fallbackStart = $schoolClass->study_start_time ?: $schoolClass->study_time;
                                                            $fallbackEnd = $schoolClass->study_end_time;
                                                            if ($fallbackStart && $fallbackEnd) {
                                                                $editStudySlots[] = [
                                                                    'period' => 'custom',
                                                                    'start_time' => \Carbon\Carbon::parse($fallbackStart)->format('H:i'),
                                                                    'end_time' => \Carbon\Carbon::parse($fallbackEnd)->format('H:i'),
                                                                ];
                                                            } else {
                                                                $editStudySlots[] = [
                                                                    'period' => 'morning',
                                                                    'start_time' => '',
                                                                    'end_time' => '',
                                                                ];
                                                            }
                                                        }
                                                    @endphp

                                                    <div>
                                                        <div class="mb-1 flex items-center justify-between gap-2">
                                                            <label class="block text-xs font-semibold text-slate-600">Study
                                                                Times</label>
                                                            <button type="button"
                                                                class="js-add-study-slot rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700 hover:bg-indigo-100"
                                                                data-target="edit-study-slots-{{ $schoolClass->id }}">
                                                                + Add Time
                                                            </button>
                                                        </div>
                                                        <div id="edit-study-slots-{{ $schoolClass->id }}"
                                                            class="js-study-slots space-y-2" data-name-prefix="study_slots"
                                                            data-next-index="{{ count($editStudySlots) }}">
                                                            @foreach ($editStudySlots as $index => $slot)
                                                                @php
                                                                    $slotPeriod = strtolower(trim((string) ($slot['period'] ?? 'morning')));
                                                                    $slotStart = trim((string) ($slot['start_time'] ?? ''));
                                                                    $slotEnd = trim((string) ($slot['end_time'] ?? ''));
                                                                @endphp
                                                                <div
                                                                    class="js-slot-row grid gap-2 rounded-xl border border-slate-200 bg-slate-50/80 p-2 sm:grid-cols-[1fr_1fr_1fr_auto]">
                                                                    <select
                                                                        name="study_slots[{{ $index }}][period]"
                                                                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                        @foreach ($periodOptions as $periodKey => $periodLabel)
                                                                            <option value="{{ $periodKey }}"
                                                                                {{ $slotPeriod === $periodKey ? 'selected' : '' }}>
                                                                                {{ $periodLabel }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <input type="time"
                                                                        name="study_slots[{{ $index }}][start_time]"
                                                                        value="{{ $slotStart }}"
                                                                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    <input type="time"
                                                                        name="study_slots[{{ $index }}][end_time]"
                                                                        value="{{ $slotEnd }}"
                                                                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    <button type="button"
                                                                        class="js-remove-study-slot rounded-xl border border-rose-200 bg-rose-50 px-2.5 py-2 text-[11px] font-semibold text-rose-700 hover:bg-rose-100">
                                                                        Remove
                                                                    </button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="edit_capacity_{{ $schoolClass->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Capacity</label>
                                                        <input id="edit_capacity_{{ $schoolClass->id }}" name="capacity"
                                                            type="number" min="1" max="500"
                                                            value="{{ $schoolClass->capacity }}"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    </div>

                                                    <div>
                                                        <label for="edit_description_{{ $schoolClass->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Description</label>
                                                        <textarea id="edit_description_{{ $schoolClass->id }}" name="description" rows="3"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ $schoolClass->description }}</textarea>
                                                    </div>

                                                    <label
                                                        class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5">
                                                        <span class="text-sm font-semibold text-slate-700">Status</span>
                                                        <span
                                                            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                                                            <input type="checkbox" name="is_active" value="1"
                                                                class="h-4 w-4 rounded border-slate-300"
                                                                {{ $schoolClass->is_active ? 'checked' : '' }}>
                                                            Active
                                                        </span>
                                                    </label>

                                                    <div class="flex justify-end gap-2 pt-2">
                                                        <button type="button" @click="open = false"
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
                                    <td colspan="9" class="px-3 py-10 text-center text-sm text-slate-500">
                                        No classes found.
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
            </section>
        </div>
    </div>

    <style>
        .class-stage .max-h-\[560px\]::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        .class-stage .max-h-\[560px\]::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 999px;
            border: 2px solid #f8fafc;
        }

        .class-stage .max-h-\[560px\]::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 999px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hasSwal = typeof Swal !== 'undefined';

            const periodOptions = @json($periodOptions);

            const periodOptionHtml = (selectedValue) => Object.entries(periodOptions).map(([value, label]) => {
                const selected = value === selectedValue ? 'selected' : '';
                return `<option value="${value}" ${selected}>${label}</option>`;
            }).join('');

            const buildSlotRowHtml = (namePrefix, index, defaults = {}) => {
                const period = defaults.period || 'morning';
                const startTime = defaults.start_time || '';
                const endTime = defaults.end_time || '';

                return `
                    <div class="js-slot-row grid gap-2 rounded-xl border border-slate-200 bg-slate-50/80 p-2 sm:grid-cols-[1fr_1fr_1fr_auto]">
                        <select name="${namePrefix}[${index}][period]"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            ${periodOptionHtml(period)}
                        </select>
                        <input type="time" name="${namePrefix}[${index}][start_time]" value="${startTime}"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <input type="time" name="${namePrefix}[${index}][end_time]" value="${endTime}"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <button type="button"
                            class="js-remove-study-slot rounded-xl border border-rose-200 bg-rose-50 px-2.5 py-2 text-[11px] font-semibold text-rose-700 hover:bg-rose-100">
                            Remove
                        </button>
                    </div>
                `;
            };

            const syncRemoveButtons = (container) => {
                const rows = container.querySelectorAll('.js-slot-row');
                rows.forEach((row) => {
                    const button = row.querySelector('.js-remove-study-slot');
                    if (!button) {
                        return;
                    }

                    if (rows.length <= 1) {
                        button.classList.add('opacity-50', 'cursor-not-allowed');
                        button.setAttribute('disabled', 'disabled');
                    } else {
                        button.classList.remove('opacity-50', 'cursor-not-allowed');
                        button.removeAttribute('disabled');
                    }
                });
            };

            document.querySelectorAll('.js-study-slots').forEach((container) => {
                syncRemoveButtons(container);

                container.addEventListener('click', function(event) {
                    const removeButton = event.target.closest('.js-remove-study-slot');
                    if (!removeButton) {
                        return;
                    }

                    const rows = container.querySelectorAll('.js-slot-row');
                    if (rows.length <= 1) {
                        const row = removeButton.closest('.js-slot-row');
                        row?.querySelectorAll('input[type="time"]').forEach((input) => {
                            input.value = '';
                        });
                        const select = row?.querySelector('select');
                        if (select) {
                            select.value = 'morning';
                        }
                        return;
                    }

                    removeButton.closest('.js-slot-row')?.remove();
                    syncRemoveButtons(container);
                });
            });

            document.querySelectorAll('.js-add-study-slot').forEach((button) => {
                button.addEventListener('click', function() {
                    const targetId = button.dataset.target || '';
                    const container = targetId ? document.getElementById(targetId) : null;
                    if (!container) {
                        return;
                    }

                    const namePrefix = container.dataset.namePrefix || 'study_slots';
                    const index = Number(container.dataset.nextIndex || 0);
                    container.insertAdjacentHTML('beforeend', buildSlotRowHtml(namePrefix, index));
                    container.dataset.nextIndex = String(index + 1);
                    syncRemoveButtons(container);
                });
            });

            const confirmSubmit = (selector, buildConfig) => {
                if (!hasSwal) {
                    return;
                }

                document.querySelectorAll(selector).forEach((form) => {
                    form.addEventListener('submit', function(event) {
                        if (form.dataset.confirmed === '1') {
                            return;
                        }

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
                title: 'Create class?',
                text: 'A new class will be saved.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, create',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-edit-form', (form) => ({
                title: 'Save changes?',
                text: `Update details for ${form.dataset.class || 'this class'}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, save',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-status-form', (form) => ({
                title: 'Change class status?',
                text: `This will ${form.dataset.action || 'change status'} for ${form.dataset.class || 'this class'}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, continue',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#f59e0b'
            }));

            confirmSubmit('.js-delete-form', (form) => ({
                title: 'Delete class?',
                text: `Delete ${form.dataset.class || 'this class'} permanently. This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626'
            }));

            const validationErrors = @json($errors->all());
            if (hasSwal && validationErrors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: validationErrors[0]
                });
                return;
            }

            if (hasSwal) {
                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: @json(session('error'))
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
            }
        });
    </script>
@endsection
