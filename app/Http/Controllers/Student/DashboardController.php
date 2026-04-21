<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();

        $student->loadMissing([
            'schoolClass:id,name,section,room,study_time,study_start_time,study_end_time',
            'majorSubject:id,name,code',
        ]);

        $classId = (int) ($student->school_class_id ?? 0);
        $todayKey = strtolower(now()->englishDayOfWeek);
        $dayLabels = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        $majorSubjects = collect();
        if (Schema::hasTable('student_major_subjects')) {
            $majorSubjects = $student->majorSubjects()
                ->select('subjects.id', 'subjects.name', 'subjects.code')
                ->orderBy('subjects.name')
                ->get();
        } elseif ($student->majorSubject) {
            $majorSubjects = collect([$student->majorSubject]);
        }

        $subjectsTotal = 0;
        $classSubjects = collect();
        if ($classId > 0) {
            $hasSubjectStatusColumn = Schema::hasColumn('subjects', 'is_active');
            $classSubjectsQuery = Subject::query()
                ->with('teacher:id,name')
                ->where('school_class_id', $classId);

            if ($hasSubjectStatusColumn) {
                $classSubjectsQuery->where('is_active', true);
            }

            $subjectsTotal = (clone $classSubjectsQuery)->count();
            $classSubjects = (clone $classSubjectsQuery)
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name', 'code', 'teacher_id']);
        }

        $hasClassStudyTimeTable = Schema::hasTable('class_study_times');
        $hasSubjectStudyTimeTable = Schema::hasTable('subject_study_times');
        $hasClassDayColumn = $hasClassStudyTimeTable && Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = $hasSubjectStudyTimeTable && Schema::hasColumn('subject_study_times', 'day_of_week');

        $classSlots = collect();
        if ($classId > 0 && $hasClassStudyTimeTable) {
            $classSlotsQuery = ClassStudyTime::query()
                ->where('school_class_id', $classId);

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
        }

        $subjectSlots = collect();
        if ($classId > 0 && $hasSubjectStudyTimeTable && Schema::hasColumn('subject_study_times', 'school_class_id')) {
            $subjectSlotsQuery = SubjectStudyTime::query()
                ->with(['subject:id,name,teacher_id', 'subject.teacher:id,name'])
                ->where('school_class_id', $classId);

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
        }

        $todayClassTimeline = $classSlots
            ->filter(fn($slot) => $this->dayMatches($hasClassDayColumn ? (string) ($slot->day_of_week ?? '') : null, $todayKey))
            ->map(function ($slot) use ($student): array {
                return [
                    'type' => 'class',
                    'title' => $student->schoolClass?->display_name ?? 'Class Session',
                    'subtitle' => $this->periodLabel((string) ($slot->period ?? '')),
                    'start' => $this->formatTime((string) ($slot->start_time ?? '')),
                    'end' => $this->formatTime((string) ($slot->end_time ?? '')),
                    'sort_key' => (string) ($slot->start_time ?? '99:99:99'),
                ];
            });

        $todaySubjectTimeline = $subjectSlots
            ->filter(fn($slot) => $this->dayMatches($hasSubjectDayColumn ? (string) ($slot->day_of_week ?? '') : null, $todayKey))
            ->map(function ($slot): array {
                $teacherName = trim((string) ($slot->subject?->teacher?->name ?? ''));
                $periodLabel = $this->periodLabel((string) ($slot->period ?? ''));
                $subtitle = $teacherName === ''
                    ? $periodLabel
                    : $periodLabel . ' | ' . $teacherName;

                return [
                    'type' => 'subject',
                    'title' => $slot->subject?->name ?? 'Subject Session',
                    'subtitle' => $subtitle,
                    'start' => $this->formatTime((string) ($slot->start_time ?? '')),
                    'end' => $this->formatTime((string) ($slot->end_time ?? '')),
                    'sort_key' => (string) ($slot->start_time ?? '99:99:99'),
                ];
            });

        $todayTimeline = $todayClassTimeline
            ->concat($todaySubjectTimeline)
            ->sortBy('sort_key')
            ->values()
            ->all();

        $weeklySummary = collect(array_keys($dayLabels))
            ->map(function (string $dayKey) use ($dayLabels, $classSlots, $subjectSlots, $hasClassDayColumn, $hasSubjectDayColumn): array {
                $classCount = $classSlots
                    ->filter(fn($slot) => $this->dayMatches($hasClassDayColumn ? (string) ($slot->day_of_week ?? '') : null, $dayKey))
                    ->count();
                $subjectCount = $subjectSlots
                    ->filter(fn($slot) => $this->dayMatches($hasSubjectDayColumn ? (string) ($slot->day_of_week ?? '') : null, $dayKey))
                    ->count();

                return [
                    'key' => $dayKey,
                    'label' => $dayLabels[$dayKey] ?? ucfirst($dayKey),
                    'class_count' => $classCount,
                    'subject_count' => $subjectCount,
                    'total' => $classCount + $subjectCount,
                ];
            })
            ->values();

        $attendanceByStatus = [
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
        ];
        $attendanceTotal = 0;
        $attendanceThisMonth = 0;
        $latestAttendance = collect();

        if (Schema::hasTable('student_attendances')) {
            $attendanceQuery = StudentAttendance::query()
                ->where('student_id', $student->id);

            $attendanceTotal = (clone $attendanceQuery)->count();
            $attendanceThisMonth = (clone $attendanceQuery)
                ->whereDate('attendance_date', '>=', now()->startOfMonth()->toDateString())
                ->count();

            foreach (array_keys($attendanceByStatus) as $status) {
                $attendanceByStatus[$status] = (clone $attendanceQuery)
                    ->where('status', $status)
                    ->count();
            }

            $latestAttendance = (clone $attendanceQuery)
                ->with('teacher:id,name')
                ->select(['id', 'teacher_id', 'attendance_date', 'status', 'remark', 'checked_at'])
                ->orderByDesc('attendance_date')
                ->orderByDesc('id')
                ->limit(5)
                ->get();
        }

        $attendancePositive = $attendanceByStatus['present'] + $attendanceByStatus['late'] + $attendanceByStatus['excused'];
        $attendanceRate = $attendanceTotal > 0
            ? round(($attendancePositive / $attendanceTotal) * 100, 1)
            : 0.0;

        $studySlotCount = Schema::hasTable('student_study_times')
            ? (int) $student->studyTimes()->count()
            : ((int) ($student->class_study_time_id ? 1 : 0));

        $monthlyAttendanceTrend = collect(range(2, 0))
            ->map(function (int $offset) use ($student): array {
                $monthStart = now()->copy()->startOfMonth()->subMonths($offset);

                return [
                    'label' => $monthStart->format('M'),
                    'value' => 0.0,
                    'total' => 0,
                ];
            })
            ->values();

        if (Schema::hasTable('student_attendances')) {
            $positiveStatuses = ['present', 'late', 'excused'];

            $monthlyAttendanceTrend = collect(range(2, 0))
                ->map(function (int $offset) use ($student, $positiveStatuses): array {
                    $monthStart = now()->copy()->startOfMonth()->subMonths($offset);
                    $monthEnd = $monthStart->copy()->endOfMonth();

                    $monthQuery = StudentAttendance::query()
                        ->where('student_id', $student->id)
                        ->whereBetween('attendance_date', [
                            $monthStart->toDateString(),
                            $monthEnd->toDateString(),
                        ]);

                    $monthTotal = (clone $monthQuery)->count();
                    $monthPositive = (clone $monthQuery)
                        ->whereIn('status', $positiveStatuses)
                        ->count();

                    return [
                        'label' => $monthStart->format('M'),
                        'value' => $monthTotal > 0 ? round(($monthPositive / $monthTotal) * 100, 1) : 0.0,
                        'total' => $monthTotal,
                    ];
                })
                ->values();
        }

        $chartData = [
            'attendance' => [
                'labels' => ['Present', 'Late', 'Excused', 'Absent'],
                'values' => [
                    (int) ($attendanceByStatus['present'] ?? 0),
                    (int) ($attendanceByStatus['late'] ?? 0),
                    (int) ($attendanceByStatus['excused'] ?? 0),
                    (int) ($attendanceByStatus['absent'] ?? 0),
                ],
            ],
            'weekly' => [
                'labels' => $weeklySummary->pluck('label')->values()->all(),
                'classes' => $weeklySummary->pluck('class_count')->map(fn($value) => (int) $value)->values()->all(),
                'subjects' => $weeklySummary->pluck('subject_count')->map(fn($value) => (int) $value)->values()->all(),
            ],
            'subjects' => [
                'labels' => ['Class Subjects', 'Major Subjects'],
                'values' => [
                    (int) $subjectsTotal,
                    (int) $majorSubjects->count(),
                ],
            ],
        ];

        return view('student.dashboard', [
            'student' => $student,
            'todayKey' => $todayKey,
            'dayLabels' => $dayLabels,
            'majorSubjects' => $majorSubjects,
            'majorSubjectsCount' => $majorSubjects->count(),
            'subjectsTotal' => $subjectsTotal,
            'classSubjects' => $classSubjects,
            'todayTimeline' => $todayTimeline,
            'todayTimelineCount' => count($todayTimeline),
            'weeklySummary' => $weeklySummary,
            'maxWeeklyTotal' => max(1, (int) $weeklySummary->max('total')),
            'attendanceByStatus' => $attendanceByStatus,
            'attendanceTotal' => $attendanceTotal,
            'attendanceThisMonth' => $attendanceThisMonth,
            'attendanceRate' => $attendanceRate,
            'latestAttendance' => $latestAttendance,
            'studySlotCount' => $studySlotCount,
            'monthlyAttendanceTrend' => $monthlyAttendanceTrend,
            'chartData' => $chartData,
        ]);
    }

    private function dayMatches(?string $slotDay, string $targetDay): bool
    {
        $normalized = strtolower(trim((string) ($slotDay ?? 'all')));
        if ($normalized === '' || $normalized === 'all') {
            return true;
        }

        return $normalized === $targetDay;
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
        } catch (\Throwable) {
            return strtoupper(substr($value, 0, 5));
        }
    }
}
