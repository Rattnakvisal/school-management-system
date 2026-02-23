<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\SchoolClass;
use App\Models\SubjectStudyTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = (int) ($request->user()?->id ?? 0);
        $search = trim((string) $request->query('q', ''));
        $classIdRaw = (string) $request->query('class_id', 'all');
        $dayRaw = strtolower(trim((string) $request->query('day', 'all')));

        $dayOptions = [
            'all' => 'All Days',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        $selectedDay = array_key_exists($dayRaw, $dayOptions) ? $dayRaw : 'all';
        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');

        $teacherClasses = SchoolClass::query()
            ->whereHas('subjects', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->orderBy('name')
            ->orderBy('section')
            ->get(['id', 'name', 'section']);

        $classIds = $teacherClasses->pluck('id')->map(fn($id) => (int) $id)->values();
        $classId = ctype_digit($classIdRaw) && $classIds->contains((int) $classIdRaw) ? (int) $classIdRaw : null;

        $classSlots = ClassStudyTime::query()
            ->with('schoolClass')
            ->whereIn('school_class_id', $classIds)
            ->when($classId !== null, function ($query) use ($classId) {
                $query->where('school_class_id', $classId);
            })
            ->when($selectedDay !== 'all' && $hasClassDayColumn, function ($query) use ($selectedDay) {
                $query->where(function ($inner) use ($selectedDay) {
                    $inner->where('day_of_week', $selectedDay)
                        ->orWhere('day_of_week', 'all');
                });
            })
            ->when($search !== '', function ($query) use ($search, $hasClassDayColumn) {
                $query->where(function ($inner) use ($search, $hasClassDayColumn) {
                    if ($hasClassDayColumn) {
                        $inner->where('day_of_week', 'like', '%' . $search . '%')
                            ->orWhere('period', 'like', '%' . $search . '%')
                            ->orWhere('start_time', 'like', '%' . $search . '%')
                            ->orWhere('end_time', 'like', '%' . $search . '%');
                    } else {
                        $inner->where('period', 'like', '%' . $search . '%')
                            ->orWhere('start_time', 'like', '%' . $search . '%')
                            ->orWhere('end_time', 'like', '%' . $search . '%');
                    }

                    $inner->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                        $classQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('section', 'like', '%' . $search . '%')
                            ->orWhere('room', 'like', '%' . $search . '%');
                    });
                });
            })
            ->orderBy('school_class_id')
            ->when($hasClassDayColumn, function ($query) {
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
            ->get();

        $subjectSlots = SubjectStudyTime::query()
            ->with('subject.schoolClass')
            ->whereHas('subject', function ($query) use ($teacherId, $classId) {
                $query->where('teacher_id', $teacherId);
                if ($classId !== null) {
                    $query->where('school_class_id', $classId);
                }
            })
            ->when($selectedDay !== 'all' && $hasSubjectDayColumn, function ($query) use ($selectedDay) {
                $query->where(function ($inner) use ($selectedDay) {
                    $inner->where('day_of_week', $selectedDay)
                        ->orWhere('day_of_week', 'all');
                });
            })
            ->when($search !== '', function ($query) use ($search, $hasSubjectDayColumn) {
                $query->where(function ($inner) use ($search, $hasSubjectDayColumn) {
                    if ($hasSubjectDayColumn) {
                        $inner->where('day_of_week', 'like', '%' . $search . '%')
                            ->orWhere('period', 'like', '%' . $search . '%')
                            ->orWhere('start_time', 'like', '%' . $search . '%')
                            ->orWhere('end_time', 'like', '%' . $search . '%');
                    } else {
                        $inner->where('period', 'like', '%' . $search . '%')
                            ->orWhere('start_time', 'like', '%' . $search . '%')
                            ->orWhere('end_time', 'like', '%' . $search . '%');
                    }

                    $inner->orWhereHas('subject', function ($subjectQuery) use ($search) {
                        $subjectQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('code', 'like', '%' . $search . '%');
                    });
                });
            })
            ->orderBy('subject_id')
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
            ->get();

        $stats = [
            'classes' => $teacherClasses->count(),
            'classSlots' => $classSlots->count(),
            'subjectSlots' => $subjectSlots->count(),
        ];

        return view('teacher.schedule', [
            'classes' => $teacherClasses,
            'classSlots' => $classSlots,
            'subjectSlots' => $subjectSlots,
            'search' => $search,
            'classId' => $classId !== null ? (string) $classId : 'all',
            'selectedDay' => $selectedDay,
            'dayOptions' => $dayOptions,
            'dayColumnEnabled' => $hasClassDayColumn || $hasSubjectDayColumn,
            'stats' => $stats,
            'periodLabels' => [
                'morning' => 'Morning',
                'afternoon' => 'Afternoon',
                'evening' => 'Evening',
                'night' => 'Night',
                'custom' => 'Custom',
            ],
        ]);
    }
}
