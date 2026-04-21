<?php

use App\Models\Grade;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('teacher can assign a grade and notify the targeted student', function () {
    $class = SchoolClass::query()->create([
        'name' => 'Grade 9',
        'section' => 'A',
        'room' => '101',
        'capacity' => 40,
    ]);

    $teacher = User::factory()->create([
        'role' => 'teacher',
        'name' => 'Mr. Sovan',
    ]);

    $subject = Subject::query()->create([
        'name' => 'Biology',
        'code' => 'BIO-9',
        'teacher_id' => $teacher->id,
        'school_class_id' => $class->id,
        'description' => 'Biology subject',
        'is_active' => true,
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'school_class_id' => $class->id,
        'name' => 'Student One',
    ]);

    $response = $this
        ->actingAs($teacher)
        ->post(route('teacher.grades.store'), [
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'title' => 'Weekly Quiz',
            'score' => 18,
            'max_score' => 20,
            'graded_at' => now()->toDateString(),
            'remarks' => 'Strong work.',
        ]);

    $response
        ->assertRedirect(route('teacher.grades.index'))
        ->assertSessionHas('success');

    $grade = Grade::query()->first();
    expect($grade)->not->toBeNull();
    expect((int) $grade->student_id)->toBe($student->id);
    expect((string) $grade->grade_letter)->toBe('A');

    $notifications = Notification::query()
        ->where('type', 'student_grade_posted')
        ->where('message', 'like', '%[student_id:' . $student->id . ']%')
        ->count();

    expect($notifications)->toBe(1);
});

test('student grades page only shows grades for the authenticated student', function () {
    $class = SchoolClass::query()->create([
        'name' => 'Grade 8',
        'section' => 'B',
        'room' => '202',
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

    Grade::query()->create([
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'title' => 'Visible Grade',
        'score' => 15,
        'max_score' => 20,
        'grade_letter' => 'B',
        'graded_at' => now()->toDateString(),
    ]);

    Grade::query()->create([
        'teacher_id' => $teacher->id,
        'student_id' => $otherStudent->id,
        'title' => 'Hidden Grade',
        'score' => 10,
        'max_score' => 20,
        'grade_letter' => 'D',
        'graded_at' => now()->toDateString(),
    ]);

    $response = $this
        ->actingAs($student)
        ->get(route('student.grades.index'));

    $response->assertOk();
    $response->assertSee('Visible Grade');
    $response->assertDontSee('Hidden Grade');
});

test('teacher can edit an assigned grade', function () {
    $class = SchoolClass::query()->create([
        'name' => 'Grade 7',
        'section' => 'C',
        'room' => '303',
        'capacity' => 32,
    ]);

    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $subject = Subject::query()->create([
        'name' => 'History',
        'code' => 'HIS-7',
        'teacher_id' => $teacher->id,
        'school_class_id' => $class->id,
        'description' => 'History subject',
        'is_active' => true,
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'school_class_id' => $class->id,
    ]);

    $grade = Grade::query()->create([
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'subject_id' => $subject->id,
        'title' => 'Original Grade',
        'score' => 12,
        'max_score' => 20,
        'grade_letter' => 'D',
        'graded_at' => now()->toDateString(),
    ]);

    $response = $this
        ->actingAs($teacher)
        ->put(route('teacher.grades.update', $grade), [
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'title' => 'Updated Grade',
            'score' => 17,
            'max_score' => 20,
            'graded_at' => now()->addDay()->toDateString(),
            'remarks' => 'Much better improvement.',
        ]);

    $response
        ->assertRedirect(route('teacher.grades.index'))
        ->assertSessionHas('success')
        ->assertSessionHas('grade_action', 'updated');

    $grade->refresh();

    expect((string) $grade->title)->toBe('Updated Grade');
    expect((string) $grade->grade_letter)->toBe('B');
});
