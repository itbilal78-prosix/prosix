<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->unsignedInteger('color_count')
                ->nullable()
                ->after('image_data');

            $table->json('selected_colors')
                ->nullable()
                ->after('color_count');

            $table->json('color_mappings')
                ->nullable()
                ->after('selected_colors');
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn([
                'color_count',
                'selected_colors',
                'color_mappings'
            ]);
        });
    }
};
