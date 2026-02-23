<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $search = trim((string) $request->query('q', ''));

        $classId = ctype_digit($classIdRaw) ? (int) $classIdRaw : null;
        $subjectId = ctype_digit($subjectIdRaw) ? (int) $subjectIdRaw : null;

        $allowedPeriods = $this->allowedPeriods();
        $allowedDays = $this->allowedDays();
        $day = in_array($day, $allowedDays, true) ? $day : 'all';

        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');
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
            ->paginate(10, ['*'], 'class_page')
            ->withQueryString();

        $subjectTimes = SubjectStudyTime::query()
            ->with('subject.schoolClass')
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
                });
            })
            ->when($subjectId !== null, function ($query) use ($subjectId) {
                $query->where('subject_id', $subjectId);
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
            ->paginate(10, ['*'], 'subject_page')
            ->withQueryString();

        $classes = SchoolClass::query()
            ->orderBy('name')
            ->orderBy('section')
            ->get(['id', 'name', 'section']);

        $subjects = Subject::query()
            ->with('schoolClass')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'school_class_id']);

        $periodOptions = $this->periodOptions();

        $subjectOptionsByClass = $subjects
            ->whereNotNull('school_class_id')
            ->groupBy(fn(Subject $subject) => (string) $subject->school_class_id)
            ->map(function ($group) {
                return $group->map(function (Subject $subject) {
                    return [
                        'id' => (int) $subject->id,
                        'label' => $subject->name . ' (' . $subject->code . ')',
                        'school_class_id' => (int) $subject->school_class_id,
                    ];
                })->values()->all();
            })
            ->toArray();

        $subjectOptionsAll = $subjects
            ->map(function (Subject $subject) {
                return [
                    'id' => (int) $subject->id,
                    'label' => $subject->name . ' (' . $subject->code . ')',
                    'school_class_id' => $subject->school_class_id !== null ? (int) $subject->school_class_id : null,
                ];
            })
            ->values()
            ->all();

        $occupiedQuery = SubjectStudyTime::query()
            ->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
            ->whereNotNull('subjects.school_class_id')
            ->select([
                'subject_study_times.id as subject_study_time_id',
                'subjects.school_class_id',
                'subject_study_times.period',
                'subject_study_times.start_time',
                'subject_study_times.end_time',
            ]);

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

        $stats = [
            'classSlots' => ClassStudyTime::query()->count(),
            'subjectSlots' => SubjectStudyTime::query()->count(),
            'classesWithSlots' => ClassStudyTime::query()->distinct('school_class_id')->count('school_class_id'),
            'subjectsWithSlots' => SubjectStudyTime::query()->distinct('subject_id')->count('subject_id'),
        ];

        return view('admin.time-studies', [
            'tab' => in_array($tab, ['class', 'subject'], true) ? $tab : 'class',
            'period' => in_array($period, array_merge(['all'], $allowedPeriods), true) ? $period : 'all',
            'day' => $day,
            'classId' => $classId !== null ? (string) $classId : 'all',
            'subjectId' => $subjectId !== null ? (string) $subjectId : 'all',
            'classes' => $classes,
            'subjects' => $subjects,
            'classTimes' => $classTimes,
            'subjectTimes' => $subjectTimes,
            'stats' => $stats,
            'periodOptions' => $this->periodOptions(),
            'dayOptions' => $this->dayOptions(),
            'subjectOptionsByClass' => $subjectOptionsByClass,
            'subjectOptionsAll' => $subjectOptionsAll,
            'classTimeOptionsByClass' => $classTimeOptionsByClass,
            'occupiedClassSlotsByClass' => $occupiedClassSlotsByClass,
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
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'class_slots' => ['required', 'array', 'min:1'],
            'class_slots.*.day_of_week' => $hasClassDayColumn ? ['required', Rule::in($this->allowedDays())] : ['nullable'],
            'class_slots.*.period' => ['required', Rule::in($this->allowedPeriods())],
            'class_slots.*.start_time' => ['required', 'date_format:H:i'],
            'class_slots.*.end_time' => ['required', 'date_format:H:i'],
        ]);

        $classId = (int) $validated['school_class_id'];

        $nextSortOrder = (int) ClassStudyTime::query()
            ->where('school_class_id', $classId)
            ->max('sort_order');

        $seenSlotKeys = [];
        $payloads = [];
        foreach ($validated['class_slots'] as $index => $slot) {
            $day = strtolower((string) ($slot['day_of_week'] ?? 'all'));
            $period = strtolower((string) ($slot['period'] ?? 'custom'));
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

            if ($this->classSlotExists($classId, $day, $period, $startTime, $endTime)) {
                throw ValidationException::withMessages([
                    "class_slots.{$index}.start_time" => 'This class time already exists for the selected class.',
                ]);
            }

            $payload = [
                'school_class_id' => $classId,
                'period' => $period,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'sort_order' => ++$nextSortOrder,
            ];

            if ($hasClassDayColumn) {
                $payload['day_of_week'] = $day;
            }

            $seenSlotKeys[$slotKey] = true;
            $payloads[] = $payload;
        }

        foreach ($payloads as $payload) {
            ClassStudyTime::create($payload);
        }

        $successMessage = count($payloads) > 1
            ? 'Class study times added successfully.'
            : 'Class study time added successfully.';

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'class'])
            ->with('success', $successMessage);
    }

    public function updateClass(Request $request, ClassStudyTime $classStudyTime)
    {
        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');

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

        $classStudyTime->update($payload);

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'class'])
            ->with('success', 'Class study time updated successfully.');
    }

    public function destroyClass(ClassStudyTime $classStudyTime)
    {
        $classStudyTime->delete();

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'class'])
            ->with('success', 'Class study time removed successfully.');
    }

    public function storeSubject(Request $request)
    {
        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');

        $validated = $request->validate([
            'subject_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => [
                'required',
                Rule::exists('subjects', 'id')->where(function ($query) use ($request) {
                    $query->where('school_class_id', (int) $request->input('subject_class_id'));
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

        $classSlotDay = strtolower((string) ($classSlot->day_of_week ?? 'all'));

        if ($this->subjectSlotTakenInClass(
            (int) $validated['subject_class_id'],
            $classSlotDay,
            strtolower((string) $classSlot->period),
            (string) $classSlot->start_time,
            (string) $classSlot->end_time
        )) {
            throw ValidationException::withMessages([
                'class_time_id' => 'This class time is already assigned to a subject in the same class.',
            ]);
        }

        $nextSortOrder = (int) SubjectStudyTime::query()
            ->where('subject_id', (int) $validated['subject_id'])
            ->max('sort_order');

        $payload = [
            'subject_id' => (int) $validated['subject_id'],
            'period' => strtolower((string) $classSlot->period),
            'start_time' => $classSlot->start_time,
            'end_time' => $classSlot->end_time,
            'sort_order' => $nextSortOrder + 1,
        ];

        if ($hasSubjectDayColumn) {
            $payload['day_of_week'] = $classSlotDay;
        }

        SubjectStudyTime::create($payload);

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'subject'])
            ->with('success', 'Subject study time added successfully.');
    }

    public function updateSubject(Request $request, SubjectStudyTime $subjectStudyTime)
    {
        $hasClassDayColumn = Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = Schema::hasColumn('subject_study_times', 'day_of_week');

        $validated = $request->validate([
            'subject_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => [
                'required',
                Rule::exists('subjects', 'id')->where(function ($query) use ($request) {
                    $query->where('school_class_id', (int) $request->input('subject_class_id'));
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

        $newSubjectId = (int) $validated['subject_id'];
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

        if ($newSubjectId !== $previousSubjectId) {
            $nextSortOrder = (int) SubjectStudyTime::query()
                ->where('subject_id', $newSubjectId)
                ->max('sort_order');
            $payload['sort_order'] = $nextSortOrder + 1;
        }

        $subjectStudyTime->update($payload);

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'subject'])
            ->with('success', 'Subject study time updated successfully.');
    }

    public function destroySubject(SubjectStudyTime $subjectStudyTime)
    {
        $subjectStudyTime->delete();

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
            ->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
            ->where('subjects.school_class_id', $classId)
            ->where('subject_study_times.period', strtolower($period))
            ->where('subject_study_times.start_time', $startTime)
            ->where('subject_study_times.end_time', $endTime);

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
}
