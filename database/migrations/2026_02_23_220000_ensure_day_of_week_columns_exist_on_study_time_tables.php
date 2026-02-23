<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('class_study_times', 'day_of_week')) {
            Schema::table('class_study_times', function (Blueprint $table) {
                $table->string('day_of_week', 12)->default('all')->after('school_class_id');
                $table->index(['school_class_id', 'day_of_week'], 'class_study_times_class_day_idx_v2');
            });
        }

        if (!Schema::hasColumn('subject_study_times', 'day_of_week')) {
            Schema::table('subject_study_times', function (Blueprint $table) {
                $table->string('day_of_week', 12)->default('all')->after('subject_id');
                $table->index(['subject_id', 'day_of_week'], 'subject_study_times_subject_day_idx_v2');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('subject_study_times', 'day_of_week')) {
            Schema::table('subject_study_times', function (Blueprint $table) {
                $table->dropColumn('day_of_week');
            });
        }

        if (Schema::hasColumn('class_study_times', 'day_of_week')) {
            Schema::table('class_study_times', function (Blueprint $table) {
                $table->dropColumn('day_of_week');
            });
        }
    }
};

