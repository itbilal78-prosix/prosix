<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customizer_models', function (Blueprint $table) {
            $table->json('color_changes')->nullable()->after('description');
            // Optional: last save ka time track karne ke liye
            $table->timestamp('customized_at')->nullable()->after('color_changes');
        });
    }

    public function down(): void
    {
        Schema::table('customizer_models', function (Blueprint $table) {
            $table->dropColumn(['color_changes', 'customized_at']);
        });
    }
};