<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();
        $teacherId = (int) ($teacher?->id ?? 0);
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasSubjectTeacherColumn = Schema::hasColumn('subject_study_times', 'teacher_id');
        $useSlotAssignments = $hasSubjectClassColumn && $hasSubjectTeacherColumn;

        if ($useSlotAssignments) {
            $classIds = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->whereNotNull('school_class_id')
                ->distinct()
                ->pluck('school_class_id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->values();

            $classes = SchoolClass::query()
                ->whereIn('id', $classIds->all())
                ->withCount('students')
                ->orderBy('name')
                ->orderBy('section')
                ->get(['id', 'name', 'section', 'room']);

            $subjectCountsByClass = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->whereIn('school_class_id', $classIds->all())
                ->selectRaw('school_class_id, COUNT(DISTINCT subject_id) as subject_count')
                ->groupBy('school_class_id')
                ->pluck('subject_count', 'school_class_id');

            $classes->each(function (SchoolClass $schoolClass) use ($subjectCountsByClass): void {
                $schoolClass->setAttribute(
                    'taught_subjects_count',
                    (int) $subjectCountsByClass->get((int) $schoolClass->id, 0)
                );
            });
        } else {
            $classes = SchoolClass::query()
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
                ->get(['id', 'name', 'section', 'room']);
        }

        $classIds = $classes->pluck('id')->map(fn($id) => (int) $id)->values();
        $selectedClassIdRaw = (string) $request->query('class_id', (string) ($classes->first()?->id ?? ''));
        $selectedClassId = ctype_digit($selectedClassIdRaw) && $classIds->contains((int) $selectedClassIdRaw)
            ? (int) $selectedClassIdRaw
            : null;

        $selectedDate = trim((string) $request->query('date', now()->toDateString()));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
            $selectedDate = now()->toDateString();
        }

        $hasAttendanceTable = Schema::hasTable('student_attendances');
        $subjectCount = $useSlotAssignments
            ? (int) SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->distinct('subject_id')
                ->count('subject_id')
            : (int) Subject::query()->where('teacher_id', $teacherId)->count();

        $stats = [
            'classes' => $classes->count(),
            'subjects' => $subjectCount,
            'students' => (int) $classes->sum('students_count'),
            'checkedToday' => 0,
            'checkedThisWeek' => 0,
            'selectedDateChecked' => 0,
        ];

        $statusLabels = [
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'excused' => 'Excused',
        ];

        $selectedDateSummary = [
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
        ];

        $recentAttendances = collect();
        if ($hasAttendanceTable) {
            $attendanceBase = StudentAttendance::query()->where('teacher_id', $teacherId);

            $stats['checkedToday'] = (int) (clone $attendanceBase)
                ->whereDate('attendance_date', now()->toDateString())
                ->count();

            $stats['checkedThisWeek'] = (int) (clone $attendanceBase)
                ->whereBetween('attendance_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
                ->count();

            if ($selectedClassId !== null) {
                $selectedDateRows = (clone $attendanceBase)
                    ->where('school_class_id', $selectedClassId)
                    ->whereDate('attendance_date', $selectedDate)
                    ->get(['status']);

                $stats['selectedDateChecked'] = (int) $selectedDateRows->count();
                foreach ($selectedDateRows as $row) {
                    $status = strtolower((string) $row->status);
                    if (array_key_exists($status, $selectedDateSummary)) {
                        $selectedDateSummary[$status] += 1;
                    }
                }
            }

            $recentAttendances = (clone $attendanceBase)
                ->with(['student:id,name,email', 'schoolClass:id,name,section'])
                ->orderByDesc('checked_at')
                ->orderByDesc('id')
                ->limit(8)
                ->get();
        }

        return view('teacher.settings', [
            'teacher' => $teacher,
            'classes' => $classes,
            'classId' => $selectedClassId !== null ? (string) $selectedClassId : '',
            'selectedDate' => $selectedDate,
            'hasAttendanceTable' => $hasAttendanceTable,
            'stats' => $stats,
            'statusLabels' => $statusLabels,
            'selectedDateSummary' => $selectedDateSummary,
            'recentAttendances' => $recentAttendances,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->id)],
            'avatar' => ['nullable', 'file', 'image', 'max:4096'],
        ]);

        $teacher->name = (string) $validated['name'];
        $teacher->email = (string) $validated['email'];

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars/teachers', 'public');
            if ($avatarPath !== false) {
                $teacher->avatar = $avatarPath;
            }
        }

        $teacher->save();

        return redirect()
            ->route('teacher.settings')
            ->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $teacher->password = (string) $validated['password'];
        $teacher->save();

        return redirect()
            ->route('teacher.settings')
            ->with('success', 'Password updated successfully.');
    }
}
