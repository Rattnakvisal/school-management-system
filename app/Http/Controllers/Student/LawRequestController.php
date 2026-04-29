<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\StudentLawRequest;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LawRequestController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();
        $studentId = (int) ($student?->id ?? 0);
        $student->loadMissing('schoolClass:id,name,section');

        $lawTypes = $this->lawTypes();
        $subjectOptions = $this->studentSubjects($student);
        $subjectTimeOptionsBySubject = $this->studentSubjectTimes((int) ($student?->school_class_id ?? 0), $subjectOptions);

        $lawRequests = StudentLawRequest::query()
            ->where('student_id', $studentId)
            ->with('teacher:id,name')
            ->latest()
            ->get();

        $editingRequest = null;
        $editIdRaw = trim((string) $request->query('edit', ''));
        if (ctype_digit($editIdRaw)) {
            $editingRequest = $lawRequests->firstWhere('id', (int) $editIdRaw);
            if ($editingRequest && strtolower((string) ($editingRequest->status ?? 'pending')) !== 'pending') {
                return redirect()
                    ->route('student.law-requests.index')
                    ->with('warning', 'Approved or rejected requests can no longer be edited.');
            }
        }

        $selectionDefaults = $this->resolveSelectionFromRequest($editingRequest, $subjectOptions, $subjectTimeOptionsBySubject);
        $defaultSubjectId = old('subject_id', (string) ($selectionDefaults['subject_id'] ?? ''));
        $defaultTimeKeys = $this->normalizeSubjectTimeKeys(old('subject_time_keys', $selectionDefaults['subject_time_keys'] ?? []));
        $defaultRequestedFor = old('requested_for', $editingRequest?->requested_for?->toDateString() ?? now()->toDateString());
        $defaultRequestedUntil = old('requested_until', $editingRequest?->requested_until?->toDateString() ?? $defaultRequestedFor);
        $defaultReason = old('reason', (string) ($editingRequest?->reason ?? ''));
        $subjectTimeMap = $subjectTimeOptionsBySubject
            ->map(function ($items) {
                return collect($items)->map(function ($item) {
                    return [
                        'key' => (string) ($item['key'] ?? ''),
                        'label' => (string) ($item['label'] ?? ''),
                        'day_of_week' => (string) ($item['day_of_week'] ?? ''),
                        'period' => (string) ($item['period'] ?? ''),
                        'start_time' => (string) ($item['start_time'] ?? ''),
                        'end_time' => (string) ($item['end_time'] ?? ''),
                        'teacher_id' => (int) ($item['teacher_id'] ?? 0),
                        'teacher_name' => (string) ($item['teacher_name'] ?? ''),
                    ];
                })->values()->all();
            })
            ->toArray();
        $teacherRecipientsByTime = collect($subjectTimeMap)
            ->flatMap(function ($items) {
                return collect($items)->mapWithKeys(function ($item) {
                    $teacherId = (int) ($item['teacher_id'] ?? 0);
                    $teacherName = trim((string) ($item['teacher_name'] ?? ''));

                    return [
                        (string) ($item['key'] ?? '') => $teacherId > 0 && $teacherName !== ''
                            ? [[
                                'id' => $teacherId,
                                'name' => $teacherName,
                            ]]
                            : [],
                    ];
                });
            })
            ->toArray();
        $defaultTeacherRecipients = collect();
        foreach ($defaultTimeKeys as $timeKey) {
            foreach (($teacherRecipientsByTime[$timeKey] ?? []) as $teacherRow) {
                $teacherId = (int) ($teacherRow['id'] ?? 0);
                if ($teacherId <= 0) {
                    continue;
                }

                $defaultTeacherRecipients->put((string) $teacherId, (object) [
                    'id' => $teacherId,
                    'name' => (string) ($teacherRow['name'] ?? ''),
                ]);
            }
        }

        return view('student.law-requests', [
            'lawTypes' => $lawTypes,
            'lawRequests' => $lawRequests,
            'editingRequest' => $editingRequest,
            'classLabel' => $student->schoolClass?->display_name ?? 'No class assigned',
            'teacherRecipients' => $defaultTeacherRecipients->values(),
            'teacherRecipientsByTime' => $teacherRecipientsByTime,
            'subjectOptions' => $subjectOptions,
            'subjectTimeOptionsBySubject' => $subjectTimeMap,
            'formDefaults' => [
                'law_type' => old('law_type', (string) ($editingRequest?->law_type ?? array_key_first($lawTypes))),
                'subject_id' => $defaultSubjectId,
                'subject_time_keys' => $defaultTimeKeys,
                'requested_for' => $defaultRequestedFor,
                'requested_until' => $defaultRequestedUntil,
                'reason' => $defaultReason,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $student = $request->user();
        $studentId = (int) ($student?->id ?? 0);
        $schoolClassId = (int) ($student?->school_class_id ?? 0);

        if ($schoolClassId <= 0) {
            return back()->withInput()->with('error', 'You must be assigned to a class before submitting a law request.');
        }

        $resolved = $this->validateAndBuildPayload($request, $student, $this->lawTypes());

        $lawRequest = StudentLawRequest::query()->create(array_merge(
            [
                'student_id' => $studentId,
                'school_class_id' => $schoolClassId,
                'status' => 'pending',
            ],
            $resolved['payload']
        ));

        $this->notifyTeachersForLawRequest($student, $lawRequest, $resolved['teacher_ids'] ?? []);

        return redirect()
            ->route('student.law-requests.index')
            ->with('success', 'Law request submitted successfully.');
    }

    public function update(Request $request, StudentLawRequest $lawRequest)
    {
        $student = $request->user();
        $studentId = (int) ($student?->id ?? 0);
        if ((int) ($lawRequest->student_id ?? 0) !== $studentId) {
            abort(404);
        }

        if (strtolower((string) ($lawRequest->status ?? 'pending')) !== 'pending') {
            return redirect()
                ->route('student.law-requests.index')
                ->with('warning', 'Approved or rejected requests can no longer be edited.');
        }

        $resolved = $this->validateAndBuildPayload($request, $student, $this->lawTypes());
        $lawRequest->update($resolved['payload']);

        return redirect()
            ->route('student.law-requests.index')
            ->with('success', 'Law request updated successfully.');
    }

    public function destroy(Request $request, StudentLawRequest $lawRequest)
    {
        $studentId = (int) ($request->user()?->id ?? 0);
        if ((int) ($lawRequest->student_id ?? 0) !== $studentId) {
            abort(404);
        }

        if (strtolower((string) ($lawRequest->status ?? 'pending')) !== 'pending') {
            return redirect()
                ->route('student.law-requests.index')
                ->with('warning', 'Only pending law requests can be removed.');
        }

        $lawRequest->delete();

        return redirect()
            ->route('student.law-requests.index')
            ->with('success', 'Law request removed successfully.');
    }

    private function validateAndBuildPayload(Request $request, ?User $student, array $lawTypes): array
    {
        $subjectOptions = $this->studentSubjects($student);
        $subjectTimeOptionsBySubject = $this->studentSubjectTimes((int) ($student?->school_class_id ?? 0), $subjectOptions);

        $subjectKeys = $subjectOptions
            ->pluck('id')
            ->map(fn($id) => (string) (int) $id)
            ->filter(fn($id) => $id !== '')
            ->unique()
            ->values()
            ->all();

        $timeKeys = $subjectTimeOptionsBySubject
            ->flatMap(function ($items) {
                return collect($items)->pluck('key');
            })
            ->map(fn($key) => (string) $key)
            ->filter(fn($key) => $key !== '')
            ->unique()
            ->values()
            ->all();

        $selectedTimeKeys = $this->normalizeSubjectTimeKeys($request->input('subject_time_keys', $request->input('subject_time_key', [])));
        $request->merge([
            'subject_time_keys' => $selectedTimeKeys,
        ]);

        $validated = $request->validate([
            'law_type' => ['required', 'string', Rule::in(array_keys($lawTypes))],
            'subject_id' => ['required', 'string', Rule::in($subjectKeys)],
            'subject_time_keys' => ['required', 'array', 'min:1'],
            'subject_time_keys.*' => ['required', 'string', Rule::in($timeKeys)],
            'requested_for' => ['required', 'date'],
            'requested_until' => ['required', 'date', 'after_or_equal:requested_for'],
            'reason' => ['required', 'string', 'max:5000'],
        ]);

        $selectedSubjectKey = (string) ($validated['subject_id'] ?? '');
        $selectedTimeKeys = $this->normalizeSubjectTimeKeys($validated['subject_time_keys'] ?? []);
        $selectedSubject = $subjectOptions->firstWhere('id', (int) $selectedSubjectKey);
        if (!$selectedSubject) {
            throw ValidationException::withMessages([
                'subject_id' => 'Selected subject is invalid.',
            ]);
        }

        $selectedTimeOptions = collect($subjectTimeOptionsBySubject->get($selectedSubjectKey, collect()))->values();
        $selectedTimeItems = collect($selectedTimeKeys)->map(function ($timeKey) use ($selectedTimeOptions) {
            return $selectedTimeOptions->first(function ($item) use ($timeKey) {
                return (string) ($item['key'] ?? '') === $timeKey;
            });
        })->filter()->values();

        if ($selectedTimeItems->count() !== count($selectedTimeKeys)) {
            throw ValidationException::withMessages([
                'subject_time_keys' => 'Selected subject time is invalid.',
            ]);
        }

        $subjectText = $this->trimText((string) ($selectedSubject->name ?? ''), 150);
        $teacherIds = $selectedTimeItems
            ->map(fn($item) => (int) ($item['teacher_id'] ?? 0))
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
        $teacherId = count($teacherIds) === 1 ? (int) $teacherIds[0] : null;
        $subjectTimeLabels = $selectedTimeItems
            ->map(function ($item) {
                return trim((string) ($item['label'] ?? ''));
            })
            ->filter(fn($label) => $label !== '')
            ->values()
            ->all();
        $subjectTimeText = implode(', ', $subjectTimeLabels);
        $hasSubjectColumn = Schema::hasColumn('student_law_requests', 'subject');
        $hasSubjectTimeColumn = Schema::hasColumn('student_law_requests', 'subject_time');

        $payload = [
            'law_type' => $validated['law_type'],
            'requested_for' => $validated['requested_for'] ?? null,
            'reason' => trim((string) ($validated['reason'] ?? '')),
        ];

        if (Schema::hasColumn('student_law_requests', 'requested_until')) {
            $payload['requested_until'] = $validated['requested_until'] ?? ($validated['requested_for'] ?? null);
        }

        if (Schema::hasColumn('student_law_requests', 'subject_id')) {
            $payload['subject_id'] = (int) $selectedSubjectKey;
        }

        if (Schema::hasColumn('student_law_requests', 'teacher_id')) {
            $payload['teacher_id'] = $teacherId !== null && $teacherId > 0 ? $teacherId : null;
        }

        if ($hasSubjectColumn) {
            $payload['subject'] = $subjectText;
        }

        if ($hasSubjectTimeColumn) {
            $payload['subject_time'] = $subjectTimeText !== '' ? $subjectTimeText : null;
        }

        return [
            'payload' => $payload,
            'subject' => $subjectText,
            'subject_time' => $subjectTimeText,
            'subject_time_keys' => $selectedTimeKeys,
            'teacher_id' => $teacherId !== null && $teacherId > 0 ? $teacherId : null,
            'teacher_ids' => $teacherIds,
        ];
    }

    private function notifyTeachersForLawRequest(?User $student, StudentLawRequest $lawRequest, array $teacherIds = []): void
    {
        $teacherId = $this->resolveStoredTeacherId($lawRequest);
        $recipientIds = collect($teacherIds)
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0);

        if ($teacherId !== null) {
            $recipientIds->push($teacherId);
        }

        $recipientIds = $recipientIds->unique()->values()->all();
        $teacherRecipients = $recipientIds !== []
            ? User::query()
                ->where('role', 'teacher')
                ->whereIn('id', $recipientIds)
                ->get(['id', 'name', 'email'])
            : collect();
        if ($teacherRecipients->isEmpty()) {
            return;
        }

        $notificationDate = $lawRequest->requested_for?->toDateString() ?? now()->toDateString();
        $notificationSchedule = $this->buildRequestedForScheduleText($lawRequest);
        $subjectId = $this->resolveStoredSubjectId($lawRequest);
        $classLabel = $student?->schoolClass?->display_name
            ?? SchoolClass::query()->find((int) ($lawRequest->school_class_id ?? 0))?->display_name
            ?? 'Assigned class';
        $lawTypeLabel = $this->lawTypes()[$lawRequest->law_type] ?? ucfirst(str_replace('_', ' ', (string) $lawRequest->law_type));

        foreach ($teacherRecipients as $teacher) {
            $teacherId = (int) ($teacher->id ?? 0);
            if ($teacherId <= 0) {
                continue;
            }

            Notification::query()->create([
                'type' => 'student_law_request',
                'title' => 'New student law request',
                'message' => '[teacher_id:' . $teacherId . '] '
                    . ($student?->name ?? 'Student')
                    . ' submitted a '
                    . $lawTypeLabel
                    . ' request'
                    . ($notificationSchedule !== '' ? (' for ' . $notificationSchedule) : '')
                    . ' in '
                    . $classLabel
                    . '.',
                'url' => route('teacher.attendance.index', [
                    'class_id' => (int) ($lawRequest->school_class_id ?? 0),
                    'subject_id' => $subjectId,
                    'date' => $notificationDate,
                ]),
                'is_read' => false,
            ]);
        }
    }

    private function resolveSelectionFromRequest(?StudentLawRequest $lawRequest, $subjectOptions, $subjectTimeOptionsBySubject): array
    {
        if (!$lawRequest) {
            $firstSubject = collect($subjectOptions)->first();
            $subjectId = $firstSubject ? (string) (int) ($firstSubject->id ?? 0) : '';
            $firstTimeKey = (string) (collect($subjectTimeOptionsBySubject->get($subjectId, collect()))->first()['key'] ?? '');

            return [
                'subject_id' => $subjectId,
                'subject_time_keys' => $firstTimeKey !== '' ? [$firstTimeKey] : [],
            ];
        }

        [$subjectText, $subjectTimeText] = $this->extractSubjectAndTimeTexts($lawRequest);
        $matchedSubject = collect($subjectOptions)->first(function ($subject) use ($subjectText) {
            return strcasecmp(trim((string) ($subject->name ?? '')), $subjectText) === 0;
        });

        if (!$matchedSubject) {
            $firstSubject = collect($subjectOptions)->first();
            $subjectId = $firstSubject ? (string) (int) ($firstSubject->id ?? 0) : '';
            $firstTimeKey = (string) (collect($subjectTimeOptionsBySubject->get($subjectId, collect()))->first()['key'] ?? '');

            return [
                'subject_id' => $subjectId,
                'subject_time_keys' => $firstTimeKey !== '' ? [$firstTimeKey] : [],
            ];
        }

        $subjectId = (string) (int) ($matchedSubject->id ?? 0);
        $options = collect($subjectTimeOptionsBySubject->get($subjectId, collect()))->values();
        $timeKeys = $this->resolveSubjectTimeKeysFromStoredText($subjectTimeText, $options);

        if ($timeKeys === []) {
            $firstTimeKey = (string) (($options->first()['key'] ?? ''));
            if ($firstTimeKey !== '') {
                $timeKeys = [$firstTimeKey];
            }
        }

        return [
            'subject_id' => $subjectId,
            'subject_time_keys' => array_values(array_filter($timeKeys, fn($key) => $key !== '')),
        ];
    }

    private function extractSubjectAndTimeTexts(StudentLawRequest $lawRequest): array
    {
        return [
            trim((string) ($lawRequest->subject ?? '')),
            trim((string) ($lawRequest->subject_time ?? '')),
        ];
    }

    private function resolveSubjectTimeKeysFromStoredText(string $subjectTimeText, $subjectTimeOptions): array
    {
        $storedValues = $this->splitStoredSubjectTimeValues($subjectTimeText);
        if ($storedValues === []) {
            return [];
        }

        $resolved = [];
        foreach ($storedValues as $storedValue) {
            $matchedTime = collect($subjectTimeOptions)->first(function ($item) use ($storedValue) {
                $label = trim((string) ($item['label'] ?? ''));
                $key = trim((string) ($item['key'] ?? ''));

                return strcasecmp($label, $storedValue) === 0 || strcasecmp($key, $storedValue) === 0;
            });

            if ($matchedTime) {
                $resolved[] = (string) ($matchedTime['key'] ?? '');
            }
        }

        return array_values(array_unique(array_filter($resolved, fn($key) => $key !== '')));
    }

    private function splitStoredSubjectTimeValues(string $subjectTimeText): array
    {
        $clean = trim($subjectTimeText);
        if ($clean === '') {
            return [];
        }

        $parts = preg_split('/\s*,\s*/', $clean);
        if (!is_array($parts)) {
            return [$clean];
        }

        return array_values(array_filter(array_map('trim', $parts), fn($value) => $value !== ''));
    }

    private function normalizeSubjectTimeKeys(mixed $value): array
    {
        if (is_string($value)) {
            $value = trim($value);
            return $value !== '' ? [$value] : [];
        }

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(function ($item) {
            return trim((string) $item);
        }, $value), fn($item) => $item !== '')));
    }

    private function buildRequestedForScheduleText(StudentLawRequest $lawRequest): string
    {
        $requestedForText = $this->formatRequestedDateRange($lawRequest);
        $subjectText = trim((string) ($lawRequest->subject ?? ''));
        $subjectTimeText = trim((string) ($lawRequest->subject_time ?? ''));

        $parts = array_filter([
            $subjectText !== '' ? $subjectText : null,
            $requestedForText !== '' ? $requestedForText : null,
            $subjectTimeText !== '' ? $subjectTimeText : null,
        ]);

        return trim(implode(' | ', $parts));
    }

    private function formatRequestedDateRange(StudentLawRequest $lawRequest): string
    {
        $from = $lawRequest->requested_for ? Carbon::parse($lawRequest->requested_for)->format('M d, Y') : '';
        $until = $lawRequest->requested_until ? Carbon::parse($lawRequest->requested_until)->format('M d, Y') : '';

        if ($from === '') {
            return $until;
        }

        if ($until === '' || $until === $from) {
            return $from;
        }

        return $from . ' - ' . $until;
    }

    private function resolveStoredSubjectId(StudentLawRequest $lawRequest): ?int
    {
        if (Schema::hasColumn('student_law_requests', 'subject_id')) {
            $subjectId = (int) ($lawRequest->subject_id ?? 0);
            if ($subjectId > 0) {
                return $subjectId;
            }
        }

        $subjectName = trim((string) ($lawRequest->subject ?? ''));
        if ($subjectName === '') {
            return null;
        }

        return Subject::query()
            ->where('school_class_id', (int) ($lawRequest->school_class_id ?? 0))
            ->where('name', $subjectName)
            ->value('id');
    }

    private function resolveStoredTeacherId(StudentLawRequest $lawRequest): ?int
    {
        if (Schema::hasColumn('student_law_requests', 'teacher_id')) {
            $teacherId = (int) ($lawRequest->teacher_id ?? 0);
            if ($teacherId > 0) {
                return $teacherId;
            }
        }

        return null;
    }

    private function studentSubjects(?User $student)
    {
        $classId = (int) ($student?->school_class_id ?? 0);
        if ($classId <= 0) {
            return collect();
        }

        $hasSubjectStatusColumn = Schema::hasColumn('subjects', 'is_active');

        $classSubjectsQuery = Subject::query()
            ->leftJoin('school_classes as classes', 'classes.id', '=', 'subjects.school_class_id')
            ->leftJoin('users as teachers', 'teachers.id', '=', 'subjects.teacher_id')
            ->select([
                'subjects.id',
                'subjects.name',
                'subjects.code',
                'subjects.teacher_id',
                'subjects.study_time',
                'subjects.study_start_time',
                'subjects.study_end_time',
                'subjects.school_class_id',
                'classes.name as class_name',
                'classes.section as class_section',
                'teachers.name as teacher_name',
            ])
            ->where('subjects.school_class_id', $classId);

        if ($hasSubjectStatusColumn) {
            $classSubjectsQuery->where('subjects.is_active', true);
        }

        $subjects = $classSubjectsQuery
            ->orderBy('subjects.name')
            ->get();

        if ($student && Schema::hasTable('student_major_subjects')) {
            $majorSubjectsQuery = $student->majorSubjects()
                ->leftJoin('school_classes as classes', 'classes.id', '=', 'subjects.school_class_id')
                ->leftJoin('users as teachers', 'teachers.id', '=', 'subjects.teacher_id')
                ->select([
                    'subjects.id',
                    'subjects.name',
                    'subjects.code',
                    'subjects.teacher_id',
                    'subjects.study_time',
                    'subjects.study_start_time',
                    'subjects.study_end_time',
                    'subjects.school_class_id',
                    'classes.name as class_name',
                    'classes.section as class_section',
                    'teachers.name as teacher_name',
                ]);

            if ($hasSubjectStatusColumn) {
                $majorSubjectsQuery->where('subjects.is_active', true);
            }

            $subjects = $subjects
                ->concat($majorSubjectsQuery->get())
                ->unique('id')
                ->values();
        }

        return $subjects
            ->sortBy(function ($subject) {
                return strtolower(trim((string) ($subject->name ?? '')));
            })
            ->values();
    }

    private function studentSubjectTimes(int $classId, $subjectOptions)
    {
        $subjectRows = collect($subjectOptions)->values();
        $subjectIds = $subjectRows->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $grouped = collect($subjectIds)->mapWithKeys(function ($subjectId) {
            return [(string) $subjectId => collect()];
        });

        if ($classId <= 0 || $subjectIds === []) {
            return $grouped->map(fn($items) => collect($items)->values());
        }

        $hasSlotTable = Schema::hasTable('subject_study_times');
        $hasClassColumn = $hasSlotTable && Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasDayColumn = $hasSlotTable && Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasTeacherColumn = $hasSlotTable && Schema::hasColumn('subject_study_times', 'teacher_id');

        if ($hasSlotTable) {
            $daySelect = $hasDayColumn
                ? 'sst.day_of_week as day_of_week'
                : DB::raw("'all' as day_of_week");
            $teacherSelect = $hasTeacherColumn
                ? 'sst.teacher_id as teacher_id'
                : DB::raw('NULL as teacher_id');

            $slotQuery = DB::table('subject_study_times as sst')
                ->join('subjects', 'subjects.id', '=', 'sst.subject_id')
                ->leftJoin('users as teachers', 'teachers.id', '=', 'sst.teacher_id')
                ->whereIn('sst.subject_id', $subjectIds)
                ->select([
                    'sst.id',
                    'sst.subject_id',
                    'sst.period',
                    'sst.start_time',
                    'sst.end_time',
                    'sst.sort_order',
                    $daySelect,
                    $teacherSelect,
                    'teachers.name as teacher_name',
                ]);

            if ($hasClassColumn) {
                $slotQuery->where(function ($query) use ($classId) {
                    $query->where('sst.school_class_id', $classId)
                        ->orWhereNull('sst.school_class_id');
                });
            }

            if ($hasDayColumn) {
                $slotQuery->orderByRaw("CASE sst.day_of_week
                    WHEN 'monday' THEN 1
                    WHEN 'tuesday' THEN 2
                    WHEN 'wednesday' THEN 3
                    WHEN 'thursday' THEN 4
                    WHEN 'friday' THEN 5
                    WHEN 'saturday' THEN 6
                    WHEN 'sunday' THEN 7
                    ELSE 8 END");
            }

            $slots = $slotQuery
                ->orderBy('sst.subject_id')
                ->orderBy('sst.sort_order')
                ->orderBy('sst.start_time')
                ->get();

            foreach ($slots as $slot) {
                $subjectId = (int) ($slot->subject_id ?? 0);
                if ($subjectId <= 0) {
                    continue;
                }

                $grouped[(string) $subjectId]->push([
                    'key' => 'slot:' . (int) $slot->id,
                    'subject_id' => $subjectId,
                    'label' => $this->formatSubjectSlotLabel(
                        (string) ($slot->day_of_week ?? 'all'),
                        (string) ($slot->period ?? 'custom'),
                        (string) ($slot->start_time ?? ''),
                        (string) ($slot->end_time ?? '')
                    ),
                    'day_of_week' => (string) ($slot->day_of_week ?? 'all'),
                    'period' => (string) ($slot->period ?? 'custom'),
                    'start_time' => (string) ($slot->start_time ?? ''),
                    'end_time' => (string) ($slot->end_time ?? ''),
                    'teacher_id' => (int) ($slot->teacher_id ?? 0),
                    'teacher_name' => (string) ($slot->teacher_name ?? ''),
                ]);
            }
        }

        foreach ($subjectRows as $subject) {
            $subjectId = (int) ($subject->id ?? 0);
            if ($subjectId <= 0) {
                continue;
            }

            $subjectKey = (string) $subjectId;
            if (($grouped[$subjectKey] ?? collect())->isNotEmpty()) {
                continue;
            }

            $grouped[$subjectKey] = collect([[
                'key' => 'subject:' . $subjectId,
                'subject_id' => $subjectId,
                'label' => $this->formatFallbackSubjectTimeLabel(
                    (string) ($subject->study_time ?? ''),
                    (string) ($subject->study_start_time ?? ''),
                    (string) ($subject->study_end_time ?? '')
                ),
                'day_of_week' => 'all',
                'period' => 'custom',
                'start_time' => '',
                'end_time' => '',
                'teacher_id' => (int) ($subject->teacher_id ?? 0),
                'teacher_name' => (string) ($subject->teacher_name ?? ''),
            ]]);
        }

        return $grouped->map(fn($items) => collect($items)->values());
    }

    private function formatSubjectSlotLabel(string $dayKey, string $periodKey, string $startTime, string $endTime): string
    {
        $dayLabels = [
            'all' => 'All Days',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
        $periodLabels = [
            'morning' => 'Morning',
            'afternoon' => 'Afternoon',
            'evening' => 'Evening',
            'night' => 'Night',
            'custom' => 'Custom',
        ];

        $day = strtolower(trim($dayKey));
        $period = strtolower(trim($periodKey));
        $dayLabel = $dayLabels[$day] ?? ucfirst($day !== '' ? $day : 'all');
        $periodLabel = $periodLabels[$period] ?? ucfirst($period !== '' ? $period : 'custom');
        $start = $this->formatClock($startTime);
        $end = $this->formatClock($endTime);

        return $dayLabel . ' | ' . $periodLabel . ' ' . $start . '-' . $end;
    }

    private function formatFallbackSubjectTimeLabel(string $studyTime, string $startTime, string $endTime): string
    {
        $start = $this->formatClock($startTime);
        $end = $this->formatClock($endTime);
        if ($start !== '--' && $end !== '--') {
            return 'Custom ' . $start . '-' . $end;
        }

        $legacy = trim($studyTime);
        if ($legacy !== '') {
            return 'Time ' . $legacy;
        }

        return 'No fixed time';
    }

    private function formatClock(string $value): string
    {
        $clean = trim($value);
        if ($clean === '') {
            return '--';
        }

        try {
            return Carbon::parse($clean)->format('h:i A');
        } catch (\Throwable) {
            return $clean;
        }
    }

    private function trimText(string $value, int $max): string
    {
        $clean = trim($value);
        if ($max <= 0 || strlen($clean) <= $max) {
            return $clean;
        }

        return rtrim(substr($clean, 0, $max));
    }

    private function lawTypes(): array
    {
        return [
            'medical_leave' => 'Medical Leave',
            'family_emergency' => 'Family Emergency',
            'official_activity' => 'Official Activity',
            'transport_issue' => 'Transport Issue',
            'other' => 'Other',
        ];
    }
}
