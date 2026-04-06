<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentStudyController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $classIdRaw = (string) $request->query('class_id', 'all');
        $subjectIdRaw = (string) $request->query('subject_id', 'all');
        $period = (string) $request->query('period', 'all');

        $classId = ctype_digit($classIdRaw) ? (int) $classIdRaw : null;
        $subjectId = ctype_digit($subjectIdRaw) ? (int) $subjectIdRaw : null;
        $hasMajorSubjectColumn = $this->hasMajorSubjectColumn();
        $hasClassStudyTimeColumn = $this->hasClassStudyTimeColumn();
        $hasStudentMajorSubjectsTable = Schema::hasTable('student_major_subjects');
        $hasStudentStudyTimesTable = Schema::hasTable('student_study_times');
        $hasSubjectStudyTimesTable = Schema::hasTable('subject_study_times');
        $hasClassDayColumn = $hasClassStudyTimeColumn && Schema::hasColumn('class_study_times', 'day_of_week');
        $hasSubjectDayColumn = $hasSubjectStudyTimesTable && Schema::hasColumn('subject_study_times', 'day_of_week');
        $hasSubjectClassColumn = $hasSubjectStudyTimesTable && Schema::hasColumn('subject_study_times', 'school_class_id');
        $hasSubjectTeacherColumn = $hasSubjectStudyTimesTable && Schema::hasColumn('subject_study_times', 'teacher_id');

        $studyQuery = DB::table('users as students')
            ->leftJoin('school_classes as classes', 'classes.id', '=', 'students.school_class_id')
            ->where('students.role', 'student');

        if ($hasMajorSubjectColumn) {
            $studyQuery->leftJoin('subjects', 'subjects.id', '=', 'students.major_subject_id');
        } else {
            $firstSubjectPerClass = DB::table('subjects')
                ->selectRaw('MIN(id) as id, school_class_id')
                ->groupBy('school_class_id');

            $studyQuery->leftJoinSub($firstSubjectPerClass, 'subject_map', function ($join) {
                $join->on('subject_map.school_class_id', '=', 'classes.id');
            });
            $studyQuery->leftJoin('subjects', 'subjects.id', '=', 'subject_map.id');
        }

        if ($hasClassStudyTimeColumn) {
            $studyQuery->leftJoin('class_study_times as class_slots', 'class_slots.id', '=', 'students.class_study_time_id');
        }

        if ($hasSubjectStudyTimesTable) {
            $firstSubjectSlot = DB::table('subject_study_times')
                ->selectRaw('MIN(id) as id, subject_id')
                ->groupBy('subject_id');

            $studyQuery->leftJoinSub($firstSubjectSlot, 'subject_slot_fallback_map', function ($join) {
                $join->on('subject_slot_fallback_map.subject_id', '=', 'subjects.id');
            });
            $studyQuery->leftJoin('subject_study_times as subject_slots_fallback', 'subject_slots_fallback.id', '=', 'subject_slot_fallback_map.id');

            if ($hasClassStudyTimeColumn) {
                $matchedSlotMap = DB::table('subject_study_times as sst')
                    ->join('class_study_times as cst', function ($join) use ($hasSubjectClassColumn, $hasClassDayColumn, $hasSubjectDayColumn) {
                        $join
                            ->on('cst.period', '=', 'sst.period')
                            ->on('cst.start_time', '=', 'sst.start_time')
                            ->on('cst.end_time', '=', 'sst.end_time');

                        if ($hasSubjectClassColumn) {
                            $join->on('cst.school_class_id', '=', 'sst.school_class_id');
                        }

                        if ($hasClassDayColumn && $hasSubjectDayColumn) {
                            $join->where(function ($dayQuery) {
                                $dayQuery
                                    ->whereColumn('cst.day_of_week', 'sst.day_of_week')
                                    ->orWhere('cst.day_of_week', 'all')
                                    ->orWhere('sst.day_of_week', 'all');
                            });
                        }
                    })
                    ->selectRaw('MIN(sst.id) as id, sst.subject_id, cst.id as class_study_time_id')
                    ->groupBy('sst.subject_id', 'cst.id');

                $studyQuery->leftJoinSub($matchedSlotMap, 'subject_slot_match_map', function ($join) {
                    $join
                        ->on('subject_slot_match_map.subject_id', '=', 'subjects.id')
                        ->on('subject_slot_match_map.class_study_time_id', '=', 'students.class_study_time_id');
                });
                $studyQuery->leftJoin('subject_study_times as subject_slots_matched', 'subject_slots_matched.id', '=', 'subject_slot_match_map.id');
            }
        }

        $teacherIdExpression = 'subjects.teacher_id';
        if ($hasSubjectStudyTimesTable && $hasSubjectTeacherColumn) {
            $teacherIdExpression = $hasClassStudyTimeColumn
                ? 'COALESCE(subject_slots_matched.teacher_id, subject_slots_fallback.teacher_id, subjects.teacher_id)'
                : 'COALESCE(subject_slots_fallback.teacher_id, subjects.teacher_id)';
        }

        $studyQuery->leftJoin('users as teachers', function ($join) use ($teacherIdExpression) {
            $join->on('teachers.id', '=', DB::raw($teacherIdExpression))
                ->where('teachers.role', '=', 'teacher');
        });

        $classStudyStartExpression = $hasClassStudyTimeColumn
            ? 'COALESCE(class_slots.start_time, classes.study_start_time, classes.study_time)'
            : 'COALESCE(classes.study_start_time, classes.study_time)';
        $classStudyEndExpression = $hasClassStudyTimeColumn
            ? 'COALESCE(class_slots.end_time, classes.study_end_time)'
            : 'classes.study_end_time';
        $classStudyPeriodExpression = $hasClassStudyTimeColumn ? 'class_slots.period' : 'NULL';
        $classStudyDayExpression = $hasClassStudyTimeColumn && $hasClassDayColumn ? 'class_slots.day_of_week' : 'NULL';

        if ($hasSubjectStudyTimesTable) {
            $subjectStudyStartExpression = $hasClassStudyTimeColumn
                ? 'COALESCE(subject_slots_matched.start_time, subject_slots_fallback.start_time)'
                : 'subject_slots_fallback.start_time';
            $subjectStudyEndExpression = $hasClassStudyTimeColumn
                ? 'COALESCE(subject_slots_matched.end_time, subject_slots_fallback.end_time)'
                : 'subject_slots_fallback.end_time';
            $subjectStudyPeriodExpression = $hasClassStudyTimeColumn
                ? 'COALESCE(subject_slots_matched.period, subject_slots_fallback.period)'
                : 'subject_slots_fallback.period';
            $subjectStudyDayExpression = $hasSubjectDayColumn
                ? ($hasClassStudyTimeColumn
                    ? 'COALESCE(subject_slots_matched.day_of_week, subject_slots_fallback.day_of_week)'
                    : 'subject_slots_fallback.day_of_week')
                : 'NULL';
        } else {
            $subjectStudyStartExpression = 'COALESCE(subjects.study_start_time, subjects.study_time)';
            $subjectStudyEndExpression = 'subjects.study_end_time';
            $subjectStudyPeriodExpression = 'NULL';
            $subjectStudyDayExpression = 'NULL';
        }

        $studyQuery->select([
            'students.id as student_id',
            'students.name as student_name',
            'students.email as student_email',
            'students.created_at as student_created_at',
            'classes.name as class_name',
            'classes.section as class_section',
            'classes.room as class_room',
            'class_slots.id as class_study_time_id',
            DB::raw($classStudyStartExpression . ' as class_study_start_time'),
            DB::raw($classStudyEndExpression . ' as class_study_end_time'),
            DB::raw($classStudyPeriodExpression . ' as class_study_period'),
            DB::raw($classStudyDayExpression . ' as class_study_day'),
            'subjects.id as subject_id',
            'subjects.name as subject_name',
            'subjects.code as subject_code',
            DB::raw($subjectStudyStartExpression . ' as subject_study_start_time'),
            DB::raw($subjectStudyEndExpression . ' as subject_study_end_time'),
            DB::raw($subjectStudyPeriodExpression . ' as subject_study_period'),
            DB::raw($subjectStudyDayExpression . ' as subject_study_day'),
            'teachers.name as teacher_name',
            'teachers.email as teacher_email',
            'teachers.created_at as teacher_created_at',
        ]);

        if ($classId !== null) {
            $studyQuery->where('classes.id', $classId);
        }

        if ($subjectId !== null) {
            if ($hasStudentMajorSubjectsTable) {
                $studyQuery->where(function ($query) use ($subjectId) {
                    $query->where('subjects.id', $subjectId)
                        ->orWhereExists(function ($subQuery) use ($subjectId) {
                            $subQuery->selectRaw('1')
                                ->from('student_major_subjects as sms')
                                ->whereColumn('sms.user_id', 'students.id')
                                ->where('sms.subject_id', $subjectId);
                        });
                });
            } else {
                $studyQuery->where('subjects.id', $subjectId);
            }
        }

        if (in_array($period, array_keys($this->periodOptions()), true)) {
            if ($hasClassStudyTimeColumn) {
                $studyQuery->where(function ($query) use ($period, $hasStudentStudyTimesTable) {
                    $query->where('class_slots.period', $period);

                    if ($hasStudentStudyTimesTable) {
                        $query->orWhereExists(function ($subQuery) use ($period) {
                            $subQuery->selectRaw('1')
                                ->from('student_study_times as sst')
                                ->join('class_study_times as cst_filter', 'cst_filter.id', '=', 'sst.class_study_time_id')
                                ->whereColumn('sst.user_id', 'students.id')
                                ->where('cst_filter.period', $period);
                        });
                    }
                });
            } else {
                $studyQuery->whereRaw('1 = 0');
            }
        }

        if ($search !== '') {
            $studyQuery->where(function ($query) use ($search, $hasSubjectStudyTimesTable, $hasClassStudyTimeColumn) {
                $query->where('students.name', 'like', '%' . $search . '%')
                    ->orWhere('students.email', 'like', '%' . $search . '%')
                    ->orWhere('classes.name', 'like', '%' . $search . '%')
                    ->orWhere('classes.section', 'like', '%' . $search . '%')
                    ->orWhere('classes.room', 'like', '%' . $search . '%')
                    ->orWhere('classes.study_time', 'like', '%' . $search . '%')
                    ->orWhere('classes.study_start_time', 'like', '%' . $search . '%')
                    ->orWhere('classes.study_end_time', 'like', '%' . $search . '%')
                    ->orWhere('subjects.name', 'like', '%' . $search . '%')
                    ->orWhere('subjects.code', 'like', '%' . $search . '%')
                    ->orWhere('subjects.study_time', 'like', '%' . $search . '%')
                    ->orWhere('subjects.study_start_time', 'like', '%' . $search . '%')
                    ->orWhere('subjects.study_end_time', 'like', '%' . $search . '%')
                    ->orWhere('teachers.name', 'like', '%' . $search . '%')
                    ->orWhere('teachers.email', 'like', '%' . $search . '%');

                if ($hasSubjectStudyTimesTable) {
                    $query->orWhere('subject_slots_fallback.start_time', 'like', '%' . $search . '%')
                        ->orWhere('subject_slots_fallback.end_time', 'like', '%' . $search . '%');

                    if ($hasClassStudyTimeColumn) {
                        $query->orWhere('subject_slots_matched.start_time', 'like', '%' . $search . '%')
                            ->orWhere('subject_slots_matched.end_time', 'like', '%' . $search . '%');
                    }
                }
            });
        }

        $studies = $studyQuery
            ->orderByDesc('students.created_at')
            ->orderBy('students.id')
            ->paginate(15)
            ->withQueryString();

        $studentIds = $studies->pluck('student_id')
            ->filter(fn($id) => (int) $id > 0)
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        $majorSubjectsByStudent = [];
        if ($hasStudentMajorSubjectsTable && !empty($studentIds)) {
            $majorSubjectsByStudent = DB::table('student_major_subjects as sms')
                ->join('subjects', 'subjects.id', '=', 'sms.subject_id')
                ->whereIn('sms.user_id', $studentIds)
                ->orderBy('subjects.name')
                ->get([
                    'sms.user_id as student_id',
                    'subjects.id as subject_id',
                    'subjects.name as subject_name',
                    'subjects.code as subject_code',
                ])
                ->groupBy('student_id')
                ->map(function ($rows) {
                    return $rows->map(function ($row) {
                        return [
                            'id' => (int) $row->subject_id,
                            'name' => (string) $row->subject_name,
                            'code' => (string) ($row->subject_code ?? ''),
                        ];
                    })->values()->all();
                })
                ->toArray();
        }

        $studyTimesByStudent = [];
        if ($hasStudentStudyTimesTable && !empty($studentIds)) {
            $studyTimesByStudent = DB::table('student_study_times as sst')
                ->join('class_study_times as cst', 'cst.id', '=', 'sst.class_study_time_id')
                ->leftJoin('school_classes as classes', 'classes.id', '=', 'cst.school_class_id')
                ->whereIn('sst.user_id', $studentIds)
                ->orderBy('cst.start_time')
                ->get([
                    'sst.user_id as student_id',
                    'cst.id as class_study_time_id',
                    'cst.school_class_id',
                    'cst.day_of_week',
                    'cst.period',
                    'cst.start_time',
                    'cst.end_time',
                    'classes.name as class_name',
                    'classes.section as class_section',
                ])
                ->groupBy('student_id')
                ->map(function ($rows) {
                    return $rows->map(function ($row) {
                        $classLabel = trim((string) ($row->class_name ?? ''));
                        $section = trim((string) ($row->class_section ?? ''));
                        if ($classLabel !== '' && $section !== '') {
                            $classLabel .= ' - ' . $section;
                        }

                        return [
                            'id' => (int) $row->class_study_time_id,
                            'school_class_id' => $row->school_class_id !== null ? (int) $row->school_class_id : null,
                            'class_label' => $classLabel,
                            'day_of_week' => strtolower(trim((string) ($row->day_of_week ?? 'all'))),
                            'period' => strtolower(trim((string) ($row->period ?? ''))),
                            'start_time' => substr((string) ($row->start_time ?? ''), 0, 5),
                            'end_time' => substr((string) ($row->end_time ?? ''), 0, 5),
                        ];
                    })->values()->all();
                })
                ->toArray();
        }

        $stats = [
            'students' => User::query()->where('role', 'student')->count(),
            'subjects' => Subject::query()->count(),
            'teachers' => User::query()->where('role', 'teacher')->count(),
            'withMajorSubject' => $hasStudentMajorSubjectsTable
                ? (int) DB::table('student_major_subjects')
                    ->join('users', 'users.id', '=', 'student_major_subjects.user_id')
                    ->where('users.role', 'student')
                    ->distinct('student_major_subjects.user_id')
                    ->count('student_major_subjects.user_id')
                : ($hasMajorSubjectColumn
                    ? User::query()->where('role', 'student')->whereNotNull('major_subject_id')->count()
                    : 0),
            'withStudyTime' => $hasStudentStudyTimesTable
                ? (int) DB::table('student_study_times')
                    ->join('users', 'users.id', '=', 'student_study_times.user_id')
                    ->where('users.role', 'student')
                    ->distinct('student_study_times.user_id')
                    ->count('student_study_times.user_id')
                : ($hasClassStudyTimeColumn
                    ? User::query()->where('role', 'student')->whereNotNull('class_study_time_id')->count()
                    : 0),
        ];

        $classes = SchoolClass::query()
            ->orderBy('name')
            ->orderBy('section')
            ->get();

        $subjects = Subject::query()
            ->select(['id', 'name', 'code', 'school_class_id'])
            ->when($classId !== null, function ($query) use ($classId) {
                $query->where('school_class_id', $classId);
            })
            ->orderBy('name')
            ->get();

        return view('admin.student-study', [
            'studies' => $studies,
            'search' => $search,
            'classes' => $classes,
            'subjects' => $subjects,
            'classId' => $classId !== null ? (string) $classId : 'all',
            'subjectId' => $subjectId !== null ? (string) $subjectId : 'all',
            'period' => in_array($period, array_merge(['all'], array_keys($this->periodOptions())), true) ? $period : 'all',
            'periodOptions' => $this->periodOptions(),
            'stats' => $stats,
            'hasMajorSubjectColumn' => $hasMajorSubjectColumn,
            'hasClassStudyTimeColumn' => $hasClassStudyTimeColumn,
            'majorSubjectsByStudent' => $majorSubjectsByStudent,
            'studyTimesByStudent' => $studyTimesByStudent,
        ]);
    }

    private function hasMajorSubjectColumn(): bool
    {
        return Schema::hasColumn('users', 'major_subject_id');
    }

    private function hasClassStudyTimeColumn(): bool
    {
        return Schema::hasColumn('users', 'class_study_time_id');
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
}
