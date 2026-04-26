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
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $showCreateFormOnLoad = in_array(old('_form'), ['create_class_time', 'create_subject_time'], true);
        @endphp

        <div class="grid gap-6 xl:grid-cols-12">

            <!-- LEFT: Create Times (same card style as Student create card) -->
            <section x-data="{
                createOpen: @js($showCreateFormOnLoad),
                isDesktop: false,
                init() {
                    const media = window.matchMedia('(min-width: 1280px)');
                    const update = () => {
                        this.isDesktop = media.matches;
                        if (this.isDesktop) {
                            this.createOpen = true;
                        } else if (!@js($showCreateFormOnLoad)) {
                            this.createOpen = false;
                        }
                    };
            
                    update();
            
                    if (typeof media.addEventListener === 'function') {
                        media.addEventListener('change', update);
                    } else if (typeof media.addListener === 'function') {
                        media.addListener(update);
                    }
                }
            }" x-init="init()"
                class="study-time-reveal study-time-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--sd: 3;">

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Add Time Slot</h2>
                        <p class="mt-1 text-xs text-slate-500">Create study schedules for classes and subjects.</p>
                    </div>
                    <button type="button" @click="createOpen = !createOpen"
                        :aria-expanded="(createOpen || isDesktop).toString()" aria-controls="time-study-create-panel"
                        class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 shadow-sm hover:bg-indigo-100 xl:hidden">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" x-show="!(createOpen || isDesktop)"></path>
                            <path d="M5 12h14" x-show="createOpen || isDesktop"></path>
                        </svg>
                        <span x-show="!(createOpen || isDesktop)">Add Time Slot</span>
                        <span x-show="createOpen || isDesktop">Hide Form</span>
                    </button>
                </div>

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

                <div id="time-study-create-panel" x-show="createOpen || isDesktop" x-cloak
                    x-transition.opacity.duration.150ms>
                    <!-- Class Study Time -->
                    <form method="POST" action="{{ route('admin.time-studies.classes.store') }}"
                        id="class_time_create_form" class="js-create-form mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="_form" value="create_class_time">

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
                                                <option value="{{ $dayKey }}"
                                                    {{ $dayKey === 'all' ? 'selected' : '' }}>
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
                        id="subject_time_create_form" class="js-create-form mt-6 space-y-4 border-t border-slate-200 pt-6">
                        @csrf
                        <input type="hidden" name="_form" value="create_subject_time">

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

                        <div id="subject_slot_rows" class="space-y-3"
                            data-next-index="{{ count($subjectFormSlotRows) }}">
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
                                            <label class="mb-1 block text-xs font-semibold text-slate-600">Class
                                                Time</label>
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
                                                <option value="">Select teacher</option>
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
                                            <option value="">Select teacher</option>
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
                </div>
            </section>

            <!-- RIGHT: List + Filters (same style as Student list card) -->
            <section
                class="study-time-reveal study-time-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--sd: 4;">

                <div x-data="{
                    filterOpen: false,
                    activeTab: @js(in_array($tab, ['class', 'subject', 'teacher'], true) ? $tab : 'class'),
                    switchTab(tab) {
                        if (tab === 'teacher') {
                            const teacherUrl = new URL(@js(route('admin.time-studies.index')), window.location.origin);
                            teacherUrl.searchParams.set('tab', 'teacher');
                            teacherUrl.searchParams.set('per_page', @js((string) $perPage));
                            window.location.href = teacherUrl.toString();
                            return;
                        }

                        this.activeTab = tab;
                        const url = new URL(window.location.href);
                        url.searchParams.set('tab', tab);
                        window.history.replaceState({}, '', url);
                    }
                }" @open-filter-panel.window="filterOpen = true" class="space-y-4">

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-black text-slate-900">
                            <span x-show="activeTab === 'class'" x-cloak>Class List</span>
                            <span x-show="activeTab === 'subject'" x-cloak>Subject List</span>
                            <span x-show="activeTab === 'teacher'" x-cloak>Teacher Study List</span>
                        </h2>

                        <div class="flex w-full flex-wrap items-center justify-end gap-2 sm:w-auto">
                            <div class="max-w-full overflow-x-auto rounded-xl border border-slate-200 bg-slate-50 p-1">
                                <div class="inline-flex min-w-max">
                                    <button type="button" @click="switchTab('class')"
                                        class="rounded-lg px-3 py-1.5 text-sm font-semibold"
                                        :class="activeTab === 'class' ? 'bg-white text-slate-900 shadow-sm' :
                                            'text-slate-600 hover:text-slate-900'">
                                        Class Times
                                    </button>
                                    <button type="button" @click="switchTab('subject')"
                                        class="rounded-lg px-3 py-1.5 text-sm font-semibold"
                                        :class="activeTab === 'subject' ? 'bg-white text-slate-900 shadow-sm' :
                                            'text-slate-600 hover:text-slate-900'">
                                        Subject Times
                                    </button>
                                    <button type="button" @click="switchTab('teacher')"
                                        class="rounded-lg px-3 py-1.5 text-sm font-semibold"
                                        :class="activeTab === 'teacher' ? 'bg-white text-slate-900 shadow-sm' :
                                            'text-slate-600 hover:text-slate-900'">
                                        Teacher Times
                                    </button>
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
                                    <input type="hidden" name="tab" :value="activeTab">

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
                                <div class="max-h-[800px] overflow-auto">

                                    <div x-show="activeTab === 'class'" x-cloak>
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

                                                        <td class="whitespace-nowrap px-3 py-3 align-top">
                                                            <div
                                                                class="flex flex-nowrap items-center justify-end gap-2 whitespace-nowrap">
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
                                    </div>

                                    <div x-show="activeTab === 'subject'" x-cloak>
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

                                                        <td class="whitespace-nowrap px-3 py-3 align-top">
                                                            <div
                                                                class="flex flex-nowrap items-center justify-end gap-2 whitespace-nowrap">
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
                                                                                <option value="">Select teacher</option>
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
                                    </div>

                                    <div x-show="activeTab === 'teacher'" x-cloak>
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
                                    </div>

                                </div>
                            </div>

                            <div class="mt-5">
                                <div x-show="activeTab === 'class'" x-cloak>
                                    {{ $classTimes->links() }}
                                </div>
                                <div x-show="activeTab === 'subject'" x-cloak>
                                    {{ $subjectTimes->links() }}
                                </div>
                                <div x-show="activeTab === 'teacher'" x-cloak>
                                    {{ $teacherTimes->links() }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @php
        $timeStudiesPageData = [
            'subjectOptionsByClass' => $subjectOptionsByClass,
            'subjectOptionsAll' => $subjectOptionsAll,
            'teacherOptionsAll' => $teacherOptionsAll,
            'classTimeOptionsByClass' => $classTimeOptionsByClass,
            'occupiedClassSlotsByClass' => $occupiedClassSlotsByClass,
            'teacherBusySlots' => $teacherBusySlots,
            'validationErrors' => $errors->all(),
            'flash' => [
                'success' => session('success'),
                'warning' => session('warning'),
                'error' => session('error'),
            ],
        ];
    @endphp
    <script id="admin-time-studies-data" type="application/json">{!! json_encode($timeStudiesPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/admin/time-studies.js'])
@endsection
