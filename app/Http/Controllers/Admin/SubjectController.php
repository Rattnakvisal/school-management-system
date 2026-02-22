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
        $classIdRaw = (string) $request->query('class_id', 'all');
        $classId = ctype_digit($classIdRaw) ? (int) $classIdRaw : null;

        $subjectQuery = Subject::query()
            ->with(['schoolClass', 'teacher', 'studySchedules'])
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
            $subjectQuery->where('school_class_id', $classId);
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $subjectQuery->where('is_active', $status === 'active');
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
            'stats' => $stats,
            'periodOptions' => $this->periodOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateSubjectRequest($request, 'subjectCreate');
        $studySlots = $this->resolveStudySlots(
            isset($validated['school_class_id']) ? (int) $validated['school_class_id'] : null,
            $validated['study_slots'] ?? [],
            $validated['study_start_time'] ?? null,
            $validated['study_end_time'] ?? null
        );

        $subject = Subject::create($this->buildPayload(
            $validated,
            $request->boolean('is_active', true),
            $studySlots
        ));
        $this->syncStudySlots($subject, $studySlots);


        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $this->validateSubjectRequest($request, 'subjectUpdate', $subject);
        $studySlots = $this->resolveStudySlots(
            isset($validated['school_class_id']) ? (int) $validated['school_class_id'] : null,
            $validated['study_slots'] ?? [],
            $validated['study_start_time'] ?? null,
            $validated['study_end_time'] ?? null
        );

        $subject->update($this->buildPayload(
            $validated,
            $request->boolean('is_active'),
            $studySlots
        ));
        $this->syncStudySlots($subject, $studySlots);

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

    private function buildPayload(array $validated, bool $isActive, array $studySlots): array
    {
        $primarySlot = $studySlots[0] ?? null;
        $studyStartTime = $primarySlot['start_time'] ?? ($validated['study_start_time'] ?? null);
        $studyEndTime = $primarySlot['end_time'] ?? ($validated['study_end_time'] ?? null);

        return [
            'name' => trim((string) $validated['name']),
            'code' => strtoupper(trim((string) $validated['code'])),
            'study_time' => $studyStartTime,
            'study_start_time' => $studyStartTime,
            'study_end_time' => $studyEndTime,
            'school_class_id' => $validated['school_class_id'] ?? null,
            'teacher_id' => $validated['teacher_id'] ?? null,
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
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'teacher_id' => ['nullable', Rule::exists('users', 'id')->where(fn($query) => $query->where('role', 'teacher'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $allowedPeriods = $this->allowedPeriods();
        $validator->after(function ($validator) use ($request, $allowedPeriods) {
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
        });

        if ($validator->fails()) {
            throw (new ValidationException($validator))->errorBag($bag);
        }

        return $validator->validated();
    }

    private function resolveStudySlots(?int $classId, array $submittedStudySlots, ?string $submittedStartTime, ?string $submittedEndTime): array
    {
        if ($classId) {
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
        }

        return $this->normalizeStudySlots($submittedStudySlots, $submittedStartTime, $submittedEndTime);
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
