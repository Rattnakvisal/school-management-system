<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->date('payment_date');
            $table->date('due_date')->nullable();
            $table->string('status', 24)->default('paid')->index();
            $table->string('payment_method', 40)->nullable();
            $table->string('reference', 120)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'payment_date']);
            $table->index(['payment_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
