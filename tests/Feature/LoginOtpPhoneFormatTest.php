<?php

use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('admin can login with local phone when account is stored with +855 format', function () {
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    $user = User::factory()->create([
        'role' => 'admin',
        'phone_number' => '+85512345678',
        'password' => bcrypt('Password123!'),
        'telegram_chat_id' => '123456700',
        'is_active' => true,
    ]);

    $this->mock(TelegramBotService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('sendMessage')
            ->once()
            ->andReturn(true);
    });

    $response = $this->post(route('login.submit'), [
        'login' => '012345678',
        'password' => 'Password123!',
    ]);

    $response->assertRedirect(route('login.otp.form'));
    $response->assertSessionHas('success', 'OTP sent to your Telegram.');
    $this->assertGuest();

    $otpRow = DB::table('login_otps')->where('user_id', $user->id)->first();
    expect($otpRow)->not->toBeNull();
});

test('admin can login with +855 phone when account is stored with local phone format', function () {
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    $user = User::factory()->create([
        'role' => 'admin',
        'phone_number' => '012345678',
        'password' => bcrypt('Password123!'),
        'telegram_chat_id' => '123456701',
        'is_active' => true,
    ]);

    $this->mock(TelegramBotService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('sendMessage')
            ->once()
            ->andReturn(true);
    });

    $response = $this->post(route('login.submit'), [
        'login' => '+85512345678',
        'password' => 'Password123!',
    ]);

    $response->assertRedirect(route('login.otp.form'));
    $response->assertSessionHas('success', 'OTP sent to your Telegram.');
    $this->assertGuest();

    $otpRow = DB::table('login_otps')->where('user_id', $user->id)->first();
    expect($otpRow)->not->toBeNull();
});

test('student login by email sends otp to telegram challenge', function () {
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    $user = User::factory()->create([
        'role' => 'student',
        'email' => 'student@example.com',
        'phone_number' => '+85588812345',
        'telegram_chat_id' => '123456789',
        'password' => bcrypt('Password123!'),
        'is_active' => true,
    ]);

    $this->mock(TelegramBotService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('sendMessage')
            ->once()
            ->andReturn(true);
    });

    $response = $this->post(route('login.submit'), [
        'login' => 'student@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertRedirect(route('login.otp.form'));
    $response->assertSessionHas('success', 'OTP sent to your Telegram.');

    $otpRow = DB::table('login_otps')->where('user_id', $user->id)->first();
    expect($otpRow)->not->toBeNull();
});

test('admin login by email sends otp to telegram challenge', function () {
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    $user = User::factory()->create([
        'role' => 'admin',
        'email' => 'admin@example.com',
        'phone_number' => '+85588812346',
        'telegram_chat_id' => '987654321',
        'password' => bcrypt('Password123!'),
        'is_active' => true,
    ]);

    $this->mock(TelegramBotService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('sendMessage')
            ->once()
            ->andReturn(true);
    });

    $response = $this->post(route('login.submit'), [
        'login' => 'admin@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertRedirect(route('login.otp.form'));
    $response->assertSessionHas('success', 'OTP sent to your Telegram.');

    $otpRow = DB::table('login_otps')->where('user_id', $user->id)->first();
    expect($otpRow)->not->toBeNull();
});
