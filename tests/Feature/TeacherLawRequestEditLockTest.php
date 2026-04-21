<?php

use App\Models\TeacherLawRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('approved teacher law requests close edit mode', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $lawRequest = TeacherLawRequest::query()->create([
        'teacher_id' => $teacher->id,
        'law_type' => 'school_policy',
        'subject' => 'All Subjects',
        'subject_time' => 'All Times',
        'requested_for' => now()->toDateString(),
        'reason' => 'Approved request.',
        'status' => 'approved',
    ]);

    $response = $this
        ->actingAs($teacher)
        ->get(route('teacher.law-requests.index', ['edit' => $lawRequest->id]));

    $response
        ->assertRedirect(route('teacher.law-requests.index'))
        ->assertSessionHas('warning', 'Approved or rejected requests can no longer be edited.');
});

test('approved teacher law requests cannot be updated', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $lawRequest = TeacherLawRequest::query()->create([
        'teacher_id' => $teacher->id,
        'law_type' => 'school_policy',
        'subject' => 'All Subjects',
        'subject_time' => 'All Times',
        'requested_for' => now()->toDateString(),
        'reason' => 'Approved request.',
        'status' => 'approved',
    ]);

    $response = $this
        ->actingAs($teacher)
        ->put(route('teacher.law-requests.update', $lawRequest), [
            'law_type' => 'school_policy',
            'subject_id' => 'all',
            'subject_time_keys' => ['all:all'],
            'requested_for' => now()->addDay()->toDateString(),
            'reason' => 'Trying to update approved request.',
        ]);

    $response
        ->assertRedirect(route('teacher.law-requests.index'))
        ->assertSessionHas('warning', 'Approved or rejected requests can no longer be edited.');

    $lawRequest->refresh();

    expect($lawRequest->reason)->toBe('Approved request.');
});
