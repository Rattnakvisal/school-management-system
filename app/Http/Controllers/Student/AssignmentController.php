<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                'students' => function ($query) use ($student) {
                    $query->where('users.id', (int) $student->id);
                },
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

        $assignmentTotal = (int) (clone $assignmentQuery)->count();
        $assignmentSubmitted = (int) DB::table('assignment_student')
            ->where('student_id', (int) $student->id)
            ->whereNotNull('submitted_at')
            ->count();

        $stats = [
            'total' => $assignmentTotal,
            'dueSoon' => (int) (clone $assignmentQuery)
                ->whereNotNull('due_at')
                ->whereBetween('due_at', [now(), now()->copy()->addDays(7)->endOfDay()])
                ->count(),
            'overdue' => (int) (clone $assignmentQuery)
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->count(),
            'submitted' => $assignmentSubmitted,
            'pending' => max(0, $assignmentTotal - $assignmentSubmitted),
        ];

        return view('student.assignments', [
            'assignments' => $assignments,
            'stats' => $stats,
            'classLabel' => $student->schoolClass?->display_name ?? 'No class assigned',
        ]);
    }

    public function submit(Request $request, Assignment $assignment): RedirectResponse
    {
        $student = $request->user();
        abort_unless($student instanceof User, 403);

        $isAssigned = $assignment->students()
            ->where('users.id', (int) $student->id)
            ->exists();
        abort_unless($isAssigned, 403);

        $request->validate([
            'submission_file' => ['required', 'file', 'max:10240'],
        ]);

        $file = $request->file('submission_file');
        if (!$file || !$file->isValid()) {
            return back()->withErrors(['submission_file' => 'Please choose a valid file before sending.']);
        }

        $pivot = DB::table('assignment_student')
            ->where('assignment_id', (int) $assignment->id)
            ->where('student_id', (int) $student->id)
            ->first();

        $oldPath = trim((string) ($pivot?->submission_file_path ?? ''));
        if ($oldPath !== '') {
            Storage::disk('public')->delete($oldPath);
        }

        $path = $file->store('assignment-submissions', 'public');

        DB::table('assignment_student')
            ->where('assignment_id', (int) $assignment->id)
            ->where('student_id', (int) $student->id)
            ->update([
                'submission_file_path' => $path,
                'submission_file_name' => $file->getClientOriginalName(),
                'submission_file_mime' => $file->getClientMimeType(),
                'submission_file_size' => $file->getSize(),
                'submitted_at' => now(),
                'updated_at' => now(),
            ]);

        Notification::query()->create([
            'type' => 'student_assignment_submitted',
            'title' => 'Assignment submitted',
            'message' => '[teacher_id:' . (int) $assignment->teacher_id . '] '
                . trim((string) $student->name)
                . ' submitted "' . trim((string) $assignment->title) . '".',
            'url' => route('teacher.assignments.index') . '#assignment-' . $assignment->id,
            'is_read' => false,
        ]);

        return back()
            ->with('success', 'Assignment file sent to your teacher.')
            ->with('success_title', 'Sent to teacher');
    }

    public function destroySubmission(Request $request, Assignment $assignment): RedirectResponse
    {
        $student = $request->user();
        abort_unless($student instanceof User, 403);

        $isAssigned = $assignment->students()
            ->where('users.id', (int) $student->id)
            ->exists();
        abort_unless($isAssigned, 403);

        $pivot = DB::table('assignment_student')
            ->where('assignment_id', (int) $assignment->id)
            ->where('student_id', (int) $student->id)
            ->first();

        $oldPath = trim((string) ($pivot?->submission_file_path ?? ''));
        if ($oldPath === '') {
            return back()->withErrors(['submission_file' => 'No submitted file found to delete.']);
        }

        Storage::disk('public')->delete($oldPath);

        DB::table('assignment_student')
            ->where('assignment_id', (int) $assignment->id)
            ->where('student_id', (int) $student->id)
            ->update([
                'submission_file_path' => null,
                'submission_file_name' => null,
                'submission_file_mime' => null,
                'submission_file_size' => null,
                'submitted_at' => null,
                'updated_at' => now(),
            ]);

        return back()
            ->with('success', 'Assignment submission deleted.')
            ->with('success_title', 'Submission deleted');
    }
}
