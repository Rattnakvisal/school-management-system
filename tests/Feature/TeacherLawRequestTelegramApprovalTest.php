<?php

use App\Models\TeacherLawRequest;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('admin approval sends styled telegram alert to linked teacher', function () {
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin Sopheak',
    ]);

    $teacher = User::factory()->create([
        'role' => 'teacher',
        'name' => 'Teacher Dara',
        'telegram_chat_id' => '11223344',
    ]);

    $lawRequest = TeacherLawRequest::query()->create([
        'teacher_id' => $teacher->id,
        'law_type' => 'school_policy',
        'subject' => 'Financial Accounting I (ACC 105)',
        'subject_time' => '09:15-10:45',
        'requested_for' => '2026-04-29',
        'reason' => 'School meeting.',
        'status' => 'pending',
    ]);

    $this->mock(TelegramBotService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('sendMessage')
            ->once()
            ->with('11223344', Mockery::on(function (string $text): bool {
                return str_contains($text, 'សេចក្តីជូនដំណឹងពី TechBridge Academy')
                    && str_contains($text, 'សំណើសុំច្បាប់របស់លោក/អ្នកត្រូវបានអនុម័ត')
                    && str_contains($text, 'Admin Sopheak (Admin)')
                    && str_contains($text, 'Financial Accounting I (ACC 105)')
                    && str_contains($text, '09:15-10:45')
                    && str_contains($text, 'ស្ថានភាព: Approved');
            }))
            ->andReturn(true);
    });

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.attendance.teachers.law-requests.approve', $lawRequest), [
            'date' => '2026-04-29',
        ]);

    $response->assertSessionHas('success');

    $lawRequest->refresh();
    expect($lawRequest->status)->toBe('approved');
});
