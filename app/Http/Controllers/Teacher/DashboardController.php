<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\SchoolClass;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
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
        $teacherStudents = User::query()
            ->where('role', 'student')
            ->whereIn('school_class_id', $classIds->all())
            ->with('schoolClass:id,name,section')
            ->orderBy('name')
            ->get(['id', 'name', 'avatar', 'school_class_id']);

        $attendanceByStudent = StudentAttendance::query()
            ->select(
                'student_id',
                DB::raw("SUM(CASE WHEN status IN ('present', 'late') THEN 1 ELSE 0 END) as attended_count"),
                DB::raw('COUNT(*) as total_count')
            )
            ->where('teacher_id', $teacherId)
            ->whereIn('student_id', $teacherStudents->pluck('id')->all())
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

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
            ->map(function ($slot) use ($periodLabels, $dayLabels, $todayKey) {
                $startAt = Carbon::parse($slot->start_time);
                $endAt = Carbon::parse($slot->end_time);
                $startSort = (string) $slot->start_time;
                $endSort = (string) $slot->end_time;
                $slotDayKey = strtolower((string) ($slot->day_of_week ?? $todayKey));
                $slotKey = implode('|', [
                    (int) ($slot->school_class_id ?? 0),
                    strtolower((string) $slot->period),
                    $startSort,
                    $endSort,
                ]);

                return [
                    'slot_key' => $slotKey,
                    'slot_type' => 'class',
                    'day_key' => $slotDayKey,
                    'day_label' => $dayLabels[$slotDayKey] ?? ucfirst($slotDayKey),
                    'class_name' => $slot->schoolClass?->display_name ?? 'Class Schedule',
                    'subject_name' => null,
                    'subject_names' => [],
                    'period' => $periodLabels[strtolower((string) $slot->period)] ?? ucfirst((string) $slot->period),
                    'start' => $startAt->format('h:i A'),
                    'end' => $endAt->format('h:i A'),
                    'start_24' => $startAt->format('H:i:s'),
                    'end_24' => $endAt->format('H:i:s'),
                    'start_sort' => $startSort,
                    'end_sort' => $endSort,
                ];
            });

        $todaySubjectSlots = $subjectSlots
            ->filter(fn($slot) => $dayMatches($slot->day_of_week ?? 'all', $todayKey))
            ->map(function ($slot) use ($periodLabels, $dayLabels, $todayKey) {
                $startAt = Carbon::parse($slot->start_time);
                $endAt = Carbon::parse($slot->end_time);
                $startSort = (string) $slot->start_time;
                $endSort = (string) $slot->end_time;
                $slotDayKey = strtolower((string) ($slot->day_of_week ?? $todayKey));
                $slotKey = implode('|', [
                    (int) ($slot->school_class_id ?? $slot->subject?->school_class_id ?? 0),
                    strtolower((string) $slot->period),
                    $startSort,
                    $endSort,
                ]);

                return [
                    'slot_key' => $slotKey,
                    'slot_type' => 'subject',
                    'day_key' => $slotDayKey,
                    'day_label' => $dayLabels[$slotDayKey] ?? ucfirst($slotDayKey),
                    'class_name' => $slot->schoolClass?->display_name ?? $slot->subject?->schoolClass?->display_name ?? 'Unassigned class',
                    'subject_name' => $slot->subject?->name ?? 'Subject Schedule',
                    'subject_names' => [$slot->subject?->name ?? 'Subject Schedule'],
                    'period' => $periodLabels[strtolower((string) $slot->period)] ?? ucfirst((string) $slot->period),
                    'start' => $startAt->format('h:i A'),
                    'end' => $endAt->format('h:i A'),
                    'start_24' => $startAt->format('H:i:s'),
                    'end_24' => $endAt->format('H:i:s'),
                    'start_sort' => $startSort,
                    'end_sort' => $endSort,
                ];
            });

        $subjectSlotsByKey = $todaySubjectSlots->groupBy('slot_key');

        $todayTimeline = $todayClassSlots
            ->map(function (array $classSlot) use ($subjectSlotsByKey) {
                $subjectNames = collect($subjectSlotsByKey->get($classSlot['slot_key'], collect()))
                    ->pluck('subject_name')
                    ->filter()
                    ->unique()
                    ->values();

                return [
                    'slot_key' => $classSlot['slot_key'],
                    'slot_type' => 'combined',
                    'class_name' => $classSlot['class_name'],
                    'subject_name' => $subjectNames->first() ?? 'No subject assigned',
                    'subject_names' => $subjectNames->all(),
                    'subject_label' => $subjectNames->isNotEmpty()
                        ? $subjectNames->join(', ')
                        : 'No subject assigned',
                    'day_key' => $classSlot['day_key'],
                    'day_label' => $classSlot['day_label'],
                    'period' => $classSlot['period'],
                    'start' => $classSlot['start'],
                    'end' => $classSlot['end'],
                    'start_24' => $classSlot['start_24'],
                    'end_24' => $classSlot['end_24'],
                    'start_sort' => $classSlot['start_sort'],
                    'end_sort' => $classSlot['end_sort'],
                ];
            })
            ->sortBy('start_sort')
            ->values()
            ->all();

        $todayScheduledMinutes = collect($todayTimeline)->sum(function (array $slot): int {
            try {
                return Carbon::parse($slot['start_24'] ?? null)->diffInMinutes(Carbon::parse($slot['end_24'] ?? null));
            } catch (\Throwable $exception) {
                return 0;
            }
        });

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

        $taughtWeeklySummary = $weeklySummary->filter(fn($day) => (int) ($day['total'] ?? 0) > 0)->values();
        $weeklyChartSummary = $taughtWeeklySummary->isNotEmpty() ? $taughtWeeklySummary : $weeklySummary;

        $todayTypeCounts = collect($todayTimeline)
            ->groupBy('type')
            ->map(fn($items) => count($items))
            ->all();

        $todayPeriodCounts = collect($todayTimeline)
            ->groupBy('period')
            ->map(fn($items) => count($items))
            ->all();

        $todayPeriodSummary = collect($periodLabels)->map(function (string $label, string $periodKey) use ($todayPeriodCounts) {
            return [
                'label' => $label,
                'value' => (int) ($todayPeriodCounts[$periodKey] ?? 0),
            ];
        })->values();

        $featuredStudents = $teacherStudents
            ->map(function (User $student) use ($attendanceByStudent) {
                $attendance = $attendanceByStudent->get((int) $student->id);
                $total = (int) ($attendance->total_count ?? 0);
                $attended = (int) ($attendance->attended_count ?? 0);
                $percent = $total > 0 ? (int) round(($attended / $total) * 100) : null;

                return [
                    'id' => (int) $student->id,
                    'name' => $student->name,
                    'avatar_url' => $student->avatar_url,
                    'class_label' => $student->schoolClass?->display_name ?? 'Student',
                    'attendance_percent' => $percent,
                    'attendance_total' => $total,
                ];
            })
            ->sortByDesc(fn(array $student) => $student['attendance_percent'] ?? -1)
            ->take(7)
            ->values()
            ->all();

        $featuredClasses = $teacherClasses
            ->take(3)
            ->map(function (SchoolClass $schoolClass, int $index) use ($todayTimeline) {
                $classSlotsToday = collect($todayTimeline)
                    ->where('class_name', $schoolClass->display_name)
                    ->count();

                return [
                    'badge' => ['A1', 'B1', 'C1'][$index] ?? 'A' . ($index + 1),
                    'title' => $schoolClass->display_name,
                    'meta' => $schoolClass->taught_subjects_count . ' subject' . ($schoolClass->taught_subjects_count === 1 ? '' : 's'),
                    'action' => $classSlotsToday > 0 ? 'Today has ' . $classSlotsToday . ' lesson' . ($classSlotsToday === 1 ? '' : 's') : 'No lesson today',
                    'members' => (int) $schoolClass->students_count,
                    'capacity_label' => $schoolClass->capacity ? ((int) $schoolClass->capacity . ' seats') : 'Open class',
                ];
            })
            ->values()
            ->all();

        $workloadPercent = (int) max(0, min(100, round(($todayScheduledMinutes / 480) * 100)));
        $coveragePercent = $todayClassSlots->count() > 0
            ? (int) max(0, min(100, round(($todaySubjectSlots->count() / max($todayClassSlots->count(), 1)) * 100)))
            : 0;

        $chartData = [
            'weekly' => [
                'labels' => $weeklyChartSummary->pluck('label')->values()->all(),
                'classes' => $weeklyChartSummary->pluck('class_count')->map(fn($value) => (int) $value)->values()->all(),
                'subjects' => $weeklyChartSummary->pluck('subject_count')->map(fn($value) => (int) $value)->values()->all(),
            ],
            'todayMix' => [
                'labels' => ['Class Time', 'Subject'],
                'values' => [
                    (int) $todayClassSlots->count(),
                    (int) $todaySubjectSlots->count(),
                ],
            ],
            'todayPeriods' => [
                'labels' => $todayPeriodSummary->pluck('label')->values()->all(),
                'values' => $todayPeriodSummary->pluck('value')->map(fn($value) => (int) $value)->values()->all(),
            ],
        ];

        return view('teacher.dashboard', [
            'todayKey' => $todayKey,
            'dayLabels' => $dayLabels,
            'periodLabels' => $periodLabels,
            'stats' => $stats,
            'todayTimeline' => $todayTimeline,
            'weeklySummary' => $weeklyChartSummary,
            'maxWeeklyTotal' => max(1, (int) $weeklyChartSummary->max('total')),
            'chartData' => $chartData,
            'featuredStudents' => $featuredStudents,
            'featuredClasses' => $featuredClasses,
            'workloadPercent' => $workloadPercent,
            'coveragePercent' => $coveragePercent,
        ]);
    }
}
