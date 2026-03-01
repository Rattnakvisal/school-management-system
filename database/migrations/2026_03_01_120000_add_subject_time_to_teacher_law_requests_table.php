<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_law_requests') || Schema::hasColumn('teacher_law_requests', 'subject_time')) {
            return;
        }

        Schema::table('teacher_law_requests', function (Blueprint $table) {
            $table->string('subject_time', 150)->nullable()->after('subject');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('teacher_law_requests') || !Schema::hasColumn('teacher_law_requests', 'subject_time')) {
            return;
        }

        Schema::table('teacher_law_requests', function (Blueprint $table) {
            $table->dropColumn('subject_time');
        });
    }
};

