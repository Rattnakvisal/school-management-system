<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MissionEvent;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class MissionEventController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $audience = trim((string) $request->query('audience', 'all'));
        $priority = trim((string) $request->query('priority', 'all'));
        $status = trim((string) $request->query('status', 'all'));

        $missions = MissionEvent::query()
            ->with('creator:id,name,role')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when(in_array($audience, ['teacher', 'staff'], true), fn($query) => $query->where('audience', $audience))
            ->when(in_array($priority, ['low', 'normal', 'high', 'urgent'], true), fn($query) => $query->where('priority', $priority))
            ->when($status === 'active', fn($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn($query) => $query->where('is_active', false))
            ->latest('starts_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $this->attachSubmissionDetails($missions->getCollection());

        $stats = [
            'total' => MissionEvent::query()->count(),
            'active' => MissionEvent::query()->where('is_active', true)->count(),
            'teacher' => MissionEvent::query()->whereIn('audience', ['teacher', 'all'])->where('is_active', true)->count(),
            'staff' => MissionEvent::query()->whereIn('audience', ['staff', 'all'])->where('is_active', true)->count(),
            'submissions' => $this->submissionCount(),
        ];

        return view('admin.mission', [
            'missions' => $missions,
            'stats' => $stats,
            'search' => $search,
            'audience' => $audience,
            'priority' => $priority,
            'status' => $status,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedMissionData($request);

        $mission = MissionEvent::create([
            ...$validated,
            'created_by' => $request->user()?->id,
            'is_active' => true,
        ]);

        $this->sendMissionNotification($mission);

        return redirect()
            ->route('admin.mission.index')
            ->with('success', 'Mission event created and sent.');
    }

    public function update(Request $request, MissionEvent $mission): RedirectResponse
    {
        $mission->update($this->validatedMissionData($request));

        return redirect()
            ->route('admin.mission.index')
            ->with('success', 'Mission event updated.');
    }

    public function toggleStatus(MissionEvent $mission): RedirectResponse
    {
        $mission->update(['is_active' => ! $mission->is_active]);

        return back()->with('success', 'Mission event status updated.');
    }

    public function destroy(MissionEvent $mission): RedirectResponse
    {
        $mission->delete();

        return back()->with('success', 'Mission event deleted.');
    }

    private function sendMissionNotification(MissionEvent $mission): void
    {
        $targets = match ($mission->audience) {
            'teacher' => ['teacher' => route('teacher.missions.index')],
            'staff' => ['staff' => route('staff.missions.index')],
            default => [
                'teacher' => route('teacher.missions.index'),
                'staff' => route('staff.missions.index'),
            ],
        };

        foreach ($targets as $role => $url) {
            Notification::create([
                'type' => 'mission_event_' . $role,
                'title' => 'New mission event: ' . $mission->title,
                'message' => $this->notificationMessage($mission),
                'url' => $url,
                'is_read' => false,
            ]);
        }
    }

    private function notificationMessage(MissionEvent $mission): string
    {
        $parts = [];

        if ($mission->starts_at) {
            $parts[] = 'Start: ' . $mission->starts_at->format('M d, Y h:i A');
        }

        $parts[] = str($mission->description)->limit(140)->toString();

        return implode(' | ', $parts);
    }

    private function attachSubmissionDetails(Collection $missions): void
    {
        $missionIds = $missions->pluck('id')->map(fn($id) => (int) $id)->filter()->values();

        if ($missionIds->isEmpty()) {
            return;
        }

        $submissions = collect();

        if (Schema::hasTable('mission_event_teacher_submissions')) {
            $submissions = $submissions->merge(
                DB::table('mission_event_teacher_submissions as submissions')
                    ->join('users', 'users.id', '=', 'submissions.teacher_id')
                    ->whereIn('submissions.mission_event_id', $missionIds)
                    ->whereNotNull('submissions.submission_file_path')
                    ->select([
                        'submissions.mission_event_id',
                        'submissions.submission_file_path',
                        'submissions.submission_file_name',
                        'submissions.submission_file_mime',
                        'submissions.submission_file_size',
                        'submissions.submitted_at',
                        'users.id as submitter_id',
                        'users.name as submitter_name',
                        'users.email as submitter_email',
                        DB::raw("'Teacher' as submitter_role"),
                    ])
                    ->get()
            );
        }

        if (Schema::hasTable('mission_event_staff_submissions')) {
            $submissions = $submissions->merge(
                DB::table('mission_event_staff_submissions as submissions')
                    ->join('users', 'users.id', '=', 'submissions.staff_id')
                    ->whereIn('submissions.mission_event_id', $missionIds)
                    ->whereNotNull('submissions.submission_file_path')
                    ->select([
                        'submissions.mission_event_id',
                        'submissions.submission_file_path',
                        'submissions.submission_file_name',
                        'submissions.submission_file_mime',
                        'submissions.submission_file_size',
                        'submissions.submitted_at',
                        'users.id as submitter_id',
                        'users.name as submitter_name',
                        'users.email as submitter_email',
                        DB::raw("'Staff' as submitter_role"),
                    ])
                    ->get()
            );
        }

        $grouped = $submissions
            ->sortByDesc('submitted_at')
            ->groupBy('mission_event_id');

        $missions->each(function (MissionEvent $mission) use ($grouped) {
            $details = $grouped->get($mission->id, collect())->values();

            $mission->setAttribute('submission_details', $details);
            $mission->setAttribute('submission_count', $details->count());
            $mission->setAttribute('latest_submission', $details->first());
        });
    }

    private function submissionCount(): int
    {
        $count = 0;

        if (Schema::hasTable('mission_event_teacher_submissions')) {
            $count += (int) DB::table('mission_event_teacher_submissions')
                ->whereNotNull('submission_file_path')
                ->count();
        }

        if (Schema::hasTable('mission_event_staff_submissions')) {
            $count += (int) DB::table('mission_event_staff_submissions')
                ->whereNotNull('submission_file_path')
                ->count();
        }

        return $count;
    }

    private function validatedMissionData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'audience' => ['required', Rule::in(['all', 'teacher', 'staff'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
        ]);
    }
}
