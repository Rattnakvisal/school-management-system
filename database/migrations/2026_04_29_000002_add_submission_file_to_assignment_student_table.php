<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignment_student', function (Blueprint $table) {
            $table->string('submission_file_path')->nullable();
            $table->string('submission_file_name')->nullable();
            $table->string('submission_file_mime')->nullable();
            $table->unsignedBigInteger('submission_file_size')->nullable();
            $table->timestamp('submitted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('assignment_student', function (Blueprint $table) {
            $table->dropColumn([
                'submission_file_path',
                'submission_file_name',
                'submission_file_mime',
                'submission_file_size',
                'submitted_at',
            ]);
        });
    }
};
