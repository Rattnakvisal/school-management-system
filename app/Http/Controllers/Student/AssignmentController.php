<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();
        abort_unless($student instanceof User, 403);

        $assignmentQuery = Assignment::query()
            ->with([
                'teacher:id,name',
                'subject:id,name,code,school_class_id',
                'subject.schoolClass:id,name,section',
            ])
            ->whereHas('students', function ($query) use ($student) {
                $query->where('users.id', (int) $student->id);
            });

        $assignments = (clone $assignmentQuery)
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => (int) (clone $assignmentQuery)->count(),
            'dueSoon' => (int) (clone $assignmentQuery)
                ->whereNotNull('due_at')
                ->whereBetween('due_at', [now(), now()->copy()->addDays(7)->endOfDay()])
                ->count(),
            'overdue' => (int) (clone $assignmentQuery)
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->count(),
        ];

        return view('student.assignments', [
            'assignments' => $assignments,
            'stats' => $stats,
        ]);
    }
}
