<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MissionEvent;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('location', 'like', '%' . $search . '%');
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

        $stats = [
            'total' => MissionEvent::query()->count(),
            'active' => MissionEvent::query()->where('is_active', true)->count(),
            'teacher' => MissionEvent::query()->whereIn('audience', ['teacher', 'all'])->where('is_active', true)->count(),
            'staff' => MissionEvent::query()->whereIn('audience', ['staff', 'all'])->where('is_active', true)->count(),
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

        if ($mission->location) {
            $parts[] = 'Location: ' . $mission->location;
        }

        $parts[] = str($mission->description)->limit(140)->toString();

        return implode(' | ', $parts);
    }

    private function validatedMissionData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'audience' => ['required', Rule::in(['all', 'teacher', 'staff'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
        ]);
    }
}
