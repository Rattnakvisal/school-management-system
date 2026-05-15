<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'age')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('age')->nullable()->after('gender');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'age')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('age');
            });
        }
    }
};
