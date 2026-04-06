<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_study_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('period', 20);
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['subject_id', 'period']);
            $table->index(['start_time', 'end_time']);
        });

        $now = now();
        $rows = DB::table('subjects')
            ->select(['id', 'study_start_time', 'study_end_time', 'created_at', 'updated_at'])
            ->whereNotNull('study_start_time')
            ->whereNotNull('study_end_time')
            ->orderBy('id')
            ->get();

        $payload = [];
        foreach ($rows as $row) {
            $payload[] = [
                'subject_id' => $row->id,
                'period' => 'custom',
                'start_time' => $row->study_start_time,
                'end_time' => $row->study_end_time,
                'sort_order' => 0,
                'created_at' => $row->created_at ?? $now,
                'updated_at' => $row->updated_at ?? $now,
            ];
        }

        if ($payload !== []) {
            DB::table('subject_study_times')->insert($payload);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_study_times');
    }
};
