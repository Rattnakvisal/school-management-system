<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Notification;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        abort_unless($teacher instanceof User, 403);

        $subjectOptions = $this->resolveTeacherSubjects($teacher);
        $studentOptions = $this->resolveTeacherStudents($teacher, $subjectOptions);
        $teacherId = (int) $teacher->id;
        $editingAssignment = null;

        $editId = (int) $request->query('edit', 0);
        if ($editId > 0) {
            $editingAssignment = Assignment::query()
                ->with(['students:id,name', 'subject:id,name,code,school_class_id'])
                ->where('teacher_id', $teacherId)
                ->find($editId);
        }

        $assignmentQuery = Assignment::query()
            ->with([
                'subject:id,name,code,school_class_id',
                'subject.schoolClass:id,name,section',
                'students:id,name,email',
            ])
            ->withCount('students')
            ->withCount([
                'students as submissions_count' => function ($query) {
                    $query->whereNotNull('assignment_student.submitted_at');
                },
            ])
            ->where('teacher_id', $teacherId);

        $assignments = (clone $assignmentQuery)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => (int) (clone $assignmentQuery)->count(),
            'dueSoon' => (int) (clone $assignmentQuery)
                ->whereNotNull('due_at')
                ->whereBetween('due_at', [now(), now()->copy()->addDays(7)->endOfDay()])
                ->count(),
            'students' => $studentOptions->count(),
            'subjects' => $subjectOptions->count(),
        ];

        return view('teacher.assignments', [
            'subjectOptions' => $subjectOptions,
            'studentOptions' => $studentOptions,
            'assignments' => $assignments,
            'stats' => $stats,
            'editingAssignment' => $editingAssignment,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();
        abort_unless($teacher instanceof User, 403);

        $subjectOptions = $this->resolveTeacherSubjects($teacher);
        $studentOptions = $this->resolveTeacherStudents($teacher, $subjectOptions);
        $allowedSubjectIds = $subjectOptions->pluck('id')->map(fn ($id) => (int) $id)->all();
        $allowedStudentIds = $studentOptions->pluck('id')->map(fn ($id) => (int) $id)->all();

        $validated = $request->validate([
            'subject_id' => [
                'nullable',
                'integer',
                Rule::in($allowedSubjectIds),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'due_at' => ['nullable', 'date'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'distinct', Rule::in($allowedStudentIds)],
        ]);

        $selectedStudentIds = collect($validated['student_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        $selectedSubjectId = (int) ($validated['subject_id'] ?? 0);
        if ($selectedSubjectId > 0) {
            $subjectStudentIds = $studentOptions
                ->filter(fn (array $student) => in_array($selectedSubjectId, $student['subject_ids'] ?? [], true))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $invalidForSubject = $selectedStudentIds->diff($subjectStudentIds);
            if ($invalidForSubject->isNotEmpty()) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'student_ids' => 'One or more selected students are not linked to the chosen subject.',
                    ]);
            }
        }

        $dueAt = null;
        if (!empty($validated['due_at'])) {
            $dueAt = Carbon::parse((string) $validated['due_at'])->endOfDay();
        }

        $assignment = DB::transaction(function () use (
            $teacher,
            $validated,
            $selectedStudentIds,
            $selectedSubjectId,
            $dueAt
        ): Assignment {
            $assignment = Assignment::query()->create([
                'teacher_id' => (int) $teacher->id,
                'subject_id' => $selectedSubjectId > 0 ? $selectedSubjectId : null,
                'title' => trim((string) $validated['title']),
                'description' => trim((string) $validated['description']),
                'due_at' => $dueAt,
            ]);

            $assignment->students()->sync($selectedStudentIds->all());

            $assignmentUrl = route('student.assignment.index') . '#assignment-' . $assignment->id;
            $subject = $assignment->subject()->first(['id', 'name']);
            $subjectLabel = trim((string) ($subject?->name ?? ''));

            foreach ($selectedStudentIds as $studentId) {
                $message = '[student_id:' . $studentId . '] '
                    . trim((string) $teacher->name)
                    . ' assigned "' . trim((string) $assignment->title) . '"';

                if ($subjectLabel !== '') {
                    $message .= ' for ' . $subjectLabel;
                }

                if ($dueAt instanceof Carbon) {
                    $message .= ' due on ' . $dueAt->format('M d, Y');
                }

                Notification::query()->create([
                    'type' => 'student_assignment_posted',
                    'title' => 'New assignment available',
                    'message' => $message,
                    'url' => $assignmentUrl,
                    'is_read' => false,
                ]);
            }

            return $assignment;
        });

        return redirect()
            ->route('teacher.assignments.index')
            ->with('success', 'Assignment sent to ' . $selectedStudentIds->count() . ' student' . ($selectedStudentIds->count() === 1 ? '' : 's') . '.')
            ->with('assignment_action', 'created');
    }

    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        $teacher = $request->user();
        abort_unless($teacher instanceof User, 403);
        abort_unless((int) $assignment->teacher_id === (int) $teacher->id, 403);

        $subjectOptions = $this->resolveTeacherSubjects($teacher);
        $studentOptions = $this->resolveTeacherStudents($teacher, $subjectOptions);
        $allowedSubjectIds = $subjectOptions->pluck('id')->map(fn ($id) => (int) $id)->all();
        $allowedStudentIds = $studentOptions->pluck('id')->map(fn ($id) => (int) $id)->all();

        $validated = $request->validate([
            'subject_id' => [
                'nullable',
                'integer',
                Rule::in($allowedSubjectIds),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'due_at' => ['nullable', 'date'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'distinct', Rule::in($allowedStudentIds)],
        ]);

        $selectedStudentIds = collect($validated['student_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        $selectedSubjectId = (int) ($validated['subject_id'] ?? 0);
        if ($selectedSubjectId > 0) {
            $subjectStudentIds = $studentOptions
                ->filter(fn (array $student) => in_array($selectedSubjectId, $student['subject_ids'] ?? [], true))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $invalidForSubject = $selectedStudentIds->diff($subjectStudentIds);
            if ($invalidForSubject->isNotEmpty()) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'student_ids' => 'One or more selected students are not linked to the chosen subject.',
                    ]);
            }
        }

        $dueAt = null;
        if (!empty($validated['due_at'])) {
            $dueAt = Carbon::parse((string) $validated['due_at'])->endOfDay();
        }

        DB::transaction(function () use ($assignment, $validated, $selectedStudentIds, $selectedSubjectId, $dueAt): void {
            $assignment->update([
                'subject_id' => $selectedSubjectId > 0 ? $selectedSubjectId : null,
                'title' => trim((string) $validated['title']),
                'description' => trim((string) $validated['description']),
                'due_at' => $dueAt,
            ]);

            $assignment->students()->sync($selectedStudentIds->all());
        });

        return redirect()
            ->route('teacher.assignments.index')
            ->with('success', 'Assignment updated successfully.')
            ->with('assignment_action', 'updated');
    }

    private function resolveTeacherSubjects(User $teacher): Collection
    {
        $teacherId = (int) $teacher->id;
        $hasSubjectStatusColumn = Schema::hasColumn('subjects', 'is_active');
        $hasStudyTimesTable = Schema::hasTable('subject_study_times');
        $hasStudyTimeTeacherColumn = $hasStudyTimesTable && Schema::hasColumn('subject_study_times', 'teacher_id');

        $query = Subject::query()
            ->with('schoolClass:id,name,section')
            ->orderBy('name');

        if ($hasStudyTimeTeacherColumn) {
            $subjectIds = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->distinct()
                ->pluck('subject_id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn (int $id) => $id > 0)
                ->values();

            if ($subjectIds->isNotEmpty()) {
                $query->whereIn('id', $subjectIds->all());
            } else {
                $query->where('teacher_id', $teacherId);
            }
        } else {
            $query->where('teacher_id', $teacherId);
        }

        if ($hasSubjectStatusColumn) {
            $query->where('is_active', true);
        }

        return $query
            ->get(['id', 'name', 'code', 'school_class_id'])
            ->map(function (Subject $subject): array {
                return [
                    'id' => (int) $subject->id,
                    'name' => trim((string) $subject->name),
                    'code' => trim((string) ($subject->code ?? '')),
                    'class_label' => trim((string) ($subject->schoolClass?->display_name ?? '')),
                ];
            })
            ->values();
    }

    private function resolveTeacherStudents(User $teacher, Collection $subjectOptions): Collection
    {
        $teacherId = (int) $teacher->id;
        $subjectIds = $subjectOptions
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->values();

        $classIdsFromSubjects = Subject::query()
            ->whereIn('id', $subjectIds->all())
            ->whereNotNull('school_class_id')
            ->distinct()
            ->pluck('school_class_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->values();

        $slotClassIds = collect();
        if (Schema::hasTable('subject_study_times')
            && Schema::hasColumn('subject_study_times', 'teacher_id')
            && Schema::hasColumn('subject_study_times', 'school_class_id')) {
            $slotClassIds = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->whereNotNull('school_class_id')
                ->distinct()
                ->pluck('school_class_id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn (int $id) => $id > 0)
                ->values();
        }

        $classIds = $classIdsFromSubjects
            ->concat($slotClassIds)
            ->unique()
            ->values();

        $majorStudentIds = collect();
        if (Schema::hasTable('student_major_subjects') && $subjectIds->isNotEmpty()) {
            $majorStudentIds = DB::table('student_major_subjects')
                ->whereIn('subject_id', $subjectIds->all())
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn (int $id) => $id > 0)
                ->values();
        }

        $studentQuery = User::query()
            ->where('role', 'student')
            ->with('schoolClass:id,name,section')
            ->orderBy('name');

        if ($classIds->isNotEmpty() || $majorStudentIds->isNotEmpty()) {
            $studentQuery->where(function ($query) use ($classIds, $majorStudentIds) {
                if ($classIds->isNotEmpty()) {
                    $query->whereIn('school_class_id', $classIds->all());
                }

                if ($majorStudentIds->isNotEmpty()) {
                    $query->orWhereIn('id', $majorStudentIds->all());
                }
            });
        } else {
            return collect();
        }

        $students = $studentQuery->get(['id', 'name', 'school_class_id']);
        $studentIds = $students->pluck('id')->map(fn ($id) => (int) $id)->values();

        $subjectNameById = $subjectOptions
            ->keyBy('id')
            ->map(fn (array $subject) => trim((string) $subject['name']));

        $subjectIdsByStudent = [];
        if ($classIds->isNotEmpty()) {
            $subjectsByClass = Subject::query()
                ->whereIn('id', $subjectIds->all())
                ->whereNotNull('school_class_id')
                ->get(['id', 'school_class_id'])
                ->groupBy(fn (Subject $subject) => (int) $subject->school_class_id);

            foreach ($students as $student) {
                $classSubjectIds = collect($subjectsByClass->get((int) ($student->school_class_id ?? 0), collect()))
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                $subjectIdsByStudent[(int) $student->id] = $classSubjectIds;
            }
        }

        if (Schema::hasTable('student_major_subjects') && $studentIds->isNotEmpty() && $subjectIds->isNotEmpty()) {
            $majorPairs = DB::table('student_major_subjects')
                ->whereIn('user_id', $studentIds->all())
                ->whereIn('subject_id', $subjectIds->all())
                ->get(['user_id', 'subject_id']);

            foreach ($majorPairs as $pair) {
                $userId = (int) ($pair->user_id ?? 0);
                $subjectId = (int) ($pair->subject_id ?? 0);
                $subjectIdsByStudent[$userId] = array_values(array_unique(array_merge(
                    $subjectIdsByStudent[$userId] ?? [],
                    $subjectId > 0 ? [$subjectId] : []
                )));
            }
        }

        return $students
            ->map(function (User $student) use ($subjectIdsByStudent, $subjectNameById): array {
                $studentSubjectIds = collect($subjectIdsByStudent[(int) $student->id] ?? [])
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn (int $id) => $id > 0)
                    ->unique()
                    ->values()
                    ->all();

                $subjectNames = collect($studentSubjectIds)
                    ->map(fn (int $id) => trim((string) $subjectNameById->get($id, '')))
                    ->filter()
                    ->values()
                    ->all();

                return [
                    'id' => (int) $student->id,
                    'name' => trim((string) $student->name),
                    'class_label' => trim((string) ($student->schoolClass?->display_name ?? 'Unassigned class')),
                    'subject_ids' => $studentSubjectIds,
                    'subject_names' => $subjectNames,
                ];
            })
            ->filter(fn (array $student) => !empty($student['subject_ids']))
            ->values();
    }
}
