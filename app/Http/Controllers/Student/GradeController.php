<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();
        abort_unless($student instanceof User, 403);

        $gradeQuery = Grade::query()
            ->with([
                'teacher:id,name',
                'subject:id,name,code,school_class_id',
                'subject.schoolClass:id,name,section',
            ])
            ->where('student_id', (int) $student->id);

        $grades = (clone $gradeQuery)
            ->latest('graded_at')
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        $averagePercentage = (clone $gradeQuery)
            ->selectRaw('AVG((score / NULLIF(max_score, 0)) * 100) as average_percentage')
            ->value('average_percentage');

        $stats = [
            'total' => (int) (clone $gradeQuery)->count(),
            'subjects' => (int) (clone $gradeQuery)->distinct('subject_id')->count('subject_id'),
            'average' => $averagePercentage !== null ? round((float) $averagePercentage, 1) : null,
        ];

        return view('student.grades', [
            'grades' => $grades,
            'stats' => $stats,
        ]);
    }
}
