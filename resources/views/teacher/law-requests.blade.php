@extends('layout.teacher.navbar')

@section('page')
    @php
        $statusStyles = [
            'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
            'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'rejected' => 'border-red-200 bg-red-50 text-red-700',
        ];

        $formValues = $formDefaults ?? [];
        $isEditing = isset($editingRequest) && $editingRequest;
        $selectedSubjectId = (string) ($formValues['subject_id'] ?? 'all');
        $selectedTimeKeys = array_values(
            array_unique(array_filter(array_map('strval', (array) ($formValues['subject_time_keys'] ?? [])))),
        );
        $subjectTimeMap = $subjectTimeOptionsBySubject ?? [];
        $initialTimeOptions = $subjectTimeMap[$selectedSubjectId] ?? [];
        $lawRequestTotal = ($lawRequests ?? collect())->count();
        $filters = $filters ?? [];
        $filterSearch = (string) ($filters['q'] ?? '');
        $filterStatus = (string) ($filters['status'] ?? 'all');
        $filterType = (string) ($filters['law_type'] ?? 'all');
        $filterSubjectId = (string) ($filters['subject_id'] ?? 'all');
        $hasActiveFilters =
            $filterSearch !== '' ||
            ($filterStatus !== '' && $filterStatus !== 'all') ||
            ($filterType !== '' && $filterType !== 'all') ||
            ($filterSubjectId !== '' && $filterSubjectId !== 'all');
        $teacherLawRequestStatCards = [
            [
                'label' => 'Requests',
                'activeLabel' => 'Submitted',
                'active' => $lawRequestTotal,
                'total' => $lawRequestTotal,
                'icon' => 'records',
                'tone' => 'from-indigo-100 to-white text-indigo-600',
            ],
            [
                'label' => 'Pending',
                'activeLabel' => 'Waiting review',
                'active' => ($lawRequests ?? collect())->where('status', 'pending')->count(),
                'total' => $lawRequestTotal,
                'icon' => 'pending',
                'tone' => 'from-amber-100 to-white text-amber-600',
            ],
            [
                'label' => 'Approved',
                'activeLabel' => 'Accepted',
                'active' => ($lawRequests ?? collect())->where('status', 'approved')->count(),
                'total' => $lawRequestTotal,
                'icon' => 'active',
                'tone' => 'from-emerald-100 to-white text-emerald-600',
            ],
        ];
    @endphp

    <div class="teacher-time-stage space-y-6">
        <section class="teacher-time-reveal admin-page-header teacher-page-header" style="--sd: 1;">
            <div class="admin-page-header__main flex flex-wrap items-start justify-between gap-4">
                <div class="admin-page-header__intro min-w-0">
                    <div class="admin-page-header__title-row flex items-start gap-3">
                        <span class="admin-page-header__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7Z" />
                                <path d="M14 2v5h5" />
                                <path d="M9 13h6" />
                                <path d="M9 17h4" />
                            </svg>
                        </span>

                        <div>
                            <div class="admin-page-header__eyebrow">Teacher Panel</div>
                            <h1 class="admin-page-title text-3xl font-black tracking-tight">Teacher Law Requests</h1>
                            <p class="admin-page-subtitle mt-1 text-sm">Submit a law request and track its status from
                                creation to
                                resolution.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <x-admin.stat-cards :cards="$teacherLawRequestStatCards" reveal-class="teacher-time-reveal" float-class="teacher-time-float"
            grid-class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3" />

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
            <section
                class="teacher-time-reveal teacher-time-float min-w-0 rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-5"
                style="--sd: 3;">
                <h2 class="text-lg font-black text-slate-900">{{ $isEditing ? 'Edit Request' : 'Create Request' }}</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">
                    {{ $isEditing ? 'Update your law request and save changes.' : 'Fill in the details and submit your request.' }}
                </p>

                <form method="POST"
                    action="{{ $isEditing ? route('teacher.law-requests.update', $editingRequest) : route('teacher.law-requests.store') }}"
                    class="js-law-request-submit-form mt-5 space-y-4">
                    @csrf
                    @if ($isEditing)
                        @method('PUT')
                    @endif

                    <div class="space-y-2">
                        <label for="law_type" class="text-sm font-semibold text-slate-700">Law Type</label>
                        <select id="law_type" name="law_type"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @foreach ($lawTypes as $typeKey => $typeLabel)
                                <option value="{{ $typeKey }}"
                                    {{ (string) ($formValues['law_type'] ?? '') === (string) $typeKey ? 'selected' : '' }}>
                                    {{ $typeLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="subject" class="text-sm font-semibold text-slate-700">Subject</label>
                        <select id="subject" name="subject_id"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="all" {{ $selectedSubjectId === 'all' ? 'selected' : '' }}>All Subjects
                            </option>
                            @foreach ($subjectOptions as $subjectOption)
                                @php
                                    $className = trim((string) ($subjectOption->class_name ?? ''));
                                    $classSection = trim((string) ($subjectOption->class_section ?? ''));
                                    $classDisplay =
                                        $className !== ''
                                            ? $className . ($classSection !== '' ? ' - ' . $classSection : '')
                                            : '';
                                @endphp
                                <option value="{{ $subjectOption->id }}"
                                    {{ $selectedSubjectId === (string) $subjectOption->id ? 'selected' : '' }}>
                                    {{ $subjectOption->name }}
                                    {{ !empty($subjectOption->code) ? ' (' . $subjectOption->code . ')' : '' }}
                                    {{ $classDisplay !== '' ? ' | ' . $classDisplay : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <div class="text-sm font-semibold text-slate-700">Subject Time</div>
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
                                        {{ $isChecked ? 'checked' : '' }}
                                        class="js-time-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-800">{{ $timeLabel }}</div>
                                    </div>
                                </label>
                            @empty
                                <div
                                    class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-500">
                                    No time available
                                </div>
                            @endforelse
                        </div>
                        <p class="text-xs font-medium text-slate-400">Choose one time slot, or select All to include every
                            class time for this subject.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="requested_for" class="text-sm font-semibold text-slate-700">From Date</label>
                            <input id="requested_for" type="date" name="requested_for"
                                value="{{ (string) ($formValues['requested_for'] ?? now()->toDateString()) }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>
                        <div class="space-y-2">
                            <label for="requested_until" class="text-sm font-semibold text-slate-700">Until Date</label>
                            <input id="requested_until" type="date" name="requested_until"
                                value="{{ (string) ($formValues['requested_until'] ?? ($formValues['requested_for'] ?? now()->toDateString())) }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="reason" class="text-sm font-semibold text-slate-700">Reason</label>
                        <textarea id="reason" name="reason" rows="6" maxlength="5000"
                            placeholder="Write full details of the law request..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ (string) ($formValues['reason'] ?? '') }}</textarea>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <button type="submit"
                            class="inline-flex w-full flex-1 items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                            {{ $isEditing ? 'Update Law Request' : 'Submit Law Request' }}
                        </button>
                        @if ($isEditing)
                            <a href="{{ route('teacher.law-requests.index') }}"
                                class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 sm:w-auto">
                                Cancel
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            <section x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true"
                class="teacher-time-reveal teacher-time-float min-w-0 rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-7"
                style="--sd: 4;">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-black text-slate-900">Request History</h2>
                    <button type="button" @click="filterOpen = true"
                        class="inline-flex min-w-[140px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                        </svg>
                        Filters
                    </button>
                </div>

                <div x-show="filterOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[80] bg-slate-900/40"
                    @click="filterOpen = false"></div>

                <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl">
                    <div class="flex h-full flex-col">
                        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                            <h3 class="text-3xl font-black text-slate-900">Filters</h3>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('teacher.law-requests.index') }}"
                                    class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-800">
                                    Clear All
                                </a>
                                <button type="button" @click="filterOpen = false"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900"
                                    aria-label="Close filters">
                                    &times;
                                </button>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('teacher.law-requests.index') }}"
                            class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                            <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                    <input name="q" type="text" value="{{ $filterSearch }}"
                                        placeholder="Search by subject, time, or reason"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Law Type</h4>
                                    <select name="law_type"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="all">All types</option>
                                        @foreach ($lawTypes as $typeKey => $typeLabel)
                                            <option value="{{ $typeKey }}" @selected($filterType === (string) $typeKey)>
                                                {{ $typeLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Subject</h4>
                                    <select name="subject_id"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="all">All subjects</option>
                                        @foreach ($subjectOptions as $subjectOption)
                                            @php
                                                $className = trim((string) ($subjectOption->class_name ?? ''));
                                                $classSection = trim((string) ($subjectOption->class_section ?? ''));
                                                $classDisplay =
                                                    $className !== ''
                                                        ? $className .
                                                            ($classSection !== '' ? ' - ' . $classSection : '')
                                                        : '';
                                            @endphp
                                            <option value="{{ $subjectOption->id }}" @selected($filterSubjectId === (string) $subjectOption->id)>
                                                {{ $subjectOption->name }}
                                                {{ !empty($subjectOption->code) ? ' (' . $subjectOption->code . ')' : '' }}
                                                {{ $classDisplay !== '' ? ' | ' . $classDisplay : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                    <select name="status"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="all" @selected($filterStatus === 'all')>All status</option>
                                        <option value="pending" @selected($filterStatus === 'pending')>Pending</option>
                                        <option value="approved" @selected($filterStatus === 'approved')>Approved</option>
                                        <option value="rejected" @selected($filterStatus === 'rejected')>Rejected</option>
                                    </select>
                                </section>
                            </div>

                            <div class="border-t border-slate-200 px-5 py-4">
                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-slate-800">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </aside>

                @if ($hasActiveFilters)
                    <div
                        class="mb-4 flex flex-wrap items-center gap-2 rounded-2xl border border-indigo-100 bg-indigo-50/60 px-3 py-2 text-xs font-semibold text-slate-600">
                        <span class="text-indigo-700">Active filters:</span>
                        @if ($filterSearch !== '')
                            <span class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100">Search:
                                {{ $filterSearch }}</span>
                        @endif
                        @if ($filterType !== '' && $filterType !== 'all')
                            <span class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100">Type:
                                {{ $lawTypes[$filterType] ?? ucfirst(str_replace('_', ' ', $filterType)) }}</span>
                        @endif
                        @if ($filterSubjectId !== '' && $filterSubjectId !== 'all')
                            <span class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100">Subject:
                                {{ data_get(collect($subjectOptions)->firstWhere('id', (int) $filterSubjectId), 'name', 'Selected') }}</span>
                        @endif
                        @if ($filterStatus !== '' && $filterStatus !== 'all')
                            <span class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100">Status:
                                {{ ucfirst($filterStatus) }}</span>
                        @endif
                        <a href="{{ route('teacher.law-requests.index') }}"
                            class="ml-auto rounded-full bg-white px-2.5 py-1 text-indigo-700 ring-1 ring-indigo-100 hover:bg-indigo-100">
                            Clear
                        </a>
                    </div>
                @endif

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="max-h-[620px] overflow-auto">
                        <table class="w-full min-w-[980px] lg:min-w-[1100px] text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-3 py-3 font-semibold">Type</th>
                                    <th class="px-3 py-3 font-semibold">Subject</th>
                                    <th class="px-3 py-3 font-semibold">From Date</th>
                                    <th class="px-3 py-3 font-semibold">Until Date</th>
                                    <th class="px-3 py-3 font-semibold">Status</th>
                                    <th class="px-3 py-3 font-semibold">Submitted</th>
                                    <th class="px-3 py-3 font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($lawRequests as $lawRequest)
                                    @php
                                        $statusKey = strtolower((string) ($lawRequest->status ?? 'pending'));
                                        $statusClass =
                                            $statusStyles[$statusKey] ?? 'border-slate-200 bg-slate-50 text-slate-700';
                                        $typeLabel =
                                            $lawTypes[$lawRequest->law_type] ??
                                            ucfirst(str_replace('_', ' ', (string) $lawRequest->law_type));
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
                                            $requestedUntilText !== '' ? $requestedUntilText : $requestedForText;
                                        if ($subjectTimeText === '' && str_contains($subjectText, '|')) {
                                            [$parsedSubject, $parsedTime] = array_pad(
                                                array_map('trim', explode('|', $subjectText, 2)),
                                                2,
                                                '',
                                            );
                                            $subjectText = $parsedSubject;
                                            $subjectTimeText = $parsedTime;
                                        }
                                    @endphp
                                    <tr x-data="{ editOpen: false }"
                                        class="align-top hover:bg-slate-50/80 {{ $isEditing && (int) ($editingRequest->id ?? 0) === (int) $lawRequest->id ? 'bg-indigo-50/40' : '' }}">
                                        <td class="px-3 py-3 font-semibold text-slate-800">{{ $typeLabel }}</td>
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-900">{{ $subjectText }}</div>
                                            <div class="mt-1 text-xs text-slate-500 line-clamp-2">
                                                {{ $lawRequest->reason }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">
                                            <div class="font-semibold text-slate-900">{{ $requestedForText }}</div>
                                            @if ($subjectTimeParts !== [])
                                                <div class="mt-2 flex flex-wrap gap-1.5">
                                                    @foreach ($subjectTimeParts as $subjectTimePart)
                                                        <span
                                                            class="inline-flex rounded-full border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700">
                                                            {{ $subjectTimePart }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">
                                            <div class="font-semibold text-slate-900">{{ $requestedUntilDisplay }}</div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span
                                                class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                {{ ucfirst($statusKey) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">
                                            {{ $lawRequest->created_at?->format('M d, Y h:i A') }}</td>
                                        <td class="px-3 py-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if ($statusKey === 'pending')
                                                    <button type="button" @click="editOpen = true"
                                                        class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                        Edit
                                                    </button>
                                                @else
                                                    <span
                                                        class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-400">
                                                        Locked
                                                    </span>
                                                @endif
                                                <form method="POST"
                                                    action="{{ route('teacher.law-requests.destroy', $lawRequest) }}"
                                                    class="js-law-request-delete-form"
                                                    data-request-label="{{ $subjectText !== '' ? $subjectText : 'Request #' . $lawRequest->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>

                                            @if ($statusKey === 'pending')
                                                @php
                                                    $editSubjectId = 'all';
                                                    if (strcasecmp($subjectText, 'All Subjects') !== 0) {
                                                        $matchedSubject = collect($subjectOptions)->first(function (
                                                            $subjectOption,
                                                        ) use ($subjectText) {
                                                            return strcasecmp(
                                                                trim((string) ($subjectOption->name ?? '')),
                                                                $subjectText,
                                                            ) === 0;
                                                        });
                                                        $editSubjectId = $matchedSubject
                                                            ? (string) $matchedSubject->id
                                                            : 'all';
                                                    }

                                                    $editTimeOptions = $subjectTimeMap[$editSubjectId] ?? [];
                                                    $editTimeKeys = [];
                                                    if ($editSubjectId === 'all') {
                                                        $editTimeKeys = ['all:all'];
                                                    } else {
                                                        foreach ($subjectTimeParts as $subjectTimePart) {
                                                            foreach ($editTimeOptions as $timeOption) {
                                                                if (
                                                                    strcasecmp(
                                                                        trim((string) ($timeOption['label'] ?? '')),
                                                                        $subjectTimePart,
                                                                    ) === 0
                                                                ) {
                                                                    $editTimeKeys[] =
                                                                        (string) ($timeOption['key'] ?? '');
                                                                }
                                                            }
                                                        }

                                                        if (
                                                            $editTimeKeys === [] &&
                                                            strcasecmp($subjectTimeText, 'All Times') === 0
                                                        ) {
                                                            $editTimeKeys = collect($editTimeOptions)
                                                                ->pluck('key')
                                                                ->map(fn($key) => (string) $key)
                                                                ->filter()
                                                                ->values()
                                                                ->all();
                                                        }
                                                    }

                                                    $editTimeKeys = array_values(
                                                        array_unique(
                                                            array_filter($editTimeKeys, fn($key) => $key !== ''),
                                                        ),
                                                    );
                                                @endphp
                                                <div x-show="editOpen" x-cloak x-transition.opacity
                                                    class="fixed inset-0 z-[80] bg-slate-900/40"
                                                    @click="editOpen = false"></div>
                                                <div x-show="editOpen" x-cloak x-transition
                                                    class="fixed inset-x-3 top-6 z-[81] mx-auto max-h-[calc(100vh-3rem)] w-full max-w-3xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl sm:top-10">
                                                    <form method="POST"
                                                        action="{{ route('teacher.law-requests.update', $lawRequest) }}"
                                                        class="js-law-request-edit-form flex max-h-[calc(100vh-3rem)] flex-col"
                                                        data-request-label="{{ $subjectText !== '' ? $subjectText : 'Request #' . $lawRequest->id }}">
                                                        @csrf
                                                        @method('PUT')

                                                        <div
                                                            class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                                                            <div class="min-w-0">
                                                                <h3 class="truncate text-2xl font-black text-slate-900">
                                                                    Edit Request</h3>
                                                                <p class="mt-2 text-sm text-slate-500">Update your pending
                                                                    law request details.</p>
                                                            </div>
                                                            <button type="button" @click="editOpen = false"
                                                                class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-slate-200 text-xl font-bold text-slate-600 hover:bg-slate-50"
                                                                aria-label="Close edit request">
                                                                &times;
                                                            </button>
                                                        </div>

                                                        <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-6 py-5">
                                                            <div class="space-y-2">
                                                                <label for="edit_law_type_{{ $lawRequest->id }}"
                                                                    class="text-sm font-semibold text-slate-700">Law
                                                                    Type</label>
                                                                <select id="edit_law_type_{{ $lawRequest->id }}"
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
                                                                <label for="edit_subject_{{ $lawRequest->id }}"
                                                                    class="text-sm font-semibold text-slate-700">Subject</label>
                                                                <select id="edit_subject_{{ $lawRequest->id }}"
                                                                    name="subject_id"
                                                                    class="js-edit-law-subject w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                    data-selected-time-keys='@json($editTimeKeys)'>
                                                                    <option value="all" @selected($editSubjectId === 'all')>All
                                                                        Subjects</option>
                                                                    @foreach ($subjectOptions as $subjectOption)
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
                                                                                        ($classSection !== ''
                                                                                            ? ' - ' . $classSection
                                                                                            : '')
                                                                                    : '';
                                                                        @endphp
                                                                        <option value="{{ $subjectOption->id }}"
                                                                            @selected($editSubjectId === (string) $subjectOption->id)>
                                                                            {{ $subjectOption->name }}
                                                                            {{ !empty($subjectOption->code) ? ' (' . $subjectOption->code . ')' : '' }}
                                                                            {{ $classDisplay !== '' ? ' | ' . $classDisplay : '' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="space-y-2">
                                                                <div class="text-sm font-semibold text-slate-700">Subject
                                                                    Time</div>
                                                                <div
                                                                    class="js-edit-law-time-options grid gap-2 sm:grid-cols-2">
                                                                </div>
                                                                <p class="text-xs font-medium text-slate-400">Choose one
                                                                    time slot, or select All to include every class time for
                                                                    this subject.</p>
                                                            </div>

                                                            <div class="grid gap-5 sm:grid-cols-2">
                                                                <div class="space-y-2">
                                                                    <label for="edit_requested_for_{{ $lawRequest->id }}"
                                                                        class="text-sm font-semibold text-slate-700">From
                                                                        Date</label>
                                                                    <input id="edit_requested_for_{{ $lawRequest->id }}"
                                                                        type="date" name="requested_for"
                                                                        value="{{ $lawRequest->requested_for?->toDateString() ?? now()->toDateString() }}"
                                                                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                </div>
                                                                <div class="space-y-2">
                                                                    <label
                                                                        for="edit_requested_until_{{ $lawRequest->id }}"
                                                                        class="text-sm font-semibold text-slate-700">Until
                                                                        Date</label>
                                                                    <input id="edit_requested_until_{{ $lawRequest->id }}"
                                                                        type="date" name="requested_until"
                                                                        value="{{ $lawRequest->requested_until?->toDateString() ?? ($lawRequest->requested_for?->toDateString() ?? now()->toDateString()) }}"
                                                                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                </div>
                                                            </div>

                                                            <div class="space-y-2">
                                                                <label for="edit_reason_{{ $lawRequest->id }}"
                                                                    class="text-sm font-semibold text-slate-700">Reason</label>
                                                                <textarea id="edit_reason_{{ $lawRequest->id }}" name="reason" rows="6" maxlength="5000"
                                                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                    placeholder="Write full details of the law request...">{{ $lawRequest->reason }}</textarea>
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
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-10 text-center text-sm text-slate-500">
                                            No law requests submitted yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @php
        $lawRequestPageData = [
            'approvalAlerts' => $approvalAlerts ?? [],
            'validationErrors' => $errors->all(),
            'subjectTimeOptionsBySubject' => $subjectTimeMap ?? [],
            'formDefaults' => [
                'subject_id' => $selectedSubjectId,
                'subject_time_keys' => $selectedTimeKeys,
                'requested_for' => (string) ($formValues['requested_for'] ?? ''),
                'requested_until' => (string) ($formValues['requested_until'] ?? ''),
            ],
            'flash' => [
                'success' => session('success'),
                'warning' => session('warning'),
                'error' => session('error'),
            ],
        ];
    @endphp
    <script id="teacher-law-request-data" type="application/json">{!! json_encode($lawRequestPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/Teacher/LawRequests.js'])
@endsection
