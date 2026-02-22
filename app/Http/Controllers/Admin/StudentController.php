<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
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
        $classId = $hasClassColumn && ctype_digit($classIdRaw) ? (int) $classIdRaw : null;

        $studentQuery = User::query()
            ->where('role', 'student')
            ->when($hasClassColumn, function ($query) {
                $query->with('schoolClass');
            })
            ->when($hasMajorSubjectColumn, function ($query) {
                $query->with('majorSubject');
            })
            ->when($hasClassStudyTimeColumn, function ($query) {
                $query->with('classStudyTime');
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

        return view('admin.students', [
            'students' => $students,
            'classes' => $classes,
            'subjectsByClass' => $subjectsByClass,
            'studyTimesByClass' => $studyTimesByClass,
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

        $avatarResult = $this->resolveAvatarUpload($request);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'student',
            'avatar' => $avatarResult['path'],
            'provider' => null,
            'google_id' => null,
        ];

        if ($this->hasStatusColumn()) {
            $payload['is_active'] = $request->boolean('is_active', true);
        }

        if ($this->hasClassColumn()) {
            $payload['school_class_id'] = $validated['school_class_id'] ?? null;
        }

        if ($this->hasMajorSubjectColumn()) {
            $payload['major_subject_id'] = $validated['major_subject_id'] ?? null;
        }

        if ($this->hasClassStudyTimeColumn()) {
            $payload['class_study_time_id'] = $validated['class_study_time_id'] ?? null;
        }

        User::create($payload);

        $redirect = redirect()
            ->route('admin.students.index')
            ->with('success', 'Student account created successfully.');

        if ($avatarResult['warning']) {
            $redirect->with('warning', $avatarResult['warning']);
        }

        return $redirect;
    }

    public function update(Request $request, User $student)
    {
        $student = $this->studentOrFail($student);
        $validated = $this->validateStudentRequest($request, $student);

        $avatarResult = $this->resolveAvatarUpload($request, $student);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
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
            $payload['school_class_id'] = $validated['school_class_id'] ?? null;
        }

        if ($this->hasMajorSubjectColumn()) {
            $payload['major_subject_id'] = $validated['major_subject_id'] ?? null;
        }

        if ($this->hasClassStudyTimeColumn()) {
            $payload['class_study_time_id'] = $validated['class_study_time_id'] ?? null;
        }

        $student->update($payload);

        $redirect = redirect()
            ->route('admin.students.index')
            ->with('success', 'Student updated successfully.');

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

    private function validateStudentRequest(Request $request, ?User $student = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email' . ($student ? ',' . $student->id : '')],
            'password' => [$student ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'is_active' => ['nullable', 'boolean'],
        ];

        $hasClassColumn = $this->hasClassColumn();
        $hasMajorSubjectColumn = $this->hasMajorSubjectColumn();
        $hasClassStudyTimeColumn = $this->hasClassStudyTimeColumn();

        if ($hasClassColumn) {
            $rules['school_class_id'] = ['nullable', 'exists:school_classes,id'];
        }

        if ($hasMajorSubjectColumn) {
            $rules['major_subject_id'] = [
                'nullable',
                'exists:subjects,id',
                $hasClassColumn ? 'required_with:school_class_id' : 'nullable',
            ];
        }

        if ($hasClassStudyTimeColumn) {
            $rules['class_study_time_id'] = [
                'nullable',
                'exists:class_study_times,id',
                $hasClassColumn ? 'required_with:school_class_id' : 'nullable',
            ];
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($request, $hasClassColumn, $hasMajorSubjectColumn, $hasClassStudyTimeColumn) {
            $classId = $hasClassColumn ? (int) ($request->input('school_class_id') ?? 0) : 0;
            $majorSubjectId = $hasMajorSubjectColumn ? (int) ($request->input('major_subject_id') ?? 0) : 0;
            $classStudyTimeId = $hasClassStudyTimeColumn ? (int) ($request->input('class_study_time_id') ?? 0) : 0;

            if ($majorSubjectId > 0) {
                $subject = Subject::query()->select(['id', 'school_class_id'])->find($majorSubjectId);
                if ($subject && $classId > 0 && (int) $subject->school_class_id !== $classId) {
                    $validator->errors()->add('major_subject_id', 'Selected major subject does not belong to the selected class.');
                }
            }

            if ($classStudyTimeId > 0) {
                $studyTime = ClassStudyTime::query()->select(['id', 'school_class_id'])->find($classStudyTimeId);
                if ($studyTime && $classId > 0 && (int) $studyTime->school_class_id !== $classId) {
                    $validator->errors()->add('class_study_time_id', 'Selected study time does not belong to the selected class.');
                }
            }
        });

        return $validator->validate();
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

        return ClassStudyTime::query()
            ->select(['id', 'school_class_id', 'period', 'start_time', 'end_time'])
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($slot) => (string) $slot->school_class_id)
            ->map(function ($group) {
                return $group->map(function ($slot) {
                    return [
                        'id' => (int) $slot->id,
                        'period' => strtolower((string) $slot->period),
                        'start_time' => substr((string) $slot->start_time, 0, 5),
                        'end_time' => substr((string) $slot->end_time, 0, 5),
                    ];
                })->values()->all();
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
