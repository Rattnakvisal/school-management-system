<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\ContactMessage;
use App\Models\SchoolClass;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\TeacherAttendance;
use App\Models\TeacherLawRequest;
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
        $staffBaseQuery = User::query()->whereIn('role', ['admin', 'staff']);
        $classesBaseQuery = SchoolClass::query();
        $subjectsBaseQuery = Subject::query();

        $studentsTotal = (clone $studentsBaseQuery)->count();
        $teachersTotal = (clone $teachersBaseQuery)->count();
        $staffTotal = (clone $staffBaseQuery)->count();
        $classesTotal = (clone $classesBaseQuery)->count();
        $subjectsTotal = (clone $subjectsBaseQuery)->count();

        $studentsActive = $hasUserStatusColumn ? (clone $studentsBaseQuery)->where('is_active', true)->count() : $studentsTotal;
        $teachersActive = $hasUserStatusColumn ? (clone $teachersBaseQuery)->where('is_active', true)->count() : $teachersTotal;
        $classesActive = $hasClassStatusColumn ? (clone $classesBaseQuery)->where('is_active', true)->count() : $classesTotal;
        $subjectsActive = $hasSubjectStatusColumn ? (clone $subjectsBaseQuery)->where('is_active', true)->count() : $subjectsTotal;

        $messagesTotal = ContactMessage::query()->count();
        $messagesUnread = $hasContactReadColumn
            ? ContactMessage::query()->where('is_read', false)->count()
            : 0;

        $studentsWithStudyTime = Schema::hasTable('student_study_times')
            ? (int) DB::table('student_study_times')
                ->join('users', 'users.id', '=', 'student_study_times.user_id')
                ->where('users.role', 'student')
                ->distinct('student_study_times.user_id')
                ->count('student_study_times.user_id')
            : (Schema::hasColumn('users', 'class_study_time_id')
                ? (clone $studentsBaseQuery)->whereNotNull('class_study_time_id')->count()
                : 0);
        $studentsNeedStudyTime = max(0, $studentsTotal - $studentsWithStudyTime);

        $pendingTeacherRequests = Schema::hasTable('teacher_law_requests')
            ? TeacherLawRequest::query()
                ->where('status', 'pending')
                ->whereDate('requested_for', Carbon::now()->toDateString())
                ->count()
            : 0;

        $today = Carbon::now();
        $todayStudentAttendance = Schema::hasTable('student_attendances')
            ? StudentAttendance::query()->whereDate('attendance_date', $today->toDateString())->count()
            : 0;
        $todayTeacherAttendance = Schema::hasTable('teacher_attendances')
            ? TeacherAttendance::query()->whereDate('attendance_date', $today->toDateString())->count()
            : 0;
        $todayMarkedTeachers = Schema::hasTable('teacher_attendances')
            ? TeacherAttendance::query()
                ->whereDate('attendance_date', $today->toDateString())
                ->distinct('teacher_id')
                ->count('teacher_id')
            : 0;
        $teachersNotMarkedToday = max(0, $teachersTotal - $todayMarkedTeachers);

        $latestContactMessages = ContactMessage::query()
            ->when($hasContactReadColumn, fn($query) => $query->orderBy('is_read'))
            ->latest()
            ->take(4)
            ->get();

        $recentAccounts = User::query()
            ->whereIn('role', ['admin', 'staff', 'teacher'])
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'role', 'avatar', 'created_at']);

        $onlineAccountIds = collect();
        if (
            Schema::hasTable('sessions')
            && Schema::hasColumn('sessions', 'user_id')
            && Schema::hasColumn('sessions', 'last_activity')
        ) {
            $onlineCutoff = now()->subMinutes((int) config('session.lifetime', 120))->timestamp;
            $onlineAccountIds = DB::table('sessions')
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', $onlineCutoff)
                ->pluck('user_id')
                ->map(fn($userId) => (int) $userId)
                ->unique()
                ->values();
        }

        $recentAccounts = $recentAccounts->map(function (User $account) use ($onlineAccountIds) {
            $account->setAttribute('is_online', $onlineAccountIds->contains((int) $account->id));

            return $account;
        });

        $healthParts = array_filter([
            $studentsTotal > 0 ? ($studentsActive / max($studentsTotal, 1)) * 100 : null,
            $teachersTotal > 0 ? ($teachersActive / max($teachersTotal, 1)) * 100 : null,
            $classesTotal > 0 ? ($classesActive / max($classesTotal, 1)) * 100 : null,
            $subjectsTotal > 0 ? ($subjectsActive / max($subjectsTotal, 1)) * 100 : null,
        ], static fn($value) => $value !== null);
        $workloadPercent = $healthParts !== []
            ? (int) max(0, min(100, round(array_sum($healthParts) / count($healthParts))))
            : 0;

        $dayLabels = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
        $todayKey = strtolower($today->englishDayOfWeek);
        $todaySchedule = $this->todaySchedule($todayKey, $dayLabels);

        return view('staff.dashboard', [
            'todayKey' => $todayKey,
            'dayLabels' => $dayLabels,
            'stats' => [
                'students' => $studentsTotal,
                'studentsActive' => $studentsActive,
                'teachers' => $teachersTotal,
                'teachersActive' => $teachersActive,
                'staff' => $staffTotal,
                'classes' => $classesTotal,
                'classesActive' => $classesActive,
                'subjects' => $subjectsTotal,
                'subjectsActive' => $subjectsActive,
                'messages' => $messagesTotal,
                'messagesUnread' => $messagesUnread,
                'studentsNeedStudyTime' => $studentsNeedStudyTime,
                'pendingTeacherRequests' => $pendingTeacherRequests,
                'teachersNotMarkedToday' => $teachersNotMarkedToday,
                'todayStudentAttendance' => $todayStudentAttendance,
                'todayTeacherAttendance' => $todayTeacherAttendance,
            ],
            'latestContactMessages' => $latestContactMessages,
            'recentAccounts' => $recentAccounts,
            'todaySchedule' => $todaySchedule,
            'workloadPercent' => $workloadPercent,
        ]);
    }

    private function todaySchedule(string $todayKey, array $dayLabels): array
    {
        if (!Schema::hasTable('class_study_times')) {
            return [];
        }

        $hasDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $columns = ['id', 'school_class_id', 'period', 'start_time', 'end_time', 'sort_order'];
        if ($hasDayColumn) {
            $columns[] = 'day_of_week';
        }

        return ClassStudyTime::query()
            ->with('schoolClass:id,name,section')
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get($columns)
            ->filter(function (ClassStudyTime $slot) use ($hasDayColumn, $todayKey) {
                $day = $hasDayColumn ? strtolower((string) ($slot->day_of_week ?? 'all')) : 'all';

                return $day === 'all' || $day === $todayKey;
            })
            ->take(5)
            ->map(function (ClassStudyTime $slot) use ($hasDayColumn, $todayKey, $dayLabels) {
                $startAt = Carbon::parse($slot->start_time);
                $endAt = Carbon::parse($slot->end_time);
                $dayKey = $hasDayColumn ? strtolower((string) ($slot->day_of_week ?? $todayKey)) : $todayKey;

                return [
                    'class_name' => $slot->schoolClass?->display_name ?? 'Unassigned Class',
                    'period' => ucfirst((string) ($slot->period ?? 'Custom')),
                    'day_label' => $dayLabels[$dayKey] ?? 'Today',
                    'start' => $startAt->format('h:i A'),
                    'end' => $endAt->format('h:i A'),
                    'start_24' => $startAt->format('H:i:s'),
                    'end_24' => $endAt->format('H:i:s'),
                ];
            })
            ->values()
            ->all();
    }
}
