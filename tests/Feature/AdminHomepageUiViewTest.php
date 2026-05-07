<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin homepage ui renders from separated homepage ui partials', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.homepage.index'));

    $response
        ->assertOk()
        ->assertSeeText('Navbar Page')
        ->assertSeeText('Footer Page');
});

test('admin account settings still renders account settings sidebar', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.settings'));

    $response
        ->assertOk()
        ->assertSeeText('Profile Settings')
        ->assertSeeText('Password');
});
