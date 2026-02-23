<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('student_study_times')) {
            return;
        }

        Schema::create('student_study_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_study_time_id')->constrained('class_study_times')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'class_study_time_id'], 'student_study_times_user_slot_unique');
            $table->index(['class_study_time_id', 'user_id'], 'student_study_times_slot_user_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_study_times')) {
            Schema::dropIfExists('student_study_times');
        }
    }
};
