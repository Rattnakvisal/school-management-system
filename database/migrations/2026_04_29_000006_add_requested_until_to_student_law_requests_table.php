<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_law_requests') || Schema::hasColumn('student_law_requests', 'requested_until')) {
            return;
        }

        Schema::table('student_law_requests', function (Blueprint $table) {
            $table->date('requested_until')->nullable()->after('requested_for');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('student_law_requests') || !Schema::hasColumn('student_law_requests', 'requested_until')) {
            return;
        }

        Schema::table('student_law_requests', function (Blueprint $table) {
            $table->dropColumn('requested_until');
        });
    }
};
