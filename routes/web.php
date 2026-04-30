<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\HomePageItem;
use App\Models\User;
use App\Support\HomePageViewData;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Website\TelegramBotController as WebsiteTelegramBotController;
use App\Http\Controllers\Website\ContactMessageController as WebsiteContactMessageController;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $studentsTotal = User::where('role', 'student')->count();
    $teachersTotal = User::where('role', 'teacher')->count();
    $dashboardRoute = null;
    $homePageItems = collect();

    if (auth()->check()) {
        $dashboardRoute = match (strtolower(trim((string) auth()->user()->role))) {
            'admin' => 'admin.dashboard',
            'staff' => 'staff.dashboard',
            'teacher' => 'teacher.dashboard',
            default => 'student.dashboard',
        };
    }

    if (Schema::hasTable('home_page_items')) {
        $homePageItems = HomePageItem::query()
            ->where('is_active', true)
            ->orderBy('section')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('section');
    }

    $viewData = [
        'studentsTotal' => $studentsTotal,
        'teachersTotal' => $teachersTotal,
        'dashboardRoute' => $dashboardRoute,
        'homePageItems' => $homePageItems,
    ];

    return view('website.home', array_merge(
        $viewData,
        HomePageViewData::make($homePageItems, $studentsTotal, $teachersTotal, $dashboardRoute),
    ));
})->name('home');

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
    require __DIR__ . '/staff.php';
    require __DIR__ . '/teacher.php';
    require __DIR__ . '/student.php';
});
