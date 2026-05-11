@extends('layout.students.navbar')

@section('page')
    @php
        $statusStyles = [
            'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
            'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'rejected' => 'border-red-200 bg-red-50 text-red-700',
        ];

        $formValues = $formDefaults ?? [];
        $isEditing = isset($editingRequest) && $editingRequest;

        $teacherRecipients = collect($teacherRecipients ?? []);
        $lawRequests = $lawRequests ?? collect();

        $lawRequestTotal = $lawRequests->count();
        $lawRequestPending = $lawRequests->where('status', 'pending')->count();
        $lawRequestApproved = $lawRequests->where('status', 'approved')->count();

        $studentStatPanelClass =
            'rounded-[26px] border border-white/80 bg-white/90 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.55)] ring-1 ring-slate-200 backdrop-blur';

        $selectedSubjectId = (string) ($formValues['subject_id'] ?? '');

        $selectedTimeKeys = array_values(
            array_unique(array_filter(array_map('strval', (array) ($formValues['subject_time_keys'] ?? [])))),
        );

        $subjectTimeMap = $subjectTimeOptionsBySubject ?? [];
        $initialTimeOptions = $selectedSubjectId !== '' ? $subjectTimeMap[$selectedSubjectId] ?? [] : [];

        $showCreateFormOnLoad = $errors->any() || $isEditing;
    @endphp

    <div class="student-stage space-y-6 overflow-x-hidden">
        {{-- Page Header --}}
        <section class="admin-page-header" style="--sd: 1;">
            <div class="admin-page-header__main flex flex-wrap items-start gap-4">
                <div class="admin-page-header__intro space-y-2">
                    <div class="admin-page-header__title-row flex items-start gap-3">
                        <span class="admin-page-header__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7Z" />
                                <path d="M14 2v5h5" />
                                <path d="M9 13h6" />
                                <path d="M9 17h4" />
                            </svg>
                        </span>

                        <div>
                            <div class="admin-page-header__eyebrow">Student Portal</div>
                            <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">
                                Law Requests
                            </h1>
                        </div>
                    </div>

                    <p class="admin-page-subtitle text-sm">
                        Connect your request to a real subject schedule so your teacher sees the exact class and time.
                    </p>

                    <p class="text-xs font-semibold text-slate-600">
                        Home Class:
                        <span class="text-slate-900">{{ $classLabel ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>
        </section>
        {{-- Flash Messages --}}
        @if (session('success'))
            <div
                class="js-inline-flash rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div
                class="js-inline-flash rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="js-inline-flash rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="js-inline-flash rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            {{-- Create / Edit Request --}}
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
                class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--sd: 3;">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">
                            {{ $isEditing ? 'Edit Request' : 'Create Request' }}
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $isEditing ? 'Update your pending request and save changes.' : 'Choose subject, class time, and send your request to teacher.' }}
                        </p>
                    </div>

                    <button type="button" @click="createOpen = !createOpen"
                        :aria-expanded="(createOpen || isDesktop).toString()" aria-controls="create-law-request-form-panel"
                        class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 shadow-sm hover:bg-indigo-100 xl:hidden">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" x-show="!(createOpen || isDesktop)"></path>
                            <path d="M5 12h14" x-show="createOpen || isDesktop"></path>
                        </svg>

                        <span x-show="!(createOpen || isDesktop)">Create Request</span>
                        <span x-show="createOpen || isDesktop">Hide Form</span>
                    </button>
                </div>

                <form id="create-law-request-form-panel" method="POST"
                    action="{{ $isEditing ? route('student.law-requests.update', $editingRequest) : route('student.law-requests.store') }}"
                    class="js-student-law-request-submit-form mt-5 space-y-4" x-show="createOpen || isDesktop" x-cloak
                    x-transition.opacity.duration.150ms>
                    @csrf

                    @if ($isEditing)
                        @method('PUT')
                    @endif

                    <input type="hidden" name="_form" value="create_law_request">

                    <div>
                        <label for="law_type" class="mb-1 block text-xs font-semibold text-slate-600">
                            Law Type
                        </label>

                        <select id="law_type" name="law_type"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @foreach ($lawTypes as $typeKey => $typeLabel)
                                <option value="{{ $typeKey }}" @selected((string) ($formValues['law_type'] ?? '') === (string) $typeKey)>
                                    {{ $typeLabel }}
                                </option>
                            @endforeach
                        </select>

                        @error('law_type')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subject" class="mb-1 block text-xs font-semibold text-slate-600">
                            Subject
                        </label>

                        <select id="subject" name="subject_id"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @forelse ($subjectOptions ?? [] as $subjectOption)
                                @php
                                    $className = trim((string) ($subjectOption->class_name ?? ''));
                                    $classSection = trim((string) ($subjectOption->class_section ?? ''));

                                    $classDisplay =
                                        $className !== ''
                                            ? $className . ($classSection !== '' ? ' - ' . $classSection : '')
                                            : '';
                                @endphp

                                <option value="{{ $subjectOption->id }}" @selected($selectedSubjectId === (string) $subjectOption->id)>
                                    {{ $subjectOption->name }}
                                    {{ !empty($subjectOption->code) ? ' (' . $subjectOption->code . ')' : '' }}
                                    {{ $classDisplay !== '' ? ' | ' . $classDisplay : '' }}
                                </option>
                            @empty
                                <option value="">No subject available</option>
                            @endforelse
                        </select>

                        @error('subject_id')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-1 block text-xs font-semibold text-slate-600">
                            Subject Time
                        </div>

                        <div id="subject_time_options" class="grid gap-2 sm:grid-cols-2">
                            @forelse ($initialTimeOptions as $timeOption)
                                @php
                                    $timeKey = (string) ($timeOption['key'] ?? '');
                                    $timeLabel = (string) ($timeOption['label'] ?? '');
                                    $isChecked = in_array($timeKey, $selectedTimeKeys, true);
                                @endphp

                                <label
                                    class="js-time-card flex cursor-pointer items-start gap-3 rounded-xl border px-3 py-3 text-sm transition hover:border-indigo-300 hover:bg-indigo-50/40 {{ $isChecked ? 'border-indigo-300 bg-indigo-50' : 'border-slate-200 bg-white' }}"
                                    data-day-of-week="{{ (string) ($timeOption['day_of_week'] ?? '') }}"
                                    data-time-key="{{ $timeKey }}">
                                    <input type="checkbox" name="subject_time_keys[]" value="{{ $timeKey }}"
                                        data-time-key="{{ $timeKey }}"
                                        data-day-of-week="{{ (string) ($timeOption['day_of_week'] ?? '') }}"
                                        @checked($isChecked)
                                        class="js-time-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">

                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-800">
                                            {{ $timeLabel }}
                                        </div>

                                        <div class="mt-1 text-xs font-semibold text-slate-500">
                                            Teacher:
                                            {{ !empty($timeOption['teacher_name']) ? $timeOption['teacher_name'] : 'Not assigned yet' }}
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div
                                    class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-500">
                                    No time available
                                </div>
                            @endforelse
                        </div>

                        @error('subject_time_keys')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="mt-1 text-xs font-medium text-slate-400">
                            Choose one class time, or select all class times for this subject.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="requested_for" class="mb-1 block text-xs font-semibold text-slate-600">
                                From Date
                            </label>

                            <input id="requested_for" type="date" name="requested_for"
                                value="{{ (string) ($formValues['requested_for'] ?? now()->toDateString()) }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">

                            @error('requested_for')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="requested_until" class="mb-1 block text-xs font-semibold text-slate-600">
                                Until Date
                            </label>

                            <input id="requested_until" type="date" name="requested_until"
                                value="{{ (string) ($formValues['requested_until'] ?? ($formValues['requested_for'] ?? now()->toDateString())) }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">

                            @error('requested_until')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="reason" class="mb-1 block text-xs font-semibold text-slate-600">
                            Reason
                        </label>

                        <textarea id="reason" name="reason" rows="5" maxlength="5000" required
                            class="w-full resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Explain why you need an attendance law request...">{{ (string) ($formValues['reason'] ?? '') }}</textarea>

                        @error('reason')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Teachers Who Will Be Notified
                        </div>

                        @if ($teacherRecipients->isNotEmpty())
                            <div id="teacher_recipient_list" class="mt-3 flex flex-wrap gap-2">
                                @foreach ($teacherRecipients as $teacherRecipient)
                                    <span
                                        class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                        {{ $teacherRecipient->name }}
                                    </span>
                                @endforeach
                            </div>

                            <div id="teacher_recipient_empty" class="mt-2 hidden text-sm text-slate-500">
                                No teacher is assigned to this subject time yet. Your request will still be saved.
                            </div>
                        @else
                            <div id="teacher_recipient_list" class="mt-3 hidden flex-wrap gap-2"></div>

                            <div id="teacher_recipient_empty" class="mt-2 text-sm text-slate-500">
                                No teacher is assigned to this subject time yet. Your request will still be saved.
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <button type="submit"
                            class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                            {{ $isEditing ? 'Update Law Request' : 'Submit Law Request' }}
                        </button>

                        @if ($isEditing)
                            <a href="{{ route('student.law-requests.index') }}"
                                class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 sm:w-auto">
                                Cancel
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            {{-- Request History --}}
            <section
                class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--sd: 3;">
                <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-black text-slate-900">Request History</h2>
                        <button type="button" @click="filterOpen = true"
                            class="inline-flex min-w-[150px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                            </svg>
                            Filters
                        </button>
                    </div>

                    <div x-show="filterOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[80] bg-slate-900/40"
                        @click="filterOpen = false">
                    </div>

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
                                        <a href="{{ route('student.law-requests.index') }}"
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

                                <form method="GET" action="{{ route('student.law-requests.index') }}"
                                    class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">

                                    <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Search</h4>

                                            <input id="law_q" name="q" type="text"
                                                value="{{ request('q', '') }}"
                                                placeholder="Search type, subject, teacher, or status"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        </section>

                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Status</h4>

                                            <select name="status"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" @selected(request('status', 'all') === 'all')>All</option>
                                                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                                                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                                                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
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
                                <div class="student-table-scroller max-h-[720px] overflow-auto">
                                    <table class="admin-table w-full min-w-[1280px] whitespace-nowrap text-left text-sm">
                                        <thead
                                            class="admin-table-head sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                            <tr>
                                                <th class="px-3 py-3 font-semibold">Type / Subject</th>
                                                <th class="px-3 py-3 font-semibold">Teacher</th>
                                                <th class="whitespace-nowrap px-3 py-3 font-semibold">From Date</th>
                                                <th class="whitespace-nowrap px-3 py-3 font-semibold">Until Date</th>
                                                <th class="px-3 py-3 font-semibold">Status</th>
                                                <th class="whitespace-nowrap px-3 py-3 font-semibold">Submitted</th>
                                                <th class="px-3 py-3 font-semibold text-right">Action</th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-slate-100 bg-white">
                                            @forelse ($lawRequests as $lawRequest)
                                                @php
                                                    $statusKey = strtolower(
                                                        (string) ($lawRequest->status ?? 'pending'),
                                                    );

                                                    $statusClass =
                                                        $statusStyles[$statusKey] ??
                                                        'border-slate-200 bg-slate-50 text-slate-700';

                                                    $typeLabel =
                                                        $lawTypes[$lawRequest->law_type] ??
                                                        ucfirst(str_replace('_', ' ', (string) $lawRequest->law_type));

                                                    $canManage = $statusKey === 'pending';

                                                    $subjectText = trim((string) ($lawRequest->subject ?? ''));
                                                    $subjectTimeText = trim((string) ($lawRequest->subject_time ?? ''));

                                                    $subjectTimeParts = array_values(
                                                        array_filter(
                                                            array_map('trim', explode(',', $subjectTimeText)),
                                                            fn($value) => $value !== '',
                                                        ),
                                                    );

                                                    $requestedForText = $lawRequest->requested_for
                                                        ? $lawRequest->requested_for->format('M d, Y')
                                                        : 'N/A';

                                                    $requestedUntilText = $lawRequest->requested_until
                                                        ? $lawRequest->requested_until->format('M d, Y')
                                                        : '';

                                                    $requestedUntilDisplay =
                                                        $requestedUntilText !== ''
                                                            ? $requestedUntilText
                                                            : $requestedForText;

                                                    $editSubjectId =
                                                        (string) ((int) ($lawRequest->subject_id ?? 0) ?: '');

                                                    $hasEditSubject =
                                                        $editSubjectId !== '' &&
                                                        collect($subjectOptions ?? [])->contains(
                                                            fn($subjectOption) => (string) ($subjectOption->id ??
                                                                '') === $editSubjectId,
                                                        );

                                                    if (!$hasEditSubject && $subjectText !== '') {
                                                        $matchedSubject = collect($subjectOptions ?? [])->first(
                                                            function ($subjectOption) use ($subjectText) {
                                                                return strcasecmp(
                                                                    trim((string) ($subjectOption->name ?? '')),
                                                                    $subjectText,
                                                                ) === 0;
                                                            },
                                                        );

                                                        $editSubjectId = $matchedSubject
                                                            ? (string) $matchedSubject->id
                                                            : '';
                                                    }

                                                    if ($editSubjectId === '') {
                                                        $firstSubject = collect($subjectOptions ?? [])->first();
                                                        $editSubjectId = $firstSubject
                                                            ? (string) $firstSubject->id
                                                            : '';
                                                    }

                                                    $editTimeOptions =
                                                        $editSubjectId !== ''
                                                            ? $subjectTimeMap[$editSubjectId] ?? []
                                                            : [];

                                                    $editTimeKeys = [];

                                                    foreach ($subjectTimeParts as $subjectTimePart) {
                                                        foreach ($editTimeOptions as $timeOption) {
                                                            $timeLabel = trim((string) ($timeOption['label'] ?? ''));
                                                            $timeKey = trim((string) ($timeOption['key'] ?? ''));

                                                            if (
                                                                strcasecmp($timeLabel, $subjectTimePart) === 0 ||
                                                                strcasecmp($timeKey, $subjectTimePart) === 0
                                                            ) {
                                                                $editTimeKeys[] = $timeKey;
                                                            }
                                                        }
                                                    }

                                                    $editTimeKeys = array_values(
                                                        array_unique(
                                                            array_filter($editTimeKeys, fn($key) => $key !== ''),
                                                        ),
                                                    );
                                                @endphp

                                                <tr x-data="{ editOpen: false }"
                                                    class="align-top {{ $isEditing && (int) ($editingRequest->id ?? 0) === (int) $lawRequest->id ? 'bg-indigo-50/40' : 'hover:bg-slate-50/80' }}">
                                                    <td class="px-3 py-3">
                                                        <div class="max-w-[260px]">
                                                            <div class="font-semibold text-slate-900">
                                                                {{ $typeLabel }}
                                                            </div>

                                                            <div class="mt-1 font-semibold text-slate-700">
                                                                {{ $subjectText !== '' ? $subjectText : 'N/A' }}
                                                            </div>

                                                            <div class="mt-1 truncate text-xs text-slate-500">
                                                                {{ $lawRequest->reason }}
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="px-3 py-3">
                                                        <div class="max-w-[210px] text-xs font-semibold text-sky-600">
                                                            Teacher: {{ $lawRequest->teacher?->name ?: 'Not assigned' }}
                                                        </div>
                                                    </td>

                                                    <td class="whitespace-nowrap px-3 py-3 text-slate-700">
                                                        <div class="font-semibold text-slate-900">
                                                            {{ $requestedForText }}
                                                        </div>

                                                        @if ($subjectTimeParts !== [])
                                                            <div class="mt-2 flex max-w-[330px] flex-wrap gap-1.5">
                                                                @foreach ($subjectTimeParts as $subjectTimePart)
                                                                    <span
                                                                        class="inline-flex rounded-full border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700">
                                                                        {{ $subjectTimePart }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </td>

                                                    <td class="whitespace-nowrap px-3 py-3 text-slate-700">
                                                        <div class="font-semibold text-slate-900">
                                                            {{ $requestedUntilDisplay }}
                                                        </div>
                                                    </td>

                                                    <td class="whitespace-nowrap px-3 py-3">
                                                        <span
                                                            class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                            {{ ucfirst($statusKey) }}
                                                        </span>
                                                    </td>

                                                    <td class="whitespace-nowrap px-3 py-3 text-slate-700">
                                                        {{ $lawRequest->created_at?->format('M d, Y h:i A') }}
                                                    </td>

                                                    <td class="whitespace-nowrap px-3 py-3">
                                                        @if ($canManage)
                                                            <div class="flex flex-nowrap items-center justify-end gap-2">
                                                                <button type="button" @click="editOpen = true"
                                                                    class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                                    Edit
                                                                </button>

                                                                <form method="POST"
                                                                    action="{{ route('student.law-requests.destroy', $lawRequest) }}"
                                                                    class="js-student-law-request-delete-form"
                                                                    data-request-label="{{ $typeLabel }} for {{ $requestedForText }}">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button type="submit"
                                                                        class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                                        Remove
                                                                    </button>
                                                                </form>
                                                            </div>

                                                            {{-- Edit Modal --}}
                                                            <div x-show="editOpen" x-cloak x-transition.opacity
                                                                class="fixed inset-0 z-[80] bg-slate-900/40"
                                                                @click="editOpen = false"></div>

                                                            <div x-show="editOpen" x-cloak x-transition
                                                                class="fixed inset-x-3 top-6 z-[81] mx-auto max-h-[calc(100vh-3rem)] w-full max-w-3xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl sm:top-10">
                                                                <form method="POST"
                                                                    action="{{ route('student.law-requests.update', $lawRequest) }}"
                                                                    class="js-student-law-request-edit-form flex max-h-[calc(100vh-3rem)] flex-col"
                                                                    data-request-label="{{ $subjectText !== '' ? $subjectText : 'Request #' . $lawRequest->id }}">
                                                                    @csrf
                                                                    @method('PUT')

                                                                    <div
                                                                        class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                                                                        <div class="min-w-0">
                                                                            <h3
                                                                                class="truncate text-2xl font-black text-slate-900">
                                                                                Edit Request
                                                                            </h3>

                                                                            <p class="mt-2 text-sm text-slate-500">
                                                                                Update your pending law request details.
                                                                            </p>
                                                                        </div>

                                                                        <button type="button" @click="editOpen = false"
                                                                            class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-slate-200 text-xl font-bold text-slate-600 hover:bg-slate-50">
                                                                            &times;
                                                                        </button>
                                                                    </div>

                                                                    <div
                                                                        class="min-h-0 flex-1 space-y-5 overflow-y-auto px-6 py-5">
                                                                        <div class="space-y-2">
                                                                            <label
                                                                                for="edit_student_law_type_{{ $lawRequest->id }}"
                                                                                class="text-sm font-semibold text-slate-700">
                                                                                Law Type
                                                                            </label>

                                                                            <select
                                                                                id="edit_student_law_type_{{ $lawRequest->id }}"
                                                                                name="law_type"
                                                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                @foreach ($lawTypes as $typeKey => $typeLabel)
                                                                                    <option value="{{ $typeKey }}"
                                                                                        @selected((string) ($lawRequest->law_type ?? '') === (string) $typeKey)>
                                                                                        {{ $typeLabel }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="space-y-2">
                                                                            <label
                                                                                for="edit_student_subject_{{ $lawRequest->id }}"
                                                                                class="text-sm font-semibold text-slate-700">
                                                                                Subject
                                                                            </label>

                                                                            <select
                                                                                id="edit_student_subject_{{ $lawRequest->id }}"
                                                                                name="subject_id"
                                                                                class="js-edit-student-law-subject w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                                data-selected-time-keys='@json($editTimeKeys)'>
                                                                                @forelse ($subjectOptions ?? [] as $subjectOption)
                                                                                    @php
                                                                                        $className = trim(
                                                                                            (string) ($subjectOption->class_name ??
                                                                                                ''),
                                                                                        );
                                                                                        $classSection = trim(
                                                                                            (string) ($subjectOption->class_section ??
                                                                                                ''),
                                                                                        );

                                                                                        $classDisplay =
                                                                                            $className !== ''
                                                                                                ? $className .
                                                                                                    ($classSection !==
                                                                                                    ''
                                                                                                        ? ' - ' .
                                                                                                            $classSection
                                                                                                        : '')
                                                                                                : '';
                                                                                    @endphp

                                                                                    <option
                                                                                        value="{{ $subjectOption->id }}"
                                                                                        @selected($editSubjectId === (string) $subjectOption->id)>
                                                                                        {{ $subjectOption->name }}
                                                                                        {{ !empty($subjectOption->code) ? ' (' . $subjectOption->code . ')' : '' }}
                                                                                        {{ $classDisplay !== '' ? ' | ' . $classDisplay : '' }}
                                                                                    </option>
                                                                                @empty
                                                                                    <option value="">No subject
                                                                                        available
                                                                                    </option>
                                                                                @endforelse
                                                                            </select>
                                                                        </div>

                                                                        <div class="space-y-2">
                                                                            <div
                                                                                class="text-sm font-semibold text-slate-700">
                                                                                Subject Time
                                                                            </div>

                                                                            <div id="edit_student_subject_time_options_{{ $lawRequest->id }}"
                                                                                class="js-edit-student-law-time-options grid gap-2 sm:grid-cols-2">
                                                                            </div>

                                                                            <p class="text-xs font-medium text-slate-400">
                                                                                Choose one class time, or select All to
                                                                                include
                                                                                every
                                                                                class time for this subject.
                                                                            </p>
                                                                        </div>

                                                                        <div class="grid gap-5 sm:grid-cols-2">
                                                                            <div class="space-y-2">
                                                                                <label
                                                                                    for="edit_student_requested_for_{{ $lawRequest->id }}"
                                                                                    class="text-sm font-semibold text-slate-700">
                                                                                    From Date
                                                                                </label>

                                                                                <input
                                                                                    id="edit_student_requested_for_{{ $lawRequest->id }}"
                                                                                    type="date" name="requested_for"
                                                                                    value="{{ $lawRequest->requested_for?->toDateString() ?? now()->toDateString() }}"
                                                                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                            </div>

                                                                            <div class="space-y-2">
                                                                                <label
                                                                                    for="edit_student_requested_until_{{ $lawRequest->id }}"
                                                                                    class="text-sm font-semibold text-slate-700">
                                                                                    Until Date
                                                                                </label>

                                                                                <input
                                                                                    id="edit_student_requested_until_{{ $lawRequest->id }}"
                                                                                    type="date" name="requested_until"
                                                                                    value="{{ $lawRequest->requested_until?->toDateString() ?? ($lawRequest->requested_for?->toDateString() ?? now()->toDateString()) }}"
                                                                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                            </div>
                                                                        </div>

                                                                        <div class="space-y-2">
                                                                            <label
                                                                                for="edit_student_reason_{{ $lawRequest->id }}"
                                                                                class="text-sm font-semibold text-slate-700">
                                                                                Reason
                                                                            </label>

                                                                            <textarea id="edit_student_reason_{{ $lawRequest->id }}" name="reason" rows="6" maxlength="5000"
                                                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                                placeholder="Explain why you need an attendance law request...">{{ $lawRequest->reason }}</textarea>
                                                                        </div>

                                                                        <div
                                                                            class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                                            <div
                                                                                class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                                                                Teachers Who Will Be Notified
                                                                            </div>

                                                                            <div
                                                                                class="js-edit-teacher-recipient-list mt-3 hidden flex-wrap gap-2">
                                                                            </div>

                                                                            <div
                                                                                class="js-edit-teacher-recipient-empty mt-2 text-sm text-slate-500">
                                                                                No teacher is assigned to this subject time
                                                                                yet.
                                                                                Your
                                                                                request will still be saved.
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="flex justify-end gap-3 border-t border-slate-100 px-6 py-5">
                                                                        <button type="button" @click="editOpen = false"
                                                                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
                                                                            Cancel
                                                                        </button>

                                                                        <button type="submit"
                                                                            class="rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-500">
                                                                            Save Changes
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        @else
                                                            <span class="text-xs font-semibold text-slate-400">
                                                                Locked after review
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7"
                                                        class="px-3 py-10 text-center text-sm text-slate-500">
                                                        No law requests submitted yet.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
            </section>
        </div>

        @php
            $lawRequestPageData = [
                'validationErrors' => $errors->all(),
                'subjectTimeOptionsBySubject' => $subjectTimeMap ?? [],
                'teacherRecipientsByTime' => $teacherRecipientsByTime ?? [],
                'formDefaults' => [
                    'subject_id' => $selectedSubjectId,
                    'subject_time_keys' => $selectedTimeKeys,
                    'requested_for' => (string) ($formValues['requested_for'] ?? ''),
                ],
                'flash' => [
                    'success' => session('success'),
                    'warning' => session('warning'),
                    'error' => session('error'),
                ],
            ];
        @endphp

        <script id="student-law-request-data" type="application/json">
            {!! json_encode($lawRequestPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
        </script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @vite(['resources/js/Student/LawRequests.js'])
    </div>
@endsection
