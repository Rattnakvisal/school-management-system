<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentAttendance;
use App\Models\User;
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

        $classIds = $classes->pluck('id')->map(fn($id) => (int) $id)->values();
        $selectedClassIdRaw = trim((string) $request->query('class_id', (string) $request->old('school_class_id', '')));
        $selectedClassId = ctype_digit($selectedClassIdRaw) && $classIds->contains((int) $selectedClassIdRaw)
            ? (int) $selectedClassIdRaw
            : null;

        $selectedDate = trim((string) $request->query('date', (string) $request->old('attendance_date', now()->toDateString())));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
            $selectedDate = now()->toDateString();
        }

        $hasAttendanceTable = Schema::hasTable('student_attendances');
        $attendanceCheckedByClass = collect();
        $classAttendanceStatus = [];

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

        $students = collect();
        $attendanceByStudent = collect();
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
                $attendanceByStudent = StudentAttendance::query()
                    ->where('teacher_id', $teacherId)
                    ->where('school_class_id', $selectedClassId)
                    ->whereDate('attendance_date', $selectedDate)
                    ->get()
                    ->keyBy('student_id');
            }

            if ($statusFilter !== 'all' && in_array($statusFilter, $attendanceStatuses, true)) {
                $students = $students->filter(function ($student) use ($attendanceByStudent, $statusFilter) {
                    $record = $attendanceByStudent->get($student->id);
                    $currentStatus = strtolower((string) ($record?->status ?? ''));
                    return $currentStatus === $statusFilter;
                })->values();
            }
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

        return view('teacher.attendance', [
            'classes' => $classes,
            'classId' => $selectedClassId !== null ? (string) $selectedClassId : '',
            'selectedDate' => $selectedDate,
            'search' => $search,
            'statusFilter' => in_array($statusFilter, array_merge(['all'], $attendanceStatuses), true) ? $statusFilter : 'all',
            'students' => $students,
            'attendanceByStudent' => $attendanceByStudent,
            'statusLabels' => $allowedStatuses,
            'summary' => $summary,
            'hasAttendanceTable' => $hasAttendanceTable,
            'classAttendanceStatus' => $classAttendanceStatus,
            'hasClassDayColumn' => $hasClassDayColumn,
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

        $classIds = SchoolClass::query()
            ->whereHas('subjects', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        if (count($classIds) === 0) {
            return back()->withInput()->with('error', 'No class found for this teacher.');
        }

        $statuses = array_keys($this->allowedStatuses());
        $validated = $request->validate([
            'school_class_id' => ['required', 'integer', Rule::in($classIds)],
            'attendance_date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'string', Rule::in($statuses)],
            'attendance.*.remark' => ['nullable', 'string', 'max:255'],
        ]);

        $schoolClassId = (int) $validated['school_class_id'];
        $attendanceDate = (string) $validated['attendance_date'];
        $attendanceRows = $validated['attendance'] ?? [];

        $studentIds = User::query()
            ->where('role', 'student')
            ->where('school_class_id', $schoolClassId)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        if (count($studentIds) === 0) {
            return back()->withInput()->with('warning', 'No students found in selected class.');
        }

        $alreadyCheckedCount = StudentAttendance::query()
            ->where('teacher_id', $teacherId)
            ->where('school_class_id', $schoolClassId)
            ->whereDate('attendance_date', $attendanceDate)
            ->whereIn('student_id', $studentIds)
            ->distinct('student_id')
            ->count('student_id');

        if ($alreadyCheckedCount >= count($studentIds)) {
            return redirect()
                ->route('teacher.attendance.index', [
                    'class_id' => $schoolClassId,
                    'date' => $attendanceDate,
                ])
                ->with('warning', 'Attendance already saved successfully. Check status cannot be changed.');
        }

        $now = now();
        $savedCount = 0;

        DB::transaction(function () use (
            $studentIds,
            $attendanceRows,
            $attendanceDate,
            $schoolClassId,
            $teacherId,
            $now,
            &$savedCount
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

                StudentAttendance::query()->updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'attendance_date' => $attendanceDate,
                    ],
                    [
                        'teacher_id' => $teacherId,
                        'school_class_id' => $schoolClassId,
                        'status' => $status,
                        'remark' => $remark !== '' ? $remark : null,
                        'checked_at' => $now,
                    ]
                );

                $savedCount += 1;
            }
        });

        return redirect()
            ->route('teacher.attendance.index', [
                'class_id' => $schoolClassId,
                'date' => $attendanceDate,
            ])
            ->with('success', 'Attendance checked successfully for ' . $savedCount . ' student(s).');
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
}
