<?php

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('student subjects page renders assigned subjects and schedule', function () {
    $schoolClass = SchoolClass::create([
        'name' => 'Grade 10',
        'section' => 'A',
        'room' => 'R-12',
        'is_active' => true,
    ]);

    $teacher = User::factory()->create([
        'name' => 'Teacher One',
        'role' => 'teacher',
        'is_active' => true,
    ]);

    $student = User::factory()->create([
        'name' => 'Student One',
        'role' => 'student',
        'is_active' => true,
        'school_class_id' => $schoolClass->id,
    ]);

    $classSubject = Subject::create([
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'name' => 'Mathematics',
        'code' => 'MATH101',
        'description' => 'Core class mathematics',
        'is_active' => true,
    ]);

    $majorSubject = Subject::create([
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'name' => 'Physics',
        'code' => 'PHY101',
        'description' => 'Major focus subject',
        'is_active' => true,
    ]);

    $student->majorSubjects()->sync([$majorSubject->id]);

    SubjectStudyTime::create([
        'subject_id' => $classSubject->id,
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'monday',
        'period' => 'morning',
        'start_time' => '08:00:00',
        'end_time' => '09:00:00',
        'sort_order' => 0,
    ]);

    $response = $this->actingAs($student)->get(route('student.subjects.index'));

    $response->assertOk();
    $response->assertSeeText('Student Subjects');
    $response->assertSeeText('Mathematics');
    $response->assertSeeText('Physics');
    $response->assertSeeText('08:00 AM');
});
