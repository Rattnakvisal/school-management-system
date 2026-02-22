<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            if (!Schema::hasColumn('school_classes', 'study_start_time')) {
                $table->time('study_start_time')->nullable()->after('study_time');
            }

            if (!Schema::hasColumn('school_classes', 'study_end_time')) {
                $table->time('study_end_time')->nullable()->after('study_start_time');
            }
        });

        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'study_start_time')) {
                $table->time('study_start_time')->nullable()->after('study_time');
            }

            if (!Schema::hasColumn('subjects', 'study_end_time')) {
                $table->time('study_end_time')->nullable()->after('study_start_time');
            }
        });

        if (Schema::hasColumn('school_classes', 'study_time') && Schema::hasColumn('school_classes', 'study_start_time')) {
            DB::table('school_classes')
                ->whereNull('study_start_time')
                ->whereNotNull('study_time')
                ->update([
                    'study_start_time' => DB::raw('study_time'),
                ]);
        }

        if (Schema::hasColumn('subjects', 'study_time') && Schema::hasColumn('subjects', 'study_start_time')) {
            DB::table('subjects')
                ->whereNull('study_start_time')
                ->whereNotNull('study_time')
                ->update([
                    'study_start_time' => DB::raw('study_time'),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('subjects', 'study_start_time')) {
                $columnsToDrop[] = 'study_start_time';
            }

            if (Schema::hasColumn('subjects', 'study_end_time')) {
                $columnsToDrop[] = 'study_end_time';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::table('school_classes', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('school_classes', 'study_start_time')) {
                $columnsToDrop[] = 'study_start_time';
            }

            if (Schema::hasColumn('school_classes', 'study_end_time')) {
                $columnsToDrop[] = 'study_end_time';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
