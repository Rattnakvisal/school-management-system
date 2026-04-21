<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
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
use Illuminate\Validation\ValidationException;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        abort_unless($teacher instanceof User, 403);

        $subjectOptions = $this->resolveTeacherSubjects($teacher);
        $studentOptions = $this->resolveTeacherStudents($teacher, $subjectOptions);
        $classOptions = $studentOptions
            ->map(function (array $student): array {
                return [
                    'id' => (int) ($student['class_id'] ?? 0),
                    'label' => trim((string) ($student['class_label'] ?? '')),
                ];
            })
            ->filter(fn (array $item) => $item['id'] > 0 && $item['label'] !== '')
            ->unique(fn (array $item) => $item['id'])
            ->sortBy('label')
            ->values();
        $teacherId = (int) $teacher->id;
        $editingGrade = null;

        $editId = (int) $request->query('edit', 0);
        if ($editId > 0) {
            $editingGrade = Grade::query()
                ->with([
                    'student:id,name,school_class_id',
                    'student.schoolClass:id,name,section',
                    'subject:id,name,code,school_class_id',
                ])
                ->where('teacher_id', $teacherId)
                ->find($editId);
        }

        $gradeQuery = Grade::query()
            ->with([
                'student:id,name,school_class_id',
                'student.schoolClass:id,name,section',
                'subject:id,name,code,school_class_id',
                'subject.schoolClass:id,name,section',
            ])
            ->where('teacher_id', $teacherId);

        $grades = (clone $gradeQuery)
            ->latest('graded_at')
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $averagePercentage = (clone $gradeQuery)
            ->selectRaw('AVG((score / NULLIF(max_score, 0)) * 100) as average_percentage')
            ->value('average_percentage');

        $stats = [
            'total' => (int) (clone $gradeQuery)->count(),
            'students' => $studentOptions->count(),
            'subjects' => $subjectOptions->count(),
            'average' => $averagePercentage !== null ? round((float) $averagePercentage, 1) : null,
        ];

        return view('teacher.grades', [
            'subjectOptions' => $subjectOptions,
            'studentOptions' => $studentOptions,
            'classOptions' => $classOptions,
            'grades' => $grades,
            'stats' => $stats,
            'editingGrade' => $editingGrade,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();
        abort_unless($teacher instanceof User, 403);

        [$validated, $studentOptions] = $this->validateGradeRequest($request, $teacher);

        $grade = DB::transaction(function () use ($teacher, $validated): Grade {
            $grade = Grade::query()->create($validated);
            $this->sendGradeNotification($teacher, $grade, 'created');

            return $grade;
        });

        return redirect()
            ->route('teacher.grades.index')
            ->with('success', 'Grade assigned to ' . $this->studentLabelFromOptions($studentOptions, (int) $grade->student_id) . '.')
            ->with('grade_action', 'created');
    }

    public function update(Request $request, Grade $grade): RedirectResponse
    {
        $teacher = $request->user();
        abort_unless($teacher instanceof User, 403);
        abort_unless((int) $grade->teacher_id === (int) $teacher->id, 403);

        [$validated, $studentOptions] = $this->validateGradeRequest($request, $teacher);

        DB::transaction(function () use ($grade, $validated): void {
            $grade->update($validated);
        });

        return redirect()
            ->route('teacher.grades.index')
            ->with('success', 'Grade updated for ' . $this->studentLabelFromOptions($studentOptions, (int) $grade->student_id) . '.')
            ->with('grade_action', 'updated');
    }

    private function validateGradeRequest(Request $request, User $teacher): array
    {
        $subjectOptions = $this->resolveTeacherSubjects($teacher);
        $studentOptions = $this->resolveTeacherStudents($teacher, $subjectOptions);
        $allowedSubjectIds = $subjectOptions->pluck('id')->map(fn ($id) => (int) $id)->all();
        $allowedStudentIds = $studentOptions->pluck('id')->map(fn ($id) => (int) $id)->all();

        $validated = $request->validate([
            'student_id' => ['required', 'integer', Rule::in($allowedStudentIds)],
            'subject_id' => ['nullable', 'integer', Rule::in($allowedSubjectIds)],
            'title' => ['required', 'string', 'max:255'],
            'score' => ['required', 'numeric', 'min:0'],
            'max_score' => ['required', 'numeric', 'gt:0'],
            'graded_at' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        $studentId = (int) ($validated['student_id'] ?? 0);
        $selectedSubjectId = (int) ($validated['subject_id'] ?? 0);

        if ($selectedSubjectId > 0) {
            $student = $studentOptions->firstWhere('id', $studentId);
            $studentSubjectIds = collect($student['subject_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->all();

            if (!in_array($selectedSubjectId, $studentSubjectIds, true)) {
                throw ValidationException::withMessages([
                    'subject_id' => 'The selected subject is not linked to the chosen student.',
                ]);
            }
        }

        $score = round((float) ($validated['score'] ?? 0), 2);
        $maxScore = round((float) ($validated['max_score'] ?? 0), 2);
        if ($score > $maxScore) {
            throw ValidationException::withMessages([
                'score' => 'The score cannot be greater than the maximum score.',
            ]);
        }

        return [[
            'teacher_id' => (int) $teacher->id,
            'student_id' => $studentId,
            'subject_id' => $selectedSubjectId > 0 ? $selectedSubjectId : null,
            'title' => trim((string) $validated['title']),
            'score' => $score,
            'max_score' => $maxScore,
            'grade_letter' => $this->gradeLetter($score, $maxScore),
            'remarks' => trim((string) ($validated['remarks'] ?? '')) ?: null,
            'graded_at' => Carbon::parse((string) $validated['graded_at'])->toDateString(),
        ], $studentOptions];
    }

    private function sendGradeNotification(User $teacher, Grade $grade, string $action): void
    {
        $subjectLabel = trim((string) ($grade->subject?->name ?? ''));
        $title = $action === 'updated' ? 'Grade updated' : 'New grade available';
        $message = '[student_id:' . (int) $grade->student_id . '] '
            . trim((string) $teacher->name)
            . ' recorded ' . trim((string) $grade->grade_letter)
            . ' for "' . trim((string) $grade->title) . '"';

        if ($subjectLabel !== '') {
            $message .= ' in ' . $subjectLabel;
        }

        $message .= ' (' . number_format((float) $grade->score, 2) . '/' . number_format((float) $grade->max_score, 2) . ').';

        Notification::query()->create([
            'type' => 'student_grade_posted',
            'title' => $title,
            'message' => $message,
            'url' => route('student.grades.index') . '#grade-' . $grade->id,
            'is_read' => false,
        ]);
    }

    private function studentLabelFromOptions(Collection $studentOptions, int $studentId): string
    {
        $student = $studentOptions->firstWhere('id', $studentId);

        return trim((string) ($student['name'] ?? 'student'));
    }

    private function gradeLetter(float $score, float $maxScore): string
    {
        $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;

        return match (true) {
            $percentage >= 90 => 'A',
            $percentage >= 80 => 'B',
            $percentage >= 70 => 'C',
            $percentage >= 60 => 'D',
            default => 'F',
        };
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
                    'class_id' => (int) ($student->school_class_id ?? 0),
                    'class_label' => trim((string) ($student->schoolClass?->display_name ?? 'Unassigned class')),
                    'subject_ids' => $studentSubjectIds,
                    'subject_names' => $subjectNames,
                ];
            })
            ->filter(fn (array $student) => !empty($student['subject_ids']))
            ->values();
    }
}
