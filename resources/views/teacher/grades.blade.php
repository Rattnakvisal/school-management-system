@extends('layout.teacher.navbar')

@section('page')
    @php
        $subjectOptions = collect($subjectOptions ?? []);
        $studentOptions = collect($studentOptions ?? []);
        $classOptions = collect($classOptions ?? []);
        $assessmentTitleOptions = collect($assessmentTitleOptions ?? ['Homework Assignment', 'Quiz', 'Midterm', 'Final']);
        $gradeDetails = collect($gradeDetails ?? []);
        $editingGrade = $editingGrade ?? null;
        $isEditing = $editingGrade !== null;
        $selectedClassId = (string) old('class_id', $isEditing ? (string) ($editingGrade?->student?->school_class_id ?? '') : '');
        $selectedSubjectId = (string) old('subject_id', $isEditing ? (string) ($editingGrade->subject_id ?? '') : '');
        $selectedStudentId = (string) old('student_id', $isEditing ? (string) ($editingGrade->student_id ?? '') : '');
        $titleValue = (string) old('title', $isEditing ? (string) ($editingGrade->title ?? '') : '');
        $scoreValue = (string) old('score', $isEditing ? number_format((float) ($editingGrade->score ?? 0), 2, '.', '') : '');
        $maxScoreValue = (string) old('max_score', $isEditing ? number_format((float) ($editingGrade->max_score ?? 0), 2, '.', '') : '');
        $gradedAtValue = (string) old(
            'graded_at',
            $isEditing && $editingGrade?->graded_at ? $editingGrade->graded_at->format('Y-m-d') : now()->toDateString(),
        );
        $remarksValue = (string) old('remarks', $isEditing ? (string) ($editingGrade->remarks ?? '') : '');
        $gradeTotal = max(0, (int) ($stats['total'] ?? 0));
        $gradeAverage = isset($stats['average']) ? (float) $stats['average'] : null;
        $teacherGradeStatCards = [
            [
                'label' => 'Grades',
                'activeLabel' => 'Posted',
                'active' => $gradeTotal,
                'total' => $gradeTotal,
                'icon' => 'grades',
                'tone' => 'from-indigo-100 to-white text-indigo-600',
            ],
            [
                'label' => 'Students',
                'activeLabel' => 'Graded',
                'active' => (int) ($stats['students'] ?? 0),
                'total' => (int) ($stats['students'] ?? 0),
                'icon' => 'students',
                'tone' => 'from-emerald-100 to-white text-emerald-600',
            ],
            [
                'label' => 'Subjects',
                'activeLabel' => 'With grades',
                'active' => (int) ($stats['subjects'] ?? 0),
                'total' => (int) ($stats['subjects'] ?? 0),
                'icon' => 'subjects',
                'tone' => 'from-sky-100 to-white text-sky-600',
            ],
            [
                'label' => 'Average',
                'activeLabel' => 'Overall',
                'active' => $gradeAverage !== null ? (int) round($gradeAverage) : 0,
                'total' => 100,
                'displayActive' => $gradeAverage !== null ? number_format($gradeAverage, 1) . '%' : 'N/A',
                'hideTotal' => true,
                'icon' => 'average',
                'tone' => 'from-amber-100 to-white text-amber-600',
            ],
        ];
    @endphp

    <div class="teacher-stage teacher-grade-stage space-y-6">
        <section class="admin-page-header teacher-page-header">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Grades</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        Record student grades and automatically notify each student when a new grade is assigned.
                    </p>
                </div>
            </div>
        </section>

        <x-admin.stat-cards :cards="$teacherGradeStatCards" reveal-class="teacher-reveal" float-class="teacher-float"
            grid-class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4" />

        @if (session('success') && session('grade_action') !== 'updated')
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
                        <h2 class="text-lg font-black text-slate-900">{{ $isEditing ? 'Edit Grade' : 'Assign Grade' }}</h2>
                        <p class="mt-1 text-xs font-semibold text-slate-500">
                            {{ $isEditing ? 'Update the grade details and save the changes.' : 'Choose a student, enter the score, and send the result.' }}
                        </p>
                    </div>
                    <span class="rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ $isEditing ? 'Edit mode' : 'Alert enabled' }}
                    </span>
                </div>

                @if ($studentOptions->isNotEmpty())
                    <form method="POST"
                        action="{{ $isEditing ? route('teacher.grades.update', $editingGrade) : route('teacher.grades.store') }}"
                        class="mt-5 space-y-4"
                        id="teacher-grade-form">
                        @csrf
                        @if ($isEditing)
                            @method('PUT')
                        @endif

                        <div class="space-y-2">
                            <label for="grade_title" class="text-sm font-semibold text-slate-700">Assessment Title</label>
                            <select id="grade_title" name="title"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                <option value="">Select assessment</option>
                                @foreach ($assessmentTitleOptions as $assessmentTitle)
                                    <option value="{{ $assessmentTitle }}" {{ $titleValue === (string) $assessmentTitle ? 'selected' : '' }}>
                                        {{ $assessmentTitle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="grade_class_id" class="text-sm font-semibold text-slate-700">Class</label>
                                <select id="grade_class_id" name="class_id"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">All classes</option>
                                    @foreach ($classOptions as $classOption)
                                        <option value="{{ $classOption['id'] }}"
                                            {{ $selectedClassId === (string) $classOption['id'] ? 'selected' : '' }}>
                                            {{ $classOption['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="grade_subject_id" class="text-sm font-semibold text-slate-700">Subject</label>
                                <select id="grade_subject_id" name="subject_id"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">General result</option>
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
                                <label for="grade_graded_at" class="text-sm font-semibold text-slate-700">Graded Date</label>
                                <input id="grade_graded_at" type="date" name="graded_at" value="{{ $gradedAtValue }}"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="grade-student-search" class="text-sm font-semibold text-slate-700">Student Search</label>
                            <input id="grade-student-search" type="text" placeholder="Search by student or class"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>

                        <div class="space-y-2">
                            <label for="grade_student_id" class="text-sm font-semibold text-slate-700">Student</label>
                            <select id="grade_student_id" name="student_id"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                <option value="">Select student</option>
                                @foreach ($studentOptions as $student)
                                    <option value="{{ $student['id'] }}"
                                        data-class-id="{{ (int) ($student['class_id'] ?? 0) }}"
                                        data-subject-ids="{{ implode(',', $student['subject_ids'] ?? []) }}"
                                        data-name="{{ strtolower($student['name']) }}"
                                        data-class="{{ strtolower($student['class_label']) }}"
                                        {{ $selectedStudentId === (string) $student['id'] ? 'selected' : '' }}>
                                        {{ $student['name'] }} | {{ $student['class_label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="grade-student-detail"
                            class="hidden rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                            Selected Student
                                        </span>
                                        <span id="grade-detail-student-name" class="text-sm font-black text-slate-900"></span>
                                    </div>
                                    <div id="grade-detail-class" class="mt-1 text-xs font-semibold text-slate-500"></div>
                                    <div id="grade-detail-subjects" class="mt-3 flex flex-wrap gap-1.5"></div>
                                </div>
                                <div class="shrink-0 rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-500 ring-1 ring-slate-100">
                                    <div id="grade-detail-month">No month selected</div>
                                    <div id="grade-detail-total" class="mt-1">0 records</div>
                                </div>
                            </div>
                            <div id="grade-duplicate-warning"
                                class="mt-3 hidden rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">
                            </div>
                            <div class="mt-4">
                                <h3 class="text-xs font-black uppercase tracking-wide text-slate-500">This Month</h3>
                                <div id="grade-detail-records" class="mt-2 space-y-2"></div>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="grade_score" class="text-sm font-semibold text-slate-700">Score</label>
                                <input id="grade_score" type="number" name="score" step="0.01" min="0"
                                    value="{{ $scoreValue }}"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            </div>
                            <div class="space-y-2">
                                <label for="grade_max_score" class="text-sm font-semibold text-slate-700">Maximum Score</label>
                                <input id="grade_max_score" type="number" name="max_score" step="0.01" min="0.01"
                                    value="{{ $maxScoreValue }}"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="grade_remarks" class="text-sm font-semibold text-slate-700">Remarks</label>
                            <textarea id="grade_remarks" name="remarks" rows="5" maxlength="5000"
                                placeholder="Optional feedback for the student."
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ $remarksValue }}</textarea>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <button type="submit"
                                id="grade-submit-button"
                                class="inline-flex w-full flex-1 items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                {{ $isEditing ? 'Save Grade Changes' : 'Assign Grade And Notify Student' }}
                            </button>
                            @if ($isEditing)
                                <a href="{{ route('teacher.grades.index') }}"
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
                            Your student list will appear here once you are linked to classes or subjects.
                        </p>
                    </div>
                @endif
            </section>

            <section class="min-w-0 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Grade History</h2>
                        <p class="mt-1 text-xs font-semibold text-slate-500">Latest grade records you have assigned.</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">Latest first</span>
                </div>

                @if (session('success') && session('grade_action') === 'updated')
                    <div class="js-inline-flash mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if (($grades ?? null) && $grades->count() > 0)
                    <div class="overflow-hidden rounded-2xl border border-slate-200">
                        <div class="max-h-[44rem] overflow-auto">
                            <table class="w-full min-w-[1040px] text-left text-sm">
                                <thead class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-3 py-3 font-semibold">Assessment</th>
                                        <th class="px-3 py-3 font-semibold">Student</th>
                                        <th class="px-3 py-3 font-semibold">Subject</th>
                                        <th class="px-3 py-3 font-semibold">Score</th>
                                        <th class="px-3 py-3 font-semibold">Grade</th>
                                        <th class="px-3 py-3 font-semibold">Date</th>
                                        <th class="px-3 py-3 font-semibold text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($grades as $grade)
                                        @php
                                            $score = (float) ($grade->score ?? 0);
                                            $maxScore = (float) ($grade->max_score ?? 0);
                                            $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 1) : 0;
                                        @endphp
                                        <tr x-data="{ editOpen: false, studentPickerOpen: false }"
                                            class="align-top hover:bg-slate-50/80 {{ $isEditing && (int) $editingGrade->id === (int) $grade->id ? 'bg-indigo-50/40' : '' }}">
                                            <td class="px-3 py-3">
                                                <div class="font-semibold text-slate-900">{{ $grade->title }}</div>
                                                <div class="mt-1 line-clamp-2 text-xs text-slate-500">
                                                    {{ \Illuminate\Support\Str::limit((string) ($grade->remarks ?? 'No remarks.'), 120) }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="font-semibold text-slate-900">{{ $grade->student?->name ?? 'Student' }}</div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    {{ $grade->student?->schoolClass?->display_name ?? 'Unassigned class' }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="font-semibold text-slate-800">{{ $grade->subject?->name ?? 'General result' }}</div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    {{ trim((string) ($grade->subject?->code ?? '')) !== '' ? $grade->subject?->code : 'No subject code' }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="font-semibold text-slate-900">
                                                    {{ number_format($score, 2) }}/{{ number_format($maxScore, 2) }}
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500">{{ number_format($percentage, 1) }}%</div>
                                            </td>
                                            <td class="px-3 py-3">
                                                <span class="inline-flex rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                                    {{ $grade->grade_letter }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3 text-slate-700">
                                                <div class="font-semibold text-slate-900">{{ $grade->graded_at?->format('M d, Y') }}</div>
                                                <div class="mt-1 text-xs text-slate-500">{{ $grade->created_at?->format('h:i A') }}</div>
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
                                                        action="{{ route('teacher.grades.update', $grade) }}"
                                                        class="js-grade-edit-form flex max-h-[calc(100vh-3rem)] flex-col"
                                                        data-student="{{ $grade->student?->name ?? 'this student' }}">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                                                            <div class="min-w-0">
                                                                <h3 class="truncate text-2xl font-black text-slate-900">
                                                                    Edit Grade
                                                                </h3>
                                                                <p class="mt-2 text-sm text-slate-500">
                                                                    Update the grade record details.
                                                                </p>
                                                            </div>
                                                            <button type="button" @click="editOpen = false"
                                                                class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-slate-200 text-xl font-bold text-slate-600 hover:bg-slate-50"
                                                                aria-label="Close edit grade">
                                                                &times;
                                                            </button>
                                                        </div>

                                                        <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-6 py-5">
                                                            <div class="space-y-2">
                                                                <label for="edit_grade_title_{{ $grade->id }}"
                                                                    class="text-sm font-semibold text-slate-700">Assessment Title</label>
                                                                <select id="edit_grade_title_{{ $grade->id }}" name="title"
                                                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    @foreach ($assessmentTitleOptions as $assessmentTitle)
                                                                        <option value="{{ $assessmentTitle }}" @selected((string) $grade->title === (string) $assessmentTitle)>
                                                                            {{ $assessmentTitle }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="grid gap-5 sm:grid-cols-2">
                                                                <div class="space-y-2">
                                                                    <div class="flex items-center justify-between gap-3">
                                                                        <label for="edit_grade_student_id_{{ $grade->id }}"
                                                                            class="text-sm font-semibold text-slate-700">Student</label>
                                                                        <button type="button" @click="studentPickerOpen = true"
                                                                            class="inline-flex items-center gap-2 rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                                            Student List
                                                                        </button>
                                                                    </div>
                                                                    <select id="edit_grade_student_id_{{ $grade->id }}" name="student_id"
                                                                        class="js-edit-grade-student-select hidden"
                                                                        data-grade-id="{{ $grade->id }}">
                                                                        @foreach ($studentOptions as $student)
                                                                            <option value="{{ $student['id'] }}"
                                                                                data-class-id="{{ (int) ($student['class_id'] ?? 0) }}"
                                                                                data-subject-ids="{{ implode(',', $student['subject_ids'] ?? []) }}"
                                                                                data-name="{{ strtolower($student['name']) }}"
                                                                                data-class="{{ strtolower($student['class_label']) }}"
                                                                                @selected((int) $grade->student_id === (int) $student['id'])>
                                                                                {{ $student['name'] }} | {{ $student['class_label'] }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <button type="button" @click="studentPickerOpen = true"
                                                                        class="flex w-full items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-left transition hover:border-indigo-200 hover:bg-indigo-50/50">
                                                                        <span class="min-w-0">
                                                                            <span class="js-edit-grade-student-name block truncate text-sm font-black text-slate-900"
                                                                                data-grade-id="{{ $grade->id }}">
                                                                                {{ $grade->student?->name ?? 'Select student' }}
                                                                            </span>
                                                                            <span class="js-edit-grade-student-meta mt-1 block truncate text-xs font-semibold text-slate-500"
                                                                                data-grade-id="{{ $grade->id }}">
                                                                                {{ $grade->student?->schoolClass?->display_name ?? 'Open the student list to choose a student.' }}
                                                                            </span>
                                                                        </span>
                                                                        <span class="shrink-0 rounded-full bg-white px-3 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                                                            Browse
                                                                        </span>
                                                                    </button>

                                                                    <div x-show="studentPickerOpen" x-cloak x-transition.opacity
                                                                        class="fixed inset-0 z-[90] bg-slate-900/40" @click="studentPickerOpen = false"></div>
                                                                    <div x-show="studentPickerOpen" x-cloak x-transition
                                                                        class="fixed inset-x-3 top-6 z-[91] mx-auto flex max-h-[calc(100vh-3rem)] w-full max-w-4xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl sm:top-10">
                                                                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
                                                                            <div class="min-w-0">
                                                                                <h4 class="truncate text-lg font-black text-slate-900">Student List</h4>
                                                                                <p class="mt-1 text-xs text-slate-500">
                                                                                    Choose the student for this grade record.
                                                                                </p>
                                                                            </div>
                                                                            <button type="button" @click="studentPickerOpen = false"
                                                                                class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50"
                                                                                aria-label="Close student list">
                                                                                &times;
                                                                            </button>
                                                                        </div>

                                                                        <div class="space-y-4 overflow-y-auto px-5 py-4">
                                                                            <input type="text" placeholder="Search by student or class"
                                                                                class="js-edit-grade-student-search w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                                data-grade-id="{{ $grade->id }}">

                                                                            <div class="overflow-hidden rounded-2xl border border-slate-200">
                                                                                <div class="student-table-scroller max-h-[28rem] overflow-auto">
                                                                                    <table class="student-table w-full min-w-[720px] whitespace-nowrap text-left text-sm">
                                                                                        <thead class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                                                                            <tr>
                                                                                                <th class="px-3 py-3 font-semibold">Student</th>
                                                                                                <th class="px-3 py-3 font-semibold">Class</th>
                                                                                                <th class="px-3 py-3 font-semibold">Subjects</th>
                                                                                                <th class="px-3 py-3 text-right font-semibold">Action</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody class="divide-y divide-slate-100 bg-white">
                                                                                            @foreach ($studentOptions as $student)
                                                                                                @php
                                                                                                    $studentSubjectNames = collect($student['subject_names'] ?? [])->take(3)->all();
                                                                                                @endphp
                                                                                                <tr class="js-edit-grade-student-row align-top hover:bg-slate-50/80"
                                                                                                    data-grade-id="{{ $grade->id }}"
                                                                                                    data-student-id="{{ $student['id'] }}"
                                                                                                    data-class-id="{{ (int) ($student['class_id'] ?? 0) }}"
                                                                                                    data-subject-ids="{{ implode(',', $student['subject_ids'] ?? []) }}"
                                                                                                    data-name="{{ strtolower($student['name']) }}"
                                                                                                    data-class="{{ strtolower($student['class_label']) }}">
                                                                                                    <td class="px-3 py-3">
                                                                                                        <div class="flex items-center gap-3">
                                                                                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-indigo-50 text-xs font-black text-indigo-600 ring-1 ring-indigo-100">
                                                                                                                {{ strtoupper(substr($student['name'], 0, 2)) }}
                                                                                                            </span>
                                                                                                            <div class="min-w-0">
                                                                                                                <div class="truncate font-semibold text-slate-800">
                                                                                                                    {{ $student['name'] }}
                                                                                                                </div>
                                                                                                                <div class="text-xs text-slate-400">
                                                                                                                    ID #{{ str_pad((string) $student['id'], 7, '0', STR_PAD_LEFT) }}
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="px-3 py-3">
                                                                                                        <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                                                                            {{ $student['class_label'] }}
                                                                                                        </span>
                                                                                                    </td>
                                                                                                    <td class="px-3 py-3">
                                                                                                        <div class="flex max-w-[20rem] flex-wrap gap-1.5">
                                                                                                            @forelse ($studentSubjectNames as $subjectName)
                                                                                                                <span class="rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px] font-semibold text-indigo-700">
                                                                                                                    {{ $subjectName }}
                                                                                                                </span>
                                                                                                            @empty
                                                                                                                <span class="text-xs font-semibold text-slate-400">No subjects</span>
                                                                                                            @endforelse
                                                                                                            @if (count($student['subject_names'] ?? []) > count($studentSubjectNames))
                                                                                                                <span class="text-[11px] font-semibold text-slate-500">
                                                                                                                    +{{ count($student['subject_names'] ?? []) - count($studentSubjectNames) }} more
                                                                                                                </span>
                                                                                                            @endif
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="px-3 py-3 text-right">
                                                                                                        <button type="button"
                                                                                                            class="js-edit-grade-select-student rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100"
                                                                                                            data-grade-id="{{ $grade->id }}"
                                                                                                            data-student-id="{{ $student['id'] }}"
                                                                                                            @click="studentPickerOpen = false">
                                                                                                            Select
                                                                                                        </button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                            <tr class="js-edit-grade-empty-row hidden" data-grade-id="{{ $grade->id }}">
                                                                                                <td colspan="4" class="px-3 py-10 text-center text-sm text-slate-500">
                                                                                                    No students match the current filters.
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="space-y-2">
                                                                    <label for="edit_grade_subject_id_{{ $grade->id }}"
                                                                        class="text-sm font-semibold text-slate-700">Subject</label>
                                                                    <select id="edit_grade_subject_id_{{ $grade->id }}" name="subject_id"
                                                                        class="js-edit-grade-subject-select w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                        data-grade-id="{{ $grade->id }}">
                                                                        <option value="">General result</option>
                                                                        @foreach ($subjectOptions as $subject)
                                                                            <option value="{{ $subject['id'] }}" @selected((int) ($grade->subject_id ?? 0) === (int) $subject['id'])>
                                                                                {{ $subject['name'] }}
                                                                                {{ $subject['code'] !== '' ? ' (' . $subject['code'] . ')' : '' }}
                                                                                {{ $subject['class_label'] !== '' ? ' | ' . $subject['class_label'] : '' }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="grid gap-5 sm:grid-cols-2">
                                                                <div class="space-y-2">
                                                                    <label for="edit_grade_graded_at_{{ $grade->id }}"
                                                                        class="text-sm font-semibold text-slate-700">Graded Date</label>
                                                                    <input id="edit_grade_graded_at_{{ $grade->id }}" type="date" name="graded_at"
                                                                        value="{{ $grade->graded_at?->format('Y-m-d') }}"
                                                                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                </div>

                                                                <div class="grid gap-5 sm:grid-cols-2">
                                                                    <div class="space-y-2">
                                                                        <label for="edit_grade_score_{{ $grade->id }}"
                                                                            class="text-sm font-semibold text-slate-700">Score</label>
                                                                        <input id="edit_grade_score_{{ $grade->id }}" type="number" name="score"
                                                                            step="0.01" min="0" value="{{ number_format((float) ($grade->score ?? 0), 2, '.', '') }}"
                                                                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    </div>

                                                                    <div class="space-y-2">
                                                                        <label for="edit_grade_max_score_{{ $grade->id }}"
                                                                            class="text-sm font-semibold text-slate-700">Maximum Score</label>
                                                                        <input id="edit_grade_max_score_{{ $grade->id }}" type="number" name="max_score"
                                                                            step="0.01" min="0.01" value="{{ number_format((float) ($grade->max_score ?? 0), 2, '.', '') }}"
                                                                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="space-y-2">
                                                                <label for="edit_grade_remarks_{{ $grade->id }}"
                                                                    class="text-sm font-semibold text-slate-700">Remarks</label>
                                                                <textarea id="edit_grade_remarks_{{ $grade->id }}" name="remarks" rows="5" maxlength="5000"
                                                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                    placeholder="Optional feedback for the student.">{{ $grade->remarks }}</textarea>
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
                        {{ $grades->links() }}
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-12 text-center">
                        <div class="text-lg font-bold text-slate-900">No grades assigned yet</div>
                        <p class="mt-2 text-sm text-slate-500">
                            Grades you record here will appear in this history and on the student portal.
                        </p>
                    </div>
                @endif
            </section>
        </div>
    </div>

    @php
        $gradePageData = [
            'validationErrors' => $errors->all(),
            'flash' => [
                'success' => session('success'),
                'error' => $errors->any() ? $errors->first() : '',
                'action' => session('grade_action', ''),
            ],
            'currentGradeId' => $isEditing ? (int) $editingGrade->id : 0,
            'students' => $studentOptions,
            'subjects' => $subjectOptions,
            'gradeDetails' => $gradeDetails,
        ];
    @endphp
    <script id="teacher-grade-data" type="application/json">{!! json_encode($gradePageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('teacher-grade-form');
            const dataNode = document.getElementById('teacher-grade-data');
            const titleSelect = document.getElementById('grade_title');
            const classSelect = document.getElementById('grade_class_id');
            const subjectSelect = document.getElementById('grade_subject_id');
            const studentSelect = document.getElementById('grade_student_id');
            const searchInput = document.getElementById('grade-student-search');
            const gradedAtInput = document.getElementById('grade_graded_at');
            const submitButton = document.getElementById('grade-submit-button');
            const detailPanel = document.getElementById('grade-student-detail');
            const detailName = document.getElementById('grade-detail-student-name');
            const detailClass = document.getElementById('grade-detail-class');
            const detailSubjects = document.getElementById('grade-detail-subjects');
            const detailMonth = document.getElementById('grade-detail-month');
            const detailTotal = document.getElementById('grade-detail-total');
            const detailRecords = document.getElementById('grade-detail-records');
            const duplicateWarning = document.getElementById('grade-duplicate-warning');
            const hasSwal = typeof window.Swal !== 'undefined';
            const isEditing = @json($isEditing);
            let pageData = {
                validationErrors: [],
                flash: { success: '', error: '', action: '' },
                currentGradeId: 0,
                students: [],
                subjects: [],
                gradeDetails: [],
            };

            if (dataNode) {
                try {
                    pageData = JSON.parse(dataNode.textContent || '{}');
                } catch (error) {
                    console.error('Unable to parse teacher grade page data.', error);
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

                return alerts.reduce((chain, item) => chain.then(() => showAlert(item)), Promise.resolve());
            };

            const flash = pageData.flash || {};
            const validationErrors = Array.isArray(pageData.validationErrors) ? pageData.validationErrors : [];
            const alertQueue = [];
            const currentGradeId = Number(pageData.currentGradeId || 0) || 0;
            const studentsById = new Map((Array.isArray(pageData.students) ? pageData.students : []).map((student) => [
                String(student.id),
                student,
            ]));
            const subjectsById = new Map((Array.isArray(pageData.subjects) ? pageData.subjects : []).map((subject) => [
                String(subject.id),
                subject,
            ]));
            const gradeDetails = Array.isArray(pageData.gradeDetails) ? pageData.gradeDetails : [];

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const selectedMonthKey = () => {
                const rawDate = String(gradedAtInput?.value || '').trim();

                if (/^\d{4}-\d{2}-\d{2}$/.test(rawDate)) {
                    return rawDate.slice(0, 7);
                }

                const today = new Date();
                return `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}`;
            };

            const monthLabel = (monthKey) => {
                if (!/^\d{4}-\d{2}$/.test(monthKey)) {
                    return 'No month selected';
                }

                const [year, month] = monthKey.split('-').map((value) => Number(value));
                return new Date(year, month - 1, 1).toLocaleDateString(undefined, {
                    month: 'long',
                    year: 'numeric',
                });
            };

            const setSubmitBlocked = (blocked) => {
                if (!submitButton) {
                    return;
                }

                submitButton.disabled = blocked;
                submitButton.classList.toggle('cursor-not-allowed', blocked);
                submitButton.classList.toggle('opacity-60', blocked);
                submitButton.classList.toggle('hover:bg-indigo-500', !blocked);
            };

            const updateStudentDetail = () => {
                if (!detailPanel || !studentSelect) {
                    return false;
                }

                const studentId = String(studentSelect.value || '');
                const student = studentsById.get(studentId);
                const subjectId = Number(subjectSelect?.value || 0) || 0;
                const title = String(titleSelect?.value || '').trim();
                const monthKey = selectedMonthKey();

                if (!student) {
                    detailPanel.classList.add('hidden');
                    setSubmitBlocked(false);
                    return false;
                }

                detailPanel.classList.remove('hidden');

                if (detailName) {
                    detailName.textContent = String(student.name || 'Student');
                }

                if (detailClass) {
                    detailClass.textContent = String(student.class_label || 'Unassigned class');
                }

                if (detailSubjects) {
                    const subjectNames = Array.isArray(student.subject_names) ? student.subject_names : [];
                    detailSubjects.innerHTML = subjectNames.length > 0
                        ? subjectNames.map((subjectName) => `
                            <span class="rounded-full border border-indigo-100 bg-white px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                ${escapeHtml(subjectName)}
                            </span>
                        `).join('')
                        : '<span class="text-xs font-semibold text-slate-400">No linked subjects</span>';
                }

                const monthlyRecords = gradeDetails.filter((grade) => {
                    const gradeSubjectId = Number(grade.subject_id || 0) || 0;
                    return String(grade.student_id || '') === studentId
                        && gradeSubjectId === subjectId
                        && String(grade.graded_month || '') === monthKey;
                });
                const duplicate = monthlyRecords.find((grade) => {
                    return Number(grade.id || 0) !== currentGradeId
                        && String(grade.title || '') === title;
                });
                const selectedSubject = subjectId > 0 ? subjectsById.get(String(subjectId)) : null;
                const subjectLabel = selectedSubject
                    ? String(selectedSubject.name || 'Selected subject')
                    : 'General result';

                if (detailMonth) {
                    detailMonth.textContent = `${monthLabel(monthKey)}`;
                }

                if (detailTotal) {
                    detailTotal.textContent = `${monthlyRecords.length} record${monthlyRecords.length === 1 ? '' : 's'}`;
                }

                if (duplicateWarning) {
                    if (duplicate) {
                        duplicateWarning.textContent = `${student.name} already has ${title} for ${subjectLabel} in ${monthLabel(monthKey)}. Edit the existing grade instead.`;
                        duplicateWarning.classList.remove('hidden');
                    } else {
                        duplicateWarning.textContent = '';
                        duplicateWarning.classList.add('hidden');
                    }
                }

                if (detailRecords) {
                    if (monthlyRecords.length > 0) {
                        detailRecords.innerHTML = monthlyRecords.map((grade) => `
                            <article class="rounded-xl border border-slate-200 bg-white px-3 py-2">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                                ${escapeHtml(grade.title)}
                                            </span>
                                            <span class="text-sm font-black text-slate-900">
                                                ${escapeHtml(grade.subject_name || subjectLabel)}
                                            </span>
                                        </div>
                                        <div class="mt-1 line-clamp-2 text-xs text-slate-500">
                                            ${escapeHtml(grade.remarks || 'No remarks.')}
                                        </div>
                                    </div>
                                    <div class="shrink-0 rounded-xl bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600 ring-1 ring-slate-100">
                                        <div>${escapeHtml(grade.score)}/${escapeHtml(grade.max_score)} (${escapeHtml(grade.percentage)}%)</div>
                                        <div class="mt-1">${escapeHtml(grade.grade_letter)} | ${escapeHtml(grade.graded_at)}</div>
                                    </div>
                                </div>
                            </article>
                        `).join('');
                    } else {
                        detailRecords.innerHTML = `
                            <div class="rounded-xl border border-dashed border-slate-300 bg-white px-3 py-4 text-center text-xs font-semibold text-slate-500">
                                No grade records for ${escapeHtml(subjectLabel)} in ${escapeHtml(monthLabel(monthKey))}.
                            </div>
                        `;
                    }
                }

                setSubmitBlocked(Boolean(duplicate));
                return Boolean(duplicate);
            };

            if (String(flash.success || '').trim() !== '' && String(flash.action || '') !== 'updated') {
                alertQueue.push({
                    icon: 'success',
                    title: String(flash.action || '') === 'updated' ? 'Grade updated' : 'Grade assigned',
                    text: String(flash.success),
                    confirmButtonText: 'Great',
                });
            }

            if (String(flash.error || '').trim() !== '' && validationErrors.length === 0) {
                alertQueue.push({
                    icon: 'error',
                    title: 'Unable to save grade',
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

            const applyStudentFilters = () => {
                if (!studentSelect) {
                    return;
                }

                const classId = Number(classSelect?.value || 0) || 0;
                const subjectId = Number(subjectSelect?.value || 0) || 0;
                const searchValue = String(searchInput?.value || '').trim().toLowerCase();
                let selectedStillVisible = false;

                Array.from(studentSelect.options).forEach((option, index) => {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }

                    const subjectIds = String(option.dataset.subjectIds || '')
                        .split(',')
                        .map((value) => Number(value.trim()))
                        .filter((value) => Number.isFinite(value) && value > 0);
                    const optionClassId = Number(option.dataset.classId || 0) || 0;
                    const name = String(option.dataset.name || '');
                    const classLabel = String(option.dataset.class || '');
                    const matchesClass = classId === 0 || optionClassId === classId;
                    const matchesSubject = subjectId === 0 || subjectIds.includes(subjectId);
                    const matchesSearch = searchValue === '' || name.includes(searchValue) || classLabel.includes(searchValue);
                    const visible = matchesClass && matchesSubject && matchesSearch;

                    option.hidden = !visible;
                    option.disabled = !visible;

                    if (visible && option.selected) {
                        selectedStillVisible = true;
                    }
                });

                if (studentSelect.value !== '' && !selectedStillVisible) {
                    studentSelect.value = '';
                }

                updateStudentDetail();
            };

            classSelect?.addEventListener('change', applyStudentFilters);
            subjectSelect?.addEventListener('change', () => {
                applyStudentFilters();
                updateStudentDetail();
            });
            searchInput?.addEventListener('input', applyStudentFilters);
            titleSelect?.addEventListener('change', updateStudentDetail);
            studentSelect?.addEventListener('change', updateStudentDetail);
            gradedAtInput?.addEventListener('change', updateStudentDetail);
            applyStudentFilters();
            updateStudentDetail();

            const updateEditStudentPicker = (gradeId) => {
                const key = String(gradeId || '');
                if (key === '') {
                    return;
                }

                const select = document.querySelector(`.js-edit-grade-student-select[data-grade-id="${key}"]`);
                const subject = document.querySelector(`.js-edit-grade-subject-select[data-grade-id="${key}"]`);
                const search = document.querySelector(`.js-edit-grade-student-search[data-grade-id="${key}"]`);
                const nameNode = document.querySelector(`.js-edit-grade-student-name[data-grade-id="${key}"]`);
                const metaNode = document.querySelector(`.js-edit-grade-student-meta[data-grade-id="${key}"]`);
                const rows = Array.from(document.querySelectorAll(`.js-edit-grade-student-row[data-grade-id="${key}"]`));
                const emptyRow = document.querySelector(`.js-edit-grade-empty-row[data-grade-id="${key}"]`);
                const selectedStudent = studentsById.get(String(select?.value || ''));
                const subjectId = Number(subject?.value || 0) || 0;
                const searchValue = String(search?.value || '').trim().toLowerCase();
                let visibleCount = 0;

                if (selectedStudent) {
                    if (nameNode) {
                        nameNode.textContent = String(selectedStudent.name || 'Student');
                    }

                    if (metaNode) {
                        const subjectCount = Array.isArray(selectedStudent.subject_names)
                            ? selectedStudent.subject_names.length
                            : 0;
                        metaNode.textContent = `${selectedStudent.class_label || 'Unassigned class'} | ${subjectCount} subject${subjectCount === 1 ? '' : 's'}`;
                    }
                } else {
                    if (nameNode) {
                        nameNode.textContent = 'Select student';
                    }

                    if (metaNode) {
                        metaNode.textContent = 'Open the student list to choose a student.';
                    }
                }

                rows.forEach((row) => {
                    const subjectIds = String(row.dataset.subjectIds || '')
                        .split(',')
                        .map((value) => Number(value.trim()))
                        .filter((value) => Number.isFinite(value) && value > 0);
                    const name = String(row.dataset.name || '');
                    const classLabel = String(row.dataset.class || '');
                    const matchesSubject = subjectId === 0 || subjectIds.includes(subjectId);
                    const matchesSearch = searchValue === '' || name.includes(searchValue) || classLabel.includes(searchValue);
                    const visible = matchesSubject && matchesSearch;
                    const selected = String(row.dataset.studentId || '') === String(select?.value || '');

                    row.classList.toggle('hidden', !visible);
                    row.classList.toggle('bg-indigo-50/50', selected);

                    if (visible) {
                        visibleCount++;
                    }
                });

                emptyRow?.classList.toggle('hidden', visibleCount > 0);
            };

            document.querySelectorAll('.js-edit-grade-student-select').forEach((select) => {
                const gradeId = select.dataset.gradeId || '';
                select.addEventListener('change', () => updateEditStudentPicker(gradeId));
                updateEditStudentPicker(gradeId);
            });

            document.querySelectorAll('.js-edit-grade-subject-select').forEach((select) => {
                select.addEventListener('change', () => updateEditStudentPicker(select.dataset.gradeId || ''));
            });

            document.querySelectorAll('.js-edit-grade-student-search').forEach((input) => {
                input.addEventListener('input', () => updateEditStudentPicker(input.dataset.gradeId || ''));
            });

            document.querySelectorAll('.js-edit-grade-select-student').forEach((button) => {
                button.addEventListener('click', () => {
                    const gradeId = button.dataset.gradeId || '';
                    const select = document.querySelector(`.js-edit-grade-student-select[data-grade-id="${gradeId}"]`);

                    if (!select) {
                        return;
                    }

                    select.value = String(button.dataset.studentId || '');
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    updateEditStudentPicker(gradeId);
                });
            });

            if (!form) {
                return;
            }

            form.addEventListener('submit', (event) => {
                if (form.dataset.confirmed === '1') {
                    form.dataset.confirmed = '0';
                    return;
                }

                event.preventDefault();

                if (updateStudentDetail()) {
                    showAlert({
                        icon: 'warning',
                        title: 'Grade already exists',
                        text: 'This student already has this assessment for the selected subject and month. Please edit the existing grade instead.',
                        confirmButtonText: 'OK',
                    });
                    return;
                }

                const studentLabel = studentSelect?.selectedOptions?.[0]?.textContent?.trim() || 'this student';
                showConfirm({
                    title: isEditing ? 'Save grade changes?' : 'Assign grade?',
                    text: isEditing
                        ? `Update the grade record for ${studentLabel}?`
                        : `Send this grade and notify ${studentLabel}?`,
                    confirmButtonText: isEditing ? 'Yes, save it' : 'Yes, assign it',
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

            document.querySelectorAll('.js-grade-edit-form').forEach((editForm) => {
                editForm.dataset.confirmed = '1';
            });
        });
    </script>
@endsection
