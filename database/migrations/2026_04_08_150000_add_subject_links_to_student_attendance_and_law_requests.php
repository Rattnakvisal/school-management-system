<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('student_attendances')) {
            $hasSubjectId = Schema::hasColumn('student_attendances', 'subject_id');

            Schema::table('student_attendances', function (Blueprint $table) use ($hasSubjectId) {
                if (!$hasSubjectId) {
                    $table->foreignId('subject_id')
                        ->nullable()
                        ->after('school_class_id')
                        ->constrained('subjects')
                        ->nullOnDelete();
                }
            });

            if (!$this->indexExists('student_attendances', 'student_attendance_student_id_index')) {
                Schema::table('student_attendances', function (Blueprint $table) {
                    $table->index('student_id', 'student_attendance_student_id_index');
                });
            }

            $this->dropIndexIfExists('student_attendances', 'student_attendance_student_date_unique');
            $this->dropIndexIfExists('student_attendances', 'student_attendance_student_date_subject_unique');
            $this->dropIndexIfExists('student_attendances', 'student_attendance_teacher_date_subject_index');

            Schema::table('student_attendances', function (Blueprint $table) {
                $table->unique(
                    ['student_id', 'attendance_date', 'subject_id'],
                    'student_attendance_student_date_subject_unique'
                );
                $table->index(
                    ['teacher_id', 'attendance_date', 'subject_id'],
                    'student_attendance_teacher_date_subject_index'
                );
            });
        }

        if (Schema::hasTable('student_law_requests')) {
            $hasSubjectId = Schema::hasColumn('student_law_requests', 'subject_id');

            Schema::table('student_law_requests', function (Blueprint $table) use ($hasSubjectId) {
                if (!$hasSubjectId) {
                    $table->foreignId('subject_id')
                        ->nullable()
                        ->after('school_class_id')
                        ->constrained('subjects')
                        ->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('student_attendances')) {
            $this->dropIndexIfExists('student_attendances', 'student_attendance_student_date_subject_unique');
            $this->dropIndexIfExists('student_attendances', 'student_attendance_teacher_date_subject_index');

            Schema::table('student_attendances', function (Blueprint $table) {
                try {
                    $table->unique(
                        ['student_id', 'attendance_date'],
                        'student_attendance_student_date_unique'
                    );
                } catch (\Throwable) {
                }
            });

            if (Schema::hasColumn('student_attendances', 'subject_id')) {
                Schema::table('student_attendances', function (Blueprint $table) {
                    try {
                        $table->dropConstrainedForeignId('subject_id');
                    } catch (\Throwable) {
                    }
                });
            }
        }

        if (Schema::hasTable('student_law_requests') && Schema::hasColumn('student_law_requests', 'subject_id')) {
            Schema::table('student_law_requests', function (Blueprint $table) {
                try {
                    $table->dropConstrainedForeignId('subject_id');
                } catch (\Throwable) {
                }
            });
        }
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!$this->indexExists($table, $indexName)) {
            return;
        }

        DB::statement(sprintf('ALTER TABLE `%s` DROP INDEX `%s`', $table, $indexName));
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        return DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$database, $table, $indexName]
        ) !== [];
    }
};
