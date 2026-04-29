<?php

use App\Models\StudentLawRequest;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('pending student law requests render row edit modal', function () {
    $schoolClass = SchoolClass::query()->create([
        'name' => 'AUB',
        'section' => 'A1',
        'room' => '101',
        'capacity' => 30,
        'is_active' => true,
    ]);

    $teacher = User::factory()->create([
        'role' => 'teacher',
        'name' => 'Teacher One',
    ]);
    $student = User::factory()->create([
        'role' => 'student',
        'school_class_id' => $schoolClass->id,
    ]);

    $subject = Subject::query()->create([
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'name' => 'Mobile App Development',
        'code' => 'SUB0141561123',
        'is_active' => true,
    ]);

    DB::table('subject_study_times')->insert([
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

    $lawRequest = StudentLawRequest::query()->create([
        'student_id' => $student->id,
        'school_class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'law_type' => 'medical_leave',
        'subject' => 'Mobile App Development',
        'subject_time' => 'Monday | Morning 07:00 AM-10:00 AM',
        'requested_for' => '2026-05-04',
        'requested_until' => '2026-05-05',
        'reason' => 'Need to edit this pending request.',
        'status' => 'pending',
    ]);

    $this
        ->actingAs($student)
        ->get(route('student.law-requests.index'))
        ->assertOk()
        ->assertSee('edit_student_subject_time_options_' . $lawRequest->id)
        ->assertSee('Until Date');
});

test('approved student law requests close edit mode', function () {
    $student = User::factory()->create([
        'role' => 'student',
    ]);

    $lawRequest = StudentLawRequest::query()->create([
        'student_id' => $student->id,
        'law_type' => 'medical_leave',
        'subject' => 'Mobile App Development',
        'subject_time' => 'Monday | Morning 07:00 AM-10:00 AM',
        'requested_for' => now()->toDateString(),
        'requested_until' => now()->toDateString(),
        'reason' => 'Approved request.',
        'status' => 'approved',
    ]);

    $response = $this
        ->actingAs($student)
        ->get(route('student.law-requests.index', ['edit' => $lawRequest->id]));

    $response
        ->assertRedirect(route('student.law-requests.index'))
        ->assertSessionHas('warning', 'Approved or rejected requests can no longer be edited.');
});

test('approved student law requests cannot be updated', function () {
    $student = User::factory()->create([
        'role' => 'student',
    ]);

    $lawRequest = StudentLawRequest::query()->create([
        'student_id' => $student->id,
        'law_type' => 'medical_leave',
        'subject' => 'Mobile App Development',
        'subject_time' => 'Monday | Morning 07:00 AM-10:00 AM',
        'requested_for' => now()->toDateString(),
        'requested_until' => now()->toDateString(),
        'reason' => 'Approved request.',
        'status' => 'approved',
    ]);

    $response = $this
        ->actingAs($student)
        ->put(route('student.law-requests.update', $lawRequest), [
            'law_type' => 'medical_leave',
            'subject_id' => '1',
            'subject_time_keys' => ['slot:1'],
            'requested_for' => now()->addDay()->toDateString(),
            'requested_until' => now()->addDay()->toDateString(),
            'reason' => 'Trying to update approved request.',
        ]);

    $response
        ->assertRedirect(route('student.law-requests.index'))
        ->assertSessionHas('warning', 'Approved or rejected requests can no longer be edited.');

    $lawRequest->refresh();

    expect($lawRequest->reason)->toBe('Approved request.');
});
