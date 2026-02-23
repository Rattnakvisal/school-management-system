<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            ->with('subject.schoolClass:id,name,section')
            ->whereHas('subject', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
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
                return [
                    'type' => 'class',
                    'title' => $slot->schoolClass?->display_name ?? 'Class Schedule',
                    'subtitle' => 'Class Time',
                    'period' => $periodLabels[strtolower((string) $slot->period)] ?? ucfirst((string) $slot->period),
                    'start' => Carbon::parse($slot->start_time)->format('h:i A'),
                    'end' => Carbon::parse($slot->end_time)->format('h:i A'),
                    'start_sort' => (string) $slot->start_time,
                ];
            });

        $todaySubjectSlots = $subjectSlots
            ->filter(fn($slot) => $dayMatches($slot->day_of_week ?? 'all', $todayKey))
            ->map(function ($slot) use ($periodLabels) {
                return [
                    'type' => 'subject',
                    'title' => $slot->subject?->name ?? 'Subject Schedule',
                    'subtitle' => $slot->subject?->schoolClass?->display_name ?? 'Unassigned class',
                    'period' => $periodLabels[strtolower((string) $slot->period)] ?? ucfirst((string) $slot->period),
                    'start' => Carbon::parse($slot->start_time)->format('h:i A'),
                    'end' => Carbon::parse($slot->end_time)->format('h:i A'),
                    'start_sort' => (string) $slot->start_time,
                ];
            });

        $todayTimeline = $todayClassSlots
            ->concat($todaySubjectSlots)
            ->sortBy('start_sort')
            ->values()
            ->all();

        $stats = [
            'classes' => $teacherClasses->count(),
            'students' => (int) $teacherClasses->sum('students_count'),
            'subjects' => (int) Subject::query()->where('teacher_id', $teacherId)->count(),
            'todaySchedules' => count($todayTimeline),
        ];

        return view('teacher.dashboard', [
            'todayKey' => $todayKey,
            'dayLabels' => $dayLabels,
            'periodLabels' => $periodLabels,
            'stats' => $stats,
            'todayTimeline' => $todayTimeline,
            'weeklySummary' => $weeklySummary,
            'maxWeeklyTotal' => max(1, (int) $weeklySummary->max('total')),
        ]);
    }
}
