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

        $studyQuery->leftJoin('users as teachers', function ($join) {
            $join->on('teachers.id', '=', 'subjects.teacher_id')
                ->where('teachers.role', '=', 'teacher');
        });

        $studyQuery->select([
            'students.id as student_id',
            'students.name as student_name',
            'students.email as student_email',
            'students.created_at as student_created_at',
            'classes.name as class_name',
            'classes.section as class_section',
            'classes.room as class_room',
            DB::raw(($hasClassStudyTimeColumn ? 'COALESCE(class_slots.start_time, classes.study_start_time, classes.study_time)' : 'COALESCE(classes.study_start_time, classes.study_time)') . ' as class_study_start_time'),
            DB::raw(($hasClassStudyTimeColumn ? 'COALESCE(class_slots.end_time, classes.study_end_time)' : 'classes.study_end_time') . ' as class_study_end_time'),
            DB::raw(($hasClassStudyTimeColumn ? 'class_slots.period' : 'NULL') . ' as class_study_period'),
            'subjects.id as subject_id',
            'subjects.name as subject_name',
            'subjects.code as subject_code',
            DB::raw('COALESCE(subjects.study_start_time, subjects.study_time) as subject_study_start_time'),
            'subjects.study_end_time as subject_study_end_time',
            'teachers.name as teacher_name',
            'teachers.email as teacher_email',
            'teachers.created_at as teacher_created_at',
        ]);

        if ($classId !== null) {
            $studyQuery->where('classes.id', $classId);
        }

        if ($subjectId !== null) {
            $studyQuery->where('subjects.id', $subjectId);
        }

        if (in_array($period, array_keys($this->periodOptions()), true)) {
            if ($hasClassStudyTimeColumn) {
                $studyQuery->where('class_slots.period', $period);
            } else {
                $studyQuery->whereRaw('1 = 0');
            }
        }

        if ($search !== '') {
            $studyQuery->where(function ($query) use ($search) {
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
            });
        }

        $studies = $studyQuery
            ->orderByDesc('students.created_at')
            ->orderBy('students.id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'students' => User::query()->where('role', 'student')->count(),
            'subjects' => Subject::query()->count(),
            'teachers' => User::query()->where('role', 'teacher')->count(),
            'withMajorSubject' => $hasMajorSubjectColumn
                ? User::query()->where('role', 'student')->whereNotNull('major_subject_id')->count()
                : 0,
            'withStudyTime' => $hasClassStudyTimeColumn
                ? User::query()->where('role', 'student')->whereNotNull('class_study_time_id')->count()
                : 0,
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
