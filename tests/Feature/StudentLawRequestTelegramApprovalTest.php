<?php

use App\Models\SchoolClass;
use App\Models\StudentLawRequest;
use App\Models\Subject;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('teacher approval sends styled telegram alert to linked student', function () {
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    $teacher = User::factory()->create([
        'role' => 'teacher',
        'name' => 'Teacher Dara',
    ]);

    $schoolClass = SchoolClass::query()->create([
        'name' => 'AUB',
        'section' => 'A1',
        'room' => '101',
        'capacity' => 30,
        'is_active' => true,
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'name' => 'Sok Dara',
        'school_class_id' => $schoolClass->id,
        'telegram_chat_id' => '99887766',
    ]);

    $subject = Subject::query()->create([
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'name' => 'Financial Accounting I',
        'code' => 'ACC105',
        'is_active' => true,
    ]);

    DB::table('subject_study_times')->insert([
        'subject_id' => $subject->id,
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'period' => 'morning',
        'start_time' => '07:45:00',
        'end_time' => '09:15:00',
        'sort_order' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $lawRequest = StudentLawRequest::query()->create([
        'student_id' => $student->id,
        'school_class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'law_type' => 'medical_leave',
        'subject' => 'Financial Accounting I (ACC 105)',
        'subject_time' => '07:45-09:15',
        'requested_for' => '2026-04-29',
        'reason' => 'Doctor appointment.',
        'status' => 'pending',
    ]);

    $this->mock(TelegramBotService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('sendMessage')
            ->once()
            ->with('99887766', Mockery::on(function (string $text): bool {
                return str_contains($text, 'សេចក្តីជូនដំណឹងពី TechBridge Academy')
                    && str_contains($text, 'សំណើសុំច្បាប់របស់អ្នកត្រូវបានអនុម័ត')
                    && str_contains($text, 'Teacher Dara')
                    && str_contains($text, 'Financial Accounting I (ACC 105)')
                    && str_contains($text, '07:45-09:15')
                    && str_contains($text, 'ស្ថានភាព: Approved');
            }))
            ->andReturn(true);
    });

    $response = $this
        ->actingAs($teacher)
        ->post(route('teacher.attendance.law-requests.approve', $lawRequest), [
            'attendance_date' => '2026-04-29',
        ]);

    $response->assertSessionHas('success');

    $lawRequest->refresh();
    expect($lawRequest->status)->toBe('approved');
});
