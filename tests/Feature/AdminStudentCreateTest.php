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
