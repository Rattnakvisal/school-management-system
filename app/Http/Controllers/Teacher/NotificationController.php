<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $this->teacherNotificationQuery($user?->id ?? 0, $user?->role ?? '');

        $notifications = (clone $query)
            ->latest()
            ->take(20)
            ->get(['id', 'type', 'title', 'message', 'url', 'is_read', 'created_at'])
            ->map(fn(Notification $notification) => $this->formatNotification($notification))
            ->values()
            ->all();

        $latestId = (int) ((clone $query)->max('id') ?? 0);
        $unreadCount = (int) ((clone $query)->where('is_read', false)->count());

        return view('teacher.notices', [
            'notifications' => $notifications,
            'latestId' => $latestId,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function poll(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = $this->teacherNotificationQuery($user?->id ?? 0, $user?->role ?? '');

        $notifications = (clone $query)
            ->latest()
            ->take(10)
            ->get(['id', 'type', 'title', 'message', 'url', 'is_read', 'created_at'])
            ->map(fn(Notification $notification) => $this->formatNotification($notification))
            ->values()
            ->all();

        $latestId = (int) ((clone $query)->max('id') ?? 0);
        $unreadCount = (int) ((clone $query)->where('is_read', false)->count());

        return response()->json([
            'latest_id' => $latestId,
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function readAll(Request $request): RedirectResponse
    {
        $user = $request->user();
        $query = $this->teacherNotificationQuery($user?->id ?? 0, $user?->role ?? '');

        if ($query->exists()) {
            $query->where('is_read', false)->update(['is_read' => true]);
        }

        return back()->with('success', 'Notifications marked as read.');
    }

    private function teacherNotificationQuery(int $userId, string $role): Builder
    {
        $query = Notification::query();
        $role = strtolower(trim($role));
        $teacherTag = '[teacher_id:' . $userId . ']';
        $teacherOnlyTypes = ['teacher_law_request_approved', 'teacher_attendance_checked', 'student_law_request'];

        if ($role === 'teacher') {
            $query->where('type', '!=', 'teacher_law_request')
                ->where(function ($inner) use ($teacherTag, $teacherOnlyTypes) {
                    $inner->whereNotIn('type', $teacherOnlyTypes)
                        ->orWhere('message', 'like', '%' . $teacherTag . '%');
                });
        } elseif ($role === 'admin') {
            $query->whereNotIn('type', $teacherOnlyTypes);
        } else {
            $query->whereNotIn('type', array_merge(['teacher_law_request'], $teacherOnlyTypes));
        }

        return $query;
    }

    private function cleanNotificationText(string $text): string
    {
        $clean = trim($text);
        $clean = preg_replace('/\[teacher_id:\d+\]\s*/', '', $clean);

        return trim((string) $clean);
    }

    private function formatNotification(Notification $notification): array
    {
        return [
            'id' => (int) $notification->id,
            'type' => (string) ($notification->type ?? ''),
            'title' => trim((string) ($notification->title ?? 'Notification')),
            'message' => $this->cleanNotificationText((string) ($notification->message ?? '')),
            'url' => trim((string) ($notification->url ?? '')),
            'is_read' => (bool) ($notification->is_read ?? false),
            'created_at_human' => $notification->created_at?->diffForHumans(),
        ];
    }
}
