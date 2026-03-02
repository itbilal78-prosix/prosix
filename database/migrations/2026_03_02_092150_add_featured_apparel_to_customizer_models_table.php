<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customizer_models', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('subcategory_id');
            $table->boolean('is_apparel')->default(false)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('customizer_models', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'is_apparel']);
        });
    }
};
