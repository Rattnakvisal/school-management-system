<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin student create form always stores a student role', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.students.store'), [
            'name' => 'Forced Student',
            'email' => 'forced-student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'teacher',
        ]);

    $response
        ->assertRedirect(route('admin.students.index'))
        ->assertSessionHas('success', 'Student account created successfully.');

    $student = User::query()
        ->where('email', 'forced-student@example.com')
        ->first();

    expect($student)->not->toBeNull();
    expect((string) $student->role)->toBe('student');
});

test('admin can save student gender and age when creating and editing', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->post(route('admin.students.store'), [
            'name' => 'Profile Student',
            'email' => 'profile-student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            'gender' => 'female',
            'age' => 14,
        ])
        ->assertRedirect(route('admin.students.index'))
        ->assertSessionHas('success', 'Student account created successfully.');

    $student = User::query()
        ->where('email', 'profile-student@example.com')
        ->firstOrFail();

    expect((string) $student->gender)->toBe('female');
    expect((int) $student->age)->toBe(14);

    $this
        ->actingAs($admin)
        ->put(route('admin.students.update', $student), [
            'name' => 'Updated Profile Student',
            'email' => 'updated-profile-student@example.com',
            'password' => '',
            'password_confirmation' => '',
            'role' => 'student',
            'gender' => 'male',
            'age' => 16,
        ])
        ->assertRedirect(route('admin.students.index'))
        ->assertSessionHas('success', 'User updated successfully.');

    $student->refresh();

    expect((string) $student->gender)->toBe('male');
    expect((int) $student->age)->toBe(16);
});
