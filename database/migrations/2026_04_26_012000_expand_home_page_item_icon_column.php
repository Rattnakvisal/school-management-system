<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('home_page_items') || !Schema::hasColumn('home_page_items', 'icon')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE `home_page_items` MODIFY `icon` text NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('home_page_items') || !Schema::hasColumn('home_page_items', 'icon')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE `home_page_items` MODIFY `icon` varchar(255) NULL');
    }
};
