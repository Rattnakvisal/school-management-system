<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    public function botToken(): string
    {
        return trim((string) config('services.telegram.bot_token', ''));
    }

    public function botUsername(): string
    {
        return trim((string) config('services.telegram.bot_username', ''));
    }

    public function adminChatId(): ?string
    {
        $chatId = trim((string) config('services.telegram.admin_chat_id', ''));
        return $chatId !== '' ? $chatId : null;
    }

    public function webhookSecret(): string
    {
        $secret = trim((string) config('services.telegram.webhook_secret', ''));

        // Treat URL values as misconfiguration (secret must be a short token string).
        if ($secret !== '' && filter_var($secret, FILTER_VALIDATE_URL)) {
            return '';
        }

        return $secret;
    }

    public function enabled(): bool
    {
        return $this->botToken() !== '';
    }

    public function verifySsl(): bool
    {
        return (bool) config('services.telegram.verify_ssl', true);
    }

    public function botUrl(string $start = ''): ?string
    {
        $username = $this->botUsername();
        if ($username === '') {
            return null;
        }

        $payload = trim($start);
        if ($payload === '') {
            return 'https://t.me/' . $username;
        }

        return 'https://t.me/' . $username . '?start=' . urlencode($payload);
    }

    public function sendMessage(string|int $chatId, string $text, array $extra = []): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        $payload = array_merge([
            'chat_id' => (string) $chatId,
            'text' => $text,
            'disable_web_page_preview' => true,
        ], $extra);

        try {
            $response = $this->telegramHttp()
                ->post($this->endpoint('sendMessage'), $payload);

            if (!$response->successful()) {
                Log::warning('Telegram sendMessage failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            report($e);
            Log::error('Telegram sendMessage exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null): bool
    {
        if (!$this->enabled() || trim($callbackQueryId) === '') {
            return false;
        }

        $payload = [
            'callback_query_id' => $callbackQueryId,
        ];

        if ($text !== null && trim($text) !== '') {
            $payload['text'] = trim($text);
        }

        try {
            $response = $this->telegramHttp()
                ->post($this->endpoint('answerCallbackQuery'), $payload);

            if (!$response->successful()) {
                Log::warning('Telegram answerCallbackQuery failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            report($e);
            Log::error('Telegram answerCallbackQuery exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function notifyAdminContact(array $data): bool
    {
        $adminChatId = $this->adminChatId();
        if ($adminChatId === null) {
            return false;
        }

        $name = trim((string) ($data['name'] ?? 'Unknown'));
        $email = trim((string) ($data['email'] ?? '-'));
        $phone = trim((string) ($data['phone'] ?? '-'));
        $subject = trim((string) ($data['subject'] ?? '-'));
        $message = trim((string) ($data['message'] ?? '-'));

        $text = implode("\n", [
            "New website contact message",
            "Name: {$name}",
            "Email: {$email}",
            "Phone: {$phone}",
            "Subject: {$subject}",
            "Message:",
            $message,
        ]);

        return $this->sendMessage($adminChatId, $text);
    }

    private function endpoint(string $method): string
    {
        return 'https://api.telegram.org/bot' . $this->botToken() . '/' . ltrim($method, '/');
    }

    private function telegramHttp(): PendingRequest
    {
        return Http::asJson()
            ->timeout(12)
            ->withOptions([
                'verify' => $this->verifySsl(),
                'curl' => [
                    CURLOPT_PROXY => '',
                    CURLOPT_NOPROXY => '*',
                ],
            ]);
    }
}
