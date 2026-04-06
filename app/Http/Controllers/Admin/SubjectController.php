<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $period = (string) $request->query('period', 'all');
        $schedule = (string) $request->query('schedule', 'all');
        $classIdRaw = (string) $request->query('class_id', 'all');
        $teacherIdRaw = (string) $request->query('teacher_id', 'all');
        $classId = ctype_digit($classIdRaw) ? (int) $classIdRaw : null;
        $teacherId = ctype_digit($teacherIdRaw) ? (int) $teacherIdRaw : null;

        $subjectQuery = Subject::query()
            ->with(['schoolClass', 'teacher', 'students', 'studySchedules.schoolClass', 'studySchedules.teacher'])
            ->withCount('students')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%')
                        ->orWhere('study_time', 'like', '%' . $search . '%')
                        ->orWhere('study_start_time', 'like', '%' . $search . '%')
                        ->orWhere('study_end_time', 'like', '%' . $search . '%')
                        ->orWhereHas('studySchedules', function ($scheduleQuery) use ($search) {
                            $scheduleQuery->where('period', 'like', '%' . $search . '%')
                                ->orWhere('start_time', 'like', '%' . $search . '%')
                                ->orWhere('end_time', 'like', '%' . $search . '%');
                        })
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            });

        if ($classId !== null) {
            $subjectQuery->where(function ($query) use ($classId) {
                $query->where('school_class_id', $classId)
                    ->orWhereHas('studySchedules', function ($slotQuery) use ($classId) {
                        $slotQuery->where('school_class_id', $classId);
                    });
            });
        }

        if ($teacherId !== null) {
            $subjectQuery->where(function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                    ->orWhereHas('studySchedules', function ($slotQuery) use ($teacherId) {
                        $slotQuery->where('teacher_id', $teacherId);
                    });
            });
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $subjectQuery->where('is_active', $status === 'active');
        }

        if (in_array($period, $this->allowedPeriods(), true)) {
            $subjectQuery->whereHas('studySchedules', function ($query) use ($period) {
                $query->where('period', $period);
            });
        }

        if ($schedule === 'with_schedule') {
            $subjectQuery->whereHas('studySchedules');
        } elseif ($schedule === 'without_schedule') {
            $subjectQuery->whereDoesntHave('studySchedules');
        }

        $subjects = $subjectQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $classes = SchoolClass::query()
            ->with('studySchedules')
            ->orderBy('name')
            ->orderBy('section')
            ->get();
        $teachers = User::query()
            ->where('role', 'teacher')
            ->orderBy('name')
            ->get();

        $baseStatsQuery = Subject::query();
        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'active' => (clone $baseStatsQuery)->where('is_active', true)->count(),
            'inactive' => (clone $baseStatsQuery)->where('is_active', false)->count(),
            'assigned' => (clone $baseStatsQuery)->whereNotNull('school_class_id')->count(),
            'withTeacher' => (clone $baseStatsQuery)->whereNotNull('teacher_id')->count(),
        ];

        return view('admin.subjects', [
            'subjects' => $subjects,
            'classes' => $classes,
            'teachers' => $teachers,
            'search' => $search,
            'status' => in_array($status, ['all', 'active', 'inactive'], true) ? $status : 'all',
            'classId' => $classId !== null ? (string) $classId : 'all',
            'teacherId' => $teacherId !== null ? (string) $teacherId : 'all',
            'period' => in_array($period, array_merge(['all'], $this->allowedPeriods()), true) ? $period : 'all',
            'schedule' => in_array($schedule, ['all', 'with_schedule', 'without_schedule'], true) ? $schedule : 'all',
            'stats' => $stats,
            'periodOptions' => $this->periodOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateSubjectRequest($request, 'subjectCreate');
        Subject::create($this->buildPayload(
            $validated,
            $request->boolean('is_active', true)
        ));


        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $this->validateSubjectRequest($request, 'subjectUpdate', $subject);

        $subject->update($this->buildPayload(
            $validated,
            $request->boolean('is_active')
        ));

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function toggleStatus(Subject $subject)
    {
        $subject->is_active = !$subject->is_active;
        $subject->save();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject status updated to ' . ($subject->is_active ? 'active' : 'inactive') . '.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }

    private function buildPayload(array $validated, bool $isActive): array
    {
        return [
            'name' => trim((string) $validated['name']),
            'code' => strtoupper(trim((string) $validated['code'])),
            'description' => $this->nullableTrim($validated['description'] ?? null),
            'is_active' => $isActive,
        ];
    }

    private function nullableTrim(?string $value): ?string
    {
        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }

    private function validateSubjectRequest(Request $request, string $bag, ?Subject $subject = null): array
    {
        $codeRule = Rule::unique('subjects', 'code');
        if ($subject !== null) {
            $codeRule = $codeRule->ignore($subject->id);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'code' => ['required', 'string', 'max:40', $codeRule],
            'study_start_time' => ['nullable', 'date_format:H:i', 'required_with:study_end_time'],
            'study_end_time' => ['nullable', 'date_format:H:i', 'required_with:study_start_time', 'after:study_start_time'],
            'study_slots' => ['nullable', 'array', 'max:12'],
            'study_slots.*.period' => ['nullable', 'string', 'max:20'],
            'study_slots.*.start_time' => ['nullable', 'date_format:H:i'],
            'study_slots.*.end_time' => ['nullable', 'date_format:H:i'],
            'additional_subjects' => ['nullable', 'array', 'max:20'],
            'additional_subjects.*.name' => ['nullable', 'string', 'max:120'],
            'additional_subjects.*.code' => ['nullable', 'string', 'max:40'],
            'additional_subjects.*.period' => ['nullable', 'string', 'max:20'],
            'additional_subjects.*.start_time' => ['nullable', 'date_format:H:i'],
            'additional_subjects.*.end_time' => ['nullable', 'date_format:H:i'],
            'additional_subjects.*.description' => ['nullable', 'string', 'max:1000'],
            'additional_subjects.*.is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $allowedPeriods = $this->allowedPeriods();
        $validator->after(function ($validator) use ($request, $allowedPeriods, $bag) {
            $studySlots = $request->input('study_slots', []);

            foreach (array_values(is_array($studySlots) ? $studySlots : []) as $index => $slot) {
                $period = strtolower(trim((string) ($slot['period'] ?? '')));
                $startTime = trim((string) ($slot['start_time'] ?? ''));
                $endTime = trim((string) ($slot['end_time'] ?? ''));

                if ($period === '' && $startTime === '' && $endTime === '') {
                    continue;
                }

                if ($period === '') {
                    $validator->errors()->add("study_slots.$index.period", 'The period is required for each study schedule row.');
                } elseif (!in_array($period, $allowedPeriods, true)) {
                    $validator->errors()->add("study_slots.$index.period", 'The selected period is invalid.');
                }

                if ($startTime === '') {
                    $validator->errors()->add("study_slots.$index.start_time", 'The start time is required when adding a schedule row.');
                }

                if ($endTime === '') {
                    $validator->errors()->add("study_slots.$index.end_time", 'The end time is required when adding a schedule row.');
                }

                if ($startTime !== '' && $endTime !== '' && strcmp($endTime, $startTime) <= 0) {
                    $validator->errors()->add("study_slots.$index.end_time", 'End time must be later than start time.');
                }
            }

            if ($bag !== 'subjectCreate') {
                return;
            }

            $additionalSubjects = $request->input('additional_subjects', []);
            $submittedCodes = [];
            $baseCode = strtoupper(trim((string) $request->input('code', '')));
            if ($baseCode !== '') {
                $submittedCodes[$baseCode] = 'code';
            }

            foreach (array_values(is_array($additionalSubjects) ? $additionalSubjects : []) as $index => $row) {
                $name = trim((string) ($row['name'] ?? ''));
                $code = strtoupper(trim((string) ($row['code'] ?? '')));
                $period = strtolower(trim((string) ($row['period'] ?? '')));
                $startTime = trim((string) ($row['start_time'] ?? ''));
                $endTime = trim((string) ($row['end_time'] ?? ''));
                $description = trim((string) ($row['description'] ?? ''));

                $hasAnyValue = $name !== '' || $code !== '' || $period !== '' || $startTime !== '' || $endTime !== '' || $description !== '';
                if (!$hasAnyValue) {
                    continue;
                }

                if ($name === '') {
                    $validator->errors()->add("additional_subjects.$index.name", 'The subject name is required.');
                }

                if ($code === '') {
                    $validator->errors()->add("additional_subjects.$index.code", 'The subject code is required.');
                } elseif (isset($submittedCodes[$code])) {
                    $validator->errors()->add("additional_subjects.$index.code", 'The subject code must be unique.');
                } else {
                    $submittedCodes[$code] = $index;
                }

                if ($period === '') {
                    $validator->errors()->add("additional_subjects.$index.period", 'The period is required.');
                } elseif (!in_array($period, $allowedPeriods, true)) {
                    $validator->errors()->add("additional_subjects.$index.period", 'The selected period is invalid.');
                }

                if ($startTime === '') {
                    $validator->errors()->add("additional_subjects.$index.start_time", 'The start time is required.');
                }

                if ($endTime === '') {
                    $validator->errors()->add("additional_subjects.$index.end_time", 'The end time is required.');
                }

                if ($startTime !== '' && $endTime !== '' && strcmp($endTime, $startTime) <= 0) {
                    $validator->errors()->add("additional_subjects.$index.end_time", 'End time must be later than start time.');
                }
            }

            if ($submittedCodes !== []) {
                $existingCodes = Subject::query()
                    ->whereIn('code', array_keys($submittedCodes))
                    ->pluck('code')
                    ->map(static fn($code) => strtoupper(trim((string) $code)))
                    ->all();

                foreach ($existingCodes as $existingCode) {
                    if (!array_key_exists($existingCode, $submittedCodes)) {
                        continue;
                    }

                    $rowIndex = $submittedCodes[$existingCode];
                    if ($rowIndex === 'code') {
                        continue;
                    }

                    $validator->errors()->add("additional_subjects.$rowIndex.code", 'The subject code already exists.');
                }
            }
        });

        if ($validator->fails()) {
            throw (new ValidationException($validator))->errorBag($bag);
        }

        return $validator->validated();
    }

    private function resolveStudySlots(?int $classId, array $submittedStudySlots, ?string $submittedStartTime, ?string $submittedEndTime): array
    {
        // Keep per-subject times when provided, even if a class is selected.
        $normalizedSubmittedSlots = $this->normalizeStudySlots($submittedStudySlots, $submittedStartTime, $submittedEndTime);
        if ($normalizedSubmittedSlots !== []) {
            return $normalizedSubmittedSlots;
        }

        if (!$classId) {
            return [];
        }

        $class = SchoolClass::query()
            ->with('studySchedules')
            ->whereKey($classId)
            ->first(['id', 'study_start_time', 'study_end_time', 'study_time']);

        if ($class && $class->studySchedules->isNotEmpty()) {
            return $class->studySchedules
                ->values()
                ->map(function ($slot, $index) {
                    return [
                        'period' => strtolower((string) $slot->period),
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                        'sort_order' => $index,
                    ];
                })
                ->all();
        }

        $classStartTime = $class?->study_start_time ?: $class?->study_time;
        $classEndTime = $class?->study_end_time;

        if ($classStartTime && $classEndTime) {
            return [[
                'period' => 'custom',
                'start_time' => $classStartTime,
                'end_time' => $classEndTime,
                'sort_order' => 0,
            ]];
        }

        return [];
    }

    private function normalizeStudySlots(array $studySlots, ?string $fallbackStartTime, ?string $fallbackEndTime): array
    {
        $normalized = [];
        foreach (array_values($studySlots) as $slot) {
            $period = strtolower(trim((string) ($slot['period'] ?? '')));
            $startTime = trim((string) ($slot['start_time'] ?? ''));
            $endTime = trim((string) ($slot['end_time'] ?? ''));

            if ($period === '' && $startTime === '' && $endTime === '') {
                continue;
            }

            if ($startTime === '' || $endTime === '') {
                continue;
            }

            if (!in_array($period, $this->allowedPeriods(), true)) {
                $period = 'custom';
            }

            $normalized[] = [
                'period' => $period,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        }

        if ($normalized === [] && $fallbackStartTime && $fallbackEndTime) {
            $normalized[] = [
                'period' => 'custom',
                'start_time' => $fallbackStartTime,
                'end_time' => $fallbackEndTime,
            ];
        }

        foreach ($normalized as $index => $slot) {
            $normalized[$index]['sort_order'] = $index;
        }

        return $normalized;
    }

    private function normalizeAdditionalSubjects(array $additionalSubjects, bool $defaultIsActive): array
    {
        $normalized = [];

        foreach (array_values($additionalSubjects) as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $code = strtoupper(trim((string) ($row['code'] ?? '')));
            $period = strtolower(trim((string) ($row['period'] ?? '')));
            $startTime = trim((string) ($row['start_time'] ?? ''));
            $endTime = trim((string) ($row['end_time'] ?? ''));
            $description = $this->nullableTrim($row['description'] ?? null);

            $hasAnyValue = $name !== '' || $code !== '' || $period !== '' || $startTime !== '' || $endTime !== '' || $description !== null;
            if (!$hasAnyValue) {
                continue;
            }

            if ($name === '' || $code === '' || $startTime === '' || $endTime === '') {
                continue;
            }

            if (!in_array($period, $this->allowedPeriods(), true)) {
                $period = 'custom';
            }

            $normalized[] = [
                'name' => $name,
                'code' => $code,
                'period' => $period,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'description' => $description,
                'is_active' => (string) ($row['is_active'] ?? ($defaultIsActive ? '1' : '0')) === '1',
            ];
        }

        return $normalized;
    }

    private function createAdditionalSubjects(array $validated, array $additionalSubjects): void
    {
        if ($additionalSubjects === []) {
            return;
        }

        foreach ($additionalSubjects as $row) {
            $subject = Subject::create([
                'name' => $row['name'],
                'code' => $row['code'],
                'study_time' => $row['start_time'],
                'study_start_time' => $row['start_time'],
                'study_end_time' => $row['end_time'],
                'school_class_id' => null,
                'teacher_id' => null,
                'description' => $row['description'],
                'is_active' => $row['is_active'],
            ]);

            $this->syncStudySlots($subject, [[
                'period' => $row['period'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'sort_order' => 0,
            ]]);
        }
    }

    private function syncStudySlots(Subject $subject, array $studySlots): void
    {
        $subject->studySchedules()->delete();

        if ($studySlots === []) {
            return;
        }

        $subject->studySchedules()->createMany(array_map(static function (array $slot): array {
            return [
                'period' => $slot['period'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'sort_order' => $slot['sort_order'],
            ];
        }, $studySlots));
    }

    private function allowedPeriods(): array
    {
        return ['morning', 'afternoon', 'evening', 'night', 'custom'];
    }

    private function periodOptions(): array
    {
        return [
            'morning' => 'Morning',
            'afternoon' => 'Afternoon',
            'evening' => 'Evening',
            'night' => 'Night',
            'custom' => 'Custom',
        ];
    }
}
