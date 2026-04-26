<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TimeStudyController extends Controller
{
    public function index(Request $request)
    {
        $tab = (string) $request->query('tab', 'class');
        $period = (string) $request->query('period', 'all');
        $day = strtolower((string) $request->query('day', 'all'));
        $classIdRaw = (string) $request->query('class_id', 'all');
        $subjectIdRaw = (string) $request->query('subject_id', 'all');
        $teacherIdRaw = (string) $request->query('teacher_id', 'all');
        $search = trim((string) $request->query('q', ''));
        $perPageRaw = (string) $request->query('per_page', '15');

        $perPageOptions = [10, 15];
        $perPage = (int) $perPageRaw;
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 15;
        }

        $classId = ctype_digit($classIdRaw) ? (int) $classIdRaw : null;
        $subjectId = ctype_digit($subjectIdRaw) ? (int) $subjectIdRaw : null;
        $teacherId = ctype_digit($teacherIdRaw) ? (int) $teacherIdRaw : null;

        $allowedPeriods = $this->allowedPeriods();
        $allowedDays = $this->allowedDays();
        $day = in_array($day, $allowedDays, true) ? $day : 'all';

        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasSubjectTeacherColumn = Schema::hasColumn('subject_study_times', 'teacher_id');
        $useDayInSlotKey = $hasClassDayColumn && $hasSubjectDayColumn;

        $classTimes = ClassStudyTime::query()
            ->with('schoolClass')
            ->when($search !== '', function ($query) use ($search, $hasClassDayColumn) {
                $query->where(function ($innerQuery) use ($search, $hasClassDayColumn) {
                    if ($hasClassDayColumn) {
                        $innerQuery->where('day_of_week', 'like', '%' . $search . '%')
                            ->orWhere('period', 'like', '%' . $search . '%')
                            ->orWhere('start_time', 'like', '%' . $search . '%')
                            ->orWhere('end_time', 'like', '%' . $search . '%');
                    } else {
                        $innerQuery->where('period', 'like', '%' . $search . '%')
                            ->orWhere('start_time', 'like', '%' . $search . '%')
                            ->orWhere('end_time', 'like', '%' . $search . '%');
                    }

                    $innerQuery->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                        $classQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('section', 'like', '%' . $search . '%')
                            ->orWhereRaw("CONCAT(name, ' - ', section) like ?", ['%' . $search . '%']);
                    });
                });
            })
            ->when($classId !== null, function ($query) use ($classId) {
                $query->where('school_class_id', $classId);
            })
            ->when(in_array($period, $allowedPeriods, true), function ($query) use ($period) {
                $query->where('period', $period);
            })
            ->when($day !== 'all' && $hasClassDayColumn, function ($query) use ($day) {
                $query->where(function ($innerQuery) use ($day) {
                    $innerQuery->where('day_of_week', $day)
                        ->orWhere('day_of_week', 'all');
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
            ->paginate($perPage, ['*'], 'class_page')
            ->withQueryString();

        $subjectTimes = SubjectStudyTime::query()
            ->with(['subject.schoolClass', 'schoolClass', 'teacher'])
            ->when($search !== '', function ($query) use ($search, $hasSubjectDayColumn) {
                $query->where(function ($innerQuery) use ($search, $hasSubjectDayColumn) {
                    if ($hasSubjectDayColumn) {
                        $innerQuery->where('day_of_week', 'like', '%' . $search . '%')
                            ->orWhere('period', 'like', '%' . $search . '%')
                            ->orWhere('start_time', 'like', '%' . $search . '%')
                            ->orWhere('end_time', 'like', '%' . $search . '%');
                    } else {
                        $innerQuery->where('period', 'like', '%' . $search . '%')
                            ->orWhere('start_time', 'like', '%' . $search . '%')
                            ->orWhere('end_time', 'like', '%' . $search . '%');
                    }

                    $innerQuery->orWhereHas('subject', function ($subjectQuery) use ($search) {
                        $subjectQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('code', 'like', '%' . $search . '%')
                            ->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                                $classQuery
                                    ->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('section', 'like', '%' . $search . '%')
                                    ->orWhereRaw("CONCAT(name, ' - ', section) like ?", ['%' . $search . '%']);
                            });
                    });

                    $innerQuery->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                        $classQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('section', 'like', '%' . $search . '%')
                            ->orWhereRaw("CONCAT(name, ' - ', section) like ?", ['%' . $search . '%']);
                    })->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                        $teacherQuery->where('name', 'like', '%' . $search . '%');
                    });
                });
            })
            ->when($subjectId !== null, function ($query) use ($subjectId) {
                $query->where('subject_id', $subjectId);
            })
            ->when($teacherId !== null, function ($query) use ($teacherId, $hasSubjectTeacherColumn) {
                if ($hasSubjectTeacherColumn) {
                    $query->where('teacher_id', $teacherId);
                    return;
                }

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
            ->when(in_array($period, $allowedPeriods, true), function ($query) use ($period) {
                $query->where('period', $period);
            })
            ->when($day !== 'all' && $hasSubjectDayColumn, function ($query) use ($day) {
                $query->where(function ($innerQuery) use ($day) {
                    $innerQuery->where('day_of_week', $day)
                        ->orWhere('day_of_week', 'all');
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
            ->paginate($perPage, ['*'], 'subject_page')
            ->withQueryString();

        $teacherTimes = SubjectStudyTime::query()
            ->with(['subject.schoolClass', 'schoolClass', 'teacher', 'subject.teacher'])
            ->where(function ($query) use ($hasSubjectTeacherColumn) {
                if ($hasSubjectTeacherColumn) {
                    $query->whereNotNull('teacher_id');
                }

                $query->orWhereHas('subject', function ($subjectQuery) {
                    $subjectQuery->whereNotNull('teacher_id');
                });
            })
            ->when($search !== '', function ($query) use ($search, $hasSubjectDayColumn) {
                $query->where(function ($innerQuery) use ($search, $hasSubjectDayColumn) {
                    if ($hasSubjectDayColumn) {
                        $innerQuery->where('day_of_week', 'like', '%' . $search . '%')
                            ->orWhere('period', 'like', '%' . $search . '%')
                            ->orWhere('start_time', 'like', '%' . $search . '%')
                            ->orWhere('end_time', 'like', '%' . $search . '%');
                    } else {
                        $innerQuery->where('period', 'like', '%' . $search . '%')
                            ->orWhere('start_time', 'like', '%' . $search . '%')
                            ->orWhere('end_time', 'like', '%' . $search . '%');
                    }

                    $innerQuery->orWhereHas('subject', function ($subjectQuery) use ($search) {
                        $subjectQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('code', 'like', '%' . $search . '%');
                    })->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                        $classQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('section', 'like', '%' . $search . '%')
                            ->orWhereRaw("CONCAT(name, ' - ', section) like ?", ['%' . $search . '%']);
                    })->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                        $teacherQuery->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('subject.teacher', function ($teacherQuery) use ($search) {
                        $teacherQuery->where('name', 'like', '%' . $search . '%');
                    });
                });
            })
            ->when($subjectId !== null, function ($query) use ($subjectId) {
                $query->where('subject_id', $subjectId);
            })
            ->when($teacherId !== null, function ($query) use ($teacherId, $hasSubjectTeacherColumn) {
                if ($hasSubjectTeacherColumn) {
                    $query->where('teacher_id', $teacherId);
                    return;
                }

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
            ->when(in_array($period, $allowedPeriods, true), function ($query) use ($period) {
                $query->where('period', $period);
            })
            ->when($day !== 'all' && $hasSubjectDayColumn, function ($query) use ($day) {
                $query->where(function ($innerQuery) use ($day) {
                    $innerQuery->where('day_of_week', $day)
                        ->orWhere('day_of_week', 'all');
                });
            })
            ->when($hasSubjectTeacherColumn, function ($query) {
                $query->orderBy('teacher_id');
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
            ->paginate($perPage, ['*'], 'teacher_page')
            ->withQueryString();

        $classes = SchoolClass::query()
            ->orderBy('name')
            ->orderBy('section')
            ->get(['id', 'name', 'section']);

        $subjects = Subject::query()
            ->with('schoolClass')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'school_class_id']);

        $teachers = User::query()
            ->where('role', 'teacher')
            ->orderBy('name')
            ->get(['id', 'name']);

        $periodOptions = $this->periodOptions();

        $subjectOptionsByClass = $subjects
            ->whereNotNull('school_class_id')
            ->groupBy(fn(Subject $subject) => (string) $subject->school_class_id)
            ->map(function ($group) {
                return $group->map(function (Subject $subject) {
                    return [
                        'id' => (int) $subject->id,
                        'label' => (string) $subject->name,
                        'school_class_id' => (int) $subject->school_class_id,
                    ];
                })->values()->all();
            })
            ->toArray();

        $subjectOptionsAll = $subjects
            ->map(function (Subject $subject) {
                return [
                    'id' => (int) $subject->id,
                    'label' => (string) $subject->name,
                    'school_class_id' => $subject->school_class_id !== null ? (int) $subject->school_class_id : null,
                ];
            })
            ->values()
            ->all();

        $teacherOptionsAll = $teachers
            ->map(function (User $teacher) {
                return [
                    'id' => (int) $teacher->id,
                    'label' => (string) $teacher->name,
                ];
            })
            ->values()
            ->all();

        $occupiedQuery = SubjectStudyTime::query()
            ->select([
                'subject_study_times.id as subject_study_time_id',
                'subject_study_times.period',
                'subject_study_times.start_time',
                'subject_study_times.end_time',
            ]);

        if ($hasSubjectClassColumn) {
            $occupiedQuery
                ->whereNotNull('subject_study_times.school_class_id')
                ->addSelect('subject_study_times.school_class_id');
        } else {
            $occupiedQuery
                ->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
                ->whereNotNull('subjects.school_class_id')
                ->addSelect('subjects.school_class_id');
        }

        if ($hasSubjectDayColumn) {
            $occupiedQuery->addSelect('subject_study_times.day_of_week');
        }

        $occupiedClassSlotsByClass = $occupiedQuery
            ->get()
            ->groupBy(fn($row) => (string) $row->school_class_id)
            ->map(function ($group) use ($useDayInSlotKey) {
                return $group
                    ->mapWithKeys(function ($row) use ($useDayInSlotKey) {
                        $period = strtolower((string) $row->period);
                        $start = (string) $row->start_time;
                        $end = (string) $row->end_time;
                        $day = strtolower((string) ($row->day_of_week ?? 'all'));

                        $slotKey = $useDayInSlotKey
                            ? $day . '|' . $period . '|' . $start . '|' . $end
                            : $period . '|' . $start . '|' . $end;

                        return [$slotKey => (int) $row->subject_study_time_id];
                    })
                    ->toArray();
            })
            ->toArray();

        $occupiedTeacherQuery = SubjectStudyTime::query()
            ->select([
                'subject_study_times.id as subject_study_time_id',
                'subject_study_times.period',
                'subject_study_times.start_time',
                'subject_study_times.end_time',
            ]);

        if ($hasSubjectTeacherColumn) {
            $occupiedTeacherQuery
                ->whereNotNull('subject_study_times.teacher_id')
                ->addSelect('subject_study_times.teacher_id');
        } else {
            $occupiedTeacherQuery
                ->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
                ->whereNotNull('subjects.teacher_id')
                ->addSelect('subjects.teacher_id');
        }

        if ($hasSubjectDayColumn) {
            $occupiedTeacherQuery->addSelect('subject_study_times.day_of_week');
        }

        $teacherBusySlots = $occupiedTeacherQuery
            ->get()
            ->map(function ($row) {
                return [
                    'subject_study_time_id' => (int) $row->subject_study_time_id,
                    'teacher_id' => (int) $row->teacher_id,
                    'day_of_week' => strtolower((string) ($row->day_of_week ?? 'all')),
                    'start_time' => substr((string) $row->start_time, 0, 5),
                    'end_time' => substr((string) $row->end_time, 0, 5),
                ];
            })
            ->values()
            ->all();

        $classTimeSelect = ['id', 'school_class_id', 'period', 'start_time', 'end_time'];
        if ($hasClassDayColumn) {
            $classTimeSelect[] = 'day_of_week';
        }

        $classTimeQuery = ClassStudyTime::query()
            ->select($classTimeSelect)
            ->orderBy('school_class_id');

        if ($hasClassDayColumn) {
            $classTimeQuery->orderByRaw("CASE day_of_week
                WHEN 'monday' THEN 1
                WHEN 'tuesday' THEN 2
                WHEN 'wednesday' THEN 3
                WHEN 'thursday' THEN 4
                WHEN 'friday' THEN 5
                WHEN 'saturday' THEN 6
                WHEN 'sunday' THEN 7
                ELSE 8 END");
        }

        $classTimeOptionsByClass = $classTimeQuery
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn(ClassStudyTime $slot) => (string) $slot->school_class_id)
            ->map(function ($group) use ($periodOptions, $useDayInSlotKey, $hasClassDayColumn) {
                return $group
                    ->map(function (ClassStudyTime $slot) use ($periodOptions, $useDayInSlotKey, $hasClassDayColumn) {
                        $period = strtolower((string) $slot->period);
                        $day = $hasClassDayColumn
                            ? strtolower((string) ($slot->day_of_week ?? 'all'))
                            : 'all';

                        $start = (string) $slot->start_time;
                        $end = (string) $slot->end_time;

                        $slotKey = $useDayInSlotKey
                            ? $day . '|' . $period . '|' . $start . '|' . $end
                            : $period . '|' . $start . '|' . $end;

                        $dayLabel = $day === 'all' ? 'All Days' : ucfirst($day);
                        $labelPrefix = $hasClassDayColumn ? ($dayLabel . ' | ') : '';

                        return [
                            'id' => (int) $slot->id,
                            'day_of_week' => $day,
                            'period' => $period,
                            'start_time' => substr((string) $slot->start_time, 0, 5),
                            'end_time' => substr((string) $slot->end_time, 0, 5),
                            'key' => $slotKey,
                            'label' => $labelPrefix
                                . ($periodOptions[$period] ?? ucfirst($period))
                                . ' | '
                                . Carbon::parse($slot->start_time)->format('h:i A')
                                . ' -> '
                                . Carbon::parse($slot->end_time)->format('h:i A'),
                        ];
                    })
                    ->values()
                    ->all();
            })
            ->toArray();

        $recentLimit = 2;

        $recentClassTimes = ClassStudyTime::query()
            ->with('schoolClass')
            ->latest('created_at')
            ->limit($recentLimit)
            ->get();

        $recentSubjectTimes = SubjectStudyTime::query()
            ->with(['subject.schoolClass', 'schoolClass', 'teacher', 'subject.teacher'])
            ->latest('created_at')
            ->limit($recentLimit)
            ->get();

        $recentTeacherTimes = SubjectStudyTime::query()
            ->with(['subject.schoolClass', 'schoolClass', 'teacher', 'subject.teacher'])
            ->where(function ($query) use ($hasSubjectTeacherColumn) {
                if ($hasSubjectTeacherColumn) {
                    $query->whereNotNull('teacher_id');
                }

                $query->orWhereHas('subject', function ($subjectQuery) {
                    $subjectQuery->whereNotNull('teacher_id');
                });
            })
            ->latest('created_at')
            ->limit($recentLimit)
            ->get();

        $stats = [
            'classSlots' => ClassStudyTime::query()->count(),
            'subjectSlots' => SubjectStudyTime::query()->count(),
            'classesWithSlots' => ClassStudyTime::query()->distinct('school_class_id')->count('school_class_id'),
            'subjectsWithSlots' => SubjectStudyTime::query()->distinct('subject_id')->count('subject_id'),
        ];

        return view('admin.time-studies', [
            'tab' => in_array($tab, ['class', 'subject', 'teacher'], true) ? $tab : 'class',
            'period' => in_array($period, array_merge(['all'], $allowedPeriods), true) ? $period : 'all',
            'day' => $day,
            'classId' => $classId !== null ? (string) $classId : 'all',
            'subjectId' => $subjectId !== null ? (string) $subjectId : 'all',
            'teacherId' => $teacherId !== null ? (string) $teacherId : 'all',
            'perPage' => $perPage,
            'perPageOptions' => $perPageOptions,
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'classTimes' => $classTimes,
            'subjectTimes' => $subjectTimes,
            'teacherTimes' => $teacherTimes,
            'stats' => $stats,
            'periodOptions' => $this->periodOptions(),
            'dayOptions' => $this->dayOptions(),
            'subjectOptionsByClass' => $subjectOptionsByClass,
            'subjectOptionsAll' => $subjectOptionsAll,
            'teacherOptionsAll' => $teacherOptionsAll,
            'classTimeOptionsByClass' => $classTimeOptionsByClass,
            'occupiedClassSlotsByClass' => $occupiedClassSlotsByClass,
            'teacherBusySlots' => $teacherBusySlots,
            'recentClassTimes' => $recentClassTimes,
            'recentSubjectTimes' => $recentSubjectTimes,
            'recentTeacherTimes' => $recentTeacherTimes,
            'search' => $search,
            'dayFeatureEnabled' => $hasClassDayColumn || $hasSubjectDayColumn,
            'hasClassDayColumn' => $hasClassDayColumn,
            'hasSubjectDayColumn' => $hasSubjectDayColumn,
        ]);
    }

    public function storeClass(Request $request)
    {
        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');

        $rawSlots = $request->input('class_slots');
        if (!is_array($rawSlots) || count($rawSlots) === 0) {
            $rawSlots = [[
                'day_of_week' => $request->input('day_of_week'),
                'period' => $request->input('period'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
            ]];
        }

        $request->merge(['class_slots' => $rawSlots]);

        $validated = $request->validate([
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'class_slots' => ['required', 'array', 'min:1'],
            'class_slots.*.day_of_week' => $hasClassDayColumn ? ['nullable', Rule::in($this->allowedDays())] : ['nullable'],
            'class_slots.*.period' => ['required', Rule::in($this->allowedPeriods())],
            'class_slots.*.start_time' => ['required', 'date_format:H:i'],
            'class_slots.*.end_time' => ['required', 'date_format:H:i'],
        ]);

        $targetClassIds = $this->resolveTargetClassIds($validated);
        if ($targetClassIds === []) {
            throw ValidationException::withMessages([
                'class_slots' => 'No classes available to apply this time slot.',
            ]);
        }

        $seenSlotKeys = [];
        $normalizedSlots = [];
        foreach ($validated['class_slots'] as $index => $slot) {
            $day = $hasClassDayColumn
                ? strtolower((string) ($slot['day_of_week'] ?? 'all'))
                : 'all';
            if (!in_array($day, $this->allowedDays(), true)) {
                $day = 'all';
            }

            $period = strtolower((string) ($slot['period'] ?? 'custom'));
            if (!in_array($period, $this->allowedPeriods(), true)) {
                $period = 'custom';
            }

            $startTime = (string) $slot['start_time'];
            $endTime = (string) $slot['end_time'];

            if ($endTime <= $startTime) {
                throw ValidationException::withMessages([
                    "class_slots.{$index}.end_time" => 'The end time must be after the start time.',
                ]);
            }

            $slotKey = ($hasClassDayColumn ? $day : 'all') . '|' . $period . '|' . $startTime . '|' . $endTime;
            if (isset($seenSlotKeys[$slotKey])) {
                throw ValidationException::withMessages([
                    "class_slots.{$index}.start_time" => 'Duplicate time slot in this form. Remove duplicates and try again.',
                ]);
            }

            $seenSlotKeys[$slotKey] = true;
            $normalizedSlots[] = [
                'day' => $day,
                'period' => $period,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        }

        $createdCount = 0;
        foreach ($targetClassIds as $classId) {
            $nextSortOrder = (int) ClassStudyTime::query()
                ->where('school_class_id', $classId)
                ->max('sort_order');

            foreach ($normalizedSlots as $slot) {
                if ($this->classSlotExists(
                    $classId,
                    (string) $slot['day'],
                    (string) $slot['period'],
                    (string) $slot['start_time'],
                    (string) $slot['end_time']
                )) {
                    continue;
                }

                $payload = [
                    'school_class_id' => $classId,
                    'period' => (string) $slot['period'],
                    'start_time' => (string) $slot['start_time'],
                    'end_time' => (string) $slot['end_time'],
                    'sort_order' => ++$nextSortOrder,
                ];

                if ($hasClassDayColumn) {
                    $payload['day_of_week'] = (string) $slot['day'];
                }

                ClassStudyTime::create($payload);
                $createdCount++;
            }
        }

        if ($createdCount === 0) {
            throw ValidationException::withMessages([
                'class_slots.0.start_time' => 'This date/time already exists for all classes.',
            ]);
        }

        foreach ($targetClassIds as $classId) {
            $this->syncClassLegacyStudyTime((int) $classId);
        }

        $successMessage = count($targetClassIds) > 1
            ? 'Class study time added for all classes successfully.'
            : 'Class study time added successfully.';

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'class'])
            ->with('success', $successMessage);
    }

    public function updateClass(Request $request, ClassStudyTime $classStudyTime)
    {
        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');

        $oldClassId = (int) $classStudyTime->school_class_id;
        $oldPeriod = strtolower((string) $classStudyTime->period);
        $oldStartTime = (string) $classStudyTime->start_time;
        $oldEndTime = (string) $classStudyTime->end_time;
        $oldDay = strtolower((string) ($classStudyTime->day_of_week ?? 'all'));

        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'day_of_week' => $hasClassDayColumn ? ['required', Rule::in($this->allowedDays())] : ['nullable'],
            'period' => ['required', Rule::in($this->allowedPeriods())],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $day = strtolower((string) ($validated['day_of_week'] ?? 'all'));

        if ($this->classSlotExists(
            (int) $validated['school_class_id'],
            $day,
            (string) $validated['period'],
            (string) $validated['start_time'],
            (string) $validated['end_time'],
            (int) $classStudyTime->id
        )) {
            throw ValidationException::withMessages([
                'start_time' => 'This class time already exists for the selected class.',
            ]);
        }

        $payload = [
            'school_class_id' => (int) $validated['school_class_id'],
            'period' => strtolower((string) $validated['period']),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ];

        if ($hasClassDayColumn) {
            $payload['day_of_week'] = $day;
        }

        $newClassId = (int) $validated['school_class_id'];
        $slotTimingChanged =
            $oldDay !== $day
            || $oldPeriod !== strtolower((string) $validated['period'])
            || $oldStartTime !== (string) $validated['start_time']
            || $oldEndTime !== (string) $validated['end_time'];

        if ($oldClassId === $newClassId && $slotTimingChanged) {
            $this->ensureTeacherAvailabilityForClassTimeChange(
                $oldClassId,
                $oldDay,
                $oldPeriod,
                $oldStartTime,
                $oldEndTime,
                $day,
                strtolower((string) $validated['period']),
                (string) $validated['start_time'],
                (string) $validated['end_time']
            );
        }

        $classStudyTime->update($payload);

        if ($oldClassId === $newClassId) {
            $this->syncSubjectSlotsForClassTimeUpdate(
                $oldClassId,
                $oldDay,
                $oldPeriod,
                $oldStartTime,
                $oldEndTime,
                $day,
                strtolower((string) $validated['period']),
                (string) $validated['start_time'],
                (string) $validated['end_time']
            );
        }

        $this->syncClassLegacyStudyTime($newClassId);
        if ($oldClassId !== $newClassId) {
            $this->syncClassLegacyStudyTime($oldClassId);
        }

        $this->syncSubjectLegacyStudyTimesForClass($oldClassId);
        if ($oldClassId !== $newClassId) {
            $this->syncSubjectLegacyStudyTimesForClass($newClassId);
        }

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'class'])
            ->with('success', 'Class study time updated successfully.');
    }

    public function destroyClass(ClassStudyTime $classStudyTime)
    {
        $classId = (int) $classStudyTime->school_class_id;

        $this->deleteSubjectSlotsForClassTime(
            $classId,
            strtolower((string) ($classStudyTime->day_of_week ?? 'all')),
            strtolower((string) $classStudyTime->period),
            (string) $classStudyTime->start_time,
            (string) $classStudyTime->end_time
        );

        $classStudyTime->delete();

        $this->syncClassLegacyStudyTime($classId);
        $this->syncSubjectLegacyStudyTimesForClass($classId);

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'class'])
            ->with('success', 'Class study time removed successfully.');
    }

    public function storeSubject(Request $request)
    {
        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasSubjectTeacherColumn = Schema::hasColumn('subject_study_times', 'teacher_id');

        $rawSubjectSlots = $request->input('subject_slots');
        if (!is_array($rawSubjectSlots) || count($rawSubjectSlots) === 0) {
            $rawSubjectSlots = [[
                'subject_id' => $request->input('subject_id'),
                'teacher_id' => $request->input('teacher_id'),
                'class_time_id' => $request->input('class_time_id'),
            ]];
        }
        $request->merge(['subject_slots' => $rawSubjectSlots]);

        $validated = $request->validate([
            'subject_class_id' => ['required', 'exists:school_classes,id'],
            'subject_slots' => ['required', 'array', 'min:1'],
            'subject_slots.*.subject_id' => [
                'required',
                Rule::exists('subjects', 'id'),
            ],
            'subject_slots.*.teacher_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'teacher');
                }),
            ],
            'subject_slots.*.class_time_id' => [
                'required',
                Rule::exists('class_study_times', 'id')->where(function ($query) use ($request) {
                    $query->where('school_class_id', (int) $request->input('subject_class_id'));
                }),
            ],
        ]);

        $classSlotSelect = ['id', 'period', 'start_time', 'end_time'];
        if ($hasClassDayColumn) {
            $classSlotSelect[] = 'day_of_week';
        }

        $classTimeIds = collect($validated['subject_slots'])
            ->pluck('class_time_id')
            ->map(fn($value) => (int) $value)
            ->filter(fn($value) => $value > 0)
            ->unique()
            ->values()
            ->all();

        $classSlotsById = ClassStudyTime::query()
            ->whereIn('id', $classTimeIds)
            ->get($classSlotSelect)
            ->keyBy('id');

        $classId = (int) $validated['subject_class_id'];
        [$createdCount, $touchedSubjectIds] = DB::transaction(function () use (
            $validated,
            $classSlotsById,
            $classId,
            $hasSubjectDayColumn,
            $hasSubjectClassColumn,
            $hasSubjectTeacherColumn
        ) {
            $createdCount = 0;
            $touchedSubjectIds = [];

            foreach ($validated['subject_slots'] as $index => $slotRow) {
                $classTimeId = (int) ($slotRow['class_time_id'] ?? 0);
                $classSlot = $classSlotsById->get($classTimeId);
                if (!$classSlot) {
                    throw ValidationException::withMessages([
                        "subject_slots.{$index}.class_time_id" => 'Invalid class time selected.',
                    ]);
                }

                $subject = $hasSubjectClassColumn
                    ? Subject::query()->findOrFail((int) ($slotRow['subject_id'] ?? 0))
                    : $this->resolveSubjectForClass((int) ($slotRow['subject_id'] ?? 0), $classId);

                $classSlotDay = strtolower((string) ($classSlot->day_of_week ?? 'all'));
                $slotPeriod = strtolower((string) $classSlot->period);
                $slotStart = (string) $classSlot->start_time;
                $slotEnd = (string) $classSlot->end_time;

                if ($this->subjectSlotTakenInClass($classId, $classSlotDay, $slotPeriod, $slotStart, $slotEnd)) {
                    throw ValidationException::withMessages([
                        "subject_slots.{$index}.class_time_id" => 'This class time is already assigned to a subject in the same class.',
                    ]);
                }

                $selectedTeacherId = isset($slotRow['teacher_id']) && $slotRow['teacher_id'] !== null && $slotRow['teacher_id'] !== ''
                    ? (int) $slotRow['teacher_id']
                    : null;

                if ($selectedTeacherId !== null && $this->teacherSlotTaken($selectedTeacherId, $classSlotDay, $slotStart, $slotEnd)) {
                    throw ValidationException::withMessages([
                        "subject_slots.{$index}.teacher_id" => 'This teacher already has another class at this time.',
                    ]);
                }

                $nextSortOrder = (int) SubjectStudyTime::query()
                    ->where('subject_id', (int) $subject->id)
                    ->max('sort_order');

                $payload = [
                    'subject_id' => (int) $subject->id,
                    'period' => $slotPeriod,
                    'start_time' => $slotStart,
                    'end_time' => $slotEnd,
                    'sort_order' => $nextSortOrder + 1,
                ];

                if ($hasSubjectDayColumn) {
                    $payload['day_of_week'] = $classSlotDay;
                }
                if ($hasSubjectClassColumn) {
                    $payload['school_class_id'] = $classId;
                }
                if ($hasSubjectTeacherColumn) {
                    $payload['teacher_id'] = $selectedTeacherId;
                }

                SubjectStudyTime::create($payload);
                $createdCount++;

                if (!$hasSubjectTeacherColumn) {
                    $subject->teacher_id = $selectedTeacherId;
                    $subject->save();
                }

                $touchedSubjectIds[$subject->id] = (int) $subject->id;
            }

            return [$createdCount, array_values($touchedSubjectIds)];
        });

        foreach ($touchedSubjectIds as $subjectId) {
            $this->syncSubjectLegacyStudyTime((int) $subjectId);
        }
        $this->syncClassLegacyStudyTime($classId);

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'subject'])
            ->with('success', $createdCount > 1 ? 'Subject study times added successfully.' : 'Subject study time added successfully.');
    }

    public function updateSubject(Request $request, SubjectStudyTime $subjectStudyTime)
    {
        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasSubjectTeacherColumn = Schema::hasColumn('subject_study_times', 'teacher_id');

        $validated = $request->validate([
            'subject_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => [
                'required',
                Rule::exists('subjects', 'id'),
            ],
            'teacher_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'teacher');
                }),
            ],
            'class_time_id' => [
                'required',
                Rule::exists('class_study_times', 'id')->where(function ($query) use ($request) {
                    $query->where('school_class_id', (int) $request->input('subject_class_id'));
                }),
            ],
        ]);

        $classSlotSelect = ['id', 'period', 'start_time', 'end_time'];
        if ($hasClassDayColumn) {
            $classSlotSelect[] = 'day_of_week';
        }

        $classSlot = ClassStudyTime::query()
            ->whereKey((int) $validated['class_time_id'])
            ->firstOrFail($classSlotSelect);

        $subject = $hasSubjectClassColumn
            ? Subject::query()->findOrFail((int) $validated['subject_id'])
            : $this->resolveSubjectForClass((int) $validated['subject_id'], (int) $validated['subject_class_id']);

        $classSlotDay = strtolower((string) ($classSlot->day_of_week ?? 'all'));

        if ($this->subjectSlotTakenInClass(
            (int) $validated['subject_class_id'],
            $classSlotDay,
            strtolower((string) $classSlot->period),
            (string) $classSlot->start_time,
            (string) $classSlot->end_time,
            (int) $subjectStudyTime->id
        )) {
            throw ValidationException::withMessages([
                'class_time_id' => 'This class time is already assigned to a subject in the same class.',
            ]);
        }

        $selectedTeacherId = isset($validated['teacher_id']) && $validated['teacher_id'] !== null && $validated['teacher_id'] !== ''
            ? (int) $validated['teacher_id']
            : null;

        if ($selectedTeacherId !== null && $this->teacherSlotTaken(
            $selectedTeacherId,
            $classSlotDay,
            (string) $classSlot->start_time,
            (string) $classSlot->end_time,
            (int) $subjectStudyTime->id
        )) {
            throw ValidationException::withMessages([
                'teacher_id' => 'This teacher already has another class at this time.',
            ]);
        }

        $newSubjectId = (int) $subject->id;
        $previousSubjectId = (int) $subjectStudyTime->subject_id;

        $payload = [
            'subject_id' => $newSubjectId,
            'period' => strtolower((string) $classSlot->period),
            'start_time' => $classSlot->start_time,
            'end_time' => $classSlot->end_time,
        ];

        if ($hasSubjectDayColumn) {
            $payload['day_of_week'] = $classSlotDay;
        }
        if ($hasSubjectClassColumn) {
            $payload['school_class_id'] = (int) $validated['subject_class_id'];
        }
        if ($hasSubjectTeacherColumn) {
            $payload['teacher_id'] = $selectedTeacherId;
        }

        if ($newSubjectId !== $previousSubjectId) {
            $nextSortOrder = (int) SubjectStudyTime::query()
                ->where('subject_id', $newSubjectId)
                ->max('sort_order');
            $payload['sort_order'] = $nextSortOrder + 1;
        }

        $subjectStudyTime->update($payload);

        if (!$hasSubjectTeacherColumn) {
            $subject->teacher_id = $selectedTeacherId;
            $subject->save();
        }

        $this->syncSubjectLegacyStudyTime($newSubjectId);
        if ($newSubjectId !== $previousSubjectId) {
            $this->syncSubjectLegacyStudyTime($previousSubjectId);
        }
        $this->syncClassLegacyStudyTime((int) $validated['subject_class_id']);

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'subject'])
            ->with('success', 'Subject study time updated successfully.');
    }

    public function destroySubject(SubjectStudyTime $subjectStudyTime)
    {
        $subjectId = (int) $subjectStudyTime->subject_id;
        $classId = (int) ($subjectStudyTime->school_class_id ?? 0);
        if ($classId <= 0) {
            $classId = (int) Subject::query()
                ->whereKey($subjectId)
                ->value('school_class_id');
        }

        $subjectStudyTime->delete();

        $this->syncSubjectLegacyStudyTime($subjectId);
        if ($classId > 0) {
            $this->syncClassLegacyStudyTime($classId);
        }

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'subject'])
            ->with('success', 'Subject study time removed successfully.');
    }

    private function allowedPeriods(): array
    {
        return ['morning', 'afternoon', 'evening', 'night', 'custom'];
    }

    private function allowedDays(): array
    {
        return ['all', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    }

    private function dayOptions(): array
    {
        return [
            'all' => 'All Days',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
    }

    private function periodOptions(): array
    {
        return [
            'morning' => 'Morning',
            'afternoon' => 'Afternoon',
            'evening' => 'Evening',
            'night' => 'Night',
            'custom' => 'Custom',
        ];
    }

    private function ensureTeacherAvailabilityForClassTimeChange(
        int $classId,
        string $oldDay,
        string $oldPeriod,
        string $oldStartTime,
        string $oldEndTime,
        string $newDay,
        string $newPeriod,
        string $newStartTime,
        string $newEndTime
    ): void {
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasSubjectTeacherColumn = Schema::hasColumn('subject_study_times', 'teacher_id');

        if (!$hasSubjectTeacherColumn) {
            return;
        }

        $query = SubjectStudyTime::query()
            ->where('subject_study_times.period', strtolower($oldPeriod))
            ->where('subject_study_times.start_time', $oldStartTime)
            ->where('subject_study_times.end_time', $oldEndTime)
            ->whereNotNull('subject_study_times.teacher_id')
            ->select(['subject_study_times.id', 'subject_study_times.teacher_id']);

        if ($hasSubjectClassColumn) {
            $query->where('subject_study_times.school_class_id', $classId);
        } else {
            $query->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
                ->where('subjects.school_class_id', $classId);
        }

        if ($hasSubjectDayColumn) {
            $query->where('subject_study_times.day_of_week', strtolower($oldDay));
        }

        $teacherSlots = $query->get();
        foreach ($teacherSlots as $teacherSlot) {
            $teacherId = (int) ($teacherSlot->teacher_id ?? 0);
            if ($teacherId <= 0) {
                continue;
            }

            if ($this->teacherSlotTaken(
                $teacherId,
                strtolower($newDay),
                $newStartTime,
                $newEndTime,
                (int) $teacherSlot->id
            )) {
                throw ValidationException::withMessages([
                    'start_time' => 'A teacher in this class slot already teaches another class at the selected time.',
                ]);
            }
        }
    }

    private function classSlotExists(int $classId, string $day, string $period, string $startTime, string $endTime, ?int $ignoreId = null): bool
    {
        $query = ClassStudyTime::query()
            ->where('school_class_id', $classId)
            ->where('period', strtolower($period))
            ->where('start_time', $startTime)
            ->where('end_time', $endTime);

        if (Schema::hasColumn('class_study_times', 'day_of_week')) {
            $normalizedDay = strtolower($day);
            $dayScope = $normalizedDay === 'all'
                ? $this->allowedDays()
                : [$normalizedDay, 'all'];

            $query->whereIn('day_of_week', $dayScope);
        }

        return $query
            ->when($ignoreId !== null, function ($innerQuery) use ($ignoreId) {
                $innerQuery->where('id', '!=', $ignoreId);
            })
            ->exists();
    }

    private function subjectSlotTakenInClass(int $classId, string $day, string $period, string $startTime, string $endTime, ?int $ignoreSubjectStudyTimeId = null): bool
    {
        $query = SubjectStudyTime::query()
            ->where('subject_study_times.period', strtolower($period))
            ->where('subject_study_times.start_time', $startTime)
            ->where('subject_study_times.end_time', $endTime);

        if (Schema::hasColumn('subject_study_times', 'school_class_id')) {
            $query->where('subject_study_times.school_class_id', $classId);
        } else {
            $query->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
                ->where('subjects.school_class_id', $classId);
        }

        if (Schema::hasColumn('subject_study_times', 'day_of_week')) {
            $normalizedDay = strtolower($day);
            $dayScope = $normalizedDay === 'all'
                ? $this->allowedDays()
                : [$normalizedDay, 'all'];

            $query->whereIn('subject_study_times.day_of_week', $dayScope);
        }

        return $query
            ->when($ignoreSubjectStudyTimeId !== null, function ($innerQuery) use ($ignoreSubjectStudyTimeId) {
                $innerQuery->where('subject_study_times.id', '!=', $ignoreSubjectStudyTimeId);
            })
            ->exists();
    }

    private function teacherSlotTaken(int $teacherId, string $day, string $startTime, string $endTime, ?int $ignoreSubjectStudyTimeId = null): bool
    {
        $query = SubjectStudyTime::query()
            ->where(function ($innerQuery) use ($startTime, $endTime) {
                $innerQuery
                    ->where('subject_study_times.start_time', '<', $endTime)
                    ->where('subject_study_times.end_time', '>', $startTime);
            });

        if (Schema::hasColumn('subject_study_times', 'teacher_id')) {
            $query->where('subject_study_times.teacher_id', $teacherId);
        } else {
            $query->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
                ->where('subjects.teacher_id', $teacherId);
        }

        if (Schema::hasColumn('subject_study_times', 'day_of_week')) {
            $normalizedDay = strtolower($day);
            $dayScope = $normalizedDay === 'all'
                ? $this->allowedDays()
                : [$normalizedDay, 'all'];

            $query->whereIn('subject_study_times.day_of_week', $dayScope);
        }

        return $query
            ->when($ignoreSubjectStudyTimeId !== null, function ($innerQuery) use ($ignoreSubjectStudyTimeId) {
                $innerQuery->where('subject_study_times.id', '!=', $ignoreSubjectStudyTimeId);
            })
            ->exists();
    }

    private function resolveSubjectForClass(int $subjectId, int $classId): Subject
    {
        return Subject::query()->findOrFail($subjectId);
    }

    private function syncSubjectSlotsForClassTimeUpdate(
        int $classId,
        string $oldDay,
        string $oldPeriod,
        string $oldStartTime,
        string $oldEndTime,
        string $newDay,
        string $newPeriod,
        string $newStartTime,
        string $newEndTime
    ): void {
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');

        $query = SubjectStudyTime::query()
            ->where('subject_study_times.period', strtolower($oldPeriod))
            ->where('subject_study_times.start_time', $oldStartTime)
            ->where('subject_study_times.end_time', $oldEndTime);

        if ($hasSubjectClassColumn) {
            $query->where('subject_study_times.school_class_id', $classId);
        } else {
            $query->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
                ->where('subjects.school_class_id', $classId);
        }

        if ($hasSubjectDayColumn) {
            $query->where('subject_study_times.day_of_week', strtolower($oldDay));
        }

        $ids = $query->pluck('subject_study_times.id');
        if ($ids->isEmpty()) {
            return;
        }

        $payload = [
            'period' => strtolower($newPeriod),
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
        ];

        if ($hasSubjectDayColumn) {
            $payload['day_of_week'] = strtolower($newDay);
        }

        SubjectStudyTime::query()
            ->whereIn('id', $ids->all())
            ->update($payload);
    }

    private function deleteSubjectSlotsForClassTime(
        int $classId,
        string $day,
        string $period,
        string $startTime,
        string $endTime
    ): void {
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = Schema::hasColumn('subject_study_times', 'school_class_id');

        $query = SubjectStudyTime::query()
            ->where('subject_study_times.period', strtolower($period))
            ->where('subject_study_times.start_time', $startTime)
            ->where('subject_study_times.end_time', $endTime);

        if ($hasSubjectClassColumn) {
            $query->where('subject_study_times.school_class_id', $classId);
        } else {
            $query->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
                ->where('subjects.school_class_id', $classId);
        }

        if ($hasSubjectDayColumn) {
            $query->where('subject_study_times.day_of_week', strtolower($day));
        }

        $ids = $query->pluck('subject_study_times.id');
        if ($ids->isEmpty()) {
            return;
        }

        SubjectStudyTime::query()
            ->whereIn('id', $ids->all())
            ->delete();
    }

    private function syncClassLegacyStudyTime(int $classId): void
    {
        if ($classId <= 0) {
            return;
        }

        $class = SchoolClass::query()->find($classId);
        if (!$class) {
            return;
        }

        $slot = ClassStudyTime::query()
            ->where('school_class_id', $classId)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->orderBy('id')
            ->first(['start_time', 'end_time']);

        if (!$slot) {
            $class->update([
                'study_time' => null,
                'study_start_time' => null,
                'study_end_time' => null,
            ]);
            return;
        }

        $start = substr((string) $slot->start_time, 0, 5);
        $end = substr((string) $slot->end_time, 0, 5);

        $class->update([
            'study_time' => $start,
            'study_start_time' => $start,
            'study_end_time' => $end,
        ]);
    }

    private function syncSubjectLegacyStudyTime(int $subjectId): void
    {
        if ($subjectId <= 0) {
            return;
        }

        $subject = Subject::query()->find($subjectId);
        if (!$subject) {
            return;
        }

        $slot = SubjectStudyTime::query()
            ->where('subject_id', $subjectId)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->orderBy('id')
            ->first(['start_time', 'end_time']);

        if (!$slot) {
            $subject->update([
                'study_time' => null,
                'study_start_time' => null,
                'study_end_time' => null,
            ]);
            return;
        }

        $start = substr((string) $slot->start_time, 0, 5);
        $end = substr((string) $slot->end_time, 0, 5);

        $subject->update([
            'study_time' => $start,
            'study_start_time' => $start,
            'study_end_time' => $end,
        ]);
    }

    private function syncSubjectLegacyStudyTimesForClass(int $classId): void
    {
        if ($classId <= 0) {
            return;
        }

        $subjectIds = Subject::query()
            ->where('school_class_id', $classId)
            ->pluck('id')
            ->all();

        foreach ($subjectIds as $subjectId) {
            $this->syncSubjectLegacyStudyTime((int) $subjectId);
        }
    }

    private function resolveTargetClassIds(array $validated): array
    {
        $selectedClassId = (int) ($validated['school_class_id'] ?? 0);
        if ($selectedClassId > 0) {
            return [$selectedClassId];
        }

        return SchoolClass::query()
            ->orderBy('id')
            ->pluck('id')
            ->map(static fn($id) => (int) $id)
            ->all();
    }
}
