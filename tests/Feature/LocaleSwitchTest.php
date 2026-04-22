<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('switches the homepage language to Khmer and persists it in session', function () {
    $this->from(route('home'))
        ->get(route('language.switch', 'km'))
        ->assertRedirect(route('home'));

    $this->assertSame('km', session('locale'));

    $this->withSession(['locale' => 'km'])
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('ទំនាក់ទំនង')
        ->assertSeeText('ប្រព័ន្ធគ្រប់គ្រងសាលា');
});

it('keeps English content when English locale is active', function () {
    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Contact')
        ->assertSeeText('School Management System');
});
