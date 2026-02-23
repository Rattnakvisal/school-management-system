<?php

use App\Http\Controllers\Teacher\ClassController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
        Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
        Route::view('/students', 'teacher.students')->name('students.index');
        Route::view('/attendance', 'teacher.attendance')->name('attendance.index');
        Route::view('/assignments', 'teacher.assignments')->name('assignments.index');
        Route::view('/grades', 'teacher.grades')->name('grades.index');
        Route::view('/notices', 'teacher.notices')->name('notices.index');
        Route::view('/settings', 'teacher.settings')->name('settings');

        Route::post('/notifications/read-all', fn() => back())->name('notifications.readAll');
    });
