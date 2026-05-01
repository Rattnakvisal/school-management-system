<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();
        abort_unless($student instanceof User, 403);

        $student->loadMissing([
            'schoolClass:id,name,section',
            'majorSubject:id,name,code',
            'majorSubjects:id,name,code',
        ]);

        $gradeQuery = Grade::query()
            ->with([
                'teacher:id,name',
                'subject:id,name,code,school_class_id',
                'subject.schoolClass:id,name,section',
            ])
            ->where('student_id', (int) $student->id);

        $subjectFilter = (int) $request->query('subject_id', 0);
        if ($subjectFilter > 0) {
            $gradeQuery->where('subject_id', $subjectFilter);
        }

        $termFilter = (string) $request->query('term', '');
        if (preg_match('/^\d{4}-\d{2}$/', $termFilter) === 1) {
            $gradeQuery->whereYear('graded_at', (int) substr($termFilter, 0, 4))
                ->whereMonth('graded_at', (int) substr($termFilter, 5, 2));
        }

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

        $transcriptStats = $this->transcriptStats(
            (clone $gradeQuery)->get(['id', 'score', 'max_score'])
        );

        $allStudentGrades = Grade::query()
            ->with(['subject:id,name,code'])
            ->where('student_id', (int) $student->id)
            ->latest('graded_at')
            ->get();

        $subjectOptions = $this->subjectOptions($student, $allStudentGrades);

        $termOptions = $allStudentGrades
            ->filter(fn (Grade $grade): bool => $grade->graded_at !== null)
            ->map(fn (Grade $grade): array => [
                'value' => $grade->graded_at->format('Y-m'),
                'label' => $grade->graded_at->format('F Y'),
            ])
            ->unique('value')
            ->values();

        return view('student.grades', [
            'grades' => $grades,
            'stats' => $stats,
            'transcriptStats' => $transcriptStats,
            'classLabel' => $student->schoolClass?->display_name ?? 'No class assigned',
            'student' => $student,
            'subjectOptions' => $subjectOptions,
            'termOptions' => $termOptions,
            'selectedSubjectId' => $subjectFilter,
            'selectedTerm' => $termFilter,
        ]);
    }

    private function subjectOptions(User $student, Collection $grades): Collection
    {
        $subjects = collect();

        if ($student->majorSubject) {
            $subjects->push($student->majorSubject);
        }

        $student->majorSubjects->each(fn ($subject) => $subjects->push($subject));

        $grades
            ->pluck('subject')
            ->filter()
            ->each(fn ($subject) => $subjects->push($subject));

        return $subjects
            ->unique('id')
            ->map(fn ($subject): array => [
                'id' => (int) $subject->id,
                'label' => trim((string) $subject->name) !== '' ? (string) $subject->name : 'Selected program',
                'code' => trim((string) ($subject->code ?? '')),
            ])
            ->values();
    }

    private function transcriptStats(Collection $grades): array
    {
        $creditPerGrade = 3;
        $creditsAttempted = 0;
        $creditsEarned = 0;
        $totalPoints = 0.0;

        foreach ($grades as $grade) {
            $point = $this->gradePoint((float) ($grade->score ?? 0), (float) ($grade->max_score ?? 0));
            $creditsAttempted += $creditPerGrade;
            $creditsEarned += $point > 0 ? $creditPerGrade : 0;
            $totalPoints += $creditPerGrade * $point;
        }

        return [
            'credits_transferred' => 0,
            'credits_attempted' => $creditsAttempted,
            'credits_earned' => $creditsEarned,
            'total_points' => $totalPoints,
            'cumulative_gpa' => $creditsAttempted > 0 ? $totalPoints / $creditsAttempted : null,
        ];
    }

    private function gradePoint(float $score, float $maxScore): float
    {
        $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;

        return round(match (true) {
            $percentage >= 90 => 4.0,
            $percentage >= 80 => 3.0 + (($percentage - 80) / 10),
            $percentage >= 70 => 2.0 + (($percentage - 70) / 10),
            $percentage >= 60 => 1.0 + (($percentage - 60) / 10),
            default => 0.0,
        }, 2);
    }
}
