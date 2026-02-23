<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TimeStudyController extends Controller
{
    public function index(Request $request)
    {
        $tab = (string) $request->query('tab', 'class');
        $period = (string) $request->query('period', 'all');
        $classIdRaw = (string) $request->query('class_id', 'all');
        $subjectIdRaw = (string) $request->query('subject_id', 'all');
        $search = trim((string) $request->query('q', ''));

        $classId = ctype_digit($classIdRaw) ? (int) $classIdRaw : null;
        $subjectId = ctype_digit($subjectIdRaw) ? (int) $subjectIdRaw : null;
        $allowedPeriods = $this->allowedPeriods();

        $classTimes = ClassStudyTime::query()
            ->with('schoolClass')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('period', 'like', '%' . $search . '%')
                        ->orWhere('start_time', 'like', '%' . $search . '%')
                        ->orWhere('end_time', 'like', '%' . $search . '%')
                        ->orWhereHas('schoolClass', function ($classQuery) use ($search) {
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
            ->orderBy('school_class_id')
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->paginate(10, ['*'], 'class_page')
            ->withQueryString();

        $subjectTimes = SubjectStudyTime::query()
            ->with('subject.schoolClass')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('period', 'like', '%' . $search . '%')
                        ->orWhere('start_time', 'like', '%' . $search . '%')
                        ->orWhere('end_time', 'like', '%' . $search . '%')
                        ->orWhereHas('subject', function ($subjectQuery) use ($search) {
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
            ->orderBy('subject_id')
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

        $occupiedClassSlotsByClass = SubjectStudyTime::query()
            ->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
            ->whereNotNull('subjects.school_class_id')
            ->select([
                'subject_study_times.id as subject_study_time_id',
                'subjects.school_class_id',
                'subject_study_times.period',
                'subject_study_times.start_time',
                'subject_study_times.end_time',
            ])
            ->get()
            ->groupBy(fn($row) => (string) $row->school_class_id)
            ->map(function ($group) {
                return $group
                    ->mapWithKeys(function ($row) {
                        $slotKey = strtolower((string) $row->period) . '|' . (string) $row->start_time . '|' . (string) $row->end_time;
                        return [$slotKey => (int) $row->subject_study_time_id];
                    })
                    ->toArray();
            })
            ->toArray();

        $classTimeOptionsByClass = ClassStudyTime::query()
            ->select(['id', 'school_class_id', 'period', 'start_time', 'end_time'])
            ->orderBy('school_class_id')
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn(ClassStudyTime $slot) => (string) $slot->school_class_id)
            ->map(function ($group) use ($periodOptions) {
                return $group
                    ->map(function (ClassStudyTime $slot) use ($periodOptions) {
                        $period = strtolower((string) $slot->period);
                        return [
                            'id' => (int) $slot->id,
                            'period' => $period,
                            'start_time' => substr((string) $slot->start_time, 0, 5),
                            'end_time' => substr((string) $slot->end_time, 0, 5),
                            'key' => $period . '|' . (string) $slot->start_time . '|' . (string) $slot->end_time,
                            'label' => ($periodOptions[$period] ?? ucfirst($period))
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
            'classId' => $classId !== null ? (string) $classId : 'all',
            'subjectId' => $subjectId !== null ? (string) $subjectId : 'all',
            'classes' => $classes,
            'subjects' => $subjects,
            'classTimes' => $classTimes,
            'subjectTimes' => $subjectTimes,
            'stats' => $stats,
            'periodOptions' => $this->periodOptions(),
            'subjectOptionsByClass' => $subjectOptionsByClass,
            'subjectOptionsAll' => $subjectOptionsAll,
            'classTimeOptionsByClass' => $classTimeOptionsByClass,
            'occupiedClassSlotsByClass' => $occupiedClassSlotsByClass,
            'search' => $search,
        ]);
    }

    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'period' => ['required', Rule::in($this->allowedPeriods())],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        if ($this->classSlotExists(
            (int) $validated['school_class_id'],
            (string) $validated['period'],
            (string) $validated['start_time'],
            (string) $validated['end_time']
        )) {
            throw ValidationException::withMessages([
                'start_time' => 'This class time already exists for the selected class.',
            ]);
        }

        $nextSortOrder = (int) ClassStudyTime::query()
            ->where('school_class_id', (int) $validated['school_class_id'])
            ->max('sort_order');

        ClassStudyTime::create([
            'school_class_id' => (int) $validated['school_class_id'],
            'period' => strtolower((string) $validated['period']),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'sort_order' => $nextSortOrder + 1,
        ]);

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'class'])
            ->with('success', 'Class study time added successfully.');
    }

    public function updateClass(Request $request, ClassStudyTime $classStudyTime)
    {
        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'period' => ['required', Rule::in($this->allowedPeriods())],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        if ($this->classSlotExists(
            (int) $validated['school_class_id'],
            (string) $validated['period'],
            (string) $validated['start_time'],
            (string) $validated['end_time'],
            (int) $classStudyTime->id
        )) {
            throw ValidationException::withMessages([
                'start_time' => 'This class time already exists for the selected class.',
            ]);
        }

        $classStudyTime->update([
            'school_class_id' => (int) $validated['school_class_id'],
            'period' => strtolower((string) $validated['period']),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

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

        $classSlot = ClassStudyTime::query()
            ->whereKey((int) $validated['class_time_id'])
            ->firstOrFail(['id', 'period', 'start_time', 'end_time']);

        if ($this->subjectSlotTakenInClass(
            (int) $validated['subject_class_id'],
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

        SubjectStudyTime::create([
            'subject_id' => (int) $validated['subject_id'],
            'period' => strtolower((string) $classSlot->period),
            'start_time' => $classSlot->start_time,
            'end_time' => $classSlot->end_time,
            'sort_order' => $nextSortOrder + 1,
        ]);

        return redirect()
            ->route('admin.time-studies.index', ['tab' => 'subject'])
            ->with('success', 'Subject study time added successfully.');
    }

    public function updateSubject(Request $request, SubjectStudyTime $subjectStudyTime)
    {
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

        $classSlot = ClassStudyTime::query()
            ->whereKey((int) $validated['class_time_id'])
            ->firstOrFail(['id', 'period', 'start_time', 'end_time']);

        if ($this->subjectSlotTakenInClass(
            (int) $validated['subject_class_id'],
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

    private function classSlotExists(int $classId, string $period, string $startTime, string $endTime, ?int $ignoreId = null): bool
    {
        return ClassStudyTime::query()
            ->where('school_class_id', $classId)
            ->where('period', strtolower($period))
            ->where('start_time', $startTime)
            ->where('end_time', $endTime)
            ->when($ignoreId !== null, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();
    }

    private function subjectSlotTakenInClass(int $classId, string $period, string $startTime, string $endTime, ?int $ignoreSubjectStudyTimeId = null): bool
    {
        return SubjectStudyTime::query()
            ->join('subjects', 'subjects.id', '=', 'subject_study_times.subject_id')
            ->where('subjects.school_class_id', $classId)
            ->where('subject_study_times.period', strtolower($period))
            ->where('subject_study_times.start_time', $startTime)
            ->where('subject_study_times.end_time', $endTime)
            ->when($ignoreSubjectStudyTimeId !== null, function ($query) use ($ignoreSubjectStudyTimeId) {
                $query->where('subject_study_times.id', '!=', $ignoreSubjectStudyTimeId);
            })
            ->exists();
    }
}
