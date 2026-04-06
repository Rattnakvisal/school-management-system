<?php

use App\Http\Controllers\Teacher\ClassController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\NotificationController;
use App\Http\Controllers\Teacher\ScheduleController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\LawRequestController;
use App\Http\Controllers\Teacher\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
        Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/law-requests', [LawRequestController::class, 'index'])->name('law-requests.index');
        Route::post('/law-requests', [LawRequestController::class, 'store'])->name('law-requests.store');
        Route::put('/law-requests/{lawRequest}', [LawRequestController::class, 'update'])->name('law-requests.update');
        Route::delete('/law-requests/{lawRequest}', [LawRequestController::class, 'destroy'])->name('law-requests.destroy');
        Route::view('/assignments', 'teacher.assignments')->name('assignments.index');
        Route::view('/grades', 'teacher.grades')->name('grades.index');
        Route::view('/notices', 'teacher.notices')->name('notices.index');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
        Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');

        Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    });
