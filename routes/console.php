<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('telegram:webhook:set {url?} {--secret=} {--drop-pending}', function (?string $url = null) {
    $token = trim((string) config('services.telegram.bot_token'));
    $verifySsl = (bool) config('services.telegram.verify_ssl', true);
    if ($token === '') {
        $this->error('TELEGRAM_BOT_TOKEN is missing.');
        return self::FAILURE;
    }
    $maskToken = static fn(string $value): string => str_replace($token, '***', $value);

    $baseUrl = trim((string) ($url ?: config('app.url')));
    if ($baseUrl === '') {
        $this->error('APP_URL is missing. Provide URL: php artisan telegram:webhook:set http://localhost:8000 or set APP_URL in .env file.');
        return self::FAILURE;
    }

    $baseUrl = rtrim($baseUrl, '/');
    $webhookUrl = $baseUrl . '/telegram/webhook';
    $secret = trim((string) ($this->option('secret') ?: config('services.telegram.webhook_secret', '')));

    if ($secret !== '' && filter_var($secret, FILTER_VALIDATE_URL)) {
        $this->warn('TELEGRAM_WEBHOOK_SECRET looks like URL. Ignoring invalid secret format.');
        $secret = '';
    }

    $payload = [
        'url' => $webhookUrl,
        'allowed_updates' => ['message', 'callback_query'],
        'drop_pending_updates' => (bool) $this->option('drop-pending'),
    ];

    if ($secret !== '') {
        $payload['secret_token'] = $secret;
    }

    $endpoint = 'https://api.telegram.org/bot' . $token . '/setWebhook';

    try {
        $response = Http::asJson()
            ->timeout(20)
            ->withOptions([
                'verify' => $verifySsl,
                'curl' => [
                    CURLOPT_PROXY => '',
                    CURLOPT_NOPROXY => '*',
                ],
            ])
            ->post($endpoint, $payload);
    } catch (\Throwable $e) {
        $this->error('Request failed: ' . $maskToken($e->getMessage()));
        return self::FAILURE;
    }

    if (!$response->successful()) {
        $this->error('Telegram setWebhook failed: HTTP ' . $response->status());
        $this->line($response->body());
        return self::FAILURE;
    }

    $json = $response->json();
    if (!is_array($json) || !($json['ok'] ?? false)) {
        $this->error('Telegram rejected webhook setup.');
        $this->line($response->body());
        return self::FAILURE;
    }

    $this->info('Webhook set successfully.');
    $this->line('Webhook URL: ' . $webhookUrl);
    $this->line('Secret enabled: ' . ($secret !== '' ? 'yes' : 'no'));
    return self::SUCCESS;
})->purpose('Set Telegram webhook to /telegram/webhook on APP_URL or provided URL');

Artisan::command('telegram:webhook:info', function () {
    $token = trim((string) config('services.telegram.bot_token'));
    $verifySsl = (bool) config('services.telegram.verify_ssl', true);
    if ($token === '') {
        $this->error('TELEGRAM_BOT_TOKEN is missing.');
        return self::FAILURE;
    }
    $maskToken = static fn(string $value): string => str_replace($token, '***', $value);

    $endpoint = 'https://api.telegram.org/bot' . $token . '/getWebhookInfo';

    try {
        $response = Http::asJson()
            ->timeout(20)
            ->withOptions([
                'verify' => $verifySsl,
                'curl' => [
                    CURLOPT_PROXY => '',
                    CURLOPT_NOPROXY => '*',
                ],
            ])
            ->get($endpoint);
    } catch (\Throwable $e) {
        $this->error('Request failed: ' . $maskToken($e->getMessage()));
        return self::FAILURE;
    }

    if (!$response->successful()) {
        $this->error('Telegram getWebhookInfo failed: HTTP ' . $response->status());
        $this->line($response->body());
        return self::FAILURE;
    }

    $this->line($response->body());
    return self::SUCCESS;
})->purpose('Show Telegram webhook info');

