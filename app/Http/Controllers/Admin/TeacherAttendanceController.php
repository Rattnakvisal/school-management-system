<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\TeacherAttendance;
use App\Models\TeacherLawRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class TeacherAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $statusRaw = strtolower(trim((string) $request->query('status', 'all')));
        $date = trim((string) $request->query('date', now()->toDateString()));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = now()->toDateString();
        }

        $statusLabels = $this->statusLabels();
        $status = array_key_exists($statusRaw, $statusLabels) ? $statusRaw : 'all';
        $hasTeacherAttendanceTable = Schema::hasTable('teacher_attendances');
        $hasLawRequestTable = Schema::hasTable('teacher_law_requests');

        $teachers = User::query()
            ->where('role', 'teacher')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'is_active']);

        $attendanceByTeacher = collect();
        if ($hasTeacherAttendanceTable && $teachers->isNotEmpty()) {
            $attendanceByTeacher = TeacherAttendance::query()
                ->whereDate('attendance_date', $date)
                ->whereIn('teacher_id', $teachers->pluck('id')->all())
                ->when($status !== 'all', function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->get()
                ->keyBy('teacher_id');

            if ($status !== 'all') {
                $teachers = $teachers->filter(function ($teacher) use ($attendanceByTeacher) {
                    return $attendanceByTeacher->has((int) $teacher->id);
                })->values();
            }
        }

        $lawRequestsByTeacher = collect();
        $lawRequestSummary = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
        ];

        if ($hasLawRequestTable && $teachers->isNotEmpty()) {
            $activeLawRequests = TeacherLawRequest::query()
                ->whereDate('requested_for', $date)
                ->whereIn('teacher_id', $teachers->pluck('id')->all())
                ->whereIn('status', ['pending', 'approved'])
                ->orderByRaw("CASE WHEN status = 'approved' THEN 0 ELSE 1 END")
                ->orderByDesc('reviewed_at')
                ->orderByDesc('created_at')
                ->get([
                    'id',
                    'teacher_id',
                    'law_type',
                    'subject',
                    'subject_time',
                    'requested_for',
                    'reason',
                    'status',
                    'reviewed_at',
                    'created_at',
                ]);

            $lawRequestsByTeacher = $activeLawRequests
                ->groupBy(function ($request) {
                    return (int) ($request->teacher_id ?? 0);
                })
                ->map(function ($rows) {
                    return $rows->first();
                });

            $lawRequestSummary['total'] = (int) $lawRequestsByTeacher->count();
            $lawRequestSummary['pending'] = (int) $lawRequestsByTeacher
                ->filter(fn($request) => strtolower((string) ($request->status ?? '')) === 'pending')
                ->count();
            $lawRequestSummary['approved'] = (int) $lawRequestsByTeacher
                ->filter(fn($request) => strtolower((string) ($request->status ?? '')) === 'approved')
                ->count();
        }

        $stats = [
            'teachers' => (int) $teachers->count(),
            'checked' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'not_marked' => 0,
            'law_requests' => $lawRequestSummary['total'],
        ];

        foreach ($teachers as $teacher) {
            $record = $attendanceByTeacher->get((int) $teacher->id);
            $statusKey = strtolower((string) ($record?->status ?? ''));
            if ($statusKey !== '' && isset($stats[$statusKey])) {
                $stats['checked'] += 1;
                $stats[$statusKey] += 1;
            } else {
                $stats['not_marked'] += 1;
            }
        }

        $hasUnlockedTeachers = $teachers->contains(function ($teacher) use ($attendanceByTeacher) {
            return !$attendanceByTeacher->has((int) ($teacher->id ?? 0));
        });

        return view('admin.teacher-attendance', [
            'teachers' => $teachers,
            'attendanceByTeacher' => $attendanceByTeacher,
            'search' => $search,
            'status' => $status,
            'date' => $date,
            'statusLabels' => $statusLabels,
            'stats' => $stats,
            'hasTeacherAttendanceTable' => $hasTeacherAttendanceTable,
            'lawRequestsByTeacher' => $lawRequestsByTeacher,
            'lawRequestSummary' => $lawRequestSummary,
            'hasUnlockedTeachers' => $hasUnlockedTeachers,
        ]);
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('teacher_attendances')) {
            return back()->withInput()->with('error', 'Teacher attendance table is missing. Please run migrations.');
        }

        $teacherIds = User::query()
            ->where('role', 'teacher')
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        if (count($teacherIds) === 0) {
            return back()->withInput()->with('warning', 'No teachers found.');
        }

        $statuses = array_keys($this->statusLabels());
        $statuses = array_values(array_filter($statuses, fn($status) => $status !== 'all'));

        $validated = $request->validate([
            'attendance_date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'string', Rule::in($statuses)],
            'attendance.*.remark' => ['nullable', 'string', 'max:255'],
        ]);

        $attendanceDate = (string) $validated['attendance_date'];
        $attendanceRows = (array) ($validated['attendance'] ?? []);
        $adminId = (int) ($request->user()?->id ?? 0);
        $now = now();
        $savedCount = 0;
        $autoExcusedCount = 0;
        $skippedLockedCount = 0;
        $savedTeacherRows = [];

        $activeLawRequestsByTeacher = collect();
        if (Schema::hasTable('teacher_law_requests')) {
            $activeLawRequestsByTeacher = TeacherLawRequest::query()
                ->whereDate('requested_for', $attendanceDate)
                ->whereIn('teacher_id', $teacherIds)
                ->where('status', 'approved')
                ->orderByDesc('reviewed_at')
                ->orderByDesc('created_at')
                ->get([
                    'id',
                    'teacher_id',
                    'law_type',
                    'subject',
                    'subject_time',
                    'status',
                ])
                ->groupBy(function ($request) {
                    return (int) ($request->teacher_id ?? 0);
                })
                ->map(function ($rows) {
                    return $rows->first();
                });
        }

        $lockedTeacherLookup = TeacherAttendance::query()
            ->whereDate('attendance_date', $attendanceDate)
            ->whereIn('teacher_id', $teacherIds)
            ->pluck('teacher_id')
            ->map(fn($id) => (int) $id)
            ->flip();

        DB::transaction(function () use (
            $teacherIds,
            $attendanceRows,
            $attendanceDate,
            $adminId,
            $now,
            $activeLawRequestsByTeacher,
            $lockedTeacherLookup,
            &$savedCount,
            &$autoExcusedCount,
            &$skippedLockedCount,
            &$savedTeacherRows
        ) {
            foreach ($teacherIds as $teacherId) {
                if ($lockedTeacherLookup->has((int) $teacherId)) {
                    $skippedLockedCount += 1;
                    continue;
                }

                $row = $attendanceRows[(string) $teacherId] ?? null;
                if (!is_array($row)) {
                    continue;
                }

                $status = strtolower(trim((string) ($row['status'] ?? '')));
                if ($status === '') {
                    continue;
                }

                $remark = trim((string) ($row['remark'] ?? ''));
                $activeLawRequest = $activeLawRequestsByTeacher->get((int) $teacherId);
                if ($activeLawRequest) {
                    if ($status !== 'excused') {
                        $autoExcusedCount += 1;
                    }
                    $status = 'excused';

                    $lawRemark = $this->buildLawRequestRemark($activeLawRequest);
                    if ($lawRemark !== '') {
                        if ($remark !== '' && stripos($remark, $lawRemark) === false) {
                            $remark = $lawRemark . ' | ' . $remark;
                        } elseif ($remark === '') {
                            $remark = $lawRemark;
                        }
                        $remark = mb_substr($remark, 0, 255);
                    }
                }

                $attendance = TeacherAttendance::query()->firstOrCreate(
                    [
                        'teacher_id' => $teacherId,
                        'attendance_date' => $attendanceDate,
                    ],
                    [
                        'marked_by' => $adminId > 0 ? $adminId : null,
                        'status' => $status,
                        'remark' => $remark !== '' ? $remark : null,
                        'checked_at' => $now,
                    ]
                );

                if (!$attendance->wasRecentlyCreated) {
                    $skippedLockedCount += 1;
                    continue;
                }

                $savedCount += 1;
                $savedTeacherRows[] = [
                    'teacher_id' => $teacherId,
                    'status' => $status,
                ];
            }
        });

        if ($savedCount > 0) {
            $statusLabels = $this->statusLabels();
            $displayDate = Carbon::parse($attendanceDate)->format('M d, Y');

            foreach ($savedTeacherRows as $savedTeacherRow) {
                $teacherId = (int) ($savedTeacherRow['teacher_id'] ?? 0);
                if ($teacherId <= 0) {
                    continue;
                }

                $statusKey = strtolower((string) ($savedTeacherRow['status'] ?? ''));
                $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);
                $teacherTag = '[teacher_id:' . $teacherId . '] ';

                $notification = new Notification([
                    'type' => 'teacher_attendance_checked',
                    'title' => 'Teacher attendance checked',
                    'message' => $teacherTag . 'Your attendance was checked for ' . $displayDate . ': ' . $statusLabel . '.',
                    'url' => route('teacher.attendance.index', ['date' => $attendanceDate]),
                    'is_read' => false,
                ]);
                $notification->save();
            }
        }

        if ($savedCount === 0 && $skippedLockedCount > 0) {
            return redirect()
                ->route('admin.attendance.teachers.index', [
                    'date' => $attendanceDate,
                ])
                ->with('warning', 'Teacher attendance already saved for this date. Cannot save again.');
        }

        return redirect()
            ->route('admin.attendance.teachers.index', [
                'date' => $attendanceDate,
            ])
            ->with(
                'success',
                'Teacher attendance checked successfully for '
                . $savedCount
                . ' teacher(s).'
                . ($autoExcusedCount > 0 ? (' Auto-excused ' . $autoExcusedCount . ' teacher(s) due to approved law request.') : '')
                . ($skippedLockedCount > 0 ? (' Skipped ' . $skippedLockedCount . ' already-saved teacher(s).') : '')
            );
    }

    public function approveLawRequest(Request $request, TeacherLawRequest $lawRequest)
    {
        if (!Schema::hasTable('teacher_law_requests')) {
            return back()->with('error', 'Teacher law request table is missing.');
        }

        $status = strtolower((string) ($lawRequest->status ?? ''));
        if ($status === 'approved') {
            return back()->with('warning', 'This law request is already approved.');
        }

        $lawRequest->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        $teacherName = trim((string) ($lawRequest->teacher?->name ?? 'Teacher'));
        $subjectText = trim((string) ($lawRequest->subject ?? ''));
        $subjectTimeText = trim((string) ($lawRequest->subject_time ?? ''));
        if ($subjectTimeText === '' && str_contains($subjectText, '|')) {
            [$parsedSubject, $parsedTime] = array_pad(array_map('trim', explode('|', $subjectText, 2)), 2, '');
            $subjectText = $parsedSubject;
            $subjectTimeText = $parsedTime;
        }
        if ($subjectTimeText !== '') {
            $subjectText = $subjectText !== '' ? ($subjectText . ' @ ' . $subjectTimeText) : $subjectTimeText;
        }
        $lawRemark = $this->buildLawRequestRemark($lawRequest);
        $displayDate = $lawRequest->requested_for
            ? Carbon::parse($lawRequest->requested_for)->format('M d, Y')
            : 'selected date';
        $teacherId = (int) ($lawRequest->teacher_id ?? 0);
        $teacherTag = '[teacher_id:' . $teacherId . '] ';

        if (Schema::hasTable('teacher_attendances') && $teacherId > 0 && $lawRequest->requested_for) {
            TeacherAttendance::query()->updateOrCreate(
                [
                    'teacher_id' => $teacherId,
                    'attendance_date' => Carbon::parse($lawRequest->requested_for)->toDateString(),
                ],
                [
                    'marked_by' => (int) ($request->user()?->id ?? 0) ?: null,
                    'status' => 'excused',
                    'remark' => $lawRemark !== '' ? mb_substr($lawRemark, 0, 255) : null,
                    'checked_at' => now(),
                ]
            );
        }

        $notification = new Notification([
            'type' => 'teacher_law_request_approved',
            'title' => 'Law request approved',
            'message' => $teacherTag
                . 'Your law request has been approved for '
                . $displayDate
                . ($subjectText !== '' ? (': ' . $subjectText) : '.'),
            'url' => route('teacher.law-requests.index'),
            'is_read' => false,
        ]);
        $notification->save();

        $dateParam = trim((string) $request->input('date', (string) $request->input('attendance_date', '')));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateParam)) {
            $dateParam = $lawRequest->requested_for ? Carbon::parse($lawRequest->requested_for)->toDateString() : now()->toDateString();
        }

        return redirect()
            ->route('admin.attendance.teachers.index', ['date' => $dateParam])
            ->with(
                'success',
                'Approved law request for ' . ($teacherName !== '' ? $teacherName : 'teacher')
                . ($subjectText !== '' ? (' (' . $subjectText . ')') : '') . '.'
            );
    }

    private function statusLabels(): array
    {
        return [
            'all' => 'All',
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'excused' => 'Excused',
        ];
    }

    private function buildLawRequestRemark(TeacherLawRequest $lawRequest): string
    {
        $lawType = trim((string) ($lawRequest->law_type ?? ''));
        $subject = trim((string) ($lawRequest->subject ?? ''));
        $subjectTime = trim((string) ($lawRequest->subject_time ?? ''));

        $labelType = $lawType !== '' ? ucwords(str_replace('_', ' ', $lawType)) : 'Request';
        $details = $subject;
        if ($subjectTime !== '') {
            $details = $details !== '' ? ($details . ' @ ' . $subjectTime) : $subjectTime;
        }

        $base = 'Law Request (' . $labelType . ')';
        if ($details !== '') {
            $base .= ': ' . $details;
        }

        return mb_substr($base, 0, 170);
    }
}
