<?php

use App\Mail\RegisterOtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('user must verify registration otp before account is activated', function () {
    Mail::fake();

    $response = $this->post(route('register.submit'), [
        'name' => 'OTP User',
        'email' => 'otp-user@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect(route('register.verify'));
    $response->assertSessionHas('email', 'otp-user@example.com');

    $user = User::where('email', 'otp-user@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->is_active)->toBeFalse();
    expect($user->email_verified_at)->toBeNull();

    Mail::assertSent(RegisterOtpMail::class, function (RegisterOtpMail $mail) {
        return true;
    });

    $otp = DB::table('register_otps')
        ->where('email', 'otp-user@example.com')
        ->value('otp');

    expect($otp)->not->toBeNull();

    $verifyResponse = $this->post(route('register.verify.post'), [
        'email' => 'otp-user@example.com',
        'otp' => $otp,
    ]);

    $verifyResponse->assertRedirect(route('login'));

    $user->refresh();
    expect($user->is_active)->toBeTrue();
    expect($user->email_verified_at)->not->toBeNull();

    $otpRow = DB::table('register_otps')
        ->where('email', 'otp-user@example.com')
        ->first();

    expect($otpRow)->toBeNull();
});
