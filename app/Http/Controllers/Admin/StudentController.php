<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $classIdRaw = (string) $request->query('class_id', 'all');
        $hasStatusColumn = $this->hasStatusColumn();
        $hasClassColumn = $this->hasClassColumn();
        $hasMajorSubjectColumn = $this->hasMajorSubjectColumn();
        $hasStudentMajorSubjectsTable = $this->hasStudentMajorSubjectsTable();
        $hasClassStudyTimeColumn = $this->hasClassStudyTimeColumn();
        $hasStudentStudyTimesTable = $this->hasStudentStudyTimesTable();
        $classId = $hasClassColumn && ctype_digit($classIdRaw) ? (int) $classIdRaw : null;

        $studentQuery = User::query()
            ->where('role', 'student')
            ->when($hasClassColumn, function ($query) {
                $query->with('schoolClass');
            })
            ->when($hasMajorSubjectColumn, function ($query) {
                $query->with('majorSubject');
            })
            ->when($hasMajorSubjectColumn && $hasStudentMajorSubjectsTable, function ($query) {
                $query->with('majorSubjects.schoolClass');
            })
            ->when($hasClassStudyTimeColumn, function ($query) use ($hasStudentStudyTimesTable) {
                $query->with('classStudyTime');
                if ($hasStudentStudyTimesTable) {
                    $query->with('studyTimes');
                }
            })
            ->when($search !== '', function ($query) use ($search, $hasClassColumn, $hasMajorSubjectColumn, $hasStudentMajorSubjectsTable) {
                $query->where(function ($inner) use ($search, $hasClassColumn, $hasMajorSubjectColumn, $hasStudentMajorSubjectsTable) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');

                    if ($hasClassColumn) {
                        $inner->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                            $classQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('section', 'like', '%' . $search . '%');
                        });
                    }

                    if ($hasMajorSubjectColumn) {
                        $inner->orWhereHas('majorSubject', function ($subjectQuery) use ($search) {
                            $subjectQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('code', 'like', '%' . $search . '%');
                        });

                        if ($hasStudentMajorSubjectsTable) {
                            $inner->orWhereHas('majorSubjects', function ($subjectQuery) use ($search) {
                                $subjectQuery->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('code', 'like', '%' . $search . '%');
                            });
                        }
                    }
                });
            });

        if ($hasClassColumn && $classId !== null) {
            $studentQuery->where('school_class_id', $classId);
        }

        if ($hasStatusColumn && in_array($status, ['active', 'inactive'], true)) {
            $studentQuery->where('is_active', $status === 'active');
        }

        $students = $studentQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $baseStatsQuery = User::query()->where('role', 'student');
        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'active' => $hasStatusColumn ? (clone $baseStatsQuery)->where('is_active', true)->count() : (clone $baseStatsQuery)->count(),
            'inactive' => $hasStatusColumn ? (clone $baseStatsQuery)->where('is_active', false)->count() : 0,
            'assigned' => $hasClassColumn ? (clone $baseStatsQuery)->whereNotNull('school_class_id')->count() : 0,
            'unassigned' => $hasClassColumn ? (clone $baseStatsQuery)->whereNull('school_class_id')->count() : 0,
        ];

        $classes = $hasClassColumn
            ? SchoolClass::query()->orderBy('name')->orderBy('section')->get()
            : collect();
        $subjectsByClass = $this->subjectsByClassMap($hasClassColumn, $hasMajorSubjectColumn);
        $studyTimesByClass = $this->studyTimesByClassMap($hasClassColumn, $hasClassStudyTimeColumn);
        $subjectStudySlotsByClassSubject = $this->subjectStudySlotsByClassSubjectMap($hasClassColumn, $hasMajorSubjectColumn, $hasClassStudyTimeColumn);
        $classStudyTimeIdsByClassSubject = $this->classStudyTimeIdsByClassSubjectMapFromSlots($studyTimesByClass, $subjectStudySlotsByClassSubject);
        $classStudyTimeIdsBySubjectAll = $this->classStudyTimeIdsBySubjectAllMap($classStudyTimeIdsByClassSubject);

        return view('admin.students', [
            'students' => $students,
            'classes' => $classes,
            'subjectsByClass' => $subjectsByClass,
            'studyTimesByClass' => $studyTimesByClass,
            'subjectStudySlotsByClassSubject' => $subjectStudySlotsByClassSubject,
            'classStudyTimeIdsByClassSubject' => $classStudyTimeIdsByClassSubject,
            'classStudyTimeIdsBySubjectAll' => $classStudyTimeIdsBySubjectAll,
            'periodLabels' => $this->periodLabels(),
            'search' => $search,
            'status' => in_array($status, ['all', 'active', 'inactive'], true) ? $status : 'all',
            'classId' => $classId !== null ? (string) $classId : 'all',
            'stats' => $stats,
            'hasStatusColumn' => $hasStatusColumn,
            'hasClassColumn' => $hasClassColumn,
            'hasMajorSubjectColumn' => $hasMajorSubjectColumn,
            'hasStudentMajorSubjectsTable' => $hasStudentMajorSubjectsTable,
            'hasClassStudyTimeColumn' => $hasClassStudyTimeColumn,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateStudentRequest($request);
        $selectedMajorSubjectIds = $this->extractSelectedMajorSubjectIdsFromRequest($request);
        $selectedStudyTimeIds = $this->extractSelectedStudyTimeIdsFromRequest($request);

        $avatarResult = $this->resolveAvatarUpload($request);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => (string) ($validated['role'] ?? 'student'),
            'avatar' => $avatarResult['path'],
            'provider' => null,
            'google_id' => null,
        ];

        if ($this->hasStatusColumn()) {
            $payload['is_active'] = $request->boolean('is_active', true);
        }

        if ($this->hasClassColumn()) {
            $payload['school_class_id'] = $payload['role'] === 'student'
                ? ($validated['school_class_id'] ?? null)
                : null;
        }

        if ($this->hasMajorSubjectColumn()) {
            $payload['major_subject_id'] = $payload['role'] === 'student'
                ? ($selectedMajorSubjectIds[0] ?? null)
                : null;
        }

        if ($this->hasClassStudyTimeColumn()) {
            $payload['class_study_time_id'] = $payload['role'] === 'student'
                ? ($selectedStudyTimeIds[0] ?? null)
                : null;
        }

        $student = User::create($payload);
        $this->syncStudentMajorSubjects(
            $student,
            $payload['role'] === 'student' ? $selectedMajorSubjectIds : []
        );
        $this->syncStudentStudyTimes(
            $student,
            $payload['role'] === 'student' ? $selectedStudyTimeIds : []
        );

        $redirect = redirect()
            ->route('admin.students.index')
            ->with('success', 'User account created successfully.');

        if ($avatarResult['warning']) {
            $redirect->with('warning', $avatarResult['warning']);
        }

        return $redirect;
    }

    public function update(Request $request, User $student)
    {
        $student = $this->studentOrFail($student);
        $validated = $this->validateStudentRequest($request, $student);
        $selectedMajorSubjectIds = $this->extractSelectedMajorSubjectIdsFromRequest($request);
        $selectedStudyTimeIds = $this->extractSelectedStudyTimeIdsFromRequest($request);

        $avatarResult = $this->resolveAvatarUpload($request, $student);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => (string) ($validated['role'] ?? $student->role),
        ];

        if ($avatarResult['path'] !== $student->avatar) {
            $payload['avatar'] = $avatarResult['path'];
        }

        if ($request->filled('password')) {
            $payload['password'] = $validated['password'];
        }

        if ($this->hasStatusColumn()) {
            $payload['is_active'] = $request->boolean('is_active');
        }

        if ($this->hasClassColumn()) {
            $payload['school_class_id'] = $payload['role'] === 'student'
                ? ($validated['school_class_id'] ?? null)
                : null;
        }

        if ($this->hasMajorSubjectColumn()) {
            $payload['major_subject_id'] = $payload['role'] === 'student'
                ? ($selectedMajorSubjectIds[0] ?? null)
                : null;
        }

        if ($this->hasClassStudyTimeColumn()) {
            $payload['class_study_time_id'] = $payload['role'] === 'student'
                ? ($selectedStudyTimeIds[0] ?? null)
                : null;
        }

        $student->update($payload);
        $this->syncStudentMajorSubjects(
            $student,
            $payload['role'] === 'student' ? $selectedMajorSubjectIds : []
        );
        $this->syncStudentStudyTimes(
            $student,
            $payload['role'] === 'student' ? $selectedStudyTimeIds : []
        );

        $redirect = redirect()
            ->route('admin.students.index')
            ->with('success', 'User updated successfully.');

        if ($avatarResult['warning']) {
            $redirect->with('warning', $avatarResult['warning']);
        }

        return $redirect;
    }

    public function toggleStatus(User $student)
    {
        $student = $this->studentOrFail($student);

        if (!$this->hasStatusColumn()) {
            return redirect()
                ->route('admin.students.index')
                ->with('error', 'Student status column is missing. Please run migrations.');
        }

        $student->is_active = !$student->is_active;
        $student->save();

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student status updated to ' . ($student->is_active ? 'active' : 'inactive') . '.');
    }

    public function destroy(User $student)
    {
        $student = $this->studentOrFail($student);

        $this->deleteStoredAvatar($student->avatar);
        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    private function studentOrFail(User $student): User
    {
        abort_unless($student->role === 'student', 404);
        return $student;
    }

    private function hasStatusColumn(): bool
    {
        return Schema::hasColumn('users', 'is_active');
    }

    private function hasClassColumn(): bool
    {
        return Schema::hasColumn('users', 'school_class_id');
    }

    private function hasMajorSubjectColumn(): bool
    {
        return Schema::hasColumn('users', 'major_subject_id');
    }

    private function hasClassStudyTimeColumn(): bool
    {
        return Schema::hasColumn('users', 'class_study_time_id');
    }

    private function hasStudentMajorSubjectsTable(): bool
    {
        return Schema::hasTable('student_major_subjects');
    }

    private function hasStudentStudyTimesTable(): bool
    {
        return Schema::hasTable('student_study_times');
    }

    private function validateStudentRequest(Request $request, ?User $student = null): array
    {
        if (!$request->has('major_subject_ids') && $request->filled('major_subject_id')) {
            $request->merge([
                'major_subject_ids' => [$request->input('major_subject_id')],
            ]);
        }

        if (!$request->has('class_study_time_ids') && $request->filled('class_study_time_id')) {
            $request->merge([
                'class_study_time_ids' => [$request->input('class_study_time_id')],
            ]);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email' . ($student ? ',' . $student->id : '')],
            'password' => [$student ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in($this->allowedRoles())],
            'is_active' => ['nullable', 'boolean'],
        ];

        $hasClassColumn = $this->hasClassColumn();
        $hasMajorSubjectColumn = $this->hasMajorSubjectColumn();
        $hasClassStudyTimeColumn = $this->hasClassStudyTimeColumn();

        if ($hasClassColumn) {
            $rules['school_class_id'] = ['exclude_unless:role,student', 'nullable', 'exists:school_classes,id'];
        }

        if ($hasMajorSubjectColumn) {
            $rules['major_subject_ids'] = [
                'exclude_unless:role,student',
                $hasClassColumn ? 'required_with:school_class_id' : 'nullable',
                'array',
            ];
            $rules['major_subject_ids.*'] = [
                'integer',
                'exists:subjects,id',
            ];
            $rules['major_subject_id'] = [
                'nullable',
                'exists:subjects,id',
            ];
        }

        if ($hasClassStudyTimeColumn) {
            $rules['class_study_time_ids'] = [
                'exclude_unless:role,student',
                $hasClassColumn ? 'required_with:school_class_id' : 'nullable',
                'array',
            ];
            $rules['class_study_time_ids.*'] = ['integer', 'exists:class_study_times,id'];
            $rules['class_study_time_id'] = ['nullable', 'exists:class_study_times,id'];
        }

        $messages = [
            'major_subject_ids.required_with' => 'Please select at least one major subject when a home class is selected.',
            'class_study_time_ids.required_with' => 'Please select at least one study time when a home class is selected.',
        ];

        $attributes = [
            'school_class_id' => 'home class',
            'major_subject_ids' => 'major subjects',
            'class_study_time_ids' => 'study times',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        $validator->after(function ($validator) use ($request, $hasClassColumn, $hasMajorSubjectColumn, $hasClassStudyTimeColumn) {
            $role = strtolower((string) $request->input('role', 'student'));
            if ($role !== 'student') {
                return;
            }

            $classId = $hasClassColumn ? (int) ($request->input('school_class_id') ?? 0) : 0;
            $majorSubjectIds = $hasMajorSubjectColumn
                ? $this->extractSelectedMajorSubjectIdsFromRequest($request)
                : [];
            $selectedStudyTimeIds = $hasClassStudyTimeColumn
                ? $this->extractSelectedStudyTimeIdsFromRequest($request)
                : [];

            if ($hasClassColumn && $hasMajorSubjectColumn && $classId > 0 && !empty($majorSubjectIds)) {
                $hasInvalidSubject = collect($majorSubjectIds)
                    ->contains(fn(int $subjectId) => !$this->subjectBelongsToClass($subjectId, $classId));

                if ($hasInvalidSubject) {
                    $validator->errors()->add(
                        'major_subject_ids',
                        'Selected major subjects must belong to the selected home class.'
                    );
                }
            }

            if ($hasClassColumn && $hasClassStudyTimeColumn && $classId > 0 && !empty($selectedStudyTimeIds)) {
                $invalidStudyTimeCount = ClassStudyTime::query()
                    ->whereIn('id', $selectedStudyTimeIds)
                    ->where('school_class_id', '!=', $classId)
                    ->count();

                if ($invalidStudyTimeCount > 0) {
                    $validator->errors()->add(
                        'class_study_time_ids',
                        'Selected study times must belong to the selected home class.'
                    );
                }
            }

            if ($hasClassStudyTimeColumn && !empty($selectedStudyTimeIds)) {
                if (!empty($majorSubjectIds) && Schema::hasTable('subject_study_times')) {
                    $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
                    $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
                    $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');

                    $subjectColumns = ['period', 'start_time', 'end_time'];
                    if ($hasSubjectDayColumn) {
                        $subjectColumns[] = 'day_of_week';
                    }

                    $subjectSlotsQuery = SubjectStudyTime::query()
                        ->whereIn('subject_id', $majorSubjectIds);
                    if ($classId > 0 && $hasSubjectClassColumn) {
                        $subjectSlotsQuery->where('school_class_id', $classId);
                    }

                    $subjectSlots = $subjectSlotsQuery
                        ->get($subjectColumns)
                        ->map(fn($slot) => $this->normalizeStudySlot($slot, $hasSubjectDayColumn))
                        ->unique(fn(array $slot) => $this->studySlotSignature($slot))
                        ->values();

                    if ($subjectSlots->isNotEmpty()) {
                        $classColumns = ['period', 'start_time', 'end_time'];
                        if ($hasClassDayColumn) {
                            $classColumns[] = 'day_of_week';
                        }

                        $selectedClassSlotsQuery = ClassStudyTime::query()
                            ->whereIn('id', $selectedStudyTimeIds);
                        if ($classId > 0) {
                            $selectedClassSlotsQuery->where('school_class_id', $classId);
                        }

                        $selectedClassSlots = $selectedClassSlotsQuery
                            ->get($classColumns)
                            ->map(fn($slot) => $this->normalizeStudySlot($slot, $hasClassDayColumn))
                            ->values();

                        $invalidSelections = $selectedClassSlots
                            ->filter(function (array $classSlot) use ($subjectSlots) {
                                return !$subjectSlots->contains(function (array $subjectSlot) use ($classSlot) {
                                    return $this->studySlotsAreCompatible($classSlot, $subjectSlot);
                                });
                            })
                            ->count();

                        if ($invalidSelections > 0) {
                            $validator->errors()->add(
                                'class_study_time_ids',
                                'One or more selected study times do not match the selected major subject schedule.'
                            );
                        }
                    }
                }
            }
        });

        return $validator->validate();
    }

    private function extractSelectedMajorSubjectIdsFromRequest(Request $request): array
    {
        $rawValues = $request->input('major_subject_ids', []);
        if (!is_array($rawValues)) {
            $rawValues = $rawValues !== null && $rawValues !== '' ? [$rawValues] : [];
        }

        $selectedIds = collect($rawValues)
            ->filter(fn($value) => $value !== null && $value !== '')
            ->map(fn($value) => (int) $value)
            ->filter(fn(int $value) => $value > 0)
            ->unique()
            ->values()
            ->all();

        if (empty($selectedIds)) {
            $fallbackId = (int) ($request->input('major_subject_id') ?? 0);
            if ($fallbackId > 0) {
                $selectedIds = [$fallbackId];
            }
        }

        return $selectedIds;
    }

    private function extractSelectedStudyTimeIdsFromRequest(Request $request): array
    {
        $rawValues = $request->input('class_study_time_ids', []);
        if (!is_array($rawValues)) {
            $rawValues = $rawValues !== null && $rawValues !== '' ? [$rawValues] : [];
        }

        $selectedIds = collect($rawValues)
            ->filter(fn($value) => $value !== null && $value !== '')
            ->map(fn($value) => (int) $value)
            ->filter(fn(int $value) => $value > 0)
            ->unique()
            ->values()
            ->all();

        if (empty($selectedIds)) {
            $fallbackId = (int) ($request->input('class_study_time_id') ?? 0);
            if ($fallbackId > 0) {
                $selectedIds = [$fallbackId];
            }
        }

        return $selectedIds;
    }

    private function syncStudentStudyTimes(User $student, array $selectedStudyTimeIds): void
    {
        if (!$this->hasStudentStudyTimesTable()) {
            return;
        }

        $student->studyTimes()->sync($selectedStudyTimeIds);
    }

    private function syncStudentMajorSubjects(User $student, array $selectedMajorSubjectIds): void
    {
        if (!$this->hasStudentMajorSubjectsTable()) {
            return;
        }

        $student->majorSubjects()->sync($selectedMajorSubjectIds);
    }

    private function subjectsByClassMap(bool $hasClassColumn, bool $hasMajorSubjectColumn): array
    {
        if (!$hasClassColumn || !$hasMajorSubjectColumn) {
            return [];
        }

        $fallbackMap = Subject::query()
            ->select(['id', 'name', 'code', 'school_class_id'])
            ->whereNotNull('school_class_id')
            ->orderBy('name')
            ->get()
            ->groupBy(fn($subject) => (string) $subject->school_class_id)
            ->map(function ($group) {
                return $group->map(function ($subject) {
                    return [
                        'id' => (int) $subject->id,
                        'name' => (string) $subject->name,
                        'code' => (string) $subject->code,
                        'school_class_id' => (int) $subject->school_class_id,
                    ];
                })->values()->all();
            })
            ->toArray();

        if (!Schema::hasTable('subject_study_times')) {
            return $fallbackMap;
        }

        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
        $query = SubjectStudyTime::query()
            ->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
            ->select([
                'subjects.id',
                'subjects.name',
                'subjects.code',
            ]);

        if ($hasSubjectClassColumn) {
            $query
                ->whereNotNull('subject_study_times.school_class_id')
                ->addSelect('subject_study_times.school_class_id as class_id');
        } else {
            $query
                ->whereNotNull('subjects.school_class_id')
                ->addSelect('subjects.school_class_id as class_id');
        }

        $mapped = $query
            ->orderBy('subjects.name')
            ->get()
            ->filter(fn($row) => (int) ($row->class_id ?? 0) > 0)
            ->groupBy(fn($row) => (string) $row->class_id)
            ->map(function ($group) {
                return $group
                    ->unique(fn($row) => (int) $row->id)
                    ->values()
                    ->map(function ($subject) {
                        return [
                            'id' => (int) $subject->id,
                            'name' => (string) $subject->name,
                            'code' => (string) $subject->code,
                            'school_class_id' => (int) $subject->class_id,
                        ];
                    })
                    ->values()
                    ->all();
            })
            ->toArray();

        return !empty($mapped) ? $mapped : $fallbackMap;
    }

    private function studyTimesByClassMap(bool $hasClassColumn, bool $hasClassStudyTimeColumn): array
    {
        if (!$hasClassColumn || !$hasClassStudyTimeColumn) {
            return [];
        }

        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $columns = ['id', 'school_class_id', 'period', 'start_time', 'end_time'];
        if ($hasClassDayColumn) {
            $columns[] = 'day_of_week';
        }

        return ClassStudyTime::query()
            ->select($columns)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($slot) => (string) $slot->school_class_id)
            ->map(function ($group) {
                return $group->map(function ($slot) {
                    return [
                        'id' => (int) $slot->id,
                        'school_class_id' => (int) $slot->school_class_id,
                        'day_of_week' => strtolower((string) ($slot->day_of_week ?? 'all')),
                        'period' => strtolower((string) $slot->period),
                        'start_time' => substr((string) $slot->start_time, 0, 5),
                        'end_time' => substr((string) $slot->end_time, 0, 5),
                    ];
                })->values()->all();
            })
            ->toArray();
    }

    private function subjectStudySlotsByClassSubjectMap(bool $hasClassColumn, bool $hasMajorSubjectColumn, bool $hasClassStudyTimeColumn): array
    {
        if (
            !$hasClassColumn
            || !$hasMajorSubjectColumn
            || !$hasClassStudyTimeColumn
            || !Schema::hasTable('subject_study_times')
        ) {
            return [];
        }

        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');

        $columns = [
            'subject_study_times.subject_id',
            'subject_study_times.period',
            'subject_study_times.start_time',
            'subject_study_times.end_time',
        ];
        if ($hasSubjectDayColumn) {
            $columns[] = 'subject_study_times.day_of_week';
        }

        if ($hasSubjectClassColumn) {
            $columns[] = 'subject_study_times.school_class_id as class_id';
        } else {
            $columns[] = 'subjects.school_class_id as class_id';
        }

        $query = SubjectStudyTime::query()
            ->select($columns)
            ->orderBy('subject_study_times.subject_id')
            ->orderBy('sort_order')
            ->orderBy('subject_study_times.start_time');

        if ($hasSubjectClassColumn) {
            $query->whereNotNull('subject_study_times.school_class_id');
        } else {
            $query
                ->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
                ->whereNotNull('subjects.school_class_id');
        }

        return $query
            ->get()
            ->filter(fn($slot) => (int) ($slot->class_id ?? 0) > 0)
            ->groupBy(fn($slot) => (string) $slot->class_id)
            ->map(function ($group) use ($hasSubjectDayColumn) {
                return $group->groupBy(fn($slot) => (string) $slot->subject_id)
                    ->map(function ($subjectGroup) use ($hasSubjectDayColumn) {
                        return $subjectGroup
                            ->map(fn($slot) => $this->normalizeStudySlot($slot, $hasSubjectDayColumn))
                            ->unique(fn(array $slot) => $this->studySlotSignature($slot))
                            ->values()
                            ->all();
                    })
                    ->toArray();
            })
            ->toArray();
    }

    private function classStudyTimeIdsByClassSubjectMapFromSlots(array $studyTimesByClass, array $subjectStudySlotsByClassSubject): array
    {
        $mapped = [];

        foreach ($subjectStudySlotsByClassSubject as $classId => $subjects) {
            $classKey = (string) $classId;
            $classSlots = $studyTimesByClass[$classKey] ?? [];
            if (!is_array($classSlots) || $classSlots === []) {
                continue;
            }

            foreach ($subjects as $subjectId => $subjectSlots) {
                $allowedSlotIds = [];
                $subjectSlots = is_array($subjectSlots) ? $subjectSlots : [];

                foreach ($classSlots as $classSlot) {
                    $classSlot = is_array($classSlot) ? $classSlot : [];
                    $slotId = (int) ($classSlot['id'] ?? 0);
                    if ($slotId <= 0) {
                        continue;
                    }

                    $matchesSubject = collect($subjectSlots)->contains(function ($subjectSlot) use ($classSlot) {
                        return is_array($subjectSlot)
                            && $this->studySlotsAreCompatible($classSlot, $subjectSlot);
                    });

                    if ($matchesSubject) {
                        $allowedSlotIds[$slotId] = $slotId;
                    }
                }

                $ids = array_values($allowedSlotIds);
                sort($ids);
                $mapped[$classKey][(string) $subjectId] = $ids;
            }
        }

        return $mapped;
    }

    private function classStudyTimeIdsBySubjectAllMap(array $classStudyTimeIdsByClassSubject): array
    {
        $mapped = [];

        foreach ($classStudyTimeIdsByClassSubject as $subjects) {
            if (!is_array($subjects)) {
                continue;
            }

            foreach ($subjects as $subjectId => $studyTimeIds) {
                $subjectKey = (string) $subjectId;
                if (!is_array($studyTimeIds)) {
                    continue;
                }

                if (!isset($mapped[$subjectKey])) {
                    $mapped[$subjectKey] = [];
                }

                foreach ($studyTimeIds as $studyTimeId) {
                    $id = (int) $studyTimeId;
                    if ($id > 0) {
                        $mapped[$subjectKey][$id] = $id;
                    }
                }
            }
        }

        return collect($mapped)
            ->map(function (array $ids) {
                $values = array_values($ids);
                sort($values);
                return $values;
            })
            ->toArray();
    }

    private function subjectBelongsToClass(int $subjectId, int $classId): bool
    {
        if ($subjectId <= 0 || $classId <= 0) {
            return false;
        }

        if (Schema::hasTable('subject_study_times')) {
            $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
            if ($hasSubjectClassColumn) {
                $hasClassMatch = SubjectStudyTime::query()
                    ->where('subject_id', $subjectId)
                    ->where('school_class_id', $classId)
                    ->exists();
                if ($hasClassMatch) {
                    return true;
                }

                $hasAnyClassMapping = SubjectStudyTime::query()
                    ->where('subject_id', $subjectId)
                    ->whereNotNull('school_class_id')
                    ->exists();
                if ($hasAnyClassMapping) {
                    return false;
                }
            }
        }

        $subject = Subject::query()
            ->select(['id', 'school_class_id'])
            ->find($subjectId);

        return $subject !== null && (int) ($subject->school_class_id ?? 0) === $classId;
    }

    private function normalizeStudySlot(object $slot, bool $hasDayColumn): array
    {
        $day = 'all';
        if ($hasDayColumn) {
            $rawDay = strtolower(trim((string) ($slot->day_of_week ?? 'all')));
            $day = $rawDay !== '' ? $rawDay : 'all';
        }

        return [
            'day_of_week' => $day,
            'period' => strtolower(trim((string) $slot->period)),
            'start_time' => substr((string) $slot->start_time, 0, 5),
            'end_time' => substr((string) $slot->end_time, 0, 5),
        ];
    }

    private function studySlotSignature(array $slot): string
    {
        return strtolower((string) ($slot['day_of_week'] ?? 'all'))
            . '|'
            . strtolower((string) ($slot['period'] ?? ''))
            . '|'
            . substr((string) ($slot['start_time'] ?? ''), 0, 5)
            . '|'
            . substr((string) ($slot['end_time'] ?? ''), 0, 5);
    }

    private function studySlotsAreCompatible(array $classSlot, array $subjectSlot): bool
    {
        $samePeriod = strtolower((string) ($classSlot['period'] ?? '')) === strtolower((string) ($subjectSlot['period'] ?? ''));
        $sameStart = substr((string) ($classSlot['start_time'] ?? ''), 0, 5) === substr((string) ($subjectSlot['start_time'] ?? ''), 0, 5);
        $sameEnd = substr((string) ($classSlot['end_time'] ?? ''), 0, 5) === substr((string) ($subjectSlot['end_time'] ?? ''), 0, 5);

        if (!$samePeriod || !$sameStart || !$sameEnd) {
            return false;
        }

        $classDay = strtolower(trim((string) ($classSlot['day_of_week'] ?? 'all')));
        $subjectDay = strtolower(trim((string) ($subjectSlot['day_of_week'] ?? 'all')));
        $classDay = $classDay !== '' ? $classDay : 'all';
        $subjectDay = $subjectDay !== '' ? $subjectDay : 'all';

        return $classDay === $subjectDay || $classDay === 'all' || $subjectDay === 'all';
    }

    private function periodLabels(): array
    {
        return [
            'morning' => 'Morning',
            'afternoon' => 'Afternoon',
            'evening' => 'Evening',
            'night' => 'Night',
            'custom' => 'Custom',
        ];
    }

    private function allowedRoles(): array
    {
        return ['student', 'teacher', 'admin'];
    }

    private function uploadAvatarImage(Request $request, ?User $student = null): ?string
    {
        if (!$request->hasFile('avatar_image')) {
            return $student?->avatar;
        }

        $path = $request->file('avatar_image')->store('avatars/students', 'public');

        if ($student) {
            $this->deleteStoredAvatar($student->avatar);
        }

        return $path;
    }

    private function deleteStoredAvatar(?string $avatar): void
    {
        if (!$this->isLocalStorageAvatar($avatar)) {
            return;
        }

        $path = $this->normalizePublicPath($avatar);
        if ($path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function isLocalStorageAvatar(?string $avatar): bool
    {
        if (!$avatar) {
            return false;
        }

        return !Str::startsWith($avatar, ['http://', 'https://', '//', 'data:image/']);
    }

    private function normalizePublicPath(string $path): string
    {
        $normalized = ltrim($path, '/');

        if (Str::startsWith($normalized, 'storage/')) {
            return Str::after($normalized, 'storage/');
        }

        if (Str::startsWith($normalized, 'public/')) {
            return Str::after($normalized, 'public/');
        }

        return $normalized;
    }

    private function resolveAvatarUpload(Request $request, ?User $student = null): array
    {
        $file = $request->file('avatar_image');
        $current = $student?->avatar;

        if (!$file) {
            return ['path' => $current, 'warning' => null];
        }

        if (!$file->isValid()) {
            return [
                'path' => $current,
                'warning' => $student
                    ? 'Avatar upload failed: ' . $this->uploadErrorMessage($file->getError()) . ' Student was updated without changing avatar.'
                    : 'Avatar upload failed: ' . $this->uploadErrorMessage($file->getError()) . ' Student was created without avatar.',
            ];
        }

        $request->validate([
            'avatar_image' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB max
        ]);

        return [
            'path' => $this->uploadAvatarImage($request, $student),
            'warning' => null,
        ];
    }

    private function uploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE =>
            'Image is too large for current server upload limit (2MB max).',
            UPLOAD_ERR_PARTIAL =>
            'Upload was interrupted. Please try again.',
            UPLOAD_ERR_NO_TMP_DIR =>
            'Server temporary folder is missing.',
            UPLOAD_ERR_CANT_WRITE =>
            'Server failed to write uploaded file.',
            UPLOAD_ERR_EXTENSION =>
            'A PHP extension blocked the upload.',
            default =>
            'Unknown upload error.',
        };
    }
}
