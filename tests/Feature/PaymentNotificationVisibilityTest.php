<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('student payment notification is hidden from teachers and visible to targeted student', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);
    $student = User::factory()->create([
        'role' => 'student',
    ]);
    $otherStudent = User::factory()->create([
        'role' => 'student',
    ]);

    Notification::query()->create([
        'type' => 'student_payment_saved',
        'title' => 'Payment recorded',
        'message' => '[student_id:' . $student->id . '] Your school payment was recorded for $120.00.',
        'url' => route('student.notices.index'),
        'is_read' => false,
    ]);

    $this
        ->actingAs($teacher)
        ->get(route('teacher.notices.index'))
        ->assertOk()
        ->assertDontSeeText('Payment recorded')
        ->assertDontSeeText('school payment');

    $this
        ->actingAs($student)
        ->get(route('student.notices.index'))
        ->assertOk()
        ->assertSeeText('Payment recorded')
        ->assertSeeText('Your school payment was recorded for $120.00.');

    $this
        ->actingAs($otherStudent)
        ->get(route('student.notices.index'))
        ->assertOk()
        ->assertDontSeeText('Payment recorded')
        ->assertDontSeeText('school payment');
});

test('teacher notification poll does not return student payment notifications', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);
    $student = User::factory()->create([
        'role' => 'student',
    ]);

    Notification::query()->create([
        'type' => 'student_payment_saved',
        'title' => 'Payment updated',
        'message' => '[student_id:' . $student->id . '] Your school payment was updated for $80.00.',
        'url' => route('student.notices.index'),
        'is_read' => false,
    ]);

    $this
        ->actingAs($teacher)
        ->getJson(route('teacher.notifications.poll'))
        ->assertOk()
        ->assertJsonPath('unread_count', 0)
        ->assertJsonPath('notifications', []);
});
