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
    public function poll(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = $this->teacherNotificationQuery($user?->id ?? 0, $user?->role ?? '');

        $notifications = (clone $query)
            ->latest()
            ->take(10)
            ->get(['id', 'type', 'title', 'message', 'url', 'is_read', 'created_at'])
            ->map(function (Notification $notification) {
                return [
                    'id' => (int) $notification->id,
                    'type' => (string) ($notification->type ?? ''),
                    'title' => trim((string) ($notification->title ?? 'Notification')),
                    'message' => $this->cleanNotificationText((string) ($notification->message ?? '')),
                    'url' => trim((string) ($notification->url ?? '')),
                    'is_read' => (bool) ($notification->is_read ?? false),
                    'created_at_human' => $notification->created_at?->diffForHumans(),
                ];
            })
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

        if ($role === 'teacher') {
            $query->where('type', '!=', 'teacher_law_request')
                ->where(function ($inner) use ($teacherTag) {
                    $inner->whereNotIn('type', ['teacher_law_request_approved', 'teacher_attendance_checked'])
                        ->orWhere('message', 'like', '%' . $teacherTag . '%');
                });
        } elseif ($role === 'admin') {
            $query->whereNotIn('type', ['teacher_law_request_approved', 'teacher_attendance_checked']);
        } else {
            $query->whereNotIn('type', ['teacher_law_request', 'teacher_law_request_approved', 'teacher_attendance_checked']);
        }

        return $query;
    }

    private function cleanNotificationText(string $text): string
    {
        $clean = trim($text);
        $clean = preg_replace('/\[teacher_id:\d+\]\s*/', '', $clean);

        return trim((string) $clean);
    }
}
