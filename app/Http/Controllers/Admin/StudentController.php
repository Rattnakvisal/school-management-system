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
            ->when($hasClassStudyTimeColumn, function ($query) use ($hasStudentStudyTimesTable) {
                $query->with('classStudyTime');
                if ($hasStudentStudyTimesTable) {
                    $query->with('studyTimes');
                }
            })
            ->when($search !== '', function ($query) use ($search, $hasClassColumn) {
                $query->where(function ($inner) use ($search, $hasClassColumn) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');

                    if ($hasClassColumn) {
                        $inner->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                            $classQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('section', 'like', '%' . $search . '%');
                        });
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
        $subjectStudySlotKeysBySubject = $this->subjectStudySlotKeysBySubjectMap($hasMajorSubjectColumn, $hasClassStudyTimeColumn);

        return view('admin.students', [
            'students' => $students,
            'classes' => $classes,
            'subjectsByClass' => $subjectsByClass,
            'studyTimesByClass' => $studyTimesByClass,
            'subjectStudySlotKeysBySubject' => $subjectStudySlotKeysBySubject,
            'periodLabels' => $this->periodLabels(),
            'search' => $search,
            'status' => in_array($status, ['all', 'active', 'inactive'], true) ? $status : 'all',
            'classId' => $classId !== null ? (string) $classId : 'all',
            'stats' => $stats,
            'hasStatusColumn' => $hasStatusColumn,
            'hasClassColumn' => $hasClassColumn,
            'hasMajorSubjectColumn' => $hasMajorSubjectColumn,
            'hasClassStudyTimeColumn' => $hasClassStudyTimeColumn,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateStudentRequest($request);
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
                ? ($validated['major_subject_id'] ?? null)
                : null;
        }

        if ($this->hasClassStudyTimeColumn()) {
            $payload['class_study_time_id'] = $payload['role'] === 'student'
                ? ($selectedStudyTimeIds[0] ?? null)
                : null;
        }

        $student = User::create($payload);
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
                ? ($validated['major_subject_id'] ?? null)
                : null;
        }

        if ($this->hasClassStudyTimeColumn()) {
            $payload['class_study_time_id'] = $payload['role'] === 'student'
                ? ($selectedStudyTimeIds[0] ?? null)
                : null;
        }

        $student->update($payload);
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

    private function hasStudentStudyTimesTable(): bool
    {
        return Schema::hasTable('student_study_times');
    }

    private function validateStudentRequest(Request $request, ?User $student = null): array
    {
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
            $rules['major_subject_id'] = [
                'exclude_unless:role,student',
                'nullable',
                'exists:subjects,id',
                $hasClassColumn ? 'required_with:school_class_id' : 'nullable',
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

        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($request, $hasClassColumn, $hasMajorSubjectColumn, $hasClassStudyTimeColumn) {
            $role = strtolower((string) $request->input('role', 'student'));
            if ($role !== 'student') {
                return;
            }

            $classId = $hasClassColumn ? (int) ($request->input('school_class_id') ?? 0) : 0;
            $majorSubjectId = $hasMajorSubjectColumn ? (int) ($request->input('major_subject_id') ?? 0) : 0;
            $selectedStudyTimeIds = $hasClassStudyTimeColumn
                ? $this->extractSelectedStudyTimeIdsFromRequest($request)
                : [];

            if ($majorSubjectId > 0) {
                $subject = Subject::query()->select(['id', 'school_class_id'])->find($majorSubjectId);
                if ($subject && $classId > 0 && (int) $subject->school_class_id !== $classId) {
                    $validator->errors()->add('major_subject_id', 'Selected major subject does not belong to the selected class.');
                }
            }

            if ($hasClassStudyTimeColumn && !empty($selectedStudyTimeIds)) {
                if ($hasClassColumn && $classId <= 0) {
                    $validator->errors()->add('class_study_time_ids', 'Please select class before selecting study time.');
                    return;
                }

                if ($classId > 0) {
                    $validCount = ClassStudyTime::query()
                        ->whereIn('id', $selectedStudyTimeIds)
                        ->where('school_class_id', $classId)
                        ->count();

                    if ($validCount !== count($selectedStudyTimeIds)) {
                        $validator->errors()->add(
                            'class_study_time_ids',
                            'One or more selected study times do not belong to the selected class.'
                        );
                        return;
                    }
                }

                if ($majorSubjectId > 0 && Schema::hasTable('subject_study_times')) {
                    $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
                    $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
                    $useDayInSlotKey = $hasClassDayColumn && $hasSubjectDayColumn;

                    $subjectColumns = ['period', 'start_time', 'end_time'];
                    if ($hasSubjectDayColumn) {
                        $subjectColumns[] = 'day_of_week';
                    }

                    $subjectSlotKeys = SubjectStudyTime::query()
                        ->where('subject_id', $majorSubjectId)
                        ->get($subjectColumns)
                        ->map(function ($slot) use ($useDayInSlotKey) {
                            $dayPrefix = $useDayInSlotKey
                                ? strtolower((string) ($slot->day_of_week ?? 'all')) . '|'
                                : '';

                            return $dayPrefix . strtolower((string) $slot->period)
                                . '|'
                                . substr((string) $slot->start_time, 0, 5)
                                . '|'
                                . substr((string) $slot->end_time, 0, 5);
                        })
                        ->unique()
                        ->values();

                    if ($subjectSlotKeys->isNotEmpty()) {
                        $classColumns = ['period', 'start_time', 'end_time'];
                        if ($hasClassDayColumn) {
                            $classColumns[] = 'day_of_week';
                        }

                        $selectedSlotKeys = ClassStudyTime::query()
                            ->whereIn('id', $selectedStudyTimeIds)
                            ->get($classColumns)
                            ->map(function ($slot) use ($useDayInSlotKey) {
                                $dayPrefix = $useDayInSlotKey
                                    ? strtolower((string) ($slot->day_of_week ?? 'all')) . '|'
                                    : '';

                                return $dayPrefix . strtolower((string) $slot->period)
                                    . '|'
                                    . substr((string) $slot->start_time, 0, 5)
                                    . '|'
                                    . substr((string) $slot->end_time, 0, 5);
                            })
                            ->values();

                        $invalidSelections = $selectedSlotKeys
                            ->filter(fn(string $slotKey) => !$subjectSlotKeys->contains($slotKey))
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

    private function subjectsByClassMap(bool $hasClassColumn, bool $hasMajorSubjectColumn): array
    {
        if (!$hasClassColumn || !$hasMajorSubjectColumn) {
            return [];
        }

        return Subject::query()
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
                    ];
                })->values()->all();
            })
            ->toArray();
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
                        'day_of_week' => strtolower((string) ($slot->day_of_week ?? 'all')),
                        'period' => strtolower((string) $slot->period),
                        'start_time' => substr((string) $slot->start_time, 0, 5),
                        'end_time' => substr((string) $slot->end_time, 0, 5),
                    ];
                })->values()->all();
            })
            ->toArray();
    }

    private function subjectStudySlotKeysBySubjectMap(bool $hasMajorSubjectColumn, bool $hasClassStudyTimeColumn): array
    {
        if (!$hasMajorSubjectColumn || !$hasClassStudyTimeColumn || !Schema::hasTable('subject_study_times')) {
            return [];
        }

        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $useDayInSlotKey = $hasClassDayColumn && $hasSubjectDayColumn;

        $columns = ['subject_id', 'period', 'start_time', 'end_time'];
        if ($hasSubjectDayColumn) {
            $columns[] = 'day_of_week';
        }

        return SubjectStudyTime::query()
            ->select($columns)
            ->orderBy('subject_id')
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($slot) => (string) $slot->subject_id)
            ->map(function ($group) use ($useDayInSlotKey) {
                return $group
                    ->map(function ($slot) use ($useDayInSlotKey) {
                        $dayPrefix = $useDayInSlotKey
                            ? strtolower((string) ($slot->day_of_week ?? 'all')) . '|'
                            : '';

                        return $dayPrefix . strtolower((string) $slot->period)
                            . '|'
                            . substr((string) $slot->start_time, 0, 5)
                            . '|'
                            . substr((string) $slot->end_time, 0, 5);
                    })
                    ->unique()
                    ->values()
                    ->all();
            })
            ->toArray();
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
