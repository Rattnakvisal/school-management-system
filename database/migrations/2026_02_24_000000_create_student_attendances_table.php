<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('student_attendances')) {
            return;
        }

        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->string('status', 20)->default('present');
            $table->string('remark', 255)->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'attendance_date'], 'student_attendance_student_date_unique');
            $table->index(['teacher_id', 'attendance_date'], 'student_attendance_teacher_date_index');
            $table->index(['school_class_id', 'attendance_date'], 'student_attendance_class_date_index');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('student_attendances')) {
            Schema::dropIfExists('student_attendances');
        }
    }
};

