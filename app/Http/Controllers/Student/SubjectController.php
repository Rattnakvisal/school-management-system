<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();
        abort_unless($student instanceof User, 403);

        $student->loadMissing([
            'schoolClass:id,name,section,room',
            'majorSubject',
            'majorSubject.teacher:id,name',
        ]);

        $dayOptions = [
            'all' => 'All Days',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        $selectedDayRaw = strtolower(trim((string) $request->query('day', 'all')));
        $selectedDay = array_key_exists($selectedDayRaw, $dayOptions) ? $selectedDayRaw : 'all';
        $todayKey = strtolower(now()->englishDayOfWeek);
        $classId = (int) ($student->school_class_id ?? 0);

        $hasSubjectStatusColumn = Schema::hasColumn('subjects', 'is_active');
        $hasSubjectStudyTimesTable = Schema::hasTable('subject_study_times');
        $hasSubjectDayColumn = $hasSubjectStudyTimesTable && Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = $hasSubjectStudyTimesTable && Schema::hasColumn('subject_study_times', 'school_class_id');

        $classSubjects = collect();
        if ($classId > 0) {
            $classSubjectsQuery = Subject::query()
                ->with('teacher:id,name')
                ->where('school_class_id', $classId);

            if ($hasSubjectStatusColumn) {
                $classSubjectsQuery->where('is_active', true);
            }

            $classSubjects = $classSubjectsQuery
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'description', 'teacher_id', 'school_class_id']);
        }

        $majorSubjects = $this->resolveMajorSubjects($student, $hasSubjectStatusColumn);
        $classSubjectIds = $classSubjects
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id) => $id > 0)
            ->values()
            ->all();
        $majorSubjectIds = $majorSubjects
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id) => $id > 0)
            ->values()
            ->all();
        $majorLookup = array_fill_keys($majorSubjectIds, true);

        $allSubjects = $classSubjects
            ->concat(
                $majorSubjects->reject(fn(Subject $subject) => in_array((int) $subject->id, $classSubjectIds, true))
            )
            ->sortBy(fn(Subject $subject) => strtolower((string) $subject->name))
            ->values()
            ->map(function (Subject $subject) use ($majorLookup, $classId) {
                $subject->setAttribute('is_major_subject', isset($majorLookup[(int) $subject->id]));
                $subject->setAttribute('is_class_subject', (int) ($subject->school_class_id ?? 0) === $classId);

                return $subject;
            });

        $subjectSlots = $this->resolveSubjectSlots(
            $allSubjects,
            $classId,
            $selectedDay,
            $hasSubjectDayColumn,
            $hasSubjectClassColumn
        );

        $subjectSlotCounts = $subjectSlots
            ->groupBy('subject_id')
            ->map(static fn(Collection $slots): int => $slots->count())
            ->toArray();

        $scheduleRows = $subjectSlots
            ->map(function (SubjectStudyTime $slot) use (
                $dayOptions,
                $hasSubjectDayColumn,
                $todayKey,
                $student,
                $majorLookup,
                $classSubjectIds
            ): array {
                $dayKey = $this->normalizeDayKey($hasSubjectDayColumn ? (string) ($slot->day_of_week ?? 'all') : 'all');
                $teacherName = trim((string) (
                    $slot->teacher?->name
                    ?? $slot->subject?->teacher?->name
                    ?? ''
                ));
                $subjectId = (int) ($slot->subject_id ?? 0);

                return [
                    'day_key' => $dayKey,
                    'day_label' => $dayOptions[$dayKey] ?? ucfirst($dayKey),
                    'day_sort' => $this->daySortOrder($dayKey),
                    'subject_name' => (string) ($slot->subject?->name ?? 'Unknown Subject'),
                    'subject_code' => (string) ($slot->subject?->code ?? ''),
                    'subject_description' => (string) ($slot->subject?->description ?? ''),
                    'subject_is_major' => isset($majorLookup[$subjectId]),
                    'subject_is_class' => in_array($subjectId, $classSubjectIds, true),
                    'teacher_name' => $teacherName !== '' ? $teacherName : 'Not assigned',
                    'teacher_email' => (string) ($slot->teacher?->email ?? $slot->subject?->teacher?->email ?? ''),
                    'class_label' => $slot->schoolClass?->display_name
                        ?? $slot->subject?->schoolClass?->display_name
                        ?? $student->schoolClass?->display_name
                        ?? 'Unassigned',
                    'class_code' => trim((string) (
                        $slot->schoolClass?->section
                        ?? $slot->subject?->schoolClass?->section
                        ?? ''
                    )),
                    'period_label' => $this->periodLabel((string) ($slot->period ?? '')),
                    'start_label' => $this->formatTime((string) ($slot->start_time ?? '')),
                    'end_label' => $this->formatTime((string) ($slot->end_time ?? '')),
                    'start_sort' => substr((string) ($slot->start_time ?? ''), 0, 8),
                    'is_today' => $this->dayMatches($dayKey, $todayKey),
                ];
            })
            ->sortBy(static function (array $row): string {
                return sprintf(
                    '%02d|%s|%s',
                    (int) ($row['day_sort'] ?? 99),
                    (string) ($row['start_sort'] ?? '99:99:99'),
                    strtolower((string) ($row['subject_name'] ?? ''))
                );
            })
            ->values();

        $classLabel = $student->schoolClass?->display_name ?? 'No class assigned';

        return view('student.subjects', [
            'student' => $student,
            'classLabel' => $classLabel,
            'subjects' => $allSubjects,
            'classSubjectsCount' => $classSubjects->count(),
            'majorSubjectsCount' => $majorSubjects->count(),
            'subjectSlotCounts' => $subjectSlotCounts,
            'scheduleRows' => $scheduleRows,
            'scheduleCount' => $scheduleRows->count(),
            'todayScheduleCount' => $scheduleRows->where('is_today', true)->count(),
            'todayKey' => $todayKey,
            'selectedDay' => $selectedDay,
            'dayOptions' => $dayOptions,
            'dayColumnEnabled' => $hasSubjectDayColumn,
        ]);
    }

    private function resolveMajorSubjects(User $student, bool $hasSubjectStatusColumn): Collection
    {
        if (Schema::hasTable('student_major_subjects')) {
            $query = $student->majorSubjects()
                ->with('teacher:id,name')
                ->orderBy('subjects.name');

            if ($hasSubjectStatusColumn) {
                $query->where('subjects.is_active', true);
            }

            return $query->get([
                'subjects.id',
                'subjects.name',
                'subjects.code',
                'subjects.description',
                'subjects.teacher_id',
                'subjects.school_class_id',
            ]);
        }

        if (!$student->majorSubject) {
            return collect();
        }

        if ($hasSubjectStatusColumn && !$student->majorSubject->is_active) {
            return collect();
        }

        return collect([$student->majorSubject]);
    }

    private function resolveSubjectSlots(
        Collection $subjects,
        int $classId,
        string $selectedDay,
        bool $hasSubjectDayColumn,
        bool $hasSubjectClassColumn
    ): Collection {
        if (!$subjects->isNotEmpty() || !Schema::hasTable('subject_study_times')) {
            return collect();
        }

        $subjectIds = $subjects
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id) => $id > 0)
            ->values()
            ->all();

        if ($subjectIds === []) {
            return collect();
        }

        $query = SubjectStudyTime::query()
            ->with([
                'subject:id,name,code,description,teacher_id,school_class_id',
                'subject.teacher:id,name,email',
                'schoolClass:id,name,section',
                'teacher:id,name,email',
            ])
            ->whereIn('subject_id', $subjectIds);

        if ($hasSubjectClassColumn && $classId > 0) {
            $query->where(function ($inner) use ($classId) {
                $inner->where('school_class_id', $classId)
                    ->orWhereNull('school_class_id');
            });
        }

        if ($hasSubjectDayColumn && $selectedDay !== 'all') {
            $query->where(function ($inner) use ($selectedDay) {
                $inner->where('day_of_week', $selectedDay)
                    ->orWhere('day_of_week', 'all');
            });
        }

        $slots = $query
            ->orderBy('subject_id')
            ->when($hasSubjectClassColumn, function ($builder) {
                $builder->orderByRaw('CASE WHEN school_class_id IS NULL THEN 1 ELSE 0 END');
            })
            ->when($hasSubjectDayColumn, function ($builder) {
                $builder->orderByRaw("CASE day_of_week
                    WHEN 'monday' THEN 1
                    WHEN 'tuesday' THEN 2
                    WHEN 'wednesday' THEN 3
                    WHEN 'thursday' THEN 4
                    WHEN 'friday' THEN 5
                    WHEN 'saturday' THEN 6
                    WHEN 'sunday' THEN 7
                    ELSE 8 END");
            })
            ->orderBy('start_time')
            ->orderBy('sort_order')
            ->get();

        return $this->prioritizeClassSpecificSlots($slots, $classId, $hasSubjectClassColumn)->values();
    }

    private function prioritizeClassSpecificSlots(Collection $slots, int $classId, bool $hasSubjectClassColumn): Collection
    {
        if (!$hasSubjectClassColumn || $classId <= 0) {
            return $slots;
        }

        return $slots
            ->groupBy('subject_id')
            ->flatMap(function (Collection $group) use ($classId) {
                $classSpecific = $group->where('school_class_id', $classId)->values();
                if ($classSpecific->isNotEmpty()) {
                    return $classSpecific;
                }

                $sharedSlots = $group->whereNull('school_class_id')->values();
                if ($sharedSlots->isNotEmpty()) {
                    return $sharedSlots;
                }

                return $group->values();
            })
            ->values();
    }

    private function normalizeDayKey(?string $day): string
    {
        $normalized = strtolower(trim((string) ($day ?? 'all')));
        $allowed = ['all', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return in_array($normalized, $allowed, true) ? $normalized : 'all';
    }

    private function daySortOrder(string $day): int
    {
        return match ($this->normalizeDayKey($day)) {
            'all' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 7,
            default => 9,
        };
    }

    private function dayMatches(?string $slotDay, string $targetDay): bool
    {
        $slot = $this->normalizeDayKey($slotDay);
        return $slot === 'all' || $slot === strtolower(trim($targetDay));
    }

    private function periodLabel(string $period): string
    {
        $normalized = strtolower(trim($period));
        $labels = [
            'morning' => 'Morning',
            'afternoon' => 'Afternoon',
            'evening' => 'Evening',
            'night' => 'Night',
            'custom' => 'Custom',
        ];

        return $labels[$normalized] ?? ucfirst($normalized !== '' ? $normalized : 'Session');
    }

    private function formatTime(string $time): string
    {
        $value = trim($time);
        if ($value === '') {
            return '--:--';
        }

        try {
            return Carbon::parse($value)->format('h:i A');
        } catch (Throwable) {
            return strtoupper(substr($value, 0, 5));
        }
    }
}
