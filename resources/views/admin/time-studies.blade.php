@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="study-time-stage space-y-6">

        <!-- Header -->
        <x-admin.page-header reveal-class="study-time-reveal" delay="1" icon="time" title="Time Studies"
            subtitle="Manage class and subject study schedules in one place.">
            <x-slot:stats>
                <span class="admin-page-stat">Class Slots: {{ $stats['classSlots'] }}</span>
                <span class="admin-page-stat admin-page-stat--sky">Subject Slots: {{ $stats['subjectSlots'] }}</span>
                <span class="admin-page-stat admin-page-stat--emerald">
                    Classes With Slots: {{ $stats['classesWithSlots'] }}
                </span>
                <span class="admin-page-stat admin-page-stat--amber">
                    Subjects With Slots: {{ $stats['subjectsWithSlots'] }}
                </span>
            </x-slot:stats>
        </x-admin.page-header>

        <!-- Alerts (same style as Student page) -->
        @if (session('success'))
            <div class="study-time-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="study-time-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="study-time-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="study-time-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                <div class="font-semibold">Please check the form fields and try again.</div>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">

            <!-- LEFT: Create Times (same card style as Student create card) -->
            <section
                class="study-time-reveal study-time-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--sd: 3;">

                <h2 class="text-lg font-black text-slate-900">Add Time Slot</h2>
                <p class="mt-1 text-xs text-slate-500">Create study schedules for classes and subjects.</p>

                @php
                    $classFormDayOfWeek = old('day_of_week', 'all');
                    $classSlotRows = old('class_slots');
                    if (!is_array($classSlotRows) || count($classSlotRows) === 0) {
                        $classSlotRows = [
                            [
                                'day_of_week' => $classFormDayOfWeek,
                                'period' => old('period', 'custom'),
                                'start_time' => old('start_time', ''),
                                'end_time' => old('end_time', ''),
                            ],
                        ];
                    }
                    $subjectFormClassId = old('subject_class_id', (string) ($classes->first()?->id ?? ''));
                    $subjectFormSlotRows = old('subject_slots');
                    if (!is_array($subjectFormSlotRows) || count($subjectFormSlotRows) === 0) {
                        $subjectFormSlotRows = [
                            [
                                'subject_id' => old('subject_id', ''),
                                'teacher_id' => old('teacher_id', ''),
                                'class_time_id' => old('class_time_id', ''),
                            ],
                        ];
                    }
                @endphp

                <!-- Class Study Time -->
                <form method="POST" action="{{ route('admin.time-studies.classes.store') }}" id="class_time_create_form"
                    class="js-create-form mt-5 space-y-4">
                    @csrf

                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-black text-slate-900">Class Study Time</h3>
                        <span class="text-[11px] font-semibold text-slate-400">For all classes</span>
                    </div>

                    <div>
                        <p class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                            This creates the selected date/time for every class.
                        </p>
                    </div>

                    @error('class_slots')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror

                    <div id="class_slot_rows" class="space-y-3" data-next-index="{{ count($classSlotRows) }}">
                        @foreach ($classSlotRows as $slotIndex => $slotRow)
                            @php
                                $rowDay = strtolower((string) ($slotRow['day_of_week'] ?? 'all'));
                                $rowPeriod = strtolower((string) ($slotRow['period'] ?? 'morning'));
                                $rowStart = (string) ($slotRow['start_time'] ?? '');
                                $rowEnd = (string) ($slotRow['end_time'] ?? '');
                            @endphp

                            <div class="js-class-slot-row rounded-2xl border border-slate-200/80 bg-slate-50/60 p-3">
                                <div class="grid gap-3 sm:grid-cols-12">
                                    <div class="sm:col-span-3">
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Day</label>
                                        <select name="class_slots[{{ $slotIndex }}][day_of_week]"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            @foreach ($dayOptions as $dayKey => $dayLabel)
                                                <option value="{{ $dayKey }}"
                                                    {{ $rowDay === $dayKey ? 'selected' : '' }}>
                                                    {{ $dayLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_slots.' . $slotIndex . '.day_of_week')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Period</label>
                                        <select name="class_slots[{{ $slotIndex }}][period]"
                                            class="js-class-period-select w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            @foreach ($periodOptions as $periodKey => $periodLabel)
                                                <option value="{{ $periodKey }}"
                                                    {{ $rowPeriod === $periodKey ? 'selected' : '' }}>
                                                    {{ $periodLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_slots.' . $slotIndex . '.period')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Start</label>
                                        <input type="text" name="class_slots[{{ $slotIndex }}][start_time]"
                                            value="{{ $rowStart }}" required placeholder="07:30 AM or 19:30"
                                            class="js-class-time-input js-class-start-input w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <p class="mt-1 text-[11px] text-slate-500">AM/PM or 24H</p>
                                        @error('class_slots.' . $slotIndex . '.start_time')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">End</label>
                                        <input type="text" name="class_slots[{{ $slotIndex }}][end_time]"
                                            value="{{ $rowEnd }}" required placeholder="09:00 AM or 21:00"
                                            class="js-class-time-input js-class-end-input w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <p class="mt-1 text-[11px] text-slate-500">AM/PM or 24H</p>
                                        @error('class_slots.' . $slotIndex . '.end_time')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-2 flex justify-end">
                                    <button type="button"
                                        class="js-remove-class-slot rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button id="add_class_slot_btn" type="button"
                        class="w-full rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
                        + Add More Date Time
                    </button>

                    <template id="class_slot_row_template">
                        <div class="js-class-slot-row rounded-2xl border border-slate-200/80 bg-slate-50/60 p-3">
                            <div class="grid gap-3 sm:grid-cols-12">
                                <div class="sm:col-span-3">
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Day</label>
                                    <select name="class_slots[__INDEX__][day_of_week]"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        @foreach ($dayOptions as $dayKey => $dayLabel)
                                            <option value="{{ $dayKey }}" {{ $dayKey === 'all' ? 'selected' : '' }}>
                                                {{ $dayLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="sm:col-span-3">
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Period</label>
                                    <select name="class_slots[__INDEX__][period]"
                                        class="js-class-period-select w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        @foreach ($periodOptions as $periodKey => $periodLabel)
                                            <option value="{{ $periodKey }}"
                                                {{ $periodKey === 'morning' ? 'selected' : '' }}>
                                                {{ $periodLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="sm:col-span-3">
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Start</label>
                                    <input type="text" name="class_slots[__INDEX__][start_time]" required
                                        placeholder="07:30 AM or 19:30"
                                        class="js-class-time-input js-class-start-input w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <p class="mt-1 text-[11px] text-slate-500">AM/PM or 24H</p>
                                </div>

                                <div class="sm:col-span-3">
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">End</label>
                                    <input type="text" name="class_slots[__INDEX__][end_time]" required
                                        placeholder="09:00 AM or 21:00"
                                        class="js-class-time-input js-class-end-input w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <p class="mt-1 text-[11px] text-slate-500">AM/PM or 24H</p>
                                </div>
                            </div>

                            <div class="mt-2 flex justify-end">
                                <button type="button"
                                    class="js-remove-class-slot rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </template>

                    <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        Add Class Time
                    </button>
                </form>

                <!-- Subject Study Time -->
                <form method="POST" action="{{ route('admin.time-studies.subjects.store') }}"
                    class="js-create-form mt-6 space-y-4 border-t border-slate-200 pt-6">
                    @csrf

                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-black text-slate-900">Subject Study Time</h3>
                        <span class="text-[11px] font-semibold text-slate-400">Uses class time</span>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Class</label>
                        <select id="subject_form_class_id" name="subject_class_id"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @foreach ($classes as $classOption)
                                <option value="{{ $classOption->id }}"
                                    {{ $subjectFormClassId === (string) $classOption->id ? 'selected' : '' }}>
                                    {{ $classOption->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_class_id')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @error('subject_slots')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror

                    <div id="subject_slot_rows" class="space-y-3" data-next-index="{{ count($subjectFormSlotRows) }}">
                        @foreach ($subjectFormSlotRows as $slotIndex => $subjectSlotRow)
                            @php
                                $rowSubjectId = (string) ($subjectSlotRow['subject_id'] ?? '');
                                $rowTeacherId = (string) ($subjectSlotRow['teacher_id'] ?? '');
                                $rowClassTimeId = (string) ($subjectSlotRow['class_time_id'] ?? '');
                            @endphp
                            <div class="js-subject-slot-row rounded-2xl border border-slate-200/80 bg-slate-50/60 p-3">
                                <div class="grid gap-3">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Subject</label>
                                        <select name="subject_slots[{{ $slotIndex }}][subject_id]"
                                            data-selected="{{ $rowSubjectId }}"
                                            class="js-subject-form-subject w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="">Select a subject</option>
                                        </select>
                                        @error('subject_slots.' . $slotIndex . '.subject_id')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Class Time</label>
                                        <select name="subject_slots[{{ $slotIndex }}][class_time_id]"
                                            data-selected="{{ $rowClassTimeId }}"
                                            class="js-subject-form-class-time w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="">Select class time</option>
                                        </select>
                                        @error('subject_slots.' . $slotIndex . '.class_time_id')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Teacher</label>
                                        <select name="subject_slots[{{ $slotIndex }}][teacher_id]"
                                            data-selected="{{ $rowTeacherId }}"
                                            class="js-subject-form-teacher w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="">Unassigned</option>
                                            @foreach ($teachers as $teacherOption)
                                                <option value="{{ $teacherOption->id }}"
                                                    {{ $rowTeacherId === (string) $teacherOption->id ? 'selected' : '' }}>
                                                    {{ $teacherOption->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subject_slots.' . $slotIndex . '.teacher_id')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-2 flex justify-end">
                                    <button type="button"
                                        class="js-remove-subject-slot rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button id="add_subject_slot_btn" type="button"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        + Add More Subject Time
                    </button>

                    <template id="subject_slot_row_template">
                        <div class="js-subject-slot-row rounded-2xl border border-slate-200/80 bg-slate-50/60 p-3">
                            <div class="grid gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Subject</label>
                                    <select name="subject_slots[__INDEX__][subject_id]"
                                        class="js-subject-form-subject w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="">Select a subject</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Class Time</label>
                                    <select name="subject_slots[__INDEX__][class_time_id]"
                                        class="js-subject-form-class-time w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="">Select class time</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Teacher</label>
                                    <select name="subject_slots[__INDEX__][teacher_id]"
                                        class="js-subject-form-teacher w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="">Unassigned</option>
                                        @foreach ($teachers as $teacherOption)
                                            <option value="{{ $teacherOption->id }}">
                                                {{ $teacherOption->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-2 flex justify-end">
                                <button type="button"
                                    class="js-remove-subject-slot rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </template>

                    <button type="submit"
                        class="w-full rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Add Subject Times From Class
                    </button>
                </form>
            </section>

            <!-- RIGHT: List + Filters (same style as Student list card) -->
            <section
                class="study-time-reveal study-time-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--sd: 4;">

                <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-black text-slate-900">
                            {{ $tab === 'class' ? 'Class List' : ($tab === 'subject' ? 'Subject List' : 'Teacher Study List') }}
                        </h2>

                        <div class="flex w-full flex-wrap items-center justify-end gap-2 sm:w-auto">
                            <div class="max-w-full overflow-x-auto rounded-xl border border-slate-200 bg-slate-50 p-1">
                                <div class="inline-flex min-w-max">
                                    <a href="{{ route('admin.time-studies.index', ['tab' => 'class', 'q' => $search, 'period' => $period, 'day' => $day, 'class_id' => $classId, 'subject_id' => $subjectId, 'teacher_id' => $teacherId, 'per_page' => $perPage]) }}"
                                        class="rounded-lg px-3 py-1.5 text-sm font-semibold {{ $tab === 'class' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                        Class Times
                                    </a>
                                    <a href="{{ route('admin.time-studies.index', ['tab' => 'subject', 'q' => $search, 'period' => $period, 'day' => $day, 'class_id' => $classId, 'subject_id' => $subjectId, 'teacher_id' => $teacherId, 'per_page' => $perPage]) }}"
                                        class="rounded-lg px-3 py-1.5 text-sm font-semibold {{ $tab === 'subject' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                        Subject Times
                                    </a>
                                    <a href="{{ route('admin.time-studies.index', ['tab' => 'teacher', 'q' => $search, 'period' => $period, 'day' => $day, 'class_id' => $classId, 'subject_id' => $subjectId, 'teacher_id' => $teacherId, 'per_page' => $perPage]) }}"
                                        class="rounded-lg px-3 py-1.5 text-sm font-semibold {{ $tab === 'teacher' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                        Teacher Times
                                    </a>
                                </div>
                            </div>

                            <button type="button" @click="filterOpen = true"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
                        <!-- Filter Panel (same as Student style) -->
                        <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="translate-x-full"
                            class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl">
                            <div class="flex h-full flex-col">
                                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                                    <h3 class="text-3xl font-black text-slate-900">Filters</h3>
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('admin.time-studies.index', ['tab' => $tab, 'per_page' => $perPage]) }}"
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
                                            <input id="q" name="q" type="text"
                                                value="{{ $search }}"
                                                placeholder="Search class, teacher, subject, period, or time"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        </section>

                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Period</h4>
                                            <select name="period"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All
                                                    Periods
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
                                            <h4 class="text-xl font-bold text-slate-900">Day</h4>
                                            <select name="day"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                @foreach ($dayOptions as $dayKey => $dayLabel)
                                                    <option value="{{ $dayKey }}"
                                                        {{ $day === $dayKey ? 'selected' : '' }}>
                                                        {{ $dayLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>

                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Class</h4>
                                            <select id="filter_class_id" name="class_id"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $classId === 'all' ? 'selected' : '' }}>All
                                                    Classes
                                                </option>
                                                @foreach ($classes as $classOption)
                                                    <option value="{{ $classOption->id }}"
                                                        {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                                        {{ $classOption->display_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>

                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Subject</h4>
                                            <select id="filter_subject_id" name="subject_id"
                                                data-selected="{{ $subjectId }}"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $subjectId === 'all' ? 'selected' : '' }}>All
                                                    Subjects
                                                </option>
                                                @foreach ($subjects as $subjectOption)
                                                    <option value="{{ $subjectOption->id }}"
                                                        {{ $subjectId === (string) $subjectOption->id ? 'selected' : '' }}>
                                                        {{ $subjectOption->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>

                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Teacher</h4>
                                            <select name="teacher_id"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $teacherId === 'all' ? 'selected' : '' }}>All
                                                    Teachers
                                                </option>
                                                @foreach ($teachers as $teacherOption)
                                                    <option value="{{ $teacherOption->id }}"
                                                        {{ $teacherId === (string) $teacherOption->id ? 'selected' : '' }}>
                                                        {{ $teacherOption->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>

                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Rows</h4>
                                            <select name="per_page"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                @foreach ($perPageOptions as $option)
                                                    <option value="{{ $option }}"
                                                        {{ (int) $perPage === (int) $option ? 'selected' : '' }}>
                                                        {{ $option }} items
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>
                                    </div>

                                    <div
                                        class="sticky bottom-0 border-t border-slate-200 bg-white px-5 py-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
                                        <button type="submit"
                                            class="inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-4 py-3 text-lg font-bold text-white transition hover:bg-slate-800">
                                            Apply Filters
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </aside>

                        <!-- TABLES (same sticky header / container as Student list) -->
                        <div class="min-w-0">
                            <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200">
                                <div class="max-h-[700px] overflow-auto">

                                    @if ($tab === 'class')
                                        <table class="w-full min-w-[1280px] text-left text-sm">
                                            <thead
                                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                                <tr>
                                                    <th class="px-3 py-3 font-semibold">Class</th>
                                                    <th class="px-3 py-3 font-semibold">Day</th>
                                                    <th class="px-3 py-3 font-semibold">Period</th>
                                                    <th class="px-3 py-3 font-semibold">Start</th>
                                                    <th class="px-3 py-3 font-semibold">End</th>
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
                                                            @php
                                                                $slotDay = strtolower(
                                                                    (string) ($slot->day_of_week ?? 'all'),
                                                                );
                                                            @endphp
                                                            <span
                                                                class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                                {{ $dayOptions[$slotDay] ?? ucfirst($slotDay) }}
                                                            </span>
                                                        </td>

                                                        <td class="px-3 py-3 align-top text-slate-700">
                                                            <span
                                                                class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-700">
                                                                {{ $periodOptions[$slot->period] ?? ucfirst($slot->period) }}
                                                            </span>
                                                        </td>

                                                        <td class="whitespace-nowrap px-3 py-3 align-top text-slate-700">
                                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                                        </td>

                                                        <td class="whitespace-nowrap px-3 py-3 align-top text-slate-700">
                                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                        </td>

                                                        <td class="px-3 py-3 align-top text-slate-700">
                                                            {{ $slot->sort_order }}
                                                        </td>

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

                                                            <!-- Modal (same as Student edit modal style) -->
                                                            <div x-show="openClassEdit" x-cloak
                                                                class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                                aria-modal="true" role="dialog">
                                                                <div class="absolute inset-0 bg-slate-900/50"
                                                                    @click="openClassEdit = false"></div>

                                                                <div
                                                                    class="relative z-10 w-full max-w-xl rounded-3xl bg-white p-5 shadow-2xl">
                                                                    <div class="mb-4 flex items-center justify-between">
                                                                        <h3 class="text-lg font-black text-slate-900">Edit
                                                                            Class
                                                                            Time</h3>
                                                                        <button type="button"
                                                                            @click="openClassEdit = false"
                                                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                                            <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                                fill="currentColor">
                                                                                <path
                                                                                    d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>

                                                                    <form method="POST"
                                                                        action="{{ route('admin.time-studies.classes.update', $slot) }}"
                                                                        class="js-edit-form js-class-edit-form space-y-4"
                                                                        data-subject="{{ $slot->schoolClass?->display_name ?? 'Class Time' }}">
                                                                        @csrf
                                                                        @method('PUT')

                                                                        <div>
                                                                            <label
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">Class</label>
                                                                            <select name="school_class_id"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                @foreach ($classes as $classOption)
                                                                                    <option value="{{ $classOption->id }}"
                                                                                        {{ (int) $slot->school_class_id === (int) $classOption->id ? 'selected' : '' }}>
                                                                                        {{ $classOption->display_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="grid gap-4 sm:grid-cols-4">
                                                                            <div>
                                                                                <label
                                                                                    class="mb-1 block text-xs font-semibold text-slate-600">Day</label>
                                                                                <select name="day_of_week"
                                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                    @foreach ($dayOptions as $dayKey => $dayLabel)
                                                                                        <option
                                                                                            value="{{ $dayKey }}"
                                                                                            {{ strtolower((string) ($slot->day_of_week ?? 'all')) === $dayKey ? 'selected' : '' }}>
                                                                                            {{ $dayLabel }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div>
                                                                                <label
                                                                                    class="mb-1 block text-xs font-semibold text-slate-600">Period</label>
                                                                                <select name="period"
                                                                                    class="js-edit-class-period w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                    @foreach ($periodOptions as $periodKey => $periodLabel)
                                                                                        <option
                                                                                            value="{{ $periodKey }}"
                                                                                            {{ strtolower((string) $slot->period) === $periodKey ? 'selected' : '' }}>
                                                                                            {{ $periodLabel }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div>
                                                                                <label
                                                                                    class="mb-1 block text-xs font-semibold text-slate-600">Start</label>
                                                                                <input type="time" name="start_time"
                                                                                    value="{{ \Illuminate\Support\Str::substr((string) $slot->start_time, 0, 5) }}"
                                                                                    required
                                                                                    class="js-edit-class-start w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                            </div>

                                                                            <div>
                                                                                <label
                                                                                    class="mb-1 block text-xs font-semibold text-slate-600">End</label>
                                                                                <input type="time" name="end_time"
                                                                                    value="{{ \Illuminate\Support\Str::substr((string) $slot->end_time, 0, 5) }}"
                                                                                    required
                                                                                    class="js-edit-class-end w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex justify-end gap-2 pt-2">
                                                                            <button type="button"
                                                                                @click="openClassEdit = false"
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
                                                        <td colspan="7"
                                                            class="px-3 py-10 text-center text-sm text-slate-500">
                                                            No class study times found.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    @elseif ($tab === 'subject')
                                        {{-- SUBJECT TABLE --}}
                                        <table class="w-full min-w-[1280px] text-left text-sm">
                                            <thead
                                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                                <tr>
                                                    <th class="px-3 py-3 font-semibold">Subject</th>
                                                    <th class="px-3 py-3 font-semibold">Day</th>
                                                    <th class="px-3 py-3 font-semibold">Period</th>
                                                    <th class="px-3 py-3 font-semibold">Start</th>
                                                    <th class="px-3 py-3 font-semibold">End</th>
                                                    <th class="px-3 py-3 font-semibold">Class</th>
                                                    <th class="px-3 py-3 font-semibold text-right">Actions</th>
                                                </tr>
                                            </thead>

                                            <tbody class="divide-y divide-slate-100 bg-white">
                                                @forelse ($subjectTimes as $slot)
                                                    @php
                                                        $slotClassId =
                                                            (string) ($slot->school_class_id ?? null ?:
                                                            $slot->subject?->school_class_id ?? '');
                                                        $slotClassTimeId = '';
                                                        $slotDay = strtolower((string) ($slot->day_of_week ?? 'all'));
                                                        $slotPeriod = strtolower((string) $slot->period);
                                                        $slotStart = \Illuminate\Support\Str::substr(
                                                            (string) $slot->start_time,
                                                            0,
                                                            5,
                                                        );
                                                        $slotEnd = \Illuminate\Support\Str::substr(
                                                            (string) $slot->end_time,
                                                            0,
                                                            5,
                                                        );

                                                        if (
                                                            $slotClassId !== '' &&
                                                            isset($classTimeOptionsByClass[$slotClassId])
                                                        ) {
                                                            foreach (
                                                                $classTimeOptionsByClass[$slotClassId]
                                                                as $classTimeOption
                                                            ) {
                                                                if (
                                                                    ($classTimeOption['day_of_week'] ?? '') ===
                                                                        $slotDay &&
                                                                    ($classTimeOption['period'] ?? '') ===
                                                                        $slotPeriod &&
                                                                    ($classTimeOption['start_time'] ?? '') ===
                                                                        $slotStart &&
                                                                    ($classTimeOption['end_time'] ?? '') === $slotEnd
                                                                ) {
                                                                    $slotClassTimeId =
                                                                        (string) ($classTimeOption['id'] ?? '');
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

                                                        <td class="px-3 py-3 align-top text-slate-700">
                                                            <span
                                                                class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                                {{ $dayOptions[$slotDay] ?? ucfirst($slotDay) }}
                                                            </span>
                                                        </td>

                                                        <td class="px-3 py-3 align-top text-slate-700">
                                                            <span
                                                                class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-700">
                                                                {{ $periodOptions[$slot->period] ?? ucfirst($slot->period) }}
                                                            </span>
                                                        </td>

                                                        <td class="whitespace-nowrap px-3 py-3 align-top text-slate-700">
                                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                                        </td>

                                                        <td class="whitespace-nowrap px-3 py-3 align-top text-slate-700">
                                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                        </td>

                                                        <td class="whitespace-nowrap px-3 py-3 align-top text-slate-700">
                                                            {{ $slot->schoolClass?->display_name ?? ($slot->subject?->schoolClass?->display_name ?? 'Unassigned') }}
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

                                                            <!-- Edit Subject Modal -->
                                                            <div x-show="openSubjectEdit" x-cloak
                                                                class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                                aria-modal="true" role="dialog">
                                                                <div class="absolute inset-0 bg-slate-900/50"
                                                                    @click="openSubjectEdit = false"></div>

                                                                <div
                                                                    class="relative z-10 w-full max-w-xl rounded-3xl bg-white p-5 shadow-2xl">
                                                                    <div class="mb-4 flex items-center justify-between">
                                                                        <h3 class="text-lg font-black text-slate-900">Edit
                                                                            Subject
                                                                            Time</h3>
                                                                        <button type="button"
                                                                            @click="openSubjectEdit = false"
                                                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                                            <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                                fill="currentColor">
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
                                                                            <label
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">Class</label>
                                                                            <select name="subject_class_id"
                                                                                class="js-subject-edit-class w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                @foreach ($classes as $classOption)
                                                                                    <option
                                                                                        value="{{ $classOption->id }}"
                                                                                        {{ $slotClassId === (string) $classOption->id ? 'selected' : '' }}>
                                                                                        {{ $classOption->display_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div>
                                                                            <label
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">Subject</label>
                                                                            <select name="subject_id"
                                                                                data-selected="{{ $slot->subject_id }}"
                                                                                class="js-subject-edit-subject w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                <option value="">Select a subject
                                                                                </option>
                                                                            </select>
                                                                        </div>

                                                                        <div>
                                                                            <label
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">Class
                                                                                Time</label>
                                                                            <select name="class_time_id"
                                                                                data-selected="{{ $slotClassTimeId }}"
                                                                                class="js-subject-edit-class-time w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                <option value="">Select class time
                                                                                </option>
                                                                            </select>
                                                                        </div>

                                                                        <div>
                                                                            <label
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">Teacher</label>
                                                                            <select name="teacher_id"
                                                                                data-selected="{{ (string) ($slot->teacher_id ?? null ?: $slot->subject?->teacher_id ?? '') }}"
                                                                                class="js-subject-edit-teacher w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                <option value="">Unassigned</option>
                                                                                @foreach ($teachers as $teacherOption)
                                                                                    <option
                                                                                        value="{{ $teacherOption->id }}"
                                                                                        {{ (int) ($slot->teacher_id ?? null ?: $slot->subject?->teacher_id ?? 0) === (int) $teacherOption->id ? 'selected' : '' }}>
                                                                                        {{ $teacherOption->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="flex justify-end gap-2 pt-2">
                                                                            <button type="button"
                                                                                @click="openSubjectEdit = false"
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
                                                        <td colspan="7"
                                                            class="px-3 py-10 text-center text-sm text-slate-500">
                                                            No subject study times found.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    @else
                                        {{-- TEACHER TABLE --}}
                                        <table class="w-full min-w-[1280px] text-left text-sm">
                                            <thead
                                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                                <tr>
                                                    <th class="px-3 py-3 font-semibold">Teacher</th>
                                                    <th class="px-3 py-3 font-semibold">Subject</th>
                                                    <th class="px-3 py-3 font-semibold">Class</th>
                                                    <th class="px-3 py-3 font-semibold">Day</th>
                                                    <th class="px-3 py-3 font-semibold">Period</th>
                                                    <th class="px-3 py-3 font-semibold">Start</th>
                                                    <th class="px-3 py-3 font-semibold">End</th>
                                                </tr>
                                            </thead>

                                            <tbody class="divide-y divide-slate-100 bg-white">
                                                @forelse ($teacherTimes as $slot)
                                                    @php
                                                        $slotTeacherName =
                                                            $slot->teacher?->name ??
                                                            ($slot->subject?->teacher?->name ?? 'Unassigned');
                                                        $slotDay = strtolower((string) ($slot->day_of_week ?? 'all'));
                                                    @endphp

                                                    <tr class="align-top hover:bg-slate-50/80">
                                                        <td class="px-3 py-3 align-top">
                                                            <div class="whitespace-nowrap font-semibold text-slate-800">
                                                                {{ $slotTeacherName }}
                                                            </div>
                                                        </td>
                                                        <td class="px-3 py-3 align-top">
                                                            <div class="whitespace-nowrap font-semibold text-slate-800">
                                                                {{ $slot->subject?->name ?? '-' }}
                                                            </div>
                                                            <div class="text-xs text-slate-400">
                                                                {{ \Illuminate\Support\Str::limit($slot->subject?->description ?: 'No description', 60) }}
                                                            </div>
                                                        </td>
                                                        <td class="whitespace-nowrap px-3 py-3 align-top text-slate-700">
                                                            {{ $slot->schoolClass?->display_name ?? ($slot->subject?->schoolClass?->display_name ?? 'Unassigned') }}
                                                        </td>
                                                        <td class="px-3 py-3 align-top text-slate-700">
                                                            <span
                                                                class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                                {{ $dayOptions[$slotDay] ?? ucfirst($slotDay) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-3 align-top text-slate-700">
                                                            <span
                                                                class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-700">
                                                                {{ $periodOptions[$slot->period] ?? ucfirst($slot->period) }}
                                                            </span>
                                                        </td>
                                                        <td class="whitespace-nowrap px-3 py-3 align-top text-slate-700">
                                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                                        </td>
                                                        <td class="whitespace-nowrap px-3 py-3 align-top text-slate-700">
                                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7"
                                                            class="px-3 py-10 text-center text-sm text-slate-500">
                                                            No teacher study times found.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    @endif

                                </div>
                            </div>

                            <div class="mt-5">
                                {{ $tab === 'class' ? $classTimes->links() : ($tab === 'subject' ? $subjectTimes->links() : $teacherTimes->links()) }}
                            </div>

                            @php
                                $recentItems =
                                    $tab === 'class'
                                        ? $recentClassTimes
                                        : ($tab === 'subject'
                                            ? $recentSubjectTimes
                                            : $recentTeacherTimes);
                            @endphp

                            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                                <div class="mb-3 flex items-center justify-between gap-2">
                                    <h3 class="text-sm font-black text-slate-900">Recently Added</h3>
                                    <span class="text-xs font-semibold text-slate-500">Latest
                                        {{ $recentItems->count() }}</span>
                                </div>

                                <div class="grid gap-3 md:grid-cols-2">
                                    @forelse ($recentItems as $recent)
                                        @php
                                            $recentDay = strtolower((string) ($recent->day_of_week ?? 'all'));
                                            $recentPeriod = strtolower((string) ($recent->period ?? 'custom'));
                                        @endphp

                                        <article class="rounded-xl border border-slate-200 bg-white p-3">
                                            @if ($tab === 'class')
                                                <div class="font-semibold text-slate-900">
                                                    {{ $recent->schoolClass?->display_name ?? 'Class slot' }}
                                                </div>
                                            @elseif ($tab === 'subject')
                                                <div class="font-semibold text-slate-900">
                                                    {{ $recent->subject?->name ?? 'Subject slot' }}
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    {{ $recent->schoolClass?->display_name ?? ($recent->subject?->schoolClass?->display_name ?? 'Unassigned class') }}
                                                </div>
                                            @else
                                                <div class="font-semibold text-slate-900">
                                                    {{ $recent->teacher?->name ?? ($recent->subject?->teacher?->name ?? 'Unassigned teacher') }}
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    {{ $recent->subject?->name ?? 'Subject slot' }}
                                                </div>
                                            @endif

                                            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                                <span
                                                    class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 font-semibold text-slate-700">
                                                    {{ $dayOptions[$recentDay] ?? ucfirst($recentDay) }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2 py-0.5 font-semibold uppercase tracking-wide text-indigo-700">
                                                    {{ $periodOptions[$recentPeriod] ?? ucfirst($recentPeriod) }}
                                                </span>
                                            </div>

                                            <div class="mt-2 text-xs text-slate-600">
                                                {{ \Carbon\Carbon::parse($recent->start_time)->format('h:i A') }}
                                                -
                                                {{ \Carbon\Carbon::parse($recent->end_time)->format('h:i A') }}
                                            </div>
                                            <div class="mt-1 text-[11px] text-slate-400">
                                                Added {{ optional($recent->created_at)->diffForHumans() ?? '-' }}
                                            </div>
                                        </article>
                                    @empty
                                        <div
                                            class="md:col-span-2 rounded-xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center text-sm text-slate-500">
                                            No recent items yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
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
            const teacherOptionsAll = @json($teacherOptionsAll);
            const classTimeOptionsByClass = @json($classTimeOptionsByClass);
            const occupiedClassSlotsByClass = @json($occupiedClassSlotsByClass);
            const teacherBusySlots = @json($teacherBusySlots);
            const getSubjectsForClass = (classId, mode = 'class') => {
                if (mode === 'all') return subjectOptionsAll;
                if (mode === 'assignable') return subjectOptionsAll;
                if (!classId || classId === 'all') return subjectOptionsAll;

                const classSubjects = subjectOptionsByClass[classId] || [];
                return classSubjects;
            };

            const renderSubjectOptions = (subjectSelect, classId, selectedValue, includeAllOption = false, mode =
                'class') => {
                if (!subjectSelect) return;

                const subjects = getSubjectsForClass(classId, mode);
                const options = [];

                if (includeAllOption) {
                    const selectedAll = String(selectedValue) === 'all' ? ' selected' : '';
                    options.push(`<option value="all"${selectedAll}>All Subjects</option>`);
                }

                if (subjects.length === 0) {
                    const emptyMessage = mode === 'all' ?
                        'No subjects available' :
                        'No subjects in selected class';
                    options.push(`<option value="">${emptyMessage}</option>`);
                    subjectSelect.innerHTML = options.join('');
                    return;
                }

                for (const subject of subjects) {
                    const selected = String(selectedValue) === String(subject.id) ? ' selected' : '';
                    options.push(`<option value="${subject.id}"${selected}>${subject.label}</option>`);
                }

                subjectSelect.innerHTML = options.join('');
            };

            const renderClassTimeOptions = (timeSelect, classId, selectedValue, currentSubjectStudyTimeId =
                null) => {
                if (!timeSelect) return;

                const slots = classTimeOptionsByClass[classId] || [];
                const occupiedSlots = occupiedClassSlotsByClass[classId] || {};
                const options = [];

                if (slots.length === 0) {
                    options.push('<option value="">No class times in selected class</option>');
                    timeSelect.innerHTML = options.join('');
                    return;
                }

                options.push('<option value="">Select class time</option>');

                for (const slot of slots) {
                    const ownerId = occupiedSlots[slot.key];
                    const isCurrentSlot = currentSubjectStudyTimeId !== null &&
                        currentSubjectStudyTimeId !== '' &&
                        String(ownerId) === String(currentSubjectStudyTimeId);
                    const isOccupiedByAnotherSubject = Boolean(ownerId) && !isCurrentSlot;

                    const selected = String(selectedValue) === String(slot.id) ? ' selected' : '';
                    const disabled = isOccupiedByAnotherSubject ? ' disabled' : '';
                    const label = isOccupiedByAnotherSubject ?
                        `${slot.label} (Already used in this class)` :
                        slot.label;
                    options.push(`<option value="${slot.id}"${selected}${disabled}>${label}</option>`);
                }

                timeSelect.innerHTML = options.join('');
            };

            const getClassSlotById = (classId, classTimeId) => {
                if (!classId || !classTimeId) return null;

                const slots = classTimeOptionsByClass[classId] || [];
                for (const slot of slots) {
                    if (String(slot.id) === String(classTimeId)) {
                        return slot;
                    }
                }

                return null;
            };

            const renderTeacherOptions = (teacherSelect, classId, classTimeId, selectedValue,
                currentSubjectStudyTimeId = null) => {
                if (!teacherSelect) return;

                const selectedSlot = getClassSlotById(classId, classTimeId);
                const options = ['<option value="">Unassigned</option>'];

                const selectedDay = String(selectedSlot?.day_of_week || 'all').toLowerCase();
                const selectedStart = String(selectedSlot?.start_time || '');
                const selectedEnd = String(selectedSlot?.end_time || '');

                const dayOverlaps = (busyDay) => {
                    const normalizedBusyDay = String(busyDay || 'all').toLowerCase();
                    if (!selectedSlot) return false;
                    if (selectedDay === 'all' || normalizedBusyDay === 'all') return true;
                    return selectedDay === normalizedBusyDay;
                };

                const timeOverlaps = (busyStart, busyEnd) => {
                    if (!selectedSlot) return false;
                    return selectedStart < String(busyEnd) && selectedEnd > String(busyStart);
                };

                for (const teacher of teacherOptionsAll) {
                    const isBusyInAnotherClass = teacherBusySlots.some((busySlot) => {
                        if (String(busySlot.teacher_id) !== String(teacher.id)) return false;
                        if (!dayOverlaps(busySlot.day_of_week)) return false;
                        if (!timeOverlaps(busySlot.start_time, busySlot.end_time)) return false;

                        return !(currentSubjectStudyTimeId !== null &&
                            currentSubjectStudyTimeId !== '' &&
                            String(busySlot.subject_study_time_id) === String(
                                currentSubjectStudyTimeId));
                    });

                    const selected = String(selectedValue) === String(teacher.id) ? ' selected' : '';
                    const disabled = isBusyInAnotherClass ? ' disabled' : '';
                    const label = isBusyInAnotherClass ?
                        `${teacher.label} (Busy at this time)` :
                        teacher.label;
                    options.push(`<option value="${teacher.id}"${selected}${disabled}>${label}</option>`);
                }

                teacherSelect.innerHTML = options.join('');
            };

            const normalizeFlexibleTime = (rawValue) => {
                const value = String(rawValue || '').trim();
                if (!value) return null;

                const twentyFourMatch = value.match(/^([01]?\d|2[0-3]):([0-5]\d)$/);
                if (twentyFourMatch) {
                    const hour = String(twentyFourMatch[1]).padStart(2, '0');
                    const minute = String(twentyFourMatch[2]).padStart(2, '0');
                    return `${hour}:${minute}`;
                }

                const twelveHourMatch = value.match(/^(0?[1-9]|1[0-2]):([0-5]\d)\s*(AM|PM)$/i);
                if (twelveHourMatch) {
                    let hour = Number(twelveHourMatch[1]);
                    const minute = String(twelveHourMatch[2]).padStart(2, '0');
                    const meridiem = String(twelveHourMatch[3]).toUpperCase();

                    if (meridiem === 'AM') {
                        hour = hour === 12 ? 0 : hour;
                    } else {
                        hour = hour === 12 ? 12 : hour + 12;
                    }

                    return `${String(hour).padStart(2, '0')}:${minute}`;
                }

                const hourOnlyTwelveHourMatch = value.match(/^(0?[1-9]|1[0-2])\s*(AM|PM)$/i);
                if (hourOnlyTwelveHourMatch) {
                    let hour = Number(hourOnlyTwelveHourMatch[1]);
                    const meridiem = String(hourOnlyTwelveHourMatch[2]).toUpperCase();

                    if (meridiem === 'AM') {
                        hour = hour === 12 ? 0 : hour;
                    } else {
                        hour = hour === 12 ? 12 : hour + 12;
                    }

                    return `${String(hour).padStart(2, '0')}:00`;
                }

                return null;
            };

            const periodDefaultTimes = {
                morning: {
                    start: '07:00',
                    end: '10:00'
                },
                afternoon: {
                    start: '10:00',
                    end: '13:00'
                },
                evening: {
                    start: '13:00',
                    end: '16:00'
                },
                night: {
                    start: '17:00',
                    end: '20:00'
                },
                custom: null,
            };

            const applyPeriodPreset = (periodValue, startInput, endInput, force = false) => {
                if (!startInput || !endInput) return;

                const preset = periodDefaultTimes[String(periodValue || '').toLowerCase()] || null;
                if (!preset) return;

                if (!force) {
                    const hasStart = String(startInput.value || '').trim() !== '';
                    const hasEnd = String(endInput.value || '').trim() !== '';
                    if (hasStart || hasEnd) return;
                }

                startInput.value = preset.start;
                endInput.value = preset.end;
            };

            const bindClassSlotPeriodBehavior = (row) => {
                if (!row) return;

                const periodSelect = row.querySelector('.js-class-period-select');
                const startInput = row.querySelector('.js-class-start-input');
                const endInput = row.querySelector('.js-class-end-input');
                if (!periodSelect || !startInput || !endInput) return;

                applyPeriodPreset(periodSelect.value, startInput, endInput, false);

                periodSelect.addEventListener('change', () => {
                    applyPeriodPreset(periodSelect.value, startInput, endInput, true);
                });
            };

            const bindClassEditPeriodBehavior = (form) => {
                if (!form) return;

                const periodSelect = form.querySelector('.js-edit-class-period');
                const startInput = form.querySelector('.js-edit-class-start');
                const endInput = form.querySelector('.js-edit-class-end');
                if (!periodSelect || !startInput || !endInput) return;

                periodSelect.addEventListener('change', () => {
                    const preset = periodDefaultTimes[String(periodSelect.value || '').toLowerCase()] ||
                        null;
                    if (!preset) return;

                    startInput.value = preset.start;
                    endInput.value = preset.end;
                });
            };

            // Add more rows for Class Study Time
            const classSlotRows = document.getElementById('class_slot_rows');
            const classSlotTemplate = document.getElementById('class_slot_row_template');
            const addClassSlotBtn = document.getElementById('add_class_slot_btn');

            if (classSlotRows && classSlotTemplate && addClassSlotBtn) {
                let nextIndex = Number(classSlotRows.dataset.nextIndex || classSlotRows.querySelectorAll(
                    '.js-class-slot-row').length);

                const syncRemoveButtons = () => {
                    const rows = classSlotRows.querySelectorAll('.js-class-slot-row');
                    rows.forEach((row) => {
                        const removeButton = row.querySelector('.js-remove-class-slot');
                        if (!removeButton) return;
                        removeButton.classList.toggle('hidden', rows.length <= 1);
                    });
                };

                addClassSlotBtn.addEventListener('click', () => {
                    const html = classSlotTemplate.innerHTML.split('__INDEX__').join(String(nextIndex));
                    classSlotRows.insertAdjacentHTML('beforeend', html);
                    const addedRow = classSlotRows.querySelector('.js-class-slot-row:last-child');
                    bindClassSlotPeriodBehavior(addedRow);
                    nextIndex += 1;
                    classSlotRows.dataset.nextIndex = String(nextIndex);
                    syncRemoveButtons();
                });

                classSlotRows.addEventListener('click', (event) => {
                    const clicked = event.target;
                    if (!(clicked instanceof HTMLElement)) return;

                    const removeButton = clicked.closest('.js-remove-class-slot');
                    if (!removeButton) return;

                    const rows = classSlotRows.querySelectorAll('.js-class-slot-row');
                    if (rows.length <= 1) return;

                    const row = removeButton.closest('.js-class-slot-row');
                    if (row) {
                        row.remove();
                        syncRemoveButtons();
                    }
                });

                syncRemoveButtons();

                classSlotRows.querySelectorAll('.js-class-slot-row').forEach((row) => {
                    bindClassSlotPeriodBehavior(row);
                });
            }

            document.querySelectorAll('.js-class-edit-form').forEach((form) => {
                bindClassEditPeriodBehavior(form);
            });

            const classTimeCreateForm = document.getElementById('class_time_create_form');
            if (classTimeCreateForm) {
                classTimeCreateForm.addEventListener('submit', (event) => {
                    const timeInputs = [...classTimeCreateForm.querySelectorAll('.js-class-time-input')];
                    let firstInvalidInput = null;

                    for (const input of timeInputs) {
                        const normalized = normalizeFlexibleTime(input.value);
                        if (!normalized) {
                            if (!firstInvalidInput) firstInvalidInput = input;
                            continue;
                        }
                        input.value = normalized;
                    }

                    if (!firstInvalidInput) return;

                    event.preventDefault();
                    event.stopImmediatePropagation();
                    firstInvalidInput.focus();

                    const message = 'Invalid time format. Use 07:30 AM or 19:30.';
                    if (hasSwal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Time',
                            text: message
                        });
                    } else {
                        alert(message);
                    }
                });
            }

            // Add Subject Time form
            const subjectFormClass = document.getElementById('subject_form_class_id');
            const subjectSlotRows = document.getElementById('subject_slot_rows');
            const subjectSlotTemplate = document.getElementById('subject_slot_row_template');
            const addSubjectSlotBtn = document.getElementById('add_subject_slot_btn');

            const bindSubjectSlotRow = (row, classId, useDatasetSelection = true) => {
                if (!row) return;

                const subjectSelect = row.querySelector('.js-subject-form-subject');
                const classTimeSelect = row.querySelector('.js-subject-form-class-time');
                const teacherSelect = row.querySelector('.js-subject-form-teacher');
                if (!subjectSelect || !classTimeSelect || !teacherSelect) return;

                const selectedSubject = useDatasetSelection ?
                    (subjectSelect.dataset.selected || subjectSelect.value || '') :
                    (subjectSelect.value || '');
                const selectedClassTime = useDatasetSelection ?
                    (classTimeSelect.dataset.selected || classTimeSelect.value || '') :
                    (classTimeSelect.value || '');
                const selectedTeacher = useDatasetSelection ?
                    (teacherSelect.dataset.selected || teacherSelect.value || '') :
                    (teacherSelect.value || '');

                renderSubjectOptions(subjectSelect, classId, selectedSubject, false, 'assignable');
                renderClassTimeOptions(classTimeSelect, classId, selectedClassTime, null);
                renderTeacherOptions(
                    teacherSelect,
                    classId,
                    classTimeSelect.value || selectedClassTime,
                    selectedTeacher,
                    null
                );

                subjectSelect.dataset.selected = '';
                classTimeSelect.dataset.selected = '';
                teacherSelect.dataset.selected = '';

                if (classTimeSelect.dataset.bound !== '1') {
                    classTimeSelect.addEventListener('change', () => {
                        renderTeacherOptions(
                            teacherSelect,
                            subjectFormClass?.value || '',
                            classTimeSelect.value || '',
                            teacherSelect.value || '',
                            null
                        );
                    });
                    classTimeSelect.dataset.bound = '1';
                }
            };

            if (subjectFormClass && subjectSlotRows && subjectSlotTemplate && addSubjectSlotBtn) {
                let nextIndex = Number(subjectSlotRows.dataset.nextIndex || subjectSlotRows.querySelectorAll(
                    '.js-subject-slot-row').length);

                const syncSubjectSlotRemoveButtons = () => {
                    const rows = subjectSlotRows.querySelectorAll('.js-subject-slot-row');
                    rows.forEach((row) => {
                        const removeButton = row.querySelector('.js-remove-subject-slot');
                        if (!removeButton) return;
                        removeButton.classList.toggle('hidden', rows.length <= 1);
                    });
                };

                const refreshSubjectSlotRows = (useDatasetSelection = false) => {
                    const currentClassId = subjectFormClass.value || '';
                    subjectSlotRows.querySelectorAll('.js-subject-slot-row').forEach((row) => {
                        bindSubjectSlotRow(row, currentClassId, useDatasetSelection);
                    });
                };

                refreshSubjectSlotRows(true);
                syncSubjectSlotRemoveButtons();

                subjectFormClass.addEventListener('change', () => {
                    refreshSubjectSlotRows(false);
                });

                addSubjectSlotBtn.addEventListener('click', () => {
                    const html = subjectSlotTemplate.innerHTML.split('__INDEX__').join(String(nextIndex));
                    subjectSlotRows.insertAdjacentHTML('beforeend', html);
                    const addedRow = subjectSlotRows.querySelector('.js-subject-slot-row:last-child');
                    bindSubjectSlotRow(addedRow, subjectFormClass.value || '', false);
                    nextIndex += 1;
                    subjectSlotRows.dataset.nextIndex = String(nextIndex);
                    syncSubjectSlotRemoveButtons();
                });

                subjectSlotRows.addEventListener('click', (event) => {
                    const clicked = event.target;
                    if (!(clicked instanceof HTMLElement)) return;

                    const removeButton = clicked.closest('.js-remove-subject-slot');
                    if (!removeButton) return;

                    const rows = subjectSlotRows.querySelectorAll('.js-subject-slot-row');
                    if (rows.length <= 1) return;

                    const row = removeButton.closest('.js-subject-slot-row');
                    if (row) {
                        row.remove();
                        syncSubjectSlotRemoveButtons();
                    }
                });
            }

            // Edit Subject forms
            document.querySelectorAll('.js-subject-edit-form').forEach((form) => {
                const classSelect = form.querySelector('.js-subject-edit-class');
                const subjectSelect = form.querySelector('.js-subject-edit-subject');
                const classTimeSelect = form.querySelector('.js-subject-edit-class-time');
                const teacherSelect = form.querySelector('.js-subject-edit-teacher');
                const currentSubjectStudyTimeId = form.dataset.subjectStudyTimeId || '';

                if (!classSelect || !subjectSelect || !classTimeSelect || !teacherSelect) return;

                const selectedSubject = subjectSelect.dataset.selected || '';
                const selectedClassTime = classTimeSelect.dataset.selected || '';
                const selectedTeacher = teacherSelect.dataset.selected || '';

                renderSubjectOptions(subjectSelect, classSelect.value, selectedSubject, false,
                    'assignable');
                renderClassTimeOptions(classTimeSelect, classSelect.value, selectedClassTime,
                    currentSubjectStudyTimeId);
                renderTeacherOptions(teacherSelect, classSelect.value, selectedClassTime, selectedTeacher,
                    currentSubjectStudyTimeId);

                classSelect.addEventListener('change', () => {
                    renderSubjectOptions(subjectSelect, classSelect.value, '', false, 'assignable');
                    renderClassTimeOptions(classTimeSelect, classSelect.value, '',
                        currentSubjectStudyTimeId);
                    renderTeacherOptions(teacherSelect, classSelect.value, '', '',
                        currentSubjectStudyTimeId);
                });

                classTimeSelect.addEventListener('change', () => {
                    renderTeacherOptions(teacherSelect, classSelect.value, classTimeSelect.value,
                        teacherSelect.value, currentSubjectStudyTimeId);
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

            // SweetAlert confirmations (same behavior as Student page)
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

            // SweetAlert for validation + flash (same as your student page)
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
