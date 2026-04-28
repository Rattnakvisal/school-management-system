<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('teacher management shows women and men cards', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    User::factory()->create([
        'role' => 'teacher',
        'gender' => 'female',
    ]);

    User::factory()->create([
        'role' => 'teacher',
        'gender' => 'male',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.teachers.index'));

    $response
        ->assertOk()
        ->assertSeeText('Women')
        ->assertSeeText('1 of 2 teachers')
        ->assertSeeText('Men');
});

test('admin can save teacher gender', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.teachers.store'), [
            'name' => 'Teacher Gender Test',
            'email' => 'teacher-gender@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'gender' => 'female',
            'is_active' => '1',
        ]);

    $response
        ->assertRedirect(route('admin.teachers.index'))
        ->assertSessionHas('success', 'Teacher account created successfully.');

    expect(User::query()->where('email', 'teacher-gender@example.com')->value('gender'))->toBe('female');
});
