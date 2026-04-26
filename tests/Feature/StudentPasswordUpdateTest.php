<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('allows a student to update their own password', function () {
    $student = User::factory()->create([
        'role' => 'student',
        'password' => 'old-password',
    ]);

    $this->actingAs($student)
        ->put(route('student.settings.password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect(route('student.settings'))
        ->assertSessionHas('success', 'Password updated successfully.');

    $student->refresh();

    expect(Hash::check('new-password', $student->password))->toBeTrue();
    expect(Hash::check('old-password', $student->password))->toBeFalse();
});
