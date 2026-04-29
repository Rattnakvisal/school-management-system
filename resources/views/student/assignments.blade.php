@extends('layout.students.navbar')

@section('page')
    @php
        $studentStatPanelClass =
            'rounded-[26px] border border-white/80 bg-white/90 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.55)] ring-1 ring-slate-200 backdrop-blur';
        $assignmentTotal = (int) ($stats['total'] ?? 0);
        $assignmentDueSoon = (int) ($stats['dueSoon'] ?? 0);
        $assignmentOverdue = (int) ($stats['overdue'] ?? 0);
        $assignmentSubmitted = (int) ($stats['submitted'] ?? 0);
        $assignmentPending = (int) ($stats['pending'] ?? 0);
    @endphp

    <div class="student-stage space-y-6">
        <section class="student-reveal student-float admin-page-header" style="--sd: 1;">
            <div class="admin-page-header__main flex flex-wrap items-start gap-4">
                <div class="admin-page-header__intro space-y-2">
                    <div class="admin-page-header__title-row flex items-start gap-3">
                        <span class="admin-page-header__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M8 3.5h8A2.5 2.5 0 0 1 18.5 6v14l-3-1.75L12.5 20l-3-1.75L6.5 20V6A2.5 2.5 0 0 1 9 3.5Z" />
                                <path d="M9.5 8h5" />
                                <path d="M9.5 11.5h5" />
                                <path d="M9.5 15h3" />
                            </svg>
                        </span>
                        <div>
                            <div class="admin-page-header__eyebrow">Student Portal</div>
                            <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">Your Assignment Board</h1>
                        </div>
                    </div>
                    <p class="admin-page-subtitle text-sm">
                        Review the work your teachers posted for you, check deadlines, and stay ahead of upcoming tasks.
                    </p>
                    <p class="text-xs font-semibold text-slate-600">
                        Home Class: <span class="text-slate-900">{{ $classLabel }}</span>
                    </p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5">
            <article class="student-reveal student-float {{ $studentStatPanelClass }} min-h-[132px] p-5" style="--sd: 2;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-indigo-100 to-white text-indigo-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M8 3.5h8A2.5 2.5 0 0 1 18.5 6v14l-3-1.75L12.5 20l-3-1.75L6.5 20V6A2.5 2.5 0 0 1 9 3.5Z" />
                            <path d="M9.5 8h5" />
                            <path d="M9.5 11.5h5" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($assignmentTotal) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format(max(1, $assignmentTotal)) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Assignments</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Posted:
                    {{ number_format($assignmentTotal) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400"
                        style="width: {{ $assignmentTotal > 0 ? 100 : 0 }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $studentStatPanelClass }} min-h-[132px] p-5" style="--sd: 3;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-amber-100 to-white text-amber-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 3" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($assignmentDueSoon) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format(max(1, $assignmentTotal)) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Due Soon</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Next 7 days:
                    {{ number_format($assignmentDueSoon) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-amber-500 to-cyan-400"
                        style="width: {{ min(100, max(0, (int) round(($assignmentDueSoon / max(1, $assignmentTotal)) * 100))) }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $studentStatPanelClass }} min-h-[132px] p-5" style="--sd: 4;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-red-100 to-white text-red-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 9v4" />
                            <path d="M12 17h.01" />
                            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($assignmentOverdue) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format(max(1, $assignmentTotal)) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Overdue</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Past deadline:
                    {{ number_format($assignmentOverdue) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-red-500 to-cyan-400"
                        style="width: {{ min(100, max(0, (int) round(($assignmentOverdue / max(1, $assignmentTotal)) * 100))) }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $studentStatPanelClass }} min-h-[132px] p-5" style="--sd: 5;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-emerald-100 to-white text-emerald-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($assignmentSubmitted) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format(max(1, $assignmentTotal)) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Submitted</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Sent:
                    {{ number_format($assignmentSubmitted) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-emerald-500 to-cyan-400"
                        style="width: {{ min(100, max(0, (int) round(($assignmentSubmitted / max(1, $assignmentTotal)) * 100))) }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $studentStatPanelClass }} min-h-[132px] p-5" style="--sd: 6;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-white text-sky-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <path d="m17 8-5-5-5 5" />
                            <path d="M12 3v12" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($assignmentPending) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format(max(1, $assignmentTotal)) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">To Send</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Waiting:
                    {{ number_format($assignmentPending) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-sky-500 to-cyan-400"
                        style="width: {{ min(100, max(0, (int) round(($assignmentPending / max(1, $assignmentTotal)) * 100))) }}%"></span>
                </div>
            </article>
        </section>

        @if (session('success'))
            <div class="js-inline-flash student-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 5;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="js-inline-flash student-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 5;">
                {{ $errors->first() }}
            </div>
        @endif

        @if (($assignments ?? null) && $assignments->count() > 0)
            <section class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200"
                style="--sd: 7;">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Assignment Table</h2>
                        <p class="mt-1 text-xs font-medium text-slate-500">Choose a file from the row, then send it to your teacher.</p>
                    </div>
                    <span class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm">
                        {{ number_format($assignmentTotal) }} total
                    </span>
                </div>

                <div class="min-w-0">
                    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200">
                        <div class="student-table-scroller max-h-[720px] overflow-auto">
                            <table class="student-table w-full whitespace-nowrap text-left text-sm">
                                <thead class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-3 py-3 font-semibold">Assignment</th>
                                        <th class="px-3 py-3 font-semibold">Subject</th>
                                        <th class="px-3 py-3 font-semibold">Teacher</th>
                                        <th class="px-3 py-3 font-semibold">Due</th>
                                        <th class="px-3 py-3 font-semibold">Status</th>
                                        <th class="px-3 py-3 font-semibold">Submission</th>
                                        <th class="whitespace-nowrap px-3 py-3 text-right font-semibold">Actions</th>
                                    </tr>
                                </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($assignments as $assignment)
                                    @php
                                        $submission = $assignment->students->first()?->pivot;
                                        $isSubmitted = filled($submission?->submission_file_path);
                                        $dueAt = $assignment->due_at;
                                        $statusLabel = $isSubmitted ? 'Submitted' : 'No due date';
                                        $statusClass = $isSubmitted
                                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                            : 'border-slate-200 bg-slate-50 text-slate-700';

                                        if (!$isSubmitted && $dueAt) {
                                            if ($dueAt->isPast()) {
                                                $statusLabel = 'Overdue';
                                                $statusClass = 'border-red-200 bg-red-50 text-red-700';
                                            } elseif ($dueAt->isBefore(now()->addDays(7))) {
                                                $statusLabel = 'Due soon';
                                                $statusClass = 'border-amber-200 bg-amber-50 text-amber-700';
                                            } else {
                                                $statusLabel = 'Upcoming';
                                                $statusClass = 'border-sky-200 bg-sky-50 text-sky-700';
                                            }
                                        }

                                        $subjectLabel = trim((string) ($assignment->subject?->name ?? 'General assignment'));
                                        $assignmentClassLabel = trim((string) ($assignment->subject?->schoolClass?->display_name ?? ''));
                                    @endphp

                                    <tr id="assignment-{{ $assignment->id }}" x-data="{ detailOpen: false }" class="align-top hover:bg-slate-50/80">
                                        <td class="px-3 py-3 align-top">
                                            <div class="flex items-start gap-3">
                                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M8 3.5h8A2.5 2.5 0 0 1 18.5 6v14l-3-1.75L12.5 20l-3-1.75L6.5 20V6A2.5 2.5 0 0 1 9 3.5Z" />
                                                        <path d="M9.5 8h5" />
                                                        <path d="M9.5 11.5h5" />
                                                    </svg>
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="max-w-[280px] whitespace-normal font-semibold leading-5 text-slate-800">
                                                        {{ $assignment->title }}
                                                    </div>
                                                    <div class="mt-1 max-w-[280px] truncate text-xs text-slate-400">
                                                        ID #{{ str_pad((string) $assignment->id, 6, '0', STR_PAD_LEFT) }}
                                                        - Posted {{ $assignment->created_at?->format('M d, Y') }}
                                                    </div>
                                                    <div class="mt-1 max-w-[280px] truncate text-xs text-slate-500">
                                                        {{ $assignment->description }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 align-top text-slate-600">
                                            <div class="flex max-w-[220px] flex-col gap-1">
                                                <span class="inline-flex max-w-full items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px]">
                                                    <span class="truncate font-semibold text-indigo-700">{{ $subjectLabel }}</span>
                                                </span>
                                                <span class="truncate text-xs text-slate-400">
                                                    {{ $assignmentClassLabel !== '' ? $assignmentClassLabel : 'Shared assignment' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 align-top text-slate-600">
                                            <div class="max-w-[160px] truncate">{{ $assignment->teacher?->name ?? 'Teacher' }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 align-top text-slate-500">
                                            <span>{{ $dueAt ? $dueAt->format('M d, Y') : 'No deadline' }}</span>
                                            @if ($dueAt)
                                                <div class="mt-1 text-xs text-slate-400">{{ $dueAt->diffForHumans() }}</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <span class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                <span class="h-2 w-2 rounded-full {{ $isSubmitted ? 'bg-emerald-500' : ($statusLabel === 'Overdue' ? 'bg-rose-500' : ($statusLabel === 'Due soon' ? 'bg-amber-500' : 'bg-slate-400')) }}"></span>
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 align-top text-slate-600">
                                            @if ($isSubmitted)
                                                @php
                                                    $fileSize = (int) ($submission->submission_file_size ?? 0);
                                                    $fileSizeLabel = 'Unknown size';

                                                    if ($fileSize > 0) {
                                                        $units = ['B', 'KB', 'MB', 'GB'];
                                                        $unitIndex = 0;
                                                        $displaySize = (float) $fileSize;

                                                        while ($displaySize >= 1024 && $unitIndex < count($units) - 1) {
                                                            $displaySize /= 1024;
                                                            $unitIndex++;
                                                        }

                                                        $fileSizeLabel = rtrim(rtrim(number_format($displaySize, 1), '0'), '.') . ' ' . $units[$unitIndex];
                                                    }
                                                @endphp
                                                <button type="button" @click="detailOpen = true"
                                                    class="inline-flex max-w-[220px] items-center rounded-lg border border-emerald-100 bg-emerald-50/90 px-2 py-1 text-[11px] hover:bg-emerald-100">
                                                    <span class="truncate font-semibold text-emerald-700">{{ $submission->submission_file_name ?: 'Submitted file' }}</span>
                                                </button>
                                                @if ($submission?->submitted_at)
                                                    <div class="mt-1 text-xs text-slate-400">
                                                        {{ \Illuminate\Support\Carbon::parse($submission->submitted_at)->diffForHumans() }}
                                                    </div>
                                                @endif
                                                <div x-show="detailOpen" x-cloak x-transition.opacity
                                                    class="fixed inset-0 z-[80] bg-slate-900/40" @click="detailOpen = false"></div>
                                                <div x-show="detailOpen" x-cloak x-transition
                                                    class="fixed inset-x-3 top-6 z-[81] mx-auto max-h-[calc(100vh-3rem)] w-full max-w-3xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl sm:top-10">
                                                    <div class="flex max-h-[calc(100vh-3rem)] flex-col">
                                                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
                                                            <div class="min-w-0">
                                                                <h3 class="truncate text-lg font-black text-slate-900">Submission Details</h3>
                                                                <p class="mt-1 text-xs text-slate-500">{{ $assignment->title }} - 1 file received.</p>
                                                            </div>
                                                            <button type="button" @click="detailOpen = false"
                                                                class="grid h-11 w-11 place-items-center rounded-full border border-slate-200 text-xl font-bold text-slate-500 hover:bg-slate-50"
                                                                aria-label="Close submission details">&times;</button>
                                                        </div>
                                                        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                                                            <article class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                                                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                                    <div class="min-w-0">
                                                                        <div class="flex flex-wrap items-center gap-2">
                                                                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">Student</span>
                                                                            <span class="text-sm font-black text-slate-900">{{ auth()->user()?->name ?? 'Student' }}</span>
                                                                        </div>
                                                                        <div class="mt-1 text-xs text-slate-400">{{ auth()->user()?->email ?? 'No email' }}</div>
                                                                        <div class="mt-3">
                                                                            <a href="{{ \Illuminate\Support\Facades\Storage::url($submission->submission_file_path) }}"
                                                                                target="_blank"
                                                                                class="inline-flex max-w-full items-center gap-2 rounded-xl border border-emerald-100 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                                                                    <path d="M14 2v6h6" />
                                                                                </svg>
                                                                                <span class="truncate">{{ $submission->submission_file_name ?: 'Submitted file' }}</span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="shrink-0 rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-500 ring-1 ring-slate-100">
                                                                        <div>{{ $fileSizeLabel }}</div>
                                                                        <div class="mt-1">
                                                                            {{ $submission?->submitted_at ? \Illuminate\Support\Carbon::parse($submission->submitted_at)->format('M d, Y h:i A') : 'No date' }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </article>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 align-top">
                                            <form method="POST" action="{{ route('student.assignment.submit', $assignment) }}"
                                                enctype="multipart/form-data"
                                                class="js-student-assignment-submit-form flex flex-nowrap items-center justify-end gap-2"
                                                data-assignment="{{ $assignment->title }}"
                                                novalidate
                                                x-data="{ fileName: '' }">
                                                @csrf
                                                <input id="assignment_submission_{{ $assignment->id }}" name="submission_file" type="file"
                                                    class="sr-only" required @change="fileName = $event.target.files[0]?.name || ''">

                                                <label for="assignment_submission_{{ $assignment->id }}"
                                                    class="inline-flex cursor-pointer items-center justify-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                    <svg class="h-4 w-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                                        <path d="M14 2v6h6" />
                                                    </svg>
                                                    {{ $isSubmitted ? 'Edit' : 'Choose' }}
                                                </label>
                                                <span class="max-w-[150px] truncate text-xs font-medium text-slate-400"
                                                    x-text="fileName || 'No file chosen'"></span>

                                                <button type="submit"
                                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="m22 2-7 20-4-9-9-4Z" />
                                                        <path d="M22 2 11 13" />
                                                    </svg>
                                                    Send to teacher
                                                </button>
                                            </form>
                                            @if ($isSubmitted)
                                                <form method="POST"
                                                    action="{{ route('student.assignment.submission.destroy', $assignment) }}"
                                                    class="js-student-assignment-delete-form mt-2 flex justify-end"
                                                    data-assignment="{{ $assignment->title }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <path d="M3 6h18" />
                                                            <path d="M8 6V4h8v2" />
                                                            <path d="M19 6l-1 14H6L5 6" />
                                                            <path d="M10 11v5" />
                                                            <path d="M14 11v5" />
                                                        </svg>
                                                        Remove
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </section>

            <div>
                {{ $assignments->links() }}
            </div>
        @else
            <section class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center shadow-sm">
                <div class="text-xl font-black text-slate-900">No assignments yet</div>
                <p class="mt-2 text-sm text-slate-500">
                    When a teacher posts a new assignment for you, it will show up here and an alert notification will be sent.
                </p>
            </section>
        @endif
    </div>

    @php
        $studentAssignmentPageData = [
            'validationErrors' => $errors->all(),
            'flash' => [
                'success' => session('success'),
                'successTitle' => session('success_title', 'Sent to teacher'),
                'error' => $errors->any() ? $errors->first() : '',
            ],
        ];
    @endphp
    <script id="student-assignment-data" type="application/json">{!! json_encode($studentAssignmentPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hasSwal = typeof window.Swal !== 'undefined';
            const dataNode = document.getElementById('student-assignment-data');
            const pageData = {
                validationErrors: [],
                flash: {
                    success: '',
                    successTitle: 'Sent to teacher',
                    error: '',
                },
            };

            if (dataNode) {
                try {
                    const parsed = JSON.parse(dataNode.textContent || '{}');
                    Object.assign(pageData, parsed || {});
                    pageData.flash = {
                        ...pageData.flash,
                        ...((parsed && parsed.flash) || {}),
                    };
                } catch (error) {
                    console.error('Unable to parse student assignment data.', error);
                }
            }

            const showAlert = (config) => {
                const options = {
                    icon: config.icon || 'info',
                    title: config.title || 'Notice',
                    text: config.text || '',
                    confirmButtonText: config.confirmButtonText || 'OK',
                    confirmButtonColor: config.confirmButtonColor || '#4f46e5',
                };

                if (hasSwal) {
                    return window.Swal.fire(options);
                }

                window.alert(`${options.title}\n\n${options.text}`);
                return Promise.resolve({ isConfirmed: true });
            };

            const confirmSend = (config) => {
                const options = {
                    icon: 'question',
                    title: config.title,
                    text: config.text,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, send it',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#94a3b8',
                };

                if (hasSwal) {
                    return window.Swal.fire(options).then((result) => Boolean(result && result.isConfirmed));
                }

                return Promise.resolve(window.confirm(`${options.title}\n\n${options.text}`));
            };

            if ((pageData.validationErrors || []).length > 0) {
                document.querySelectorAll('.js-inline-flash').forEach((element) => element.classList.add('hidden'));
                showAlert({
                    icon: 'error',
                    title: 'Upload failed',
                    text: String(pageData.validationErrors[0] || 'Please choose a valid file and try again.'),
                });
            } else if (String(pageData.flash.success || '').trim() !== '') {
                document.querySelectorAll('.js-inline-flash').forEach((element) => element.classList.add('hidden'));
                showAlert({
                    icon: 'success',
                    title: String(pageData.flash.successTitle || 'Done'),
                    text: String(pageData.flash.success),
                    confirmButtonText: 'Great',
                });
            }

            document.querySelectorAll('.js-student-assignment-submit-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.confirmed === '1') {
                        form.dataset.confirmed = '0';
                        return;
                    }

                    event.preventDefault();

                    const input = form.querySelector('input[type="file"]');
                    const file = input && input.files ? input.files[0] : null;
                    const assignmentTitle = String(form.dataset.assignment || 'this assignment');

                    if (!file) {
                        showAlert({
                            icon: 'warning',
                            title: 'Choose a file first',
                            text: 'Please choose your completed assignment file before sending it to your teacher.',
                        });
                        return;
                    }

                    confirmSend({
                        title: 'Send assignment file?',
                        text: `Send "${file.name}" to your teacher for "${assignmentTitle}"?`,
                    }).then((confirmed) => {
                        if (!confirmed) {
                            return;
                        }

                        form.dataset.confirmed = '1';
                        form.submit();
                    });
                });
            });

            document.querySelectorAll('.js-student-assignment-delete-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.confirmed === '1') {
                        form.dataset.confirmed = '0';
                        return;
                    }

                    event.preventDefault();

                    const assignmentTitle = String(form.dataset.assignment || 'this assignment');
                    const options = {
                        icon: 'warning',
                        title: 'Delete submitted file?',
                        text: `Remove your submitted file for "${assignmentTitle}"?`,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#94a3b8',
                    };

                    const confirmation = hasSwal
                        ? window.Swal.fire(options).then((result) => Boolean(result && result.isConfirmed))
                        : Promise.resolve(window.confirm(`${options.title}\n\n${options.text}`));

                    confirmation.then((confirmed) => {
                        if (!confirmed) {
                            return;
                        }

                        form.dataset.confirmed = '1';
                        form.submit();
                    });
                });
            });
        });
    </script>
@endsection
