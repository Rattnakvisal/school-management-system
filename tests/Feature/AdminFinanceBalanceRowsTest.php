<?php

use App\Models\StudentPayment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function financeAdmin(): User
{
    return User::factory()->create([
        'role' => 'admin',
    ]);
}

function financeStudentWithTuition(string $name, string $email, float $tuition): User
{
    $subject = Subject::query()->create([
        'name' => $name . ' Tuition',
        'code' => strtoupper(substr(md5($email), 0, 6)),
        'tuition_fee' => $tuition,
        'is_active' => true,
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'name' => $name,
        'email' => $email,
    ]);

    $student->majorSubjects()->attach($subject->id);

    return $student;
}

test('finance table shows one balance row for a student with multiple payments', function () {
    $admin = financeAdmin();
    $student = financeStudentWithTuition('Balance Student', 'balance@example.com', 240);

    StudentPayment::query()->create([
        'student_id' => $student->id,
        'amount' => 80,
        'discount_amount' => 0,
        'payment_date' => now()->subDay()->toDateString(),
        'status' => 'partial',
        'payment_method' => 'cash',
    ]);

    $latestPayment = StudentPayment::query()->create([
        'student_id' => $student->id,
        'amount' => 60,
        'discount_amount' => 0,
        'payment_date' => now()->toDateString(),
        'status' => 'partial',
        'payment_method' => 'qr_code',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.finance.index'));

    $response
        ->assertOk()
        ->assertSeeText('Missing $100.00');

    $rows = $response->viewData('payments');
    expect($rows->total())->toBe(1);

    $row = $rows->items()[0];
    expect((int) $row['student']->id)->toBe((int) $student->id);
    expect((float) $row['paid_total'])->toBe(140.0);
    expect((float) $row['remaining_due'])->toBe(100.0);
    expect($row['status'])->toBe('partial');
    expect((int) $row['latest_payment']->id)->toBe((int) $latestPayment->id);
});

test('finance row for student without payment is pending', function () {
    $admin = financeAdmin();
    $student = financeStudentWithTuition('Pending Student', 'pending@example.com', 120);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.finance.index'));

    $response->assertOk();

    $row = $response->viewData('payments')->items()[0];
    expect((int) $row['student']->id)->toBe((int) $student->id);
    expect($row['status'])->toBe('pending');
    expect((float) $row['remaining_due'])->toBe(120.0);
});

test('full payment is automatically stored as paid', function () {
    $admin = financeAdmin();
    $student = financeStudentWithTuition('Paid Student', 'paid@example.com', 240);

    $this
        ->actingAs($admin)
        ->post(route('admin.finance.store'), [
            'student_id' => $student->id,
            'amount' => 240,
            'discount_amount' => 0,
            'payment_date' => now()->toDateString(),
            'status' => 'pending',
            'payment_method' => 'cash',
        ])
        ->assertRedirect(route('admin.finance.index'));

    expect(StudentPayment::query()->where('student_id', $student->id)->value('status'))->toBe('paid');
});

test('incomplete payment is automatically stored as partial', function () {
    $admin = financeAdmin();
    $student = financeStudentWithTuition('Partial Student', 'partial@example.com', 240);

    $this
        ->actingAs($admin)
        ->post(route('admin.finance.store'), [
            'student_id' => $student->id,
            'amount' => 100,
            'discount_amount' => 0,
            'payment_date' => now()->toDateString(),
            'status' => 'paid',
            'payment_method' => 'cash',
        ])
        ->assertRedirect(route('admin.finance.index'));

    expect(StudentPayment::query()->where('student_id', $student->id)->value('status'))->toBe('partial');
});
