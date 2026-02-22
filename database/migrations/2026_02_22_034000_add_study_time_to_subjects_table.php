<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'study_time')) {
                $table->time('study_time')->nullable()->after('code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'study_time')) {
                $table->dropColumn('study_time');
            }
        });
    }
};
