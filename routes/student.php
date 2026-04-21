<?php

use App\Http\Controllers\Student\AssignmentController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\LawRequestController;
use App\Http\Controllers\Student\SubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/law-requests', [LawRequestController::class, 'index'])->name('law-requests.index');
        Route::post('/law-requests', [LawRequestController::class, 'store'])->name('law-requests.store');
        Route::put('/law-requests/{lawRequest}', [LawRequestController::class, 'update'])->name('law-requests.update');
        Route::delete('/law-requests/{lawRequest}', [LawRequestController::class, 'destroy'])->name('law-requests.destroy');
        Route::redirect('/attendance', '/student/dashboard');
        Route::view('/grades', 'student.grades')->name('grades.index');
        Route::redirect('/Assignment', '/student/assignments');
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignment.index');
        Route::get('/notices', [NotificationController::class, 'index'])->name('notices.index');
        Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::view('/settings', 'student.settings')->name('settings');
    });
