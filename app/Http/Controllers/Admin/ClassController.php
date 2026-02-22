<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $period = (string) $request->query('period', 'all');
        $schedule = (string) $request->query('schedule', 'all');

        $classQuery = SchoolClass::query()
            ->with('studySchedules')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('section', 'like', '%' . $search . '%')
                        ->orWhere('room', 'like', '%' . $search . '%')
                        ->orWhere('study_time', 'like', '%' . $search . '%')
                        ->orWhere('study_start_time', 'like', '%' . $search . '%')
                        ->orWhere('study_end_time', 'like', '%' . $search . '%')
                        ->orWhereHas('studySchedules', function ($scheduleQuery) use ($search) {
                            $scheduleQuery->where('period', 'like', '%' . $search . '%')
                                ->orWhere('start_time', 'like', '%' . $search . '%')
                                ->orWhere('end_time', 'like', '%' . $search . '%');
                        });
                });
            });

        if (in_array($status, ['active', 'inactive'], true)) {
            $classQuery->where('is_active', $status === 'active');
        }

        if (in_array($period, $this->allowedPeriods(), true)) {
            $classQuery->whereHas('studySchedules', function ($query) use ($period) {
                $query->where('period', $period);
            });
        }

        if ($schedule === 'with_schedule') {
            $classQuery->whereHas('studySchedules');
        } elseif ($schedule === 'without_schedule') {
            $classQuery->whereDoesntHave('studySchedules');
        }

        $classQuery->withCount('subjects');
        $classQuery->withCount('studySchedules');

        if ($this->hasStudentClassColumn()) {
            $classQuery->withCount('students');
        }

        $classes = $classQuery->latest()->paginate(10)->withQueryString();

        $baseStatsQuery = SchoolClass::query();
        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'active' => (clone $baseStatsQuery)->where('is_active', true)->count(),
            'inactive' => (clone $baseStatsQuery)->where('is_active', false)->count(),
            'withSchedule' => (clone $baseStatsQuery)->whereHas('studySchedules')->count(),
        ];

        return view('admin.classes', [
            'classes' => $classes,
            'search' => $search,
            'status' => in_array($status, ['all', 'active', 'inactive'], true) ? $status : 'all',
            'period' => in_array($period, array_merge(['all'], $this->allowedPeriods()), true) ? $period : 'all',
            'schedule' => in_array($schedule, ['all', 'with_schedule', 'without_schedule'], true) ? $schedule : 'all',
            'periodOptions' => $this->periodOptions(),
            'stats' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateClassRequest($request);
        $studySlots = $this->normalizeStudySlots(
            $validated['study_slots'] ?? [],
            $validated['study_start_time'] ?? null,
            $validated['study_end_time'] ?? null
        );

        $schoolClass = SchoolClass::create($this->buildPayload(
            $validated,
            $request->boolean('is_active', true),
            $studySlots
        ));
        $this->syncStudySlots($schoolClass, $studySlots);

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function update(Request $request, SchoolClass $schoolClass)
    {
        $validated = $this->validateClassRequest($request);
        $studySlots = $this->normalizeStudySlots(
            $validated['study_slots'] ?? [],
            $validated['study_start_time'] ?? null,
            $validated['study_end_time'] ?? null
        );

        $schoolClass->update($this->buildPayload(
            $validated,
            $request->boolean('is_active'),
            $studySlots
        ));
        $this->syncStudySlots($schoolClass, $studySlots);

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function toggleStatus(SchoolClass $schoolClass)
    {
        $schoolClass->is_active = !$schoolClass->is_active;
        $schoolClass->save();

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Class status updated to ' . ($schoolClass->is_active ? 'active' : 'inactive') . '.');
    }

    public function destroy(SchoolClass $schoolClass)
    {
        $schoolClass->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }

    private function buildPayload(array $validated, bool $isActive, array $studySlots): array
    {
        $primarySlot = $studySlots[0] ?? null;
        $studyStartTime = $primarySlot['start_time'] ?? ($validated['study_start_time'] ?? null);
        $studyEndTime = $primarySlot['end_time'] ?? ($validated['study_end_time'] ?? null);

        return [
            'name' => trim((string) $validated['name']),
            'section' => $this->nullableTrim($validated['section'] ?? null),
            'room' => $this->nullableTrim($validated['room'] ?? null),
            'study_time' => $studyStartTime,
            'study_start_time' => $studyStartTime,
            'study_end_time' => $studyEndTime,
            'capacity' => $validated['capacity'] ?? null,
            'description' => $this->nullableTrim($validated['description'] ?? null),
            'is_active' => $isActive,
        ];
    }

    private function nullableTrim(?string $value): ?string
    {
        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }

    private function validateClassRequest(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'section' => ['nullable', 'string', 'max:80'],
            'room' => ['nullable', 'string', 'max:80'],
            'study_start_time' => ['nullable', 'date_format:H:i', 'required_with:study_end_time'],
            'study_end_time' => ['nullable', 'date_format:H:i', 'required_with:study_start_time', 'after:study_start_time'],
            'study_slots' => ['nullable', 'array', 'max:12'],
            'study_slots.*.period' => ['nullable', 'string', 'max:20'],
            'study_slots.*.start_time' => ['nullable', 'date_format:H:i'],
            'study_slots.*.end_time' => ['nullable', 'date_format:H:i'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
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

        return $validator->validate();
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

    private function syncStudySlots(SchoolClass $schoolClass, array $studySlots): void
    {
        $schoolClass->studySchedules()->delete();

        if ($studySlots === []) {
            return;
        }

        $schoolClass->studySchedules()->createMany(array_map(static function (array $slot): array {
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

    private function hasStudentClassColumn(): bool
    {
        return Schema::hasColumn('users', 'school_class_id');
    }
}
