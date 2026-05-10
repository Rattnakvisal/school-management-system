<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            if (Schema::hasColumn('school_classes', 'section')) {
                // Drop the composite index if present
                try {
                    $table->dropIndex(['name', 'section']);
                } catch (\Throwable $e) {
                    // ignore if index does not exist or different name
                }

                $table->dropColumn('section');
            }

            if (Schema::hasColumn('school_classes', 'description')) {
                $table->dropColumn('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            if (! Schema::hasColumn('school_classes', 'section')) {
                $table->string('section', 80)->nullable()->after('name');
            }

            if (! Schema::hasColumn('school_classes', 'description')) {
                $table->text('description')->nullable()->after('capacity');
            }

            // Attempt to recreate the composite index
            try {
                $table->index(['name', 'section']);
            } catch (\Throwable $e) {
                // ignore if unable to create index
            }
        });
    }
};
