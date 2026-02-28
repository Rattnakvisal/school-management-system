<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('teacher_attendances')) {
            return;
        }

        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('attendance_date');
            $table->string('status', 20)->default('present');
            $table->string('remark', 255)->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->unique(['teacher_id', 'attendance_date'], 'teacher_attendance_teacher_date_unique');
            $table->index(['attendance_date', 'status'], 'teacher_attendance_date_status_index');
            $table->index(['marked_by', 'attendance_date'], 'teacher_attendance_admin_date_index');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('teacher_attendances')) {
            Schema::dropIfExists('teacher_attendances');
        }
    }
};
