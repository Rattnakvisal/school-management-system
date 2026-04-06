<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = (int) ($request->user()?->id ?? 0);
        $dayKeys = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $dayLabels = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
        $periodLabels = [
            'morning' => 'Morning',
            'afternoon' => 'Afternoon',
            'evening' => 'Evening',
            'night' => 'Night',
            'custom' => 'Custom',
        ];

        $todayKey = strtolower(Carbon::now()->englishDayOfWeek);
        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasSubjectTeacherColumn = Schema::hasColumn('subject_study_times', 'teacher_id');
        $useSlotAssignments = $hasSubjectClassColumn && $hasSubjectTeacherColumn;

        if ($useSlotAssignments) {
            $teacherClassIds = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->whereNotNull('school_class_id')
                ->distinct()
                ->pluck('school_class_id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->values();

            $teacherClasses = SchoolClass::query()
                ->whereIn('id', $teacherClassIds->all())
                ->withCount('students')
                ->orderBy('name')
                ->orderBy('section')
                ->get(['id', 'name', 'section']);

            $subjectCountsByClass = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->whereIn('school_class_id', $teacherClassIds->all())
                ->select('school_class_id', DB::raw('COUNT(DISTINCT subject_id) as subject_count'))
                ->groupBy('school_class_id')
                ->pluck('subject_count', 'school_class_id');

            $teacherClasses->each(function (SchoolClass $schoolClass) use ($subjectCountsByClass): void {
                $schoolClass->setAttribute(
                    'taught_subjects_count',
                    (int) $subjectCountsByClass->get((int) $schoolClass->id, 0)
                );
            });
        } else {
            $teacherClasses = SchoolClass::query()
                ->whereHas('subjects', function ($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId);
                })
                ->withCount([
                    'students',
                    'subjects as taught_subjects_count' => function ($query) use ($teacherId) {
                        $query->where('teacher_id', $teacherId);
                    },
                ])
                ->orderBy('name')
                ->orderBy('section')
                ->get(['id', 'name', 'section']);
        }

        $classIds = $teacherClasses->pluck('id')->map(fn($id) => (int) $id)->values();

        $classSlotsQuery = ClassStudyTime::query()
            ->with('schoolClass:id,name,section')
            ->whereIn('school_class_id', $classIds)
            ->orderBy('school_class_id');

        if ($hasClassDayColumn) {
            $classSlotsQuery->orderByRaw("CASE day_of_week
                WHEN 'monday' THEN 1
                WHEN 'tuesday' THEN 2
                WHEN 'wednesday' THEN 3
                WHEN 'thursday' THEN 4
                WHEN 'friday' THEN 5
                WHEN 'saturday' THEN 6
                WHEN 'sunday' THEN 7
                ELSE 8 END");
        }

        $classSlots = $classSlotsQuery
            ->orderBy('start_time')
            ->get();

        $subjectSlotsQuery = SubjectStudyTime::query()
            ->with(['subject.schoolClass:id,name,section', 'schoolClass:id,name,section'])
            ->when($useSlotAssignments, function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            }, function ($query) use ($teacherId) {
                $query->whereHas('subject', function ($subjectQuery) use ($teacherId) {
                    $subjectQuery->where('teacher_id', $teacherId);
                });
            })
            ->orderBy('subject_id');

        if ($hasSubjectDayColumn) {
            $subjectSlotsQuery->orderByRaw("CASE day_of_week
                WHEN 'monday' THEN 1
                WHEN 'tuesday' THEN 2
                WHEN 'wednesday' THEN 3
                WHEN 'thursday' THEN 4
                WHEN 'friday' THEN 5
                WHEN 'saturday' THEN 6
                WHEN 'sunday' THEN 7
                ELSE 8 END");
        }

        $subjectSlots = $subjectSlotsQuery
            ->orderBy('start_time')
            ->get();

        $dayMatches = static function (?string $slotDay, string $day): bool {
            $normalized = strtolower((string) ($slotDay ?? 'all'));
            return $normalized === 'all' || $normalized === $day;
        };

        $weeklySummary = collect($dayKeys)->map(function (string $dayKey) use ($classSlots, $subjectSlots, $dayLabels, $dayMatches) {
            $classCount = $classSlots->filter(fn($slot) => $dayMatches($slot->day_of_week ?? 'all', $dayKey))->count();
            $subjectCount = $subjectSlots->filter(fn($slot) => $dayMatches($slot->day_of_week ?? 'all', $dayKey))->count();

            return [
                'key' => $dayKey,
                'label' => $dayLabels[$dayKey] ?? ucfirst($dayKey),
                'class_count' => $classCount,
                'subject_count' => $subjectCount,
                'total' => $classCount + $subjectCount,
            ];
        })->values();

        $todayClassSlots = $classSlots
            ->filter(fn($slot) => $dayMatches($slot->day_of_week ?? 'all', $todayKey))
            ->map(function ($slot) use ($periodLabels) {
                $startAt = Carbon::parse($slot->start_time);
                $endAt = Carbon::parse($slot->end_time);

                return [
                    'type' => 'class',
                    'title' => $slot->schoolClass?->display_name ?? 'Class Schedule',
                    'subtitle' => 'Class Time',
                    'period' => $periodLabels[strtolower((string) $slot->period)] ?? ucfirst((string) $slot->period),
                    'start' => $startAt->format('h:i A'),
                    'end' => $endAt->format('h:i A'),
                    'start_24' => $startAt->format('H:i:s'),
                    'end_24' => $endAt->format('H:i:s'),
                    'start_sort' => (string) $slot->start_time,
                ];
            });

        $todaySubjectSlots = $subjectSlots
            ->filter(fn($slot) => $dayMatches($slot->day_of_week ?? 'all', $todayKey))
            ->map(function ($slot) use ($periodLabels) {
                $startAt = Carbon::parse($slot->start_time);
                $endAt = Carbon::parse($slot->end_time);

                return [
                    'type' => 'subject',
                    'title' => $slot->subject?->name ?? 'Subject Schedule',
                    'subtitle' => $slot->schoolClass?->display_name ?? $slot->subject?->schoolClass?->display_name ?? 'Unassigned class',
                    'period' => $periodLabels[strtolower((string) $slot->period)] ?? ucfirst((string) $slot->period),
                    'start' => $startAt->format('h:i A'),
                    'end' => $endAt->format('h:i A'),
                    'start_24' => $startAt->format('H:i:s'),
                    'end_24' => $endAt->format('H:i:s'),
                    'start_sort' => (string) $slot->start_time,
                ];
            });

        $todayTimeline = $todayClassSlots
            ->concat($todaySubjectSlots)
            ->sortBy('start_sort')
            ->values()
            ->all();

        $subjectCount = $useSlotAssignments
            ? (int) SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->distinct('subject_id')
                ->count('subject_id')
            : (int) Subject::query()->where('teacher_id', $teacherId)->count();

        $stats = [
            'classes' => $teacherClasses->count(),
            'students' => (int) $teacherClasses->sum('students_count'),
            'subjects' => $subjectCount,
            'todaySchedules' => count($todayTimeline),
        ];

        $todayTypeCounts = collect($todayTimeline)
            ->groupBy('type')
            ->map(fn($items) => count($items))
            ->all();

        $chartData = [
            'weekly' => [
                'labels' => $weeklySummary->pluck('label')->values()->all(),
                'classes' => $weeklySummary->pluck('class_count')->map(fn($value) => (int) $value)->values()->all(),
                'subjects' => $weeklySummary->pluck('subject_count')->map(fn($value) => (int) $value)->values()->all(),
            ],
            'todayMix' => [
                'labels' => ['Class', 'Subject'],
                'values' => [
                    (int) ($todayTypeCounts['class'] ?? 0),
                    (int) ($todayTypeCounts['subject'] ?? 0),
                ],
            ],
        ];

        return view('teacher.dashboard', [
            'todayKey' => $todayKey,
            'dayLabels' => $dayLabels,
            'periodLabels' => $periodLabels,
            'stats' => $stats,
            'todayTimeline' => $todayTimeline,
            'weeklySummary' => $weeklySummary,
            'maxWeeklyTotal' => max(1, (int) $weeklySummary->max('total')),
            'chartData' => $chartData,
        ]);
    }
}
