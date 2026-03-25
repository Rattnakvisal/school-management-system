<?php

namespace App\Services;

use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class LoginOtpService
{
    public function __construct(
        protected TelegramBotService $telegramBotService
    ) {}

    public function requiresOtp(User $user, ?string $loginMethod = null, ?string $loginIdentifier = null): bool
    {
        $method = strtolower(trim((string) ($loginMethod ?? '')));

        if ($method === 'google') {
            return true;
        }

        if ($method === 'password' && $this->looksLikePhoneIdentifier((string) ($loginIdentifier ?? ''))) {
            return true;
        }

        return in_array((string) $user->role, ['teacher', 'student'], true);
    }

    public function issueAndSend(User $user): array
    {
        $phoneNumber = trim((string) ($user->phone_number ?? ''));
        if ($phoneNumber === '') {
            return [
                'ok' => false,
                'message' => 'Your account does not have a phone number. Please contact administrator.',
            ];
        }

        $chatId = trim((string) ($user->telegram_chat_id ?? ''));
        if ($chatId === '') {
            $this->trySyncTelegramLinkOnLocalhost($user);
            $chatId = trim((string) ($user->telegram_chat_id ?? ''));
        }

        if ($chatId === '') {
            return [
                'ok' => false,
                'message' => $this->telegramLinkHint($phoneNumber),
            ];
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresMinutes = $this->expiresMinutes();
        $expiresAt = now()->addMinutes($expiresMinutes);

        LoginOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
            ]);

        $row = LoginOtp::query()->create([
            'user_id' => $user->id,
            'otp_hash' => Hash::make($otp),
            'expires_at' => $expiresAt,
            'attempts' => 0,
        ]);

        $message = implode("\n", [
            'Login OTP',
            'Code: ' . $otp,
            'Expires in: ' . $expiresMinutes . ' minutes',
            'If this was not you, contact administrator.',
        ]);

        $sent = $this->telegramBotService->sendMessage($chatId, $message);
        if (!$sent) {
            $row->delete();

            return [
                'ok' => false,
                'message' => 'Failed to send OTP to Telegram. Please try again.',
            ];
        }

        return [
            'ok' => true,
            'message' => null,
        ];
    }

    public function verify(User $user, string $otp): bool
    {
        $otp = trim($otp);
        if ($otp === '') {
            return false;
        }

        $row = LoginOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        if (!$row) {
            return false;
        }

        if ($row->expires_at->isPast()) {
            return false;
        }

        if ((int) $row->attempts >= $this->maxAttempts()) {
            return false;
        }

        if (!Hash::check($otp, (string) $row->otp_hash)) {
            $row->attempts = (int) $row->attempts + 1;
            $row->save();
            return false;
        }

        $row->used_at = now();
        $row->save();

        return true;
    }

    public function resendThrottleSeconds(): int
    {
        return (int) env('LOGIN_OTP_RESEND_THROTTLE_SECONDS', 30);
    }

    private function expiresMinutes(): int
    {
        return max(1, (int) env('LOGIN_OTP_EXPIRES_MINUTES', 5));
    }

    private function maxAttempts(): int
    {
        return max(1, (int) env('LOGIN_OTP_MAX_ATTEMPTS', 5));
    }

    private function trySyncTelegramLinkOnLocalhost(User $user): void
    {
        if (!app()->environment('local')) {
            return;
        }

        if (!(bool) env('LOGIN_OTP_AUTO_SYNC_TELEGRAM_ON_LOCAL', true)) {
            return;
        }

        if (!$this->telegramBotService->enabled()) {
            return;
        }

        try {
            // Localhost cannot receive Telegram webhook directly, so sync one polling cycle.
            Artisan::call('telegram:webhook:delete');
            Artisan::call('telegram:poll', [
                '--once' => true,
                '--timeout' => 1,
            ]);
            $user->refresh();
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function telegramLinkHint(string $phoneNumber): string
    {
        $lines = [
            'Telegram is not linked yet.',
            'Open the bot and send: link ' . $phoneNumber,
        ];

        if (app()->environment('local')) {
            $lines[] = 'If you run localhost, keep this command running in another terminal: php artisan telegram:poll';
        }

        return implode("\n", $lines);
    }

    private function looksLikePhoneIdentifier(string $value): bool
    {
        $value = trim($value);
        if ($value === '') {
            return false;
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';
        return strlen($digits) >= 7;
    }
}
