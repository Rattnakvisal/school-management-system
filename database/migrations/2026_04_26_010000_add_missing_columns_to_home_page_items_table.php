<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('home_page_items')) {
            return;
        }

        Schema::table('home_page_items', function (Blueprint $table) {
            if (!Schema::hasColumn('home_page_items', 'type')) {
                $table->string('type', 80)->default('content')->after('section');
            }

            if (!Schema::hasColumn('home_page_items', 'key')) {
                $table->string('key', 120)->nullable()->after('type');
            }

            if (!Schema::hasColumn('home_page_items', 'value')) {
                $table->string('value')->nullable()->after('description');
            }

            if (!Schema::hasColumn('home_page_items', 'link_label')) {
                $table->string('link_label')->nullable()->after('value');
            }

            if (!Schema::hasColumn('home_page_items', 'link_url')) {
                $table->string('link_url')->nullable()->after('link_label');
            }

            if (!Schema::hasColumn('home_page_items', 'icon')) {
                $table->text('icon')->nullable()->after('image_path');
            }

            if (!Schema::hasColumn('home_page_items', 'color')) {
                $table->string('color')->nullable()->after('icon');
            }

            if (!Schema::hasColumn('home_page_items', 'meta')) {
                $table->json('meta')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('home_page_items')) {
            return;
        }

        Schema::table('home_page_items', function (Blueprint $table) {
            foreach (['type', 'key', 'value', 'link_label', 'link_url', 'icon', 'color', 'meta'] as $column) {
                if (Schema::hasColumn('home_page_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
