<?php

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('subject dashboard cards count time study class teacher and student connections', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $teacher = User::factory()->create([
        'role' => 'teacher',
        'name' => 'Chun Rattnakvisal',
    ]);

    $schoolClass = SchoolClass::query()->create([
        'name' => 'Angkor Wat',
        'section' => 'A',
        'is_active' => true,
    ]);

    User::factory()->create([
        'role' => 'student',
        'school_class_id' => $schoolClass->id,
    ]);

    $subject = Subject::query()->create([
        'name' => 'Financial Accounting I',
        'code' => 'ACC105',
        'is_active' => true,
    ]);

    SubjectStudyTime::query()->create([
        'subject_id' => $subject->id,
        'school_class_id' => $schoolClass->id,
        'teacher_id' => $teacher->id,
        'day_of_week' => 'monday',
        'period' => 'morning',
        'start_time' => '07:00',
        'end_time' => '10:00',
        'sort_order' => 0,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.subjects.index'));

    $response
        ->assertOk()
        ->assertSeeText('Class Links')
        ->assertSeeText('1 of 1 subjects connected')
        ->assertSeeText('Teacher Links')
        ->assertSeeText('1 of 1 subjects with teachers')
        ->assertSeeText('Students Learning')
        ->assertSeeText('1 of 1 students connected');
});
