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
            $notifs = Notification::latest()->take(10)->get();
            $unreadCount = Notification::where('is_read', false)->count();
            $contactMessages = ContactMessage::latest()->take(10)->get();
            $contactUnreadCount = ContactMessage::where('is_read', false)->count();

            $view->with('navNotifs', $notifs)
                ->with('navUnread', $unreadCount)
                ->with('navContacts', $contactMessages)
                ->with('contactUnread', $contactUnreadCount);
        });
    }
}
