<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('exports the filtered student report as pdf', function () {
    $hasStatusColumn = Schema::hasColumn('users', 'is_active');

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    User::factory()->create(array_filter([
        'role' => 'student',
        'name' => 'Alice Student',
        'email' => 'alice@example.com',
        'is_active' => $hasStatusColumn ? true : null,
    ], static fn($value) => $value !== null));

    User::factory()->create(array_filter([
        'role' => 'student',
        'name' => 'Bob Student',
        'email' => 'bob@example.com',
        'is_active' => $hasStatusColumn ? false : null,
    ], static fn($value) => $value !== null));

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.students.export.pdf', [
            'q' => 'Alice',
            'status' => 'active',
        ]));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
    expect($response->headers->get('content-disposition'))->toContain('.pdf');
});

it('exports the filtered teacher report as excel', function () {
    $hasStatusColumn = Schema::hasColumn('users', 'is_active');

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    User::factory()->create(array_filter([
        'role' => 'teacher',
        'name' => 'Teacher One',
        'email' => 'teacher.one@example.com',
        'is_active' => $hasStatusColumn ? true : null,
    ], static fn($value) => $value !== null));

    User::factory()->create(array_filter([
        'role' => 'teacher',
        'name' => 'Teacher Two',
        'email' => 'teacher.two@example.com',
        'is_active' => $hasStatusColumn ? false : null,
    ], static fn($value) => $value !== null));

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.teachers.export.excel', [
            'status' => 'inactive',
        ]));

    $response->assertOk();
    $response->assertHeader(
        'content-type',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );
    expect($response->headers->get('content-disposition'))->toContain('.xlsx');
});
