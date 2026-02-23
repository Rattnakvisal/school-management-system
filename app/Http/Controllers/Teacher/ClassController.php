<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = (int) ($request->user()?->id ?? 0);
        $search = trim((string) $request->query('q', ''));
        $room = trim((string) $request->query('room', 'all'));
        $period = strtolower(trim((string) $request->query('period', 'all')));
        $schedule = strtolower(trim((string) $request->query('schedule', 'all')));
        $allowedPeriods = ['morning', 'afternoon', 'evening', 'night', 'custom'];
        $allowedSchedules = ['all', 'with_schedule', 'without_schedule'];

        $baseTeacherClassesQuery = SchoolClass::query()
            ->whereHas('subjects', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            });

        $roomOptions = (clone $baseTeacherClassesQuery)
            ->whereNotNull('room')
            ->where('room', '!=', '')
            ->orderBy('room')
            ->distinct()
            ->pluck('room')
            ->values();

        $classQuery = (clone $baseTeacherClassesQuery)
            ->with([
                'studySchedules',
                'subjects' => function ($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId)->orderBy('name');
                },
            ])
            ->withCount([
                'students',
                'subjects as taught_subjects_count' => function ($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId);
                },
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('section', 'like', '%' . $search . '%')
                        ->orWhere('room', 'like', '%' . $search . '%')
                        ->orWhereHas('subjects', function ($subjectQuery) use ($search) {
                            $subjectQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('code', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($room !== '' && strtolower($room) !== 'all', function ($query) use ($room) {
                $query->where('room', $room);
            })
            ->when(in_array($period, $allowedPeriods, true), function ($query) use ($period) {
                $query->whereHas('studySchedules', function ($scheduleQuery) use ($period) {
                    $scheduleQuery->whereRaw('LOWER(period) = ?', [$period]);
                });
            })
            ->when($schedule === 'with_schedule', function ($query) {
                $query->whereHas('studySchedules');
            })
            ->when($schedule === 'without_schedule', function ($query) {
                $query->whereDoesntHave('studySchedules');
            })
            ->orderBy('name')
            ->orderBy('section');

        $classes = $classQuery
            ->paginate(10)
            ->withQueryString();

        $totalClasses = (clone $classQuery)->toBase()->getCountForPagination();
        $totalStudents = $classes->getCollection()->sum('students_count');
        $totalSubjects = $classes->getCollection()->sum('taught_subjects_count');

        return view('teacher.classes', [
            'classes' => $classes,
            'search' => $search,
            'room' => $room !== '' ? $room : 'all',
            'period' => in_array($period, array_merge(['all'], $allowedPeriods), true) ? $period : 'all',
            'schedule' => in_array($schedule, $allowedSchedules, true) ? $schedule : 'all',
            'roomOptions' => $roomOptions,
            'stats' => [
                'classes' => $totalClasses,
                'students' => (int) $totalStudents,
                'subjects' => (int) $totalSubjects,
            ],
            'periodLabels' => [
                'morning' => 'Morning',
                'afternoon' => 'Afternoon',
                'evening' => 'Evening',
                'night' => 'Night',
                'custom' => 'Custom',
            ],
        ]);
    }
}
