<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $classIdRaw = (string) $request->query('class_id', 'all');
        $teacherIdRaw = (string) $request->query('teacher_id', 'all');
        $statusRaw = strtolower(trim((string) $request->query('status', 'all')));
        $date = trim((string) $request->query('date', now()->toDateString()));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = now()->toDateString();
        }

        $classId = ctype_digit($classIdRaw) ? (int) $classIdRaw : null;
        $teacherId = ctype_digit($teacherIdRaw) ? (int) $teacherIdRaw : null;
        $statusLabels = $this->statusLabels();
        $status = array_key_exists($statusRaw, $statusLabels) ? $statusRaw : 'all';
        $hasAttendanceTable = Schema::hasTable('student_attendances');

        $classes = SchoolClass::query()
            ->orderBy('name')
            ->orderBy('section')
            ->get(['id', 'name', 'section', 'room']);

        $teachers = User::query()
            ->where('role', 'teacher')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        if (!$hasAttendanceTable) {
            return view('admin.attendance', [
                'records' => collect(),
                'classes' => $classes,
                'teachers' => $teachers,
                'search' => $search,
                'classId' => $classId !== null ? (string) $classId : 'all',
                'teacherId' => $teacherId !== null ? (string) $teacherId : 'all',
                'status' => $status,
                'date' => $date,
                'statusLabels' => $statusLabels,
                'stats' => $this->emptyStats(),
                'hasAttendanceTable' => false,
            ]);
        }

        $filteredQuery = StudentAttendance::query()
            ->from('student_attendances as attendance')
            ->join('users as students', 'students.id', '=', 'attendance.student_id')
            ->join('users as teachers', function ($join) {
                $join->on('teachers.id', '=', 'attendance.teacher_id')
                    ->where('teachers.role', '=', 'teacher');
            })
            ->join('school_classes as classes', 'classes.id', '=', 'attendance.school_class_id')
            ->whereDate('attendance.attendance_date', $date)
            ->when($classId !== null, function ($query) use ($classId) {
                $query->where('attendance.school_class_id', $classId);
            })
            ->when($teacherId !== null, function ($query) use ($teacherId) {
                $query->where('attendance.teacher_id', $teacherId);
            })
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('attendance.status', $status);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('students.name', 'like', '%' . $search . '%')
                        ->orWhere('students.email', 'like', '%' . $search . '%')
                        ->orWhere('teachers.name', 'like', '%' . $search . '%')
                        ->orWhere('teachers.email', 'like', '%' . $search . '%')
                        ->orWhere('classes.name', 'like', '%' . $search . '%')
                        ->orWhere('classes.section', 'like', '%' . $search . '%')
                        ->orWhere('attendance.remark', 'like', '%' . $search . '%');
                });
            })
            ->select([
                'attendance.id',
                'attendance.student_id',
                'attendance.teacher_id',
                'attendance.school_class_id',
                'attendance.attendance_date',
                'attendance.status',
                'attendance.remark',
                'attendance.checked_at',
                'attendance.created_at',
                'students.name as student_name',
                'students.email as student_email',
                'teachers.name as teacher_name',
                'teachers.email as teacher_email',
                'classes.name as class_name',
                'classes.section as class_section',
                'classes.room as class_room',
            ]);

        $records = (clone $filteredQuery)
            ->orderByDesc('attendance.attendance_date')
            ->orderByDesc('attendance.checked_at')
            ->orderByDesc('attendance.id')
            ->paginate(20)
            ->appends([
                'q' => $search,
                'class_id' => $classId !== null ? (string) $classId : 'all',
                'teacher_id' => $teacherId !== null ? (string) $teacherId : 'all',
                'status' => $status,
                'date' => $date,
            ]);

        $records->getCollection()->transform(function ($row) {
            $classLabel = trim((string) ($row->class_name ?? ''));
            $section = trim((string) ($row->class_section ?? ''));
            if ($classLabel !== '' && $section !== '') {
                $classLabel .= ' - ' . $section;
            } elseif ($classLabel === '') {
                $classLabel = 'Unassigned';
            }

            $row->class_label = $classLabel;
            return $row;
        });

        $stats = [
            'records' => (clone $filteredQuery)->count(),
            'students' => (clone $filteredQuery)->distinct('attendance.student_id')->count('attendance.student_id'),
            'teachers' => (clone $filteredQuery)->distinct('attendance.teacher_id')->count('attendance.teacher_id'),
            'classes' => (clone $filteredQuery)->distinct('attendance.school_class_id')->count('attendance.school_class_id'),
            'present' => (clone $filteredQuery)->where('attendance.status', 'present')->count(),
            'absent' => (clone $filteredQuery)->where('attendance.status', 'absent')->count(),
            'late' => (clone $filteredQuery)->where('attendance.status', 'late')->count(),
            'excused' => (clone $filteredQuery)->where('attendance.status', 'excused')->count(),
        ];

        return view('admin.attendance', [
            'records' => $records,
            'classes' => $classes,
            'teachers' => $teachers,
            'search' => $search,
            'classId' => $classId !== null ? (string) $classId : 'all',
            'teacherId' => $teacherId !== null ? (string) $teacherId : 'all',
            'status' => $status,
            'date' => $date,
            'statusLabels' => $statusLabels,
            'stats' => $stats,
            'hasAttendanceTable' => true,
        ]);
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

    private function emptyStats(): array
    {
        return [
            'records' => 0,
            'students' => 0,
            'teachers' => 0,
            'classes' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
        ];
    }
}
