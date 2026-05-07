<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('student_payments', 'proof_file_path')) {
                $table->string('proof_file_path')->nullable()->after('reference');
            }

            if (!Schema::hasColumn('student_payments', 'proof_file_name')) {
                $table->string('proof_file_name')->nullable()->after('proof_file_path');
            }

            if (!Schema::hasColumn('student_payments', 'proof_file_mime')) {
                $table->string('proof_file_mime')->nullable()->after('proof_file_name');
            }

            if (!Schema::hasColumn('student_payments', 'proof_file_size')) {
                $table->unsignedBigInteger('proof_file_size')->nullable()->after('proof_file_mime');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $columns = [
                'proof_file_path',
                'proof_file_name',
                'proof_file_mime',
                'proof_file_size',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('student_payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
