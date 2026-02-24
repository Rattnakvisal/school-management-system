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
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasSubjectTeacherColumn = Schema::hasColumn('subject_study_times', 'teacher_id');

        if ($hasSubjectClassColumn && $hasSubjectTeacherColumn) {
            $teacherClassIds = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->whereNotNull('school_class_id')
                ->distinct()
                ->pluck('school_class_id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->values();

            $teacherClasses = SchoolClass::query()
                ->whereIn('id', $teacherClassIds->all())
                ->orderBy('name')
                ->orderBy('section')
                ->get(['id', 'name', 'section']);
        } else {
            $teacherClasses = SchoolClass::query()
                ->whereHas('subjects', function ($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId);
                })
                ->orderBy('name')
                ->orderBy('section')
                ->get(['id', 'name', 'section']);
        }

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

        if ($hasSubjectClassColumn && $hasSubjectTeacherColumn) {
            $assignedSubjectSlots = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->whereIn('school_class_id', $classIds->all())
                ->when($classId !== null, function ($query) use ($classId) {
                    $query->where('school_class_id', $classId);
                })
                ->when($selectedDay !== 'all' && $hasSubjectDayColumn, function ($query) use ($selectedDay) {
                    $query->where(function ($inner) use ($selectedDay) {
                        $inner->where('day_of_week', $selectedDay)
                            ->orWhere('day_of_week', 'all');
                    });
                })
                ->get(['school_class_id', 'period', 'start_time', 'end_time', 'day_of_week']);

            $assignedByClass = $assignedSubjectSlots->groupBy(fn($slot) => (string) $slot->school_class_id);
            $classSlots = $classSlots
                ->filter(function (ClassStudyTime $classSlot) use ($assignedByClass, $hasClassDayColumn) {
                    $slots = $assignedByClass->get((string) $classSlot->school_class_id, collect());
                    if ($slots->isEmpty()) {
                        return false;
                    }

                    $classDay = strtolower((string) ($hasClassDayColumn ? ($classSlot->day_of_week ?? 'all') : 'all'));
                    $classPeriod = strtolower((string) $classSlot->period);
                    $classStart = (string) $classSlot->start_time;
                    $classEnd = (string) $classSlot->end_time;

                    return $slots->contains(function ($subjectSlot) use ($classDay, $classPeriod, $classStart, $classEnd) {
                        $subjectDay = strtolower((string) ($subjectSlot->day_of_week ?? 'all'));
                        $sameDay = $subjectDay === $classDay || $subjectDay === 'all' || $classDay === 'all';

                        return $sameDay
                            && strtolower((string) $subjectSlot->period) === $classPeriod
                            && (string) $subjectSlot->start_time === $classStart
                            && (string) $subjectSlot->end_time === $classEnd;
                    });
                })
                ->values();
        }

        $subjectSlots = SubjectStudyTime::query()
            ->with(['subject.schoolClass', 'schoolClass'])
            ->when($hasSubjectTeacherColumn, function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            }, function ($query) use ($teacherId) {
                $query->whereHas('subject', function ($subjectQuery) use ($teacherId) {
                    $subjectQuery->where('teacher_id', $teacherId);
                });
            })
            ->when($classId !== null, function ($query) use ($classId, $hasSubjectClassColumn) {
                if ($hasSubjectClassColumn) {
                    $query->where('school_class_id', $classId);
                    return;
                }

                $query->whereHas('subject', function ($subjectQuery) use ($classId) {
                    $subjectQuery->where('school_class_id', $classId);
                });
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
                    })->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                        $classQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('section', 'like', '%' . $search . '%')
                            ->orWhere('room', 'like', '%' . $search . '%');
                    });
                });
            })
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
            ->orderBy('start_time')
            ->when($hasSubjectClassColumn, function ($query) {
                $query->orderBy('school_class_id');
            })
            ->orderBy('subject_id')
            ->orderBy('sort_order')
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
