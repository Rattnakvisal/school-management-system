<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();
        $studentId = (int) ($student?->id ?? 0);
        $selectedDate = trim((string) $request->query('date', ''));
        if ($selectedDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
            $selectedDate = '';
        }

        $statusLabels = $this->statusLabels();
        $statusFilter = strtolower(trim((string) $request->query('status', 'all')));
        if (!array_key_exists($statusFilter, $statusLabels)) {
            $statusFilter = 'all';
        }

        $subjectOptions = $this->studentSubjects($student);
        $subjectIds = $subjectOptions->pluck('id')->map(fn ($id) => (int) $id)->filter(fn ($id) => $id > 0)->values();
        $selectedSubjectRaw = trim((string) $request->query('subject_id', 'all'));
        $selectedSubjectId = ctype_digit($selectedSubjectRaw) && $subjectIds->contains((int) $selectedSubjectRaw)
            ? (int) $selectedSubjectRaw
            : null;

        $hasAttendanceTable = Schema::hasTable('student_attendances');
        $recordsQuery = StudentAttendance::query()
            ->where('student_id', $studentId)
            ->with([
                'teacher:id,name,email',
                'schoolClass:id,name,section',
                'subject:id,name,code',
            ]);

        if ($hasAttendanceTable && $selectedDate !== '') {
            $recordsQuery->whereDate('attendance_date', $selectedDate);
        }

        if (
            $hasAttendanceTable
            && $selectedSubjectId !== null
            && Schema::hasColumn('student_attendances', 'subject_id')
        ) {
            $recordsQuery->where('subject_id', $selectedSubjectId);
        }

        if ($hasAttendanceTable && $statusFilter !== 'all') {
            $recordsQuery->where('status', $statusFilter);
        }

        $records = $hasAttendanceTable
            ? (clone $recordsQuery)
                ->orderByDesc('attendance_date')
                ->orderByDesc('checked_at')
                ->orderByDesc('id')
                ->paginate(12)
                ->appends([
                    'date' => $selectedDate,
                    'status' => $statusFilter,
                    'subject_id' => $selectedSubjectId !== null ? (string) $selectedSubjectId : 'all',
                ])
            : collect();

        $summaryQuery = StudentAttendance::query()->where('student_id', $studentId);
        $summary = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
        ];

        if ($hasAttendanceTable) {
            if (
                $selectedSubjectId !== null
                && Schema::hasColumn('student_attendances', 'subject_id')
            ) {
                $summaryQuery->where('subject_id', $selectedSubjectId);
            }

            if ($selectedDate !== '') {
                $summaryQuery->whereDate('attendance_date', $selectedDate);
            }

            $summary['total'] = (clone $summaryQuery)->count();
            foreach (['present', 'absent', 'late', 'excused'] as $statusKey) {
                $summary[$statusKey] = (clone $summaryQuery)->where('status', $statusKey)->count();
            }
        }

        return view('student.attendance', [
            'student' => $student,
            'records' => $records,
            'summary' => $summary,
            'statusLabels' => $statusLabels,
            'statusFilter' => $statusFilter,
            'subjectOptions' => $subjectOptions,
            'subjectId' => $selectedSubjectId !== null ? (string) $selectedSubjectId : 'all',
            'selectedDate' => $selectedDate,
            'hasAttendanceTable' => $hasAttendanceTable,
        ]);
    }

    private function studentSubjects(?User $student)
    {
        $classId = (int) ($student?->school_class_id ?? 0);
        if ($classId <= 0) {
            return collect();
        }

        $query = Subject::query()
            ->where('school_class_id', $classId)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        if ($student && Schema::hasTable('student_major_subjects')) {
            $query = $query
                ->concat(
                    $student->majorSubjects()
                        ->orderBy('subjects.name')
                        ->get(['subjects.id', 'subjects.name', 'subjects.code'])
                )
                ->unique('id')
                ->values();
        }

        return $query->sortBy(fn ($subject) => strtolower(trim((string) ($subject->name ?? ''))))->values();
    }

    private function statusLabels(): array
    {
        return [
            'all' => 'All Status',
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'excused' => 'Excused',
        ];
    }
}
