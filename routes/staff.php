<?php

use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\MissionEventController;
use App\Models\Notification;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/missions', [MissionEventController::class, 'index'])->name('missions.index');
        Route::post('/missions/{mission}/submit', [MissionEventController::class, 'submit'])->name('missions.submit');
        Route::delete('/missions/{mission}/submission', [MissionEventController::class, 'destroySubmission'])->name('missions.submission.destroy');

        Route::post('/notifications/read-all', function () {
            Notification::query()->where('is_read', false)->update(['is_read' => true]);

            return back();
        })->name('notifications.readAll');
    });
