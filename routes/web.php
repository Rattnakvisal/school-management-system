<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\PasswordOtpController;

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
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Auth (Email/Password)
    |--------------------------------------------------------------------------
    */
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/login', 'login')->name('login.submit');

        Route::get('/register', 'showRegister')->name('register');
        Route::post('/register', 'register')->name('register.submit');
    });

    /*
    |--------------------------------------------------------------------------
    | Forgot Password (OTP)
    |--------------------------------------------------------------------------
    */
    Route::prefix('forgot-password')->name('password.')->controller(PasswordOtpController::class)->group(function () {
        Route::get('/', 'requestForm')->name('request');          // GET /forgot-password
        Route::post('/', 'sendOtp')->name('email');               // POST /forgot-password

        Route::get('/verify', 'verifyForm')->name('verify');      // GET /forgot-password/verify
        Route::post('/verify', 'verifyOtp')->name('verify.post'); // POST /forgot-password/verify

        Route::get('/reset/{token}', 'resetForm')->name('reset'); // GET /forgot-password/reset/{token}
        Route::post('/reset', 'updatePassword')->name('update');  // POST /forgot-password/reset
    });

    /*
    |--------------------------------------------------------------------------
    | Google Auth
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->name('google.')->controller(GoogleController::class)->group(function () {
        Route::get('/google', 'authProviderRedirect')->name('redirect');
        Route::get('/google/callback', 'socialAuthentication')->name('callback');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    require __DIR__ . '/admin.php';
    require __DIR__ . '/teacher.php';
    require __DIR__ . '/student.php';
});
