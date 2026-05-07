<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin page renders the shared admin staff navbar layout', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.settings'))
        ->assertOk()
        ->assertSeeText('Dashboard')
        ->assertSeeText('Reports');
});

test('staff dashboard renders the shared admin staff navbar layout with staff routes', function () {
    $staff = User::factory()->create([
        'role' => 'staff',
    ]);

    $this
        ->actingAs($staff)
        ->get(route('staff.dashboard'))
        ->assertOk()
        ->assertSee(route('staff.dashboard'), false)
        ->assertSee(route('staff.notifications.readAll'), false)
        ->assertSeeText('Mission Events')
        ->assertDontSeeText('Reports');
});
