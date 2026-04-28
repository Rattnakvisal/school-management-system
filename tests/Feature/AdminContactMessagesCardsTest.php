<?php

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin contact messages page shows dashboard cards', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    ContactMessage::query()->create([
        'name' => 'Unread Sender',
        'email' => 'unread@example.com',
        'subject' => 'Question',
        'message' => 'Please contact me.',
        'is_read' => false,
    ]);

    ContactMessage::query()->create([
        'name' => 'Read Sender',
        'email' => 'read@example.com',
        'subject' => 'Follow up',
        'message' => 'Thank you.',
        'is_read' => true,
        'read_at' => now(),
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.contacts.index'));

    $response
        ->assertOk()
        ->assertSeeText('Messages')
        ->assertSeeText('Unread')
        ->assertSeeText('1 of 2 messages unread')
        ->assertSeeText('Read')
        ->assertSeeText('1 of 2 messages read');
});
