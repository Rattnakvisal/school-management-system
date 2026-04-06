<?php

use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('telegram bot can link admin account by phone number', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin OTP',
        'phone_number' => '078841050',
    ]);

    $this->mock(TelegramBotService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('webhookSecret')->andReturn('');
        $mock->shouldReceive('sendMessage')->once()->andReturn(true);
        $mock->shouldReceive('answerCallbackQuery')->zeroOrMoreTimes();
    });

    $response = $this->postJson(route('telegram.webhook'), [
        'message' => [
            'chat' => ['id' => '13579'],
            'from' => [
                'first_name' => 'Visal',
                'username' => 'admin_visal',
            ],
            'text' => 'link 078841050',
        ],
    ]);

    $response->assertOk();

    $admin->refresh();
    expect((string) $admin->telegram_chat_id)->toBe('13579');
    expect((string) $admin->telegram_username)->toBe('admin_visal');
    expect($admin->telegram_linked_at)->not->toBeNull();
});
