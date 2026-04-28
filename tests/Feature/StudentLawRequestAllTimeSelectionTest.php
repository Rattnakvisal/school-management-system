<?php

use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\StudentLawRequest;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('student can submit law request for all subject times and notify each assigned teacher', function () {
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    $schoolClass = SchoolClass::query()->create([
        'name' => 'AUB',
        'section' => 'A1',
        'room' => '101',
        'capacity' => 30,
        'is_active' => true,
    ]);

    $teacherOne = User::factory()->create([
        'role' => 'teacher',
        'name' => 'Teacher One',
    ]);
    $teacherTwo = User::factory()->create([
        'role' => 'teacher',
        'name' => 'Teacher Two',
    ]);
    $student = User::factory()->create([
        'role' => 'student',
        'name' => 'Student Dara',
        'school_class_id' => $schoolClass->id,
    ]);

    $subject = Subject::query()->create([
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacherOne->id,
        'name' => 'Mobile App Development',
        'code' => 'SUB0141561123',
        'is_active' => true,
    ]);

    $morningSlotId = DB::table('subject_study_times')->insertGetId([
        'subject_id' => $subject->id,
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacherOne->id,
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
        'teacher_id' => $teacherTwo->id,
        'day_of_week' => 'monday',
        'period' => 'afternoon',
        'start_time' => '10:00:00',
        'end_time' => '13:00:00',
        'sort_order' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this
        ->actingAs($student)
        ->post(route('student.law-requests.store'), [
            'law_type' => 'medical_leave',
            'subject_id' => (string) $subject->id,
            'subject_time_keys' => [
                'slot:' . $morningSlotId,
                'slot:' . $afternoonSlotId,
            ],
            'requested_for' => '2026-05-04',
            'reason' => 'Need to request the full subject day.',
        ]);

    $response
        ->assertRedirect(route('student.law-requests.index'))
        ->assertSessionHas('success', 'Law request submitted successfully.');

    $lawRequest = StudentLawRequest::query()->first();

    expect($lawRequest)->not->toBeNull()
        ->and($lawRequest->teacher_id)->toBeNull()
        ->and((string) $lawRequest->subject_time)->toContain('Morning')
        ->and((string) $lawRequest->subject_time)->toContain('Afternoon');

    expect(Notification::query()
        ->where('type', 'student_law_request')
        ->where('message', 'like', '%[teacher_id:' . $teacherOne->id . ']%')
        ->exists())->toBeTrue();

    expect(Notification::query()
        ->where('type', 'student_law_request')
        ->where('message', 'like', '%[teacher_id:' . $teacherTwo->id . ']%')
        ->exists())->toBeTrue();
});
