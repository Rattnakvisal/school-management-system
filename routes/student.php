<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::view('/dashboard', 'student.dashboard')->name('dashboard');
        Route::view('/subjects', 'student.subjects')->name('subjects.index');
        Route::view('/attendance', 'student.attendance')->name('attendance.index');
        Route::view('/grades', 'student.grades')->name('grades.index');
        Route::view('/schedule', 'student.schedule')->name('schedule.index');
        Route::view('/notices', 'student.notices')->name('notices.index');
        Route::view('/settings', 'student.settings')->name('settings');
    });
