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
            'layout.students.navbar',
        ], function ($view) {
            $role = strtolower((string) (auth()->user()?->role ?? ''));
            $userId = (int) (auth()->id() ?? 0);
            $notifQuery = Notification::query()->latest();
            $unreadQuery = Notification::query()->where('is_read', false);
            $teacherOnlyTypes = ['teacher_law_request_approved', 'teacher_attendance_checked', 'student_law_request'];
            $studentOnlyTypes = ['student_law_request_approved', 'student_attendance_checked', 'student_assignment_posted', 'student_grade_posted'];

            // Teacher law-request notifications are admin workflow alerts.
            if ($role === 'teacher') {
                $teacherTag = '[teacher_id:' . $userId . ']';

                $notifQuery->where('type', '!=', 'teacher_law_request')
                    ->whereNotIn('type', $studentOnlyTypes)
                    ->where(function ($query) use ($teacherTag, $teacherOnlyTypes) {
                        $query->whereNotIn('type', $teacherOnlyTypes)
                            ->orWhere('message', 'like', '%' . $teacherTag . '%');
                    });

                $unreadQuery->where('type', '!=', 'teacher_law_request')
                    ->whereNotIn('type', $studentOnlyTypes)
                    ->where(function ($query) use ($teacherTag, $teacherOnlyTypes) {
                        $query->whereNotIn('type', $teacherOnlyTypes)
                            ->orWhere('message', 'like', '%' . $teacherTag . '%');
                    });
            } elseif ($role === 'admin') {
                // Approval notifications are for teachers.
                $notifQuery->whereNotIn('type', array_merge($teacherOnlyTypes, $studentOnlyTypes));
                $unreadQuery->whereNotIn('type', array_merge($teacherOnlyTypes, $studentOnlyTypes));
            } else {
                // Student notifications can include targeted student alerts.
                $studentTag = '[student_id:' . $userId . ']';
                $notifQuery->whereNotIn('type', array_merge(['teacher_law_request'], $teacherOnlyTypes))
                    ->where(function ($query) use ($studentOnlyTypes, $studentTag) {
                        $query->whereNotIn('type', $studentOnlyTypes)
                            ->orWhere('message', 'like', '%' . $studentTag . '%');
                    });
                $unreadQuery->whereNotIn('type', array_merge(['teacher_law_request'], $teacherOnlyTypes))
                    ->where(function ($query) use ($studentOnlyTypes, $studentTag) {
                        $query->whereNotIn('type', $studentOnlyTypes)
                            ->orWhere('message', 'like', '%' . $studentTag . '%');
                    });
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
