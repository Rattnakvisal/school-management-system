<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = (int) ($request->user()?->id ?? 0);
        $search = trim((string) $request->query('q', ''));
        $room = trim((string) $request->query('room', 'all'));
        $period = strtolower(trim((string) $request->query('period', 'all')));
        $schedule = strtolower(trim((string) $request->query('schedule', 'all')));
        $allowedPeriods = ['morning', 'afternoon', 'evening', 'night', 'custom'];
        $allowedSchedules = ['all', 'with_schedule', 'without_schedule'];
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasSubjectTeacherColumn = Schema::hasColumn('subject_study_times', 'teacher_id');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $useSlotAssignments = $hasSubjectClassColumn && $hasSubjectTeacherColumn;

        if ($useSlotAssignments) {
            $teacherClassIds = SubjectStudyTime::query()
                ->where('teacher_id', $teacherId)
                ->whereNotNull('school_class_id')
                ->distinct()
                ->pluck('school_class_id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->values();

            $baseTeacherClassesQuery = SchoolClass::query()
                ->whereIn('id', $teacherClassIds->all());
        } else {
            $baseTeacherClassesQuery = SchoolClass::query()
                ->whereHas('subjects', function ($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId);
                });
        }

        $roomOptions = (clone $baseTeacherClassesQuery)
            ->whereNotNull('room')
            ->where('room', '!=', '')
            ->orderBy('room')
            ->distinct()
            ->pluck('room')
            ->values();

        $classQuery = (clone $baseTeacherClassesQuery)
            ->withCount('students')
            ->when($room !== '' && strtolower($room) !== 'all', function ($query) use ($room) {
                $query->where('room', $room);
            })
            ->when(in_array($period, $allowedPeriods, true), function ($query) use ($period) {
                if ($useSlotAssignments) {
                    $query->whereExists(function ($slotQuery) use ($teacherId, $period) {
                        $slotQuery->select(DB::raw(1))
                            ->from('subject_study_times as sst')
                            ->whereColumn('sst.school_class_id', 'school_classes.id')
                            ->where('sst.teacher_id', $teacherId)
                            ->whereRaw('LOWER(sst.period) = ?', [$period]);
                    });
                } else {
                    $query->whereHas('studySchedules', function ($scheduleQuery) use ($period) {
                        $scheduleQuery->whereRaw('LOWER(period) = ?', [$period]);
                    });
                }
            })
            ->when($schedule === 'with_schedule', function ($query) use ($useSlotAssignments, $teacherId) {
                if ($useSlotAssignments) {
                    $query->whereExists(function ($slotQuery) use ($teacherId) {
                        $slotQuery->select(DB::raw(1))
                            ->from('subject_study_times as sst')
                            ->whereColumn('sst.school_class_id', 'school_classes.id')
                            ->where('sst.teacher_id', $teacherId);
                    });
                    return;
                }

                $query->whereHas('studySchedules');
            })
            ->when($schedule === 'without_schedule', function ($query) use ($useSlotAssignments, $teacherId) {
                if ($useSlotAssignments) {
                    $query->whereNotExists(function ($slotQuery) use ($teacherId) {
                        $slotQuery->select(DB::raw(1))
                            ->from('subject_study_times as sst')
                            ->whereColumn('sst.school_class_id', 'school_classes.id')
                            ->where('sst.teacher_id', $teacherId);
                    });
                    return;
                }

                $query->whereDoesntHave('studySchedules');
            })
            ->orderBy('name')
            ->orderBy('section');

        if ($useSlotAssignments) {
            $classQuery->when($search !== '', function ($query) use ($search, $teacherId) {
                $query->where(function ($inner) use ($search, $teacherId) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('section', 'like', '%' . $search . '%')
                        ->orWhere('room', 'like', '%' . $search . '%')
                        ->orWhereExists(function ($slotQuery) use ($teacherId, $search) {
                            $slotQuery->select(DB::raw('1'))
                                ->from('subject_study_times as sst')
                                ->join('subjects as s', 's.id', '=', 'sst.subject_id')
                                ->whereColumn('sst.school_class_id', 'school_classes.id')
                                ->where('sst.teacher_id', $teacherId)
                                ->where(function ($subjectQuery) use ($search) {
                                    $subjectQuery->where('s.name', 'like', '%' . $search . '%')
                                        ->orWhere('s.code', 'like', '%' . $search . '%');
                                });
                        });
                });
            });
        } else {
            $classQuery
                ->with([
                    'subjects' => function ($query) use ($teacherId) {
                        $query->where('teacher_id', $teacherId)->orderBy('name');
                    },
                ])
                ->withCount([
                    'subjects as taught_subjects_count' => function ($query) use ($teacherId) {
                        $query->where('teacher_id', $teacherId);
                    },
                ])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('name', 'like', '%' . $search . '%')
                            ->orWhere('section', 'like', '%' . $search . '%')
                            ->orWhere('room', 'like', '%' . $search . '%')
                            ->orWhereHas('subjects', function ($subjectQuery) use ($search) {
                                $subjectQuery
                                    ->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('code', 'like', '%' . $search . '%');
                            });
                    });
                });
        }

        $classes = $classQuery
            ->paginate(10)
            ->withQueryString();

        if ($useSlotAssignments) {
            $classIds = $classes->getCollection()
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->values();

            $subjectsByClass = collect();
            if ($classIds->isNotEmpty()) {
                $subjectsByClass = Subject::query()
                    ->select(['subjects.id', 'subjects.name', 'subject_study_times.school_class_id'])
                    ->join('subject_study_times', 'subject_study_times.subject_id', '=', 'subjects.id')
                    ->where('subject_study_times.teacher_id', $teacherId)
                    ->whereIn('subject_study_times.school_class_id', $classIds->all())
                    ->distinct()
                    ->orderBy('subjects.name')
                    ->get()
                    ->groupBy(fn($row) => (string) $row->school_class_id);
            }

            $teacherStudyTimesByClass = collect();
            if ($classIds->isNotEmpty()) {
                $slotSelect = ['id', 'subject_id', 'school_class_id', 'period', 'start_time', 'end_time', 'sort_order'];
                if ($hasSubjectDayColumn) {
                    $slotSelect[] = 'day_of_week';
                }

                $teacherStudyTimesByClass = SubjectStudyTime::query()
                    ->with(['subject:id,name', 'schoolClass:id,name,section'])
                    ->select($slotSelect)
                    ->where('teacher_id', $teacherId)
                    ->whereIn('school_class_id', $classIds->all())
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

            $classes->getCollection()->transform(function (SchoolClass $schoolClass) use ($subjectsByClass, $teacherStudyTimesByClass) {
                $assignedSubjects = $subjectsByClass
                    ->get((string) $schoolClass->id, collect())
                    ->values()
                    ->map(function ($row) {
                        return new Subject([
                            'id' => (int) $row->id,
                            'name' => (string) $row->name,
                        ]);
                    });

                $schoolClass->setRelation('subjects', $assignedSubjects instanceof Collection ? $assignedSubjects : collect());
                $schoolClass->setAttribute('taught_subjects_count', $assignedSubjects->count());

                $teacherStudySlots = $teacherStudyTimesByClass
                    ->get((string) $schoolClass->id, collect())
                    ->values();
                $schoolClass->setRelation('studySchedules', $teacherStudySlots);
                $schoolClass->setRelation('teacherStudySchedules', $teacherStudySlots);
                $schoolClass->setAttribute('class_slots_count', $teacherStudySlots->count());

                return $schoolClass;
            });
        }

        $totalClasses = (clone $classQuery)->toBase()->getCountForPagination();
        $totalStudents = $classes->getCollection()->sum('students_count');
        $totalSubjects = $classes->getCollection()->sum('taught_subjects_count');

        return view('teacher.classes', [
            'classes' => $classes,
            'search' => $search,
            'room' => $room !== '' ? $room : 'all',
            'period' => in_array($period, array_merge(['all'], $allowedPeriods), true) ? $period : 'all',
            'schedule' => in_array($schedule, $allowedSchedules, true) ? $schedule : 'all',
            'roomOptions' => $roomOptions,
            'hasSubjectDayColumn' => $hasSubjectDayColumn,
            'stats' => [
                'classes' => $totalClasses,
                'students' => (int) $totalStudents,
                'subjects' => (int) $totalSubjects,
            ],
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
}
