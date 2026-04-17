<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\StudentStudyController;
use App\Http\Controllers\Admin\TimeStudyController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\TeacherAttendanceController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/export/pdf', [StudentController::class, 'exportPdf'])->name('students.export.pdf');
        Route::get('/students/export/excel', [StudentController::class, 'exportExcel'])->name('students.export.excel');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::patch('/students/{student}/status', [StudentController::class, 'toggleStatus'])->name('students.status');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/export/pdf', [TeacherController::class, 'exportPdf'])->name('teachers.export.pdf');
        Route::get('/teachers/export/excel', [TeacherController::class, 'exportExcel'])->name('teachers.export.excel');
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
        Route::get('/time-studies', [TimeStudyController::class, 'index'])->name('time-studies.index');
        Route::post('/time-studies/classes', [TimeStudyController::class, 'storeClass'])->name('time-studies.classes.store');
        Route::put('/time-studies/classes/{classStudyTime}', [TimeStudyController::class, 'updateClass'])->name('time-studies.classes.update');
        Route::delete('/time-studies/classes/{classStudyTime}', [TimeStudyController::class, 'destroyClass'])->name('time-studies.classes.destroy');
        Route::post('/time-studies/subjects', [TimeStudyController::class, 'storeSubject'])->name('time-studies.subjects.store');
        Route::put('/time-studies/subjects/{subjectStudyTime}', [TimeStudyController::class, 'updateSubject'])->name('time-studies.subjects.update');
        Route::delete('/time-studies/subjects/{subjectStudyTime}', [TimeStudyController::class, 'destroySubject'])->name('time-studies.subjects.destroy');
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/teachers', [TeacherAttendanceController::class, 'index'])->name('attendance.teachers.index');
        Route::post('/attendance/teachers', [TeacherAttendanceController::class, 'store'])->name('attendance.teachers.store');
        Route::post('/attendance/teachers/law-requests/{lawRequest}/approve', [TeacherAttendanceController::class, 'approveLawRequest'])
            ->name('attendance.teachers.law-requests.approve');
        Route::post('/attendance/teachers/law-requests/{lawRequest}/reject', [TeacherAttendanceController::class, 'rejectLawRequest'])
            ->name('attendance.teachers.law-requests.reject');
        Route::view('/mission', 'admin.mission')->name('mission.index');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
        Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
        // Mark all admin notifications as read
        Route::post('/notifications/read-all', function () {
            \App\Models\Notification::where('is_read', false)->update(['is_read' => true]);
            return back();
        })->name('notifications.readAll');
    });
