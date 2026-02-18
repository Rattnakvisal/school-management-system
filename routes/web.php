<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return match (auth()->user()->role) {
        'admin'   => redirect()->route('admin.dashboard'),
        'teacher' => redirect()->route('teacher.dashboard'),
        default   => redirect()->route('student.dashboard'),
    };
})->name('home');


/*
|--------------------------------------------------------------------------
| Guest Routes (Login / Register / Google)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // Login / Register
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // Google Login (Student Only)
    Route::prefix('auth')->controller(GoogleController::class)->group(function () {
        Route::get('/google', 'authProviderRedirect')->name('google.redirect');
    });
});

// Keep callback public so it can complete OAuth flow reliably.
Route::prefix('auth')->controller(GoogleController::class)->group(function () {
    Route::get('/google/callback', 'socialAuthentication')->name('google.callback');
});


/*
|--------------------------------------------------------------------------
| Auth Routes (Logout + Dashboards)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Dashboard
    Route::get('/admin/dashboard', function () {
        abort_unless(auth()->user()->role === 'admin', 403);
        return view('admin.dashboard', ['title' => 'Admin Dashboard']);
    })->name('admin.dashboard');

    // Teacher Dashboard
    Route::get('/teacher/dashboard', function () {
        abort_unless(auth()->user()->role === 'teacher', 403);
        return view('teacher.dashboard', ['title' => 'Teacher Dashboard']);
    })->name('teacher.dashboard');

    // Student Dashboard
    Route::get('/student/dashboard', function () {
        abort_unless(auth()->user()->role === 'student', 403);
        return view('student.dashboard', ['title' => 'Student Dashboard']);
    })->name('student.dashboard');
});
