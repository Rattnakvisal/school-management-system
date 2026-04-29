<?php

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeacherLawRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('teacher can submit law request for all selected subject times', function () {
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

    $subject = Subject::query()->create([
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'name' => 'Mobile App Development',
        'code' => 'SUB0141561123',
        'is_active' => true,
    ]);

    $morningSlotId = DB::table('subject_study_times')->insertGetId([
        'subject_id' => $subject->id,
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'monday',
        'period' => 'morning',
        'start_time' => '07:00:00',
        'end_time' => '10:00:00',
        'sort_order' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $afternoonSlotId = DB::table('subject_study_times')->insertGetId([
        'subject_id' => $subject->id,
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'monday',
        'period' => 'afternoon',
        'start_time' => '10:00:00',
        'end_time' => '13:00:00',
        'sort_order' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this
        ->actingAs($teacher)
        ->post(route('teacher.law-requests.store'), [
            'law_type' => 'school_policy',
            'subject_id' => (string) $subject->id,
            'subject_time_keys' => [
                'slot:' . $morningSlotId,
                'slot:' . $afternoonSlotId,
            ],
            'requested_for' => '2026-05-04',
            'requested_until' => '2026-05-04',
            'reason' => 'Need to request all subject times.',
        ]);

    $response
        ->assertRedirect(route('teacher.law-requests.index'))
        ->assertSessionHas('success', 'Law request submitted successfully.');

    $lawRequest = TeacherLawRequest::query()->first();

    expect($lawRequest)->not->toBeNull()
        ->and($lawRequest->subject)->toBe('Mobile App Development')
        ->and((string) $lawRequest->subject_time)->toContain('Morning')
        ->and((string) $lawRequest->subject_time)->toContain('Afternoon');
});
