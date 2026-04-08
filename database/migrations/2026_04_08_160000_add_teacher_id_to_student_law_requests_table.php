<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('student_law_requests') && !Schema::hasColumn('student_law_requests', 'teacher_id')) {
            Schema::table('student_law_requests', function (Blueprint $table) {
                $table->foreignId('teacher_id')
                    ->nullable()
                    ->after('subject_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('student_law_requests') && Schema::hasColumn('student_law_requests', 'teacher_id')) {
            Schema::table('student_law_requests', function (Blueprint $table) {
                $table->dropConstrainedForeignId('teacher_id');
            });
        }
    }
};
