<?php

use App\Models\Assignment;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('teacher can create an assignment and notify targeted students', function () {
    $class = SchoolClass::query()->create([
        'name' => 'Grade 10',
        'section' => 'A',
        'room' => '201',
        'capacity' => 40,
    ]);

    $teacher = User::factory()->create([
        'role' => 'teacher',
        'name' => 'Ms. Dara',
    ]);

    $subject = Subject::query()->create([
        'name' => 'Mathematics',
        'code' => 'MATH-10',
        'teacher_id' => $teacher->id,
        'school_class_id' => $class->id,
        'description' => 'Core mathematics',
        'is_active' => true,
    ]);

    $targetStudent = User::factory()->create([
        'role' => 'student',
        'school_class_id' => $class->id,
        'name' => 'Target Student',
    ]);

    $otherStudent = User::factory()->create([
        'role' => 'student',
        'school_class_id' => $class->id,
        'name' => 'Other Student',
    ]);

    $response = $this
        ->actingAs($teacher)
        ->post(route('teacher.assignments.store'), [
            'subject_id' => $subject->id,
            'title' => 'Chapter 5 Exercises',
            'description' => 'Complete questions 1 to 10.',
            'due_at' => now()->addDays(3)->toDateString(),
            'student_ids' => [$targetStudent->id],
        ]);

    $response
        ->assertRedirect(route('teacher.assignments.index'))
        ->assertSessionHas('success');

    $assignment = Assignment::query()->first();
    expect($assignment)->not->toBeNull();
    expect($assignment->students()->pluck('users.id')->all())->toBe([$targetStudent->id]);

    $targetNotifications = Notification::query()
        ->where('type', 'student_assignment_posted')
        ->where('message', 'like', '%[student_id:' . $targetStudent->id . ']%')
        ->count();

    $otherNotifications = Notification::query()
        ->where('type', 'student_assignment_posted')
        ->where('message', 'like', '%[student_id:' . $otherStudent->id . ']%')
        ->count();

    expect($targetNotifications)->toBe(1);
    expect($otherNotifications)->toBe(0);
});

test('student assignment page only shows assignments assigned to that student', function () {
    $class = SchoolClass::query()->create([
        'name' => 'Grade 11',
        'section' => 'B',
        'room' => '305',
        'capacity' => 35,
    ]);

    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'school_class_id' => $class->id,
    ]);

    $otherStudent = User::factory()->create([
        'role' => 'student',
        'school_class_id' => $class->id,
    ]);

    $visibleAssignment = Assignment::query()->create([
        'teacher_id' => $teacher->id,
        'title' => 'Visible Assignment',
        'description' => 'This one belongs to the current student.',
        'due_at' => now()->addDay(),
    ]);
    $visibleAssignment->students()->attach($student->id);

    $hiddenAssignment = Assignment::query()->create([
        'teacher_id' => $teacher->id,
        'title' => 'Hidden Assignment',
        'description' => 'This should not appear.',
        'due_at' => now()->addDays(2),
    ]);
    $hiddenAssignment->students()->attach($otherStudent->id);

    $response = $this
        ->actingAs($student)
        ->get(route('student.assignment.index'));

    $response->assertOk();
    $response->assertSee('Visible Assignment');
    $response->assertDontSee('Hidden Assignment');
});

test('teacher can edit an assignment from assignment history', function () {
    $class = SchoolClass::query()->create([
        'name' => 'Grade 12',
        'section' => 'C',
        'room' => '410',
        'capacity' => 30,
    ]);

    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $subject = Subject::query()->create([
        'name' => 'Chemistry',
        'code' => 'CHEM-12',
        'teacher_id' => $teacher->id,
        'school_class_id' => $class->id,
        'description' => 'Chemistry class',
        'is_active' => true,
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'school_class_id' => $class->id,
    ]);

    $assignment = Assignment::query()->create([
        'teacher_id' => $teacher->id,
        'subject_id' => $subject->id,
        'title' => 'Original Title',
        'description' => 'Original description.',
        'due_at' => now()->addDays(2),
    ]);
    $assignment->students()->attach($student->id);

    $response = $this
        ->actingAs($teacher)
        ->put(route('teacher.assignments.update', $assignment), [
            'subject_id' => $subject->id,
            'title' => 'Updated Title',
            'description' => 'Updated assignment description.',
            'due_at' => now()->addDays(5)->toDateString(),
            'student_ids' => [$student->id],
        ]);

    $response
        ->assertRedirect(route('teacher.assignments.index'))
        ->assertSessionHas('success')
        ->assertSessionHas('assignment_action', 'updated');

    $assignment->refresh();

    expect($assignment->title)->toBe('Updated Title');
    expect($assignment->description)->toBe('Updated assignment description.');
});
