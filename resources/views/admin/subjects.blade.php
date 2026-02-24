@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="subject-stage space-y-6">
        <section class="subject-reveal admin-page-header overflow-hidden" style="--sd: 1;">
            <div class="flex flex-wrap items-center justify-between gap-5">
                <div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Subject Management</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">Create subjects and manage schedules in Time Studies.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                    <span class="admin-page-stat">Total: {{ $stats['total'] }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Active:
                        {{ $stats['active'] }}</span>
                    <span class="admin-page-stat admin-page-stat--sky">Assigned:
                        {{ $stats['assigned'] }}</span>
                    <span class="admin-page-stat admin-page-stat--amber">With Teacher:
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
                class="subject-reveal subject-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-5"
                style="--sd: 3;">
                <h2 class="text-lg font-black text-slate-900">Create Subject</h2>
                <p class="mt-1 text-xs text-slate-500">Create a new subject. Manage class and time from Time Studies.</p>

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
                            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300" {{ old('is_active', '1') ? 'checked' : '' }}>
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
                class="subject-reveal subject-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-7"
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

                    <div x-show="filterOpen" x-cloak x-transition.opacity
                        class="fixed inset-0 z-[80] bg-slate-900/40" @click="filterOpen = false"></div>

                    <div class="grid gap-4">
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
                                        <a href="{{ route('admin.subjects.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                                            Clear All
                                        </a>
                                        <button type="button" @click="filterOpen = false"
                                            class="text-2xl font-bold leading-none text-slate-700 hover:text-slate-900" aria-label="Close filters">
                                            &times;
                                        </button>
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('admin.subjects.index') }}" class="flex min-h-0 flex-1 flex-col"
                                    @submit="filterOpen = false">
                                    <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                            <input id="q" name="q" type="text" value="{{ $search }}"
                                                placeholder="Search subject, teacher, class, time, or schedule"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        </section>
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Class</h4>
                                            <select name="class_id"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $classId === 'all' ? 'selected' : '' }}>All Classes</option>
                                                @foreach ($classes as $classOption)
                                                    <option value="{{ $classOption->id }}" {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                                        {{ $classOption->display_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Teacher</h4>
                                            <select name="teacher_id"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $teacherId === 'all' ? 'selected' : '' }}>All Teachers</option>
                                                @foreach ($teachers as $teacherOption)
                                                    <option value="{{ $teacherOption->id }}" {{ $teacherId === (string) $teacherOption->id ? 'selected' : '' }}>
                                                        {{ $teacherOption->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                            <select name="status"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </section>
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Period</h4>
                                            <select name="period"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Periods</option>
                                                @foreach ($periodOptions as $periodKey => $periodLabel)
                                                    <option value="{{ $periodKey }}" {{ $period === $periodKey ? 'selected' : '' }}>
                                                        {{ $periodLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Schedule</h4>
                                            <select name="schedule"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" {{ $schedule === 'all' ? 'selected' : '' }}>All Schedules</option>
                                                <option value="with_schedule" {{ $schedule === 'with_schedule' ? 'selected' : '' }}>With Schedule</option>
                                                <option value="without_schedule" {{ $schedule === 'without_schedule' ? 'selected' : '' }}>Without Schedule</option>
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
                    </div>

                <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200">
                    <div class="max-h-[560px] overflow-auto">
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
                                    <th class="px-3 py-3 font-semibold">Created</th>
                                    <th class="px-3 py-3 font-semibold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($subjects as $subject)
                                    <tr class="align-top hover:bg-slate-50/80" x-data="{ open: false }">
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
                                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
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
                                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                                            {{ $slotTeacherName }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ $subject->teacher?->name ?: 'Unassigned' }}
                                            @endif
                                        </td>
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
                                                    class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                    Edit
                                                </button>

                                                <form method="POST" action="{{ route('admin.subjects.status', $subject) }}"
                                                    class="js-status-form" data-subject="{{ $subject->name }}"
                                                    data-action="{{ $subject->is_active ? 'set inactive' : 'set active' }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="whitespace-nowrap rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $subject->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                        {{ $subject->is_active ? 'Set Inactive' : 'Set Active' }}
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                                                    class="js-delete-form" data-subject="{{ $subject->name }}">
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

                                                        <input type="hidden" id="edit_code_{{ $subject->id }}" name="code"
                                                            value="{{ $subject->code }}">

                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-slate-600">Study
                                                                Times</label>
                                                            <div
                                                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                                Manage this subject schedule from Time Studies.
                                                                <a href="{{ route('admin.time-studies.index', ['tab' => 'subject', 'subject_id' => $subject->id]) }}"
                                                                    class="font-semibold text-indigo-700 hover:text-indigo-900">Open
                                                                    Time Studies</a>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label for="edit_description_{{ $subject->id }}"
                                                                class="mb-1 block text-xs font-semibold text-slate-600">Description</label>
                                                            <textarea id="edit_description_{{ $subject->id }}"
                                                                name="description" rows="3"
                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ $subject->description }}</textarea>
                                                        </div>

                                                        <label
                                                            class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5">
                                                            <span class="text-sm font-semibold text-slate-700">Status</span>
                                                            <span
                                                                class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                                                                <input type="checkbox" name="is_active" value="1"
                                                                    class="h-4 w-4 rounded border-slate-300" {{ $subject->is_active ? 'checked' : '' }}>
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
                                        <td colspan="8" class="px-3 py-10 text-center text-sm text-slate-500">
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
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

                container.addEventListener('click', function (event) {
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
                button.addEventListener('click', function () {
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
                    form.addEventListener('submit', function (event) {
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
