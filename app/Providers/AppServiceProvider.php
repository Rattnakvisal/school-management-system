<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer([
            'layout.admin.navbar.navbar',
            'layout.teacher.navbar',
        ], function ($view) {
            $role = strtolower((string) (auth()->user()?->role ?? ''));
            $userId = (int) (auth()->id() ?? 0);
            $notifQuery = Notification::query()->latest();
            $unreadQuery = Notification::query()->where('is_read', false);

            // Teacher law-request notifications are admin workflow alerts.
            if ($role === 'teacher') {
                $teacherTag = '[teacher_id:' . $userId . ']';

                $notifQuery->where('type', '!=', 'teacher_law_request')
                    ->where(function ($query) use ($teacherTag) {
                        $query->whereNotIn('type', ['teacher_law_request_approved', 'teacher_attendance_checked'])
                            ->orWhere('message', 'like', '%' . $teacherTag . '%');
                    });

                $unreadQuery->where('type', '!=', 'teacher_law_request')
                    ->where(function ($query) use ($teacherTag) {
                        $query->whereNotIn('type', ['teacher_law_request_approved', 'teacher_attendance_checked'])
                            ->orWhere('message', 'like', '%' . $teacherTag . '%');
                    });
            } elseif ($role === 'admin') {
                // Approval notifications are for teachers.
                $notifQuery->whereNotIn('type', ['teacher_law_request_approved', 'teacher_attendance_checked']);
                $unreadQuery->whereNotIn('type', ['teacher_law_request_approved', 'teacher_attendance_checked']);
            } else {
                // Non-admin/non-teacher users should not see admin/teacher workflow alerts.
                $notifQuery->whereNotIn('type', ['teacher_law_request', 'teacher_law_request_approved', 'teacher_attendance_checked']);
                $unreadQuery->whereNotIn('type', ['teacher_law_request', 'teacher_law_request_approved', 'teacher_attendance_checked']);
            }

            $notifs = $notifQuery->take(10)->get();
            $unreadCount = $unreadQuery->count();
            $contactMessages = ContactMessage::latest()->take(10)->get();
            $contactUnreadCount = ContactMessage::where('is_read', false)->count();

            $view->with('navNotifs', $notifs)
                ->with('navUnread', $unreadCount)
                ->with('navContacts', $contactMessages)
                ->with('contactUnread', $contactUnreadCount);
        });
    }
}
