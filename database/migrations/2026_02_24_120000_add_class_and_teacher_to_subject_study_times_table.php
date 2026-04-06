<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_study_times', function (Blueprint $table) {
            if (!Schema::hasColumn('subject_study_times', 'school_class_id')) {
                $table->foreignId('school_class_id')
                    ->nullable()
                    ->after('subject_id')
                    ->constrained('school_classes')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('subject_study_times', 'teacher_id')) {
                $table->foreignId('teacher_id')
                    ->nullable()
                    ->after('school_class_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        // Backfill existing rows from subjects table so legacy data keeps working.
        DB::statement('
            UPDATE subject_study_times sst
            INNER JOIN subjects s ON s.id = sst.subject_id
            SET sst.school_class_id = COALESCE(sst.school_class_id, s.school_class_id),
                sst.teacher_id = COALESCE(sst.teacher_id, s.teacher_id)
        ');
    }

    public function down(): void
    {
        Schema::table('subject_study_times', function (Blueprint $table) {
            if (Schema::hasColumn('subject_study_times', 'teacher_id')) {
                $table->dropConstrainedForeignId('teacher_id');
            }

            if (Schema::hasColumn('subject_study_times', 'school_class_id')) {
                $table->dropConstrainedForeignId('school_class_id');
            }
        });
    }
};

