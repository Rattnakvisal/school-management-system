<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_event_teacher_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_event_id')->constrained('mission_events')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('submission_file_path')->nullable();
            $table->string('submission_file_name')->nullable();
            $table->string('submission_file_mime')->nullable();
            $table->unsignedBigInteger('submission_file_size')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['mission_event_id', 'teacher_id'], 'mission_teacher_submission_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_event_teacher_submissions');
    }
};