Artisan::command('telegram:webhook:delete {--drop-pending}', function () {
    $token = trim((string) config('services.telegram.bot_token'));
    $verifySsl = (bool) config('services.telegram.verify_ssl', true);
    if ($token === '') {
        $this->error('TELEGRAM_BOT_TOKEN is missing.');
        return self::FAILURE;
    }
    $maskToken = static fn(string $value): string => str_replace($token, '***', $value);

    $endpoint = 'https://api.telegram.org/bot' . $token . '/deleteWebhook';
    $payload = [
        'drop_pending_updates' => (bool) $this->option('drop-pending'),
    ];

    try {
        $response = Http::asJson()
            ->timeout(20)
            ->withOptions([
                'verify' => $verifySsl,
                'curl' => [
                    CURLOPT_PROXY => '',
                    CURLOPT_NOPROXY => '*',
                ],
            ])
            ->post($endpoint, $payload);
    } catch (\Throwable $e) {
        $this->error('Request failed: ' . $maskToken($e->getMessage()));
        return self::FAILURE;
    }

    if (!$response->successful()) {
        $this->error('Telegram deleteWebhook failed: HTTP ' . $response->status());
        $this->line($response->body());
        return self::FAILURE;
    }

    $json = $response->json();
    if (!is_array($json) || !($json['ok'] ?? false)) {
        $this->error('Telegram rejected deleteWebhook.');
        $this->line($response->body());
        return self::FAILURE;
    }

    $this->info('Webhook deleted successfully.');
    return self::SUCCESS;
})->purpose('Delete Telegram webhook (useful for localhost polling mode)');

Artisan::command('telegram:poll {--once} {--timeout=20}', function () {
    $token = trim((string) config('services.telegram.bot_token'));
    $verifySsl = (bool) config('services.telegram.verify_ssl', true);
    if ($token === '') {
        $this->error('TELEGRAM_BOT_TOKEN is missing.');
        return self::FAILURE;
    }
    $maskToken = static fn(string $value): string => str_replace($token, '***', $value);
    $timeout = max(1, min(50, (int) $this->option('timeout')));
    $runOnce = (bool) $this->option('once');

    $offsetFile = storage_path('app/telegram_offset.txt');
    $offset = 0;
    if (is_file($offsetFile)) {
        $offset = (int) trim((string) file_get_contents($offsetFile));
    }

    $secret = trim((string) config('services.telegram.webhook_secret', ''));
    if ($secret !== '' && filter_var($secret, FILTER_VALIDATE_URL)) {
        $secret = '';
    }

    $this->info('Telegram polling started. Press Ctrl+C to stop.');

    do {
        $endpoint = 'https://api.telegram.org/bot' . $token . '/getUpdates';
        $query = [
            'timeout' => $timeout,
            'allowed_updates' => json_encode(['message', 'callback_query']),
        ];

        if ($offset > 0) {
            $query['offset'] = $offset;
        }

        try {
            $response = Http::asJson()
                ->timeout($timeout + 10)
                ->withOptions([
                    'verify' => $verifySsl,
                    'curl' => [
                        CURLOPT_PROXY => '',
                        CURLOPT_NOPROXY => '*',
                    ],
                ])
                ->get($endpoint, $query);
        } catch (\Throwable $e) {
            $this->error('Polling request failed: ' . $maskToken($e->getMessage()));
            if ($runOnce) {
                return self::FAILURE;
            }
            sleep(2);
            continue;
        }

        if (!$response->successful()) {
            $this->error('getUpdates failed: HTTP ' . $response->status());
            $this->line($response->body());
            if ($runOnce) {
                return self::FAILURE;
            }
            sleep(2);
            continue;
        }

        $json = $response->json();
        $updates = is_array($json) ? ($json['result'] ?? []) : [];
        if (!is_array($updates)) {
            $updates = [];
        }

        foreach ($updates as $update) {
            if (!is_array($update)) {
                continue;
            }

            $updateId = (int) ($update['update_id'] ?? 0);
            if ($updateId > 0) {
                $offset = $updateId + 1;
            }

            $request = \Illuminate\Http\Request::create(
                '/telegram/webhook',
                'POST',
                [],
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode($update, JSON_UNESCAPED_UNICODE)
            );

            try {
                /** @var \App\Http\Controllers\Website\TelegramBotController $controller */
                $controller = app(\App\Http\Controllers\Website\TelegramBotController::class);
                /** @var \App\Services\TelegramBotService $service */
                $service = app(\App\Services\TelegramBotService::class);
                $controller->webhook($request, $service, $secret !== '' ? $secret : null);
                $this->line('Processed update_id: ' . $updateId);
            } catch (\Throwable $e) {
                report($e);
                $this->error('Failed to process update_id ' . $updateId . ': ' . $e->getMessage());
            }
        }

        if (!is_dir(dirname($offsetFile))) {
            @mkdir(dirname($offsetFile), 0777, true);
        }
        @file_put_contents($offsetFile, (string) $offset);

        if ($runOnce) {
            break;
        }
    } while (true);

    return self::SUCCESS;
})->purpose('Run Telegram long polling for localhost (no public webhook URL required)');
