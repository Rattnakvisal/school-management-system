<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Subject;
use App\Models\TeacherLawRequest;
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
        $teacherId = (int) ($request->user()?->id ?? 0);
        $lawTypes = $this->lawTypes();
        $subjectOptions = $this->teacherSubjects($teacherId);
        $subjectTimeOptionsBySubject = $this->teacherSubjectTimes($teacherId, $subjectOptions);

        $lawRequests = TeacherLawRequest::query()
            ->where('teacher_id', $teacherId)
            ->latest()
            ->get();

        $editingRequest = null;
        $editIdRaw = trim((string) $request->query('edit', ''));
        if (ctype_digit($editIdRaw)) {
            $editingRequest = $lawRequests->firstWhere('id', (int) $editIdRaw);
            if ($editingRequest && strtolower((string) ($editingRequest->status ?? 'pending')) !== 'pending') {
                return redirect()
                    ->route('teacher.law-requests.index')
                    ->with('warning', 'Approved or rejected requests can no longer be edited.');
            }
        }

        $selectionDefaults = $this->resolveSelectionFromRequest($editingRequest, $subjectOptions, $subjectTimeOptionsBySubject);

        $defaultLawType = old('law_type', (string) ($editingRequest?->law_type ?? array_key_first($lawTypes)));
        $defaultSubjectId = old('subject_id', (string) ($selectionDefaults['subject_id'] ?? 'all'));
        $defaultTimeKeys = $this->normalizeSubjectTimeKeys(old('subject_time_keys', $editingRequest ? ($selectionDefaults['subject_time_keys'] ?? []) : []));
        $defaultRequestedFor = old('requested_for', $editingRequest?->requested_for?->toDateString() ?? '');
        $defaultReason = old('reason', (string) ($editingRequest?->reason ?? ''));

        if ($defaultSubjectId === '') {
            $defaultSubjectId = 'all';
        }
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
                    ];
                })->values()->all();
            })
            ->toArray();

        $approvalAlertNotifications = Notification::query()
            ->where('type', 'teacher_law_request_approved')
            ->where('is_read', false)
            ->where('message', 'like', '%[teacher_id:' . $teacherId . ']%')
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'message']);

        $approvalAlerts = $approvalAlertNotifications
            ->map(function ($notification) {
                return [
                    'title' => trim((string) ($notification->title ?? 'Law request approved')),
                    'text' => $this->cleanTeacherNotificationText((string) ($notification->message ?? '')),
                ];
            })
            ->values()
            ->all();

        if ($approvalAlertNotifications->isNotEmpty()) {
            Notification::query()
                ->whereIn('id', $approvalAlertNotifications->pluck('id')->all())
                ->update(['is_read' => true]);
        }

        return view('teacher.law-requests', [
            'lawTypes' => $lawTypes,
            'lawRequests' => $lawRequests,
            'subjectOptions' => $subjectOptions,
            'subjectTimeOptionsBySubject' => $subjectTimeMap,
            'editingRequest' => $editingRequest,
            'approvalAlerts' => $approvalAlerts,
            'formDefaults' => [
                'law_type' => $defaultLawType,
                'subject_id' => $defaultSubjectId,
                'subject_time_keys' => $defaultTimeKeys,
                'requested_for' => $defaultRequestedFor,
                'reason' => $defaultReason,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $teacher = $request->user();
        $teacherId = (int) ($teacher?->id ?? 0);
        $resolved = $this->validateAndBuildPayload($request, $teacherId, $this->lawTypes());

        $lawRequest = TeacherLawRequest::query()->create(array_merge(
            [
                'teacher_id' => $teacherId,
                'status' => 'pending',
            ],
            $resolved['payload']
        ));

        $notificationDate = $lawRequest->requested_for
            ? $lawRequest->requested_for->toDateString()
            : now()->toDateString();
        $requestedForText = $lawRequest->requested_for
            ? Carbon::parse($lawRequest->requested_for)->format('M d, Y')
            : '';
        $notificationSchedule = $this->buildRequestedForScheduleText($lawRequest);

        $notification = new Notification([
            'type' => 'teacher_law_request',
            'title' => 'New teacher law request',
            'message' => ($teacher?->name ?? 'Teacher')
                . ' submitted a law request'
                . ($notificationSchedule !== '' ? ' for ' . $notificationSchedule : ($requestedForText !== '' ? ' for ' . $requestedForText : '.')),
            'url' => route('admin.attendance.teachers.index', ['date' => $notificationDate]),
            'is_read' => false,
        ]);
        $notification->save();

        return redirect()
            ->route('teacher.law-requests.index')
            ->with('success', 'Law request submitted successfully.');
    }

    public function update(Request $request, TeacherLawRequest $lawRequest)
    {
        $teacherId = (int) ($request->user()?->id ?? 0);
        if ((int) ($lawRequest->teacher_id ?? 0) !== $teacherId) {
            abort(404);
        }

        if (strtolower((string) ($lawRequest->status ?? 'pending')) !== 'pending') {
            return redirect()
                ->route('teacher.law-requests.index')
                ->with('warning', 'Approved or rejected requests can no longer be edited.');
        }

        $resolved = $this->validateAndBuildPayload($request, $teacherId, $this->lawTypes());
        $lawRequest->update($resolved['payload']);

        return redirect()
            ->route('teacher.law-requests.index')
            ->with('success', 'Law request updated successfully.');
    }

    public function destroy(Request $request, TeacherLawRequest $lawRequest)
    {
        $teacherId = (int) ($request->user()?->id ?? 0);
        if ((int) ($lawRequest->teacher_id ?? 0) !== $teacherId) {
            abort(404);
        }

        $lawRequest->delete();

        return redirect()
            ->route('teacher.law-requests.index')
            ->with('success', 'Law request removed successfully.');
    }

    private function validateAndBuildPayload(Request $request, int $teacherId, array $lawTypes): array
    {
        $subjectOptions = $this->teacherSubjects($teacherId);
        $subjectTimeOptionsBySubject = $this->teacherSubjectTimes($teacherId, $subjectOptions);

        $subjectKeys = $subjectOptions
            ->pluck('id')
            ->map(fn($id) => (string) (int) $id)
            ->filter(fn($id) => $id !== '')
            ->unique()
            ->values()
            ->all();
        $subjectKeys[] = 'all';
        $subjectKeys = array_values(array_unique($subjectKeys));

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
            'requested_for' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'max:5000'],
        ]);

        $selectedSubjectKey = (string) ($validated['subject_id'] ?? '');
        $selectedTimeKeys = $this->normalizeSubjectTimeKeys($validated['subject_time_keys'] ?? []);
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

        if ($selectedSubjectKey === 'all' && $selectedTimeKeys !== ['all:all']) {
            throw ValidationException::withMessages([
                'subject_time_keys' => 'All Subjects must use All Times.',
            ]);
        }

        $subjectText = 'All Subjects';
        if ($selectedSubjectKey !== 'all') {
            $selectedSubject = $subjectOptions->firstWhere('id', (int) $selectedSubjectKey);
            if (!$selectedSubject) {
                throw ValidationException::withMessages([
                    'subject_id' => 'Selected subject is invalid.',
                ]);
            }

            $subjectText = $this->trimText((string) ($selectedSubject->name ?? ''), 150);
        }

        $subjectTimeLabels = $selectedTimeItems
            ->map(function ($item) {
                return trim((string) ($item['label'] ?? ''));
            })
            ->filter(fn($label) => $label !== '')
            ->values()
            ->all();
        $subjectTimeText = implode(', ', $subjectTimeLabels);
        $hasSubjectTimeColumn = Schema::hasColumn('teacher_law_requests', 'subject_time');

        $payload = [
            'law_type' => $validated['law_type'],
            'subject' => $subjectText,
            'requested_for' => $validated['requested_for'] ?? null,
            'reason' => trim((string) ($validated['reason'] ?? '')),
        ];
        if ($hasSubjectTimeColumn) {
            $payload['subject_time'] = $subjectTimeText !== '' ? $subjectTimeText : null;
        }

        return [
            'payload' => $payload,
            'subject' => $subjectText,
            'subject_time' => $subjectTimeText,
            'subject_time_keys' => $selectedTimeKeys,
        ];
    }

    private function resolveSelectionFromRequest(?TeacherLawRequest $lawRequest, $subjectOptions, $subjectTimeOptionsBySubject): array
    {
        if (!$lawRequest) {
            return [
                'subject_id' => 'all',
                'subject_time_keys' => ['all:all'],
            ];
        }

        [$subjectText, $subjectTimeText] = $this->extractSubjectAndTimeTexts($lawRequest);
        if (strcasecmp($subjectText, 'All Subjects') === 0) {
            return [
                'subject_id' => 'all',
                'subject_time_keys' => ['all:all'],
            ];
        }

        $matchedSubject = collect($subjectOptions)->first(function ($subject) use ($subjectText) {
            return strcasecmp(trim((string) ($subject->name ?? '')), $subjectText) === 0;
        });

        if (!$matchedSubject) {
            return [
                'subject_id' => 'all',
                'subject_time_keys' => ['all:all'],
            ];
        }

        $subjectId = (string) (int) ($matchedSubject->id ?? 0);
        $options = collect($subjectTimeOptionsBySubject->get($subjectId, collect()))->values();
        $timeKeys = $this->resolveSubjectTimeKeysFromStoredText($subjectTimeText, $options);

        if ($timeKeys === []) {
            $subjectAllKey = 'all:' . $subjectId;
            $hasSubjectAll = $options->contains(function ($item) use ($subjectAllKey) {
                return (string) ($item['key'] ?? '') === $subjectAllKey;
            });
            if ($hasSubjectAll) {
                $timeKeys = [$subjectAllKey];
            } else {
                $timeKeys = [(string) (($options->first()['key'] ?? ''))];
            }
        }

        return [
            'subject_id' => $subjectId,
            'subject_time_keys' => array_values(array_filter($timeKeys, fn($key) => $key !== '')),
        ];
    }

    private function extractSubjectAndTimeTexts(TeacherLawRequest $lawRequest): array
    {
        $subjectText = trim((string) ($lawRequest->subject ?? ''));
        $subjectTimeText = trim((string) ($lawRequest->subject_time ?? ''));

        if ($subjectTimeText === '' && str_contains($subjectText, '|')) {
            [$parsedSubject, $parsedTime] = array_pad(array_map('trim', explode('|', $subjectText, 2)), 2, '');
            $subjectText = $parsedSubject;
            $subjectTimeText = $parsedTime;
        }

        return [$subjectText, $subjectTimeText];
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
            if ($value === '') {
                return [];
            }

            return [$value];
        }

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(function ($item) {
            return trim((string) $item);
        }, $value), fn($item) => $item !== '')));
    }

    private function buildRequestedForScheduleText(TeacherLawRequest $lawRequest): string
    {
        $requestedForText = $lawRequest->requested_for
            ? Carbon::parse($lawRequest->requested_for)->format('M d, Y')
            : '';
        $subjectTimeText = trim((string) ($lawRequest->subject_time ?? ''));

        if ($subjectTimeText === '' && str_contains(trim((string) ($lawRequest->subject ?? '')), '|')) {
            [, $subjectTimeText] = array_pad(array_map('trim', explode('|', (string) $lawRequest->subject, 2)), 2, '');
        }

        $parts = array_filter([
            $requestedForText !== '' ? $requestedForText : null,
            $subjectTimeText !== '' ? $subjectTimeText : null,
        ]);

        return trim(implode(' | ', $parts));
    }

    private function teacherSubjectTimes(int $teacherId, $subjectOptions)
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
        $grouped['all'] = collect([
            [
                'key' => 'all:all',
                'subject_id' => 0,
                'label' => 'All Times',
            ],
        ]);

        if ($teacherId <= 0 || $subjectIds === []) {
            return $grouped->map(fn($items) => collect($items)->values());
        }

        $hasSlotTable = Schema::hasTable('subject_study_times');
        $hasSlotTeacherColumn = $hasSlotTable && Schema::hasColumn('subject_study_times', 'teacher_id');
        $hasSubjectTeacherColumn = Schema::hasColumn('subjects', 'teacher_id');
        $hasDayColumn = $hasSlotTable && Schema::hasColumn('subject_study_times', 'day_of_week');

        if ($hasSlotTable) {
            $daySelect = $hasDayColumn
                ? 'sst.day_of_week as day_of_week'
                : DB::raw("'all' as day_of_week");

            $slotQuery = DB::table('subject_study_times as sst')
                ->join('subjects', 'subjects.id', '=', 'sst.subject_id')
                ->whereIn('sst.subject_id', $subjectIds)
                ->select([
                    'sst.id',
                    'sst.subject_id',
                    'sst.period',
                    'sst.start_time',
                    'sst.end_time',
                    'sst.sort_order',
                    $daySelect,
                ]);

            if ($hasSlotTeacherColumn) {
                $slotQuery->where('sst.teacher_id', $teacherId);
            } elseif ($hasSubjectTeacherColumn) {
                $slotQuery->where('subjects.teacher_id', $teacherId);
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

                $slotLabel = $this->formatSubjectSlotLabel(
                    (string) ($slot->day_of_week ?? 'all'),
                    (string) ($slot->period ?? 'custom'),
                    (string) ($slot->start_time ?? ''),
                    (string) ($slot->end_time ?? '')
                );

                $grouped[(string) $subjectId]->push([
                    'key' => 'slot:' . (int) $slot->id,
                    'subject_id' => $subjectId,
                    'label' => $slotLabel,
                    'day_of_week' => (string) ($slot->day_of_week ?? 'all'),
                    'period' => (string) ($slot->period ?? 'custom'),
                    'start_time' => (string) ($slot->start_time ?? ''),
                    'end_time' => (string) ($slot->end_time ?? ''),
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

            $fallbackLabel = $this->formatFallbackSubjectTimeLabel(
                (string) ($subject->study_time ?? ''),
                (string) ($subject->study_start_time ?? ''),
                (string) ($subject->study_end_time ?? '')
            );

            $grouped[$subjectKey] = collect([
                [
                    'key' => 'subject:' . $subjectId,
                    'subject_id' => $subjectId,
                    'label' => $fallbackLabel,
                    'day_of_week' => 'all',
                    'period' => 'custom',
                    'start_time' => '',
                    'end_time' => '',
                ],
            ]);
        }

        foreach ($grouped as $subjectKey => $items) {
            $subjectId = (int) $subjectKey;
            if ($subjectId <= 0) {
                continue;
            }

            $allKey = 'all:' . $subjectId;
            $list = collect($items)->values();
            $hasAllOption = $list->contains(function ($item) use ($allKey) {
                return (string) ($item['key'] ?? '') === $allKey;
            });

            if (!$hasAllOption) {
                $list = collect([[
                    'key' => $allKey,
                    'subject_id' => $subjectId,
                    'label' => 'All Times',
                    'day_of_week' => 'all',
                    'period' => 'all',
                    'start_time' => '',
                    'end_time' => '',
                ]])->merge($list)->values();
            }

            $grouped[$subjectKey] = $list;
        }

        return $grouped->map(fn($items) => collect($items)->values());
    }

    private function teacherSubjects(int $teacherId)
    {
        if ($teacherId <= 0) {
            return collect();
        }

        $hasSubjectTeacherColumn = Schema::hasColumn('subjects', 'teacher_id');
        $hasSlotTable = Schema::hasTable('subject_study_times');
        $hasSlotTeacherColumn = $hasSlotTable && Schema::hasColumn('subject_study_times', 'teacher_id');

        $query = Subject::query()
            ->leftJoin('school_classes as classes', 'classes.id', '=', 'subjects.school_class_id')
            ->select([
                'subjects.id',
                'subjects.name',
                'subjects.code',
                'subjects.study_time',
                'subjects.study_start_time',
                'subjects.study_end_time',
                'classes.name as class_name',
                'classes.section as class_section',
            ]);

        if ($hasSlotTeacherColumn) {
            $query->join('subject_study_times', 'subject_study_times.subject_id', '=', 'subjects.id')
                ->where('subject_study_times.teacher_id', $teacherId);
        } elseif ($hasSubjectTeacherColumn) {
            $query->where('subjects.teacher_id', $teacherId);
        } else {
            return collect();
        }

        return $query
            ->distinct()
            ->orderBy('subjects.name')
            ->orderBy('classes.name')
            ->orderBy('classes.section')
            ->get();
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
        } catch (\Throwable $e) {
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

    private function cleanTeacherNotificationText(string $rawText): string
    {
        $text = trim($rawText);
        $text = preg_replace('/\[teacher_id:\d+\]\s*/', '', $text);

        return trim((string) $text);
    }

    private function lawTypes(): array
    {
        return [
            'school_policy' => 'School Policy',
            'classroom_rule' => 'Classroom Rule',
            'student_discipline' => 'Student Discipline',
            'safety' => 'Safety and Security',
            'other' => 'Other',
        ];
    }
}
