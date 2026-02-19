<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::view('/dashboard', 'teacher.dashboard')->name('dashboard');
        Route::view('/classes', 'teacher.classes')->name('classes.index');
        Route::view('/students', 'teacher.students')->name('students.index');
        Route::view('/attendance', 'teacher.attendance')->name('attendance.index');
        Route::view('/assignments', 'teacher.assignments')->name('assignments.index');
        Route::view('/grades', 'teacher.grades')->name('grades.index');
        Route::view('/notices', 'teacher.notices')->name('notices.index');
        Route::view('/settings', 'teacher.settings')->name('settings');

        Route::post('/notifications/read-all', fn() => back())->name('notifications.readAll');
    });
