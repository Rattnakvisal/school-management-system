<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'school_class_id')) {
                $table->foreignId('school_class_id')
                    ->nullable()
                    ->after('is_active')
                    ->constrained('school_classes')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'school_class_id')) {
                $table->dropConstrainedForeignId('school_class_id');
            }
        });
    }
};
