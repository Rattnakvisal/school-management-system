<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
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
            $data = trim((string) data_get($callbackQuery, 'data', ''));

            if ($callbackId !== '') {
                $telegram->answerCallbackQuery($callbackId);
            }

            if ($chatId !== '' && $data !== '') {
                $callbackAnswer = $this->callbackAnswer($data);
                if ($callbackAnswer !== null) {
                    $telegram->sendMessage($chatId, $callbackAnswer, $this->mainKeyboard());
                }
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
        $lastName = trim((string) data_get($message, 'from.last_name', ''));
        $username = trim((string) data_get($message, 'from.username', ''));
        $fromId = (string) data_get($message, 'from.id', '');

        if ($text === '' || str_starts_with($text, '/start')) {
            $telegram->sendMessage($chatId, $this->startFullContentText($firstName), $this->faqInlineKeyboard());
            $telegram->sendMessage($chatId, "Use the menu below for quick actions.", $this->mainKeyboard());
            return response()->json(['ok' => true]);
        }

        if ($text === '/help' || $text === '/faq' || strcasecmp($text, 'faq') === 0) {
            $telegram->sendMessage($chatId, $this->startFullContentText($firstName), $this->faqInlineKeyboard());
            $telegram->sendMessage($chatId, "Use the menu below for quick actions.", $this->mainKeyboard());
            return response()->json(['ok' => true]);
        }

        if ($text === '/contact' || strcasecmp($text, 'contact admin') === 0) {
            $telegram->sendMessage(
                $chatId,
                "Please send your message with details:\nName, class/grade, and your question.\nI will forward it to admin.",
                $this->mainKeyboard()
            );
            return response()->json(['ok' => true]);
        }

        $answer = $this->findAnswer($text);
        if ($answer !== null) {
            $telegram->sendMessage($chatId, $answer, $this->mainKeyboard());
            return response()->json(['ok' => true]);
        }

        $telegram->sendMessage(
            $chatId,
            "Thanks. I sent your message to admin. They will contact you soon.",
            $this->mainKeyboard()
        );

        $adminChatId = $telegram->adminChatId();
        if ($adminChatId !== null) {
            $fullName = trim($firstName . ' ' . $lastName);
            $senderLabel = $fullName !== '' ? $fullName : 'Unknown';
            if ($username !== '') {
                $senderLabel .= ' (@' . $username . ')';
            }

            $adminMessage = implode("\n", [
                "New Telegram user question",
                "User: {$senderLabel}",
                "Telegram ID: " . ($fromId !== '' ? $fromId : '-'),
                "Chat ID: {$chatId}",
                "Message:",
                $text,
            ]);

            $telegram->sendMessage($adminChatId, $adminMessage);
        }

        return response()->json(['ok' => true]);
    }

    private function welcomeText(string $firstName): string
    {
        $name = $firstName !== '' ? $firstName : 'Student';
        return "Welcome {$name} to NextGen International Institute Telegram Assistant.\n\n"
            . "This bot helps you with study accounts, class schedules, subjects, admissions, and contacting admin.\n"
            . "Choose a question below to get a quick answer, or type your own question anytime.";
    }

    private function startFullContentText(string $firstName): string
    {
        $welcome = $this->welcomeText($firstName);

        return implode("\n", [
            $welcome,
            "",
            "Q&A Details:",
            "1. How to get study account?",
            "A: Send your full name, class/grade, and phone or email. Admin will create your account and provide login details.",
            "",
            "2. How to apply for admission?",
            "A: Submit your information and required documents. School reviews and confirms enrollment.",
            "",
            "3. What programs are available?",
            "A: Primary, Lower Secondary, Upper Secondary, and co-curricular activities (clubs, arts, sports).",
            "",
            "4. What are office hours?",
            "A: Monday to Friday, 8:00 AM to 5:00 PM.",
            "",
            "5. How to contact admin?",
            "A: Send your message with name + class + question. Bot forwards to admin Telegram.",
            "",
            "You can tap Q&A buttons below or type your question anytime.",
        ]);
    }

    private function mainKeyboard(): array
    {
        return [
            'reply_markup' => [
                'keyboard' => [
                    [['text' => 'How to get study account?'], ['text' => 'How to apply for admission?']],
                    [['text' => 'What programs are available?'], ['text' => 'What are office hours?']],
                    [['text' => 'Contact admin'], ['text' => 'FAQ']],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
            ],
        ];
    }

    private function faqInlineKeyboard(): array
    {
        return [
            'reply_markup' => [
                'inline_keyboard' => [
                    [
                        ['text' => 'Q1: Study Account', 'callback_data' => 'faq_study_account'],
                        ['text' => 'Q2: Admission', 'callback_data' => 'faq_admission'],
                    ],
                    [
                        ['text' => 'Q3: Programs', 'callback_data' => 'faq_programs'],
                        ['text' => 'Q4: Office Hours', 'callback_data' => 'faq_office_hours'],
                    ],
                    [
                        ['text' => 'Q5: Contact Admin', 'callback_data' => 'faq_contact_admin'],
                    ],
                ],
            ],
        ];
    }

    private function callbackAnswer(string $data): ?string
    {
        return match ($data) {
            'faq_study_account' => "Q1: How to get study account?\nA: Send your full name, class/grade, and phone or email. Admin will create your study account and share login details.",
            'faq_admission' => "Q2: How to apply for admission?\nA: Submit your details and required documents. School reviews the request and confirms enrollment.",
            'faq_programs' => "Q3: What programs are available?\nA: Primary, Lower Secondary, Upper Secondary, and co-curricular activities such as clubs, arts, and sports.",
            'faq_office_hours' => "Q4: What are office hours?\nA: Monday to Friday, 8:00 AM to 5:00 PM.",
            'faq_contact_admin' => "Q5: How to contact admin?\nA: Type your message with name + class + question. The bot forwards it to admin Telegram.",
            default => null,
        };
    }

    private function findAnswer(string $text): ?string
    {
        $normalized = strtolower(trim(preg_replace('/[^a-z0-9\s\?]/i', ' ', $text) ?? ''));
        if ($normalized === '') {
            return null;
        }

        $map = [
            [
                'keys' => ['study account', 'account', 'login', 'get account'],
                'answer' => "To get a study account, please contact school admin with your full name, class, and phone/email. Admin will create and activate your account.",
            ],
            [
                'keys' => ['admission', 'apply', 'enroll'],
                'answer' => "Admission steps: submit your details and documents, school reviews your request, then confirms placement and account activation.",
            ],
            [
                'keys' => ['program', 'primary', 'secondary', 'club', 'sports'],
                'answer' => "Programs include Primary, Lower Secondary, Upper Secondary, and Co-Curricular activities (clubs, arts, sports).",
            ],
            [
                'keys' => ['office hours', 'hours', 'time', 'open'],
                'answer' => "Office hours are Monday to Friday, 8:00 AM to 5:00 PM.",
            ],
            [
                'keys' => ['contact admin', 'contact', 'administrator'],
                'answer' => "Please send your message now with name + class + question. I will forward it to admin.",
            ],
        ];

        foreach ($map as $row) {
            foreach ($row['keys'] as $key) {
                if (str_contains($normalized, $key)) {
                    return (string) $row['answer'];
                }
            }
        }

        return null;
    }
}
