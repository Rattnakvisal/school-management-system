@extends('layout.teacher.navbar')

@section('page')
    @php
        $subjectOptions = collect($subjectOptions ?? []);
        $studentOptions = collect($studentOptions ?? []);
        $classOptions = collect($classOptions ?? []);
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
                            <input id="grade_title" name="title" type="text" value="{{ $titleValue }}"
                                maxlength="255" placeholder="Example: Midterm Exam"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
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
                                        <tr class="align-top hover:bg-slate-50/80 {{ $isEditing && (int) $editingGrade->id === (int) $grade->id ? 'bg-indigo-50/40' : '' }}">
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
                                                    <a href="{{ route('teacher.grades.index', ['edit' => $grade->id]) }}"
                                                        class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                        Edit
                                                    </a>
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
        ];
    @endphp
    <script id="teacher-grade-data" type="application/json">{!! json_encode($gradePageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('teacher-grade-form');
            const dataNode = document.getElementById('teacher-grade-data');
            const classSelect = document.getElementById('grade_class_id');
            const subjectSelect = document.getElementById('grade_subject_id');
            const studentSelect = document.getElementById('grade_student_id');
            const searchInput = document.getElementById('grade-student-search');
            const hasSwal = typeof window.Swal !== 'undefined';
            const isEditing = @json($isEditing);
            let pageData = {
                validationErrors: [],
                flash: { success: '', error: '', action: '' },
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

            if (String(flash.success || '').trim() !== '') {
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
            };

            classSelect?.addEventListener('change', applyStudentFilters);
            subjectSelect?.addEventListener('change', applyStudentFilters);
            searchInput?.addEventListener('input', applyStudentFilters);
            applyStudentFilters();

            if (!form) {
                return;
            }

            form.addEventListener('submit', (event) => {
                if (form.dataset.confirmed === '1') {
                    form.dataset.confirmed = '0';
                    return;
                }

                event.preventDefault();

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
        });
    </script>
@endsection
