<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\StudentAttendance;
use App\Models\StudentLawRequest;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = (int) ($request->user()?->id ?? 0);
        $search = trim((string) $request->query('q', ''));
        $statusFilter = strtolower(trim((string) $request->query('status', 'all')));
        $allowedStatuses = $this->allowedStatuses();
        $attendanceStatuses = array_keys($allowedStatuses);
        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasSubjectTeacherColumn = Schema::hasColumn('subject_study_times', 'teacher_id');
        $hasAttendancePeriodColumn = Schema::hasColumn('student_attendances', 'attendance_period');
        $useSlotAssignments = $hasSubjectClassColumn && $hasSubjectTeacherColumn;
        $allowedPeriods = ['morning', 'afternoon', 'evening', 'night', 'custom'];

        if ($useSlotAssignments) {
            $teacherClassIds = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->whereNotNull('school_class_id')
                ->distinct()
                ->pluck('school_class_id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->values();

            $classes = SchoolClass::query()
                ->whereIn('id', $teacherClassIds->all())
                ->with([
                    'studySchedules' => function ($query) use ($hasClassDayColumn) {
                        $select = ['id', 'school_class_id', 'period', 'start_time', 'end_time', 'sort_order'];
                        if ($hasClassDayColumn) {
                            $select[] = 'day_of_week';
                        }

                        $query->select($select)
                            ->when($hasClassDayColumn, function ($inner) {
                                $inner->orderByRaw("CASE day_of_week
                                WHEN 'monday' THEN 1
                                WHEN 'tuesday' THEN 2
                                WHEN 'wednesday' THEN 3
                                WHEN 'thursday' THEN 4
                                WHEN 'friday' THEN 5
                                WHEN 'saturday' THEN 6
                                WHEN 'sunday' THEN 7
                                ELSE 8 END");
                            })
                            ->orderBy('sort_order')
                            ->orderBy('start_time');
                    },
                ])
                ->withCount([
                    'students' => function ($query) {
                        $query->where('role', 'student');
                    },
                    'studySchedules as class_slots_count',
                ])
                ->orderBy('name')
                ->orderBy('section')
                ->get(['id', 'name', 'section', 'room']);

            $subjectsByClass = collect();
            if ($teacherClassIds->isNotEmpty()) {
                $subjectsByClass = Subject::query()
                    ->select(['subjects.id', 'subjects.name', 'subject_study_times.school_class_id'])
                    ->join('subject_study_times', 'subject_study_times.subject_id', '=', 'subjects.id')
                    ->where('subject_study_times.teacher_id', $teacherId)
                    ->whereIn('subject_study_times.school_class_id', $teacherClassIds->all())
                    ->distinct()
                    ->orderBy('subjects.name')
                    ->get()
                    ->groupBy(fn($row) => (string) $row->school_class_id);
            }

            $classes->each(function (SchoolClass $schoolClass) use ($subjectsByClass): void {
                $assignedSubjects = $subjectsByClass
                    ->get((string) $schoolClass->id, collect())
                    ->values()
                    ->map(function ($row) {
                        return new Subject([
                            'id' => (int) $row->id,
                            'name' => (string) $row->name,
                        ]);
                    });

                $schoolClass->setRelation('subjects', $assignedSubjects);
                $schoolClass->setAttribute('taught_subjects_count', $assignedSubjects->count());
            });

            $teacherStudyTimesByClass = collect();
            if ($teacherClassIds->isNotEmpty()) {
                $slotSelect = ['id', 'subject_id', 'school_class_id', 'period', 'start_time', 'end_time', 'sort_order'];
                if ($hasSubjectDayColumn) {
                    $slotSelect[] = 'day_of_week';
                }

                $teacherStudyTimesByClass = SubjectStudyTime::query()
                    ->with(['subject:id,name', 'schoolClass:id,name,section'])
                    ->select($slotSelect)
                    ->where('teacher_id', $teacherId)
                    ->whereIn('school_class_id', $teacherClassIds->all())
                    ->orderBy('school_class_id')
                    ->when($hasSubjectDayColumn, function ($query) {
                        $query->orderByRaw("CASE day_of_week
                            WHEN 'monday' THEN 1
                            WHEN 'tuesday' THEN 2
                            WHEN 'wednesday' THEN 3
                            WHEN 'thursday' THEN 4
                            WHEN 'friday' THEN 5
                            WHEN 'saturday' THEN 6
                            WHEN 'sunday' THEN 7
                            ELSE 8 END");
                    })
                    ->orderBy('sort_order')
                    ->orderBy('start_time')
                    ->get()
                    ->groupBy(fn($row) => (string) $row->school_class_id);
            }

            $classes->each(function (SchoolClass $schoolClass) use ($teacherStudyTimesByClass): void {
                $teacherStudySlots = $teacherStudyTimesByClass
                    ->get((string) $schoolClass->id, collect())
                    ->values();

                $schoolClass->setRelation('teacherStudySchedules', $teacherStudySlots);
                $schoolClass->setRelation('studySchedules', $teacherStudySlots);
                $schoolClass->setAttribute('class_slots_count', $teacherStudySlots->count());
            });
        } else {
            $classes = SchoolClass::query()
                ->whereHas('subjects', function ($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId);
                })
                ->with([
                    'subjects' => function ($query) use ($teacherId) {
                        $query->where('teacher_id', $teacherId)->orderBy('name');
                    },
                    'studySchedules' => function ($query) use ($hasClassDayColumn) {
                        $select = ['id', 'school_class_id', 'period', 'start_time', 'end_time', 'sort_order'];
                        if ($hasClassDayColumn) {
                            $select[] = 'day_of_week';
                        }

                        $query->select($select)
                            ->when($hasClassDayColumn, function ($inner) {
                                $inner->orderByRaw("CASE day_of_week
                                WHEN 'monday' THEN 1
                                WHEN 'tuesday' THEN 2
                                WHEN 'wednesday' THEN 3
                                WHEN 'thursday' THEN 4
                                WHEN 'friday' THEN 5
                                WHEN 'saturday' THEN 6
                                WHEN 'sunday' THEN 7
                                ELSE 8 END");
                            })
                            ->orderBy('sort_order')
                            ->orderBy('start_time');
                    },
                ])
                ->withCount([
                    'students' => function ($query) {
                        $query->where('role', 'student');
                    },
                    'subjects as taught_subjects_count' => function ($query) use ($teacherId) {
                        $query->where('teacher_id', $teacherId);
                    },
                    'studySchedules as class_slots_count',
                ])
                ->orderBy('name')
                ->orderBy('section')
                ->get(['id', 'name', 'section', 'room']);
        }

        $classIds = $classes->pluck('id')->map(fn($id) => (int) $id)->values();
        $selectedClassIdRaw = trim((string) $request->query('class_id', (string) $request->old('school_class_id', '')));
        $selectedClassId = ctype_digit($selectedClassIdRaw) && $classIds->contains((int) $selectedClassIdRaw)
            ? (int) $selectedClassIdRaw
            : null;

        $selectedDate = trim((string) $request->query('date', (string) $request->old('attendance_date', now()->toDateString())));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
            $selectedDate = now()->toDateString();
        }
        $selectedDayKey = strtolower(Carbon::parse($selectedDate)->format('l'));
        $selectedPeriodRaw = strtolower(trim((string) $request->query('period', (string) $request->old('attendance_period', ''))));
        $selectedPeriod = in_array($selectedPeriodRaw, $allowedPeriods, true) ? $selectedPeriodRaw : '';
        $dayMatchesSelectedDate = function ($dayOfWeek) use ($selectedDayKey): bool {
            $slotDay = strtolower(trim((string) $dayOfWeek));

            return $slotDay === '' || $slotDay === 'all' || $slotDay === $selectedDayKey;
        };

        $classes = $classes
            ->transform(function (SchoolClass $schoolClass) use ($dayMatchesSelectedDate): SchoolClass {
                $slots = collect($schoolClass->getRelation('teacherStudySchedules') ?? $schoolClass->getRelation('studySchedules') ?? []);
                if ($slots->isEmpty()) {
                    $slots = collect($schoolClass->studySchedules ?? []);
                }

                $filteredSlots = $slots
                    ->filter(function ($slot) use ($dayMatchesSelectedDate) {
                        return $dayMatchesSelectedDate($slot->day_of_week ?? 'all');
                    })
                    ->values();

                if ($schoolClass->relationLoaded('teacherStudySchedules')) {
                    $schoolClass->setRelation('teacherStudySchedules', $filteredSlots);
                }

                if ($schoolClass->relationLoaded('studySchedules')) {
                    $schoolClass->setRelation('studySchedules', $filteredSlots);
                }

                $schoolClass->setAttribute('class_slots_count', $filteredSlots->count());

                return $schoolClass;
            })
            ->filter(function (SchoolClass $schoolClass) {
                $slots = collect($schoolClass->getRelation('teacherStudySchedules') ?? $schoolClass->getRelation('studySchedules') ?? []);

                return $slots->isNotEmpty();
            })
            ->values();

        $availableClassIds = $classes->pluck('id')->map(fn($id) => (int) $id)->values();
        if ($selectedClassId !== null && !$availableClassIds->contains($selectedClassId)) {
            $selectedClassId = null;
        }

        $selectedClass = $selectedClassId !== null
            ? $classes->firstWhere('id', $selectedClassId)
            : null;

        $subjectsForSelectedClass = collect();
        if ($selectedClass) {
            $subjectIdsForDay = collect($selectedClass->getRelation('teacherStudySchedules') ?? $selectedClass->getRelation('studySchedules') ?? [])
                ->pluck('subject_id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->unique()
                ->values();

            $subjectsForSelectedClass = collect($selectedClass->getRelation('subjects') ?? [])
                ->filter(function ($subject) use ($subjectIdsForDay) {
                    return $subjectIdsForDay->isEmpty() || $subjectIdsForDay->contains((int) ($subject->id ?? 0));
                })
                ->values();

            if ($subjectsForSelectedClass->isEmpty() && $subjectIdsForDay->isNotEmpty()) {
                $subjectsForSelectedClass = Subject::query()
                    ->whereIn('id', $subjectIdsForDay->all())
                    ->orderBy('name')
                    ->get(['id', 'name', 'code']);
            }

            if ($selectedPeriod !== '') {
                $periodSubjectIds = collect($selectedClass->getRelation('teacherStudySchedules') ?? $selectedClass->getRelation('studySchedules') ?? [])
                    ->filter(fn($slot) => strtolower((string) ($slot->period ?? 'custom')) === $selectedPeriod)
                    ->pluck('subject_id')
                    ->map(fn($id) => (int) $id)
                    ->filter(fn($id) => $id > 0)
                    ->unique()
                    ->values();

                if ($periodSubjectIds->isNotEmpty()) {
                    $subjectsForSelectedClass = $subjectsForSelectedClass
                        ->filter(fn($subject) => $periodSubjectIds->contains((int) ($subject->id ?? 0)))
                        ->values();
                }
            }
        }

        $subjectIdsForSelectedClass = $subjectsForSelectedClass
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->values();

        $selectedSubjectIdRaw = trim((string) $request->query('subject_id', (string) $request->old('subject_id', '')));
        $selectedSubjectId = $subjectIdsForSelectedClass->isNotEmpty()
            ? (
                ctype_digit($selectedSubjectIdRaw) && $subjectIdsForSelectedClass->contains((int) $selectedSubjectIdRaw)
                ? (int) $selectedSubjectIdRaw
                : (int) $subjectIdsForSelectedClass->first()
            )
            : null;
        $selectedSubject = $selectedSubjectId !== null
            ? $subjectsForSelectedClass->firstWhere('id', $selectedSubjectId)
            : null;

        if ($selectedSubjectId !== null && $selectedPeriod === '') {
            $inferredPeriod = $this->resolveAttendancePeriod(
                $request,
                $teacherId,
                (int) ($selectedClassId ?? 0),
                $selectedSubjectId,
                $selectedDate
            );

            if (is_string($inferredPeriod) && $inferredPeriod !== '') {
                $selectedPeriod = $inferredPeriod;
            }
        }

        $hasAttendanceTable = Schema::hasTable('student_attendances');
        $attendanceCheckedByClass = collect();
        $classAttendanceStatus = [];
        $periodAttendanceStatus = [];
        $subjectAttendanceStatus = [];
        $selectedSubjectAttendanceStatus = [
            'state' => 'pending',
            'students_count' => 0,
            'checked_count' => 0,
        ];

        if ($hasAttendanceTable && $classIds->isNotEmpty()) {
            $attendanceCheckedByClass = StudentAttendance::query()
                ->where('teacher_id', $teacherId)
                ->whereDate('attendance_date', $selectedDate)
                ->whereIn('school_class_id', $classIds->all())
                ->select('school_class_id', DB::raw('COUNT(DISTINCT student_id) as checked_count'))
                ->groupBy('school_class_id')
                ->get()
                ->mapWithKeys(function ($row) {
                    return [(int) $row->school_class_id => (int) $row->checked_count];
                });
        }

        foreach ($classes as $classOption) {
            $classKey = (int) ($classOption->id ?? 0);
            $studentsCount = (int) ($classOption->students_count ?? 0);
            $checkedCount = (int) $attendanceCheckedByClass->get($classKey, 0);
            $isSaved = $studentsCount > 0 && $checkedCount >= $studentsCount;

            $classAttendanceStatus[$classKey] = [
                'state' => $studentsCount === 0 ? 'empty' : ($isSaved ? 'saved' : 'pending'),
                'students_count' => $studentsCount,
                'checked_count' => min($checkedCount, $studentsCount),
            ];
        }

        if ($hasAttendanceTable && $classIds->isNotEmpty() && Schema::hasColumn('student_attendances', 'subject_id')) {
            $attendancePeriodSelect = $hasAttendancePeriodColumn
                ? 'COALESCE(attendance_period, "") as attendance_period_key'
                : '"" as attendance_period_key';

            $attendanceCheckedBySubjectPeriod = StudentAttendance::query()
                ->where('teacher_id', $teacherId)
                ->whereDate('attendance_date', $selectedDate)
                ->whereIn('school_class_id', $classIds->all())
                ->select(
                    'school_class_id',
                    'subject_id',
                    DB::raw($attendancePeriodSelect),
                    DB::raw('COUNT(DISTINCT student_id) as checked_count')
                )
                ->groupBy('school_class_id', 'subject_id')
                ->when($hasAttendancePeriodColumn, fn($query) => $query->groupBy('attendance_period'))
                ->get()
                ->groupBy(fn($row) => (int) $row->school_class_id)
                ->map(function ($classRows) {
                    return $classRows
                        ->groupBy(fn($row) => (int) $row->subject_id)
                        ->map(function ($subjectRows) {
                            return $subjectRows->mapWithKeys(function ($row) {
                                return [(string) ($row->attendance_period_key ?? '') => (int) $row->checked_count];
                            });
                        });
                });

            foreach ($classes as $classOption) {
                $classKey = (int) ($classOption->id ?? 0);
                $studentsCount = (int) ($classOption->students_count ?? 0);
                $teacherSchedules = collect($classOption->teacherStudySchedules ?? ($classOption->studySchedules ?? []));
                $periodGroups = $teacherSchedules->groupBy(fn($slot) => strtolower((string) ($slot->period ?? 'custom')));

                foreach ($periodGroups as $periodKey => $periodSlots) {
                    $subjectIds = collect($periodSlots)
                        ->pluck('subject_id')
                        ->map(fn($id) => (int) $id)
                        ->filter(fn($id) => $id > 0)
                        ->unique()
                        ->values();

                    $subjectCounts = $subjectIds->map(function (int $subjectId) use (
                        $attendanceCheckedBySubjectPeriod,
                        $classKey,
                        $periodKey,
                        $hasAttendancePeriodColumn
                    ): int {
                        $countsByPeriod = $attendanceCheckedBySubjectPeriod
                            ->get($classKey, collect())
                            ->get($subjectId, collect());

                        return (int) $countsByPeriod->get($hasAttendancePeriodColumn ? $periodKey : '', 0);
                    });

                    $checkedCount = $subjectCounts->isEmpty() ? 0 : (int) $subjectCounts->min();
                    $isSaved = $studentsCount > 0
                        && $subjectIds->isNotEmpty()
                        && $subjectCounts->every(fn(int $count): bool => $count >= $studentsCount);

                    $periodAttendanceStatus[$classKey][$periodKey] = [
                        'state' => $studentsCount === 0 ? 'empty' : ($isSaved ? 'saved' : 'pending'),
                        'students_count' => $studentsCount,
                        'checked_count' => min($checkedCount, $studentsCount),
                    ];
                }
            }
        }

        $students = collect();
        $attendanceByStudent = collect();
        $lawRequestsByStudent = collect();
        if ($selectedClassId !== null) {
            $students = User::query()
                ->where('role', 'student')
                ->where('school_class_id', $selectedClassId)
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
                })
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'is_active', 'school_class_id']);

            if ($hasAttendanceTable) {
                $attendanceQuery = StudentAttendance::query()
                    ->where('teacher_id', $teacherId)
                    ->where('school_class_id', $selectedClassId)
                    ->whereDate('attendance_date', $selectedDate);

                if ($selectedSubjectId !== null && Schema::hasColumn('student_attendances', 'subject_id')) {
                    $attendanceQuery->where('subject_id', $selectedSubjectId);
                }

                if ($hasAttendancePeriodColumn && $selectedPeriod !== '') {
                    $attendanceQuery->where('attendance_period', $selectedPeriod);
                }

                $attendanceByStudent = $attendanceQuery
                    ->get()
                    ->keyBy('student_id');
            }

            if (Schema::hasTable('student_law_requests')) {
                $lawRequestQuery = StudentLawRequest::query()
                    ->where('school_class_id', $selectedClassId)
                    ->whereDate('requested_for', $selectedDate)
                    ->whereIn('status', ['pending', 'approved', 'rejected'])
                    ->orderByRaw("CASE status WHEN 'approved' THEN 0 WHEN 'pending' THEN 1 WHEN 'rejected' THEN 2 ELSE 3 END")
                    ->orderByDesc('reviewed_at')
                    ->orderByDesc('created_at');

                if ($selectedSubjectId !== null) {
                    if (Schema::hasColumn('student_law_requests', 'subject_id')) {
                        $lawRequestQuery->where('subject_id', $selectedSubjectId);
                    } elseif ($selectedSubject) {
                        $lawRequestQuery->where('subject', (string) ($selectedSubject->name ?? ''));
                    }
                }

                if (Schema::hasColumn('student_law_requests', 'teacher_id')) {
                    $lawRequestQuery->where(function ($query) use ($teacherId) {
                        $query->where('teacher_id', $teacherId)
                            ->orWhereNull('teacher_id');
                    });
                }

                $lawRequestsByStudent = $lawRequestQuery
                    ->get()
                    ->groupBy(function (StudentLawRequest $lawRequest) {
                        return (int) ($lawRequest->student_id ?? 0);
                    })
                    ->map(function ($rows) {
                        return $rows->first();
                    });
            }

            if ($statusFilter !== 'all' && in_array($statusFilter, $attendanceStatuses, true)) {
                $students = $students->filter(function ($student) use ($attendanceByStudent, $statusFilter) {
                    $record = $attendanceByStudent->get($student->id);
                    $currentStatus = strtolower((string) ($record?->status ?? ''));
                    return $currentStatus === $statusFilter;
                })->values();
            }

            if (
                $hasAttendanceTable
                && $subjectIdsForSelectedClass->isNotEmpty()
                && Schema::hasColumn('student_attendances', 'subject_id')
            ) {
                $attendanceCheckedBySubject = StudentAttendance::query()
                    ->where('teacher_id', $teacherId)
                    ->where('school_class_id', $selectedClassId)
                    ->whereDate('attendance_date', $selectedDate)
                    ->whereIn('subject_id', $subjectIdsForSelectedClass->all())
                    ->when($hasAttendancePeriodColumn && $selectedPeriod !== '', fn($query) => $query->where('attendance_period', $selectedPeriod))
                    ->select('subject_id', DB::raw('COUNT(DISTINCT student_id) as checked_count'))
                    ->groupBy('subject_id')
                    ->get()
                    ->mapWithKeys(function ($row) {
                        return [(int) $row->subject_id => (int) $row->checked_count];
                    });

                foreach ($subjectsForSelectedClass as $subjectOption) {
                    $subjectKey = (int) ($subjectOption->id ?? 0);
                    $studentsCount = $students->count();
                    $checkedCount = (int) $attendanceCheckedBySubject->get($subjectKey, 0);
                    $isSaved = $studentsCount > 0 && $checkedCount >= $studentsCount;

                    $subjectAttendanceStatus[$subjectKey] = [
                        'state' => $studentsCount === 0 ? 'empty' : ($isSaved ? 'saved' : 'pending'),
                        'students_count' => $studentsCount,
                        'checked_count' => min($checkedCount, $studentsCount),
                    ];
                }
            }
        }

        if ($selectedSubjectId !== null) {
            $selectedSubjectAttendanceStatus = $subjectAttendanceStatus[$selectedSubjectId] ?? [
                'state' => $students->isEmpty() ? 'empty' : 'pending',
                'students_count' => $students->count(),
                'checked_count' => $attendanceByStudent->count(),
            ];
        }

        $summary = [
            'students' => $students->count(),
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'not_marked' => 0,
        ];

        foreach ($students as $student) {
            $record = $attendanceByStudent->get($student->id);
            $status = strtolower((string) ($record?->status ?? ''));
            if (array_key_exists($status, $summary)) {
                $summary[$status] += 1;
            } else {
                $summary['not_marked'] += 1;
            }
        }

        $attendanceAlertNotifications = Notification::query()
            ->where('type', 'teacher_attendance_checked')
            ->where('is_read', false)
            ->where('message', 'like', '%[teacher_id:' . $teacherId . ']%')
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'message']);

        $attendanceAlerts = $attendanceAlertNotifications
            ->map(function ($notification) {
                return [
                    'title' => trim((string) ($notification->title ?? 'Attendance Checked')),
                    'text' => $this->cleanTeacherNotificationText((string) ($notification->message ?? '')),
                ];
            })
            ->values()
            ->all();

        if ($attendanceAlertNotifications->isNotEmpty()) {
            Notification::query()
                ->whereIn('id', $attendanceAlertNotifications->pluck('id')->all())
                ->update(['is_read' => true]);
        }

        return view('teacher.attendance', [
            'classes' => $classes,
            'classId' => $selectedClassId !== null ? (string) $selectedClassId : '',
            'subjectId' => $selectedSubjectId !== null ? (string) $selectedSubjectId : '',
            'subjectsForSelectedClass' => $subjectsForSelectedClass,
            'subjectAttendanceStatus' => $subjectAttendanceStatus,
            'selectedSubjectAttendanceStatus' => $selectedSubjectAttendanceStatus,
            'selectedDate' => $selectedDate,
            'search' => $search,
            'statusFilter' => in_array($statusFilter, array_merge(['all'], $attendanceStatuses), true) ? $statusFilter : 'all',
            'students' => $students,
            'attendanceByStudent' => $attendanceByStudent,
            'lawRequestsByStudent' => $lawRequestsByStudent,
            'statusLabels' => $allowedStatuses,
            'summary' => $summary,
            'attendanceAlerts' => $attendanceAlerts,
            'hasAttendanceTable' => $hasAttendanceTable,
            'classAttendanceStatus' => $classAttendanceStatus,
            'periodAttendanceStatus' => $periodAttendanceStatus,
            'hasClassDayColumn' => $hasClassDayColumn,
            'selectedDayKey' => $selectedDayKey,
            'selectedPeriod' => $selectedPeriod,
            'selectedDayLabel' => [
                'all' => 'All Days',
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
            ][$selectedDayKey] ?? ucfirst($selectedDayKey),
            'periodLabels' => [
                'morning' => 'Morning',
                'afternoon' => 'Afternoon',
                'evening' => 'Evening',
                'night' => 'Night',
                'custom' => 'Custom',
            ],
            'dayLabels' => [
                'all' => 'All Days',
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $teacherId = (int) ($request->user()?->id ?? 0);
        $hasAttendanceTable = Schema::hasTable('student_attendances');

        if (!$hasAttendanceTable) {
            return back()->withInput()->with('error', 'Attendance table is missing. Please run migrations.');
        }

        $resolved = $this->resolveAttendanceSubmission($request, $teacherId);
        if (!$resolved['ok']) {
            return back()->withInput()->with($resolved['flash_type'], $resolved['message']);
        }

        $schoolClassId = $resolved['school_class_id'];
        $subjectId = $resolved['subject_id'];
        $selectedSubject = $resolved['selected_subject'];
        $attendanceDate = $resolved['attendance_date'];
        $attendancePeriod = $resolved['attendance_period'] ?? null;
        $attendanceRows = $resolved['attendance_rows'];
        $studentIds = $resolved['student_ids'];

        if (count($studentIds) === 0) {
            return back()->withInput()->with('warning', 'No students found in selected class.');
        }

        $alreadyCheckedCount = StudentAttendance::query()
            ->where('teacher_id', $teacherId)
            ->where('school_class_id', $schoolClassId)
            ->whereDate('attendance_date', $attendanceDate)
            ->whereIn('student_id', $studentIds)
            ->when(
                Schema::hasColumn('student_attendances', 'subject_id') && $subjectId > 0,
                fn($query) => $query->where('subject_id', $subjectId)
            )
            ->when(
                Schema::hasColumn('student_attendances', 'attendance_period') && filled($attendancePeriod),
                fn($query) => $query->where('attendance_period', $attendancePeriod)
            )
            ->distinct('student_id')
            ->count('student_id');

        if ($alreadyCheckedCount >= count($studentIds)) {
            return redirect()
                ->route('teacher.attendance.index', [
                    'class_id' => $schoolClassId,
                    'subject_id' => $subjectId,
                    'date' => $attendanceDate,
                    'period' => $attendancePeriod,
                ])
                ->with('warning', 'Attendance already saved successfully. Check status cannot be changed.');
        }

        [$savedCount, $autoExcusedCount, $savedStudentRows] = $this->persistAttendanceRows(
            $teacherId,
            $schoolClassId,
            $subjectId,
            $selectedSubject,
            $attendanceDate,
            $attendancePeriod,
            $attendanceRows,
            $studentIds
        );

        $this->notifyStudentsAttendanceChecked(
            $savedStudentRows,
            $selectedSubject,
            trim((string) ($request->user()?->name ?? '')),
            $attendanceDate
        );

        return redirect()
            ->route('teacher.attendance.index', [
                'class_id' => $schoolClassId,
                'subject_id' => $subjectId,
                'date' => $attendanceDate,
                'period' => $attendancePeriod,
            ])
            ->with(
                'success',
                'Attendance checked successfully for '
                    . $savedCount
                    . ' student(s).'
                    . ($autoExcusedCount > 0 ? (' Auto-excused ' . $autoExcusedCount . ' student(s) due to approved law request.') : '')
            );
    }

    public function approveLawRequest(Request $request, StudentLawRequest $lawRequest)
    {
        [$teacherId, $schoolClassId, $lawRequestSubjectId] = $this->authorizeTeacherLawRequest($request, $lawRequest);

        if (strtolower((string) ($lawRequest->status ?? 'pending')) === 'approved') {
            return $this->lawRequestActionResponse($request, 'warning', 'This student law request is already approved.');
        }

        $lawRequest->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        $attendanceDate = $lawRequest->requested_for?->toDateString();
        $lawRemark = $this->buildStudentLawRequestRemark($lawRequest);
        $studentName = trim((string) ($lawRequest->student?->name ?? 'Student'));
        $teacherName = trim((string) ($request->user()?->name ?? 'Teacher'));

        $savedCount = null;
        if (Schema::hasTable('student_attendances') && $attendanceDate !== null && $request->has('attendance')) {
            $resolved = $this->resolveAttendanceSubmission($request, $teacherId);
            if (!$resolved['ok']) {
                return $this->lawRequestActionResponse($request, $resolved['flash_type'], $resolved['message']);
            }

            [$savedCount] = $this->persistAttendanceRows(
                $teacherId,
                $resolved['school_class_id'],
                $resolved['subject_id'],
                $resolved['selected_subject'],
                $resolved['attendance_date'],
                $resolved['attendance_period'] ?? null,
                $resolved['attendance_rows'],
                $resolved['student_ids']
            );
        } elseif (Schema::hasTable('student_attendances') && $attendanceDate !== null) {
            StudentAttendance::query()->updateOrCreate(
                $this->attendanceMatchAttributes((int) ($lawRequest->student_id ?? 0), $attendanceDate, $lawRequestSubjectId, null),
                $this->attendancePayload(
                    $teacherId > 0 ? $teacherId : null,
                    $schoolClassId > 0 ? $schoolClassId : null,
                    $lawRequestSubjectId,
                    'excused',
                    $lawRemark !== '' ? mb_substr($lawRemark, 0, 255) : null,
                    now()
                )
            );
        }

        $dateParam = trim((string) $request->input('attendance_date', $attendanceDate ?: now()->toDateString()));
        $this->notifyStudentLawRequestApproved($lawRequest, $teacherName, $dateParam);

        return $this->lawRequestActionResponse(
            $request,
            'success',
            'Approved law request for ' . ($studentName !== '' ? $studentName : 'student') . '.',
            [
                'law_request_status' => 'approved',
                'attendance_status' => 'excused',
                'remark' => $lawRemark !== '' ? mb_substr($lawRemark, 0, 255) : null,
                'law_request_id' => (int) ($lawRequest->id ?? 0),
                'student_id' => (int) ($lawRequest->student_id ?? 0),
                'saved_count' => $savedCount,
            ],
            [
                'class_id' => $schoolClassId,
                'subject_id' => $lawRequestSubjectId,
                'date' => $dateParam,
            ]
        );
    }

    public function rejectLawRequest(Request $request, StudentLawRequest $lawRequest)
    {
        if (!Schema::hasTable('student_law_requests')) {
            return back()->with('error', 'Student law request table is missing.');
        }

        [$teacherId, $schoolClassId, $lawRequestSubjectId] = $this->authorizeTeacherLawRequest($request, $lawRequest);

        if (strtolower((string) ($lawRequest->status ?? 'pending')) === 'rejected') {
            return $this->lawRequestActionResponse($request, 'warning', 'This student law request is already cancelled.');
        }

        $lawRequest->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
        ]);

        $attendanceDate = $lawRequest->requested_for?->toDateString();
        $studentName = trim((string) ($lawRequest->student?->name ?? 'Student'));
        $remark = 'Law Request Cancelled';

        $savedCount = null;
        if (Schema::hasTable('student_attendances') && $attendanceDate !== null && $request->has('attendance')) {
            $resolved = $this->resolveAttendanceSubmission($request, $teacherId);
            if (!$resolved['ok']) {
                return $this->lawRequestActionResponse($request, $resolved['flash_type'], $resolved['message']);
            }

            [$savedCount] = $this->persistAttendanceRows(
                $teacherId,
                $resolved['school_class_id'],
                $resolved['subject_id'],
                $resolved['selected_subject'],
                $resolved['attendance_date'],
                $resolved['attendance_period'] ?? null,
                $resolved['attendance_rows'],
                $resolved['student_ids'],
                [
                    (int) ($lawRequest->student_id ?? 0) => [
                        'status' => 'absent',
                        'remark' => $remark,
                    ],
                ]
            );
        } elseif (Schema::hasTable('student_attendances') && $attendanceDate !== null) {
            StudentAttendance::query()->updateOrCreate(
                $this->attendanceMatchAttributes((int) ($lawRequest->student_id ?? 0), $attendanceDate, $lawRequestSubjectId, null),
                $this->attendancePayload(
                    $teacherId > 0 ? $teacherId : null,
                    $schoolClassId > 0 ? $schoolClassId : null,
                    $lawRequestSubjectId,
                    'absent',
                    $remark,
                    now()
                )
            );
        }

        $dateParam = trim((string) $request->input('attendance_date', $attendanceDate ?: now()->toDateString()));

        return $this->lawRequestActionResponse(
            $request,
            'success',
            'Cancelled law request for ' . ($studentName !== '' ? $studentName : 'student') . '. Attendance is now absent.',
            [
                'law_request_status' => 'rejected',
                'attendance_status' => 'absent',
                'remark' => $remark,
                'law_request_id' => (int) ($lawRequest->id ?? 0),
                'student_id' => (int) ($lawRequest->student_id ?? 0),
                'saved_count' => $savedCount,
            ],
            [
                'class_id' => $schoolClassId,
                'subject_id' => $lawRequestSubjectId,
                'date' => $dateParam,
            ]
        );
    }

    private function teacherClassIds(int $teacherId)
    {
        return SchoolClass::query()
            ->when(
                Schema::hasColumn('subject_study_times', 'school_class_id') && Schema::hasColumn('subject_study_times', 'teacher_id'),
                function ($query) use ($teacherId) {
                    $query->whereIn(
                        'id',
                        SubjectStudyTime::query()
                            ->where('teacher_id', $teacherId)
                            ->whereNotNull('school_class_id')
                            ->distinct()
                            ->pluck('school_class_id')
                            ->all()
                    );
                },
                function ($query) use ($teacherId) {
                    $query->whereHas('subjects', function ($subjectQuery) use ($teacherId) {
                        $subjectQuery->where('teacher_id', $teacherId);
                    });
                }
            )
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->values();
    }

    private function authorizeTeacherLawRequest(Request $request, StudentLawRequest $lawRequest): array
    {
        $teacherId = (int) ($request->user()?->id ?? 0);
        $allowedClassIds = $this->teacherClassIds($teacherId);
        $schoolClassId = (int) ($lawRequest->school_class_id ?? 0);
        $lawRequestSubjectId = $this->resolveLawRequestSubjectId($lawRequest);

        if (!$allowedClassIds->contains($schoolClassId)) {
            abort(404);
        }

        if (Schema::hasColumn('student_law_requests', 'teacher_id')) {
            $assignedTeacherId = (int) ($lawRequest->teacher_id ?? 0);
            if ($assignedTeacherId > 0 && $assignedTeacherId !== $teacherId) {
                abort(404);
            }
        }

        if ($lawRequestSubjectId !== null) {
            $allowedSubjectIds = $this->teacherSubjectOptions($teacherId, $schoolClassId)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->values();

            if ($allowedSubjectIds->isNotEmpty() && !$allowedSubjectIds->contains($lawRequestSubjectId)) {
                abort(404);
            }
        }

        return [$teacherId, $schoolClassId, $lawRequestSubjectId];
    }

    private function lawRequestActionResponse(Request $request, string $flashType, string $message, array $payload = [], array $routeParams = [])
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(array_merge([
                'ok' => $flashType !== 'error',
                'message' => $message,
                'flash_type' => $flashType,
            ], $payload));
        }

        return redirect()
            ->route('teacher.attendance.index', $routeParams)
            ->with($flashType, $message);
    }

    private function notifyStudentLawRequestApproved(StudentLawRequest $lawRequest, string $teacherName, string $dateParam): void
    {
        $studentId = (int) ($lawRequest->student_id ?? 0);
        if ($studentId <= 0) {
            return;
        }

        $subjectText = trim((string) ($lawRequest->subject ?? ''));
        $subjectTimeText = trim((string) ($lawRequest->subject_time ?? ''));
        $displayDate = $lawRequest->requested_for
            ? Carbon::parse($lawRequest->requested_for)->format('M d, Y')
            : Carbon::parse($dateParam)->format('M d, Y');
        $studentTag = '[student_id:' . $studentId . '] ';
        $message = $studentTag . 'Your law request was approved by '
            . ($teacherName !== '' ? $teacherName : 'your teacher')
            . ' for '
            . $displayDate;

        if ($subjectText !== '') {
            $message .= ': ' . $subjectText;
            if ($subjectTimeText !== '') {
                $message .= ' | ' . $subjectTimeText;
            }
        } else {
            $message .= '.';
        }

        Notification::query()->create([
            'type' => 'student_law_request_approved',
            'title' => 'Law request approved',
            'message' => $message,
            'url' => route('student.law-requests.index'),
            'is_read' => false,
        ]);

        $this->sendStudentLawRequestApprovedTelegramAlert($lawRequest, $teacherName, $displayDate);
    }

    private function sendStudentLawRequestApprovedTelegramAlert(StudentLawRequest $lawRequest, string $teacherName, string $displayDate): void
    {
        $lawRequest->loadMissing(['student.schoolClass', 'schoolClass']);

        $student = $lawRequest->student;
        $chatId = trim((string) ($student?->telegram_chat_id ?? ''));
        if ($chatId === '') {
            return;
        }

        $studentName = trim((string) ($student?->name ?? 'Student'));
        $teacherText = trim($teacherName) !== '' ? trim($teacherName) : 'your teacher';
        $lawType = trim((string) ($lawRequest->law_type ?? ''));
        $lawTypeLabel = $lawType !== '' ? ucwords(str_replace('_', ' ', $lawType)) : 'Law Request';
        $subjectText = trim((string) ($lawRequest->subject ?? ''));
        $subjectTimeText = trim((string) ($lawRequest->subject_time ?? ''));
        $classLabel = trim((string) (
            $lawRequest->schoolClass?->display_name
            ?? $student?->schoolClass?->display_name
            ?? ''
        ));

        $lines = [
            'សេចក្តីជូនដំណឹងពី TechBridge Academy',
            '',
            'សូមជម្រាបជូន ' . ($studentName !== '' ? $studentName : 'សិស្ស') . ' ថា សំណើសុំច្បាប់របស់អ្នកត្រូវបានអនុម័ត។',
            'អ្នកអនុម័ត: ' . $teacherText,
            'ប្រភេទសំណើ: ' . $lawTypeLabel,
            'កាលបរិច្ឆេទ: ' . $displayDate,
        ];

        if ($subjectText !== '') {
            $lines[] = 'មុខវិជ្ជា: ' . $subjectText;
        }

        if ($subjectTimeText !== '') {
            $lines[] = 'ម៉ោងសិក្សា: ' . $subjectTimeText;
        }

        if ($classLabel !== '') {
            $lines[] = 'ថ្នាក់: ' . $classLabel;
        }

        $lines[] = 'ស្ថានភាព: Approved';
        $lines[] = '';
        $lines[] = 'សូមអរគុណ🙏';
        $lines[] = 'TechBridge Academy Team';

        app(TelegramBotService::class)->sendMessage($chatId, implode("\n", $lines));
    }

    private function notifyStudentsAttendanceChecked(array $savedStudentRows, $selectedSubject, string $teacherName, string $attendanceDate): void
    {
        if ($savedStudentRows === []) {
            return;
        }

        $statusLabels = $this->allowedStatuses();
        $displayDate = Carbon::parse($attendanceDate)->format('M d, Y');
        $subjectText = trim((string) ($selectedSubject->name ?? ''));
        $teacherText = $teacherName !== '' ? $teacherName : 'your teacher';

        foreach ($savedStudentRows as $savedStudentRow) {
            $studentId = (int) ($savedStudentRow['student_id'] ?? 0);
            if ($studentId <= 0) {
                continue;
            }

            $statusKey = strtolower(trim((string) ($savedStudentRow['status'] ?? '')));
            if ($statusKey === '' || !isset($statusLabels[$statusKey])) {
                continue;
            }

            $message = '[student_id:' . $studentId . '] '
                . 'Your attendance was checked by '
                . $teacherText
                . ' for '
                . $displayDate;

            if ($subjectText !== '') {
                $message .= ' in ' . $subjectText;
            }

            $message .= ': ' . $statusLabels[$statusKey] . '.';

            Notification::query()->create([
                'type' => 'student_attendance_checked',
                'title' => 'Attendance updated',
                'message' => $message,
                'url' => route('student.notices.index'),
                'is_read' => false,
            ]);
        }
    }

    private function resolveAttendanceSubmission(Request $request, int $teacherId): array
    {
        $classIds = SchoolClass::query()
            ->when(
                Schema::hasColumn('subject_study_times', 'school_class_id') && Schema::hasColumn('subject_study_times', 'teacher_id'),
                function ($query) use ($teacherId) {
                    $query->whereIn(
                        'id',
                        SubjectStudyTime::query()
                            ->where('teacher_id', $teacherId)
                            ->whereNotNull('school_class_id')
                            ->distinct()
                            ->pluck('school_class_id')
                            ->all()
                    );
                },
                function ($query) use ($teacherId) {
                    $query->whereHas('subjects', function ($subjectQuery) use ($teacherId) {
                        $subjectQuery->where('teacher_id', $teacherId);
                    });
                }
            )
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        if ($classIds === []) {
            return [
                'ok' => false,
                'flash_type' => 'error',
                'message' => 'No class found for this teacher.',
            ];
        }

        $statuses = array_keys($this->allowedStatuses());
        $validated = $request->validate([
            'school_class_id' => ['required', 'integer', Rule::in($classIds)],
            'attendance_date' => ['required', 'date'],
            'attendance_period' => ['nullable', 'string', Rule::in(['morning', 'afternoon', 'evening', 'night', 'custom'])],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'string', Rule::in($statuses)],
            'attendance.*.remark' => ['nullable', 'string', 'max:255'],
        ]);

        $schoolClassId = (int) $validated['school_class_id'];
        $subjectOptions = $this->teacherSubjectOptions($teacherId, $schoolClassId);
        $subjectIds = $subjectOptions
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->values()
            ->all();

        if ($subjectIds === []) {
            return [
                'ok' => false,
                'flash_type' => 'error',
                'message' => 'No subject found for this teacher in the selected class.',
            ];
        }

        $subjectValidated = $request->validate([
            'subject_id' => ['required', 'integer', Rule::in($subjectIds)],
        ]);

        $subjectId = (int) ($subjectValidated['subject_id'] ?? 0);
        $attendanceDate = (string) $validated['attendance_date'];
        $attendancePeriod = $this->resolveAttendancePeriod(
            $request,
            $teacherId,
            $schoolClassId,
            $subjectId,
            $attendanceDate
        );

        if ($attendancePeriod === false) {
            return [
                'ok' => false,
                'flash_type' => 'error',
                'message' => 'Selected subject is not assigned to this period.',
            ];
        }

        $studentIds = User::query()
            ->where('role', 'student')
            ->where('school_class_id', $schoolClassId)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        return [
            'ok' => true,
            'school_class_id' => $schoolClassId,
            'subject_id' => $subjectId,
            'selected_subject' => $subjectOptions->firstWhere('id', $subjectId),
            'attendance_date' => $attendanceDate,
            'attendance_period' => $attendancePeriod,
            'attendance_rows' => $validated['attendance'] ?? [],
            'student_ids' => $studentIds,
        ];
    }

    private function resolveAttendancePeriod(Request $request, int $teacherId, int $schoolClassId, int $subjectId, string $attendanceDate): string|false|null
    {
        $period = strtolower(trim((string) $request->input('attendance_period', '')));
        $allowedPeriods = ['morning', 'afternoon', 'evening', 'night', 'custom'];
        $selectedDayKey = strtolower(Carbon::parse($attendanceDate)->format('l'));

        if ($period !== '' && !in_array($period, $allowedPeriods, true)) {
            return false;
        }

        if (
            !Schema::hasTable('subject_study_times')
            || !Schema::hasColumn('subject_study_times', 'school_class_id')
            || !Schema::hasColumn('subject_study_times', 'teacher_id')
        ) {
            return $period !== '' ? $period : null;
        }

        $query = SubjectStudyTime::query()
            ->where('teacher_id', $teacherId)
            ->where('school_class_id', $schoolClassId)
            ->where('subject_id', $subjectId);

        if (Schema::hasColumn('subject_study_times', 'day_of_week')) {
            $query->where(function ($inner) use ($selectedDayKey) {
                $inner->whereNull('day_of_week')
                    ->orWhere('day_of_week', '')
                    ->orWhere('day_of_week', 'all')
                    ->orWhere('day_of_week', $selectedDayKey);
            });
        }

        if ($period !== '') {
            return (clone $query)->whereRaw('LOWER(period) = ?', [$period])->exists() ? $period : false;
        }

        $resolvedPeriod = (clone $query)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->value('period');
        $resolvedPeriod = strtolower(trim((string) $resolvedPeriod));

        return in_array($resolvedPeriod, $allowedPeriods, true) ? $resolvedPeriod : null;
    }

    private function approvedLawRequestsByStudent(
        int $schoolClassId,
        string $attendanceDate,
        array $studentIds,
        int $teacherId,
        ?int $subjectId,
        $selectedSubject
    ) {
        if (!Schema::hasTable('student_law_requests') || $studentIds === []) {
            return collect();
        }

        $lawRequestQuery = StudentLawRequest::query()
            ->where('school_class_id', $schoolClassId)
            ->whereDate('requested_for', $attendanceDate)
            ->whereIn('student_id', $studentIds)
            ->where('status', 'approved')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at');

        if (Schema::hasColumn('student_law_requests', 'subject_id') && $subjectId > 0) {
            $lawRequestQuery->where('subject_id', $subjectId);
        } elseif ($selectedSubject) {
            $lawRequestQuery->where('subject', (string) ($selectedSubject->name ?? ''));
        }

        if (Schema::hasColumn('student_law_requests', 'teacher_id')) {
            $lawRequestQuery->where(function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                    ->orWhereNull('teacher_id');
            });
        }

        return $lawRequestQuery
            ->get()
            ->groupBy(function (StudentLawRequest $lawRequest) {
                return (int) ($lawRequest->student_id ?? 0);
            })
            ->map(function ($rows) {
                return $rows->first();
            });
    }

    private function persistAttendanceRows(
        int $teacherId,
        int $schoolClassId,
        ?int $subjectId,
        $selectedSubject,
        string $attendanceDate,
        ?string $attendancePeriod,
        array $attendanceRows,
        array $studentIds,
        array $forcedRows = []
    ): array {
        $now = now();
        $savedCount = 0;
        $autoExcusedCount = 0;
        $savedStudentRows = [];
        $approvedLawRequestsByStudent = $this->approvedLawRequestsByStudent(
            $schoolClassId,
            $attendanceDate,
            $studentIds,
            $teacherId,
            $subjectId,
            $selectedSubject
        );

        DB::transaction(function () use (
            $studentIds,
            $attendanceRows,
            $attendanceDate,
            $attendancePeriod,
            $schoolClassId,
            $subjectId,
            $teacherId,
            $now,
            $approvedLawRequestsByStudent,
            $forcedRows,
            &$savedCount,
            &$autoExcusedCount,
            &$savedStudentRows
        ) {
            foreach ($studentIds as $studentId) {
                $row = $attendanceRows[(string) $studentId] ?? null;
                if (!is_array($row)) {
                    continue;
                }

                $status = strtolower(trim((string) ($row['status'] ?? '')));
                if ($status === '') {
                    continue;
                }

                $remark = trim((string) ($row['remark'] ?? ''));
                $approvedLawRequest = $approvedLawRequestsByStudent->get((int) $studentId);
                if ($approvedLawRequest) {
                    if ($status !== 'excused') {
                        $autoExcusedCount += 1;
                    }

                    $status = 'excused';
                    $lawRemark = $this->buildStudentLawRequestRemark($approvedLawRequest);
                    if ($lawRemark !== '') {
                        if ($remark !== '' && stripos($remark, $lawRemark) === false) {
                            $remark = $lawRemark . ' | ' . $remark;
                        } elseif ($remark === '') {
                            $remark = $lawRemark;
                        }
                        $remark = mb_substr($remark, 0, 255);
                    }
                }

                $forcedRow = $forcedRows[(int) $studentId] ?? null;
                if (is_array($forcedRow)) {
                    $forcedStatus = strtolower(trim((string) ($forcedRow['status'] ?? '')));
                    if ($forcedStatus !== '') {
                        $status = $forcedStatus;
                    }

                    $forcedRemark = trim((string) ($forcedRow['remark'] ?? ''));
                    if ($forcedRemark !== '') {
                        $remark = $forcedRemark;
                    }
                }

                StudentAttendance::query()->updateOrCreate(
                    $this->attendanceMatchAttributes($studentId, $attendanceDate, $subjectId, $attendancePeriod),
                    $this->attendancePayload($teacherId, $schoolClassId, $subjectId, $status, $remark, $now)
                );

                $savedCount += 1;
                $savedStudentRows[] = [
                    'student_id' => (int) $studentId,
                    'status' => $status,
                ];
            }
        });

        return [$savedCount, $autoExcusedCount, $savedStudentRows];
    }

    private function buildStudentLawRequestRemark(StudentLawRequest $lawRequest): string
    {
        $lawType = trim((string) ($lawRequest->law_type ?? ''));
        $subject = trim((string) ($lawRequest->subject ?? ''));
        $subjectTime = trim((string) ($lawRequest->subject_time ?? ''));
        $reason = trim((string) ($lawRequest->reason ?? ''));
        $labelType = $lawType !== '' ? ucwords(str_replace('_', ' ', $lawType)) : 'Request';
        $remark = 'Law Request (' . $labelType . ')';

        if ($subject !== '') {
            $remark .= ': ' . $subject;
            if ($subjectTime !== '') {
                $remark .= ' @ ' . $subjectTime;
            }
        }

        if ($reason !== '') {
            $remark .= ($subject !== '' ? ' | ' : ': ') . $reason;
        }

        return trim($remark);
    }

    private function teacherSubjectOptions(int $teacherId, int $schoolClassId)
    {
        $query = Subject::query()
            ->orderBy('name');

        if (
            Schema::hasTable('subject_study_times')
            && Schema::hasColumn('subject_study_times', 'teacher_id')
            && Schema::hasColumn('subject_study_times', 'school_class_id')
        ) {
            $subjectIds = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->where('school_class_id', $schoolClassId)
                ->pluck('subject_id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            if ($subjectIds !== []) {
                return $query->whereIn('id', $subjectIds)->get(['id', 'name', 'code']);
            }
        }

        return $query
            ->where('teacher_id', $teacherId)
            ->where('school_class_id', $schoolClassId)
            ->get(['id', 'name', 'code']);
    }

    private function attendanceMatchAttributes(int $studentId, string $attendanceDate, ?int $subjectId = null, ?string $attendancePeriod = null): array
    {
        $attributes = [
            'student_id' => $studentId,
            'attendance_date' => $attendanceDate,
        ];

        if (Schema::hasColumn('student_attendances', 'subject_id')) {
            $attributes['subject_id'] = $subjectId > 0 ? $subjectId : null;
        }

        if (Schema::hasColumn('student_attendances', 'attendance_period')) {
            $period = strtolower(trim((string) $attendancePeriod));
            $attributes['attendance_period'] = $period !== '' ? $period : null;
        }

        return $attributes;
    }

    private function attendancePayload(?int $teacherId, ?int $schoolClassId, ?int $subjectId, string $status, ?string $remark, $checkedAt): array
    {
        $payload = [
            'teacher_id' => $teacherId,
            'school_class_id' => $schoolClassId,
            'status' => $status,
            'remark' => $remark !== '' ? $remark : null,
            'checked_at' => $checkedAt,
        ];

        if (Schema::hasColumn('student_attendances', 'subject_id')) {
            $payload['subject_id'] = $subjectId > 0 ? $subjectId : null;
        }

        return $payload;
    }

    private function resolveLawRequestSubjectId(StudentLawRequest $lawRequest): ?int
    {
        if (Schema::hasColumn('student_law_requests', 'subject_id')) {
            $subjectId = (int) ($lawRequest->subject_id ?? 0);
            if ($subjectId > 0) {
                return $subjectId;
            }
        }

        $subjectName = trim((string) ($lawRequest->subject ?? ''));
        if ($subjectName === '') {
            return null;
        }

        return Subject::query()
            ->where('school_class_id', (int) ($lawRequest->school_class_id ?? 0))
            ->where('name', $subjectName)
            ->value('id');
    }

    private function allowedStatuses(): array
    {
        return [
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'excused' => 'Excused',
        ];
    }

    private function cleanTeacherNotificationText(string $rawText): string
    {
        $text = trim($rawText);
        $text = preg_replace('/\[teacher_id:\d+\]\s*/', '', $text);

        return trim((string) $text);
    }
}
