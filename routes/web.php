<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Website\TelegramBotController as WebsiteTelegramBotController;
use App\Http\Controllers\Website\ContactMessageController as WebsiteContactMessageController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $studentsTotal = User::where('role', 'student')->count();
    $teachersTotal = User::where('role', 'teacher')->count();
    $dashboardRoute = null;

    if (auth()->check()) {
        $dashboardRoute = match (strtolower(trim((string) auth()->user()->role))) {
            'admin' => 'admin.dashboard',
            'staff' => 'admin.dashboard',
            'teacher' => 'teacher.dashboard',
            default => 'student.dashboard',
        };
    }

    return view('website.home', [
        'studentsTotal' => $studentsTotal,
        'teachersTotal' => $teachersTotal,
        'dashboardRoute' => $dashboardRoute,
    ]);
})->name('home');

Route::get('/language/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, config('app.supported_locales', ['en', 'km']), true), 404);

    $request->session()->put('locale', $locale);

    $previousUrl = url()->previous();

    if (! $previousUrl || $previousUrl === $request->fullUrl()) {
        return redirect()->route('home');
    }

    return redirect()->to($previousUrl);
})->name('language.switch');

Route::get('/storage/{path}', function (string $path) {
    $path = ltrim(str_replace('\\', '/', $path), '/');

    if ($path === '' || str_contains($path, '..')) {
        abort(404);
    }

    $disk = Storage::disk('public');

    if (!$disk->exists($path)) {
        abort(404);
    }

    return $disk->response($path);
})->where('path', '.*')->name('public.storage');

Route::post('/contact', [WebsiteContactMessageController::class, 'store'])->name('contact.store');
Route::post('/telegram/webhook/{secret?}', [WebsiteTelegramBotController::class, 'webhook'])->name('telegram.webhook');

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
        Route::get('/login/otp', 'showLoginOtpForm')->name('login.otp.form');
        Route::post('/login/otp', 'verifyLoginOtp')->name('login.otp.verify');
        Route::post('/login/otp/resend', 'resendLoginOtp')->name('login.otp.resend');
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
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

    require __DIR__ . '/admin.php';
    require __DIR__ . '/teacher.php';
    require __DIR__ . '/student.php';
});
