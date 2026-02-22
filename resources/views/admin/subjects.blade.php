@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="subject-stage space-y-6">
        <section
            class="subject-reveal overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-900 to-blue-800 p-6 text-white shadow-lg"
            style="--sd: 1;">
            <div class="flex flex-wrap items-center justify-between gap-5">
                <div>
                    <h1 class="text-3xl font-black tracking-tight">Subject Management</h1>
                    <p class="mt-1 text-sm text-indigo-100">Create and assign subjects to classes.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                    <span class="rounded-full bg-white/15 px-3 py-1.5">Total: {{ $stats['total'] }}</span>
                    <span class="rounded-full bg-emerald-400/20 px-3 py-1.5 text-emerald-100">Active:
                        {{ $stats['active'] }}</span>
                    <span class="rounded-full bg-sky-400/20 px-3 py-1.5 text-sky-100">Assigned:
                        {{ $stats['assigned'] }}</span>
                    <span class="rounded-full bg-amber-300/20 px-3 py-1.5 text-amber-100">With Teacher:
                        {{ $stats['withTeacher'] }}</span>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="subject-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="subject-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->subjectCreate->any())
            <div class="subject-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                <div class="font-semibold">Please check the form fields and try again.</div>
            </div>
        @endif

        @if ($errors->subjectUpdate->any())
            <div class="subject-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                <div class="font-semibold">Unable to update subject. Please open Edit and review the fields.</div>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            <section
                class="subject-reveal subject-float rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-5"
                style="--sd: 3;">
                <h2 class="text-lg font-black text-slate-900">Create Subject</h2>
                <p class="mt-1 text-xs text-slate-500">Create a new subject and optionally assign it to a class.</p>

                <form method="POST" action="{{ route('admin.subjects.store') }}" class="js-create-form mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold text-slate-600">Subject Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Mathematics">
                        @error('name', 'subjectCreate')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="code" class="mb-1 block text-xs font-semibold text-slate-600">Code</label>
                        <input id="code" name="code" type="text" value="{{ old('code') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="MATH101">
                        @error('code', 'subjectCreate')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @php
                        $createStudySlots = old('study_slots');
                        if (!is_array($createStudySlots) || $createStudySlots === []) {
                            $createStudySlots = [['period' => 'morning', 'start_time' => '', 'end_time' => '']];
                        }
                        $createSlotError =
                            $errors->subjectCreate->first('study_slots') ?:
                            $errors->subjectCreate->first('study_slots.*.period') ?:
                            $errors->subjectCreate->first('study_slots.*.start_time') ?:
                            $errors->subjectCreate->first('study_slots.*.end_time');
                    @endphp

                    <div>
                        <div class="mb-1 flex items-center justify-between gap-2">
                            <label class="block text-xs font-semibold text-slate-600">Study Times (Morning / Night /
                                More)</label>
                            <button type="button"
                                class="js-add-study-slot rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700 hover:bg-indigo-100"
                                data-target="create-subject-study-slots">
                                + Add Time
                            </button>
                        </div>
                        <div id="create-subject-study-slots" class="js-study-slots space-y-2" data-name-prefix="study_slots"
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
                        <input id="study_start_time" name="study_start_time" type="hidden"
                            value="{{ old('study_start_time') }}">
                        <input id="study_end_time" name="study_end_time" type="hidden" value="{{ old('study_end_time') }}">
                        <p class="mt-1 text-[11px] text-slate-500">Auto-syncs from selected class schedules when class is
                            selected.</p>
                        @if ($createSlotError)
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $createSlotError }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="school_class_id" class="mb-1 block text-xs font-semibold text-slate-600">Assign to
                            Class</label>
                        <select id="school_class_id" name="school_class_id"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="">Unassigned</option>
                            @foreach ($classes as $classOption)
                                @php
                                    $classSlots = $classOption->studySchedules
                                        ->map(function ($slot) {
                                            return [
                                                'period' => strtolower((string) $slot->period),
                                                'start_time' => $slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('H:i') : '',
                                                'end_time' => $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('H:i') : '',
                                            ];
                                        })
                                        ->values()
                                        ->all();

                                    if ($classSlots === []) {
                                        $fallbackStart = $classOption->study_start_time ?: $classOption->study_time;
                                        $fallbackEnd = $classOption->study_end_time;
                                        if ($fallbackStart && $fallbackEnd) {
                                            $classSlots[] = [
                                                'period' => 'custom',
                                                'start_time' => \Carbon\Carbon::parse($fallbackStart)->format('H:i'),
                                                'end_time' => \Carbon\Carbon::parse($fallbackEnd)->format('H:i'),
                                            ];
                                        }
                                    }
                                @endphp
                                <option value="{{ $classOption->id }}"
                                    data-study-slots='@json($classSlots)'
                                    data-study-start="{{ ($classOption->study_start_time ?: $classOption->study_time) ? \Illuminate\Support\Str::substr((string) ($classOption->study_start_time ?: $classOption->study_time), 0, 5) : '' }}"
                                    data-study-end="{{ $classOption->study_end_time ? \Illuminate\Support\Str::substr((string) $classOption->study_end_time, 0, 5) : '' }}"
                                    {{ (string) old('school_class_id') === (string) $classOption->id ? 'selected' : '' }}>
                                    {{ $classOption->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('school_class_id', 'subjectCreate')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="teacher_id" class="mb-1 block text-xs font-semibold text-slate-600">Teacher</label>
                        <select id="teacher_id" name="teacher_id"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="">Unassigned</option>
                            @foreach ($teachers as $teacherOption)
                                <option value="{{ $teacherOption->id }}"
                                    {{ (string) old('teacher_id') === (string) $teacherOption->id ? 'selected' : '' }}>
                                    {{ $teacherOption->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id', 'subjectCreate')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="mb-1 block text-xs font-semibold text-slate-600">Description
                            (Optional)</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Notes about the subject...">{{ old('description') }}</textarea>
                        @error('description', 'subjectCreate')
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
                        Create Subject
                    </button>
                </form>
            </section>

            <section
                class="subject-reveal subject-float rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-7"
                style="--sd: 4;">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-black text-slate-900">Subject List</h2>

                    <form method="GET" action="{{ route('admin.subjects.index') }}"
                        class="grid w-full max-w-3xl gap-2 sm:grid-cols-[1fr_auto_auto_auto]">
                        <input id="q" name="q" type="text" value="{{ $search }}"
                            placeholder="Search by subject, code, start/end time, or description"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <select name="class_id"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="all" {{ $classId === 'all' ? 'selected' : '' }}>All Classes</option>
                            @foreach ($classes as $classOption)
                                <option value="{{ $classOption->id }}"
                                    {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                    {{ $classOption->display_name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white">Filter</button>
                    </form>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-3 font-semibold">Subject</th>
                                <th class="px-3 py-3 font-semibold">Code</th>
                                <th class="px-3 py-3 font-semibold">Study Time</th>
                                <th class="px-3 py-3 font-semibold">Class</th>
                                <th class="px-3 py-3 font-semibold">Teacher</th>
                                <th class="px-3 py-3 font-semibold">Students Learning</th>
                                <th class="px-3 py-3 font-semibold">Status</th>
                                <th class="px-3 py-3 font-semibold">Created</th>
                                <th class="px-3 py-3 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($subjects as $subject)
                                <tr class="hover:bg-slate-50" x-data="{ open: false }">
                                    <td class="px-3 py-3">
                                        <div class="font-semibold text-slate-800">{{ $subject->name }}</div>
                                        <div class="text-xs text-slate-400">
                                            {{ \Illuminate\Support\Str::limit($subject->description ?: 'No description', 60) }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">{{ $subject->code }}</td>
                                    <td class="px-3 py-3 text-slate-600">
                                        @if ($subject->studySchedules->isNotEmpty())
                                            <div class="flex max-w-sm flex-wrap gap-1.5">
                                                @foreach ($subject->studySchedules as $slot)
                                                    <div
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px]">
                                                        <span class="font-bold uppercase tracking-wide text-indigo-700">
                                                            {{ $periodOptions[$slot->period] ?? ucfirst($slot->period) }}
                                                        </span>
                                                        <span class="text-indigo-300">|</span>
                                                        <span class="whitespace-nowrap font-semibold text-slate-700">
                                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} ->
                                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            @php
                                                $subjectStartTime = $subject->study_start_time ?: $subject->study_time;
                                                $subjectEndTime = $subject->study_end_time;
                                            @endphp
                                            @if ($subjectStartTime && $subjectEndTime)
                                                {{ \Carbon\Carbon::parse($subjectStartTime)->format('h:i A') }} ->
                                                {{ \Carbon\Carbon::parse($subjectEndTime)->format('h:i A') }}
                                            @elseif ($subjectStartTime)
                                                {{ \Carbon\Carbon::parse($subjectStartTime)->format('h:i A') }}
                                            @else
                                                -
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">{{ $subject->schoolClass?->display_name ?: 'Unassigned' }}
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">{{ $subject->teacher?->name ?: 'Unassigned' }}</td>
                                    <td class="px-3 py-3 text-slate-600">{{ $subject->students_count ?? 0 }}</td>
                                    <td class="px-3 py-3">
                                        @if ($subject->is_active)
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
                                    <td class="px-3 py-3 text-slate-500">{{ $subject->created_at->format('M d, Y') }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <button @click="open = true" type="button"
                                                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('admin.subjects.status', $subject) }}"
                                                class="js-status-form" data-subject="{{ $subject->name }}"
                                                data-action="{{ $subject->is_active ? 'set inactive' : 'set active' }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $subject->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                    {{ $subject->is_active ? 'Set Inactive' : 'Set Active' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                                                class="js-delete-form" data-subject="{{ $subject->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>

                                        <div x-show="open" x-cloak class="fixed inset-0 z-[70] grid place-items-center p-4"
                                            aria-modal="true" role="dialog">
                                            <div class="absolute inset-0 bg-slate-900/50" @click="open = false"></div>

                                            <div class="relative z-10 w-full max-w-xl rounded-3xl bg-white p-5 shadow-2xl">
                                                <div class="mb-4 flex items-center justify-between">
                                                    <h3 class="text-lg font-black text-slate-900">Edit Subject</h3>
                                                    <button type="button" @click="open = false"
                                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                                            <path
                                                                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <form method="POST" action="{{ route('admin.subjects.update', $subject) }}"
                                                    class="js-edit-form space-y-4" data-subject="{{ $subject->name }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <div>
                                                        <label for="edit_name_{{ $subject->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Subject
                                                            Name</label>
                                                        <input id="edit_name_{{ $subject->id }}" name="name" type="text"
                                                            value="{{ $subject->name }}"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    </div>

                                                    <div>
                                                        <label for="edit_code_{{ $subject->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Code</label>
                                                        <input id="edit_code_{{ $subject->id }}" name="code" type="text"
                                                            value="{{ $subject->code }}"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    </div>

                                                    @php
                                                        $editStudySlots = $subject->studySchedules
                                                            ->map(function ($slot) {
                                                                return [
                                                                    'period' => strtolower((string) $slot->period),
                                                                    'start_time' => $slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('H:i') : '',
                                                                    'end_time' => $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('H:i') : '',
                                                                ];
                                                            })
                                                            ->values()
                                                            ->all();

                                                        if ($editStudySlots === []) {
                                                            $fallbackStart = $subject->study_start_time ?: $subject->study_time;
                                                            $fallbackEnd = $subject->study_end_time;
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
                                                                data-target="edit-subject-study-slots-{{ $subject->id }}">
                                                                + Add Time
                                                            </button>
                                                        </div>
                                                        <div id="edit-subject-study-slots-{{ $subject->id }}"
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
                                                                    <select name="study_slots[{{ $index }}][period]"
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
                                                        <input id="edit_study_start_time_{{ $subject->id }}" name="study_start_time"
                                                            type="hidden"
                                                            value="{{ ($subject->study_start_time ?: $subject->study_time) ? \Carbon\Carbon::parse($subject->study_start_time ?: $subject->study_time)->format('H:i') : '' }}">
                                                        <input id="edit_study_end_time_{{ $subject->id }}" name="study_end_time"
                                                            type="hidden"
                                                            value="{{ $subject->study_end_time ? \Carbon\Carbon::parse($subject->study_end_time)->format('H:i') : '' }}">
                                                        <p class="mt-1 text-[11px] text-slate-500">Auto-syncs from selected class
                                                            schedules.</p>
                                                    </div>

                                                    <div>
                                                        <label for="edit_school_class_id_{{ $subject->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Assign to
                                                            Class</label>
                                                        <select id="edit_school_class_id_{{ $subject->id }}"
                                                            name="school_class_id"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                            <option value="">Unassigned</option>
                                                            @foreach ($classes as $classOption)
                                                                @php
                                                                    $classSlots = $classOption->studySchedules
                                                                        ->map(function ($slot) {
                                                                            return [
                                                                                'period' => strtolower((string) $slot->period),
                                                                                'start_time' => $slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('H:i') : '',
                                                                                'end_time' => $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('H:i') : '',
                                                                            ];
                                                                        })
                                                                        ->values()
                                                                        ->all();

                                                                    if ($classSlots === []) {
                                                                        $fallbackStart = $classOption->study_start_time ?: $classOption->study_time;
                                                                        $fallbackEnd = $classOption->study_end_time;
                                                                        if ($fallbackStart && $fallbackEnd) {
                                                                            $classSlots[] = [
                                                                                'period' => 'custom',
                                                                                'start_time' => \Carbon\Carbon::parse($fallbackStart)->format('H:i'),
                                                                                'end_time' => \Carbon\Carbon::parse($fallbackEnd)->format('H:i'),
                                                                            ];
                                                                        }
                                                                    }
                                                                @endphp
                                                                <option value="{{ $classOption->id }}"
                                                                    data-study-slots='@json($classSlots)'
                                                                    data-study-start="{{ ($classOption->study_start_time ?: $classOption->study_time) ? \Illuminate\Support\Str::substr((string) ($classOption->study_start_time ?: $classOption->study_time), 0, 5) : '' }}"
                                                                    data-study-end="{{ $classOption->study_end_time ? \Illuminate\Support\Str::substr((string) $classOption->study_end_time, 0, 5) : '' }}"
                                                                    {{ (string) $subject->school_class_id === (string) $classOption->id ? 'selected' : '' }}>
                                                                    {{ $classOption->display_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label for="edit_teacher_id_{{ $subject->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Teacher</label>
                                                        <select id="edit_teacher_id_{{ $subject->id }}"
                                                            name="teacher_id"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                            <option value="">Unassigned</option>
                                                            @foreach ($teachers as $teacherOption)
                                                                <option value="{{ $teacherOption->id }}"
                                                                    {{ (string) $subject->teacher_id === (string) $teacherOption->id ? 'selected' : '' }}>
                                                                    {{ $teacherOption->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label for="edit_description_{{ $subject->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Description</label>
                                                        <textarea id="edit_description_{{ $subject->id }}" name="description" rows="3"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ $subject->description }}</textarea>
                                                    </div>

                                                    <label
                                                        class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5">
                                                        <span class="text-sm font-semibold text-slate-700">Status</span>
                                                        <span
                                                            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                                                            <input type="checkbox" name="is_active" value="1"
                                                                class="h-4 w-4 rounded border-slate-300"
                                                                {{ $subject->is_active ? 'checked' : '' }}>
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
                                        No subjects found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $subjects->links() }}
                </div>
            </section>
        </div>
    </div>

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

            const readSlotsFromContainer = (container) => {
                return Array.from(container.querySelectorAll('.js-slot-row')).map((row) => {
                    const period = row.querySelector('select')?.value || 'morning';
                    const startTime = row.querySelector('input[name$="[start_time]"]')?.value || '';
                    const endTime = row.querySelector('input[name$="[end_time]"]')?.value || '';
                    return {
                        period: period.toLowerCase(),
                        start_time: startTime,
                        end_time: endTime
                    };
                });
            };

            const syncHiddenFromContainer = (container, startInput, endInput) => {
                if (!startInput || !endInput) {
                    return;
                }

                const firstSlot = readSlotsFromContainer(container).find((slot) => slot.start_time && slot.end_time);
                startInput.value = firstSlot ? firstSlot.start_time : '';
                endInput.value = firstSlot ? firstSlot.end_time : '';
            };

            const renderSlots = (container, slots) => {
                if (!container) {
                    return;
                }

                const namePrefix = container.dataset.namePrefix || 'study_slots';
                const normalizedSlots = Array.isArray(slots) && slots.length > 0 ? slots : [{
                    period: 'morning',
                    start_time: '',
                    end_time: ''
                }];

                const html = normalizedSlots.map((slot, index) => buildSlotRowHtml(namePrefix, index, slot)).join('');
                container.innerHTML = html;
                container.dataset.nextIndex = String(normalizedSlots.length);
                syncRemoveButtons(container);
            };

            const getClassSlots = (classSelect) => {
                if (!classSelect) {
                    return [];
                }

                const selectedOption = classSelect.options[classSelect.selectedIndex];
                if (!selectedOption) {
                    return [];
                }

                const raw = selectedOption.dataset.studySlots || '';
                if (raw !== '') {
                    try {
                        const parsed = JSON.parse(raw);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            return parsed.map((slot) => ({
                                period: String(slot.period || 'custom').toLowerCase(),
                                start_time: String(slot.start_time || ''),
                                end_time: String(slot.end_time || ''),
                            }));
                        }
                    } catch (error) {
                        // Ignore malformed data and fall back to single start/end.
                    }
                }

                const startTime = selectedOption.dataset.studyStart || '';
                const endTime = selectedOption.dataset.studyEnd || '';
                if (startTime && endTime) {
                    return [{
                        period: 'custom',
                        start_time: startTime,
                        end_time: endTime
                    }];
                }

                return [];
            };

            const wireStudySlots = (classSelect, slotsContainer, startInput, endInput) => {
                if (!slotsContainer) {
                    return;
                }

                const applyFromClass = () => {
                    const selectedClassId = classSelect ? (classSelect.value || '') : '';
                    if (selectedClassId !== '') {
                        renderSlots(slotsContainer, getClassSlots(classSelect));
                    }
                    syncHiddenFromContainer(slotsContainer, startInput, endInput);
                };

                applyFromClass();

                if (classSelect) {
                    classSelect.addEventListener('change', applyFromClass);
                }

                slotsContainer.addEventListener('input', () => {
                    syncHiddenFromContainer(slotsContainer, startInput, endInput);
                });

                slotsContainer.addEventListener('change', () => {
                    syncHiddenFromContainer(slotsContainer, startInput, endInput);
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

            wireStudySlots(
                document.getElementById('school_class_id'),
                document.getElementById('create-subject-study-slots'),
                document.getElementById('study_start_time'),
                document.getElementById('study_end_time')
            );

            document.querySelectorAll('[id^="edit_school_class_id_"]').forEach((classSelect) => {
                const suffix = classSelect.id.replace('edit_school_class_id_', '');
                wireStudySlots(
                    classSelect,
                    document.getElementById(`edit-subject-study-slots-${suffix}`),
                    document.getElementById(`edit_study_start_time_${suffix}`),
                    document.getElementById(`edit_study_end_time_${suffix}`)
                );
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
                title: 'Create subject?',
                text: 'A new subject will be saved.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, create',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-edit-form', (form) => ({
                title: 'Save changes?',
                text: `Update details for ${form.dataset.subject || 'this subject'}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, save',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-status-form', (form) => ({
                title: 'Change subject status?',
                text: `This will ${form.dataset.action || 'change status'} for ${form.dataset.subject || 'this subject'}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, continue',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#f59e0b'
            }));

            confirmSubmit('.js-delete-form', (form) => ({
                title: 'Delete subject?',
                text: `Delete ${form.dataset.subject || 'this subject'} permanently. This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626'
            }));

            const createValidationErrors = @json($errors->subjectCreate->all());
            if (hasSwal && createValidationErrors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: createValidationErrors[0]
                });
                return;
            }

            const updateValidationErrors = @json($errors->subjectUpdate->all());
            if (hasSwal && updateValidationErrors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Error',
                    text: updateValidationErrors[0]
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
