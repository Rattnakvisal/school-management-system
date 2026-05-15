@extends('layout.admin.navbar')

@section('page')
    @php
        $subjectTotal = max(0, (int) ($stats['total'] ?? 0));
        $studentTotal = max(0, (int) ($stats['students'] ?? 0));

        $showCreateFormOnLoad = old('_form') === 'create_subject';

        $panelClass =
            'subject-reveal rounded-2xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:ring-slate-800';

        $labelClass = 'mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300';

        $inputClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20';

        $moneyInputClass =
            'w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-7 pr-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20';

        $textareaClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20';

        $softBoxClass =
            'rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-800/70 dark:text-slate-300';

        $modalPanelClass =
            'relative z-10 flex max-h-[calc(100vh-2rem)] w-full flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900';
    @endphp

    <div
        class="subject-stage admin-management-page subject-page mx-auto max-w-[1500px] space-y-5 pb-8 text-slate-900 dark:text-slate-100">
        <section class="subject-reveal flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between" style="--sd: 1;">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-950 dark:text-white">Subjects</h1>
                <p class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-slate-500 dark:text-slate-400">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M4 4v15.5" />
                        <path d="M8 4h12v13H8z" />
                    </svg>
                    Total: {{ number_format($subjectTotal) }}
                </p>
            </div>

            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-subject-create'))"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-500/20 transition hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/25">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Add subject
            </button>
        </section>

        @if (session('success'))
            <div class="subject-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="subject-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->subjectCreate->any())
            <div class="subject-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300"
                style="--sd: 2;">
                <div class="font-semibold">Please check the form fields and try again.</div>
            </div>
        @endif

        @if ($errors->subjectUpdate->any())
            <div class="subject-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300"
                style="--sd: 2;">
                <div class="font-semibold">Unable to update subject. Please open Edit and review the fields.</div>
            </div>
        @endif

        <div x-data="{ createOpen: @js($showCreateFormOnLoad) }"
            @open-subject-create.window="
                createOpen = true;
                $nextTick(() => document.getElementById('name')?.focus());
            "
            class="grid gap-6 xl:grid-cols-12">
            {{-- CREATE SUBJECT --}}
            <section x-show="createOpen" x-cloak x-transition.opacity.duration.150ms
                @keydown.escape.window="createOpen = false" @click.self="createOpen = false" role="dialog"
                aria-modal="true" aria-labelledby="create-subject-title"
                class="fixed inset-0 z-[90] flex items-start justify-center overflow-y-auto bg-slate-950/65 px-4 py-6 backdrop-blur-sm sm:py-10"
                style="--sd: 3;">
                <div
                    class="w-full max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl ring-1 ring-slate-950/5 dark:border-slate-700 dark:bg-slate-900 dark:ring-white/10">

                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-700">
                        <div>
                            <h2 id="create-subject-title" class="text-2xl font-black text-slate-950 dark:text-white">
                                Create Subject
                            </h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400">
                                Create a new subject. Manage class and time from Time Studies.
                            </p>
                        </div>

                        <button type="button" @click="createOpen = false" aria-controls="create-subject-form-panel"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-600 text-lg font-black leading-none text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus:ring-4 focus:ring-red-200 dark:focus:ring-red-500/25"
                            aria-label="Close create subject form">
                            &times;
                        </button>
                    </div>

                    <form id="create-subject-form-panel" method="POST" action="{{ route('admin.subjects.store') }}"
                        class="js-create-form">
                        @csrf

                        <input type="hidden" name="_form" value="create_subject">

                        <div class="max-h-[calc(100vh-15rem)] overflow-y-auto px-6 py-5">
                            <div class="grid gap-5 lg:grid-cols-2">

                                <div>
                                    <label for="name" class="{{ $labelClass }}">Subject Name</label>
                                    <input id="name" name="name" type="text" value="{{ old('name') }}"
                                        class="{{ $inputClass }}" placeholder="Mathematics">

                                    @error('name', 'subjectCreate')
                                        <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <input type="hidden" name="code"
                                    value="{{ old('code', 'SUB' . now()->format('His') . strtoupper(substr(md5((string) microtime(true)), 0, 4))) }}">

                                <div>
                                    <label for="tuition_fee" class="{{ $labelClass }}">Tuition Fee</label>

                                    <div class="relative">
                                        <span
                                            class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm font-bold text-slate-400 dark:text-slate-500">
                                            $
                                        </span>

                                        <input id="tuition_fee" name="tuition_fee" type="number" step="0.01"
                                            min="0" value="{{ old('tuition_fee') }}" class="{{ $moneyInputClass }}"
                                            placeholder="0.00">
                                    </div>

                                    @error('tuition_fee', 'subjectCreate')
                                        <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">
                                            {{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="{{ $labelClass }}">Study Times</label>

                                    <div class="{{ $softBoxClass }}">
                                        Study times are now managed in Time Studies.
                                        <a href="{{ route('admin.time-studies.index', ['tab' => 'subject']) }}"
                                            class="font-semibold text-indigo-700 transition hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-indigo-200">
                                            Manage subject times
                                        </a>
                                    </div>
                                </div>

                                <div>
                                    <label for="description" class="{{ $labelClass }}">Description Optional</label>
                                    <textarea id="description" name="description" rows="3" class="{{ $textareaClass }}"
                                        placeholder="Notes about the subject...">{{ old('description') }}</textarea>

                                    @error('description', 'subjectCreate')
                                        <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">
                                            {{ $message }}</p>
                                    @enderror
                                </div>

                                <label
                                    class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Initial
                                        Status</span>

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
                                Create Subject
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            {{-- SUBJECT LIST --}}
            <section class="{{ $panelClass }} xl:col-span-12" style="--sd: 4;">
                <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4 p-5">

                    <form method="GET" action="{{ route('admin.subjects.index') }}"
                        class="flex flex-wrap items-center gap-3">
                        <select name="class_id"
                            class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm outline-none transition hover:bg-slate-50 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <option value="all" {{ $classId === 'all' ? 'selected' : '' }}>Classes</option>
                            @foreach ($classes as $classOption)
                                <option value="{{ $classOption->id }}"
                                    {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                    {{ $classOption->display_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status"
                            class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm outline-none transition hover:bg-slate-50 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Status</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>

                        <input name="q" type="search" value="{{ $search }}" placeholder="Search subjects"
                            class="h-10 min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 hover:bg-slate-50 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 sm:max-w-xs dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:placeholder:text-slate-500">

                        <button type="submit"
                            class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                            Apply
                        </button>

                        <button type="button" @click="filterOpen = true"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 6h16M7 12h10M10 18h4" />
                            </svg>
                            All filters
                        </button>
                    </form>

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
                                    <a href="{{ route('admin.subjects.index') }}"
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

                            <form method="GET" action="{{ route('admin.subjects.index') }}"
                                class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">

                                <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Search</h4>
                                        <input id="q" name="q" type="text" value="{{ $search }}"
                                            placeholder="Search subject, teacher, class, time, or schedule"
                                            class="{{ $inputClass }}">
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Class</h4>
                                        <select name="class_id" class="{{ $inputClass }}">
                                            <option value="all" {{ $classId === 'all' ? 'selected' : '' }}>All Classes
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
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Teacher</h4>
                                        <select name="teacher_id" class="{{ $inputClass }}">
                                            <option value="all" {{ $teacherId === 'all' ? 'selected' : '' }}>All
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
                    @if (
                        $search !== '' ||
                            $classId !== 'all' ||
                            $teacherId !== 'all' ||
                            $status !== 'all' ||
                            $period !== 'all' ||
                            $schedule !== 'all')
                        <div
                            class="flex flex-wrap items-center gap-2 rounded-2xl bg-indigo-50/70 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-indigo-500/10 dark:text-slate-300">
                            <span class="text-indigo-700 dark:text-indigo-300">Active filters:</span>

                            @if ($search !== '')
                                <span
                                    class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                    Search: {{ $search }}
                                </span>
                            @endif

                            @if ($classId !== 'all')
                                <span
                                    class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                    Class selected
                                </span>
                            @endif

                            @if ($teacherId !== 'all')
                                <span
                                    class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                    Teacher selected
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

                            <a href="{{ route('admin.subjects.index') }}"
                                class="ml-auto rounded-full bg-white px-2.5 py-1 text-indigo-700 ring-1 ring-indigo-100 hover:bg-indigo-100 dark:bg-slate-800 dark:text-indigo-300 dark:ring-indigo-400/20 dark:hover:bg-slate-700">
                                Clear
                            </a>
                        </div>
                    @endif

                    {{-- TABLE --}}
                    <div class="min-w-0">
                        <div
                            class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
                            <div class="subject-table-scroller min-h-[520px] max-h-[720px] overflow-auto">
                                <table class="student-roster-table subject-table w-full min-w-[1240px] text-left text-sm">
                                    <thead
                                        class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-[11px] uppercase tracking-wide text-slate-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-500">
                                        <tr>
                                            <th class="w-20 whitespace-nowrap px-3 py-3 font-bold">ID</th>
                                            <th class="min-w-[220px] px-3 py-3 font-bold">Subject</th>
                                            <th class="w-32 whitespace-nowrap px-3 py-3 font-bold">Tuition Fee</th>
                                            <th class="min-w-[240px] px-3 py-3 font-bold">Study Time</th>
                                            <th class="min-w-[150px] px-3 py-3 font-bold">Class</th>
                                            <th class="min-w-[150px] px-3 py-3 font-bold">Teacher</th>
                                            <th class="w-36 whitespace-nowrap px-3 py-3 font-bold">Students Learning</th>
                                            <th class="w-28 whitespace-nowrap px-3 py-3 font-bold">Status</th>
                                            <th class="w-32 whitespace-nowrap px-3 py-3 font-bold">Created</th>
                                            <th class="w-36 whitespace-nowrap px-4 py-3 text-right font-bold">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody
                                        class="divide-y divide-slate-100 bg-white text-slate-700 dark:divide-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                        @forelse ($subjects as $subject)
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

                                                        $dayKey = strtolower((string) ($slot->day_of_week ?? 'all'));

                                                        return sprintf(
                                                            '%02d-%s-%s',
                                                            $daySortMap[$dayKey] ?? 99,
                                                            (string) ($slot->period ?? ''),
                                                            (string) ($slot->start_time ?? ''),
                                                        );
                                                    })
                                                    ->values();

                                                $previewSubjectRows = $subjectStudyRows->take(3);

                                                $slotClasses = $subject->studySchedules
                                                    ->map(fn($slot) => $slot->schoolClass?->display_name)
                                                    ->filter()
                                                    ->unique()
                                                    ->values();

                                                $slotTeachers = $subject->studySchedules
                                                    ->map(fn($slot) => $slot->teacher?->name)
                                                    ->filter()
                                                    ->unique()
                                                    ->values();

                                                $subjectStudents = $subject->relationLoaded('learningStudents')
                                                    ? $subject->getRelation('learningStudents')
                                                    : $subject->students->sortBy('name')->values();

                                                $subjectPreviewStudents = $subjectStudents->take(6);
                                            @endphp

                                            <tr class="align-middle transition hover:bg-indigo-50/40 dark:hover:bg-slate-800/70"
                                                x-data="{ open: false, detailOpen: false, menuOpen: false }">
                                                <td
                                                    class="whitespace-nowrap px-3 py-3 text-xs font-bold tabular-nums text-slate-500 dark:text-slate-400">
                                                    {{ $subject->id }}
                                                </td>

                                                <td class="px-3 py-3">
                                                    <div
                                                        class="whitespace-nowrap font-semibold text-slate-700 dark:text-slate-100">
                                                        {{ $subject->name }}
                                                    </div>

                                                    <div class="text-xs text-slate-400 dark:text-slate-500">
                                                        {{ \Illuminate\Support\Str::limit($subject->description ?: 'No description', 60) }}
                                                    </div>
                                                </td>

                                                <td
                                                    class="whitespace-nowrap px-3 py-3 font-semibold tabular-nums text-slate-800 dark:text-slate-100">
                                                    ${{ number_format((float) ($subject->tuition_fee ?? 0), 2) }}
                                                </td>

                                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                                    @if ($subjectStudyRows->isNotEmpty())
                                                        <div class="space-y-2">
                                                            <div class="flex max-w-sm flex-wrap gap-1.5">
                                                                @foreach ($previewSubjectRows as $slot)
                                                                    <div
                                                                        class="student-study-chip inline-flex items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px] dark:border-indigo-400/20 dark:bg-indigo-500/15">
                                                                        <span
                                                                            class="font-bold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">
                                                                            {{ $periodOptions[$slot->period] ?? ucfirst($slot->period) }}
                                                                        </span>

                                                                        <span
                                                                            class="text-indigo-300 dark:text-indigo-400">|</span>

                                                                        <span
                                                                            class="whitespace-nowrap font-semibold text-slate-700 dark:text-slate-300">
                                                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                                                            ->
                                                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            @if ($subjectStudyRows->count() > $previewSubjectRows->count())
                                                                <div
                                                                    class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">
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
                                                            <span class="text-slate-400 dark:text-slate-500">-</span>
                                                        @endif
                                                    @endif
                                                </td>

                                                <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                    @if ($slotClasses->isNotEmpty())
                                                        <div class="flex flex-wrap gap-1.5">
                                                            @foreach ($slotClasses as $slotClassName)
                                                                <span
                                                                    class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                                    {{ $slotClassName }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        {{ $subject->schoolClass?->display_name ?: 'Unassigned' }}
                                                    @endif
                                                </td>

                                                <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-slate-300">
                                                    @if ($slotTeachers->isNotEmpty())
                                                        <div class="flex flex-wrap gap-1.5">
                                                            @foreach ($slotTeachers as $slotTeacherName)
                                                                <span
                                                                    class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                                    {{ $slotTeacherName }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        {{ $subject->teacher?->name ?: 'Unassigned' }}
                                                    @endif
                                                </td>

                                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                                    {{ $subject->students_learning_count ?? ($subject->students_count ?? 0) }}
                                                </td>

                                                <td class="px-3 py-3">
                                                    @if ($subject->is_active)
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
                                                    {{ $subject->created_at->format('M d, Y') }}
                                                </td>

                                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                                    <div class="relative flex items-center justify-end gap-2"
                                                        @click.outside="menuOpen = false">
                                                        <button type="button" @click="menuOpen = !menuOpen"
                                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-300"
                                                            aria-label="Open subject actions">
                                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"
                                                                aria-hidden="true">
                                                                <path
                                                                    d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                                                            </svg>
                                                        </button>

                                                        <div x-show="menuOpen" x-cloak
                                                            x-transition.opacity.scale.origin.top.right
                                                            class="absolute right-0 top-9 z-30 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 text-left shadow-xl ring-1 ring-slate-900/5 dark:border-slate-700 dark:bg-slate-900 dark:ring-white/10">
                                                            <button @click="detailOpen = true; menuOpen = false"
                                                                type="button"
                                                                class="flex w-full items-center gap-3 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                                                                View detail
                                                            </button>

                                                            <button @click="open = true; menuOpen = false" type="button"
                                                                class="flex w-full items-center gap-3 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-indigo-50 hover:text-indigo-700 dark:text-slate-200 dark:hover:bg-indigo-500/15 dark:hover:text-indigo-300">
                                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    aria-hidden="true">
                                                                    <path d="M12 20h9" />
                                                                    <path
                                                                        d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                                </svg>
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
                                                                    class="flex w-full items-center gap-3 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                                                                    <svg class="h-4 w-4" viewBox="0 0 24 24"
                                                                        fill="none" stroke="currentColor"
                                                                        stroke-width="2" stroke-linecap="round"
                                                                        stroke-linejoin="round" aria-hidden="true">
                                                                        <path d="M20 6 9 17l-5-5" />
                                                                    </svg>
                                                                    {{ $subject->is_active ? 'Set inactive' : 'Set active' }}
                                                                </button>
                                                            </form>

                                                            <form method="POST"
                                                                action="{{ route('admin.subjects.destroy', $subject) }}"
                                                                class="js-delete-form"
                                                                data-subject="{{ $subject->name }}">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit"
                                                                    class="flex w-full items-center gap-3 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-500/15">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>
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
                                                                        Subject Detail</h3>
                                                                    <p
                                                                        class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                                                        {{ $subject->name }} &bull;
                                                                        {{ $subject->code ?: 'No code assigned' }}
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
                                                                                    class="text-slate-500 dark:text-slate-400">Subject</span>
                                                                                <span
                                                                                    class="font-semibold text-slate-900 dark:text-white">{{ $subject->name }}</span>
                                                                            </div>

                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span
                                                                                    class="text-slate-500 dark:text-slate-400">Code</span>
                                                                                <span
                                                                                    class="font-semibold text-slate-900 dark:text-white">{{ $subject->code ?: '-' }}</span>
                                                                            </div>

                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span
                                                                                    class="text-slate-500 dark:text-slate-400">Tuition
                                                                                    Fee</span>
                                                                                <span
                                                                                    class="font-semibold text-slate-900 dark:text-white">
                                                                                    ${{ number_format((float) ($subject->tuition_fee ?? 0), 2) }}
                                                                                </span>
                                                                            </div>

                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span
                                                                                    class="text-slate-500 dark:text-slate-400">Total
                                                                                    Students</span>
                                                                                <span
                                                                                    class="font-semibold text-slate-900 dark:text-white">{{ $subjectStudents->count() }}</span>
                                                                            </div>

                                                                            <div
                                                                                class="flex items-center justify-between gap-3">
                                                                                <span
                                                                                    class="text-slate-500 dark:text-slate-400">Status</span>
                                                                                <span
                                                                                    class="font-semibold {{ $subject->is_active ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                                                                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                                                                </span>
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                                                                            <div
                                                                                class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                                                                Description
                                                                            </div>

                                                                            <p
                                                                                class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                                                                {{ $subject->description ?: 'No description available.' }}
                                                                            </p>
                                                                        </div>

                                                                        <div
                                                                            class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                                                                            <div
                                                                                class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                                                                Total Students
                                                                            </div>

                                                                            <div
                                                                                class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                                                                {{ $subjectStudents->count() }} enrolled
                                                                            </div>

                                                                            <div class="mt-3 flex flex-wrap gap-1.5">
                                                                                @forelse ($subjectPreviewStudents as $student)
                                                                                    <span
                                                                                        class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300">
                                                                                        {{ $student->name }}
                                                                                    </span>
                                                                                @empty
                                                                                    <span
                                                                                        class="text-sm text-slate-400 dark:text-slate-500">
                                                                                        No students assigned.
                                                                                    </span>
                                                                                @endforelse
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
                                                                                    {{ $subjectStudyRows->count() }} slots
                                                                                    total
                                                                                </div>
                                                                            </div>

                                                                            <a href="{{ route('admin.time-studies.index', ['tab' => 'subject', 'subject_id' => $subject->id]) }}"
                                                                                class="text-xs font-semibold text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                                                Manage times
                                                                            </a>
                                                                        </div>

                                                                        <div
                                                                            class="mt-4 max-h-[420px] space-y-2 overflow-y-auto pr-1">
                                                                            @forelse ($subjectStudyRows as $slot)
                                                                                @php
                                                                                    $slotDayKey = strtolower(
                                                                                        (string) ($slot->day_of_week ??
                                                                                            'all'),
                                                                                    );
                                                                                    $slotDayLabel =
                                                                                        $dayOptions[$slotDayKey] ??
                                                                                        ucfirst($slotDayKey);
                                                                                    $slotPeriodLabel =
                                                                                        $periodOptions[$slot->period] ??
                                                                                        ucfirst($slot->period);
                                                                                    $slotStartTime = \Carbon\Carbon::parse(
                                                                                        $slot->start_time,
                                                                                    )->format('h:i A');
                                                                                    $slotEndTime = \Carbon\Carbon::parse(
                                                                                        $slot->end_time,
                                                                                    )->format('h:i A');
                                                                                @endphp

                                                                                <div
                                                                                    class="student-study-chip flex flex-wrap items-center gap-2 rounded-2xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-800 dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-300">
                                                                                    <span
                                                                                        class="uppercase tracking-wide">{{ $slotDayLabel }}</span>
                                                                                    <span
                                                                                        class="text-indigo-300 dark:text-indigo-400">|</span>
                                                                                    <span>{{ $slotPeriodLabel }}</span>
                                                                                    <span
                                                                                        class="text-indigo-300 dark:text-indigo-400">|</span>
                                                                                    <span
                                                                                        class="whitespace-nowrap text-slate-700 dark:text-slate-300">
                                                                                        {{ $slotStartTime }} ->
                                                                                        {{ $slotEndTime }}
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
                                                    <div x-show="open" x-cloak
                                                        class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                        aria-modal="true" role="dialog">
                                                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                                                            @click="open = false"></div>

                                                        <div class="{{ $modalPanelClass }} max-w-xl">
                                                            <div
                                                                class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                                                                <h3
                                                                    class="text-lg font-black text-slate-950 dark:text-white">
                                                                    Edit Subject</h3>

                                                                <button type="button" @click="open = false"
                                                                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                                                                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                        fill="currentColor">
                                                                        <path
                                                                            d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <form method="POST"
                                                                action="{{ route('admin.subjects.update', $subject) }}"
                                                                class="js-edit-form flex min-h-0 flex-1 flex-col"
                                                                data-subject="{{ $subject->name }}">
                                                                @csrf
                                                                @method('PUT')

                                                                <div
                                                                    class="flex-1 space-y-4 overflow-y-auto overscroll-contain px-5 pb-4 pt-4">
                                                                    <div>
                                                                        <label for="edit_name_{{ $subject->id }}"
                                                                            class="{{ $labelClass }}">Subject
                                                                            Name</label>
                                                                        <input id="edit_name_{{ $subject->id }}"
                                                                            name="name" type="text"
                                                                            value="{{ $subject->name }}"
                                                                            class="{{ $inputClass }}">
                                                                    </div>

                                                                    <input type="hidden"
                                                                        id="edit_code_{{ $subject->id }}"
                                                                        name="code" value="{{ $subject->code }}">

                                                                    <div>
                                                                        <label for="edit_tuition_fee_{{ $subject->id }}"
                                                                            class="{{ $labelClass }}">Tuition
                                                                            Fee</label>

                                                                        <div class="relative">
                                                                            <span
                                                                                class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm font-bold text-slate-400 dark:text-slate-500">
                                                                                $
                                                                            </span>

                                                                            <input
                                                                                id="edit_tuition_fee_{{ $subject->id }}"
                                                                                name="tuition_fee" type="number"
                                                                                step="0.01" min="0"
                                                                                value="{{ $subject->tuition_fee }}"
                                                                                class="{{ $moneyInputClass }}">
                                                                        </div>
                                                                    </div>

                                                                    <div>
                                                                        <label class="{{ $labelClass }}">Study
                                                                            Times</label>

                                                                        <div class="{{ $softBoxClass }}">
                                                                            Manage this subject schedule from Time Studies.
                                                                            <a href="{{ route('admin.time-studies.index', ['tab' => 'subject', 'subject_id' => $subject->id]) }}"
                                                                                class="font-semibold text-indigo-700 transition hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                                                Open Time Studies
                                                                            </a>
                                                                        </div>
                                                                    </div>

                                                                    <div>
                                                                        <label for="edit_description_{{ $subject->id }}"
                                                                            class="{{ $labelClass }}">Description</label>
                                                                        <textarea id="edit_description_{{ $subject->id }}" name="description" rows="3"
                                                                            class="{{ $textareaClass }}">{{ $subject->description }}</textarea>
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
                                                                                {{ $subject->is_active ? 'checked' : '' }}>
                                                                            Active
                                                                        </span>
                                                                    </label>
                                                                </div>

                                                                <div
                                                                    class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4 dark:border-slate-700">
                                                                    <button type="button" @click="open = false"
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
                                                    class="px-4 py-10 text-left text-sm text-slate-500 sm:text-center dark:text-slate-400">
                                                    No subjects found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div
                            class="subject-pagination -mx-5 -mb-5 border-t border-slate-100 bg-slate-50/70 px-4 py-3 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                            {{ $subjects->links() }}
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

    <script id="admin-subject-data" type="application/json">{!! json_encode($subjectPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/admin/subject.js'])
@endsection
