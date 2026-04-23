<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TelegramBotController extends Controller
{
    private const TELEGRAM_OTP_ROLES = ['admin', 'teacher', 'student'];

    public function webhook(Request $request, TelegramBotService $telegram, ?string $secret = null)
    {
        $configuredSecret = $telegram->webhookSecret();
        $headerSecret = trim((string) $request->header('X-Telegram-Bot-Api-Secret-Token', ''));
        $routeSecret = trim((string) $secret);
        if (
            $configuredSecret !== ''
            && $routeSecret !== $configuredSecret
            && $headerSecret !== $configuredSecret
        ) {
            return response()->json(['ok' => false, 'error' => 'invalid webhook secret'], 403);
        }

        $update = $request->all();
        $callbackQuery = data_get($update, 'callback_query');
        if (is_array($callbackQuery)) {
            $chatId = (string) data_get($callbackQuery, 'message.chat.id', '');
            $callbackId = (string) data_get($callbackQuery, 'id', '');

            if ($callbackId !== '') {
                $telegram->answerCallbackQuery($callbackId);
            }

            if ($chatId !== '') {
                $telegram->sendMessage($chatId, $this->defaultLinkInstruction(), $this->mainKeyboard());
            }

            return response()->json(['ok' => true]);
        }

        $message = data_get($update, 'message');
        if (!is_array($message)) {
            return response()->json(['ok' => true]);
        }

        $chatId = (string) data_get($message, 'chat.id', '');
        if ($chatId === '') {
            return response()->json(['ok' => true]);
        }

        $text = trim((string) data_get($message, 'text', ''));
        $firstName = trim((string) data_get($message, 'from.first_name', ''));
        $username = trim((string) data_get($message, 'from.username', ''));
        $sharedPhone = trim((string) data_get($message, 'contact.phone_number', ''));

        if ($this->handlePhoneLinkCommand($telegram, $text, $chatId, $username)) {
            return response()->json(['ok' => true]);
        }

        if ($sharedPhone !== '' && $this->linkTelegramByPhone($telegram, $sharedPhone, $chatId, $username, true)) {
            return response()->json(['ok' => true]);
        }

        if ($this->handleRawPhoneLink($telegram, $text, $chatId, $username)) {
            return response()->json(['ok' => true]);
        }

        if (
            $text === ''
            || str_starts_with($text, '/start')
            || strcasecmp($text, 'menu') === 0
            || strcasecmp($text, '/menu') === 0
        ) {
            $telegram->sendMessage($chatId, $this->startLinkText($firstName), $this->mainKeyboard());
            return response()->json(['ok' => true]);
        }

        if ($text === '/help' || $text === '/faq' || strcasecmp($text, 'faq') === 0) {
            $telegram->sendMessage($chatId, $this->startLinkText($firstName), $this->mainKeyboard());
            return response()->json(['ok' => true]);
        }

        $telegram->sendMessage(
            $chatId,
            $this->defaultLinkInstruction(),
            $this->mainKeyboard()
        );

        return response()->json(['ok' => true]);
    }

    private function handlePhoneLinkCommand(TelegramBotService $telegram, string $text, string $chatId, string $username): bool
    {
        $normalized = strtolower(trim($text));
        if (
            $normalized === ''
            || (!str_starts_with($normalized, '/link') && !str_starts_with($normalized, 'link'))
        ) {
            return false;
        }

        $parts = preg_split('/\s+/', trim($text), 2) ?: [];
        $phone = trim((string) ($parts[1] ?? ''));

        if ($phone === '') {
            $telegram->sendMessage(
                $chatId,
                $this->defaultLinkInstruction(),
                $this->mainKeyboard()
            );
            return true;
        }

        return $this->linkTelegramByPhone($telegram, $phone, $chatId, $username);
    }

    private function handleRawPhoneLink(TelegramBotService $telegram, string $text, string $chatId, string $username): bool
    {
        if (!$this->looksLikePhoneText($text)) {
            return false;
        }

        return $this->linkTelegramByPhone($telegram, trim($text), $chatId, $username);
    }

    private function linkTelegramByPhone(
        TelegramBotService $telegram,
        string $phone,
        string $chatId,
        string $username,
        bool $fromContactShare = false
    ): bool {
        if (!$this->telegramLinkingEnabled()) {
            $telegram->sendMessage(
                $chatId,
                'Telegram linking is not available yet. Please ask the administrator to run migrations.',
                $this->mainKeyboard()
            );
            return true;
        }

        $user = $this->findUserByPhone($phone);
        if (!$user) {
            $message = $fromContactShare
                ? 'No admin/student/teacher account found with this contact phone number.'
                : 'No admin/student/teacher account found for that phone number.';

            $telegram->sendMessage($chatId, $message, $this->mainKeyboard());
            return true;
        }

        User::query()
            ->where('id', '!=', $user->id)
            ->where('telegram_chat_id', $chatId)
            ->update([
                'telegram_chat_id' => null,
                'telegram_username' => null,
                'telegram_linked_at' => null,
            ]);

        $alreadyLinked = (string) ($user->telegram_chat_id ?? '') === $chatId;

        $user->telegram_chat_id = $chatId;
        $user->telegram_username = $username !== '' ? $username : null;
        $user->telegram_linked_at = now();
        $user->save();

        $statusText = $alreadyLinked ? 'already linked' : 'linked successfully';
        $telegram->sendMessage(
            $chatId,
            "Telegram {$statusText} for {$user->name} (" . $this->maskPhone((string) $user->phone_number) . ").\n"
                . "You will receive login OTP here in real time.",
            $this->mainKeyboard()
        );

        return true;
    }

    private function findUserByPhone(string $phone): ?User
    {
        $targetTokens = $this->phoneLookupTokens($phone);
        if ($targetTokens === []) {
            return null;
        }

        $users = User::query()
            ->whereIn('role', self::TELEGRAM_OTP_ROLES)
            ->whereNotNull('phone_number')
            ->get();

        foreach ($users as $user) {
            if ($this->phoneNumbersMatch($targetTokens, (string) ($user->phone_number ?? ''))) {
                return $user;
            }
        }

        return null;
    }

    private function telegramLinkingEnabled(): bool
    {
        return Schema::hasColumn('users', 'telegram_chat_id')
            && Schema::hasColumn('users', 'telegram_username')
            && Schema::hasColumn('users', 'telegram_linked_at')
            && Schema::hasColumn('users', 'phone_number');
    }

    private function normalizePhoneDigits(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }

    private function looksLikePhoneText(string $text): bool
    {
        $trimmed = trim($text);
        if ($trimmed === '' || preg_match('/\s/', $trimmed)) {
            return false;
        }

        $digits = $this->normalizePhoneDigits($trimmed);
        if ($digits === '') {
            return false;
        }

        $length = strlen($digits);
        return $length >= 8 && $length <= 15;
    }

    private function phoneNumbersMatch(array $targetTokens, string $candidatePhone): bool
    {
        $candidateTokens = $this->phoneLookupTokens($candidatePhone);
        if ($candidateTokens === []) {
            return false;
        }

        return array_intersect($targetTokens, $candidateTokens) !== [];
    }

    private function phoneLookupTokens(string $phone): array
    {
        $digits = $this->normalizePhoneDigits($phone);
        if ($digits === '') {
            return [];
        }

        $tokens = [$digits];
        $khCore = $this->cambodiaCoreDigits($digits);
        if ($khCore !== null) {
            $tokens[] = '0' . $khCore;
            $tokens[] = '855' . $khCore;
        }

        return array_values(array_unique(array_filter($tokens, fn(string $token): bool => $token !== '')));
    }

    private function cambodiaCoreDigits(string $digits): ?string
    {
        if (str_starts_with($digits, '855')) {
            $core = ltrim(substr($digits, 3), '0');
            return $core !== '' ? $core : null;
        }

        if (str_starts_with($digits, '0')) {
            $core = ltrim(substr($digits, 1), '0');
            return $core !== '' ? $core : null;
        }

        return null;
    }

    private function maskPhone(string $phone): string
    {
        $digits = $this->normalizePhoneDigits($phone);
        if ($digits === '') {
            return 'phone not set';
        }

        return '******' . substr($digits, -4);
    }

    private function startLinkText(string $firstName): string
    {
        $name = $firstName !== '' ? $firstName : 'Student';
        return "Hi {$name}.\n"
            . "This Telegram bot is for TechBridge Academy login OTP linking only.\n\n"
            . $this->defaultLinkInstruction();
    }

    private function defaultLinkInstruction(): string
    {
        return implode("\n", [
            "To link your account, send:",
            "link <your phone number>",
            "",
            "Example:",
            "link +85512345678",
            "",
            "Use the same phone number saved in your school account.",
            "Type Menu any time to see this message again.",
        ]);
    }

    private function mainKeyboard(): array
    {
        return [
            'reply_markup' => [
                'keyboard' => [
                    [['text' => 'Menu']],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
            ],
        ];
    }
}
