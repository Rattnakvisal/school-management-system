<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_major_subjects')) {
            Schema::create('student_major_subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'subject_id'], 'student_major_subjects_user_subject_unique');
                $table->index(['subject_id', 'user_id'], 'student_major_subjects_subject_user_index');
            });
        }

        if (!Schema::hasColumn('users', 'major_subject_id') || !Schema::hasTable('student_major_subjects')) {
            return;
        }

        $now = now();
        $rows = DB::table('users')
            ->select(['id as user_id', 'major_subject_id as subject_id'])
            ->where('role', 'student')
            ->whereNotNull('major_subject_id')
            ->get()
            ->map(function ($row) use ($now) {
                return [
                    'user_id' => (int) $row->user_id,
                    'subject_id' => (int) $row->subject_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->all();

        if (!empty($rows)) {
            DB::table('student_major_subjects')->upsert(
                $rows,
                ['user_id', 'subject_id'],
                ['updated_at']
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('student_major_subjects')) {
            Schema::dropIfExists('student_major_subjects');
        }
    }
};

