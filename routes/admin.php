<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
        Route::view('/students', 'admin.students')->name('students.index');
        Route::view('/teachers', 'admin.teachers')->name('teachers.index');
        Route::view('/classes', 'admin.classes')->name('classes.index');
        Route::view('/subjects', 'admin.subjects')->name('subjects.index');
        Route::view('/attendance', 'admin.attendance')->name('attendance.index');
        Route::view('/exams', 'admin.exams')->name('exams.index');
        Route::view('/notices', 'admin.notices')->name('notices.index');
        Route::view('/settings', 'admin.settings')->name('settings');
        // Mark all admin notifications as read
        Route::post('/notifications/read-all', function () {
            \App\Models\Notification::where('is_read', false)->update(['is_read' => true]);
            return back();
        })->name('notifications.readAll');
    });
