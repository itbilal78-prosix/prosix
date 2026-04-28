<?php
// ====================================================
// STEP 1: Migration banao
// php artisan make:migration add_is_hidden_to_customizer_models_table
// Phir is code se replace karo:
// ====================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customizer_models', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('is_apparel');
        });
    }

    public function down(): void
    {
        Schema::table('customizer_models', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });
    }
};
