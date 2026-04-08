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
        if (!Schema::hasTable('student_law_requests')) {
            return;
        }

        Schema::table('student_law_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('student_law_requests', 'subject')) {
                $table->string('subject', 150)->nullable()->after('law_type');
            }

            if (!Schema::hasColumn('student_law_requests', 'subject_time')) {
                $table->text('subject_time')->nullable()->after('subject');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('student_law_requests')) {
            return;
        }

        Schema::table('student_law_requests', function (Blueprint $table) {
            if (Schema::hasColumn('student_law_requests', 'subject_time')) {
                $table->dropColumn('subject_time');
            }

            if (Schema::hasColumn('student_law_requests', 'subject')) {
                $table->dropColumn('subject');
            }
        });
    }
};
