<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\ContactMessage;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $hasUserStatusColumn = Schema::hasColumn('users', 'is_active');
        $hasClassStatusColumn = Schema::hasColumn('school_classes', 'is_active');
        $hasSubjectStatusColumn = Schema::hasColumn('subjects', 'is_active');
        $hasContactReadColumn = Schema::hasColumn('contact_messages', 'is_read');

        $studentsBaseQuery = User::query()->where('role', 'student');
        $teachersBaseQuery = User::query()->where('role', 'teacher');
        $classesBaseQuery = SchoolClass::query();
        $subjectsBaseQuery = Subject::query();

        $studentsTotal = (clone $studentsBaseQuery)->count();
        $teachersTotal = (clone $teachersBaseQuery)->count();
        $classesTotal = (clone $classesBaseQuery)->count();
        $subjectsTotal = (clone $subjectsBaseQuery)->count();
        $studentsActive = $hasUserStatusColumn ? (clone $studentsBaseQuery)->where('is_active', true)->count() : $studentsTotal;
        $teachersActive = $hasUserStatusColumn ? (clone $teachersBaseQuery)->where('is_active', true)->count() : $teachersTotal;
        $classesActive = $hasClassStatusColumn ? (clone $classesBaseQuery)->where('is_active', true)->count() : $classesTotal;
        $subjectsActive = $hasSubjectStatusColumn ? (clone $subjectsBaseQuery)->where('is_active', true)->count() : $subjectsTotal;
        $subjectsWithTeacher = (clone $subjectsBaseQuery)->whereNotNull('teacher_id')->count();
        $studentsWithMajor = Schema::hasColumn('users', 'major_subject_id')
            ? (clone $studentsBaseQuery)->whereNotNull('major_subject_id')->count()
            : 0;
        $studentsWithStudyTime = Schema::hasColumn('users', 'class_study_time_id')
            ? (clone $studentsBaseQuery)->whereNotNull('class_study_time_id')->count()
            : 0;

        $messagesTotal = ContactMessage::query()->count();
        $messagesUnread = $hasContactReadColumn
            ? ContactMessage::query()->where('is_read', false)->count()
            : 0;

        $latestContactMessages = ContactMessage::query()
            ->when($hasContactReadColumn, function ($query) {
                $query->orderBy('is_read');
            })
            ->latest()
            ->take(4)
            ->get();

        $monthlyLabels = [];
        $monthKeys = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->startOfMonth();
            $monthKeys[] = $month->format('Y-m');
            $monthlyLabels[] = $month->format('M Y');
        }

        $studentsMonthlyRaw = (clone $studentsBaseQuery)
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->groupBy('month_key')
            ->pluck('total', 'month_key')
            ->all();

        $teachersMonthlyRaw = (clone $teachersBaseQuery)
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->groupBy('month_key')
            ->pluck('total', 'month_key')
            ->all();

        $studentsMonthly = array_map(static fn(string $key): int => (int) ($studentsMonthlyRaw[$key] ?? 0), $monthKeys);
        $teachersMonthly = array_map(static fn(string $key): int => (int) ($teachersMonthlyRaw[$key] ?? 0), $monthKeys);

        $topClasses = SchoolClass::query()
            ->withCount('students')
            ->orderByDesc('students_count')
            ->orderBy('name')
            ->limit(6)
            ->get();

        $classLoadLabels = $topClasses->map(fn(SchoolClass $schoolClass) => $schoolClass->display_name)->values()->all();
        $classLoadValues = $topClasses->map(fn(SchoolClass $schoolClass) => (int) $schoolClass->students_count)->values()->all();

        $periodCounts = [
            'morning' => 0,
            'afternoon' => 0,
            'evening' => 0,
            'night' => 0,
            'custom' => 0,
        ];

        if (Schema::hasTable('class_study_times')) {
            $periodRaw = ClassStudyTime::query()
                ->selectRaw('LOWER(period) as period_key, COUNT(*) as total')
                ->groupBy('period_key')
                ->pluck('total', 'period_key')
                ->all();

            foreach ($periodCounts as $key => $value) {
                $periodCounts[$key] = (int) ($periodRaw[$key] ?? 0);
            }
        }

        $chartData = [
            'trend' => [
                'labels' => $monthlyLabels,
                'students' => $studentsMonthly,
                'teachers' => $teachersMonthly,
            ],
            'studentProfile' => [
                'labels' => ['Active Students', 'Inactive Students'],
                'values' => [
                    $studentsActive,
                    max(0, $studentsTotal - $studentsActive),
                ],
            ],
            'composition' => [
                'labels' => ['Students', 'Teachers', 'Classes', 'Subjects'],
                'values' => [$studentsTotal, $teachersTotal, $classesTotal, $subjectsTotal],
            ],
            'classLoad' => [
                'labels' => $classLoadLabels,
                'values' => $classLoadValues,
            ],
            'subjectHealth' => [
                'labels' => ['Active', 'Inactive', 'With Teacher', 'No Teacher'],
                'values' => [
                    $subjectsActive,
                    max(0, $subjectsTotal - $subjectsActive),
                    $subjectsWithTeacher,
                    max(0, $subjectsTotal - $subjectsWithTeacher),
                ],
            ],
            'periods' => [
                'labels' => ['Morning', 'Afternoon', 'Evening', 'Night', 'Custom'],
                'values' => [
                    $periodCounts['morning'],
                    $periodCounts['afternoon'],
                    $periodCounts['evening'],
                    $periodCounts['night'],
                    $periodCounts['custom'],
                ],
            ],
            'attendance' => $this->attendanceOverviewData(),
        ];

        return view('admin.dashboard', [
            'studentsTotal' => $studentsTotal,
            'teachersTotal' => $teachersTotal,
            'classesTotal' => $classesTotal,
            'subjectsTotal' => $subjectsTotal,
            'studentsActive' => $studentsActive,
            'teachersActive' => $teachersActive,
            'classesActive' => $classesActive,
            'subjectsActive' => $subjectsActive,
            'studentsWithMajor' => $studentsWithMajor,
            'studentsWithStudyTime' => $studentsWithStudyTime,
            'messagesTotal' => $messagesTotal,
            'messagesUnread' => $messagesUnread,
            'chartData' => $chartData,
            'latestContactMessages' => $latestContactMessages,
        ]);
    }

    private function attendanceOverviewData(): array
    {
        $days = [];
        for ($i = 4; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $dayKey = $date->toDateString();
            $days[$dayKey] = [
                'label' => $date->format('D'),
                'present' => 0,
                'absent' => 0,
            ];
        }

        $startDate = array_key_first($days);
        if ($startDate === null) {
            return [
                'labels' => [],
                'present' => [],
                'absent' => [],
                'hasData' => false,
            ];
        }

        $tableCandidates = [
            'student_attendances',
            'attendances',
            'attendance_records',
        ];

        foreach ($tableCandidates as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            $columns = array_map('strtolower', Schema::getColumnListing($tableName));
            $dateColumn = collect(['attendance_date', 'date', 'recorded_on', 'created_at'])->first(
                fn(string $column) => in_array($column, $columns, true)
            );
            $statusColumn = collect(['status', 'attendance_status'])->first(
                fn(string $column) => in_array($column, $columns, true)
            );

            if (!$dateColumn || !$statusColumn) {
                continue;
            }

            $rows = DB::table($tableName)
                ->selectRaw("DATE($dateColumn) as day_key, LOWER($statusColumn) as status_key, COUNT(*) as total")
                ->whereDate($dateColumn, '>=', $startDate)
                ->groupBy('day_key', 'status_key')
                ->get();

            foreach ($rows as $row) {
                $dayKey = (string) ($row->day_key ?? '');
                if (!array_key_exists($dayKey, $days)) {
                    continue;
                }

                $status = strtolower((string) ($row->status_key ?? ''));
                $total = (int) ($row->total ?? 0);

                if (in_array($status, ['absent', 'a', 'missed', 'leave', 'excused_absent'], true)) {
                    $days[$dayKey]['absent'] += $total;
                    continue;
                }

                $days[$dayKey]['present'] += $total;
            }

            return [
                'labels' => array_values(array_map(static fn(array $day) => $day['label'], $days)),
                'present' => array_values(array_map(static fn(array $day) => $day['present'], $days)),
                'absent' => array_values(array_map(static fn(array $day) => $day['absent'], $days)),
                'hasData' => $rows->isNotEmpty(),
            ];
        }

        return [
            'labels' => array_values(array_map(static fn(array $day) => $day['label'], $days)),
            'present' => [0, 0, 0, 0, 0],
            'absent' => [0, 0, 0, 0, 0],
            'hasData' => false,
        ];
    }
}
