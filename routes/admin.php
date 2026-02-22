<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\StudentStudyController;
use App\Http\Controllers\Admin\ContactMessageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::patch('/students/{student}/status', [StudentController::class, 'toggleStatus'])->name('students.status');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::patch('/teachers/{teacher}/status', [TeacherController::class, 'toggleStatus'])->name('teachers.status');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
        Route::get('/contacts', [ContactMessageController::class, 'index'])->name('contacts.index');
        Route::patch('/contacts/{contactMessage}/read', [ContactMessageController::class, 'markRead'])->name('contacts.read');
        Route::post('/contacts/read-all', [ContactMessageController::class, 'markAllRead'])->name('contacts.readAll');
        Route::delete('/contacts/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contacts.destroy');
        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
        Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
        Route::put('/classes/{schoolClass}', [ClassController::class, 'update'])->name('classes.update');
        Route::patch('/classes/{schoolClass}/status', [ClassController::class, 'toggleStatus'])->name('classes.status');
        Route::delete('/classes/{schoolClass}', [ClassController::class, 'destroy'])->name('classes.destroy');

        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
        Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::patch('/subjects/{subject}/status', [SubjectController::class, 'toggleStatus'])->name('subjects.status');
        Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
        Route::get('/student-study', [StudentStudyController::class, 'index'])->name('student-study.index');
        Route::view('/attendance', 'admin.attendance')->name('attendance.index');
        Route::view('/exams', 'admin.exams')->name('exams.index');
        Route::view('/settings', 'admin.settings')->name('settings');
        // Mark all admin notifications as read
        Route::post('/notifications/read-all', function () {
            \App\Models\Notification::where('is_read', false)->update(['is_read' => true]);
            return back();
        })->name('notifications.readAll');
    });
