<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class TeacherAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $statusRaw = strtolower(trim((string) $request->query('status', 'all')));
        $date = trim((string) $request->query('date', now()->toDateString()));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = now()->toDateString();
        }

        $statusLabels = $this->statusLabels();
        $status = array_key_exists($statusRaw, $statusLabels) ? $statusRaw : 'all';
        $hasTeacherAttendanceTable = Schema::hasTable('teacher_attendances');

        $teachers = User::query()
            ->where('role', 'teacher')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'is_active']);

        $attendanceByTeacher = collect();
        if ($hasTeacherAttendanceTable && $teachers->isNotEmpty()) {
            $attendanceByTeacher = TeacherAttendance::query()
                ->whereDate('attendance_date', $date)
                ->whereIn('teacher_id', $teachers->pluck('id')->all())
                ->when($status !== 'all', function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->get()
                ->keyBy('teacher_id');

            if ($status !== 'all') {
                $teachers = $teachers->filter(function ($teacher) use ($attendanceByTeacher) {
                    return $attendanceByTeacher->has((int) $teacher->id);
                })->values();
            }
        }

        $stats = [
            'teachers' => (int) $teachers->count(),
            'checked' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'not_marked' => 0,
        ];

        foreach ($teachers as $teacher) {
            $record = $attendanceByTeacher->get((int) $teacher->id);
            $statusKey = strtolower((string) ($record?->status ?? ''));
            if ($statusKey !== '' && isset($stats[$statusKey])) {
                $stats['checked'] += 1;
                $stats[$statusKey] += 1;
            } else {
                $stats['not_marked'] += 1;
            }
        }

        return view('admin.teacher-attendance', [
            'teachers' => $teachers,
            'attendanceByTeacher' => $attendanceByTeacher,
            'search' => $search,
            'status' => $status,
            'date' => $date,
            'statusLabels' => $statusLabels,
            'stats' => $stats,
            'hasTeacherAttendanceTable' => $hasTeacherAttendanceTable,
        ]);
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('teacher_attendances')) {
            return back()->withInput()->with('error', 'Teacher attendance table is missing. Please run migrations.');
        }

        $teacherIds = User::query()
            ->where('role', 'teacher')
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        if (count($teacherIds) === 0) {
            return back()->withInput()->with('warning', 'No teachers found.');
        }

        $statuses = array_keys($this->statusLabels());
        $statuses = array_values(array_filter($statuses, fn($status) => $status !== 'all'));

        $validated = $request->validate([
            'attendance_date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'string', Rule::in($statuses)],
            'attendance.*.remark' => ['nullable', 'string', 'max:255'],
        ]);

        $attendanceDate = (string) $validated['attendance_date'];
        $attendanceRows = (array) ($validated['attendance'] ?? []);
        $adminId = (int) ($request->user()?->id ?? 0);
        $now = now();
        $savedCount = 0;

        DB::transaction(function () use (
            $teacherIds,
            $attendanceRows,
            $attendanceDate,
            $adminId,
            $now,
            &$savedCount
        ) {
            foreach ($teacherIds as $teacherId) {
                $row = $attendanceRows[(string) $teacherId] ?? null;
                if (!is_array($row)) {
                    continue;
                }

                $status = strtolower(trim((string) ($row['status'] ?? '')));
                if ($status === '') {
                    continue;
                }

                $remark = trim((string) ($row['remark'] ?? ''));

                TeacherAttendance::query()->updateOrCreate(
                    [
                        'teacher_id' => $teacherId,
                        'attendance_date' => $attendanceDate,
                    ],
                    [
                        'marked_by' => $adminId > 0 ? $adminId : null,
                        'status' => $status,
                        'remark' => $remark !== '' ? $remark : null,
                        'checked_at' => $now,
                    ]
                );

                $savedCount += 1;
            }
        });

        return redirect()
            ->route('admin.attendance.teachers.index', [
                'date' => $attendanceDate,
            ])
            ->with('success', 'Teacher attendance checked successfully for ' . $savedCount . ' teacher(s).');
    }

    private function statusLabels(): array
    {
        return [
            'all' => 'All',
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'excused' => 'Excused',
        ];
    }
}
