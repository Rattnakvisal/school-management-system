<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('student_attendances')) {
            return;
        }

        $hasPeriod = Schema::hasColumn('student_attendances', 'attendance_period');
        $hasSubjectId = Schema::hasColumn('student_attendances', 'subject_id');

        Schema::table('student_attendances', function (Blueprint $table) use ($hasPeriod, $hasSubjectId) {
            if (!$hasPeriod) {
                $column = $table->string('attendance_period', 20)->nullable();
                $hasSubjectId ? $column->after('subject_id') : $column->after('school_class_id');
            }
        });

        $this->dropIndexIfExists('student_attendances', 'student_attendance_student_date_subject_unique');
        $this->dropIndexIfExists('student_attendances', 'student_attendance_teacher_date_subject_index');
        $this->dropIndexIfExists('student_attendances', 'student_attendance_student_date_subject_period_unique');
        $this->dropIndexIfExists('student_attendances', 'student_attendance_teacher_date_subject_period_index');

        Schema::table('student_attendances', function (Blueprint $table) {
            $table->unique(
                ['student_id', 'attendance_date', 'subject_id', 'attendance_period'],
                'student_attendance_student_date_subject_period_unique'
            );
            $table->index(
                ['teacher_id', 'attendance_date', 'subject_id', 'attendance_period'],
                'student_attendance_teacher_date_subject_period_index'
            );
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('student_attendances')) {
            return;
        }

        $this->dropIndexIfExists('student_attendances', 'student_attendance_student_date_subject_period_unique');
        $this->dropIndexIfExists('student_attendances', 'student_attendance_teacher_date_subject_period_index');

        Schema::table('student_attendances', function (Blueprint $table) {
            try {
                $table->unique(
                    ['student_id', 'attendance_date', 'subject_id'],
                    'student_attendance_student_date_subject_unique'
                );
            } catch (\Throwable) {
            }

            try {
                $table->index(
                    ['teacher_id', 'attendance_date', 'subject_id'],
                    'student_attendance_teacher_date_subject_index'
                );
            } catch (\Throwable) {
            }
        });

        if (Schema::hasColumn('student_attendances', 'attendance_period')) {
            Schema::table('student_attendances', function (Blueprint $table) {
                $table->dropColumn('attendance_period');
            });
        }
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!$this->indexExists($table, $indexName)) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
                try {
                    $blueprint->dropIndex($indexName);
                } catch (\Throwable) {
                }
            });

            return;
        }

        DB::statement(sprintf('ALTER TABLE `%s` DROP INDEX `%s`', $table, $indexName));
    }

    private function indexExists(string $table, string $indexName): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$table}')");

            foreach ($indexes as $index) {
                if ((string) ($index->name ?? '') === $indexName) {
                    return true;
                }
            }

            return false;
        }

        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        return DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$database, $table, $indexName]
        ) !== [];
    }
};
