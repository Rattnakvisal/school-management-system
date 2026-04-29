@extends('layout.teacher.navbar')

@section('page')
    @php
        $subjectOptions = collect($subjectOptions ?? []);
        $studentOptions = collect($studentOptions ?? []);
        $editingAssignment = $editingAssignment ?? null;
        $isEditing = $editingAssignment !== null;
        $formStudentIds = old('student_ids', $isEditing ? $editingAssignment->students->pluck('id')->all() : []);
        $oldStudentIds = collect($formStudentIds)
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id) => $id > 0)
            ->values()
            ->all();
        $selectedSubjectId = (string) old('subject_id', $isEditing ? (string) ($editingAssignment->subject_id ?? '') : '');
        $titleValue = (string) old('title', $isEditing ? (string) ($editingAssignment->title ?? '') : '');
        $descriptionValue = (string) old('description', $isEditing ? (string) ($editingAssignment->description ?? '') : '');
        $dueDateValue = (string) old(
            'due_at',
            $isEditing && $editingAssignment?->due_at ? $editingAssignment->due_at->format('Y-m-d') : '',
        );
        $assignmentTotal = max(0, (int) ($stats['total'] ?? 0));
        $teacherAssignmentStatCards = [
            [
                'label' => 'Assignments',
                'activeLabel' => 'Posted',
                'active' => $assignmentTotal,
                'total' => $assignmentTotal,
                'icon' => 'assignment',
                'tone' => 'from-indigo-100 to-white text-indigo-600',
            ],
            [
                'label' => 'Students',
                'activeLabel' => 'Assigned',
                'active' => (int) ($stats['students'] ?? 0),
                'total' => (int) ($stats['students'] ?? 0),
                'icon' => 'students',
                'tone' => 'from-emerald-100 to-white text-emerald-600',
            ],
            [
                'label' => 'Subjects',
                'activeLabel' => 'Available',
                'active' => (int) ($stats['subjects'] ?? 0),
                'total' => (int) ($stats['subjects'] ?? 0),
                'icon' => 'subjects',
                'tone' => 'from-sky-100 to-white text-sky-600',
            ],
            [
                'label' => 'Due Soon',
                'activeLabel' => 'Next 7 days',
                'active' => (int) ($stats['dueSoon'] ?? 0),
                'total' => $assignmentTotal,
                'icon' => 'pending',
                'tone' => 'from-amber-100 to-white text-amber-600',
            ],
        ];
    @endphp

    <div class="teacher-stage teacher-assignment-stage space-y-6">
        <section class="admin-page-header teacher-page-header">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Assignments</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        Create assignments for your students and send an alert as soon as they are posted.
                    </p>
                </div>
            </div>
        </section>

        <x-admin.stat-cards :cards="$teacherAssignmentStatCards" reveal-class="teacher-reveal" float-class="teacher-float"
            grid-class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4" />

        @if (session('success'))
            <div class="js-inline-flash rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="js-inline-flash rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            <section class="min-w-0 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">{{ $isEditing ? 'Edit Assignment' : 'Create Assignment' }}</h2>
                        <p class="mt-1 text-xs font-semibold text-slate-500">
                            {{ $isEditing ? 'Update the assignment details and save your changes.' : 'Choose the students, write the task, and send it with one post.' }}
                        </p>
                    </div>
                    <span class="rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ $isEditing ? 'Edit mode' : 'Alert enabled' }}
                    </span>
                </div>

                @if ($studentOptions->isNotEmpty())
                    <form method="POST"
                        action="{{ $isEditing ? route('teacher.assignments.update', $editingAssignment) : route('teacher.assignments.store') }}"
                        class="mt-5 space-y-4"
                        id="teacher-assignment-form">
                        @csrf
                        @if ($isEditing)
                            @method('PUT')
                        @endif

                        <div class="space-y-2">
                            <label for="assignment_title" class="text-sm font-semibold text-slate-700">Title</label>
                            <input id="assignment_title" name="title" type="text" value="{{ $titleValue }}"
                                maxlength="255" placeholder="Example: Chapter 4 Homework"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="assignment_subject_id" class="text-sm font-semibold text-slate-700">Subject</label>
                                <select id="assignment_subject_id" name="subject_id"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">General assignment</option>
                                    @foreach ($subjectOptions as $subject)
                                        <option value="{{ $subject['id'] }}"
                                            {{ $selectedSubjectId === (string) $subject['id'] ? 'selected' : '' }}>
                                            {{ $subject['name'] }}
                                            {{ $subject['code'] !== '' ? ' (' . $subject['code'] . ')' : '' }}
                                            {{ $subject['class_label'] !== '' ? ' | ' . $subject['class_label'] : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="assignment_due_at" class="text-sm font-semibold text-slate-700">Due Date</label>
                                <input id="assignment_due_at" type="date" name="due_at" value="{{ $dueDateValue }}"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="assignment_description" class="text-sm font-semibold text-slate-700">Description</label>
                            <textarea id="assignment_description" name="description" rows="6" maxlength="5000"
                                placeholder="Write the task details, what students should submit, and any important instructions."
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ $descriptionValue }}</textarea>
                        </div>

                        <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-sm font-black text-slate-900">Assign To Students</h3>
                                    <p class="mt-1 text-xs font-medium text-slate-500">
                                        Filter the list, then select one or more students.
                                    </p>
                                </div>
                                <div id="assignment-selected-count"
                                    class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700">
                                    0 selected
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto_auto]">
                                <input id="assignment-student-search" type="text"
                                    placeholder="Search by student or class"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                <button type="button" id="assignment-select-visible"
                                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    Select visible
                                </button>
                                <button type="button" id="assignment-clear-visible"
                                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    Clear visible
                                </button>
                            </div>

                            <div class="max-h-[26rem] space-y-3 overflow-auto pr-1" id="assignment-student-list">
                                @foreach ($studentOptions as $student)
                                    @php
                                        $subjectIds = implode(',', $student['subject_ids'] ?? []);
                                        $subjectNames = collect($student['subject_names'] ?? [])->take(3)->all();
                                        $isChecked = in_array((int) $student['id'], $oldStudentIds, true);
                                    @endphp
                                    <label data-student-card data-student-name="{{ strtolower($student['name']) }}"
                                        data-student-class="{{ strtolower($student['class_label']) }}"
                                        data-subject-ids="{{ $subjectIds }}"
                                        class="flex cursor-pointer items-start gap-3 rounded-2xl border {{ $isChecked ? 'border-indigo-300 bg-indigo-50/70' : 'border-slate-200 bg-white' }} px-4 py-3 transition hover:border-indigo-300 hover:bg-indigo-50/50">
                                        <input type="checkbox" name="student_ids[]" value="{{ $student['id'] }}"
                                            {{ $isChecked ? 'checked' : '' }}
                                            class="assignment-student-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="text-sm font-semibold text-slate-900">{{ $student['name'] }}</div>
                                                <span
                                                    class="rounded-full border border-sky-100 bg-sky-50 px-2.5 py-1 text-[11px] font-semibold text-sky-700">
                                                    {{ $student['class_label'] }}
                                                </span>
                                            </div>
                                            @if ($subjectNames !== [])
                                                <div class="mt-2 flex flex-wrap gap-1.5">
                                                    @foreach ($subjectNames as $subjectName)
                                                        <span
                                                            class="rounded-full border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700">
                                                            {{ $subjectName }}
                                                        </span>
                                                    @endforeach
                                                    @if (count($student['subject_names'] ?? []) > count($subjectNames))
                                                        <span class="text-[11px] font-semibold text-slate-400">
                                                            +{{ count($student['subject_names'] ?? []) - count($subjectNames) }} more
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <button type="submit"
                                class="inline-flex w-full flex-1 items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                {{ $isEditing ? 'Save Assignment Changes' : 'Post Assignment And Send Alert' }}
                            </button>
                            @if ($isEditing)
                                <a href="{{ route('teacher.assignments.index') }}"
                                    class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 sm:w-auto">
                                    Cancel
                                </a>
                            @endif
                        </div>
                    </form>
                @else
                    <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center">
                        <div class="text-lg font-bold text-slate-900">No students available yet</div>
                        <p class="mt-2 text-sm text-slate-500">
                            Once you are linked to a class or subject, your student roster will appear here.
                        </p>
                    </div>
                @endif
            </section>

            <section class="min-w-0 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Assignment History</h2>
                        <p class="mt-1 text-xs font-semibold text-slate-500">Latest assignments you have posted.</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">Latest first</span>
                </div>

                @if (($assignments ?? null) && $assignments->count() > 0)
                    <div class="overflow-hidden rounded-2xl border border-slate-200">
                        <div class="max-h-[44rem] overflow-auto">
                            <table class="student-table w-full min-w-[1180px] text-left text-sm">
                                <thead class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="whitespace-nowrap px-3 py-3 font-semibold">Assignment</th>
                                        <th class="whitespace-nowrap px-3 py-3 font-semibold">Subject</th>
                                        <th class="whitespace-nowrap px-3 py-3 font-semibold">Recipients</th>
                                        <th class="whitespace-nowrap px-3 py-3 font-semibold">Due</th>
                                        <th class="whitespace-nowrap px-3 py-3 font-semibold">Posted</th>
                                        <th class="whitespace-nowrap px-3 py-3 font-semibold text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($assignments as $assignment)
                                        @php
                                            $subjectLabel = trim((string) ($assignment->subject?->name ?? 'General assignment'));
                                            $classLabel = trim((string) ($assignment->subject?->schoolClass?->display_name ?? ''));
                                            $submittedStudents = $assignment->students->filter(
                                                fn($student) => filled($student->pivot?->submission_file_path),
                                            );
                                            $latestSubmission = $submittedStudents
                                                ->sortByDesc(fn($student) => $student->pivot?->submitted_at)
                                                ->first();
                                            $dueAt = $assignment->due_at;
                                            $dueClass = 'border-slate-200 bg-slate-50 text-slate-700';
                                            if ($dueAt && $dueAt->isPast()) {
                                                $dueClass = 'border-red-200 bg-red-50 text-red-700';
                                            } elseif ($dueAt && $dueAt->isBefore(now()->addDays(7))) {
                                                $dueClass = 'border-amber-200 bg-amber-50 text-amber-700';
                                            }
                                        @endphp
                                        <tr id="assignment-{{ $assignment->id }}" x-data="{ detailOpen: false, editOpen: false }"
                                            class="align-top hover:bg-slate-50/80 {{ $isEditing && (int) $editingAssignment->id === (int) $assignment->id ? 'bg-indigo-50/40' : '' }}">
                                            <td class="px-3 py-3">
                                                <div class="font-semibold text-slate-900">{{ $assignment->title }}</div>
                                                <div class="mt-1 line-clamp-2 text-xs text-slate-500">
                                                    {{ \Illuminate\Support\Str::limit($assignment->description, 120) }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="font-semibold text-slate-800">{{ $subjectLabel }}</div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    {{ $classLabel !== '' ? $classLabel : 'No class label' }}
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-3">
                                                <button type="button" @click="detailOpen = true"
                                                    class="inline-flex items-center gap-2 rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                                    View Recipients
                                                </button>
                                                <div class="mt-2 flex flex-wrap gap-1.5">
                                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                        {{ number_format((int) ($assignment->students_count ?? 0)) }} student{{ (int) ($assignment->students_count ?? 0) === 1 ? '' : 's' }}
                                                    </span>
                                                    @if ($submittedStudents->isNotEmpty())
                                                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                            {{ number_format($submittedStudents->count()) }} submitted
                                                        </span>
                                                    @else
                                                        <span class="inline-flex rounded-full bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-500 ring-1 ring-slate-200">
                                                            No files
                                                        </span>
                                                    @endif
                                                </div>
                                                @if ($latestSubmission?->pivot?->submitted_at)
                                                    <div class="mt-1 text-xs text-slate-400">
                                                        Latest {{ \Illuminate\Support\Carbon::parse($latestSubmission->pivot->submitted_at)->diffForHumans() }}
                                                    </div>
                                                @endif

                                                <div x-show="detailOpen" x-cloak x-transition.opacity
                                                    class="fixed inset-0 z-[80] bg-slate-900/40" @click="detailOpen = false"></div>
                                                <div x-show="detailOpen" x-cloak x-transition
                                                    class="fixed inset-x-3 top-6 z-[81] mx-auto max-h-[calc(100vh-3rem)] w-full max-w-3xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl sm:top-10">
                                                    <div class="flex max-h-[calc(100vh-3rem)] flex-col">
                                                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
                                                            <div class="min-w-0">
                                                                <h3 class="truncate text-lg font-black text-slate-900">
                                                                    Recipient Details
                                                                </h3>
                                                                <p class="mt-1 text-xs text-slate-500">
                                                                    {{ $assignment->title }} - {{ number_format((int) ($assignment->students_count ?? $assignment->students->count())) }} recipient{{ (int) ($assignment->students_count ?? $assignment->students->count()) === 1 ? '' : 's' }}, {{ number_format($submittedStudents->count()) }} file{{ $submittedStudents->count() === 1 ? '' : 's' }} received.
                                                                </p>
                                                            </div>
                                                            <button type="button" @click="detailOpen = false"
                                                                class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50"
                                                                aria-label="Close recipient details">
                                                                &times;
                                                            </button>
                                                        </div>

                                                        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                                                            @if ($assignment->students->isNotEmpty())
                                                                <div class="space-y-3">
                                                                    @foreach ($assignment->students as $student)
                                                                        @php
                                                                            $hasSubmission = filled($student->pivot?->submission_file_path);
                                                                            $fileSize = (int) ($student->pivot?->submission_file_size ?? 0);
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
                                                                        <article class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                                                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                                                <div class="min-w-0">
                                                                                    <div class="flex flex-wrap items-center gap-2">
                                                                                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                                                                            Student
                                                                                        </span>
                                                                                        <span class="text-sm font-black text-slate-900">
                                                                                            {{ $student->name }}
                                                                                        </span>
                                                                                        @if ($hasSubmission)
                                                                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                                                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                                                                Submitted
                                                                                            </span>
                                                                                        @else
                                                                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500 ring-1 ring-slate-200">
                                                                                                Pending
                                                                                            </span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="mt-1 text-xs text-slate-400">
                                                                                        {{ $student->email ?? 'No email' }}
                                                                                    </div>
                                                                                    <div class="mt-3">
                                                                                        @if ($hasSubmission)
                                                                                            <a href="{{ \Illuminate\Support\Facades\Storage::url($student->pivot->submission_file_path) }}"
                                                                                                target="_blank"
                                                                                                class="inline-flex max-w-full items-center gap-2 rounded-xl border border-emerald-100 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                                                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                                                                                    <path d="M14 2v6h6" />
                                                                                                </svg>
                                                                                                <span class="truncate">
                                                                                                    {{ $student->pivot->submission_file_name ?: 'Submitted file' }}
                                                                                                </span>
                                                                                            </a>
                                                                                        @else
                                                                                            <span class="inline-flex rounded-xl border border-dashed border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-500">
                                                                                                No file submitted yet
                                                                                            </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                <div class="shrink-0 rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-500 ring-1 ring-slate-100">
                                                                                    @if ($hasSubmission)
                                                                                        <div>{{ $fileSizeLabel }}</div>
                                                                                        <div class="mt-1">
                                                                                            {{ $student->pivot?->submitted_at ? \Illuminate\Support\Carbon::parse($student->pivot->submitted_at)->format('M d, Y h:i A') : 'No date' }}
                                                                                        </div>
                                                                                    @else
                                                                                        <div>Awaiting file</div>
                                                                                        <div class="mt-1">Not submitted</div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </article>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                                                                    <h4 class="text-base font-black text-slate-900">No recipients yet</h4>
                                                                    <p class="mt-2 text-sm text-slate-500">Assigned students will appear here after this assignment is saved.</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3">
                                                @if ($dueAt)
                                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $dueClass }}">
                                                        {{ $dueAt->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                                        No due date
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 text-slate-700">
                                                <div class="font-semibold text-slate-900">
                                                    {{ $assignment->created_at?->format('M d, Y') }}
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    {{ $assignment->created_at?->format('h:i A') }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="flex justify-end">
                                                    <button type="button" @click="editOpen = true"
                                                        class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                        Edit
                                                    </button>
                                                </div>

                                                <div x-show="editOpen" x-cloak x-transition.opacity
                                                    class="fixed inset-0 z-[80] bg-slate-900/40" @click="editOpen = false"></div>
                                                <div x-show="editOpen" x-cloak x-transition
                                                    class="fixed inset-x-3 top-6 z-[81] mx-auto max-h-[calc(100vh-3rem)] w-full max-w-3xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl sm:top-10">
                                                    <form method="POST"
                                                        action="{{ route('teacher.assignments.update', $assignment) }}"
                                                        class="js-assignment-edit-form flex max-h-[calc(100vh-3rem)] flex-col"
                                                        data-assignment="{{ $assignment->title }}">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                                                            <div class="min-w-0">
                                                                <h3 class="truncate text-2xl font-black text-slate-900">Edit Assignment</h3>
                                                                <p class="mt-2 text-sm text-slate-500">Update the assignment details and recipients.</p>
                                                            </div>
                                                            <button type="button" @click="editOpen = false"
                                                                class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-slate-200 text-xl font-bold text-slate-600 hover:bg-slate-50"
                                                                aria-label="Close edit assignment">
                                                                &times;
                                                            </button>
                                                        </div>

                                                        <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-6 py-5">
                                                            <div class="space-y-2">
                                                                <label for="edit_assignment_title_{{ $assignment->id }}" class="text-sm font-semibold text-slate-700">Title</label>
                                                                <input id="edit_assignment_title_{{ $assignment->id }}" name="title" type="text"
                                                                    value="{{ $assignment->title }}" maxlength="255"
                                                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                            </div>

                                                            <div class="grid gap-5 sm:grid-cols-2">
                                                                <div class="space-y-2">
                                                                    <label for="edit_assignment_subject_id_{{ $assignment->id }}" class="text-sm font-semibold text-slate-700">Subject</label>
                                                                    <select id="edit_assignment_subject_id_{{ $assignment->id }}" name="subject_id"
                                                                        class="js-assignment-edit-subject w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                        <option value="">General assignment</option>
                                                                        @foreach ($subjectOptions as $subject)
                                                                            <option value="{{ $subject['id'] }}" @selected((int) ($assignment->subject_id ?? 0) === (int) $subject['id'])>
                                                                                {{ $subject['name'] }}
                                                                                {{ $subject['code'] !== '' ? ' (' . $subject['code'] . ')' : '' }}
                                                                                {{ $subject['class_label'] !== '' ? ' | ' . $subject['class_label'] : '' }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="space-y-2">
                                                                    <label for="edit_assignment_due_at_{{ $assignment->id }}" class="text-sm font-semibold text-slate-700">Due Date</label>
                                                                    <input id="edit_assignment_due_at_{{ $assignment->id }}" type="date" name="due_at"
                                                                        value="{{ $assignment->due_at?->format('Y-m-d') }}"
                                                                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                </div>
                                                            </div>

                                                            <div class="space-y-2">
                                                                <label for="edit_assignment_description_{{ $assignment->id }}" class="text-sm font-semibold text-slate-700">Description</label>
                                                                <textarea id="edit_assignment_description_{{ $assignment->id }}" name="description" rows="5" maxlength="5000"
                                                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ $assignment->description }}</textarea>
                                                            </div>

                                                            <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                                <div class="flex flex-wrap items-center justify-between gap-3">
                                                                    <div>
                                                                        <h4 class="text-sm font-black text-slate-900">Student List</h4>
                                                                        <p class="mt-1 text-xs font-medium text-slate-500">Choose the recipients for this assignment.</p>
                                                                    </div>
                                                                    <div class="js-assignment-edit-count rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700">
                                                                        0 selected
                                                                    </div>
                                                                </div>

                                                                <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto_auto]">
                                                                    <input type="text" placeholder="Search by student or class"
                                                                        class="js-assignment-edit-search w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    <button type="button"
                                                                        class="js-assignment-edit-select-visible inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                                                        Select visible
                                                                    </button>
                                                                    <button type="button"
                                                                        class="js-assignment-edit-clear-visible inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                                                        Clear visible
                                                                    </button>
                                                                </div>

                                                                <div class="max-h-[22rem] space-y-3 overflow-auto pr-1">
                                                                    @php
                                                                        $assignmentStudentIds = $assignment->students->pluck('id')->map(fn($id) => (int) $id)->all();
                                                                    @endphp
                                                                    @foreach ($studentOptions as $student)
                                                                        @php
                                                                            $editSubjectIds = implode(',', $student['subject_ids'] ?? []);
                                                                            $editSubjectNames = collect($student['subject_names'] ?? [])->take(3)->all();
                                                                            $editChecked = in_array((int) $student['id'], $assignmentStudentIds, true);
                                                                        @endphp
                                                                        <label data-edit-student-card
                                                                            data-student-name="{{ strtolower($student['name']) }}"
                                                                            data-student-class="{{ strtolower($student['class_label']) }}"
                                                                            data-subject-ids="{{ $editSubjectIds }}"
                                                                            class="flex cursor-pointer items-start gap-3 rounded-2xl border {{ $editChecked ? 'border-indigo-300 bg-indigo-50/70' : 'border-slate-200 bg-white' }} px-4 py-3 transition hover:border-indigo-300 hover:bg-indigo-50/50">
                                                                            <input type="checkbox" name="student_ids[]" value="{{ $student['id'] }}"
                                                                                {{ $editChecked ? 'checked' : '' }}
                                                                                class="js-assignment-edit-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
                                                                            <div class="min-w-0 flex-1">
                                                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                                                    <div class="text-sm font-semibold text-slate-900">{{ $student['name'] }}</div>
                                                                                    <span class="rounded-full border border-sky-100 bg-sky-50 px-2.5 py-1 text-[11px] font-semibold text-sky-700">
                                                                                        {{ $student['class_label'] }}
                                                                                    </span>
                                                                                </div>
                                                                                @if ($editSubjectNames !== [])
                                                                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                                                                        @foreach ($editSubjectNames as $subjectName)
                                                                                            <span class="rounded-full border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700">
                                                                                                {{ $subjectName }}
                                                                                            </span>
                                                                                        @endforeach
                                                                                        @if (count($student['subject_names'] ?? []) > count($editSubjectNames))
                                                                                            <span class="text-[11px] font-semibold text-slate-400">
                                                                                                +{{ count($student['subject_names'] ?? []) - count($editSubjectNames) }} more
                                                                                            </span>
                                                                                        @endif
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-5">
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
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-5">
                        {{ $assignments->links() }}
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-12 text-center">
                        <div class="text-lg font-bold text-slate-900">No assignments posted yet</div>
                        <p class="mt-2 text-sm text-slate-500">
                            The assignments you send from this page will appear here for quick review.
                        </p>
                    </div>
                @endif
            </section>
        </div>
    </div>

    @php
        $assignmentPageData = [
            'validationErrors' => $errors->all(),
            'flash' => [
                'success' => session('success'),
                'error' => $errors->any() ? $errors->first() : '',
                'action' => session('assignment_action', ''),
            ],
        ];
    @endphp
    <script id="teacher-assignment-data" type="application/json">{!! json_encode($assignmentPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('teacher-assignment-form');
            const dataNode = document.getElementById('teacher-assignment-data');
            const hasSwal = typeof window.Swal !== 'undefined';
            const isEditing = @json($isEditing);
            let pageData = {
                validationErrors: [],
                flash: {
                    success: '',
                    error: '',
                },
            };

            if (dataNode) {
                try {
                    pageData = JSON.parse(dataNode.textContent || '{}');
                } catch (error) {
                    console.error('Unable to parse teacher assignment page data.', error);
                }
            }

            const showAlert = (options) => {
                const config = {
                    icon: options.icon || 'success',
                    title: options.title || 'Notification',
                    text: options.text || '',
                    confirmButtonText: options.confirmButtonText || 'OK',
                    confirmButtonColor: options.confirmButtonColor || '#4f46e5',
                };

                if (hasSwal) {
                    return window.Swal.fire(config);
                }

                window.alert(`${config.title}\n\n${config.text}`);
                return Promise.resolve({ isConfirmed: true });
            };

            const showConfirm = (options) => {
                const config = {
                    icon: options.icon || 'question',
                    title: options.title || 'Are you sure?',
                    text: options.text || '',
                    showCancelButton: true,
                    confirmButtonText: options.confirmButtonText || 'Yes',
                    cancelButtonText: options.cancelButtonText || 'Cancel',
                    confirmButtonColor: options.confirmButtonColor || '#4f46e5',
                    cancelButtonColor: options.cancelButtonColor || '#94a3b8',
                };

                if (hasSwal) {
                    return window.Swal.fire(config).then((result) => Boolean(result && result.isConfirmed));
                }

                return Promise.resolve(window.confirm(`${config.title}\n\n${config.text}`));
            };

            const queueAlerts = (items) => {
                const alerts = Array.isArray(items) ? items.filter(Boolean) : [];
                if (alerts.length === 0) {
                    return Promise.resolve();
                }

                document.querySelectorAll('.js-inline-flash').forEach((element) => {
                    element.classList.add('hidden');
                });

                return alerts.reduce((chain, item) => {
                    return chain.then(() => showAlert(item));
                }, Promise.resolve());
            };

            const flash = pageData.flash || {};
            const validationErrors = Array.isArray(pageData.validationErrors) ? pageData.validationErrors : [];
            const alertQueue = [];

            if (String(flash.success || '').trim() !== '') {
                alertQueue.push({
                    icon: 'success',
                    title: String(flash.action || '') === 'updated' ? 'Assignment updated' : 'Assignment posted',
                    text: String(flash.success),
                    confirmButtonText: 'Great',
                });
            }

            if (String(flash.error || '').trim() !== '' && validationErrors.length === 0) {
                alertQueue.push({
                    icon: 'error',
                    title: 'Unable to post assignment',
                    text: String(flash.error),
                });
            }

            if (validationErrors.length > 0) {
                alertQueue.push({
                    icon: 'error',
                    title: 'Validation Error',
                    text: String(validationErrors[0] || ''),
                });
            }

            queueAlerts(alertQueue);

            const setupAssignmentStudentPicker = (targetForm) => {
                if (!targetForm) {
                    return;
                }

                const targetSubject = targetForm.querySelector('.js-assignment-edit-subject');
                const targetSearch = targetForm.querySelector('.js-assignment-edit-search');
                const targetSelectVisible = targetForm.querySelector('.js-assignment-edit-select-visible');
                const targetClearVisible = targetForm.querySelector('.js-assignment-edit-clear-visible');
                const targetCount = targetForm.querySelector('.js-assignment-edit-count');
                const targetCards = Array.from(targetForm.querySelectorAll('[data-edit-student-card]'));

                const readTargetSubjectIds = (card) => {
                    return String(card.dataset.subjectIds || '')
                        .split(',')
                        .map((value) => Number(value.trim()))
                        .filter((value) => Number.isFinite(value) && value > 0);
                };

                const updateTargetSelection = () => {
                    const checkedCount = targetCards.filter((card) => {
                        const checkbox = card.querySelector('.js-assignment-edit-checkbox');
                        return checkbox && checkbox.checked;
                    }).length;

                    if (targetCount) {
                        targetCount.textContent = `${checkedCount} selected`;
                    }

                    targetCards.forEach((card) => {
                        const checkbox = card.querySelector('.js-assignment-edit-checkbox');
                        if (!checkbox) {
                            return;
                        }

                        card.classList.toggle('border-indigo-300', checkbox.checked);
                        card.classList.toggle('bg-indigo-50/70', checkbox.checked);
                        card.classList.toggle('border-slate-200', !checkbox.checked);
                        card.classList.toggle('bg-white', !checkbox.checked);
                    });
                };

                const applyTargetFilters = () => {
                    const subjectId = Number(targetSubject?.value || 0) || 0;
                    const searchValue = String(targetSearch?.value || '').trim().toLowerCase();

                    targetCards.forEach((card) => {
                        const name = String(card.dataset.studentName || '');
                        const classLabel = String(card.dataset.studentClass || '');
                        const subjectIds = readTargetSubjectIds(card);
                        const matchesSubject = subjectId === 0 || subjectIds.includes(subjectId);
                        const matchesSearch = searchValue === '' || name.includes(searchValue) || classLabel.includes(searchValue);
                        const visible = matchesSubject && matchesSearch;

                        card.classList.toggle('hidden', !visible);
                    });
                };

                targetCards.forEach((card) => {
                    card.querySelector('.js-assignment-edit-checkbox')?.addEventListener('change', updateTargetSelection);
                });

                targetSubject?.addEventListener('change', applyTargetFilters);
                targetSearch?.addEventListener('input', applyTargetFilters);

                targetSelectVisible?.addEventListener('click', () => {
                    targetCards.forEach((card) => {
                        if (card.classList.contains('hidden')) {
                            return;
                        }

                        const checkbox = card.querySelector('.js-assignment-edit-checkbox');
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });

                    updateTargetSelection();
                });

                targetClearVisible?.addEventListener('click', () => {
                    targetCards.forEach((card) => {
                        if (card.classList.contains('hidden')) {
                            return;
                        }

                        const checkbox = card.querySelector('.js-assignment-edit-checkbox');
                        if (checkbox) {
                            checkbox.checked = false;
                        }
                    });

                    updateTargetSelection();
                });

                targetForm.dataset.confirmed = '1';
                applyTargetFilters();
                updateTargetSelection();
            };

            document.querySelectorAll('.js-assignment-edit-form').forEach(setupAssignmentStudentPicker);

            if (!form) {
                return;
            }

            const subjectSelect = document.getElementById('assignment_subject_id');
            const searchInput = document.getElementById('assignment-student-search');
            const selectVisibleButton = document.getElementById('assignment-select-visible');
            const clearVisibleButton = document.getElementById('assignment-clear-visible');
            const selectedCount = document.getElementById('assignment-selected-count');
            const cards = Array.from(form.querySelectorAll('[data-student-card]'));

            const readSubjectIds = (card) => {
                return String(card.dataset.subjectIds || '')
                    .split(',')
                    .map((value) => Number(value.trim()))
                    .filter((value) => Number.isFinite(value) && value > 0);
            };

            const updateSelectionState = () => {
                const checkedCount = cards.filter((card) => {
                    const checkbox = card.querySelector('.assignment-student-checkbox');
                    return checkbox && checkbox.checked;
                }).length;

                if (selectedCount) {
                    selectedCount.textContent = `${checkedCount} selected`;
                }

                cards.forEach((card) => {
                    const checkbox = card.querySelector('.assignment-student-checkbox');
                    if (!checkbox) {
                        return;
                    }

                    card.classList.toggle('border-indigo-300', checkbox.checked);
                    card.classList.toggle('bg-indigo-50/70', checkbox.checked);
                    card.classList.toggle('border-slate-200', !checkbox.checked);
                    card.classList.toggle('bg-white', !checkbox.checked);
                });
            };

            const applyFilters = () => {
                const subjectId = Number(subjectSelect?.value || 0) || 0;
                const searchValue = String(searchInput?.value || '').trim().toLowerCase();

                cards.forEach((card) => {
                    const name = String(card.dataset.studentName || '');
                    const classLabel = String(card.dataset.studentClass || '');
                    const subjectIds = readSubjectIds(card);
                    const matchesSubject = subjectId === 0 || subjectIds.includes(subjectId);
                    const matchesSearch = searchValue === '' || name.includes(searchValue) || classLabel.includes(searchValue);
                    const visible = matchesSubject && matchesSearch;

                    card.classList.toggle('hidden', !visible);
                });
            };

            cards.forEach((card) => {
                const checkbox = card.querySelector('.assignment-student-checkbox');
                checkbox?.addEventListener('change', updateSelectionState);
            });

            subjectSelect?.addEventListener('change', applyFilters);
            searchInput?.addEventListener('input', applyFilters);

            selectVisibleButton?.addEventListener('click', () => {
                cards.forEach((card) => {
                    if (card.classList.contains('hidden')) {
                        return;
                    }

                    const checkbox = card.querySelector('.assignment-student-checkbox');
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });

                updateSelectionState();
            });

            clearVisibleButton?.addEventListener('click', () => {
                cards.forEach((card) => {
                    if (card.classList.contains('hidden')) {
                        return;
                    }

                    const checkbox = card.querySelector('.assignment-student-checkbox');
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                });

                updateSelectionState();
            });

            form.addEventListener('submit', (event) => {
                if (form.dataset.confirmed === '1') {
                    form.dataset.confirmed = '0';
                    return;
                }

                event.preventDefault();

                const checkedCount = cards.filter((card) => {
                    const checkbox = card.querySelector('.assignment-student-checkbox');
                    return checkbox && checkbox.checked;
                }).length;
                const targetLabel = `${checkedCount} student${checkedCount === 1 ? '' : 's'}`;

                showConfirm({
                    title: isEditing ? 'Save assignment changes?' : 'Post assignment?',
                    text: checkedCount > 0
                        ? (isEditing
                            ? `Update this assignment for ${targetLabel}?`
                            : `Send this assignment and alert ${targetLabel}?`)
                        : (isEditing ? 'Save this assignment now?' : 'Send this assignment now?'),
                    confirmButtonText: isEditing ? 'Yes, save it' : 'Yes, post it',
                    cancelButtonText: 'Cancel',
                    icon: 'question',
                }).then((confirmed) => {
                    if (!confirmed) {
                        return;
                    }

                    form.dataset.confirmed = '1';
                    form.submit();
                });
            });

            applyFilters();
            updateSelectionState();
        });
    </script>
@endsection
