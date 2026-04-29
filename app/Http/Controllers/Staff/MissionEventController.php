<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MissionEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MissionEventController extends Controller
{
    public function index(Request $request)
    {
        $staff = $request->user();
        abort_unless($staff instanceof User, 403);

        $missionQuery = MissionEvent::query()
            ->visibleToRole('staff')
            ->with('creator:id,name,role');

        $missions = (clone $missionQuery)
            ->latest('starts_at')
            ->latest()
            ->paginate(12);

        $missionIds = $missions->getCollection()->pluck('id')->map(fn($id) => (int) $id)->all();
        $submissions = DB::table('mission_event_staff_submissions')
            ->where('staff_id', (int) $staff->id)
            ->whereIn('mission_event_id', $missionIds)
            ->get()
            ->keyBy('mission_event_id');

        return view('staff.missions', [
            'missions' => $missions,
            'missionSubmissions' => $submissions,
            'submitRouteName' => 'staff.missions.submit',
            'deleteRouteName' => 'staff.missions.submission.destroy',
        ]);
    }

    public function submit(Request $request, MissionEvent $mission): RedirectResponse
    {
        $staff = $request->user();
        abort_unless($staff instanceof User, 403);
        $this->authorizeVisibleMission($mission);

        $request->validate([
            'submission_file' => ['required', 'file', 'max:10240'],
        ]);

        $file = $request->file('submission_file');
        if (!$file || !$file->isValid()) {
            return back()->withErrors(['submission_file' => 'Please choose a valid file before sending.']);
        }

        $pivot = DB::table('mission_event_staff_submissions')
            ->where('mission_event_id', (int) $mission->id)
            ->where('staff_id', (int) $staff->id)
            ->first();

        $oldPath = trim((string) ($pivot?->submission_file_path ?? ''));
        if ($oldPath !== '') {
            Storage::disk('public')->delete($oldPath);
        }

        $path = $file->store('mission-submissions', 'public');

        DB::table('mission_event_staff_submissions')->updateOrInsert(
            [
                'mission_event_id' => (int) $mission->id,
                'staff_id' => (int) $staff->id,
            ],
            [
                'submission_file_path' => $path,
                'submission_file_name' => $file->getClientOriginalName(),
                'submission_file_mime' => $file->getClientMimeType(),
                'submission_file_size' => $file->getSize(),
                'submitted_at' => now(),
                'created_at' => $pivot?->created_at ?? now(),
                'updated_at' => now(),
            ],
        );

        Notification::query()->create([
            'type' => 'staff_mission_submitted',
            'title' => 'Mission file sent',
            'message' => trim((string) $staff->name)
                . ' sent "' . $file->getClientOriginalName() . '" for mission "' . trim((string) $mission->title) . '".',
            'url' => route('admin.mission.index') . '#mission-' . $mission->id,
            'is_read' => false,
        ]);

        return back()
            ->with('success', 'Mission file sent to admin.')
            ->with('success_title', 'Sent to admin');
    }

    public function destroySubmission(Request $request, MissionEvent $mission): RedirectResponse
    {
        $staff = $request->user();
        abort_unless($staff instanceof User, 403);
        $this->authorizeVisibleMission($mission);

        $pivot = DB::table('mission_event_staff_submissions')
            ->where('mission_event_id', (int) $mission->id)
            ->where('staff_id', (int) $staff->id)
            ->first();

        $oldPath = trim((string) ($pivot?->submission_file_path ?? ''));
        if ($oldPath === '') {
            return back()->withErrors(['submission_file' => 'No mission file found to remove.']);
        }

        Storage::disk('public')->delete($oldPath);

        DB::table('mission_event_staff_submissions')
            ->where('mission_event_id', (int) $mission->id)
            ->where('staff_id', (int) $staff->id)
            ->update([
                'submission_file_path' => null,
                'submission_file_name' => null,
                'submission_file_mime' => null,
                'submission_file_size' => null,
                'submitted_at' => null,
                'updated_at' => now(),
            ]);

        return back()
            ->with('success', 'Mission file removed.')
            ->with('success_title', 'Mission file removed');
    }

    private function authorizeVisibleMission(MissionEvent $mission): void
    {
        abort_unless(
            (bool) $mission->is_active && in_array((string) $mission->audience, ['staff', 'all'], true),
            403,
        );
    }
}
