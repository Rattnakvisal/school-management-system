<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'major_subject_id')) {
                $table->foreignId('major_subject_id')
                    ->nullable()
                    ->after('school_class_id')
                    ->constrained('subjects')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'class_study_time_id')) {
                $table->foreignId('class_study_time_id')
                    ->nullable()
                    ->after('major_subject_id')
                    ->constrained('class_study_times')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'class_study_time_id')) {
                $table->dropConstrainedForeignId('class_study_time_id');
            }

            if (Schema::hasColumn('users', 'major_subject_id')) {
                $table->dropConstrainedForeignId('major_subject_id');
            }
        });
    }
};
