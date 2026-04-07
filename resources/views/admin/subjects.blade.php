@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="subject-stage space-y-6">
        <x-admin.page-header reveal-class="subject-reveal" delay="1" icon="subjects" title="Subject Management"
            subtitle="Create subjects and manage schedules in Time Studies.">
            <x-slot:stats>
                <span class="admin-page-stat">Total: {{ $stats['total'] }}</span>
                <span class="admin-page-stat admin-page-stat--emerald">Active:
                    {{ $stats['active'] }}</span>
                <span class="admin-page-stat admin-page-stat--sky">Assigned:
                    {{ $stats['assigned'] }}</span>
                <span class="admin-page-stat admin-page-stat--amber">With Teacher:
                    {{ $stats['withTeacher'] }}</span>
            </x-slot:stats>
        </x-admin.page-header>

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
        @php
            $showCreateFormOnLoad = old('_form') === 'create_subject';
        @endphp
        <div class="grid gap-6 xl:grid-cols-12">
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
                class="subject-reveal subject-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--sd: 3;">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Create Subject</h2>
                        <p class="mt-1 text-xs text-slate-500">Create a new subject. Manage class and time from Time
                            Studies.</p>
                    </div>
                    <button type="button" @click="createOpen = !createOpen"
                        :aria-expanded="(createOpen || isDesktop).toString()" aria-controls="create-subject-form-panel"
                        class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 shadow-sm hover:bg-indigo-100 xl:hidden">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" x-show="!(createOpen || isDesktop)"></path>
                            <path d="M5 12h14" x-show="createOpen || isDesktop"></path>
                        </svg>
                        <span x-show="!(createOpen || isDesktop)">Create Subject</span>
                        <span x-show="createOpen || isDesktop">Hide Form</span>
                    </button>
                </div>
                <form id="create-subject-form-panel" method="POST" action="{{ route('admin.subjects.store') }}"
                    class="js-create-form mt-5 space-y-4" x-show="createOpen || isDesktop" x-cloak
                    x-transition.opacity.duration.150ms>
                    @csrf
                    <input type="hidden" name="_form" value="create_subject">
                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold text-slate-600">Subject Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Mathematics">
                        @error('name', 'subjectCreate')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <input type="hidden" name="code"
                        value="{{ old('code', 'SUB' . now()->format('His') . strtoupper(substr(md5((string) microtime(true)), 0, 4))) }}">

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Study Times</label>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                            Study times are now managed in Time Studies.
                            <a href="{{ route('admin.time-studies.index', ['tab' => 'subject']) }}"
                                class="font-semibold text-indigo-700 hover:text-indigo-900">Manage subject times</a>
                        </div>
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
                class="subject-reveal subject-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--sd: 4;">
                <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-black text-slate-900">Subject List</h2>
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
                                        <a href="{{ route('admin.subjects.index') }}"
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

                                <form method="GET" action="{{ route('admin.subjects.index') }}"
                                    class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                                    <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                            <input id="q" name="q" type="text"
                                                value="{{ $search }}"
                                                placeholder="Search subject, teacher, class, time, or schedule"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        </section>
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Class</h4>
                                            <select name="class_id"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $classId === 'all' ? 'selected' : '' }}>All
                                                    Classes</option>
                                                @foreach ($classes as $classOption)
                                                    <option value="{{ $classOption->id }}"
                                                        {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                                        {{ $classOption->display_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Teacher</h4>
                                            <select name="teacher_id"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $teacherId === 'all' ? 'selected' : '' }}>
                                                    All
                                                    Teachers</option>
                                                @foreach ($teachers as $teacherOption)
                                                    <option value="{{ $teacherOption->id }}"
                                                        {{ $teacherId === (string) $teacherOption->id ? 'selected' : '' }}>
                                                        {{ $teacherOption->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                            <select name="status"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All
                                                </option>
                                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                        </section>
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Period</h4>
                                            <select name="period"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All
                                                    Periods</option>
                                                @foreach ($periodOptions as $periodKey => $periodLabel)
                                                    <option value="{{ $periodKey }}"
                                                        {{ $period === $periodKey ? 'selected' : '' }}>
                                                        {{ $periodLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Schedule</h4>
                                            <select name="schedule"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $schedule === 'all' ? 'selected' : '' }}>All
                                                    Schedules</option>
                                                <option value="with_schedule"
                                                    {{ $schedule === 'with_schedule' ? 'selected' : '' }}>With Schedule
                                                </option>
                                                <option value="without_schedule"
                                                    {{ $schedule === 'without_schedule' ? 'selected' : '' }}>Without
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
                            <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200">
                                <div class="max-h-[700px] overflow-auto">
                                    <table class="w-full min-w-[1150px] text-left text-sm">
                                        <thead
                                            class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                            <tr>
                                                <th class="px-3 py-3 font-semibold">Subject</th>
                                                <th class="px-3 py-3 font-semibold">Study Time</th>
                                                <th class="px-3 py-3 font-semibold">Class</th>
                                                <th class="px-3 py-3 font-semibold">Teacher</th>
                                                <th class="px-3 py-3 font-semibold">Students Learning</th>
                                                <th class="px-3 py-3 font-semibold">Status</th>
                                                <th class="whitespace-nowrap px-3 py-3 font-semibold">Created</th>
                                                <th class="px-3 py-3 font-semibold text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 bg-white">
                                            @forelse ($subjects as $subject)
                                                <tr class="align-top hover:bg-slate-50/80" x-data="{ open: false, detailOpen: false }">
                                                    <td class="px-3 py-3">
                                                        <div class=" whitespace-nowrap font-semibold text-slate-700">
                                                            {{ $subject->name }}
                                                        </div>
                                                        <div class="text-xs text-slate-400">
                                                            {{ \Illuminate\Support\Str::limit($subject->description ?: 'No description', 60) }}
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-3 text-slate-600">
                                                        @if ($subject->studySchedules->isNotEmpty())
                                                            @php
                                                                $subjectStudyRows = $subject->studySchedules
                                                                    ->sortBy(function ($slot) {
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
                                                                            (string) ($slot->day_of_week ?? 'all'),
                                                                        );

                                                                        return sprintf(
                                                                            '%02d-%s-%s',
                                                                            $daySortMap[$dayKey] ?? 99,
                                                                            (string) ($slot->period ?? ''),
                                                                            (string) ($slot->start_time ?? ''),
                                                                        );
                                                                    })
                                                                    ->values();
                                                                $previewSubjectRows = $subjectStudyRows->take(3);
                                                            @endphp
                                                            <div class="space-y-2">
                                                                <div class="flex max-w-sm flex-wrap gap-1.5">
                                                                    @foreach ($previewSubjectRows as $slot)
                                                                        <div
                                                                            class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px]">
                                                                            <span
                                                                                class="font-bold uppercase tracking-wide text-indigo-700">
                                                                                {{ $periodOptions[$slot->period] ?? ucfirst($slot->period) }}
                                                                            </span>
                                                                            <span class="text-indigo-300">|</span>
                                                                            <span
                                                                                class="whitespace-nowrap font-semibold text-slate-700">
                                                                                {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                                                                ->
                                                                                {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                @if ($subjectStudyRows->count() > $previewSubjectRows->count())
                                                                    <div class="text-[11px] font-semibold text-slate-400">
                                                                        +{{ $subjectStudyRows->count() - $previewSubjectRows->count() }}
                                                                        more slots
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @else
                                                            @php
                                                                $subjectStartTime =
                                                                    $subject->study_start_time ?: $subject->study_time;
                                                                $subjectEndTime = $subject->study_end_time;
                                                            @endphp
                                                            @if ($subjectStartTime && $subjectEndTime)
                                                                {{ \Carbon\Carbon::parse($subjectStartTime)->format('h:i A') }}
                                                                ->
                                                                {{ \Carbon\Carbon::parse($subjectEndTime)->format('h:i A') }}
                                                            @elseif ($subjectStartTime)
                                                                {{ \Carbon\Carbon::parse($subjectStartTime)->format('h:i A') }}
                                                            @else
                                                                -
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                                                        @php
                                                            $slotClasses = $subject->studySchedules
                                                                ->map(fn($slot) => $slot->schoolClass?->display_name)
                                                                ->filter()
                                                                ->unique()
                                                                ->values();
                                                        @endphp
                                                        @if ($slotClasses->isNotEmpty())
                                                            <div class="flex flex-wrap gap-1.5">
                                                                @foreach ($slotClasses as $slotClassName)
                                                                    <span
                                                                        class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                                                        {{ $slotClassName }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            {{ $subject->schoolClass?->display_name ?: 'Unassigned' }}
                                                        @endif
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                                                        @php
                                                            $slotTeachers = $subject->studySchedules
                                                                ->map(fn($slot) => $slot->teacher?->name)
                                                                ->filter()
                                                                ->unique()
                                                                ->values();
                                                        @endphp
                                                        @if ($slotTeachers->isNotEmpty())
                                                            <div class="flex flex-wrap gap-1.5">
                                                                @foreach ($slotTeachers as $slotTeacherName)
                                                                    <span
                                                                        class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                                                        {{ $slotTeacherName }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            {{ $subject->teacher?->name ?: 'Unassigned' }}
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-3 text-slate-600">
                                                        {{ $subject->students_count ?? 0 }}</td>
                                                    <td class="px-3 py-3">
                                                        @if ($subject->is_active)
                                                            <span
                                                                class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                                <span
                                                                    class="status-dot h-2 w-2 rounded-full bg-emerald-500"></span>Active
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                                                <span
                                                                    class="h-2 w-2 rounded-full bg-rose-500"></span>Inactive
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-3 text-slate-500">
                                                        {{ $subject->created_at->format('M d, Y') }}</td>
                                                    <td class="whitespace-nowrap px-3 py-3">
                                                        <div
                                                            class="flex flex-nowrap items-center justify-end gap-2 whitespace-nowrap">
                                                            <button @click="detailOpen = true" type="button"
                                                                class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                                View Detail
                                                            </button>

                                                            <button @click="open = true" type="button"
                                                                class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                                Edit
                                                            </button>

                                                            <form method="POST"
                                                                action="{{ route('admin.subjects.status', $subject) }}"
                                                                class="js-status-form"
                                                                data-subject="{{ $subject->name }}"
                                                                data-action="{{ $subject->is_active ? 'set inactive' : 'set active' }}">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="whitespace-nowrap rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $subject->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                                    {{ $subject->is_active ? 'Set Inactive' : 'Set Active' }}
                                                                </button>
                                                            </form>

                                                            <form method="POST"
                                                                action="{{ route('admin.subjects.destroy', $subject) }}"
                                                                class="js-delete-form"
                                                                data-subject="{{ $subject->name }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>

                                                        <div x-show="detailOpen" x-cloak
                                                            class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                            aria-modal="true" role="dialog">
                                                            <div class="absolute inset-0 bg-slate-900/50"
                                                                @click="detailOpen = false"></div>

                                                            <div
                                                                class="relative z-10 w-full max-w-3xl rounded-3xl bg-white p-5 shadow-2xl">
                                                                <div class="mb-4 flex items-start justify-between gap-3">
                                                                    <div>
                                                                        <h3 class="text-lg font-black text-slate-900">
                                                                            Subject Detail</h3>
                                                                        <p class="mt-1 text-sm text-slate-500">
                                                                            {{ $subject->name }} &bull;
                                                                            {{ $subject->code ?: 'No code assigned' }}
                                                                        </p>
                                                                    </div>
                                                                    <button type="button" @click="detailOpen = false"
                                                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                                        <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                            fill="currentColor">
                                                                            <path
                                                                                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>

                                                                @php
                                                                    $subjectDetailStudyRows = $subject->studySchedules
                                                                        ->sortBy(function ($slot) {
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
                                                                                (string) ($slot->day_of_week ?? 'all'),
                                                                            );

                                                                            return sprintf(
                                                                                '%02d-%s-%s',
                                                                                $daySortMap[$dayKey] ?? 99,
                                                                                (string) ($slot->period ?? ''),
                                                                                (string) ($slot->start_time ?? ''),
                                                                            );
                                                                        })
                                                                        ->values();
                                                                    $subjectStudents = $subject->students
                                                                        ->sortBy('name')
                                                                        ->values();
                                                                    $subjectPreviewStudents = $subjectStudents->take(6);
                                                                @endphp

                                                                <div class="grid gap-4 md:grid-cols-2">
                                                                    <div
                                                                        class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                                        <div
                                                                            class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                                                            Overview</div>
                                                                        <div class="mt-3 space-y-3 text-sm">
                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span class="text-slate-500">Subject</span>
                                                                                <span class="font-semibold text-slate-900">
                                                                                    {{ $subject->name }}
                                                                                </span>
                                                                            </div>
                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span class="text-slate-500">Code</span>
                                                                                <span class="font-semibold text-slate-900">
                                                                                    {{ $subject->code ?: '-' }}
                                                                                </span>
                                                                            </div>
                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span class="text-slate-500">Total
                                                                                    Students</span>
                                                                                <span class="font-semibold text-slate-900">
                                                                                    {{ $subjectStudents->count() }}
                                                                                </span>
                                                                            </div>
                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span class="text-slate-500">Status</span>
                                                                                <span
                                                                                    class="font-semibold {{ $subject->is_active ? 'text-emerald-700' : 'text-rose-700' }}">
                                                                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                                                                </span>
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
                                                                            <div
                                                                                class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                                                                Description
                                                                            </div>
                                                                            <p
                                                                                class="mt-2 text-sm leading-6 text-slate-600">
                                                                                {{ $subject->description ?: 'No description available.' }}
                                                                            </p>
                                                                        </div>

                                                                        <div
                                                                            class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <div>
                                                                                    <div
                                                                                        class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                                                                        Total Students</div>
                                                                                    <div
                                                                                        class="mt-1 text-sm text-slate-500">
                                                                                        {{ $subjectStudents->count() }}
                                                                                        enrolled
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mt-3 flex flex-wrap gap-1.5">
                                                                                @forelse ($subjectPreviewStudents as $student)
                                                                                    <span
                                                                                        class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                                                                        {{ $student->name }}
                                                                                    </span>
                                                                                @empty
                                                                                    <span class="text-sm text-slate-400">
                                                                                        No students assigned.
                                                                                    </span>
                                                                                @endforelse
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="rounded-2xl border border-slate-200 bg-white p-4">
                                                                        <div
                                                                            class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                                                                            <div>
                                                                                <div
                                                                                    class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                                                                    Full Schedule</div>
                                                                                <div class="mt-1 text-sm text-slate-500">
                                                                                    {{ $subjectDetailStudyRows->count() }}
                                                                                    slots total
                                                                                </div>
                                                                            </div>
                                                                            <a href="{{ route('admin.time-studies.index', ['tab' => 'subject', 'subject_id' => $subject->id]) }}"
                                                                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">
                                                                                Manage times
                                                                            </a>
                                                                        </div>

                                                                        <div class="mt-4 space-y-3">
                                                                            <div
                                                                                class="max-h-[420px] space-y-2 overflow-y-auto pr-1">
                                                                                @forelse ($subjectDetailStudyRows as $slot)
                                                                                    @php
                                                                                        $slotDayKey = strtolower(
                                                                                            (string) ($slot->day_of_week ??
                                                                                                'all'),
                                                                                        );
                                                                                        $slotDayLabel =
                                                                                            $dayOptions[$slotDayKey] ??
                                                                                            ucfirst($slotDayKey);
                                                                                        $slotPeriodLabel =
                                                                                            $periodOptions[
                                                                                                $slot->period
                                                                                            ] ?? ucfirst($slot->period);
                                                                                        $slotStartTime = \Carbon\Carbon::parse(
                                                                                            $slot->start_time,
                                                                                        )->format('h:i A');
                                                                                        $slotEndTime = \Carbon\Carbon::parse(
                                                                                            $slot->end_time,
                                                                                        )->format('h:i A');
                                                                                    @endphp
                                                                                    <div
                                                                                        class="flex flex-wrap items-center gap-2 rounded-2xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-800">
                                                                                        <span
                                                                                            class="uppercase tracking-wide">
                                                                                            {{ $slotDayLabel }}
                                                                                        </span>
                                                                                        <span
                                                                                            class="text-indigo-300">|</span>
                                                                                        <span>{{ $slotPeriodLabel }}</span>
                                                                                        <span
                                                                                            class="text-indigo-300">|</span>
                                                                                        <span
                                                                                            class="whitespace-nowrap text-slate-700">
                                                                                            {{ $slotStartTime }} ->
                                                                                            {{ $slotEndTime }}
                                                                                        </span>
                                                                                    </div>
                                                                                @empty
                                                                                    <div
                                                                                        class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                                                                                        No study schedule configured.
                                                                                    </div>
                                                                                @endforelse

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div x-show="open" x-cloak
                                                            class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                            aria-modal="true" role="dialog">
                                                            <div class="absolute inset-0 bg-slate-900/50"
                                                                @click="open = false">
                                                            </div>

                                                            <div
                                                                class="relative z-10 w-full max-w-xl rounded-3xl bg-white p-5 shadow-2xl">
                                                                <div class="mb-4 flex items-center justify-between">
                                                                    <h3 class="text-lg font-black text-slate-900">Edit
                                                                        Subject</h3>
                                                                    <button type="button" @click="open = false"
                                                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                                        <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                            fill="currentColor">
                                                                            <path
                                                                                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>

                                                                <form method="POST"
                                                                    action="{{ route('admin.subjects.update', $subject) }}"
                                                                    class="js-edit-form space-y-4"
                                                                    data-subject="{{ $subject->name }}">
                                                                    @csrf
                                                                    @method('PUT')

                                                                    <div>
                                                                        <label for="edit_name_{{ $subject->id }}"
                                                                            class="mb-1 block text-xs font-semibold text-slate-600">Subject
                                                                            Name</label>
                                                                        <input id="edit_name_{{ $subject->id }}"
                                                                            name="name" type="text"
                                                                            value="{{ $subject->name }}"
                                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    </div>

                                                                    <input type="hidden"
                                                                        id="edit_code_{{ $subject->id }}" name="code"
                                                                        value="{{ $subject->code }}">

                                                                    <div>
                                                                        <label
                                                                            class="mb-1 block text-xs font-semibold text-slate-600">Study
                                                                            Times</label>
                                                                        <div
                                                                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                                            Manage this subject schedule from Time
                                                                            Studies.
                                                                            <a href="{{ route('admin.time-studies.index', ['tab' => 'subject', 'subject_id' => $subject->id]) }}"
                                                                                class="font-semibold text-indigo-700 hover:text-indigo-900">Open
                                                                                Time Studies</a>
                                                                        </div>
                                                                    </div>

                                                                    <div>
                                                                        <label for="edit_description_{{ $subject->id }}"
                                                                            class="mb-1 block text-xs font-semibold text-slate-600">Description</label>
                                                                        <textarea id="edit_description_{{ $subject->id }}" name="description" rows="3"
                                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ $subject->description }}</textarea>
                                                                    </div>

                                                                    <label
                                                                        class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5">
                                                                        <span
                                                                            class="text-sm font-semibold text-slate-700">Status</span>
                                                                        <span
                                                                            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                                                                            <input type="checkbox" name="is_active"
                                                                                value="1"
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
                                                    <td colspan="8"
                                                        class="px-4 py-10 text-left text-sm text-slate-500 sm:text-center">
                                                        No subjects found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-5">
                                {{ $subjects->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @php
        $subjectPageData = [
            'success' => session('success'),
        ];
    @endphp
    <script id="admin-subject-data" type="application/json">{!! json_encode($subjectPageData) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/admin/subject.js'])
@endsection
